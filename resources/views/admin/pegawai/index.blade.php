@extends('layouts.admin') 

@section('title', 'Manajemen Pegawai')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Daftar Pegawai Sistem</h1>
        <a href="{{ route('admin.pegawai.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow transition duration-150 ease-in-out">
            Tambah Pegawai Baru
        </a>
    </div>

    @if (session('success'))
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

    <div class="bg-white shadow-xl rounded-xl overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="uppercase text-sm font-semibold text-gray-600 bg-200 border-b border-gray-300">
                    <th class="px-5 py-3 text-left">Nama</th>
                    <th class="px-5 py-3 text-left">Email</th>
                    <th class="px-5 py-3 text-left">Role</th>
                    <th class="px-5 py-3 text-left">Agensi</th>
                    <th class="px-5 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pegawai as $user)
                <tr class="border-b border-gray-200 hover:bg-gray-50">
                    <td class="px-5 py-3 text-sm text-gray-900">{{ $user->name }}</td>
                    <td class="px-5 py-3 text-sm text-gray-900">{{ $user->email }}</td>
                    <td class="px-5 py-3 text-sm">
                        <span class="px-3 py-1 text-xs font-semibold leading-tight rounded-full 
                            @if($user->role === 'superadmin') bg-red-200 text-red-800 
                            @elseif($user->role === 'admin_dinas') bg-yellow-200 text-yellow-800 
                            @else bg-gray-200 text-gray-800 
                            @endif">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-sm text-gray-900">{{ $user->agency_id }}</td>
                    <td class="px-5 py-3 text-sm text-center">
                        {{-- edit --}}
                        <div class="flex justify-center space-x-2">
                            <a href="{{ route('admin.pegawai.edit', $user->id) }}" 
                               class="text-indigo-600 hover:text-indigo-900 font-medium transition duration-150 ease-in-out" 
                               title="Edit">
                                <img src="{{ asset('images/edit.png') }}" 
                                        alt="Logo Dinas" 
                                        class="w-6 h-6 mr-4 object-cover mr-3 ">
                            </a>
                            
                            <!-- Form Hapus -->
                            <form action="{{ route('admin.pegawai.destroy', $user->id) }}" method="POST" 
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus pegawai ini? Tindakan ini tidak dapat dibatalkan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-900 font-medium transition duration-150 ease-in-out" 
                                        title="Hapus">
                                    <img src="{{ asset('images/hapus.png') }}" 
                                        alt="Logo Dinas" 
                                        class="w-6 h-6 mr-4 object-cover mr-3  ">
                                </button>
                            </form>
                            <!-- Tombol Reset Password -->
                            <form action="{{ route('admin.pegawai.reset-password', $user->id) }}" method="POST" 
                                onsubmit="return confirm('Reset password {{ $user->name }} ke default (Password123!)?');">
                                @csrf
                                <button type="submit" 
                                        class="text-orange-600 hover:text-orange-900 font-medium transition duration-150 ease-in-out" 
                                        title="Reset Password ke Default">
                                    <img src="{{ asset('images/reset.png') }}" alt="Reset" class="w-6 h-6 mr-4 object-cover mr-3 ">
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-5 text-center text-gray-500 bg-white text-lg">
                        Belum ada data pegawai yang terdaftar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection