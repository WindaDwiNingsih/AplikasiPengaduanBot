<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AgencyController extends Controller
{
    /**
     * Display a listing of agencies.
     */
    public function index(Request $request)
    {
        $query = Agency::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($request->filled('status') && in_array($request->status, ['active', 'inactive'])) {
            $status = $request->status == 'active' ? 1 : 0;
            $query->where('is_active', $status);
        }

        // Sort
        $sort = $request->get('sort', 'name');
        $order = $request->get('order', 'asc');
        $query->orderBy($sort, $order);

        $agencies = $query->paginate(10);

        // TAMBAHKAN STATISTICS
        $totalAgencies = Agency::count();
        $activeAgencies = Agency::where('is_active', 1)->count();
        $inactiveAgencies = Agency::where('is_active', 0)->count();

        return view('admin.agencies.index', compact(
            'agencies',
            'totalAgencies',
            'activeAgencies',
            'inactiveAgencies'
        ));
    }

    /**
     * Show the form for creating a new agency.
     */
    public function create()
    {
        return view('admin.agencies.create');
    }

    /**
     * Store a newly created agency.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:agencies',
            'code' => 'required|string|max:20|unique:agencies',
            'description' => 'nullable|string|max:500',
            'is_active' => 'required|boolean'
        ]);

        try {
            Agency::create([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'description' => $validated['description'],
                'is_active' => $validated['is_active']
            ]);

            return redirect()->route('admin.agencies.index')
                ->with('success', 'Dinas berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error creating agency: ' . $e->getMessage());
            return back()->with('error', 'Gagal menambahkan dinas.');
        }
    }

    /**
     * Display the specified agency.
     */
    public function show($id)
    {
        $agency = Agency ::findOrFail($id);
        $userCount = $agency->users()->where('role', 'admin_dinas')->count();
        return view('admin.agencies.show', compact('agency', 'userCount'));
    }

    /**
     * Show the form for editing an agency.
     */
    public function edit($id)
    {
        $agency = Agency::findOrFail($id);
        return view('admin.agencies.edit', compact('agency'));
    }

    /**
     * Update the specified agency.
     */
    public function update(Request $request, $id)
    {
        $agency = Agency::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:agencies,name,' . $agency->id,
            'code' => 'required|string|max:20|unique:agencies,code,' . $agency->id,
            'description' => 'nullable|string|max:500',
            'is_active' => 'required|boolean'
        ]);

        try {
            $agency->update([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'description' => $validated['description'],
                'is_active' => $validated['is_active']
            ]);

            return redirect()->route('admin.agencies.index')
                ->with('success', 'Data dinas berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating agency: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui data dinas.');
        }
    }

    /**
     * Remove the specified agency.
     */
    public function destroy($id)
    {
        $agency = Agency::findOrFail($id);
        // Check if agency has users
        if ($agency->users()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus dinas yang memiliki pegawai.');
        }

        try {
            $agency->delete();
            return redirect()->route('admin.agencies.index')
                ->with('success', 'Dinas berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting agency: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus dinas.');
        }
    }

    /**
     * Toggle agency status.
     */
    public function toggleStatus($id) 
    {
        Log::info('Toggle Status Called:', [
            'id' => $id,
            'full_url' => request()->fullUrl(),
            'route_name' => request()->route()->getName()
        ]);

        if (!$id) {
            Log::error('No ID provided for toggle-status!');
            return back()->with('error', 'ID tidak ditemukan.');
        }
        try {
            $agency = Agency::findOrFail($id); 

            $agency->update([
                'is_active' => !$agency->is_active
            ]);

            $status = $agency->is_active ? 'diaktifkan' : 'dinonaktifkan';
            return back()->with('success', "Dinas " . $agency->name . " berhasil $status.");
        } catch (\Exception $e) {
            Log::error('Error toggling agency status: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengubah status dinas.');
        }
    }
    public function employees($id)
    {
        try {
            

            // Cek agency
            $agency = Agency::find($id);
            Log::info('Agency found:', ['agency' => $agency ? $agency->toArray() : 'NOT FOUND']);

            if (!$agency) {
                return back()->with('error', 'Dinas tidak ditemukan');
            }

            // Debug 1: Cek jumlah user dengan agency_id ini
            $userCount = User::where('agency_id', $id)->count();
            Log::info('Total users with agency_id ' . $id . ':', ['count' => $userCount]);

            // Debug 2: Cek beberapa user contoh
            $sampleUsers = User::where('agency_id', $id)->take(3)->get();
            Log::info('Sample users:', ['users' => $sampleUsers->toArray()]);

            // Debug 3: Cek relasi
            $testUser = User::where('agency_id', $id)->first();
            if ($testUser) {
                Log::info('Test user agency relation:', [
                    'user_id' => $testUser->id,
                    'agency_id' => $testUser->agency_id,
                    'agency_relation_exists' => $testUser->agency ? 'YES' : 'NO'
                ]);
            }

            // Coba query dengan error handling
            $pegawai = User::with(['agency' => function ($query) {
                $query->select('id', 'name', 'code');
            }])
                ->where('agency_id', $id)
                ->orderBy('name')
                ->get();

            Log::info('Query successful, found:', ['count' => $pegawai->count()]);
            Log::info('=== END AGENCY EMPLOYEES ===');

            return view('admin.agencies.employees', compact('agency', 'pegawai'));
        } catch (\Exception $e) {
            Log::error('ERROR in employees method:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
