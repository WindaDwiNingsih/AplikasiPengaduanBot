@extends('layouts.admin')

@section('title', 'Dashboard Pengaduan')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Hero Section dengan Background Gradasi -->
<div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl p-6 mb-8 text-white shadow-lg">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
        <div class="mb-4 md:mb-0">
            <h1 class="text-2xl md:text-3xl font-bold mb-2">Selamat Datang, {{ Auth::user()->name }}! 👋</h1>
            <p class="text-blue-100 text-lg opacity-90">Sistem Manajemen Pengaduan Masyarakat</p>
            <div class="flex items-center mt-3 space-x-4 text-sm">
                <div class="flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ now()->format('d F Y') }}</span>
                </div>
                <div class="flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zM4.333 10a5.667 5.667 0 1111.334 0 5.667 5.667 0 01-11.334 0z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ Auth::user()->role === 'superadmin' ? 'Super Admin' : 'Admin Dinas' }}</span>
                </div>
            </div>
        </div>
        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
            <div class="text-center">
                <div class="text-2xl font-bold">{{ $totalComplaints }}</div>
                <div class="text-blue-100 text-sm">Total Pengaduan</div>
            </div>
        </div>
    </div>
</div>
    <!-- Charts Section -->
    @include('admin.components.complaint-charts')

    <!-- Informasi dan Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Progress Ringkasan -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-2xl border border-blue-200 lg:col-span-2">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Ringkasan Kinerja
            </h3>
            
            <div class="space-y-4">
                <!-- Progress Bar - Penyelesaian -->
                <div>
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Tingkat Penyelesaian</span>
                        <span class="font-semibold">
                            @if($totalComplaints > 0)
                                {{ number_format(($resolvedCount / $totalComplaints) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $totalComplaints > 0 ? ($resolvedCount / $totalComplaints) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <!-- Informasi Sistem -->
                <div class="bg-white/80 rounded-xl p-4 mt-4">
                    <h4 class="font-semibold text-blue-800 mb-2">📊 Informasi Sistem</h4>
                    <p class="text-gray-700 text-sm leading-relaxed">
                        Sistem pengaduan masyarakat memastikan setiap laporan diproses secara transparan 
                        dan akuntabel melalui tahapan penanganan, hingga penyelesaian akhir.
                    </p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                </svg>
                Aksi Cepat
            </h3>
            <div class="space-y-3">
                
                
                
            </div>
        </div>
    </div>

<style>
    .bg-gradient-to-r {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
    }
    
    .hover\:shadow-md:hover {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
</style>
@endsection