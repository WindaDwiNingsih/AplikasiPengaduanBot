<?php

namespace App\Http\Controllers\AdminDinas;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Agency;
use App\Models\AgencyCategory;
use App\Models\ComplaintHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the complaints for the agency.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $agencyId = $user->agency_id;

        // Validasi agency_id
        if (!$agencyId) {
            return redirect()->back()->with('error', 'Akun Anda tidak terhubung dengan dinas.');
        }

        $agency = Agency::findOrFail($agencyId);

        // Filter parameters
        $status = $request->get('status');
        $category = $request->get('category');
        $priority = $request->get('priority');
        $search = $request->get('search');

        // Base query - HANYA data dinas ini
        $query = Complaint::with(['user'])
            ->where('agency_id', $agencyId);

        // Apply filters
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        if ($priority && $priority !== 'all') {
            $query->where('priority', $priority);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $complaints = $query->orderBy('created_at', 'desc')->paginate(10);

        // Statistics for dashboard
        $totalComplaints = Complaint::where('agency_id', $agencyId)->count();
        $pendingCount = Complaint::where('agency_id', $agencyId)->where('status', 'pending')->count();
        $inProgressCount = Complaint::where('agency_id', $agencyId)->where('status', 'process')->count();
        $resolvedCount = Complaint::where('agency_id', $agencyId)->where('status', 'resolved')->count();

        // Get categories for filter dropdown (hanya kategori dinas ini)
        $categories = Complaint::where('agency_id', $agencyId)
            ->distinct()
            ->pluck('category')
            ->filter();

        // Kategori yang dimiliki dinas
        $agencyCategories = AgencyCategory::where('agency_id', $agencyId)
            ->orderBy('name')
            ->get();

        return view('admin_dinas.complaints.index', compact(
            'complaints',
            'agency',
            'totalComplaints',
            'pendingCount',
            'inProgressCount',
            'resolvedCount',
            'categories',
            'agencyCategories',
            'user',
            'status',
            'category',
            'priority',
            'search'
        ));
    }

    /**
     * Display the specified complaint.
     */
    public function show($id)
    {
        $user = Auth::user();
        $agencyId = $user->agency_id;

        $complaint = Complaint::with(['user', 'histories.user'])
            ->where('agency_id', $agencyId)
            ->findOrFail($id);

        return view('admin_dinas.complaints.show', compact('complaint', 'user'));
    }

    /**
     * Show the form for editing the specified complaint.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $agencyId = $user->agency_id;

        $complaint = Complaint::where('agency_id', $agencyId)
            ->findOrFail($id);

        // Ambil sub kategori khusus untuk dinas ini
        $subCategories = AgencyCategory::where('agency_id', $agencyId)
            ->whereNotNull('main_category')
            ->orderBy('name')
            ->pluck('name')
            ->toArray();

        $statuses = [
            'pending' => 'Pending',
            'process' => 'Process',
            'resolved' => 'Selesai',
            'rejected' => 'Ditolak'
        ];

        return view('admin_dinas.complaints.edit', compact(
            'complaint',
            'subCategories',
            'statuses',
            'user'
        ));
    }

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $agencyId = $user->agency_id;

            // Pastikan complaint milik dinas user
            $complaint = Complaint::where('agency_id', $agencyId)
                ->findOrFail($id);

            // Validasi input
            $validated = $request->validate([
                'sub_category' => 'required|string|max:100',
                'status' => 'required|in:pending,process,resolved,rejected',
                'status_notes' => 'nullable|string|max:500'
            ]);

            // CEK PERUBAHAN SEBELUM UPDATE
            $oldStatus = $complaint->status;
            $newStatus = $validated['status'];
            $oldSubCategory = $complaint->category;
            $newSubCategory = $validated['sub_category'];

            // PREPARE DATA UPDATE
            $updateData = [
                'category' => $validated['sub_category'],
                'status' => $validated['status'],
                'status_notes' => $validated['status_notes'] ?? null
            ];

            // AUTO-UPDATE MAIN_CATEGORY BERDASARKAN SUB_CATEGORY YANG DIPILIH
            $agencyCategory = AgencyCategory::where('agency_id', $agencyId)
                ->where('name', $newSubCategory)
                ->first();

            $categoryChanged = false;

            if ($agencyCategory) {
                $updateData['main_category'] = $agencyCategory->main_category;
                $updateData['agency_sub_category_id'] = $agencyCategory->id;
                $categoryChanged = ($oldSubCategory !== $newSubCategory);
            }

            // SIMPAN PERUBAHAN
            $complaint->update($updateData);

            // BUAT PESAN SUCCESS YANG INFORMATIF
            $successMessage = 'Laporan berhasil diperbarui.';

            if ($oldStatus !== $newStatus) {
                $statusLabels = [
                    'pending' => 'Pending',
                    'process' => 'Proses',
                    'resolved' => 'Selesai',
                    'rejected' => 'Ditolak'
                ];

                $successMessage .= " Status diubah dari {$statusLabels[$oldStatus]} ke {$statusLabels[$newStatus]}.";
            }

            if ($categoryChanged) {
                $successMessage .= " Sub kategori diubah menjadi {$newSubCategory}.";
            }

            return redirect()->route('admin_dinas.complaints.index')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            Log::error('Error updating complaint: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal memperbarui laporan: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function getStatusText($status)
    {
        $statuses = [
            'pending' => 'Menunggu',
            'process' => 'Diproses',
            'resolved' => 'Selesai',
            'rejected' => 'Ditolak'
        ];

        return $statuses[$status] ?? $status;
    }
    private function getStatusBadgeClass($status)
    {
        return [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'process' => 'bg-blue-100 text-blue-800',
            'resolved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800'
        ][$status] ?? 'bg-gray-100 text-gray-800';
    }

}
