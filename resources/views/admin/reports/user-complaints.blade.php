@extends('layouts.admin')

@section('title', 'Laporan dari User: ' . $username)

@section('content')
<h2 class="text-xl font-semibold mb-6">Daftar Semua Laporan oleh @ {{ $username }}</h2>

<div class="mb-4">
    <a href="{{ route('reports.users') }}" class="text-blue-600 hover:text-blue-800">
        &larr; Kembali ke Daftar Pelapor
    </a>
</div>

<div class="overflow-x-auto bg-white rounded-lg shadow-md">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Submit</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi Singkat</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($complaints as $complaint)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ ($complaints->currentPage() - 1) * $complaints->perPage() + $loop->iteration }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    {{ $complaint->created_at?->format('d M Y H:i') }}
                    {{-- Jika $complaint->submitted_at NULL, tampilkan string kosong, bukan error --}}
                </td>                
                <td class="px-6 py-4 whitespace-nowrap">{{ $complaint->category }}</td>
                <td class="px-6 py-4">{{ Str::limit($complaint->description, 40) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $complaint->status == 'complete' ? 'green' : ($complaint->status == 'process' ? 'yellow' : 'red') }}-100 text-{{ $complaint->status == 'complete' ? 'green' : ($complaint->status == 'process' ? 'yellow' : 'red') }}-800">
                        {{ ucfirst($complaint->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    {{-- Ganti '#' dengan route detail pengaduan (Laporan 5) --}}
                    <a href="{{ route('complaints.show', $complaint->id) }}" class="text-indigo-600 hover:text-indigo-900">Lihat Histori</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    User ini belum memiliki laporan.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">
        {{ $complaints->links() }}
    </div>
</div>
@endsection