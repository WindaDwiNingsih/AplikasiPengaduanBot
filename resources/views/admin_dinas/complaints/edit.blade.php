@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Pengaduan</h1>
            
        </div>
    </div>

    <!-- Alert -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6">
        
        <!-- Form Edit -->
        <div class="lg:col-span-2">
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium">Nomor tiket</p>
                <p class="text-gray-600">{{ $complaint->id }}</p>
            </div>
            <div>
                <p class="text-sm font-medium">Pelapor</p>
                <p class="text-gray-600">{{ $complaint->telegram_username ?: 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium">Tanggal Submit</p>
                <p class="text-gray-600">{{ $complaint->created_at?->format('d M Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium">Judul</p>
                <p class="text-gray-600">{{ $complaint->title }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm  font-medium">Deskripsi</p>
                <p class="text-gray-600 mt-1">{{ $complaint->description }}</p>
            </div>
            
        </div>
    </div>
                <form action="{{ route('complaints.update', $complaint->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- <!-- Informasi yang TIDAK bisa diubah (readonly) -->
                    <div class="space-y-4 mb-6">                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Judul Pengaduan</label>
                            <input type="text" value="{{ $complaint->title }}" 
                                   class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-gray-500"
                                   readonly>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Deskripsi Pengaduan</label>
                            <textarea class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-gray-500 h-32"
                                      readonly>{{ $complaint->description }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Alamat</label>
                                @if(is_array($complaint->location))
                                    {{ implode(', ', array_slice($complaint->location, 0, 2)) }}@if(count($complaint->location) > 2)...@endif
                                @else
                                    {{ \Illuminate\Support\Str::limit($complaint->location, 30) }}
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Pelapor</label>
                                <input type="text" value="{{ $complaint->telegram_username ?? 'N/A' }}" 
                                       class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-gray-500"
                                       readonly>
                            </div>
                        </div>
                    </div> --}}

                    <!-- Informasi yang BISA diubah -->
                    <div class="space-y-6">
                        <h4 class="text-md font-medium text-gray-700 border-b pb-2">Informasi yang Dapat Diubah</h4>
                        
                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" id="status" 
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 transition">
                                <option value="">Pilih Status</option>
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" 
                                        {{ old('status', $complaint->status) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sub Category -->
                        <div>
                            <label for="sub_category" class="block text-sm font-medium text-gray-700 mb-2">
                                Kategori
                                @if($complaint->category)
                                    <span class="text-gray-400 text-xs font-normal ml-2">
                                        (Saat ini: <strong>{{ $complaint->category }}</strong>)
                                    </span>
                                @endif
                            </label>
                            <select name="sub_category" id="sub_category" 
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 transition">
                                <option value="">Pilih Kategori</option>
                                @foreach($subCategories as $subCategory)
                                    <option value="{{ $subCategory }}" 
                                            {{ old('sub_category', $complaint->category) == $subCategory ? 'selected' : '' }}>
                                        {{ $subCategory }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Notes -->
                        <div>
                            <label for="status_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan Perubahan Status
                                <span class="text-gray-400 text-xs font-normal">(Opsional)</span>
                            </label>
                            <textarea name="status_notes" id="status_notes" rows="4"
                                      placeholder="Tambahkan catatan atau informasi tambahan..."
                                      class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 transition">{{ old('status_notes', $complaint->status_notes) }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Catatan ini akan tercatat dalam history perubahan status</p>
                        </div>
                    </div>

                    <!-- Informasi Read-only -->
                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-700">
                            <strong>Catatan:</strong> Hanya field kategori dan status yang dapat diubah. 
                            Kategori utama akan menyesuaikan secara otomatis berdasarkan sub kategori yang dipilih.
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('reports.all') }}" 
                           class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition flex items-center">
                            Batal
                        </a>
                        <button type="submit" 
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    
    function updateStatusColor() {
        const value = statusSelect.value;
        statusSelect.className = 'w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 transition ';
        
        if (value === 'resolved') {
            statusSelect.classList.add('bg-green-100', 'text-green-800', 'border-green-300');
        } else if (value === 'process') {
            statusSelect.classList.add('bg-blue-100', 'text-blue-800', 'border-blue-300');
        } else if (value === 'pending') {
            statusSelect.classList.add('bg-yellow-100', 'text-yellow-800', 'border-yellow-300');
        } else if (value === 'rejected') {
            statusSelect.classList.add('bg-red-100', 'text-red-800', 'border-red-300');
        }
    }
    
    // Update warna saat load
    updateStatusColor();
    
    // Update warna saat perubahan
    statusSelect.addEventListener('change', updateStatusColor);
});
</script>