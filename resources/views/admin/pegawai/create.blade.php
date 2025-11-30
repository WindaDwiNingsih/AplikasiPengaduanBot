@extends('layouts.admin') 

@section('title', 'Tambah Pegawai Baru')

@section('content')
<div class="container mx-auto max-w-2xl">
    <div class="bg-white p-8 shadow-2xl rounded-xl">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-3">
            Form Tambah Pegawai Baru
        </h1>

        <!-- Debug: Tampilkan semua errors -->
        @if($errors->any())
            <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 rounded-lg">
                <h4 class="font-bold">Perhatikan error berikut:</h4>
                <ul class="list-disc list-inside text-sm mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.pegawai.store') }}" method="POST" id="createForm">
            @csrf
            
            <!-- Nama Pegawai -->
            <div class="mb-5">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Pegawai</label>
                <input type="text" id="name" name="name" 
                       value="{{ old('name') }}"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror" 
                       required>
                @error('name')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Pegawai -->
            <div class="mb-5">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email (Digunakan untuk Login)</label>
                <input type="email" id="email" name="email" 
                       value="{{ old('email') }}"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror" 
                       required>
                @error('email')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role (Hak Akses) -->
            <div class="mb-5">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Hak Akses (Role)</label>
                <select id="role" name="role" 
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('role') border-red-500 @enderror" 
                        required>
                    <option value="">-- Pilih Hak Akses --</option>
                    <option value="admin_dinas" {{ old('role') == 'admin_dinas' ? 'selected' : '' }}>Admin Dinas</option>
                    <option value="superadmin" {{ old('role') == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                </select>
                @error('role')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Field Agency (Awalnya Disembunyikan) -->
            <div class="mb-5" id="agencyField" style="display: none;">
                <label for="agency_id" class="block text-sm font-medium text-gray-700 mb-2">Dinas</label>
                
                <!-- ✅ TAMBAHKAN: Info penting -->
                <div class="mb-2 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
                    <p class="font-medium">💡 Informasi Penting</p>
                    <p class="text-xs mt-1">Setiap dinas hanya boleh memiliki <strong>satu Admin Dinas</strong>. Dinas yang sudah memiliki Admin ditandai dengan ❌</p>
                </div>
                
                <select id="agency_id" name="agency_id" 
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('agency_id') border-red-500 @enderror">
                    <option value="">-- Pilih Dinas --</option>
                    @if(isset($agencies) && $agencies->count() > 0)
                        @foreach($agencies as $agency)
                            <option value="{{ $agency->id }}" {{ old('agency_id') == $agency->id ? 'selected' : '' }}
                                @if($agency->hasAdmin()) disabled @endif>
                                {{ $agency->name }}
                                @if($agency->hasAdmin()) ❌ (Sudah memiliki Admin) @endif
                            </option>
                        @endforeach
                    @else
                        <option value="">-- Tidak ada dinas yang tersedia --</option>
                        <option value="">Semua dinas sudah memiliki Admin Dinas</option>
                    @endif
                </select>
                
                <!-- ✅ TAMBAHKAN: Pesan bantuan -->
                <p class="text-xs text-gray-500 mt-1">
                    Hanya dinas yang belum memiliki Admin Dinas yang dapat dipilih
                </p>
                
                @error('agency_id')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Fields (Sementara Default) -->
            <div class="mb-5">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" id="password" name="password" 
                       value="Password123!"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror" 
                       required readonly>
                <p class="text-xs text-gray-500 mt-1">Password default: <strong>Password123!</strong></p>
                @error('password')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.pegawai.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Simpan Pegawai
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript untuk toggle agency field -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const agencyField = document.getElementById('agencyField');
    const agencySelect = document.getElementById('agency_id');
    const form = document.querySelector('form');
    
    function toggleAgencyField() {
        console.log('Role value:', roleSelect.value);
        
        if (roleSelect.value === 'admin_dinas') {
            agencyField.style.display = 'block';
            if (agencySelect) {
                agencySelect.setAttribute('required', 'required');
            }
        } else {
            agencyField.style.display = 'none';
            if (agencySelect) {
                agencySelect.removeAttribute('required');
                agencySelect.value = ''; // Reset value
            }
        }
    }
    
    if (roleSelect) {
        roleSelect.addEventListener('change', toggleAgencyField);
        toggleAgencyField(); // Jalankan saat load
    }
    
    // Validasi sebelum submit
    if (form) {
        form.addEventListener('submit', function(e) {
            if (roleSelect.value === 'admin_dinas' && (!agencySelect.value || agencySelect.value === '')) {
                e.preventDefault();
                alert('Silakan pilih dinas untuk role Admin Dinas!');
                agencySelect.focus();
                return false;
            }
        });
    }
});
</script>
@endsection