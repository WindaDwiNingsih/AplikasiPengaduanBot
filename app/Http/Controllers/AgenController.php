<?php
// app/Http/Controllers/AgenController.php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;

class AgenController extends Controller
{
    /**
     * Menampilkan daftar user agen dinas
     */
    public function index(Request $request)
    {
        try {
            // Query user dengan role admin_dinas
            $query = User::where('role', 'admin_dinas')->with('agency');

            // Filter pencarian
            if ($request->has('search') && $request->search != '') {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('email', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Urutkan berdasarkan tanggal dibuat
            $agenDinas = $query->orderBy('created_at', 'desc')->paginate(10);

            return view('admin.agen-dinas.index', compact('agenDinas'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Cetak PDF daftar agen dinas
     */
    public function cetakPdf()
    {
        try {
            // Ambil semua data agen dinas
            $agenDinas = User::where('role', 'admin_dinas')
                ->orderBy('name', 'asc')
                ->get();

            // Data untuk PDF
            $data = [
                'agenDinas' => $agenDinas,
                'tanggalCetak' => now()->format('d F Y H:i:s'),
                'totalAgen' => $agenDinas->count()
            ];

            return Pdf::view('admin.agen-dinas.pdf', $data)
                ->format('a4')
                ->name('daftar-agen-dinas-' . now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mencetak PDF: ' . $e->getMessage());
        }
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
}
