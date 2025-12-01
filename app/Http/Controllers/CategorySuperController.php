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

class CategorySuperController extends Controller
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
            $agencyCategories = AgencyCategory::with(['agency', 'creator'])
                ->whereNotNull('main_category')
                ->orderBy('agency_id')
                ->orderBy('main_category')
                ->orderBy('name')
                ->get();

            $agencies = Agency::where('is_active', true)->get();
        } else {
            $agencyCategories = AgencyCategory::with(['creator'])
                ->where('agency_id', $user->agency_id)
                ->whereNotNull('main_category')
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

            // Cek duplikasi
            $existingCategory = AgencyCategory::where('main_category', $validated['main_category'])
                ->where('name', $validated['name'])
                ->where('agency_id', $agencyId)
                ->exists();

            if ($existingCategory) {
                return back()->with('error', 'Sub kategori "' . $validated['name'] . '" sudah ada untuk ' . $validated['main_category'] . ' di dinas ini.');
            }

            // Create category
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AgencyCategory $agencyCategory)
    {
        $user = Auth::user();

        // Authorization check
        if ($user->role !== 'superadmin' && $agencyCategory->agency_id !== $user->agency_id) {
            abort(403, 'Unauthorized action.');
        }

        $mainCategories = $this->categoryService->getMainCategories();
        $agencies = $user->role === 'superadmin'
            ? Agency::where('is_active', true)->get()
            : Agency::where('id', $user->agency_id)->get();

        return view('admin_dinas.categories.edit', compact(
            'agencyCategory',
            'agencies',
            'mainCategories',
            'user'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AgencyCategory $agencyCategory)
    {
        $user = Auth::user();

        // Authorization check
        if ($user->role !== 'superadmin' && $agencyCategory->agency_id !== $user->agency_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'main_category' => 'required|string|in:Infrastruktur,Lingkungan,Pendidikan,Kesehatan,Transportasi,Lainnya',
            'agency_id' => 'nullable|exists:agencies,id'
        ]);

        DB::beginTransaction();

        try {
            $agencyId = $user->role === 'superadmin'
                ? ($validated['agency_id'] ?? $agencyCategory->agency_id)
                : $user->agency_id;

            // Cek duplikasi (kecuali untuk record yang sedang diupdate)
            $existingCategory = AgencyCategory::where('main_category', $validated['main_category'])
                ->where('name', $validated['name'])
                ->where('agency_id', $agencyId)
                ->where('id', '!=', $agencyCategory->id)
                ->exists();

            if ($existingCategory) {
                return back()->with('error', 'Sub kategori "' . $validated['name'] . '" sudah ada untuk ' . $validated['main_category'] . ' di dinas ini.');
            }

            // Update category
            $agencyCategory->update([
                'name' => $validated['name'],
                'main_category' => $validated['main_category'],
                'agency_id' => $agencyId,
            ]);

            DB::commit();

            return redirect()->route('admin_dinas.categories.index')
                ->with('success', 'Sub kategori "' . $validated['name'] . '" berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating sub category: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui sub kategori: ' . $e->getMessage())->withInput();
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
            // Cek apakah digunakan di complaints
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
        $user = Auth::user();

        $subCategories = AgencyCategory::where('main_category', $mainCategory)
            ->when($user->role !== 'superadmin', function ($query) use ($user) {
                return $query->where('agency_id', $user->agency_id);
            })
            ->orderBy('name')
            ->get()
            ->pluck('name')
            ->toArray();

        return response()->json($subCategories);
    }
}
