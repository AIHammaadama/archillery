<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Transaction Report - {{ $vendor->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #333;
            padding: 50px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 3px solid #f46839;
        }

        .header h1 {
            font-size: 16pt;
            color: #f46839;
            margin-bottom: 3px;
        }

        .header p {
            font-size: 8pt;
            color: #666;
        }

        .vendor-info {
            background-color: #f3f4f6;
            padding: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #f46839;
        }

        .vendor-info h2 {
            font-size: 12pt;
            color: #f46839;
            margin-bottom: 5px;
        }

        .vendor-info p {
            font-size: 8pt;
            color: #555;
            margin: 2px 0;
        }

        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 8px;
            text-align: center;
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }

        .stat-value {
            font-size: 14pt;
            font-weight: bold;
            color: #f46839;
            display: block;
        }

        .stat-label {
            font-size: 7pt;
            color: #666;
            text-transform: uppercase;
            margin-top: 3px;
        }

        .section-title {
            background-color: #f3f4f6;
            padding: 6px 8px;
            margin-top: 12px;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 10pt;
            color: #f46839;
            border-left: 4px solid #f46839;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 8pt;
        }

        table th {
            background-color: #f46839;
            color: white;
            padding: 5px;
            text-align: left;
            font-weight: bold;
        }

        table td {
            padding: 4px 5px;
            border-bottom: 1px solid #e5e7eb;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .status-complete {
            color: #065f46;
            font-weight: bold;
        }

        .status-pending {
            color: #92400e;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 8px 30px;
            border-top: 1px solid #ddd;
            font-size: 7pt;
            color: #666;
            text-align: center;
        }

        .rating-stars {
            color: #f59e0b;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header d-flex align-items-center" style="margin-bottom: 20px;">
        <img src="{{ public_path('images/logo_report.png') }}" alt="Company Logo"
            style="height: 60px; margin-right: 15px; margin-bottom:15px;">
        <div>
            <h1 style="margin: 0;">VENDOR TRANSACTION REPORT</h1>
            <p style="margin: 0;">{{ $startDate->format('F d, Y') }} to {{ $endDate->format('F d, Y') }}</p>
        </div>
    </div>

    <!-- Vendor Information -->
    <div class="vendor-info">
        <h2>{{ $vendor->name }}</h2>
        @if($vendor->business_registration)
        <p><strong>Registration Number:</strong> {{ $vendor->business_registration }}</p>
        @endif
        <p><strong>Contact Person:</strong> {{ $vendor->contact_person }}</p>
        <p><strong>Email:</strong> {{ $vendor->email }} | <strong>Phone:</strong> {{ $vendor->phone }}
            @if($vendor->alt_phone) | <strong>Alt Phone:</strong> {{ $vendor->alt_phone }} @endif</p>
        <p><strong>Address:</strong> {{ $vendor->address }}</p>
        @if($vendor->rating)
        <p>
            <strong>Rating:</strong>
            <span class="rating-stars">
                @for($i = 1; $i <= 5; $i++) {{ $i <= $vendor->rating ? '★' : '☆' }} @endfor </span>
                    ({{ number_format($vendor->rating, 1) }}/5.0)
        </p>
        @endif
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-box">
            <span class="stat-value">{{ $stats['total_orders'] }}</span>
            <span class="stat-label">Total Orders</span>
        </div>
        @if($canViewPricing)
        <div class="stat-box">
            <span class="stat-value">₦{{ number_format($stats['total_value'], 2) }}</span>
            <span class="stat-label">Total Value</span>
        </div>
        @endif
        <div class="stat-box">
            <span class="stat-value">{{ $stats['completed_deliveries'] }}</span>
            <span class="stat-label">Completed</span>
        </div>
        <div class="stat-box">
            <span class="stat-value">{{ $stats['pending_deliveries'] }}</span>
            <span class="stat-label">Pending</span>
        </div>
    </div>

    <!-- Transaction Details -->
    <div class="section-title">Transaction Details</div>
    <table>
        <thead>
            <tr>
                <th style="width: 12%;">Date</th>
                <th style="width: 15%;">Request #</th>
                <th style="width: 20%;">Project</th>
                <th style="width: 20%;">Material</th>
                <th style="width: 10%; text-align: center;">Quantity</th>
                @if($canViewPricing)
                <th style="width: 10%; text-align: right;">Unit Price</th>
                <th style="width: 10%; text-align: right;">Total</th>
                @endif
                <th style="width: 13%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requestItems as $item)
            @php
            $totalDelivered = $item->deliveries()
            ->whereIn('verification_status', ['accepted', 'partial'])
            ->sum('quantity_delivered');
            $isComplete = $totalDelivered >= $item->quantity;
            @endphp
            <tr>
                <td>{{ $item->request->request_date->format('M d, Y') }}</td>
                <td>{{ $item->request->request_number }}</td>
                <td>{{ $item->request->project->name }}</td>
                <td>
                    {{ $item->material->name }}<br>
                    <span style="font-size: 7pt; color: #666;">{{ $item->material->code }}</span>
                </td>
                <td class="text-center">
                    {{ number_format($item->quantity, 2) }} {{ $item->material->unit_of_measurement }}
                </td>
                @if($canViewPricing)
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
                <td class="text-center {{ $isComplete ? 'status-complete' : 'status-pending' }}">
                    {{ $isComplete ? 'Complete' : 'Pending' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ $canViewPricing ? 8 : 6 }}" class="text-center" style="padding: 15px; color: #999;">
                    No transactions found for the selected date range
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Performance Metrics -->
    @if($requestItems->count() > 0)
    <div class="section-title">Performance Summary</div>
    <table>
        <thead>
            <tr>
                <th style="width: 50%;">Metric</th>
                <th style="width: 50%; text-align: right;">Value</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Orders in Period</td>
                <td class="text-right text-bold">{{ $stats['total_orders'] }}</td>
            </tr>
            <tr>
                <td>Completed Deliveries</td>
                <td class="text-right">{{ $stats['completed_deliveries'] }}</td>
            </tr>
            <tr>
                <td>Pending Deliveries</td>
                <td class="text-right">{{ $stats['pending_deliveries'] }}</td>
            </tr>
            <tr>
                <td>Completion Rate</td>
                <td class="text-right text-bold">
                    @if($stats['total_orders'] > 0)
                    {{ number_format(($stats['completed_deliveries'] / $stats['total_orders']) * 100, 1) }}%
                    @else
                    0%
                    @endif
                </td>
            </tr>
            @if($canViewPricing)
            <tr>
                <td>Total Transaction Value</td>
                <td class="text-right text-bold">₦{{ number_format($stats['total_value'], 2) }}</td>
            </tr>
            <tr>
                <td>Average Order Value</td>
                <td class="text-right">
                    @if($stats['total_orders'] > 0)
                    ₦{{ number_format($stats['total_value'] / $stats['total_orders'], 2) }}
                    @else
                    ₦0.00
                    @endif
                </td>
            </tr>
            @endif
        </tbody>
    </table>
    @endif

    <!-- Banking Details -->
    @if($vendor->bank_name && $vendor->bank_account)
    <div class="section-title">Banking Information</div>
    <table>
        <tbody>
            <tr>
                <td style="width: 30%; font-weight: bold;">Bank Name:</td>
                <td>{{ $vendor->bank_name }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Account Number:</td>
                <td>{{ $vendor->bank_account }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Account Name:</td>
                <td>{{ $vendor->bank_account_name }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Tax Id:</td>
                <td>{{ $vendor->tax_id }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    <!-- Notes -->
    @if($vendor->notes)
    <div class="section-title">Notes</div>
    <p style="padding: 10px; background-color: #f9fafb; border-left: 3px solid #f46839; font-size: 8pt;">
        {{ $vendor->notes }}
    </p>
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