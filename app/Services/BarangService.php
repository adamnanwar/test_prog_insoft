<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BarangService
{
    private string $base = 'https://api.insoftapp.com/api_recruit/api';
    private string $token = '496e736f66745f417369615f54656b6e6f6c6f6769';

    // Http client dengan Bearer token
    private function api(array $headers = [])
    {
        return Http::withToken($this->token)->withHeaders($headers);
    }

    // Buat barang baru (POST, params via headers)
    public function create(array $data): array
    {
        return $this->api([
            'name' => $data['name'],
            'category' => $data['category'],
            'price' => $data['price'],
            'unit' => $data['unit'],
        ])->post("{$this->base}/create")->json() ?? [];
    }

    // Ambil semua barang
    public function getAll(): array
    {
        return $this->api()->get("{$this->base}/get_barang")->json() ?? [];
    }

    // Cari barang by ID, ambil dari list
    public function find(string|int $id): array
    {
        $all = $this->getAll();
        $list = $all['data'] ?? [];
        foreach ($list as $item) {
            if (($item['id_barang'] ?? null) == $id) {
                return ['data' => $item];
            }
        }
        return [];
    }

    // Update barang (POST, params via headers)
    public function edit(string|int $id, array $data): array
    {
        return $this->api([
            'idbarang' => (string) $id,
            'name' => $data['name'],
            'category' => $data['category'],
            'price' => $data['price'],
            'unit' => $data['unit'],
        ])->post("{$this->base}/edit_barang")->json() ?? [];
    }

    // Hapus barang (GET, idbarang via header)
    public function delete(string|int $id): array
    {
        return $this->api([
            'idbarang' => (string) $id,
        ])->get("{$this->base}/delete_barang")->json() ?? [];
    }
}
