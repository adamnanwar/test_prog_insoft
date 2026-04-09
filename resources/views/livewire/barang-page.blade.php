<div class="max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-bold tracking-widest uppercase">Barang</h1>
        <button wire:click="$set('showModal', true)"
            class="bg-black text-white px-4 py-2 text-xs uppercase tracking-widest hover:opacity-80 transition">
            + Add Barang
        </button>
    </div>

    {{-- Flash message --}}
    @if($flashMessage)
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
            class="mb-4 px-4 py-2 border text-sm {{ $flashType === 'success' ? 'border-black text-black' : 'border-red-400 text-red-600' }}">
            {{ $flashMessage }}
        </div>
    @endif

    {{-- Search --}}
    <div class="mb-4">
        <input wire:model.live="search" type="text" placeholder="Cari barang...(Contoh: teh botol)"
            class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-black">
    </div>

    {{-- Tabel --}}
    <div class="bg-white border border-gray-100">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="text-left px-4 py-3 text-xs uppercase tracking-widest font-medium text-gray-500">ID</th>
                    <th class="text-left px-4 py-3 text-xs uppercase tracking-widest font-medium text-gray-500">Nama
                    </th>
                    <th class="text-left px-4 py-3 text-xs uppercase tracking-widest font-medium text-gray-500">Kategori
                    </th>
                    <th class="text-left px-4 py-3 text-xs uppercase tracking-widest font-medium text-gray-500">Harga
                    </th>
                    <th class="text-left px-4 py-3 text-xs uppercase tracking-widest font-medium text-gray-500">Unit
                    </th>
                    <th class="text-left px-4 py-3 text-xs uppercase tracking-widest font-medium text-gray-500">Aksi
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($filteredBarangs as $barang)
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-400">{{ $barang['id_barang'] ?? '-' }}</td>
                        <td class="px-4 py-3 font-medium">{{ $barang['name'] ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="border border-black text-xs uppercase px-2 py-0.5">
                                {{ $barang['category_name'] ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">Rp {{ number_format($barang['price'] ?? 0, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $barang['unit'] ?? '-' }}</td>
                        <td class="px-4 py-3 flex items-center gap-2">
                            {{-- Edit --}}
                            <button wire:click="edit('{{ $barang['id_barang'] ?? '' }}')"
                                class="text-xs border border-black px-2 py-1 hover:bg-black hover:text-white transition">
                                Edit
                            </button>
                            {{-- Delete --}}
                            <button wire:click="showDeleteConfirm('{{ $barang['id_barang'] ?? '' }}')"
                                class="border border-black p-1 hover:bg-black hover:text-white transition" title="Hapus">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-sm">Tidak ada data barang.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Add/Edit --}}
    @if($showModal)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <div class="bg-white w-full max-w-md p-6 shadow-lg">
                <h2 class="text-sm font-bold uppercase tracking-widest mb-5">
                    {{ $editId ? 'Edit Barang' : 'Tambah Barang' }}
                </h2>

                <div class="flex flex-col gap-4">
                    <div>
                        <label class="text-xs uppercase tracking-widest text-gray-500 block mb-1">Nama</label>
                        <input wire:model="form.name" type="text"
                            class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-black">
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-widest text-gray-500 block mb-1">Kategori</label>
                        <select wire:model="form.category"
                            class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-black">
                            <option value="1">Minuman</option>
                            <option value="2">Makanan</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-widest text-gray-500 block mb-1">Harga</label>
                        <input wire:model="form.price" type="number"
                            class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-black">
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-widest text-gray-500 block mb-1">Unit</label>
                        <input wire:model="form.unit" type="text"
                            class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-black">
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button wire:click="closeModal"
                        class="bg-white border border-black text-black px-4 py-2 text-xs uppercase tracking-widest hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button wire:click="save"
                        class="bg-black text-white px-4 py-2 text-xs uppercase tracking-widest hover:opacity-80 transition">
                        Save
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Konfirmasi Hapus --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <div class="bg-white w-full max-w-sm p-6 shadow-lg text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-4 text-gray-400" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2" />
                </svg>
                <h2 class="text-sm font-bold uppercase tracking-widest mb-2">Hapus Barang</h2>
                <p class="text-sm text-gray-500 mb-6">Apakah kamu yakin ingin menghapus barang ini? Tindakan ini tidak bisa
                    dibatalkan.</p>
                <div class="flex justify-center gap-2">
                    <button wire:click="cancelDelete"
                        class="bg-white border border-black text-black px-4 py-2 text-xs uppercase tracking-widest hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button wire:click="confirmDelete"
                        class="bg-black text-white px-4 py-2 text-xs uppercase tracking-widest hover:opacity-80 transition">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>