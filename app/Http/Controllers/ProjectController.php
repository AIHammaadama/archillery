<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\State;
use App\Models\Lga;
use App\Models\ProjectAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Enums\RequestStatus;
use App\Events\ProjectAssigned;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Project::class);

        $user = Auth::user();

        // Directors and Admins see all projects, others see only assigned projects
        $query = Project::with(['state', 'lga', 'creator', 'siteManagers', 'procurementOfficers']);

        if (!$user->hasAnyRole(['director', 'admin', 'super_admin'])) {
            // Filter to only assigned projects for Site Managers and Procurement Officers
            $query->whereHas('assignments', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('is_active', true);
            });
        }

        // Fetch all projects (DataTables handles filtering and pagination on client-side)
        $projects = $query->latest()->get();

        // Calculate Stats
        $stats = [
            'total' => $projects->count(),
            'active' => $projects->where('status', 'active')->count(),
            'completed' => $projects->where('status', 'completed')->count(),
            'on_hold' => $projects->where('status', 'on_hold')->count(),
            'total_budget' => $user->hasPermission('view-request-pricing') ? $projects->sum('budget') : 0,
        ];

        return view('projects.index', compact('projects', 'stats'));
    }

    /**
     * Show the form for creating a new project
     */
    public function create()
    {
        $this->authorize('create', Project::class);

        $states = State::where('status', 1)->orderBy('state')->get();
        $siteManagers = User::whereHas('role', function ($q) {
            $q->where('slug', 'site_manager');
        })->where('status', 1)->get();
        $procurementOfficers = User::whereHas('role', function ($q) {
            $q->where('slug', 'procurement_officer');
        })->where('status', 1)->get();

        return view('projects.create', compact('states', 'siteManagers', 'procurementOfficers'));
    }

    /**
     * Store a newly created project
     */
    public function store(Request $request)
    {
        $this->authorize('create', Project::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'state_id' => 'nullable|exists:states,id',
            'lga_id' => 'nullable|exists:lgas,id',
            'status' => 'required|in:planning,active,on_hold,completed,cancelled',

            'site_managers' => 'nullable|array',
            'site_managers.*' => 'exists:users,id',

            'procurement_officers' => 'nullable|array',
            'procurement_officers.*' => 'exists:users,id',

            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpeg,png,jpg,pdf|max:5120', // 5MB max
        ]);

        DB::beginTransaction();

        try {
            /**
             * Handle attachments upload
             */
            $attachments = [];

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('projects', 'public');

                    $attachments[] = [
                        'original_name' => $file->getClientOriginalName(),
                        'path'          => $path,
                        'type'          => $file->getClientMimeType(),
                        'size'          => $file->getSize(),
                    ];
                }
            }

            /**
             * Create Project
             */
            $project = Project::create([
                'name'        => $validated['name'],
                'code'        => $this->generateProjectCode(),
                'description' => $validated['description'] ?? null,
                'location'    => $validated['location'] ?? null,
                'budget'      => $validated['budget'] ?? null,
                'start_date'  => $validated['start_date'] ?? null,
                'end_date'    => $validated['end_date'] ?? null,
                'state_id'    => $validated['state_id'] ?? null,
                'lga_id'      => $validated['lga_id'] ?? null,
                'status'      => $validated['status'],
                'attachments' => $attachments,
                'created_by'  => Auth::id(),
            ]);

            /**
             * Assign Site Managers
             */
            if (!empty($validated['site_managers'])) {
                foreach ($validated['site_managers'] as $userId) {
                    ProjectAssignment::create([
                        'project_id' => $project->id,
                        'user_id'    => $userId,
                        'role_type'  => 'site_manager',
                        'assigned_by' => Auth::id(),
                        'is_active'  => true,
                    ]);

                    $user = User::find($userId);
                    if ($user) {
                        event(new ProjectAssigned($project, $user, 'site_manager'));
                    }
                }
            }

            /**
             * Assign Procurement Officers
             */
            if (!empty($validated['procurement_officers'])) {
                foreach ($validated['procurement_officers'] as $userId) {
                    ProjectAssignment::create([
                        'project_id' => $project->id,
                        'user_id'    => $userId,
                        'role_type'  => 'procurement_officer',
                        'assigned_by' => Auth::id(),
                        'is_active'  => true,
                    ]);

                    $user = User::find($userId);
                    if ($user) {
                        event(new ProjectAssigned($project, $user, 'procurement_officer'));
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('projects.index')
                ->with('success', 'Project created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Failed to create project: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified project
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $project->load([
            'state',
            'lga',
            'creator',
            'siteManagers',
            'procurementOfficers',
            'procurementRequests.requestedBy',
            'procurementRequests' => function ($query) {
                $query->latest();
            }
        ]);

        // Calculate Project Stats
        $pendingStatuses = [
            RequestStatus::SUBMITTED,
            RequestStatus::PENDING_PROCUREMENT,
            RequestStatus::PROCUREMENT_PROCESSING,
            RequestStatus::PENDING_DIRECTOR
        ];

        $approvedStatuses = [
            RequestStatus::APPROVED,
            RequestStatus::PARTIALLY_DELIVERED,
            RequestStatus::FULLY_DELIVERED
        ];

        $stats = [
            'total_requests' => $project->procurementRequests->count(),
            'pending_requests' => $project->procurementRequests->whereIn('status', $pendingStatuses)->count(),
            'approved_requests' => $project->procurementRequests->whereIn('status', $approvedStatuses)->count(),
            'spent_amount' => $project->procurementRequests->whereIn('status', $approvedStatuses)->sum('total_quoted_amount'),
            'budget_percentage' => 0
        ];

        if ($project->budget > 0) {
            $stats['budget_percentage'] = min(100, round(($stats['spent_amount'] / $project->budget) * 100, 1));
        }

        return view('projects.show', compact('project', 'stats'));
    }

    /**
     * Display all requests for the specified project
     */
    public function requests(Request $request, Project $project)
    {
        $this->authorize('view', $project); // Same permission as viewing project details

        $query = $project->procurementRequests()->with(['requestedBy']); // Filtered by project via relationship

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        return view('projects.requests', compact('project', 'requests'));
    }

    /**
     * Show the form for editing the specified project
     */
    public function edit(Project $project)
    {
        $this->authorize('update', $project);

        $states = State::where('status', 1)->orderBy('state')->get();
        $lgas = $project->state_id
            ? Lga::where('state_id', $project->state_id)->where('status', 1)->get()
            : collect();

        $siteManagers = User::whereHas('role', function ($q) {
            $q->where('slug', 'site_manager');
        })->where('status', 1)->get();

        $procurementOfficers = User::whereHas('role', function ($q) {
            $q->where('slug', 'procurement_officer');
        })->where('status', 1)->get();

        $assignedSiteManagers = $project->assignments()
            ->where('role_type', 'site_manager')
            ->where('is_active', true)
            ->pluck('user_id')
            ->toArray();

        $assignedProcurementOfficers = $project->assignments()
            ->where('role_type', 'procurement_officer')
            ->where('is_active', true)
            ->pluck('user_id')
            ->toArray();

        return view('projects.edit', compact(
            'project',
            'states',
            'lgas',
            'siteManagers',
            'procurementOfficers',
            'assignedSiteManagers',
            'assignedProcurementOfficers'
        ));
    }

    /**
     * Update the specified project
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'state_id' => 'nullable|exists:states,id',
            'lga_id' => 'nullable|exists:lgas,id',
            'status' => 'required|in:planning,active,on_hold,completed,cancelled',

            'site_managers' => 'nullable|array',
            'site_managers.*' => 'exists:users,id',

            'procurement_officers' => 'nullable|array',
            'procurement_officers.*' => 'exists:users,id',

        ]);

        if ($request->hasFile('attachments')) {
            $request->validate([
                'attachments.*' => 'file|mimes:jpeg,png,jpg,pdf|max:5120',
            ]);
        }

        DB::beginTransaction();

        try {
            /**
             * Handle attachment uploads (append, not overwrite)
             */
            $existingAttachments = $project->attachments ?? [];
            $newAttachments = [];

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('projects', 'public');

                    $newAttachments[] = [
                        'original_name' => $file->getClientOriginalName(),
                        'path'          => $path,
                        'type'          => $file->getClientMimeType(),
                        'size'          => $file->getSize(),
                    ];
                }
            }

            /**
             * Update Project
             */
            $project->update([
                'name'        => $validated['name'],
                'description' => $validated['description'] ?? null,
                'location'    => $validated['location'] ?? null,
                'budget'      => $validated['budget'] ?? null,
                'start_date'  => $validated['start_date'] ?? null,
                'end_date'    => $validated['end_date'] ?? null,
                'state_id'    => $validated['state_id'] ?? null,
                'lga_id'      => $validated['lga_id'] ?? null,
                'status'      => $validated['status'],
                'attachments' => array_merge($existingAttachments, $newAttachments),
            ]);

            /**
             * Update Site Manager assignments
             */
            $this->updateAssignments(
                $project,
                'site_manager',
                $validated['site_managers'] ?? []
            );

            /**
             * Update Procurement Officer assignments
             */
            $this->updateAssignments(
                $project,
                'procurement_officer',
                $validated['procurement_officers'] ?? []
            );

            DB::commit();

            return redirect()
                ->route('projects.show', $project)
                ->with('success', 'Project updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Failed to update project: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified project
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        try {
            $project->delete();
            return redirect()->route('projects.index')
                ->with('success', 'Project deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete project: ' . $e->getMessage());
        }
    }

    /**
     * Get LGAs for a state (AJAX)
     */
    public function getLgas(Request $request, $stateId)
    {
        $lgas = Lga::where('state_id', $stateId)
            ->where('status', 1)
            ->orderBy('lga')
            ->get();

        return response()->json($lgas);
    }

    /**
     * Helper method to update project assignments
     */
    private function updateAssignments(Project $project, string $roleType, array $userIds)
    {
        // Get current active assignments for this role
        $currentAssignments = $project->assignments()
            ->where('role_type', $roleType)
            ->where('is_active', true)
            ->pluck('user_id')
            ->toArray();

        // Determine assignments to add (in new list but not current)
        $toAdd = array_diff($userIds, $currentAssignments);

        // Determine assignments to remove (in current but not new list)
        $toRemove = array_diff($currentAssignments, $userIds);

        // Deactivate removed assignments
        if (!empty($toRemove)) {
            $project->assignments()
                ->where('role_type', $roleType)
                ->whereIn('user_id', $toRemove)
                ->update(['is_active' => false]);
        }

        // Add or Reactivate new assignments
        foreach ($toAdd as $userId) {
            $existing = $project->assignments()
                ->where('role_type', $roleType)
                ->where('user_id', $userId)
                ->first();

            if ($existing) {
                $existing->update(['is_active' => true]);
            } else {
                ProjectAssignment::create([
                    'project_id' => $project->id,
                    'user_id' => $userId,
                    'role_type' => $roleType,
                    'assigned_by' => Auth::id(),
                    'is_active' => true,
                ]);

                $user = User::find($userId);
                if ($user) {
                    event(new ProjectAssigned($project, $user, $roleType));
                }
            }
        }
    }

    /**
     * Generate unique project code
     */
    private function generateProjectCode(): string
    {
        $year = date('Y');
        $month = date('m');

        // Count requests this month
        $count = Project::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count() + 1;

        return sprintf('PROJ-%s-%s-%04d', $year, $month, $count);
    }

    /**
     * Delete attachment
     */
    public function deleteAttachment(Request $request, Project $project)
    {
        $user = Auth::user();

        if (!$user->hasPermission('edit-projects')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'index' => 'required|integer|min:0'
        ]);

        try {
            $success = $this->deleteProjectAttachment($project, $validated['index']);

            if ($success) {
                return back()->with('success', 'Attachment deleted successfully.');
            }

            return back()->with('error', 'Attachment not found.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete attachment: ' . $e->getMessage());
        }
    }

    /**
     * Delete project attachment
     */
    public function deleteProjectAttachment(Project $project, int $index): bool
    {
        $attachments = $project->attachments ?? [];

        if (!isset($attachments[$index])) {
            return false;
        }

        // Delete file from storage
        Storage::disk('public')->delete($attachments[$index]['path']);

        // Remove from array
        array_splice($attachments, $index, 1);

        // Update project
        $project->update(['attachments' => $attachments]);

        return true;
    }
}
