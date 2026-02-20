<?php

namespace App\Http\Controllers;

use App\Models\ProcurementRequest;
use App\Models\Project;
use App\Models\Delivery;
use App\Models\Vendor;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
        $this->middleware('auth');
    }

    /**
     * Display reports dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // Check permission
        if (!$user->hasPermission('view-procurement-reports')) {
            abort(403, 'You do not have permission to access reports.');
        }

        // Get data for quick stats
        $stats = [
            'total_projects' => Project::count(),
            'total_requests' => ProcurementRequest::count(),
            'total_vendors' => Vendor::where('status', 'active')->count(),
            'total_deliveries' => Delivery::count(),
        ];

        // Get recent reports data
        $recentRequests = ProcurementRequest::with(['project', 'requestedBy'])
            ->latest()
            ->limit(10)
            ->get();

        $recentProjects = Project::withCount('requests')
            ->latest()
            ->limit(10)
            ->get();

        $activeVendors = Vendor::where('status', 'active')
            ->withCount(['requestItems'])
            ->orderBy('name')
            ->limit(10)
            ->get();

        return view('reports.index', compact('stats', 'recentRequests', 'recentProjects', 'activeVendors'));
    }

    /**
     * Generate Request Detail Report
     */
    public function requestDetail(ProcurementRequest $request, Request $httpRequest)
    {
        // Check authorization
        if (!Auth::user()->can('view', $request)) {
            abort(403, 'Unauthorized action.');
        }

        $download = $httpRequest->get('download', '1') === '1';

        return $this->reportService->generateRequestDetailReport($request, $download);
    }

    /**
     * Generate Project Summary Report
     */
    public function projectSummary(Project $project, Request $request)
    {
        // Check authorization
        if (!Auth::user()->can('viewAny', Project::class)) {
            abort(403, 'Unauthorized action.');
        }

        // Parse date filters
        $startDate = $request->has('start_date')
            ? Carbon::parse($request->get('start_date'))
            : null;

        $endDate = $request->has('end_date')
            ? Carbon::parse($request->get('end_date'))
            : null;

        $download = $request->get('download', '1') === '1';

        return $this->reportService->generateProjectSummaryReport(
            $project,
            $startDate,
            $endDate,
            $download
        );
    }

    /**
     * Generate Delivery Receipt
     */
    public function deliveryReceipt(Delivery $delivery, Request $request)
    {
        $user = Auth::user();
        // Check authorization - any user who can view deliveries
        if (!$user->hasPermission('view-deliveries')) {
            abort(403, 'Unauthorized action.');
        }

        $download = $request->get('download', '1') === '1';

        return $this->reportService->generateDeliveryReceipt($delivery, $download);
    }

    /**
     * Generate Vendor Transaction Report
     */
    public function vendorTransactions(Vendor $vendor, Request $request)
    {
        // Check authorization - only admin, super_admin, director, procurement_officer
        if (!Auth::user()->hasAnyRole(['admin', 'super_admin', 'director', 'procurement_officer'])) {
            abort(403, 'Unauthorized action.');
        }

        // Parse date filters
        $startDate = $request->has('start_date')
            ? Carbon::parse($request->get('start_date'))
            : null;

        $endDate = $request->has('end_date')
            ? Carbon::parse($request->get('end_date'))
            : null;

        $download = $request->get('download', '1') === '1';

        return $this->reportService->generateVendorTransactionReport(
            $vendor,
            $startDate,
            $endDate,
            $download
        );
    }
}