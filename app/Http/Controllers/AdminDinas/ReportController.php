<?php

namespace App\Http\Controllers\AdminDinas;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    /**
     * Display reports page
     */
    public function index()
    {
        $user = Auth::user();
        $agencyId = $user->agency_id;

        if (!$agencyId) {
            return redirect()->back()->with('error', 'Akun Anda tidak terhubung dengan dinas.');
        }

        $agency = Agency::findOrFail($agencyId);

        // Statistics for report
        $totalComplaints = Complaint::where('agency_id', $agencyId)->count();
        $pendingCount = Complaint::where('agency_id', $agencyId)->where('status', 'pending')->count();
        $inProgressCount = Complaint::where('agency_id', $agencyId)->where('status', 'in_progress')->count();
        $resolvedCount = Complaint::where('agency_id', $agencyId)->where('status', 'resolved')->count();
        $rejectedCount = Complaint::where('agency_id', $agencyId)->where('status', 'rejected')->count();

        // Data for charts
        $monthlyData = Complaint::where('agency_id', $agencyId)
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($item) {
                $date = Carbon::create($item->year, $item->month, 1);
                return [
                    'month' => $date->format('M Y'),
                    'total' => $item->total
                ];
            })
            ->reverse();

        return view('admin_dinas.reports.index', compact(
            'agency',
            'totalComplaints',
            'pendingCount',
            'inProgressCount',
            'resolvedCount',
            'rejectedCount',
            'monthlyData',
            'user'
        ));
    }

    /**
     * Generate PDF report
     */
    public function generatePdf(Request $request)
    {
        $user = Auth::user();
        $agencyId = $user->agency_id;

        if (!$agencyId) {
            return redirect()->back()->with('error', 'Akun Anda tidak terhubung dengan dinas.');
        }

        $agency = Agency::findOrFail($agencyId);

        // Filter parameters
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $status = $request->get('status', 'all');
        $category = $request->get('category', 'all');

        // Base query
        $query = Complaint::with(['user'])
            ->where('agency_id', $agencyId);

        // Apply filters
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        $complaints = $query->orderBy('created_at', 'desc')->get();

        // Statistics
        $totalComplaints = $complaints->count();
        $pendingCount = $complaints->where('status', 'pending')->count();
        $inProgressCount = $complaints->where('status', 'in_progress')->count();
        $resolvedCount = $complaints->where('status', 'resolved')->count();
        $rejectedCount = $complaints->where('status', 'rejected')->count();

        $data = [
            'complaints' => $complaints,
            'agency' => $agency,
            'user' => $user,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'status' => $status,
            'category' => $category,
            'totalComplaints' => $totalComplaints,
            'pendingCount' => $pendingCount,
            'inProgressCount' => $inProgressCount,
            'resolvedCount' => $resolvedCount,
            'rejectedCount' => $rejectedCount,
            'exportDate' => now()->format('d/m/Y H:i'),
        ];

        // Set paper size and orientation
        $pdf = Pdf::loadView('admin_dinas.reports.pdf', $data)
            ->setPaper('a4', 'landscape');

        $filename = 'laporan-pengaduan-' . Str::slug($agency->name) . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate PDF report with custom filters
     */
    public function generateCustomPdf(Request $request)
    {
        $user = Auth::user();
        $agencyId = $user->agency_id;

        if (!$agencyId) {
            return redirect()->back()->with('error', 'Akun Anda tidak terhubung dengan dinas.');
        }

        $agency = Agency::findOrFail($agencyId);

        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:pending,in_progress,resolved,rejected,all',
            'category' => 'nullable|string'
        ]);

        $query = Complaint::with(['user'])
            ->where('agency_id', $agencyId);

        if (!empty($validated['start_date'])) {
            $query->whereDate('created_at', '>=', $validated['start_date']);
        }

        if (!empty($validated['end_date'])) {
            $query->whereDate('created_at', '<=', $validated['end_date']);
        }

        if (!empty($validated['status']) && $validated['status'] !== 'all') {
            $query->where('status', $validated['status']);
        }

        if (!empty($validated['category']) && $validated['category'] !== 'all') {
            $query->where('category', $validated['category']);
        }

        $complaints = $query->orderBy('created_at', 'desc')->get();

        // Statistics
        $totalComplaints = $complaints->count();
        $pendingCount = $complaints->where('status', 'pending')->count();
        $inProgressCount = $complaints->where('status', 'in_progress')->count();
        $resolvedCount = $complaints->where('status', 'resolved')->count();
        $rejectedCount = $complaints->where('status', 'rejected')->count();

        $data = [
            'complaints' => $complaints,
            'agency' => $agency,
            'user' => $user,
            'startDate' => $validated['start_date'] ?? null,
            'endDate' => $validated['end_date'] ?? null,
            'status' => $validated['status'] ?? 'all',
            'category' => $validated['category'] ?? 'all',
            'totalComplaints' => $totalComplaints,
            'pendingCount' => $pendingCount,
            'inProgressCount' => $inProgressCount,
            'resolvedCount' => $resolvedCount,
            'rejectedCount' => $rejectedCount,
            'exportDate' => now()->format('d/m/Y H:i'),
        ];

        // Set paper size and orientation for better layout
        $pdf = Pdf::loadView('admin_dinas.reports.pdf', $data)
            ->setPaper('a4', 'landscape');

        $filename = 'laporan-pengaduan-' . Str::slug($agency->name) . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * View PDF in browser
     */
    public function viewPdf(Request $request)
    {
        $user = Auth::user();
        $agencyId = $user->agency_id;

        if (!$agencyId) {
            return redirect()->back()->with('error', 'Akun Anda tidak terhubung dengan dinas.');
        }

        $agency = Agency::findOrFail($agencyId);

        // Filter parameters
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $status = $request->get('status', 'all');
        $category = $request->get('category', 'all');

        // Base query
        $query = Complaint::with(['user'])
            ->where('agency_id', $agencyId);

        // Apply filters
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        $complaints = $query->orderBy('created_at', 'desc')->get();

        // Statistics
        $totalComplaints = $complaints->count();
        $pendingCount = $complaints->where('status', 'pending')->count();
        $inProgressCount = $complaints->where('status', 'in_progress')->count();
        $resolvedCount = $complaints->where('status', 'resolved')->count();
        $rejectedCount = $complaints->where('status', 'rejected')->count();

        $data = [
            'complaints' => $complaints,
            'agency' => $agency,
            'user' => $user,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'status' => $status,
            'category' => $category,
            'totalComplaints' => $totalComplaints,
            'pendingCount' => $pendingCount,
            'inProgressCount' => $inProgressCount,
            'resolvedCount' => $resolvedCount,
            'rejectedCount' => $rejectedCount,
            'exportDate' => now()->format('d/m/Y H:i'),
        ];

        return view('admin_dinas.reports.pdf', $data);
    }
}
