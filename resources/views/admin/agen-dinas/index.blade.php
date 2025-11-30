@extends('layouts.admin')

@section('title', 'Daftar User Agen Dinas')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Search Bar -->
        <div class="p-4 border-b">
            <form action="{{ route('admin.agen-dinas.index') }}" method="GET">
                <div class="flex">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari nama, email, atau username..."
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-r-lg">
                        Cari
                    </button>
                    <a href="{{ route('admin.agen-dinas.cetak-pdf') }}" 
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Cetak PDF
                    </a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            No
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nama
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Agency
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal Bergabung
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($agenDinas as $index => $agen)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $loop->iteration + ($agenDinas->currentPage() - 1) * $agenDinas->perPage() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr($agen->name, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $agen->name }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $agen->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($agen->agency)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ">
                                    {{ $agen->agency->name }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Tidak ada agen
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $agen->created_at->format('d M Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            Tidak ada data agen dinas ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($agenDinas->hasPages())
        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $agenDinas->links() }}
        </div>
        @endif
    </div>

    <!-- Info Summary -->
    <div class="mt-4 text-sm text-gray-600">
        Total: <strong>{{ $agenDinas->total() }}</strong> user agen dinas 
    </div>
</div>
@endsection