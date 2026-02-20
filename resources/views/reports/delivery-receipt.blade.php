<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Delivery Receipt - {{ $delivery->delivery_number }}</title>
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
            padding: 40px;
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
            width: 35%;
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

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 3px;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-accepted {
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

        .status-partial {
            background-color: #fed7aa;
            color: #9a3412;
        }

        .material-box {
            background-color: #f9fafb;
            border: 2px solid #f46839;
            padding: 15px;
            margin: 10px 0;
            text-align: center;
        }

        .material-name {
            font-size: 14pt;
            font-weight: bold;
            color: #f46839;
            margin-bottom: 5px;
        }

        .material-code {
            font-size: 9pt;
            color: #666;
        }

        .quantity-box {
            background-color: #fff;
            border: 2px dashed #f46839;
            padding: 20px;
            margin: 10px 0;
            text-align: center;
        }

        .quantity-label {
            font-size: 9pt;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .quantity-value {
            font-size: 24pt;
            font-weight: bold;
            color: #f46839;
        }

        .quantity-unit {
            font-size: 12pt;
            color: #666;
            margin-left: 5px;
        }

        .notes-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 12px;
            margin: 15px 0;
        }

        .notes-label {
            font-weight: bold;
            color: #92400e;
            margin-bottom: 5px;
        }

        .notes-text {
            color: #78350f;
            font-size: 9pt;
        }

        .signatures {
            margin-top: 25px;
            display: table;
            width: 100%;
        }

        .signature-block {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 10px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
            font-size: 10pt;
            font-weight: bold;
        }

        .signature-label {
            font-size: 8pt;
            color: #666;
            margin-top: 3px;
        }

        .signature-date {
            font-size: 8pt;
            color: #999;
            margin-top: 2px;
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

        .divider {
            border-top: 1px dashed #ddd;
            margin: 15px 0;
        }
        </style>
    </head>

    <body>
        <!-- Header -->
        <div class="header d-flex align-items-center" style="margin-bottom: 20px;">
            <img src="{{ public_path('images/logo_report.png') }}" alt="Company Logo"
                style="height: 60px; margin-right: 15px; margin-bottom:5px;">
            <div>
                <h1 style="margin: 0;">DELIVERY RECEIPT</h1>
                <p style="margin: 0;">{{ $delivery->delivery_number }}</p>
            </div>
        </div>

        <!-- Delivery Status -->
        <div style="text-align: center; margin-bottom: 20px;">
            @if($delivery->verification_status === 'accepted')
            <span class="status-badge status-accepted">Accepted</span>
            @elseif($delivery->verification_status === 'rejected')
            <span class="status-badge status-rejected">Rejected</span>
            @elseif($delivery->verification_status === 'partial')
            <span class="status-badge status-partial">Partial</span>
            @else
            <span class="status-badge status-pending">Pending Verification</span>
            @endif
        </div>

        <!-- Request Information -->
        <div class="section-title">Request Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Request Number:</div>
                <div class="info-value text-bold">{{ $delivery->request->request_number }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Project:</div>
                <div class="info-value">{{ $delivery->request->project->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Project Location:</div>
                <div class="info-value">
                    {{ collect([
        $delivery->request->project->location,
        optional($delivery->request->project->lga)->lga,
        optional($delivery->request->project->state)->state,
    ])->filter()->implode(', ') ?: 'Not specified' }}
                </div>
            </div>
        </div>

        <!-- Material Delivered -->
        <div class="material-box">
            <div class="material-name">{{ $delivery->requestItem->material->name }}</div>
            <div class="material-code">Code: {{ $delivery->requestItem->material->code }}</div>
        </div>

        <!-- Quantity -->
        <div class="quantity-box">
            <div class="quantity-label">Quantity Delivered</div>
            <div>
                <span class="quantity-value">{{ number_format($delivery->quantity_delivered, 2) }}</span>
                <span class="quantity-unit">{{ $delivery->requestItem->material->unit_of_measurement }}</span>
            </div>
            <div style="margin-top: 15px; font-size: 9pt; color: #666;">
                Ordered Quantity: {{ number_format($delivery->requestItem->quantity, 2) }}
                {{ $delivery->requestItem->material->unit_of_measurement }}
            </div>
        </div>

        <div class="divider"></div>

        <!-- Delivery Details -->
        <div class="section-title">Delivery Details</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Delivery Date:</div>
                <div class="info-value">{{ $delivery->delivery_date->format('F d, Y') }}</div>
            </div>
            @if($delivery->vendor)
            <div class="info-row">
                <div class="info-label">Vendor:</div>
                <div class="info-value">
                    <strong>{{ $delivery->vendor->name }}</strong><br>
                    <span style="font-size: 9pt; color: #666;">
                        {{ $delivery->vendor->contact_person }}<br>
                        {{ $delivery->vendor->phone }}
                    </span>
                </div>
            </div>
            @endif
            @if($delivery->waybill_number)
            <div class="info-row">
                <div class="info-label">Waybill Number:</div>
                <div class="info-value">{{ $delivery->waybill_number }}</div>
            </div>
            @endif
            @if($delivery->invoice_number)
            <div class="info-row">
                <div class="info-label">Invoice Number:</div>
                <div class="info-value">{{ $delivery->invoice_number }}</div>
            </div>
            @endif
            @if($delivery->invoice_amount)
            <div class="info-row">
                <div class="info-label">Invoice Amount:</div>
                <div class="info-value"><strong>₦{{ number_format($delivery->invoice_amount, 2) }}</strong></div>
            </div>
            @endif
        </div>

        <!-- Quality Notes -->
        @if($delivery->quality_notes)
        <div class="notes-box">
            <div class="notes-label">Quality Verification Notes:</div>
            <div class="notes-text">{{ $delivery->quality_notes }}</div>
        </div>
        @endif

        <!-- Attachments Info -->
        @if($delivery->attachments && count($delivery->attachments) > 0)
        <div class="section-title">Attachments</div>
        <p style="font-size: 9pt; color: #666; padding: 10px;">
            {{ count($delivery->attachments) }} file(s) attached to this delivery record in the system.
        </p>
        @endif

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-block">
                <div class="signature-line">
                    @if($delivery->receivedBy)
                    {{ $delivery->receivedBy->firstname }} {{ $delivery->receivedBy->lastname }}
                    @endif
                </div>
                <div class="signature-label">Received By</div>
                @if($delivery->created_at)
                <div class="signature-date">{{ $delivery->created_at->format('F d, Y g:i A') }}</div>
                @endif
            </div>

            <div class="signature-block">
                <div class="signature-line">
                    @if($delivery->verifiedBy)
                    {{ $delivery->verifiedBy->firstname }} {{ $delivery->verifiedBy->lastname }}
                    @elseif($delivery->verification_status === 'pending')
                    <span style="color: #999;">Pending Verification</span>
                    @endif
                </div>
                <div class="signature-label">Verified By</div>
                @if($delivery->verified_at)
                <div class="signature-date">{{ $delivery->verified_at->format('F d, Y g:i A') }}</div>
                @elseif($delivery->verification_status !== 'pending')
                <div class="signature-date">{{ $delivery->updated_at->format('F d, Y g:i A') }}</div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Generated on {{ $generatedAt->format('F d, Y g:i A') }} by {{ $generatedBy->firstname }}
            {{ $generatedBy->lastname }}
            <br>
            This is a system-generated document from the Project Procurement Management System
        </div>
    </body>

</html>