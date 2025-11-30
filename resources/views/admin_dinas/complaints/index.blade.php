@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Pengaduan</h1>
            <p class="text-gray-600 mt-1">{{ $agency->name }}</p>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Pengaduan</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalComplaints }}</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-lg">
                    <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Menunggu</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $pendingCount }}</p>
                </div>
                <div class="p-3 bg-yellow-50 rounded-lg">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Diproses</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $inProgressCount }}</p>
                </div>
                <div class="p-3 bg-cyan-50 rounded-lg">
                    <i class="fas fa-spinner text-cyan-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Selesai</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $resolvedCount }}</p>
                </div>
                <div class="p-3 bg-green-50 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Pengaduan</h3>
        <form method="GET" action="{{ route('admin_dinas.complaints.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="all">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Diproses</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Selesai</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari pengaduan..." 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                    <i class="fas fa-filter mr-2"></i> Terapkan Filter
                </button>
                <a href="{{ route('admin_dinas.complaints.index') }}" 
                   class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition flex items-center">
                    <i class="fas fa-redo mr-2"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Complaints List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-900">Daftar Pengaduan</h3>
        <div class="flex items-center space-x-4">
            <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
                {{ $complaints->total() }} Pengaduan
            </span>
            <a href="{{ route('admin_dinas.reports.generate-pdf') }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
               <i class="fas fa-file-pdf mr-2"></i> Cetak Laporan
            </a>
        </div>
    </div>
    

        <div class="p-6">
            @if($complaints->count() > 0 )
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelapor</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($complaints as $index => $complaint)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-4 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ \Illuminate\Support\Str::limit($complaint->title, 40) }}</div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900">{{ $complaint->telegram_username ?? 'N/A' }}</td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $complaint->category }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ getStatusBadgeClass($complaint->status) }}">
                                        {{ getStatusText($complaint->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500">
                                    {{ $complaint->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin_dinas.complaints.show', $complaint->id) }}" 
                                           class="text-blue-600 hover:text-blue-900 transition" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin_dinas.complaints.edit', $complaint->id) }}" 
                                           class="text-yellow-600 hover:text-yellow-900 transition" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $complaints->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">Belum ada pengaduan</h4>
                    <p class="text-gray-500">Tidak ada pengaduan yang sesuai dengan filter yang dipilih.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@php
    // FIX: Hapus $this dan buat function biasa
    function getStatusText($status) {
        return [
            'pending' => 'Menunggu',
            'in_progress' => 'Diproses',
            'resolved' => 'Selesai',
            'rejected' => 'Ditolak'
        ][$status] ?? $status;
    }

    function getStatusBadgeClass($status) {
        return [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'resolved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800'
        ][$status] ?? 'bg-gray-100 text-gray-800';
    }

    function getPriorityText($priority) {
        return [
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi'
        ][$priority] ?? $priority;
    }

    function getPriorityBadgeClass($priority) {
        return [
            'low' => 'bg-green-100 text-green-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'high' => 'bg-red-100 text-red-800'
        ][$priority] ?? 'bg-gray-100 text-gray-800';
    }
@endphp