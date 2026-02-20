<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\ProcurementRequest;
use App\Models\RequestItem;
use App\Services\DeliveryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    protected $deliveryService;

    public function __construct(DeliveryService $deliveryService)
    {
        $this->deliveryService = $deliveryService;
    }

    /**
     * Display all approved requests with delivery tracking
     */
    public function allDeliveries(Request $httpRequest)
    {
        $user = Auth::user();

        // Get requests that are approved or partially delivered
        $query = ProcurementRequest::with([
            'project',
            'requestedBy',
            'procurementOfficer',
            'items.material',
            'items.vendor',
            'items.deliveries'
        ])->whereIn('status', ['approved', 'partially_delivered']);

        // Permission-based filtering
        if ($user->hasPermission('process-purchase-request') && !$user->hasPermission('approve-purchase-request')) {
            // Procurement Officers see only their assigned requests
            $query->where('procurement_officer_id', $user->id);
        } elseif ($user->hasPermission('create-purchase-request') && !$user->hasPermission('process-purchase-request')) {
            // Site Managers see only their project requests
            $query->whereHas('project.assignments', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->where('role_type', 'site_manager')
                    ->where('is_active', true);
            });
        }

        $requests = $query->latest()->paginate(15);

        // Calculate stats
        $stats = [
            'total_requests' => $requests->total(),
            'approved' => ProcurementRequest::where('status', 'approved')->count(),
            'partially_delivered' => ProcurementRequest::where('status', 'partially_delivered')->count(),
            'fully_delivered' => ProcurementRequest::where('status', 'fully_delivered')->count(),
        ];

        return view('deliveries.all', compact('requests', 'stats'));
    }

    /**
     * Display deliveries for a request
     */
    public function index(ProcurementRequest $request)
    {
        $user = Auth::user();

        if (!$user->hasPermission('record-deliveries')) {
            abort(403, 'Unauthorized action.');
        }

        $request->load(['items.material', 'items.vendor', 'items.deliveries.receivedBy', 'items.deliveries.verifiedBy']);

        $stats = $this->deliveryService->getRequestDeliveryStats($request);

        return view('deliveries.index', compact('request', 'stats'));
    }

    /**
     * Show form to record a delivery
     */
    public function create(ProcurementRequest $request)
    {
        $user = Auth::user();

        // Only procurement officers, admins, and directors can record deliveries
        if (!$user->hasPermission('record-deliveries') && !$user->hasRole('director')) {
            abort(403, 'Unauthorized action.');
        }

        // Request must be approved or partially delivered
        if (!in_array($request->status->value, ['approved', 'partially_delivered'])) {
            return redirect()->route('requests.show', $request)
                ->with('error', 'Deliveries can only be recorded for approved requests.');
        }

        $request->load(['items.material', 'items.vendor', 'items.deliveries']);

        return view('deliveries.create', compact('request'));
    }

    /**
     * Store a new delivery
     */
    public function store(Request $httpRequest, ProcurementRequest $request)
    {
        $user = Auth::user();

        if (!$user->hasPermission('record-deliveries') && !$user->hasRole('director')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $httpRequest->validate([
            'request_item_id' => 'required|exists:request_items,id',
            'delivery_date' => 'required|date|before_or_equal:today',
            'quantity_delivered' => 'required|numeric|min:0.01',
            'waybill_number' => 'nullable|string|max:100',
            'invoice_number' => 'nullable|string|max:100',
            'invoice_amount' => 'nullable|numeric|min:0',
            'quality_notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120', // 5MB max
        ]);

        try {
            $item = RequestItem::findOrFail($validated['request_item_id']);

            // Verify item belongs to this request
            if ($item->request_id !== $request->id) {
                return back()->with('error', 'Invalid request item.');
            }

            // Check if quantity exceeds remaining
            $remaining = round(
                $item->quantity - $item->deliveries()
                    ->whereIn('verification_status', ['accepted', 'partial'])
                    ->sum('quantity_delivered'),
                2
            );

            if ($remaining <= 0) {
                return back()->with('error', 'This item has already been fully delivered.');
            }

            if (round($validated['quantity_delivered'], 2) > $remaining) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'quantity_delivered' => "Quantity cannot exceed remaining amount ({$remaining})."
                    ]);
            }

            $delivery = $this->deliveryService->recordDelivery(
                $item,
                [
                    'delivery_date' => $validated['delivery_date'],
                    'quantity_delivered' => $validated['quantity_delivered'],
                    'waybill_number' => $validated['waybill_number'] ?? null,
                    'invoice_number' => $validated['invoice_number'] ?? null,
                    'invoice_amount' => $validated['invoice_amount'] ?? null,
                    'quality_notes' => $validated['quality_notes'] ?? null,
                    'attachments' => $httpRequest->file('attachments') ?? [],
                ],
                $user
            );

            return redirect()
                ->route('deliveries.index', $request)
                ->with('success', 'Delivery recorded successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Invalid request item.');
        }
    }

    /**
     * Display a specific delivery
     */
    public function show(Delivery $delivery)
    {
        $user = Auth::user();

        if (!$user->hasPermission('view-deliveries')) {
            abort(403, 'Unauthorized action.');
        }

        $delivery->load([
            'request.project',
            'requestItem.material',
            'requestItem.deliveries',
            'vendor',
            'receivedBy',
            'verifiedBy',
            'siteManagerUpdatedBy'
        ]);

        return view('deliveries.show', compact('delivery'));
    }

    /**
     * Show form to verify a delivery
     */
    public function verify(Delivery $delivery)
    {
        $user = Auth::user();

        // Only users with the right permission can verify deliveries
        if (!$user->hasPermission('verify-deliveries')) {
            abort(403, 'Unauthorized action.');
        }

        if ($delivery->verification_status !== 'pending') {
            return redirect()->route('deliveries.show', $delivery)
                ->with('error', 'This delivery has already been verified.');
        }

        $delivery->load(['request', 'requestItem.material', 'vendor', 'receivedBy']);

        return view('deliveries.verify', compact('delivery'));
    }

    /**
     * Process verification
     */
    public function processVerification(Request $request, Delivery $delivery)
    {
        $user = Auth::user();

        if (!$user->hasPermission('verify-deliveries')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'verification_status' => 'required|in:accepted,rejected,partial',
            'quality_notes' => 'nullable|string',
        ]);

        try {
            $this->deliveryService->verifyDelivery(
                $delivery,
                $validated['verification_status'],
                $user,
                $validated['quality_notes'] ?? null
            );

            return redirect()->route('deliveries.show', $delivery)
                ->with('success', 'Delivery verified successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to verify delivery: ' . $e->getMessage());
        }
    }

    /**
     * Delete attachment
     */
    public function deleteAttachment(Request $request, Delivery $delivery)
    {
        $user = Auth::user();

        if (!$user->hasPermission('record-deliveries')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'index' => 'required|integer|min:0'
        ]);

        try {
            $success = $this->deliveryService->deleteAttachment($delivery, $validated['index']);

            if ($success) {
                return back()->with('success', 'Attachment deleted successfully.');
            }

            return back()->with('error', 'Attachment not found.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete attachment: ' . $e->getMessage());
        }
    }

    /**
     * Show form for site manager to update delivery status
     */
    public function showUpdateStatus(Delivery $delivery)
    {
        $user = Auth::user();

        // Only site managers can update delivery status
        if (!$user->hasPermission('record-deliveries')) {
            abort(403, 'Unauthorized action. Only site managers can update delivery status.');
        }

        // Check if delivery belongs to site manager's project
        $delivery->load(['request.project']);

        if ($delivery->request->project->site_manager_id !== $user->id) {
            abort(403, 'You can only update deliveries for your own projects.');
        }

        // Check if delivery has been verified
        if (!$delivery->siteManagerCanUpdate()) {
            return redirect()->route('deliveries.show', $delivery)
                ->with('error', 'Deliveries can only be updated after verification.');
        }

        $delivery->load(['request', 'requestItem.material', 'vendor', 'receivedBy', 'verifiedBy', 'siteManagerUpdatedBy']);

        return view('deliveries.update-status', compact('delivery'));
    }

    /**
     * Process site manager status update
     */
    public function updateStatus(Request $httpRequest, Delivery $delivery)
    {
        $user = Auth::user();

        // Only site managers can update delivery status
        if (!$user->hasPermission('record-deliveries')) {
            abort(403, 'Unauthorized action. Only site managers can update delivery status.');
        }

        // Check if delivery belongs to site manager's project
        $delivery->load(['request.project']);

        if ($delivery->request->project->site_manager_id !== $user->id) {
            abort(403, 'You can only update deliveries for your own projects.');
        }

        // Check if delivery has been verified
        if (!$delivery->siteManagerCanUpdate()) {
            return redirect()->route('deliveries.show', $delivery)
                ->with('error', 'Deliveries can only be updated after verification.');
        }

        $validated = $httpRequest->validate([
            'site_manager_status' => 'required|in:received,issues_noted,completed',
            'site_manager_comments' => 'required|string|max:2000',
        ]);

        try {
            $delivery->update([
                'site_manager_status' => $validated['site_manager_status'],
                'site_manager_comments' => $validated['site_manager_comments'],
                'site_manager_updated_by' => $user->id,
                'site_manager_updated_at' => now(),
            ]);

            return redirect()->route('deliveries.show', $delivery)
                ->with('success', 'Delivery status updated successfully. Other stakeholders have been notified.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update delivery status: ' . $e->getMessage());
        }
    }
}