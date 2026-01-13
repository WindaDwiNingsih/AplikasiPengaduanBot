<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Dinas - {{ now()->format('d-m-Y') }}</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { font-size: 16px; margin: 0; }
        .header p { font-size: 10px; color: #666; margin: 5px 0; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        .table th { background-color: #f5f5f5; font-weight: bold; }
        .badge { padding: 2px 6px; border-radius: 10px; font-size: 9px; }
        .badge-active { background-color: #d4edda; color: #155724; }
        .badge-inactive { background-color: #f8d7da; color: #721c24; }
        .summary { margin-top: 15px; padding: 10px; background-color: #f8f9fa; border-radius: 4px; font-size: 10px; }
        .footer { margin-top: 20px; text-align: right; font-size: 9px; color: #666; border-top: 1px solid #ddd; padding-top: 5px; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>DAFTAR DINAS KABUPATEN/KOTA</h1>
        <p>Sistem Pengaduan Masyarakat</p>
        <p>Tanggal Cetak: {{ $tanggalCetak }}</p>
        @if($filterStatus)
        <p>Filter Status: {{ $filterStatus == 'active' ? 'Aktif' : 'Non-Aktif' }}</p>
        @endif
    </div>

    <!-- Summary -->
    <div class="summary">
        <strong>Total Dinas: {{ $totalAgencies }}</strong> | 
        <span style="color: #155724;">Aktif: {{ $activeAgencies }}</span> | 
        <span style="color: #721c24;">Non-Aktif: {{ $inactiveAgencies }}</span>
    </div>

    <!-- Table -->
    <table class="table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Nama Dinas</th>
                <th width="15%">Kode</th>
                <th>Deskripsi</th>
                <th width="10%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($agencies as $index => $agency)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $agency->name }}</td>
                <td><strong>{{ $agency->code }}</strong></td>
                <td>{{ $agency->description ?: '-' }}</td>
                <td class="text-center">
                    @if($agency->is_active)
                        <span class="badge badge-active">Aktif</span>
                    @else
                        <span class="badge badge-inactive">Non-Aktif</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Dicetak oleh: Sistem Pengaduan Masyarakat</p>
        <p>Halaman 1 dari 1</p>
    </div>
</body>
</html>