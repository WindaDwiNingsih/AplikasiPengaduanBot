@extends('layouts.admin')

@section('title', 'Report User Pelapor')

@section('content')
<h2 class="text-xl font-semibold mb-6">Daftar Pengguna Telegram Berdasarkan Jumlah Laporan</h2>
<div class="py-4 border-b">
    <div class="flex">
        <a href="{{ route('reports.pelapor_pdf') }}" 
                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Cetak PDF
        </a>
    </div>
</div>
<div class="overflow-x-auto bg-white rounded-lg shadow-md">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username Telegram</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Laporan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($userReports as $index => $report)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ $report->telegram_username }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-lg font-bold text-red-600">
                    {{ $report->total_reports }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('reports.users.detail', ['username' => $report->telegram_username]) }}" 
                    class="text-indigo-600 hover:text-indigo-900">
                        Lihat Detail 
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                    Tidak ada data pengguna yang terdaftar sebagai pelapor.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection