<?php

namespace App\Http\Controllers;

use App\Models\AgencyCategory;
use App\Models\Agency;
use App\Models\Complaint;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $user = Auth::user();
        $mainCategories = $this->categoryService->getMainCategories();

        if ($user->role === 'superadmin') {
            // ⭐ SIMPLE: Ambil semua sub categories (yang punya main_category)
            $agencyCategories = AgencyCategory::with(['agency', 'creator'])
                ->whereNotNull('main_category') // Hanya yang punya main_category
                ->orderBy('agency_id')
                ->orderBy('main_category')
                ->orderBy('name')
                ->get();

            $agencies = Agency::where('is_active', true)->get();
        } else {
            // ⭐ SIMPLE: Ambil sub categories untuk dinas user
            $agencyCategories = AgencyCategory::with(['creator'])
                ->where('agency_id', $user->agency_id)
                ->whereNotNull('main_category') // Hanya yang punya main_category
                ->orderBy('main_category')
                ->orderBy('name')
                ->get();

            $agencies = Agency::where('id', $user->agency_id)->get();
        }

        return view('admin_dinas.categories.index', compact(
            'agencyCategories',
            'agencies',
            'user',
            'mainCategories'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'main_category' => 'required|string|in:Infrastruktur,Lingkungan,Pendidikan,Kesehatan,Transportasi,Lainnya',
            'agency_id' => 'nullable|exists:agencies,id'
        ]);

        DB::beginTransaction();

        try {
            $agencyId = $user->role === 'superadmin'
                ? ($validated['agency_id'] ?? null)
                : $user->agency_id;

            if (!$agencyId) {
                return back()->with('error', 'Dinas tidak ditemukan untuk akun Anda.');
            }

            // ⭐ SIMPLE: Cek duplikasi berdasarkan main_category + name + agency_id
            $existingCategory = AgencyCategory::where('main_category', $validated['main_category'])
                ->where('name', $validated['name'])
                ->where('agency_id', $agencyId)
                ->exists();

            if ($existingCategory) {
                return back()->with('error', 'Sub kategori "' . $validated['name'] . '" sudah ada untuk ' . $validated['main_category'] . ' di dinas ini.');
            }

            // ⭐ SIMPLE: Buat sub category langsung
            AgencyCategory::create([
                'name' => $validated['name'],
                'main_category' => $validated['main_category'],
                'agency_id' => $agencyId,
                'created_by' => $user->id
            ]);

            DB::commit();

            return redirect()->route('admin_dinas.categories.index')
                ->with('success', 'Sub kategori "' . $validated['name'] . '" berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating sub category: ' . $e->getMessage());
            return back()->with('error', 'Gagal menambahkan sub kategori: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(AgencyCategory $agencyCategory)
    {
        $user = Auth::user();

        // Authorization check
        if ($user->role !== 'superadmin' && $agencyCategory->agency_id !== $user->agency_id) {
            abort(403, 'Unauthorized action.');
        }

        DB::beginTransaction();

        try {
            // ⭐ SIMPLE: Cek apakah digunakan di complaints
            $usedInComplaints = Complaint::where('sub_category', $agencyCategory->name)
                ->orWhere('agency_sub_category_id', $agencyCategory->id)
                ->exists();

            if ($usedInComplaints) {
                return back()->with(
                    'error',
                    'Tidak dapat menghapus sub kategori "' . $agencyCategory->name . '" karena sudah digunakan dalam pengaduan.'
                );
            }

            $agencyCategory->delete();

            DB::commit();

            return redirect()->route('admin_dinas.categories.index')
                ->with('success', 'Sub kategori berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting sub category: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus sub kategori: ' . $e->getMessage());
        }
    }

    /**
     * API untuk mendapatkan sub kategori berdasarkan kategori utama
     */
    public function getSubCategories($mainCategory)
    {
        $subCategories = $this->categoryService->getSubCategories($mainCategory);
        return response()->json($subCategories);
    }
}
