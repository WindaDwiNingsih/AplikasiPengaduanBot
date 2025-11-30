<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pengaduan - {{ $agency->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px;
            border: 1px solid #ddd;
        }
        .info-table .label {
            background-color: #f8f9fa;
            font-weight: bold;
            width: 30%;
        }
        .complaints-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .complaints-table th,
        .complaints-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .complaints-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .complaints-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-pending { background-color: #fff3cd; color: #856404; }
        .badge-in_progress { background-color: #cce7ff; color: #004085; }
        .badge-resolved { background-color: #d4edda; color: #155724; }
        .badge-rejected { background-color: #f8d7da; color: #721c24; }
        .badge-low { background-color: #d4edda; color: #155724; }
        .badge-medium { background-color: #fff3cd; color: #856404; }
        .badge-high { background-color: #f8d7da; color: #721c24; }
        .photo-info {
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN PENGADUAN MASYARAKAT</h1>
        <p>{{ $agency->name }}</p>
        <p>Periode: 
            @if($startDate && $endDate)
                {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            @else
                Semua Periode
            @endif
        </p>
        <p>Dicetak pada: {{ $exportDate }}</p>
    </div>


    <!-- Summary Statistics -->
    <div class="summary">
        <h3 style="margin-top: 0; margin-bottom: 10px;">Ringkasan Statistik</h3>
        <table style="width: 100%;">
            <tr>
                <td style="width: 20%;">Total: <strong>{{ $complaints->count() }}</strong></td>
                <td style="width: 20%;">Menunggu: <strong>{{ $pendingCount ?? 0 }}</strong></td>
                <td style="width: 20%;">Diproses: <strong>{{ $inProgressCount ?? 0 }}</strong></td>
                <td style="width: 20%;">Selesai: <strong>{{ $resolvedCount ?? 0 }}</strong></td>
                <td style="width: 20%;">Ditolak: <strong>{{ $rejectedCount ?? 0 }}</strong></td>
            </tr>
        </table>
    </div>

    <!-- Complaints Table -->
    <h3>Daftar Pengaduan</h3>
    <table class="complaints-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Pelapor</th>
                <th width="20%">Judul</th>
                <th width="15%">Kategori</th>
                <th width="20%">Deskripsi</th>
                <th width="15%">Alamat</th>
                <th width="10%">Status</th>
                <th width="10%">Foto</th>
                <th width="10%">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($complaints as $index => $complaint)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $complaint->user->name ?? ($complaint->telegram_username ?? 'N/A') }}</td>
                <td>{{ $complaint->title }}</td>
                <td>{{ $complaint->category }}</td>
                <td>{{ Str::limit($complaint->description, 50) }}</td>
                <td>
                    @if(isset($complaint->location['address']))
                        {{ Str::limit($complaint->location['address'], 30) }}
                    @elseif(isset($complaint->location['coordinates']))
                        {{ Str::limit($complaint->location['coordinates'], 30) }}
                    @else
                        Data Lokasi Tidak Tersedia
                    @endif
                </td>
                <td>
                    <span class="badge badge-{{ $complaint->status }}">
                        {{ getStatusText($complaint->status) }}
                    </span>
                </td>
                <td>
                    @if($complaint->photos && is_array($complaint->photos) && count($complaint->photos) > 0)
                        <div class="photo-info">
                            📷 {{ count($complaint->photos) }}
                        </div>
                    @else
                        <div class="photo-info">
                           -
                        </div>
                    @endif
                </td>
                <td>{{ $complaint->created_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center; padding: 20px; color: #666;">
                    Tidak ada data pengaduan untuk ditampilkan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Dicetak oleh: {{ $user->name }} ({{ $user->email }})</p>
        <p>Halaman 1 of 1</p>
    </div>
</body>
</html>

@php
    function getStatusText($status) {
        return [
            'pending' => 'Menunggu',
            'in_progress' => 'Diproses',
            'resolved' => 'Selesai',
            'rejected' => 'Ditolak'
        ][$status] ?? $status;
    }

    function getPriorityText($priority) {
        return [
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi'
        ][$priority] ?? $priority;
    }
@endphp