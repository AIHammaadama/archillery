<?php

namespace App\Services;

use App\Models\User;
use App\Models\Project;
use App\Models\ProcurementRequest;
use App\Models\Vendor;
use App\Models\Material;
use App\Models\Delivery;
use App\Enums\RequestStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    protected $cacheTime = 900; // 15 minutes

    /**
     * Get Site Manager dashboard data
     */
    public function getSiteManagerDashboard(User $user): array
    {
        $cacheKey = "dashboard_site_manager_{$user->id}";

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($user) {
            // Projects assigned to this site manager
            $myProjects = Project::whereHas('assignments', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->where('role_type', 'site_manager')
                    ->where('is_active', true);
            })->count();

            // Requests created by this user 
            $myRequests = ProcurementRequest::where('requested_by', $user->id)->count();

            // Pending requests (draft)
            $pendingRequests = ProcurementRequest::where('requested_by', $user->id)
                ->where('status', RequestStatus::DRAFT)
                ->count();

            // Approved requests
            $approvedRequests = ProcurementRequest::where('requested_by', $user->id)
                ->where('status', RequestStatus::APPROVED)
                ->count();

            // Recent requests
            $recentRequests = ProcurementRequest::where('requested_by', $user->id)
                ->with(['project', 'items'])
                ->latest()
                ->limit(5)
                ->get();

            // Request trends (last 6 months)
            $requestTrends = $this->getRequestTrends($user, 'site_manager');

            // Status distribution
            $statusDistribution = $this->getRequestStatusDistribution($user, 'site_manager');

            return [
                'stats' => [
                    'my_projects' => $myProjects,
                    'my_requests' => $myRequests,
                    'pending_requests' => $pendingRequests,
                    'approved_requests' => $approvedRequests,
                ],
                'recent_requests' => $recentRequests,
                'charts' => [
                    'request_trends' => $requestTrends,
                    'status_distribution' => $statusDistribution,
                ],
            ];
        });
    }

    /**
     * Get Procurement Officer dashboard data
     */
    public function getProcurementOfficerDashboard(User $user): array
    {
        $cacheKey = "dashboard_procurement_officer_{$user->id}";

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($user) {
            // Assigned projects
            $assignedProjects = Project::whereHas('assignments', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->where('role_type', 'procurement_officer')
                    ->where('is_active', true);
            })->count();

            // Pending vendor assignments
            $pendingAssignments = ProcurementRequest::whereIn('status', [
                RequestStatus::SUBMITTED,
                RequestStatus::PENDING_PROCUREMENT,
                RequestStatus::PROCUREMENT_PROCESSING
            ])
                ->whereHas('project', function ($query) use ($user) {
                    $query->whereHas('assignments', function ($q) use ($user) {
                        $q->where('user_id', $user->id)
                            ->where('role_type', 'procurement_officer')
                            ->where('is_active', true);
                    });
                })
                ->count();

            // Processed requests
            $processedRequests = ProcurementRequest::where('procurement_officer_id', $user->id)
                ->count();

            // Active vendors
            $activeVendors = Vendor::where('status', 'active')->count();

            // Recent procurement activity
            $recentActivity = ProcurementRequest::whereHas('project', function ($query) use ($user) {
                $query->whereHas('assignments', function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->where('role_type', 'procurement_officer');
                });
            })
                ->with(['project', 'requestedBy'])
                ->latest()
                ->limit(5)
                ->get();

            // Vendor engagement stats
            $vendorEngagement = $this->getVendorEngagementStats();

            // Request trends
            $requestTrends = $this->getRequestTrends($user, 'procurement_officer');

            return [
                'stats' => [
                    'assigned_projects' => $assignedProjects,
                    'pending_assignments' => $pendingAssignments,
                    'processed_requests' => $processedRequests,
                    'active_vendors' => $activeVendors,
                ],
                'recent_activity' => $recentActivity,
                'charts' => [
                    'vendor_engagement' => $vendorEngagement,
                    'request_trends' => $requestTrends,
                ],
            ];
        });
    }

    /**
     * Get Director dashboard data
     */
    public function getDirectorDashboard(User $user): array
    {
        $cacheKey = "dashboard_director_{$user->id}";

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($user) {
            // Total projects
            $totalProjects = Project::count();

            // Pending approvals
            $pendingApprovals = ProcurementRequest::where('status', RequestStatus::PENDING_DIRECTOR)
                ->count();

            // Approved this month
            $approvedThisMonth = ProcurementRequest::where('status', RequestStatus::APPROVED)
                ->whereMonth('updated_at', date('m'))
                ->whereYear('updated_at', date('Y'))
                ->count();

            // Total budget allocated
            $totalBudget = Project::where('status', 'active')->sum('budget');

            // Total procurement value (approved requests)
            $totalProcurement = ProcurementRequest::where('status', RequestStatus::APPROVED)
                ->sum('total_quoted_amount');

            // Budget utilization percentage
            $budgetUtilization = $totalBudget > 0
                ? round(($totalProcurement / $totalBudget) * 100, 2)
                : 0;

            // Recent approval queue
            $approvalQueue = ProcurementRequest::where('status', RequestStatus::PENDING_DIRECTOR)
                ->with(['project', 'requestedBy', 'procurementOfficer', 'items'])
                ->latest()
                ->limit(5)
                ->get();

            // Monthly trends
            $monthlyTrends = $this->getMonthlyProcurementTrends();

            // Status distribution (all requests)
            $statusDistribution = $this->getAllRequestsStatusDistribution();

            // Budget utilization by project
            $budgetByProject = $this->getBudgetUtilizationByProject();

            return [
                'stats' => [
                    'total_projects' => $totalProjects,
                    'pending_approvals' => $pendingApprovals,
                    'approved_this_month' => $approvedThisMonth,
                    'total_budget' => $totalBudget,
                    'total_procurement' => $totalProcurement,
                    'budget_utilization' => $budgetUtilization,
                ],
                'approval_queue' => $approvalQueue,
                'charts' => [
                    'monthly_trends' => $monthlyTrends,
                    'status_distribution' => $statusDistribution,
                    'budget_by_project' => $budgetByProject,
                ],
            ];
        });
    }

    /**
     * Get Admin dashboard data
     */
    public function getAdminDashboard(): array
    {
        $cacheKey = "dashboard_admin";

        return Cache::remember($cacheKey, $this->cacheTime, function () {
            // System-wide statistics
            $stats = [
                'total_users' => User::count(),
                'total_projects' => Project::count(),
                'total_requests' => ProcurementRequest::count(),
                'total_vendors' => Vendor::count(),
                'total_materials' => Material::count(),
                'active_projects' => Project::where('status', 'active')->count(),
                'pending_approvals' => ProcurementRequest::where('status', RequestStatus::PENDING_DIRECTOR)->count(),
                'total_deliveries' => Delivery::count(),
            ];

            // Recent system activity
            $recentRequests = ProcurementRequest::with(['project', 'requestedBy'])
                ->latest()
                ->limit(10)
                ->get();

            // Monthly trends
            $monthlyTrends = $this->getMonthlyProcurementTrends();

            // Status distribution
            $statusDistribution = $this->getAllRequestsStatusDistribution();

            // Top vendors by transactions
            $topVendors = $this->getTopVendorsByTransactions();

            return [
                'stats' => $stats,
                'recent_requests' => $recentRequests,
                'charts' => [
                    'monthly_trends' => $monthlyTrends,
                    'status_distribution' => $statusDistribution,
                    'top_vendors' => $topVendors,
                ],
            ];
        });
    }

    /**
     * Get request trends for a user
     */
    protected function getRequestTrends(User $user, string $role): array
    {
        $months = [];
        $counts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('M Y');

            $query = ProcurementRequest::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);

            if ($role === 'site_manager') {
                $query->where('requested_by', $user->id);
            } elseif ($role === 'procurement_officer') {
                $query->where('procurement_officer_id', $user->id);
            }

            $months[] = $month;
            $counts[] = $query->count();
        }

        return [
            'labels' => $months,
            'data' => $counts,
        ];
    }

    /**
     * Get request status distribution for a user
     */
    protected function getRequestStatusDistribution(User $user, string $role): array
    {
        $query = ProcurementRequest::select('status', DB::raw('count(*) as count'));

        if ($role === 'site_manager') {
            $query->where('requested_by', $user->id);
        } elseif ($role === 'procurement_officer') {
            $query->where('procurement_officer_id', $user->id);
        }

        $distribution = $query->groupBy('status')->get();

        $labels = [];
        $data = [];

        foreach ($distribution as $item) {
            $labels[] = $item->status->label();
            $data[] = $item->count;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get all requests status distribution
     */
    protected function getAllRequestsStatusDistribution(): array
    {
        $distribution = ProcurementRequest::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $labels = [];
        $data = [];

        foreach ($distribution as $item) {
            $labels[] = $item->status->label();
            $data[] = $item->count;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get vendor engagement statistics
     */
    protected function getVendorEngagementStats(): array
    {
        $vendorStats = Vendor::select('vendors.name', DB::raw('count(request_items.id) as transaction_count'))
            ->join('request_items', 'vendors.id', '=', 'request_items.vendor_id')
            ->groupBy('vendors.id', 'vendors.name')
            ->orderBy('transaction_count', 'desc')
            ->limit(10)
            ->get();

        $labels = [];
        $data = [];

        foreach ($vendorStats as $stat) {
            $labels[] = $stat->name;
            $data[] = $stat->transaction_count;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get monthly procurement trends (last 6 months)
     */
    protected function getMonthlyProcurementTrends(): array
    {
        $months = [];
        $requestCounts = [];
        $amounts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('M Y');

            $requests = ProcurementRequest::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);

            $months[] = $month;
            $requestCounts[] = $requests->count();
            $amounts[] = $requests->sum('total_quoted_amount');
        }

        return [
            'labels' => $months,
            'requests' => $requestCounts,
            'amounts' => $amounts,
        ];
    }

    /**
     * Get budget utilization by project (top 10)
     */
    protected function getBudgetUtilizationByProject(): array
    {
        $projects = Project::select(
            'projects.name',
            'projects.budget',
            DB::raw('COALESCE(SUM(procurement_requests.total_quoted_amount), 0) as spent')
        )
            ->leftJoin('procurement_requests', function ($join) {
                $join->on('projects.id', '=', 'procurement_requests.project_id')
                    ->where('procurement_requests.status', '=', RequestStatus::APPROVED->value);
            })
            ->where('projects.budget', '>', 0)
            ->groupBy('projects.id', 'projects.name', 'projects.budget')
            ->orderBy('spent', 'desc')
            ->limit(10)
            ->get();

        $labels = [];
        $budgets = [];
        $spent = [];

        foreach ($projects as $project) {
            $labels[] = $project->name;
            $budgets[] = $project->budget;
            $spent[] = $project->spent;
        }

        return [
            'labels' => $labels,
            'budgets' => $budgets,
            'spent' => $spent,
        ];
    }

    /**
     * Get top vendors by transaction count
     */
    protected function getTopVendorsByTransactions(): array
    {
        $vendors = Vendor::select(
            'vendors.name',
            DB::raw('count(request_items.id) as transaction_count'),
            DB::raw('COALESCE(SUM(request_items.quantity * request_items.quoted_unit_price), 0) as total_value')
        )
            ->join('request_items', 'vendors.id', '=', 'request_items.vendor_id')
            ->groupBy('vendors.id', 'vendors.name')
            ->orderBy('transaction_count', 'desc')
            ->limit(5)
            ->get();

        $labels = [];
        $counts = [];
        $values = [];

        foreach ($vendors as $vendor) {
            $labels[] = $vendor->name;
            $counts[] = $vendor->transaction_count;
            $values[] = $vendor->total_value;
        }

        return [
            'labels' => $labels,
            'counts' => $counts,
            'values' => $values,
        ];
    }

    /**
     * Clear dashboard cache for a user
     */
    public function clearUserCache(User $user): void
    {
        Cache::forget("dashboard_site_manager_{$user->id}");
        Cache::forget("dashboard_procurement_officer_{$user->id}");
        Cache::forget("dashboard_director_{$user->id}");
        Cache::forget("dashboard_admin");
    }

    /**
     * Clear all dashboard caches
     */
    public function clearAllCaches(): void
    {
        $users = User::all();
        foreach ($users as $user) {
            $this->clearUserCache($user);
        }
    }
}