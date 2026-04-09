<?php

namespace App\Livewire;

use App\Services\BarangService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class BarangPage extends Component
{
    public array $barangs = [];
    public string $search = '';
    public array $form = ['name' => '', 'category' => '1', 'price' => '', 'unit' => 'pcs'];
    public ?string $editId = null;
    public ?string $deleteId = null;
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public string $flashMessage = '';
    public string $flashType = 'success';

    // Muat data saat komponen dimount
    public function mount(): void
    {
        $this->loadData();
    }

    // Ambil data barang dari API
    public function loadData(): void
    {
        $service = new BarangService();
        $result = $service->getAll();
        // API bisa return {data: [...]} atau {data: {single}}
        $data = $result['data'] ?? [];
        $this->barangs = isset($data['id_barang']) ? [$data] : (is_array($data) ? array_values($data) : []);
    }

    // Simpan barang baru atau update
    public function save(): void
    {
        $service = new BarangService();

        if ($this->editId) {
            $service->edit($this->editId, $this->form);
            $this->flash('Barang berhasil diupdate');
        } else {
            $service->create($this->form);
            $this->flash('Barang berhasil ditambahkan');
        }

        $this->closeModal();
        $this->loadData();
    }

    // Buka modal edit dengan data barang
    public function edit(string|int $id): void
    {
        $service = new BarangService();
        $result = $service->find($id);
        $data = $result['data'] ?? $result;

        $this->editId = (string) $id;
        $this->form = [
            'name' => $data['name'] ?? '',
            'category' => $data['id_category'] ?? '1',
            'price' => $data['price'] ?? '',
            'unit' => $data['unit'] ?? 'pcs',
        ];
        $this->showModal = true;
    }

    // Tampilkan modal konfirmasi hapus
    public function showDeleteConfirm(string|int $id): void
    {
        $this->deleteId = (string) $id;
        $this->showDeleteModal = true;
    }

    // Proses hapus barang
    public function confirmDelete(): void
    {
        $service = new BarangService();
        $service->delete($this->deleteId);
        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->flash('Barang berhasil dihapus');
        $this->loadData();
    }

    // Batal hapus
    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    // Tutup modal dan reset form
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editId = null;
        $this->form = ['name' => '', 'category' => '1', 'price' => '', 'unit' => 'pcs'];
    }

    // Set flash message
    private function flash(string $message, string $type = 'success'): void
    {
        $this->flashMessage = $message;
        $this->flashType = $type;
        $this->dispatch('flash-shown');
    }

    // Filter barang berdasarkan pencarian
    #[Computed]
    public function filteredBarangs(): array
    {
        if (!$this->search) {
            return $this->barangs;
        }

        return array_values(array_filter($this->barangs, fn($b) =>
            str_contains(strtolower($b['name'] ?? ''), strtolower($this->search)) ||
            str_contains(strtolower($b['category_name'] ?? ''), strtolower($this->search))
        ));
    }

    public function render()
    {
        return view('livewire.barang-page', [
            'filteredBarangs' => $this->filteredBarangs(),
        ])->layout('layouts.app');
    }
}
