<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Project Summary - {{ $project->code }}</title>
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
            border-bottom: 3px solid #1abe68;
        }

        .header h1 {
            font-size: 16pt;
            color: #1abe68;
            margin-bottom: 3px;
        }

        .header p {
            font-size: 8pt;
            color: #666;
        }

        .project-info {
            background-color: #f3f4f6;
            padding: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #1abe68;
        }

        .project-info h2 {
            font-size: 12pt;
            color: #1abe68;
            margin-bottom: 5px;
        }

        .project-info p {
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
            color: #1abe68;
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
            color: #1abe68;
            border-left: 4px solid #1abe68;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 8pt;
        }

        table th {
            background-color: #1abe68;
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

        .page-break {
            page-break-after: always;
        }
        </style>
    </head>

    <body>
        <!-- Header -->
        <div class="header d-flex align-items-center" style="margin-bottom: 20px;">
            <img src="{{ public_path('images/logo_report.png') }}" alt="Company Logo"
                style="height: 60px; margin-right: 15px; margin-bottom:15px;">
            <div>
                <h1 style="margin: 0;">PROJECT SUMMARY REPORT</h1>
                <p style="margin: 0;">{{ $startDate->format('F d, Y') }} to {{ $endDate->format('F d, Y') }}</p>
            </div>
        </div>

        <!-- Project Information -->
        <div class="project-info">
            <h2>{{ $project->name }}</h2>
            <p><strong>Code:</strong> {{ $project->code }}</p>
            <p><strong>Location:</strong> {{ collect([
        $project->location,
        optional($project->lga)->lga,
        optional($project->state)->state,
    ])->filter()->implode(', ') ?: 'Not specified' }}</p>
            <p><strong>Budget:</strong> ₦{{ number_format($project->budget ?? 0, 2) }}</p>
            @if($project->description)
            <p><strong>Description:</strong> {{ $project->description }}</p>
            @endif
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-box">
                <span class="stat-value">{{ $stats['total_requests'] }}</span>
                <span class="stat-label">Total Requests</span>
            </div>
            <div class="stat-box">
                <span class="stat-value">{{ $stats['approved_requests'] }}</span>
                <span class="stat-label">Approved</span>
            </div>
            <div class="stat-box">
                <span class="stat-value">{{ $stats['rejected_requests'] }}</span>
                <span class="stat-label">Rejected</span>
            </div>
            <div class="stat-box">
                <span class="stat-value">{{ $stats['pending_requests'] }}</span>
                <span class="stat-label">Pending</span>
            </div>
        </div>

        @if($canViewPricing)
        <div class="stats-grid" style="margin-bottom: 15px;">
            <div class="stat-box" style="width: 33%;">
                <span class="stat-value">₦{{ number_format($stats['total_value'], 2) }}</span>
                <span class="stat-label">Total Procurement Value</span>
            </div>
            <div class="stat-box" style="width: 33%;">
                <span class="stat-value">₦{{ number_format($expenses->sum('amount'), 2) }}</span>
                <span class="stat-label">Total Other Expenses</span>
            </div>
            <div class="stat-box" style="width: 34%;">
                <span class="stat-value">₦{{ number_format($stats['total_project_spending'], 2) }}</span>
                <span class="stat-label">Total Project Spending</span>
            </div>
        </div>
        @endif

        <!-- Requests Summary -->
        <div class="section-title">Requests Overview</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Request #</th>
                    <th style="width: 12%;">Date</th>
                    <th style="width: 20%;">Requested By</th>
                    <th style="width: 15%;">Status</th>
                    <th style="width: 8%; text-align: center;">Items</th>
                    @if($canViewPricing)
                    <th style="width: 15%; text-align: right;">Value</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                <tr>
                    <td>{{ $request->request_number }}</td>
                    <td>{{ $request->request_date->format('M d, Y') }}</td>
                    <td>{{ $request->requestedBy->firstname ?? '' }} {{ $request->requestedBy->lastname ?? '' }}</td>
                    <td>{{ $request->status->label() }}</td>
                    <td class="text-center">{{ $request->items->count() }}</td>
                    @if($canViewPricing)
                    <td class="text-right">₦{{ number_format($request->total_quoted_amount ?? 0, 2) }}</td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $canViewPricing ? 6 : 5 }}" class="text-center" style="padding: 15px; color: #999;">
                        No requests found for the selected date range
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Status Breakdown -->
        @if(count($statusBreakdown) > 0)
        <div class="section-title">Status Breakdown</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Status</th>
                    <th style="width: 25%; text-align: center;">Count</th>
                    @if($canViewPricing)
                    <th style="width: 25%; text-align: right;">Total Value</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($statusBreakdown as $status => $data)
                <tr>
                    <td>{{ ucwords(str_replace('_', ' ', $status)) }}</td>
                    <td class="text-center">{{ $data['count'] }}</td>
                    @if($canViewPricing)
                    <td class="text-right">₦{{ number_format($data['total_value'], 2) }}</td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- Vendor Breakdown -->
        @if($canViewPricing && count($vendorBreakdown) > 0)
        <div class="section-title">Vendor Engagement</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Vendor</th>
                    <th style="width: 25%; text-align: center;">Orders</th>
                    <th style="width: 25%; text-align: right;">Total Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vendorBreakdown as $vendorName => $data)
                <tr>
                    <td>{{ $vendorName }}</td>
                    <td class="text-center">{{ $data['count'] }}</td>
                    <td class="text-right">₦{{ number_format($data['total_value'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- Other Project Expenses -->
        @if($canViewPricing && count($expenseBreakdown) > 0)
        <div class="section-title">Other Project Expenses</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Expense Type</th>
                    <th style="width: 25%; text-align: center;">Transaction Count</th>
                    <th style="width: 25%; text-align: right;">Total Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenseBreakdown as $expenseType => $data)
                <tr>
                    <td>{{ ucwords(str_replace('_', ' ', $expenseType)) }}</td>
                    <td class="text-center">{{ $data['count'] }}</td>
                    <td class="text-right">₦{{ number_format($data['total_value'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- Material Breakdown -->
        @if(count($materialBreakdown) > 0)
        <div class="section-title">Material Breakdown</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Material</th>
                    <th style="width: 25%; text-align: center;">Total Quantity</th>
                    @if($canViewPricing)
                    <th style="width: 25%; text-align: right;">Total Value</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($materialBreakdown as $materialName => $data)
                <tr>
                    <td>{{ $materialName }}</td>
                    <td class="text-center">{{ number_format($data['quantity'], 2) }}</td>
                    @if($canViewPricing)
                    <td class="text-right">₦{{ number_format($data['total_value'], 2) }}</td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- Project Team -->
        <div class="section-title">Project Team</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Name</th>
                    <th style="width: 25%;">Role</th>
                    <th style="width: 25%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($project->siteManagers as $manager)
                <tr>
                    <td>{{ $manager->firstname }} {{ $manager->lastname }}</td>
                    <td>Site Manager</td>
                    <td>{{ $manager->pivot->is_active ? 'Active' : 'Inactive' }}</td>
                </tr>
                @endforeach
                @foreach($project->procurementOfficers as $officer)
                <tr>
                    <td>{{ $officer->firstname }} {{ $officer->lastname }}</td>
                    <td>Procurement Officer</td>
                    <td>{{ $officer->pivot->is_active ? 'Active' : 'Inactive' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            Generated on {{ $generatedAt->format('F d, Y g:i A') }} by {{ $generatedBy->firstname }}
            {{ $generatedBy->lastname }}
            <br>
            This is a system-generated document from the Project Procurement Management System
        </div>
    </body>

</html>