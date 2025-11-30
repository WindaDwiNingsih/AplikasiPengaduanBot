<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>History Pengaduan #{{ $complaint->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; }
        .subtitle { font-size: 14px; color: #666; }
        .info-section { margin-bottom: 20px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .info-table td { padding: 5px; border: 1px solid #ddd; }
        .info-table .label { background: #f5f5f5; font-weight: bold; width: 30%; }
        .history-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .history-table th { background: #333; color: white; padding: 8px; text-align: left; }
        .history-table td { padding: 8px; border: 1px solid #ddd; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #666; }
        .status-badge { padding: 3px 8px; border-radius: 10px; font-size: 10px; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-process { background: #d1ecf1; color: #0c5460; }
        .status-complete { background: #d4edda; color: #155724; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">HISTORY PERUBAHAN STATUS PENGADUAN</div>
        <div class="subtitle">Nomor Tiket: {{ $complaint->id }}</div>
    </div>

    {{-- Informasi Pengaduan --}}
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td class="label">ID Pengaduan</td>
                <td> {{ $complaint->id }}</td>
            </tr>
            <tr>
                <td class="label">Pelapor</td>
                <td>{{ $complaint->telegram_username ?: 'Tidak tersedia' }}</td>
            </tr>
            <tr>
                <td class="label">Kategori</td>
                <td>{{ $complaint->category }}</td>
            </tr>
            <tr>
                <td class="label">Status Saat Ini</td>
                <td>
                    <span class="status-badge status-{{ $complaint->status }}">
                        {{ ucfirst($complaint->status) }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="label">Deskripsi</td>
                <td>{{ $complaint->description }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Dibuat</td>
                <td>{{ $complaint->created_at->format('d M Y H:i') }}</td>
            </tr>
        </table>
    </div>

    {{-- Tabel History --}}
    <div style="margin-top: 25px;">
        <h3 style="margin-bottom: 10px;">Riwayat Perubahan Status</h3>
        
        @if($histories->count() > 0)
        <table class="history-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">Tanggal</th>
                    <th width="20%">User</th>
                    <th width="15%">Dari</th>
                    <th width="15%">Ke</th>
                    <th width="30%">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($histories as $index => $history)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $history->created_at->format('d M Y H:i') }}</td>
                    <td>{{ $history->user->name ?? 'System' }}</td>
                    <td>
                        <span class="status-badge status-{{ $history->old_status }}">
                            {{ ucfirst($history->old_status) }}
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $history->new_status }}">
                            {{ ucfirst($history->new_status) }}
                        </span>
                    </td>
                    <td>{{ $history->notes ?: '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p style="text-align: center; color: #666; padding: 20px;">
            Tidak ada riwayat perubahan status
        </p>
        @endif
    </div>

    <div class="footer">
        Dicetak pada: {{ $tanggalCetak }}
    </div>
</body>
</html>