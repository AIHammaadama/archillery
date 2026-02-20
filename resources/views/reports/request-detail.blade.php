<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request {{ $request->request_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
            padding: 50px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #f46839;
        }

        .header h1 {
            font-size: 18pt;
            color: #f46839;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 9pt;
            color: #666;
        }

        .section-title {
            background-color: #f3f4f6;
            padding: 8px 10px;
            margin-top: 15px;
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 11pt;
            color: #f46839;
            border-left: 4px solid #f46839;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            width: 30%;
            padding: 5px;
            font-weight: bold;
            color: #555;
            vertical-align: top;
        }

        .info-value {
            display: table-cell;
            padding: 5px;
            color: #333;
            vertical-align: top;
        }

        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.items-table th {
            background-color: #f46839;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
        }

        table.items-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9pt;
        }

        table.items-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-approved {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .timeline {
            margin-top: 10px;
        }

        .timeline-item {
            padding: 8px 0;
            border-left: 2px solid #e5e7eb;
            padding-left: 15px;
            margin-left: 10px;
            position: relative;
        }

        .timeline-item:before {
            content: '';
            position: absolute;
            left: -6px;
            top: 10px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #f46839;
        }

        .timeline-date {
            font-size: 8pt;
            color: #666;
        }

        .timeline-user {
            font-weight: bold;
            font-size: 9pt;
        }

        .timeline-comment {
            font-size: 9pt;
            font-style: italic;
            color: #555;
            margin-top: 3px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 10px 50px;
            border-top: 1px solid #ddd;
            font-size: 8pt;
            color: #666;
            text-align: center;
        }

        .signatures {
            margin-top: 40px;
            display: table;
            width: 100%;
        }

        .signature-block {
            display: table-cell;
            width: 33%;
            text-align: center;
            padding: 10px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 9pt;
            font-weight: bold;
        }

        .signature-label {
            font-size: 8pt;
            color: #666;
        }

        .text-right {
            text-align: right;
        }

        .text-bold {
            font-weight: bold;
        }

        .mt-1 {
            margin-top: 5px;
        }

        .total-row {
            background-color: #f3f4f6 !important;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header d-flex align-items-center" style="margin-bottom: 20px;">
        <img src="{{ public_path('images/logo_report.png') }}" alt="Company Logo"
            style="height: 60px; margin-right: 15px; margin-bottom:15px;">
        <div>
            <h1 style="margin: 0;">PROCUREMENT REQUEST</h1>
            <p style="margin: 0;">Project Procurement Management System</p>
        </div>
    </div>

    <!-- Request Information -->
    <div class="section-title">Request Information</div>
    <div class="info-grid">
        <div class="info-row">
            <div class="info-label">Request Number:</div>
            <div class="info-value text-bold">{{ $request->request_number }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">
                @if($request->status->value === 'approved')
                <span class="status-badge status-approved">Approved</span>
                @elseif($request->status->value === 'rejected')
                <span class="status-badge status-rejected">Rejected</span>
                @else
                <span class="status-badge status-pending">{{ $request->status->label() }}</span>
                @endif
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Request Date:</div>
            <div class="info-value">{{ $request->request_date->format('F d, Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Required By:</div>
            <div class="info-value">{{ $request->required_by_date->format('F d, Y') }}</div>
        </div>
    </div>

    <!-- Project Details -->
    <div class="section-title">Project Details</div>
    <div class="info-grid">
        <div class="info-row">
            <div class="info-label">Project Name:</div>
            <div class="info-value">{{ $request->project->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Project Code:</div>
            <div class="info-value">{{ $request->project->code }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Location:</div>
            <div class="info-value">{{ collect([
        $request->project->location,
        optional($request->project->lga)->lga,
        optional($request->project->state)->state,
    ])->filter()->implode(', ') ?: 'Not specified' }}</div>
        </div>
    </div>

    <!-- Personnel -->
    <div class="section-title">Personnel</div>
    <div class="info-grid">
        <div class="info-row">
            <div class="info-label">Requested By:</div>
            <div class="info-value">
                @if($request->requestedBy)
                {{ $request->requestedBy->firstname }} {{ $request->requestedBy->lastname }}
                @else
                N/A
                @endif
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Procurement Officer:</div>
            <div class="info-value">
                @if($request->procurementOfficer)
                {{ $request->procurementOfficer->firstname }} {{ $request->procurementOfficer->lastname }}
                @else
                Not assigned
                @endif
            </div>
        </div>
        @if($request->approvedBy)
        <div class="info-row">
            <div class="info-label">Approved By:</div>
            <div class="info-value">
                {{ $request->approvedBy->firstname }} {{ $request->approvedBy->lastname }}
                <br><span
                    style="font-size: 8pt; color: #666;">{{ $request->approved_at->format('F d, Y g:i A') }}</span>
            </div>
        </div>
        @endif
    </div>

    <!-- Justification -->
    @if($request->justification)
    <div class="section-title">Justification</div>
    <p style="padding: 10px; background-color: #f9fafb; border-left: 3px solid #f46839;">
        {{ $request->justification }}
    </p>
    @endif
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <!-- Items -->
    <div class="section-title">Requested Items</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 35%;">Material</th>
                <th style="width: 10%;">Quantity</th>
                <th style="width: 10%;">Unit</th>
                @if($canViewPricing)
                <th style="width: 20%;">Vendor</th>
                <th style="width: 10%; text-align: right;">Unit Price</th>
                <th style="width: 10%; text-align: right;">Total</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($request->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $item->material->name }}</strong><br>
                    <span style="font-size: 8pt; color: #666;">{{ $item->material->code }}</span>
                </td>
                <td>{{ number_format($item->quantity, 2) }}</td>
                <td>{{ $item->material->unit_of_measurement }}</td>
                @if($canViewPricing)
                <td>{{ $item->vendor ? $item->vendor->name : 'Not assigned' }}</td>
                <td class="text-right">
                    @if($item->quoted_unit_price)
                    ₦{{ number_format($item->quoted_unit_price, 2) }}
                    @else
                    -
                    @endif
                </td>
                <td class="text-right">
                    @if($item->quoted_total_price)
                    ₦{{ number_format($item->quoted_total_price, 2) }}
                    @else
                    -
                    @endif
                </td>
                @endif
            </tr>
            @endforeach
            @if($canViewPricing)
            <tr class="total-row">
                <td colspan="6" class="text-right">TOTAL:</td>
                <td class="text-right">₦{{ number_format($request->total_quoted_amount ?? 0, 2) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    @php
    $vendorSummary = $request->items
    ->filter(fn($item) => $item->vendor_id) // only assigned vendors
    ->groupBy('vendor_id')
    ->map(function ($items) {
    $vendor = $items->first()->vendor;

    $total = $items->sum(function ($item) {
    // prefer stored total if available
    if (!is_null($item->quoted_total_price)) {
    return (float) $item->quoted_total_price;
    }
    // fallback compute
    return ((float) $item->quantity) * ((float) ($item->quoted_unit_price ?? 0));
    });

    return [
    'vendor_name' => $vendor?->name ?? 'Unknown Vendor',
    'bank_name' => $vendor?->bank_name ?? '-',
    'account_name' => $vendor?->name ?? '-',
    'account_number' => $vendor?->bank_account ?? '-',
    'bank_account_name' => $vendor?->bank_account_name ?? '-',
    'total' => $total,
    ];
    })
    ->values();
    @endphp
    <!-- Vendor Payment Summary -->
    @if($canViewPricing)
    <div class="section-title" style="margin-top: 16px;">Vendor Payment Summary</div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 30%;">Vendor</th>
                <th style="width: 14%; text-align: right;">Total Payable</th>
                <th style="width: 20%;">Bank</th>
                <th style="width: 12%;">Account Name</th>
                <th style="width: 12%;">Account Number</th>
                <th style="width: 12%;">Tax ID</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vendorSummary as $row)
            <tr>
                <td>{{ $row['vendor_name'] }}</td>
                <td style="text-align:right;">
                    ₦{{ number_format($row['total'], 2) }}
                </td>
                <td>{{ $row['bank_name'] }}</td>
                <td>{{ $row['account_name'] }}</td>
                <td>{{ $row['account_number'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center; color:#666;">
                    No vendor assignments yet.
                </td>
            </tr>
            @endforelse

            @if($vendorSummary->count())
            <tr class="total-row">
                <td class="text-right" colspan="1"><strong>GRAND TOTAL:</strong></td>
                <td style="text-align:right;">
                    <strong>₦{{ number_format($vendorSummary->sum('total'), 2) }}</strong>
                </td>
                <td colspan="3"></td>
            </tr>
            @endif
        </tbody>
    </table>
    @endif

    <!-- Status History -->
    @if($request->statusHistory && $request->statusHistory->count() > 0)
    <div class="section-title">Status History</div>
    <div class="timeline">
        @foreach($request->statusHistory as $history)
        <div class="timeline-item">
            <div class="timeline-date">
                {{ optional($history->created_at)->format('M d, Y g:i A') ?? '—' }}
            </div>
            <div class="timeline-user">
                {{ $history->changedBy->firstname ?? 'System' }} {{ $history->changedBy->lastname ?? '' }}
            </div>
            <div>
                Changed from <strong>{{ ucwords(str_replace('_', ' ', $history->from_status)) }}</strong>
                to <strong>{{ ucwords(str_replace('_', ' ', $history->to_status)) }}</strong>
            </div>
            @if($history->comments)
            <div class="timeline-comment">{{ $history->comments }}</div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <!-- Remarks -->
    @if($request->remarks)
    <div class="section-title">Remarks</div>
    <p style="padding: 10px; background-color: #f9fafb; border-left: 3px solid #f46839;">
        {{ $request->remarks }}
    </p>
    @endif

    <!-- Rejection Reason -->
    @if($request->rejection_reason)
    <div class="section-title">Rejection Reason</div>
    <p style="padding: 10px; background-color: #fee2e2; border-left: 3px solid #991b1b; color: #991b1b;">
        {{ $request->rejection_reason }}
    </p>
    @endif

    <!-- Signatures (only for approved requests) -->
    @if($request->status->value === 'approved')
    <div class="signatures">
        <div class="signature-block">
            <div class="signature-line">
                {{ $request->requestedBy->firstname ?? '' }} {{ $request->requestedBy->lastname ?? '' }}
            </div>
            <div class="signature-label">Site Manager</div>
        </div>
        <div class="signature-block">
            <div class="signature-line">
                {{ $request->procurementOfficer->firstname ?? '' }}
                {{ $request->procurementOfficer->lastname ?? '' }}
            </div>
            <div class="signature-label">Procurement Officer</div>
        </div>
        <div class="signature-block">
            <div class="signature-line">
                {{ $request->approvedBy->firstname ?? '' }} {{ $request->approvedBy->lastname ?? '' }}
            </div>
            <div class="signature-label">Director</div>
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        Generated on {{ $generatedAt->format('F d, Y g:i A') }} by {{ $generatedBy->firstname }}
        {{ $generatedBy->lastname }}
        <br>
        This is a system-generated document from the Project Procurement Management System
    </div>
</body>

</html>