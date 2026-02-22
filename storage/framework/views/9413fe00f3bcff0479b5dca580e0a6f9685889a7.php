<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Project Summary - <?php echo e($project->code); ?></title>
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

        .project-info {
            background-color: #f3f4f6;
            padding: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #f46839;
        }

        .project-info h2 {
            font-size: 12pt;
            color: #f46839;
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
            <img src="<?php echo e(public_path('images/logo_report.png')); ?>" alt="Company Logo"
                style="height: 60px; margin-right: 15px; margin-bottom:15px;">
            <div>
                <h1 style="margin: 0;">PROJECT SUMMARY REPORT</h1>
                <p style="margin: 0;"><?php echo e($startDate->format('F d, Y')); ?> to <?php echo e($endDate->format('F d, Y')); ?></p>
            </div>
        </div>

        <!-- Project Information -->
        <div class="project-info">
            <h2><?php echo e($project->name); ?></h2>
            <p><strong>Code:</strong> <?php echo e($project->code); ?></p>
            <p><strong>Location:</strong> <?php echo e(collect([
        $project->location,
        optional($project->lga)->lga,
        optional($project->state)->state,
    ])->filter()->implode(', ') ?: 'Not specified'); ?></p>
            <p><strong>Budget:</strong> ₦<?php echo e(number_format($project->budget ?? 0, 2)); ?></p>
            <?php if($project->description): ?>
            <p><strong>Description:</strong> <?php echo e($project->description); ?></p>
            <?php endif; ?>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-box">
                <span class="stat-value"><?php echo e($stats['total_requests']); ?></span>
                <span class="stat-label">Total Requests</span>
            </div>
            <div class="stat-box">
                <span class="stat-value"><?php echo e($stats['approved_requests']); ?></span>
                <span class="stat-label">Approved</span>
            </div>
            <div class="stat-box">
                <span class="stat-value"><?php echo e($stats['rejected_requests']); ?></span>
                <span class="stat-label">Rejected</span>
            </div>
            <div class="stat-box">
                <span class="stat-value"><?php echo e($stats['pending_requests']); ?></span>
                <span class="stat-label">Pending</span>
            </div>
        </div>

        <?php if($canViewPricing): ?>
        <div class="stats-grid" style="margin-bottom: 15px;">
            <div class="stat-box" style="width: 33%;">
                <span class="stat-value">₦<?php echo e(number_format($stats['total_value'], 2)); ?></span>
                <span class="stat-label">Total Procurement Value</span>
            </div>
            <div class="stat-box" style="width: 33%;">
                <span class="stat-value">₦<?php echo e(number_format($expenses->sum('amount'), 2)); ?></span>
                <span class="stat-label">Total Other Expenses</span>
            </div>
            <div class="stat-box" style="width: 34%;">
                <span class="stat-value">₦<?php echo e(number_format($stats['total_project_spending'], 2)); ?></span>
                <span class="stat-label">Total Project Spending</span>
            </div>
        </div>
        <?php endif; ?>

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
                    <?php if($canViewPricing): ?>
                    <th style="width: 15%; text-align: right;">Value</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($request->request_number); ?></td>
                    <td><?php echo e($request->request_date->format('M d, Y')); ?></td>
                    <td><?php echo e($request->requestedBy->firstname ?? ''); ?> <?php echo e($request->requestedBy->lastname ?? ''); ?></td>
                    <td><?php echo e($request->status->label()); ?></td>
                    <td class="text-center"><?php echo e($request->items->count()); ?></td>
                    <?php if($canViewPricing): ?>
                    <td class="text-right">₦<?php echo e(number_format($request->total_quoted_amount ?? 0, 2)); ?></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="<?php echo e($canViewPricing ? 6 : 5); ?>" class="text-center" style="padding: 15px; color: #999;">
                        No requests found for the selected date range
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Status Breakdown -->
        <?php if(count($statusBreakdown) > 0): ?>
        <div class="section-title">Status Breakdown</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Status</th>
                    <th style="width: 25%; text-align: center;">Count</th>
                    <?php if($canViewPricing): ?>
                    <th style="width: 25%; text-align: right;">Total Value</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $statusBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e(ucwords(str_replace('_', ' ', $status))); ?></td>
                    <td class="text-center"><?php echo e($data['count']); ?></td>
                    <?php if($canViewPricing): ?>
                    <td class="text-right">₦<?php echo e(number_format($data['total_value'], 2)); ?></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <?php endif; ?>

        <!-- Vendor Breakdown -->
        <?php if($canViewPricing && count($vendorBreakdown) > 0): ?>
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
                <?php $__currentLoopData = $vendorBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendorName => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($vendorName); ?></td>
                    <td class="text-center"><?php echo e($data['count']); ?></td>
                    <td class="text-right">₦<?php echo e(number_format($data['total_value'], 2)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <?php endif; ?>

        <!-- Other Project Expenses -->
        <?php if($canViewPricing && count($expenseBreakdown) > 0): ?>
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
                <?php $__currentLoopData = $expenseBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expenseType => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e(ucwords(str_replace('_', ' ', $expenseType))); ?></td>
                    <td class="text-center"><?php echo e($data['count']); ?></td>
                    <td class="text-right">₦<?php echo e(number_format($data['total_value'], 2)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <?php endif; ?>

        <!-- Material Breakdown -->
        <?php if(count($materialBreakdown) > 0): ?>
        <div class="section-title">Material Breakdown</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Material</th>
                    <th style="width: 25%; text-align: center;">Total Quantity</th>
                    <?php if($canViewPricing): ?>
                    <th style="width: 25%; text-align: right;">Total Value</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $materialBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $materialName => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($materialName); ?></td>
                    <td class="text-center"><?php echo e(number_format($data['quantity'], 2)); ?></td>
                    <?php if($canViewPricing): ?>
                    <td class="text-right">₦<?php echo e(number_format($data['total_value'], 2)); ?></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <?php endif; ?>

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
                <?php $__currentLoopData = $project->siteManagers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $manager): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($manager->firstname); ?> <?php echo e($manager->lastname); ?></td>
                    <td>Site Manager</td>
                    <td><?php echo e($manager->pivot->is_active ? 'Active' : 'Inactive'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php $__currentLoopData = $project->procurementOfficers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $officer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($officer->firstname); ?> <?php echo e($officer->lastname); ?></td>
                    <td>Procurement Officer</td>
                    <td><?php echo e($officer->pivot->is_active ? 'Active' : 'Inactive'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            Generated on <?php echo e($generatedAt->format('F d, Y g:i A')); ?> by <?php echo e($generatedBy->firstname); ?>

            <?php echo e($generatedBy->lastname); ?>

            <br>
            This is a system-generated document from the Project Procurement Management System
        </div>
    </body>

</html><?php /**PATH /Users/Ahmadx/Downloads/archillery/resources/views/reports/project-summary.blade.php ENDPATH**/ ?>