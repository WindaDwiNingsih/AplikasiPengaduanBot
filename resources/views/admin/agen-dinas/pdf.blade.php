<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar User Agen Dinas</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f5f5f5; }
        .badge-active { background-color: #d1fae5; color: #065f46; padding: 4px 8px; border-radius: 12px; font-size: 12px; }
        .badge-inactive { background-color: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 12px; font-size: 12px; }
        .footer { margin-top: 20px; text-align: right; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1 style="font-size: 24px; font-weight: bold; margin-bottom: 5px;">DAFTAR AGEN DINAS</h1>
        <p style="color: #6b7280; margin-bottom: 10px;">Sistem Pengaduan Masyarakat</p>
        <hr style="border: 1px solid #e5e7eb; margin-bottom: 20px;">
    </div>

    <!-- Table -->
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Agency</th>
                <th>Tanggal Bergabung</th>
            </tr>
        </thead>
        <tbody>
            @foreach($agenDinas as $index => $agen)
            <tr>
                <td style="width: 50px;">{{ $loop->iteration }}</td>
                <td>{{ $agen->name }}</td>
                <td>{{ $agen->email }}</td>
                 <td>
                    @if($agen->agency)
                        <span class="badge badge-success">{{ $agen->agency->name }}</span>
                    @else
                        <span class="badge badge-secondary">Tidak ada agen</span>
                    @endif
                </td>
                <td>{{ $agen->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary -->
    <div style="margin-top: 20px; padding: 10px; background-color: #f9fafb; border-radius: 6px;">
        <strong>Total Agen Dinas: {{ $totalAgen }}</strong>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dicetak pada: {{ $tanggalCetak }}</p>
    </div>
</body>
</html>