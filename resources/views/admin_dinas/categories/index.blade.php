@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Sub Kategori</h1>
            <p class="text-gray-600 mt-1">Kelola sub kategori untuk organisasi Anda</p>
        </div>
        <div class="mt-4 md:mt-0">
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-sm font-medium">
                <i class="fas fa-layer-group mr-1"></i>
                {{ $agencyCategories->count() }} Sub Kategori
            </span>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 flex items-start">
            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
            <div class="flex-1">
                <p class="text-green-800 font-medium">{{ session('success') }}</p>
            </div>
            <button type="button" class="text-green-500 hover:text-green-700 ml-4" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 flex items-start">
            <i class="fas fa-exclamation-circle text-red-500 mt-1 mr-3"></i>
            <div class="flex-1">
                <p class="text-red-800 font-medium">{{ session('error') }}</p>
            </div>
            <button type="button" class="text-red-500 hover:text-red-700 ml-4" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add Sub Category Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-5 py-4">
                    <h2 class="text-white font-semibold flex items-center">
                        <i class="fas fa-plus-circle mr-2"></i> Tambah Sub Kategori Baru
                    </h2>
                </div>
                <div class="p-5">
                    <form action="{{ route('admin_dinas.categories.store') }}" method="POST" id="categoryForm">
                        @csrf
                        
                        @if($user->role === 'superadmin')
                        <div class="mb-4">
                            <label for="agency_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Dinas</label>
                            <select class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('agency_id') border-red-500 @enderror" 
                                    id="agency_id" 
                                    name="agency_id"
                                    required>
                                <option value="">-- Pilih Dinas --</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}" 
                                            {{ old('agency_id') == $agency->id ? 'selected' : '' }}>
                                        {{ $agency->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('agency_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        @else
                            <input type="hidden" name="agency_id" value="{{ $user->agency_id }}">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dinas</label>
                                <div class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-gray-600">
                                    {{ $agencies->first()->name ?? 'Dinas' }}
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Sub kategori akan ditambahkan untuk dinas Anda</p>
                            </div>
                        @endif

                        <div class="mb-4">
                            <label for="main_category" class="block text-sm font-medium text-gray-700 mb-1">Kategori Utama</label>
                            <select class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('main_category') border-red-500 @enderror" 
                                    id="main_category" 
                                    name="main_category"
                                    required>
                                <option value="">-- Pilih Kategori Utama --</option>
                                @foreach($mainCategories as $category)
                                    <option value="{{ $category }}" 
                                            {{ old('main_category') == $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                            @error('main_category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Sub Kategori</label>
                            <input type="text" 
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="Masukkan nama sub kategori"
                                   required
                                   maxlength="100">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Maksimal 100 karakter</p>
                        </div>

                        <div class="flex space-x-3">
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 px-4 rounded-lg font-medium transition duration-200 flex items-center justify-center">
                                <i class="fas fa-plus-circle mr-2"></i> Tambah
                            </button>
                            <button type="reset" class="px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200">
                                <i class="fas fa-redo"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Panel -->
            <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-5 py-4">
                    <h2 class="text-white font-semibold flex items-center">
                        <i class="fas fa-info-circle mr-2"></i> Informasi
                    </h2>
                </div>
                <div class="p-5">
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <i class="fas fa-lightbulb text-green-500 mt-1 mr-2"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Cara Kerja Sub Kategori</p>
                                <p class="text-xs text-gray-600 mt-1">
                                    User akan memilih kategori utama terlebih dahulu, kemudian memilih sub kategori yang Anda buat.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-building text-blue-500 mt-1 mr-2"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Auto-Assignment</p>
                                <p class="text-xs text-gray-600 mt-1">
                                    Complaint otomatis masuk ke dinas Anda berdasarkan sub kategori yang dipilih.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sub Categories List Card -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-5 py-4 border-b border-gray-200">
                    <h2 class="text-gray-800 font-semibold flex items-center">
                        <i class="fas fa-list mr-2"></i> Daftar Sub Kategori
                    </h2>
                </div>
                <div class="p-5">
                    @if($agencyCategories->count() > 0)
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        @if($user->role === 'superadmin')
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dinas</th>
                                        @endif
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sub Kategori</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori Utama</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($agencyCategories as $category)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        @if($user->role === 'superadmin')
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="text-sm font-medium text-gray-900">{{ $category->agency->name ?? 'N/A' }}</span>
                                        </td>
                                        @endif
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                {{ $category->name }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                {{ $category->main_category ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                <div class="flex items-center">
                                                    <i class="fas fa-user text-gray-400 mr-1 text-xs"></i>
                                                    {{ $category->creator->name ?? 'Unknown' }}
                                                </div>
                                                <div class="flex items-center mt-1">
                                                    <i class="fas fa-clock text-gray-400 mr-1 text-xs"></i>
                                                    {{ $category->created_at->format('d/m/Y H:i') }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            <form action="{{ route('admin_dinas.categories.destroy', $category->id) }}" 
                                                  method="POST" 
                                                  class="delete-form"
                                                  data-category-name="{{ $category->name }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200"
                                                        title="Hapus Sub Kategori">
                                                    <i class="fas fa-trash mr-1"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-10">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                                <i class="fas fa-folder-open text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada sub kategori</h3>
                            <p class="text-gray-500 max-w-md mx-auto">Tambahkan sub kategori pertama Anda menggunakan form di samping.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('categoryForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const nameInput = document.getElementById('name');
            const mainCategorySelect = document.getElementById('main_category');
            const agencySelect = document.getElementById('agency_id');
            
            // Validate name
            if (!nameInput.value.trim()) {
                e.preventDefault();
                nameInput.focus();
                showAlert('Nama sub kategori harus diisi!', 'error');
                return;
            }
            
            // Validate main category
            if (!mainCategorySelect.value) {
                e.preventDefault();
                mainCategorySelect.focus();
                showAlert('Pilih kategori utama terlebih dahulu!', 'error');
                return;
            }
            
            // Validate agency for superadmin
            @if($user->role === 'superadmin')
            if (!agencySelect.value) {
                e.preventDefault();
                agencySelect.focus();
                showAlert('Pilih dinas terlebih dahulu!', 'error');
                return;
            }
            @endif
        });
    }

    // Delete confirmation
    const deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const categoryName = this.getAttribute('data-category-name');
            
            if (confirm(`Apakah Anda yakin ingin menghapus sub kategori "${categoryName}"?`)) {
                this.submit();
            }
        });
    });

    function showAlert(message, type) {
        // Remove existing alerts
        const existingAlert = document.querySelector('.bg-red-50, .bg-green-50');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        // Create new alert
        const alertDiv = document.createElement('div');
        alertDiv.className = `mb-6 p-4 rounded-lg ${type === 'error' ? 'bg-red-50 border border-red-200' : 'bg-green-50 border border-green-200'} flex items-start`;
        alertDiv.innerHTML = `
            <i class="fas ${type === 'error' ? 'fa-exclamation-circle text-red-500' : 'fa-check-circle text-green-500'} mt-1 mr-3"></i>
            <div class="flex-1">
                <p class="${type === 'error' ? 'text-red-800' : 'text-green-800'} font-medium">${message}</p>
            </div>
            <button type="button" class="${type === 'error' ? 'text-red-500 hover:text-red-700' : 'text-green-500 hover:text-green-700'} ml-4" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // Insert after page header
        const header = document.querySelector('.flex.flex-col.md\\:flex-row');
        header.parentNode.insertBefore(alertDiv, header.nextSibling);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
});
</script>
@endsection