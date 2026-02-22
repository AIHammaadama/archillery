<?php

namespace App\Http\Controllers;

use App\Models\PaymentReceipt;
use App\Models\ProcurementRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ReceiptController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Download a payment receipt securely
     */
    public function download(PaymentReceipt $receipt)
    {
        // Check authorization - user must be able to view the related procurement request
        // which ensures they have appropriate project/request visibility
        $request = $receipt->procurementRequest;
        
        if (!Auth::user()->can('view', $request)) {
            abort(403, 'Unauthorized access to payment receipt.');
        }

        // Check if file exists in the secure local disk
        if (!Storage::disk('local')->exists($receipt->file_path)) {
            abort(404, 'Receipt file not found on the server.');
        }

        return Storage::disk('local')->response($receipt->file_path);
    }

    /**
     * Store new payment receipts linked to a procurement request
     */
    public function store(Request $httpRequest, ProcurementRequest $request)
    {
        if (!Auth::user()->can('view', $request)) {
            abort(403, 'Unauthorized to add receipts.');
        }

        $validated = $httpRequest->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'receipts' => 'required|array',
            'receipts.*' => 'required|file|mimes:pdf,jpeg,png,jpg|max:5120',
        ]);

        try {
            DB::beginTransaction();

            if ($httpRequest->hasFile('receipts')) {
                foreach ($httpRequest->file('receipts') as $file) {
                    $path = $file->store('receipts', 'local');
                    $request->paymentReceipts()->create([
                        'uploaded_by' => Auth::id(),
                        'vendor_id' => $validated['vendor_id'],
                        'file_path' => $path,
                        'original_filename' => $file->getClientOriginalName(),
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', 'Receipts uploaded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to upload receipts: ' . $e->getMessage());
        }
    }
}

