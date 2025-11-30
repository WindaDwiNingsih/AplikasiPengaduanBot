@extends('layouts.admin')

@section('title', 'Laporan Semua Data Pengaduan')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center ">
        {{-- <div>
            <h1 class="text-3xl font-bold text-gray-800"></h1>
            <p class="text-gray-600 mt-1">Report lengkap data pengaduan dengan filter</p>
        </div> --}}
        <div class="py-4 flex">
            <a href="{{ route('reports.cetak-pdf') }}" 
           class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Cetak PDF
        </a>
        </div>
    </div>
</div>
    @if(session('success'))
        <div class="alert alert-success mb-4 p-4 rounded-lg bg-green-50 border border-green-200">
            <div class="flex items-center">
                <i class="bi bi-check-circle-fill text-green-500 mr-2"></i>
                <span class="text-green-700">{{ session('success') }}</span>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger mb-4 p-4 rounded-lg bg-red-50 border border-red-200">
            <div class="flex items-center">
                <i class="bi bi-exclamation-circle-fill text-red-500 mr-2"></i>
                <span class="text-red-700">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Filter Trigger & Section -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
        <!-- Filter Trigger -->
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Laporan Pengaduan</h3>
                <p class="text-sm text-gray-500 mt-1">Kelola dan filter data pengaduan</p>
            </div>
            <div class="flex items-center space-x-3">
                <!-- Info Filter Aktif -->
                @if(request()->anyFilled(['search', 'status', 'category', 'start_date', 'end_date']))
                <div class="hidden sm:flex items-center space-x-2 text-sm text-blue-600 bg-blue-50 px-3 py-1.5 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                    </svg>
                    <span>Filter Aktif</span>
                </div>
                @endif
                
                <!-- Tombol Toggle Filter -->
                <button type="button" id="filterToggle" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                    </svg>
                    <span id="filterToggleText">Tampilkan Filter</span>
                </button>
            </div>
        </div>

        <!-- Filter Content (Awalnya Disembunyikan) -->
        <div id="filterContent" class="hidden mt-6 pt-6 border-t border-gray-200">
            <form action="{{ route('reports.all') }}" method="GET" class="space-y-6">
                <!-- Baris 1: Pencarian dan Dropdowns -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                    <!-- Pencarian -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <span>Pencarian</span>
                            </div>
                        </label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                            placeholder="Cari ID, deskripsi, atau kategori..."
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                    </div>

                    <!-- Filter Status -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Status</span>
                            </div>
                        </label>
                        <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                            <option value="">Semua Status</option>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Kategori -->
                    {{-- <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <span>Kategori</span>
                            </div>
                        </label>
                        <select name="category" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $value => $label)
                                <option value="{{ $value }}" {{ request('category') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div> --}}
                </div>

                <!-- Baris 2: Filter Tanggal -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tanggal Mulai -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>Dari Tanggal</span>
                            </div>
                        </label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                    </div>

                    <!-- Tanggal Sampai -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>Sampai Tanggal</span>
                            </div>
                        </label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                    </div>
                </div>

                <!-- Baris 3: Tombol Action -->
                <div class="flex flex-col sm:flex-row justify-between items-center pt-4 border-t border-gray-200">
                    <!-- Reset Button -->
                    <div class="mb-3 sm:mb-0">
                        <a href="{{ route('reports.all') }}" 
                        class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reset Semua Filter
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex space-x-3">
                        <button type="button" id="closeFilter" 
                                class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
                            Tutup
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                            </svg>
                            Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        </div> 
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Pengaduan</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalComplaints }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Menunggu</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $pendingCount }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-blue-400">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Diproses</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $processCount }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Selesai</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $resolvedCount }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Ditolak</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $rejectedCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Info -->
    @if(request()->anyFilled(['search', 'status', 'category', 'start_date', 'end_date']))
    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
        <p class="text-sm text-blue-700">
            Report menampilkan <strong>{{ $totalComplaints }}</strong> data 
            @if(request('search')) dengan kata kunci "<strong>{{ request('search') }}</strong>"@endif
            @if(request('status')) dengan status <strong>{{ $statuses[request('status')] ?? request('status') }}</strong>@endif
            @if(request('category')) dengan kategori <strong>{{ $categories[request('category')] ?? request('category') }}</strong>@endif
            @if(request('start_date')) dari tanggal <strong>{{ request('start_date') }}</strong>@endif
            @if(request('end_date')) sampai <strong>{{ request('end_date') }}</strong>@endif
        </p>
    </div>
    @endif

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($complaints as $index => $report)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ Str::limit($report->title, 70) }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                {{ $report->category }}
                            </span>
                        </td>
                        
                        <td class="px-4 py-3 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'process' => 'bg-blue-100 text-blue-800',
                                    'resolved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800'
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$report->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statuses[$report->status] ?? $report->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                            {{ $report->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-5 py-3 text-sm text-center">
                        
                        {{-- Aksi --}}
                        <div class="flex justify-center space-x-2">
                            <!-- Edit -->
                            <a href="{{ route('complaints.show', $report->id) }}" 
                               class="text-indigo-600 hover:text-indigo-900 font-medium transition duration-150 ease-in-out" 
                               title="Detail">
                                <img src="{{ asset('images/detail.png') }}" 
                                        alt="Detail Laporan" 
                                        class="w-6 h-6 mr-4 object-cover mr-3 ">
                            </a>

                            <!-- Edit -->
                            <a href="{{ route('complaints.edit', $report->id) }}" 
                               class="text-indigo-600 hover:text-indigo-900 font-medium transition duration-150 ease-in-out" 
                               title="Edit">
                                <img src="{{ asset('images/edit.png') }}" 
                                        alt="Edit" 
                                        class="w-6 h-6 mr-4 object-cover mr-3 ">
                            </a>
                            
                            <!-- Form Hapus -->
                            <form action="{{ route('complaints.destroy', $report->id) }}" method="POST" 
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus pegawai ini? Tindakan ini tidak dapat dibatalkan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-900 font-medium transition duration-150 ease-in-out" 
                                        title="Hapus">
                                    <img src="{{ asset('images/hapus.png') }}" 
                                        alt="hapus" 
                                        class="w-6 h-6 mr-4 object-cover mr-3  ">
                                </button>
                            </form>
                        </div>
                    </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500">
                            Tidak ada data yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none; }
    body { font-size: 12px; }
    .container-fluid { width: 100%; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #000; padding: 6px; }
    .bg-gray-50 { background-color: #f9fafb !important; }
    .shadow-md { box-shadow: none !important; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterToggle = document.getElementById('filterToggle');
    const filterContent = document.getElementById('filterContent');
    const filterToggleText = document.getElementById('filterToggleText');
    const closeFilter = document.getElementById('closeFilter');

    // Toggle filter visibility
    filterToggle.addEventListener('click', function() {
        const isHidden = filterContent.classList.contains('hidden');
        
        if (isHidden) {
            filterContent.classList.remove('hidden');
            filterToggleText.textContent = 'Sembunyikan Filter';
            filterToggle.classList.add('bg-blue-50', 'border-blue-200', 'text-blue-700');
        } else {
            filterContent.classList.add('hidden');
            filterToggleText.textContent = 'Tampilkan Filter';
            filterToggle.classList.remove('bg-blue-50', 'border-blue-200', 'text-blue-700');
        }
    });

    // Close filter button
    closeFilter.addEventListener('click', function() {
        filterContent.classList.add('hidden');
        filterToggleText.textContent = 'Tampilkan Filter';
        filterToggle.classList.remove('bg-blue-50', 'border-blue-200', 'text-blue-700');
    });

    // Auto show filter if any filter is active
    @if(request()->anyFilled(['search', 'status', 'category', 'start_date', 'end_date']))
        filterContent.classList.remove('hidden');
        filterToggleText.textContent = 'Sembunyikan Filter';
        filterToggle.classList.add('bg-blue-50', 'border-blue-200', 'text-blue-700');
    @endif
});
</script>
@endsection