<?php

namespace App\Services;

use App\Models\ProcurementRequest;
use App\Models\Project;
use App\Models\Delivery;
use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Enums\RequestStatus;

class ReportService
{
    /**
     * Generate Request Detail Report
     */
    public function generateRequestDetailReport(ProcurementRequest $request, bool $download = true)
    {
        // Load relationships
        $request->load([
            'project',
            'requestedBy',
            'procurementOfficer',
            'approvedBy',
            'items.material',
            'items.vendor',
            'statusHistory.changedBy',
            'deliveries.verifiedBy'
        ]);

        // Check if current user can view pricing
        $canViewPricing = $this->canViewPricing();

        $data = [
            'request' => $request,
            'canViewPricing' => $canViewPricing,
            'generatedAt' => now(),
            'generatedBy' => Auth::user(),
        ];

        $pdf = Pdf::loadView('reports.request-detail', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = "request-{$request->request_number}-" . now()->format('Ymd') . ".pdf";

        if ($download) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }

    /**
     * Generate Project Summary Report
     */
    public function generateProjectSummaryReport(
        Project $project,
        ?Carbon $startDate = null,
        ?Carbon $endDate = null,
        bool $download = true
    ) {
        // Default to all time if no dates provided
        $startDate = $startDate ?? Carbon::create(2020, 1, 1);
        $endDate = $endDate ?? now();

        // Load project data
        $project->load([
            'siteManagers',
            'procurementOfficers',
            'requests.items.material',
            'requests.items.vendor'
        ]);

        // Filter requests by date range
        $requests = $project->requests()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['items.material', 'items.vendor', 'requestedBy'])
            ->get();

        // Fetch project expenses within date range
        $expenses = $project->expenses()
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->get();

        $expenseBreakdown = $expenses->groupBy('expense_type')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total_value' => $group->sum('amount')
            ];
        });

        // Calculate statistics
        $stats = [

            'total_requests' => $requests->count(),

            'approved_requests' => $requests->where('status', 'approved')->count(),

            'rejected_requests' => $requests->where('status', 'rejected')->count(),

            'pending_requests' => $requests->whereIn('status', [

                'submitted',

                'pending_procurement',

                'procurement_processing',

                'pending_director'

            ])->count(),

            'total_value' => $requests->sum('total_quoted_amount'),
            
            'total_project_spending' => $requests->sum('total_quoted_amount') + $expenses->sum('amount'),

        ];

        // Status breakdown
        $statusBreakdown = $requests
            ->groupBy(fn($req) => $req->status->value)
            ->mapWithKeys(function ($group, $status) {
                $enum = RequestStatus::from($status);

                return [
                    $status => [
                        'label' => $enum->label(),
                        'count' => $group->count(),
                        'total_value' => $group->sum('total_quoted_amount'),
                    ],
                ];
            })
            ->toArray();

        // Vendor breakdown
        $vendorBreakdown = [];
        foreach ($requests as $request) {
            foreach ($request->items as $item) {
                if ($item->vendor_id) {
                    $vendorName = $item->vendor->name ?? 'Unknown';
                    if (!isset($vendorBreakdown[$vendorName])) {
                        $vendorBreakdown[$vendorName] = [
                            'count' => 0,
                            'total_value' => 0,
                        ];
                    }
                    $vendorBreakdown[$vendorName]['count']++;
                    $vendorBreakdown[$vendorName]['total_value'] += $item->quoted_total_price ?? 0;
                }
            }
        }

        // Material breakdown
        $materialBreakdown = [];
        foreach ($requests as $request) {
            foreach ($request->items as $item) {
                $materialName = $item->material->name ?? 'Unknown';
                if (!isset($materialBreakdown[$materialName])) {
                    $materialBreakdown[$materialName] = [
                        'quantity' => 0,
                        'total_value' => 0,
                    ];
                }
                $materialBreakdown[$materialName]['quantity'] += $item->quantity;
                $materialBreakdown[$materialName]['total_value'] += $item->quoted_total_price ?? 0;
            }
        }

        // Check if current user can view pricing
        $canViewPricing = $this->canViewPricing();

        $data = [
            'project' => $project,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'requests' => $requests,
            'stats' => $stats,
            'statusBreakdown' => $statusBreakdown,
            'vendorBreakdown' => $vendorBreakdown,
            'materialBreakdown' => $materialBreakdown,
            'expenses' => $expenses,
            'expenseBreakdown' => $expenseBreakdown,
            'canViewPricing' => $canViewPricing,
            'generatedAt' => now(),
            'generatedBy' => Auth::user(),
        ];

        $pdf = Pdf::loadView('reports.project-summary', $data);
        $pdf->setPaper('a4', 'landscape');

        $filename = "project-{$project->code}-summary-" . now()->format('Ymd') . ".pdf";

        if ($download) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }

    /**
     * Generate Delivery Receipt
     */
    public function generateDeliveryReceipt(Delivery $delivery, bool $download = true)
    {
        // Load relationships
        $delivery->load([
            'request.project',
            'requestItem.material',
            'vendor',
            'receivedBy',
            'verifiedBy'
        ]);

        $data = [
            'delivery' => $delivery,
            'generatedAt' => now(),
            'generatedBy' => Auth::user(),
        ];

        $pdf = Pdf::loadView('reports.delivery-receipt', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = "delivery-{$delivery->delivery_number}-" . now()->format('Ymd') . ".pdf";

        if ($download) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }

    /**
     * Generate Vendor Transaction Report
     */
    public function generateVendorTransactionReport(
        Vendor $vendor,
        ?Carbon $startDate = null,
        ?Carbon $endDate = null,
        bool $download = true
    ) {
        // Default to last 12 months if no dates provided
        $startDate = $startDate ?? now()->subMonths(12);
        $endDate = $endDate ?? now();

        // Get all request items for this vendor within date range
        $requestItems = $vendor->requestItems()
            ->with([
                'request.project',
                'material',
                'deliveries'
            ])
            ->whereHas('request', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->get();

        // Calculate statistics
        $stats = [
            'total_orders' => $requestItems->count(),
            'total_value' => $requestItems->sum(function ($item) {
                return $item->quantity * ($item->quoted_unit_price ?? 0);
            }),
            'completed_deliveries' => 0,
            'pending_deliveries' => 0,
        ];

        foreach ($requestItems as $item) {
            $totalDelivered = $item->deliveries()
                ->whereIn('verification_status', ['accepted', 'partial'])
                ->sum('quantity_delivered');

            if ($totalDelivered >= $item->quantity) {
                $stats['completed_deliveries']++;
            } else {
                $stats['pending_deliveries']++;
            }
        }

        // Check if current user can view pricing
        $canViewPricing = $this->canViewPricing();

        $data = [
            'vendor' => $vendor,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'requestItems' => $requestItems,
            'stats' => $stats,
            'canViewPricing' => $canViewPricing,
            'generatedAt' => now(),
            'generatedBy' => Auth::user(),
        ];

        $pdf = Pdf::loadView('reports.vendor-transactions', $data);
        $pdf->setPaper('a4', 'landscape');

        $filename = "vendor-{$vendor->id}-transactions-" . now()->format('Ymd') . ".pdf";

        if ($download) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }

    /**
     * Check if current user can view pricing information
     */
    protected function canViewPricing(): bool
    {
        $user = Auth::user();

        // Site managers cannot view pricing
        if ($user->hasRole('site_manager') && !$user->hasAnyRole(['admin', 'super_admin', 'director', 'procurement_officer'])) {
            return false;
        }

        return true;
    }
}