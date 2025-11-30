<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Pelapor Telegram</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* CSS Tambahan untuk membantu rendering */
        body { 
            font-family: sans-serif; 
            -webkit-print-color-adjust: exact; /* Memaksa Chrome mencetak warna bg */
        }
        @page {
            /* Mengatur margin halaman PDF */
            margin: 40px;
        }
    </style>
</head>
<body class="bg-white p-4">
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Laporan Data Pelapor (Telegram)</h2>
        <p class="text-sm text-gray-600">Total Pelapor Unik: {{ $pelapor_data->count() }} | Dicetak: {{ date('d F Y') }}</p>
    </div>

    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-100">
                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-medium text-gray-700">No</th>
                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-medium text-gray-700">ID Telegram</th>
                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-medium text-gray-700">Username Telegram</th>
                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-medium text-gray-700">Status</th>
                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-medium text-gray-700">Keluhan Pertama</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pelapor_data as $key => $pelapor)
            <tr class="hover:bg-gray-50">
                <td class="border border-gray-300 px-4 py-2">{{ $key + 1 }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $pelapor->telegram_user_id }}</td> 
                <td class="border border-gray-300 px-4 py-2">
                    @if ($pelapor->telegram_username)
                        {{ '@' . $pelapor->telegram_username }}
                    @else
                        <span class="text-gray-500 italic">N/A</span>
                    @endif
                </td>
                <td class="border border-gray-300 px-4 py-2">Pelapor Bot</td> 
                <td class="border border-gray-300 px-4 py-2">{{ \Carbon\Carbon::parse($pelapor->first_report_date)->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="border border-gray-300 px-4 py-2 text-center text-gray-500">
                    Tidak ada data pelapor yang ditemukan.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>