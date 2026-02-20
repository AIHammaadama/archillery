<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request <?php echo e($request->request_number); ?></title>
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
        <img src="<?php echo e(public_path('images/logo_report.png')); ?>" alt="Company Logo"
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
            <div class="info-value text-bold"><?php echo e($request->request_number); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">
                <?php if($request->status->value === 'approved'): ?>
                <span class="status-badge status-approved">Approved</span>
                <?php elseif($request->status->value === 'rejected'): ?>
                <span class="status-badge status-rejected">Rejected</span>
                <?php else: ?>
                <span class="status-badge status-pending"><?php echo e($request->status->label()); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Request Date:</div>
            <div class="info-value"><?php echo e($request->request_date->format('F d, Y')); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Required By:</div>
            <div class="info-value"><?php echo e($request->required_by_date->format('F d, Y')); ?></div>
        </div>
    </div>

    <!-- Project Details -->
    <div class="section-title">Project Details</div>
    <div class="info-grid">
        <div class="info-row">
            <div class="info-label">Project Name:</div>
            <div class="info-value"><?php echo e($request->project->name); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Project Code:</div>
            <div class="info-value"><?php echo e($request->project->code); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Location:</div>
            <div class="info-value"><?php echo e(collect([
        $request->project->location,
        optional($request->project->lga)->lga,
        optional($request->project->state)->state,
    ])->filter()->implode(', ') ?: 'Not specified'); ?></div>
        </div>
    </div>

    <!-- Personnel -->
    <div class="section-title">Personnel</div>
    <div class="info-grid">
        <div class="info-row">
            <div class="info-label">Requested By:</div>
            <div class="info-value">
                <?php if($request->requestedBy): ?>
                <?php echo e($request->requestedBy->firstname); ?> <?php echo e($request->requestedBy->lastname); ?>

                <?php else: ?>
                N/A
                <?php endif; ?>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Procurement Officer:</div>
            <div class="info-value">
                <?php if($request->procurementOfficer): ?>
                <?php echo e($request->procurementOfficer->firstname); ?> <?php echo e($request->procurementOfficer->lastname); ?>

                <?php else: ?>
                Not assigned
                <?php endif; ?>
            </div>
        </div>
        <?php if($request->approvedBy): ?>
        <div class="info-row">
            <div class="info-label">Approved By:</div>
            <div class="info-value">
                <?php echo e($request->approvedBy->firstname); ?> <?php echo e($request->approvedBy->lastname); ?>

                <br><span
                    style="font-size: 8pt; color: #666;"><?php echo e($request->approved_at->format('F d, Y g:i A')); ?></span>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Justification -->
    <?php if($request->justification): ?>
    <div class="section-title">Justification</div>
    <p style="padding: 10px; background-color: #f9fafb; border-left: 3px solid #f46839;">
        <?php echo e($request->justification); ?>

    </p>
    <?php endif; ?>
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
                <?php if($canViewPricing): ?>
                <th style="width: 20%;">Vendor</th>
                <th style="width: 10%; text-align: right;">Unit Price</th>
                <th style="width: 10%; text-align: right;">Total</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $request->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($index + 1); ?></td>
                <td>
                    <strong><?php echo e($item->material->name); ?></strong><br>
                    <span style="font-size: 8pt; color: #666;"><?php echo e($item->material->code); ?></span>
                </td>
                <td><?php echo e(number_format($item->quantity, 2)); ?></td>
                <td><?php echo e($item->material->unit_of_measurement); ?></td>
                <?php if($canViewPricing): ?>
                <td><?php echo e($item->vendor ? $item->vendor->name : 'Not assigned'); ?></td>
                <td class="text-right">
                    <?php if($item->quoted_unit_price): ?>
                    ₦<?php echo e(number_format($item->quoted_unit_price, 2)); ?>

                    <?php else: ?>
                    -
                    <?php endif; ?>
                </td>
                <td class="text-right">
                    <?php if($item->quoted_total_price): ?>
                    ₦<?php echo e(number_format($item->quoted_total_price, 2)); ?>

                    <?php else: ?>
                    -
                    <?php endif; ?>
                </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if($canViewPricing): ?>
            <tr class="total-row">
                <td colspan="6" class="text-right">TOTAL:</td>
                <td class="text-right">₦<?php echo e(number_format($request->total_quoted_amount ?? 0, 2)); ?></td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php
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
    ?>
    <!-- Vendor Payment Summary -->
    <?php if($canViewPricing): ?>
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
            <?php $__empty_1 = true; $__currentLoopData = $vendorSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($row['vendor_name']); ?></td>
                <td style="text-align:right;">
                    ₦<?php echo e(number_format($row['total'], 2)); ?>

                </td>
                <td><?php echo e($row['bank_name']); ?></td>
                <td><?php echo e($row['account_name']); ?></td>
                <td><?php echo e($row['account_number']); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="5" style="text-align:center; color:#666;">
                    No vendor assignments yet.
                </td>
            </tr>
            <?php endif; ?>

            <?php if($vendorSummary->count()): ?>
            <tr class="total-row">
                <td class="text-right" colspan="1"><strong>GRAND TOTAL:</strong></td>
                <td style="text-align:right;">
                    <strong>₦<?php echo e(number_format($vendorSummary->sum('total'), 2)); ?></strong>
                </td>
                <td colspan="3"></td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <!-- Status History -->
    <?php if($request->statusHistory && $request->statusHistory->count() > 0): ?>
    <div class="section-title">Status History</div>
    <div class="timeline">
        <?php $__currentLoopData = $request->statusHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="timeline-item">
            <div class="timeline-date">
                <?php echo e(optional($history->created_at)->format('M d, Y g:i A') ?? '—'); ?>

            </div>
            <div class="timeline-user">
                <?php echo e($history->changedBy->firstname ?? 'System'); ?> <?php echo e($history->changedBy->lastname ?? ''); ?>

            </div>
            <div>
                Changed from <strong><?php echo e(ucwords(str_replace('_', ' ', $history->from_status))); ?></strong>
                to <strong><?php echo e(ucwords(str_replace('_', ' ', $history->to_status))); ?></strong>
            </div>
            <?php if($history->comments): ?>
            <div class="timeline-comment"><?php echo e($history->comments); ?></div>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>

    <!-- Remarks -->
    <?php if($request->remarks): ?>
    <div class="section-title">Remarks</div>
    <p style="padding: 10px; background-color: #f9fafb; border-left: 3px solid #f46839;">
        <?php echo e($request->remarks); ?>

    </p>
    <?php endif; ?>

    <!-- Rejection Reason -->
    <?php if($request->rejection_reason): ?>
    <div class="section-title">Rejection Reason</div>
    <p style="padding: 10px; background-color: #fee2e2; border-left: 3px solid #991b1b; color: #991b1b;">
        <?php echo e($request->rejection_reason); ?>

    </p>
    <?php endif; ?>

    <!-- Signatures (only for approved requests) -->
    <?php if($request->status->value === 'approved'): ?>
    <div class="signatures">
        <div class="signature-block">
            <div class="signature-line">
                <?php echo e($request->requestedBy->firstname ?? ''); ?> <?php echo e($request->requestedBy->lastname ?? ''); ?>

            </div>
            <div class="signature-label">Site Manager</div>
        </div>
        <div class="signature-block">
            <div class="signature-line">
                <?php echo e($request->procurementOfficer->firstname ?? ''); ?>

                <?php echo e($request->procurementOfficer->lastname ?? ''); ?>

            </div>
            <div class="signature-label">Procurement Officer</div>
        </div>
        <div class="signature-block">
            <div class="signature-line">
                <?php echo e($request->approvedBy->firstname ?? ''); ?> <?php echo e($request->approvedBy->lastname ?? ''); ?>

            </div>
            <div class="signature-label">Director</div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Footer -->
    <div class="footer">
        Generated on <?php echo e($generatedAt->format('F d, Y g:i A')); ?> by <?php echo e($generatedBy->firstname); ?>

        <?php echo e($generatedBy->lastname); ?>

        <br>
        This is a system-generated document from the Project Procurement Management System
    </div>
</body>

</html><?php /**PATH /Users/Ahmadx/Downloads/ppms/resources/views/reports/request-detail.blade.php ENDPATH**/ ?>