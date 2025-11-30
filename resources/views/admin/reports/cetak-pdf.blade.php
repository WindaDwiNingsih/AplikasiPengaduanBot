<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Pengaduan - Landscape</title>
    <style>
        @page {
            size: landscape;
            margin: 1cm;
        }
        body { 
            font-family: 'DejaVu Sans', Arial, sans-serif; 
            font-size: 10px; 
            margin: 0;
            padding: 0;
        }
        .header { 
            text-align: center; 
            margin-bottom: 15px; 
            border-bottom: 2px solid #333; 
            padding-bottom: 8px; 
        }
        .table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px;
            table-layout: fixed;
        }
        .table th, .table td { 
            border: 1px solid #ddd; 
            padding: 6px; 
            text-align: left; 
            vertical-align: top;
            word-wrap: break-word;
        }
        .table th { 
            background-color: #f5f5f5; 
            font-weight: bold; 
            font-size: 9px;
        }
        .badge { 
            padding: 3px 6px; 
            border-radius: 10px; 
            font-size: 8px; 
            font-weight: bold; 
            display: inline-block;
            text-align: center;
        }
        .badge-pending { background-color: #fef3c7; color: #92400e; }
        .badge-process { background-color: #dbeafe; color: #1e40af; }
        .badge-resolved { background-color: #d1fae5; color: #065f46; }
        .badge-rejected { background-color: #fee2e2; color: #991b1b; }
        .text-center { text-align: center; }
        .photo-info { 
            width: 40px; 
            height: 40px; 
            background: #f5f5f5; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 8px; 
            color: #666;
            margin: 0 auto;
        }
        .summary {
            margin-top: 15px; 
            padding: 10px; 
            background-color: #f8fafc; 
            border-radius: 4px;
            font-size: 9px;
        }
        .footer {
            margin-top: 20px; 
            text-align: right; 
            font-size: 8px; 
            color: #6b7280;
        }
        
        /* Column widths for landscape optimization */
        .col-no { width: 3%; }
        .col-ticket { width: 6%; }
        .col-reporter { width: 8%; }
        .col-title { width: 12%; }
        .col-category { width: 8%; }
        .col-description { width: 15%; }
        .col-address { width: 15%; }
        .col-status { width: 7%; }
        .col-date { width: 7%; }
        .col-photo { width: 5%; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1 style="font-size: 16px; font-weight: bold; margin-bottom: 3px;">LAPORAN DATA PENGADUAN MASYARAKAT</h1>
        <p style="color: #6b7280; margin-bottom: 3px; font-size: 9px;">Sistem Pengaduan Masyarakat</p>
        <p style="color: #6b7280; margin-bottom: 0; font-size: 8px;">Format: Landscape</p>
    </div>

    <!-- Table -->
    <table class="table">
        <thead>
            <tr>
                <th class="col-no text-center">No</th>
                <th class="col-ticket text-center">No Tiket</th>
                <th class="col-reporter">Pelapor</th>
                <th class="col-title">Judul</th>
                <th class="col-category">Kategori</th>
                <th class="col-description">Deskripsi</th>
                <th class="col-address">Alamat</th>
                <th class="col-status text-center">Status</th>
                <th class="col-date text-center">Tanggal</th>
                <th class="col-photo text-center">Foto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($complaints as $index => $complaint)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">{{ $complaint->id }}</td>
                <td>{{ $complaint->telegram_username ?? 'N/A' }}</td>
                <td>{{ Str::limit($complaint->title, 30) }}</td>
                <td>{{ $complaint->category }}</td>
                <td>{{ Str::limit($complaint->description, 80) }}</td>
                <td>
                    {{ 
                        Str::limit($complaint->location['address'] ?? 
                        $complaint->location['coordinates'] ?? 
                        'Data Lokasi Tidak Tersedia', 50) 
                    }}
                </td>
                <td class="text-center">
                    @php
                        $badgeClass = 'badge-' . $complaint->status;
                        $statusText = match($complaint->status) {
                            'pending' => 'Menunggu',
                            'process' => 'Diproses',
                            'resolved' => 'Selesai',
                            'rejected' => 'Ditolak',
                            default => $complaint->status
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                </td>
                <td class="text-center">{{ $complaint->created_at->format('d/m/Y') }}</td>
                <td class="text-center">
                    @if($complaint->photos && is_array($complaint->photos) && count($complaint->photos) > 0)
                        <div class="photo-info">
                            📷 {{ count($complaint->photos) }}
                        </div>
                    @else
                        <div>-</div>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary -->
    <div class="summary">
        <strong>Total Pengaduan: {{ $totalComplaints }}</strong> | 
        <span style="color: #92400e;">Menunggu: {{ $pendingCount }}</span> | 
        <span style="color: #1e40af;">Diproses: {{ $processCount }}</span> | 
        <span style="color: #065f46;">Selesai: {{ $resolvedCount }}</span> | 
        <span style="color: #991b1b;">Ditolak: {{ $rejectedCount }}</span>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dicetak pada: {{ $tanggalCetak }}</p>
        <p>Total Data: {{ $complaints->count() }} pengaduan</p>
    </div>
</body>
</html>