<?php

namespace App\Jobs;

use App\Models\Image;
use App\Notifications\ImageReadyNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyImageReady implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $queue = 'high';

    public function __construct(private string $imageId)
    {
    }

    public function handle(): void
    {
        $image = Image::with('user')->find($this->imageId);

        if (!$image) {
            return;
        }

        if (!$image->user) {
            return;
        }

        $image->user->notify(new ImageReadyNotification($image));
    }
}