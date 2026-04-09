<?php

namespace App\Jobs;

use App\Models\Image;
use App\Notifications\JobFailed;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as ImageLib;
use Throwable;

class ProcessImage implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;
    public int $timeout = 180;
    public string $queue = 'default';

    public function __construct(
        private string $imageId,
        private string $sourcePath,
        private int $userId
    ) {
    }

    public function backoff(): array
    {
        return [30, 60, 120, 240, 480];
    }

    public function handle(): void
    {
        // cek apakah job ini sedang dalam batch yang dibatalkan
        if ($this->batch()?->cancelled()) {
            return;
        }

        $image = Image::find($this->imageId);

        if (!$image) {
            return;
        }

        // jika gambar sudah selesai, tidak perlu diproses lagi
        if ($image->status === 'done') {
            return;
        }

        $doneKey = "image_processed:{$this->imageId}";
        $lockKey = "lock:image_process:{$this->imageId}";

        // gunakan lock supaya tidak diproses 2 worker sekaligus
        $lock = Cache::lock($lockKey, 300);

        if (!$lock->get()) {
            // worker lain sedang memproses, coba lagi nanti
            $this->release(10);
            return;
        }

        $temporaryFiles = [];

        try {
            // cek lagi setelah dapat lock (hindari race condition)
            if (Cache::has($doneKey)) {
                return;
            }

            $image->refresh();

            if ($image->status === 'done') {
                return;
            }

            if (!$this->checkRateLimit()) {
                return;
            }

            // update status jadi processing supaya tidak diambil job lain
            $updated = Image::where('id', $this->imageId)
                ->whereNotIn('status', ['processing', 'done'])
                ->update([
                    'status' => 'processing',
                    'error' => null,
                ]);

            if (!$updated && $image->status !== 'processing') {
                return;
            }

            // step 1: buat thumbnail
            $thumbnailPath = $this->makeThumbnail($this->sourcePath);
            $temporaryFiles[] = $thumbnailPath;

            // step 2: convert ke webp
            $webpPath = $this->convertToWebp($thumbnailPath);
            $temporaryFiles[] = $webpPath;

            // step 3: optimasi
            $optimizedPath = $this->optimize($webpPath);
            $temporaryFiles[] = $optimizedPath;

            // step 4: upload ke cdn
            $cdnUrl = $this->uploadToCdn($optimizedPath);

            // tandai sudah selesai di cache
            Cache::put($doneKey, $cdnUrl, now()->addDays(7));

            // update database
            Image::where('id', $this->imageId)->update([
                'cdn_url' => $cdnUrl,
                'status' => 'done',
            ]);
        } finally {
            // hapus file temporary
            if (!empty($temporaryFiles)) {
                Storage::delete($temporaryFiles);
            }

            optional($lock)->release();
        }
    }

    public static function dispatchChained(string $imageId, string $path, int $userId): void
    {
        Bus::chain([
            (new self($imageId, $path, $userId))->onQueue('default'),
            (new NotifyImageReady($imageId))->onQueue('high'),
        ])->dispatch();
    }

    public static function dispatchBatch(array $images, int $userId): void
    {
        $jobs = collect($images)
            ->map(function ($image) use ($userId) {
                return (new self($image['id'], $image['path'], $userId))->onQueue('default');
            })
            ->toArray();

        Bus::batch($jobs)
            ->name('process-images-batch')
            ->dispatch();
    }

    public function failed(Throwable $e): void
    {
        // update status jika gagal
        Image::where('id', $this->imageId)->update([
            'status' => 'failed',
            'error' => $e->getMessage(),
        ]);

        // kirim notifikasi ke admin
        Notification::route('slack', config('services.slack.webhook'))
            ->notify(new JobFailed($this->imageId, $e));
    }

    private function checkRateLimit(): bool
    {
        $key = "rate_limit:image_process:user:{$this->userId}";
        $now = now()->timestamp;
        $window = 60;
        $limit = 5;

        // tambahkan job ke sorted set
        Redis::zadd($key, $now, $this->imageId . ':' . uniqid());

        // hapus data lama
        Redis::zremrangebyscore($key, '-inf', $now - $window);

        // set expiry
        Redis::expire($key, $window * 2);

        $count = Redis::zcard($key);

        if ($count > $limit) {
            // jika limit tercapai, coba lagi nanti
            $this->release(30);
            return false;
        }

        return true;
    }

    private function makeThumbnail(string $sourcePath): string
    {
        $thumbnailPath = 'temp/thumbnail_' . basename($sourcePath);

        $img = ImageLib::make(Storage::path($sourcePath));
        $img->fit(300, 300);
        Storage::put($thumbnailPath, $img->encode());

        return $thumbnailPath;
    }

    private function convertToWebp(string $path): string
    {
        $webpPath = str_replace(['.jpg', '.jpeg', '.png'], '.webp', $path);
        $webpPath = 'temp/webp_' . basename($webpPath);

        $img = ImageLib::make(Storage::path($path));
        Storage::put($webpPath, $img->encode('webp', 80));

        return $webpPath;
    }

    private function optimize(string $path): string
    {
        $optimizedPath = 'temp/optimized_' . basename($path);

        $img = ImageLib::make(Storage::path($path));
        Storage::put($optimizedPath, $img->encode('webp', 70));

        return $optimizedPath;
    }

    private function uploadToCdn(string $path): string
    {
        $filename = 'images/' . $this->imageId . '/' . basename($path);
        $contents = Storage::get($path);

        Storage::disk('cdn')->put($filename, $contents, 'public');

        return config('filesystems.disks.cdn.url') . '/' . $filename;
    }
}
