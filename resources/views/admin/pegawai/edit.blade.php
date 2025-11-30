@extends('layouts.admin') 

@section('title', 'Edit Data Pegawai')

@section('content')
<div class="container mx-auto max-w-2xl">
    <div class="bg-white p-8 shadow-2xl rounded-xl">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-3">
            Edit Data Pegawai
        </h1>

        <!-- Display Success/Error Messages -->
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

        <form action="{{ route('admin.pegawai.update', $pegawai->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Nama Pegawai -->
            <div class="mb-5">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Pegawai</label>
                <input type="text" id="name" name="name" 
                       value="{{ old('name', $pegawai->name) }}"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror" 
                       required
                       autofocus>
                @error('name')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Pegawai -->
            <div class="mb-5">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" id="email" name="email" 
                       value="{{ old('email', $pegawai->email) }}"
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
                    <option value="admin_dinas" {{ old('role', $pegawai->role) == 'admin_dinas' ? 'selected' : '' }}>Admin Dinas</option>
                    <option value="superadmin" {{ old('role', $pegawai->role) == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                </select>
                @error('role')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Field Agency - DENGAN INFO AGENCY YANG SUDAH ADA -->
            <div class="mb-5" id="agencyField" style="{{ $pegawai->role == 'admin_dinas' ? 'display: block;' : 'display: none;' }}">
                <label for="agency_id" class="block text-sm font-medium text-gray-700 mb-2">Dinas</label>
                
                <!-- ✅ TAMBAHKAN: Info jika agency sudah memiliki admin -->
                @if($pegawai->role == 'admin_dinas' && $pegawai->agency)
                    <div class="mb-2 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
                        <p class="font-medium">📌 Saat ini: <strong>{{ $pegawai->agency->name }}</strong></p>
                        <p class="text-xs mt-1">Jika mengganti dinas, pastikan dinas tujuan belum memiliki Admin Dinas.</p>
                    </div>
                @endif
                
                <select id="agency_id" name="agency_id" 
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('agency_id') border-red-500 @enderror"
                        @if($pegawai->role == 'admin_dinas') required @endif>
                    <option value="">-- Pilih Dinas --</option>
                    @if(isset($agencies) && $agencies->count() > 0)
                        @foreach($agencies as $agency)
                            <option value="{{ $agency->id }}" 
                                @if(old('agency_id', $pegawai->agency_id) == $agency->id) selected @endif
                                @if($agency->hasAdmin() && $agency->id != $pegawai->agency_id) disabled @endif>
                                {{ $agency->name }}
                                @if($agency->hasAdmin() && $agency->id != $pegawai->agency_id) 
                                    ❌ (Sudah memiliki Admin)
                                @elseif($agency->id == $pegawai->agency_id)
                                    ✅ (Saat ini)
                                @endif
                            </option>
                        @endforeach
                    @else
                        <option value="">-- Data dinas tidak tersedia --</option>
                    @endif
                </select>
                
                <!-- ✅ TAMBAHKAN: Pesan bantuan -->
                <p class="text-xs text-gray-500 mt-1">
                    Dinas yang sudah memiliki Admin Dinas ditandai dengan ❌
                </p>
                
                @error('agency_id')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Password Info -->
            <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Password Terenskripsi</p>
                        <p class="text-sm text-gray-500 mt-1">
                            Password : <span class="font-mono bg-gray-200 px-2 py-1 rounded">••••••••</span><br>
                            Untuk reset password, gunakan tombol <strong>"Reset Password"</strong> di halaman daftar pegawai.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.pegawai.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Pegawai
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
    
    console.log('=== EDIT PAGE LOADED ===');
    console.log('Role:', roleSelect.value);
    console.log('Agency Field Visible:', agencyField.style.display);
    
    
    function toggleAgencyField() {
        if (roleSelect.value === 'admin_dinas') {
            agencyField.style.display = 'block';
            if (agencySelect) {
                agencySelect.setAttribute('required', 'required');
            }
        } else {
            agencyField.style.display = 'none';
            if (agencySelect) {
                agencySelect.removeAttribute('required');
                agencySelect.value = ''; // Reset value saat bukan admin_dinas
            }
        }
    }
    
    if (roleSelect) {
        roleSelect.addEventListener('change', toggleAgencyField);
        // Pastikan toggle berjalan saat page load
        toggleAgencyField();
    }
});
</script>
@endsection