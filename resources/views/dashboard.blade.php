@extends('layouts.admin')

@section('title', 'Dashboard SuperAdmin')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard SuperAdmin</h1>
        <p class="text-gray-600">Overview semua laporan pengaduan</p>
    </div>

    {{-- Grafik Statistik --}}
    @include('components.complaint-charts')

    {{-- Konten dashboard lainnya --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent complaints, etc --}}
    </div>
</div>
@endsection