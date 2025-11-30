@extends('layouts.admin')

@section('title', 'Detail Pengaduan No Tiket ' . $complaint->id)

@section('content')
<div class="max-w-4xl mx-auto">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Detail Pengaduan</h2>
    
    <div class="bg-white p-6 rounded-lg shadow-xl mb-8">
        <div class="grid grid-cols-2 gap-y-4">
            
            <div class="col-span-2">
                <p class="text-sm font-medium text-gray-500">Pelapor (Telegram)</p>
                <p class="text-lg font-semibold text-gray-900">@ {{ $complaint->telegram_username ?: 'N/A' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Status Saat Ini</p>
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                    @if($complaint->status == 'resolved') bg-green-100 text-green-800
                    @elseif($complaint->status == 'process') bg-blue-100 text-blue-800
                    @elseif($complaint->status == 'rejected') bg-red-100 text-red-800
                    @else bg-yellow-100 text-yellow-800
                    @endif">
                    {{ ucfirst($complaint->status) }}
                </span>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Tanggal Submit</p>
                <p class="text-md text-gray-700">{{ $complaint->created_at?->format('d F Y, H:i') ?: 'N/A' }}</p>
            </div>
            
            <div class="col-span-2 mt-4 border-t pt-4">
                <p class="text-sm font-medium text-gray-500">Lokasi Pengaduan (Koordinat/Alamat)</p>
                <p class="text-md text-gray-700">
                    {{ 
                        $complaint->location['address'] ?? 
                        $complaint->location['coordinates'] ?? 
                        'Data Lokasi Tidak Tersedia' 
                    }}
                </p>            
            </div>

            <div class="col-span-2 mt-4">
                <p class="text-sm font-medium text-gray-500">Deskripsi Pengaduan</p>
                <p class="text-md text-gray-700 whitespace-pre-line">{{ $complaint->description }}</p>
            </div>
            
            {{-- Tautan untuk melihat foto/media (jika ada) --}}
            @if($complaint->media_path)
            <div class="col-span-2 mt-4">
                <p class="text-sm font-medium text-gray-500">Lampiran Media</p>
                <a href="{{ asset('storage/' . $complaint->media_path) }}" target="_blank" class="text-blue-600 hover:underline">
                    Lihat Lampiran Foto/File
                </a>
            </div>
            @endif
            
            <div class="col-span-2 mt-8">
                <h3 class="text-xl font-semibold mb-6 border-b pb-2">Histori Penanganan </h3>
                
                <ol class="relative border-s border-gray-200 dark:border-gray-700 ml-4">                  
                    @forelse ($complaint->histories as $history)
                    <li class="mb-8 ms-6">            
                        <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -start-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                            <svg class="w-2.5 h-2.5 text-blue-800 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                            </svg>
                        </span>
                        <h4 class="flex items-center mb-1 text-lg font-semibold text-gray-900 dark:text-white">
                            Status Diubah Menjadi: {{ ucfirst($history->new_status) }}
                            @if ($history->new_status == 'complete')
                                <span class="bg-green-100 text-green-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300 ms-3">Selesai</span>
                            @endif
                        </h4>
                        <time class="block mb-2 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
                            Pada {{ $history->created_at->format('d F Y') }} pukul {{ $history->created_at->format('H:i') }}
                            @if ($history->user)
                                oleh <span class="font-medium text-gray-600">{{ $history->user->name }} ({{ $history->user->role }})</span>
                            @else
                                oleh Sistem/Admin yang telah dihapus
                            @endif
                        </time>
                        <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">
                            Catatan: {{ $history->notes ?: 'Tidak ada catatan yang dilampirkan.' }}
                        </p>
                    </li>
                    {{-- di show.blade.php --}}
                    
                    @empty
                    <p class="text-gray-500 italic ml-6">Belum ada perubahan status atau histori penanganan yang dicatat.</p>
                    @endforelse
                </ol>
            </div>
        </div>
    </div>
    <div class="flex space-x-2 mt-4">
                        <a href="{{ route('complaints.cetak-history', $complaint->id) }}" 
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded flex items-center"
                        target="_blank">
                            <i class="bi bi-printer mr-2"></i>Cetak History
                        </a>
                    </div>
    <a href="{{ url()->previous() }}" class="text-gray-500 hover:text-gray-700 mt-4 inline-block">&larr; Kembali</a>
</div>
@endsection