**no. 2&3. function untuk mengetahui berapa banyak kata/words yang terdapat dalam suatu variable & menyisipkan satu kata/word ke dalam variable**

/logic/no2-no3

resources/views/logic/no2-no3.blade.php

**no. 4. function pola segitiga: Input: Berapa banyak baris Output jika input diisi 5 baris, maka output harus menjadi segitiga**

/logic/no4
resources/views/logic/no4.blade.php

**no. 5. Fungsi untuk mencari huruf yang sering muncul dalam sebuah kata.**

/logic/no5

resources/views/logic/no5.blade.php

**no. 6. Fungsi untuk mengurutkan sebuah array**

/logic/no6

resources/views/logic/no6.blade.php


**no. 7. arsitektur multi-instance laravel + livewire**

**daftar file**


no7/
app/Jobs/

    ProcessImage.php

    NotifyImageReady.php

config/

    session.php

    database.redis.php

docs/

    supervisor/

        laravel-worker.conf

 README.md


**tugas 1. perbandingan sticky sessions vs redis session**

**sticky sessions (load balancer)**
cara ini membuat user selalu masuk ke server yang sama.

kelebihan:

1. gampang setup
2. tidak perlu redis
3. cepat karena pakai memory server lokal

kekurangan:

1. kalau server mati -> session user hilang
2. tidak bisa scale dengan baik
3. bisa bermasalah di livewire kalau request pindah server

**redis session (centralized session store)**
semua session disimpan di redis, jadi semua server bisa akses.

kelebihan:

1. lebih aman (failover jalan)
2. bisa scale ke banyak server
3. cocok untuk livewire yang butuh state

kekurangan:

1. harus setup redis
2. ada sedikit delay karena lewat network

kesimpulan:
untuk multi-instance laravel + livewire, redis session lebih direkomendasikan.

**tugas 2. migrasi session file ke redis (zero downtime)**

**env yang digunakan**


SESSION_DRIVER=redis
SESSION_CONNECTION=session
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=contohpassword
REDIS_SESSION_DB=1


**checklist migrasi**

1. install redis client (predis atau phpredis)
2. tambahkan koneksi redis di config database
3. ubah config session jadi redis
4. set SESSION_DRIVER=redis di .env (jangan restart dulu)
5. test dulu di staging
6. restart server satu per satu (rolling restart)
7. monitor log error
8. biarkan session lama expire sendiri
9. bersihkan file session lama jika sudah aman

catatan:

1. jangan restart semua server sekaligus
2. jangan ubah app_key
3. user tidak akan logout kalau dilakukan dengan benar

**tugas 3 — processimage job**

alur proses:


dispatch processimage
-> cek sudah pernah diproses?
-> kalau iya -> skip
-> cek rate limit
-> kalau limit -> release job

step 1 -> buat thumbnail
step 2 -> convert ke webp
step 3 -> optimasi
step 4 -> upload ke cdn

-> simpan ke cache (idempotency)
-> update database
-> lanjut ke notifyimageready


**cara pakai job**


ProcessImage::dispatchChained($imageId, $path, $userId);

ProcessImage::dispatchBatch([
    ['id' => '1', 'path' => 'images/foto1.jpg'],
    ['id' => '2', 'path' => 'images/foto2.jpg'],
], $userId);


**tugas 4 — queue prioritization**

saya menggunakan 3 jenis queue:

1. high -> untuk job penting (notifikasi)
2. default -> untuk proses utama (image processing)
3. low -> untuk job tidak penting

**strategi worker pool (versi sederhana)**

worker dibagi seperti ini:

1. worker high:
   hanya mengambil queue high
   jumlah 3 proses
   cepat (sleep kecil)

2. worker default:
   mengambil queue default dan high
   jadi bisa bantu queue high kalau kosong
   jumlah 2 proses

3. worker low:
   mengambil semua queue (high, default, low)
   paling lambat
   jumlah 1 proses

jadi alurnya:
job masuk -> redis
worker baca -> sesuai prioritas queue
worker jalanin job

**cara dispatch ke queue**


ProcessImage::dispatch($id, $path, $userId)->onQueue('default');

NotifyImageReady::dispatch($id)->onQueue('high');


**tugas 5 — retry, backoff, dead-letter, rate limiting**

**retry dan backoff**


public int $tries = 3;

public function backoff(): array
{
    return [30, 60, 120];
}


artinya:

1. retry ke-1 -> 30 detik
2. retry ke-2 -> 60 detik
3. retry ke-3 -> 120 detik

**dead-letter (job gagal total)**


public function failed(Throwable $e)
{
    update status jadi failed
    kirim notifikasi ke admin
}


ini dipanggil kalau semua retry sudah habis.

**rate limiting per user**

saya pakai redis sorted set.

konsepnya:

1. simpan timestamp tiap job
2. hapus data lama
3. hitung jumlah dalam 1 menit

kalau lebih dari limit:

$this->release(30);


job akan dicoba lagi 30 detik kemudian.

**idempotency (biar job tidak dobel)**

saya pakai:

1. cache key -> image_processed:{id}
2. redis lock untuk mencegah 2 worker proses data yang sama

jadi:

1. kalau sudah pernah diproses -> skip
2. kalau sedang diproses worker lain -> tunggu

**kesimpulan**

1. redis dipakai untuk session dan queue
2. worker dijalankan di server menggunakan supervisor
3. queue dibagi jadi beberapa prioritas
4. job dibuat aman dengan retry, backoff, dan rate limit
5. idempotency digunakan supaya job tidak jalan dua kali
