@extends('layouts.admin')

@section('title', 'Edit Laporan - ' . $complaint->id)

@section('content')
<div class="bg-white rounded-lg shadow-md p-6 max-w-2xl mx-auto">
    <h2 class="text-xl font-semibold mb-6">Edit Laporan Pengaduan</h2>

    {{-- Informasi Laporan --}}
    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium">ID Laporan</p>
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

    {{-- Form Edit --}}
    <form action="{{ route('complaints.update', $complaint->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Field Sub Kategori --}}
           <div>
                <label for="sub_category" class="block text-sm font-medium text-gray-700 mb-2">
                    Kategori *
                </label>
                <select name="sub_category" id="sub_category" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Kategori</option>
                    @foreach($subCategories as $subCategory)
                        <option value="{{ $subCategory }}" 
                            {{ old('sub_category', $complaint->category) == $subCategory ? 'selected' : '' }}>
                            {{ $subCategory }}
                        </option>
                    @endforeach
                </select>
                @error('sub_category')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Field Status --}}
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    Status *
                </label>
                <select name="status" id="status" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Status</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" 
                            {{ old('status', $complaint->status) == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Field Catatan Perubahan Status --}}
        <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
                <label for="status_notes" class="block text-sm font-semibold text-gray-700">
                    <i class="bi bi-chat-left-text mr-1"></i>Catatan Perubahan Status
                </label>
                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">Opsional</span>
            </div>
            <div class="relative">
                <textarea 
                    name="status_notes" 
                    id="status_notes" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 resize-none"
                    rows="4" 
                    placeholder="💡 Jelaskan alasan perubahan status atau tambahkan catatan penting..."
                    style="min-height: 120px;"
                >{{ old('status_notes') }}</textarea>
                <div class="absolute bottom-3 right-3">
                    <span id="charCount" class="text-xs text-gray-400 bg-white px-2 py-1 rounded">0/500</span>
                </div>
            </div>
            <p class="mt-2 text-sm text-gray-500 flex items-center">
                <i class="bi bi-info-circle mr-1"></i>
                Catatan ini akan tercatat dalam history perubahan status
            </p>
        </div>

        {{-- Informasi Read-only --}}
        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-sm text-yellow-700">
                <strong>Catatan:</strong> Hanya field sub kategori dan status yang dapat diubah. 
                Kategori utama akan menyesuaikan secara otomatis berdasarkan sub kategori yang dipilih.
            </p>
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
            <a href="{{ route('reports.all', $complaint->id) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200">
                Batal
            </a>
            <button type="submit" 
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition duration-200">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

{{-- Script untuk styling select berdasarkan status --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    
    function updateStatusColor() {
        const value = statusSelect.value;
        statusSelect.className = 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 ';
        
        if (value === 'resolved') {
            statusSelect.classList.add('bg-green-100', 'text-green-800');
        } else if (value === 'process') {
            statusSelect.classList.add('bg-blue-100', 'text-blue-800');
        } else if (value === 'pending') {
            statusSelect.classList.add('bg-yellow-100', 'text-yellow-800');
        } else if (value === 'rejected') {
            statusSelect.classList.add('bg-red-100', 'text-red-800');
        }
    }
    
    // Update warna saat load
    updateStatusColor();
    
    // Update warna saat perubahan
    statusSelect.addEventListener('change', updateStatusColor);
    
    // Character counter for status notes
    const statusNotes = document.getElementById('status_notes');
    const charCount = document.getElementById('charCount');
    
    function updateCharCount() {
        const length = statusNotes.value.length;
        charCount.textContent = length + '/500';
        
        if (length > 450) {
            charCount.classList.remove('text-gray-400');
            charCount.classList.add('text-red-500');
        } else {
            charCount.classList.remove('text-red-500');
            charCount.classList.add('text-gray-400');
        }
    }
    
    statusNotes.addEventListener('input', updateCharCount);
    updateCharCount(); // Initialize on load
});
</script>
@endsection