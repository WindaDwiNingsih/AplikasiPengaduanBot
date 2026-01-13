@extends('layouts.admin')

@section('title', 'Detail Dinas')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Dinas</h1>
            <p class="text-gray-600 mt-1">Informasi lengkap tentang dinas {{ $agency->name }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.agencies.edit', $agency->id) }}" 
               class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </a>
            <a href="{{ route('admin.agencies.index') }}" 
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column (Info) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Info Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Dinas</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Info -->
                    <div>
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-1">Nama Dinas</p>
                            <p class="text-lg font-medium text-gray-900">{{ $agency->name }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-1">Kode Dinas</p>
                            <span class="inline-block bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-medium">
                                {{ $agency->code }}
                            </span>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-1">Status</p>
                            @if($agency->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Non-Aktif
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Right Info -->
                    <div>
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-1">Jumlah Pegawai</p>
                            <div class="flex items-center">
                                <span class="text-2xl font-bold text-blue-600">{{ $userCount }}</span>
                                <span class="ml-2 text-gray-600">orang</span>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-1">Dibuat</p>
                            <p class="text-gray-700">{{ $agency->created_at->translatedFormat('d F Y H:i') }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-1">Diperbarui</p>
                            <p class="text-gray-700">{{ $agency->updated_at->translatedFormat('d F Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description Card -->
            @if($agency->description)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Deskripsi Dinas</h3>
                <div class="prose max-w-none">
                    <p class="text-gray-700 leading-relaxed">{{ $agency->description }}</p>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi</h3>
                <div class="flex flex-wrap gap-3">
                    <!-- Toggle Status -->
                    <form action="{{ route('admin.agencies.toggle-status', $agency->id) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('Ubah status dinas {{ $agency->name }}?')"
                                class="px-4 py-2 {{ $agency->is_active ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600' }} text-white rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path>
                            </svg>
                            {{ $agency->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                    
                    <!-- View Employees -->
                    <a href="{{ route('admin.agencies.employees', $agency->id) }}" 
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13 0h-6"></path>
                        </svg>
                        Lihat Pegawai
                    </a>
                    
                    <!-- Delete -->
                    <form action="{{ route('admin.agencies.destroy', $agency->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Hapus dinas {{ $agency->name }}?')"
                                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 flex items-center {{ $userCount > 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ $userCount > 0 ? 'disabled' : '' }}>
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus Dinas
                        </button>
                    </form>
                </div>
                
                @if($userCount > 0)
                <div class="mt-3 text-sm text-red-600">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.698-.833-2.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    Tidak dapat dihapus karena memiliki {{ $userCount }} pegawai
                </div>
                @endif
            </div>
        </div>

        <!-- Right Column (Sidebar) -->
        <div class="space-y-6">
            <!-- ID Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Sistem</h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">ID Dinas</p>
                        <p class="font-mono text-sm bg-gray-50 p-2 rounded">{{ $agency->id }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Kode Unik</p>
                        <p class="font-mono text-sm bg-gray-50 p-2 rounded">{{ $agency->code }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi Cepat</h3>
                
                <div class="space-y-3">
                    <a href="{{ route('admin.pegawai.create') }}?agency={{ $agency->id }}" 
                       class="w-full px-4 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        Tambah Pegawai
                    </a>
                    
                    <!-- Kelola Pegawai -->
                    <a href="{{ route('admin.agencies.employees', $agency->id) }}" 
                    class="w-full px-4 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Kelola Pegawai
                    </a>
                </div>
            </div>

            <!-- Stats Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistik</h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <p class="text-2xl font-bold text-blue-600">{{ $userCount }}</p>
                        <p class="text-sm text-gray-600">Pegawai</p>
                    </div>
                    
                    <div class="text-center p-4 {{ $agency->is_active ? 'bg-green-50' : 'bg-red-50' }} rounded-lg">
                        <p class="text-2xl font-bold {{ $agency->is_active ? 'text-green-600' : 'text-red-600' }}">
                            {{ $agency->is_active ? 'Aktif' : 'Non' }}
                        </p>
                        <p class="text-sm text-gray-600">Status</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1050">
    <div class="toast align-items-center text-white bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1050">
    <div class="toast align-items-center text-white bg-danger border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide toast messages
    var toastElList = [].slice.call(document.querySelectorAll('.toast'));
    var toastList = toastElList.map(function(toastEl) {
        return new bootstrap.Toast(toastEl, { delay: 5000 });
    });
    toastList.forEach(toast => toast.show());
});
</script>
@endsection