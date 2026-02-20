<?php $__env->startSection('title', 'Procurement Requests | PPMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Procurement Requests</h4>
                    <p class="mb-0">Manage material procurement requests</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Requests</li>
                </ol>
            </div>
        </div>

        <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">All Procurement Requests</h4>

                        <div class="d-flex gap-2">
                            <!-- Filter Form -->
                            <form action="<?php echo e(route('requests.index')); ?>" method="GET" class="d-flex gap-2">
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">All Statuses</option>
                                    <?php $__currentLoopData = App\Enums\RequestStatus::cases(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($status->value); ?>"
                                        <?php echo e(request('status') == $status->value ? 'selected' : ''); ?>>
                                        <?php echo e($status->label()); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>

                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control" placeholder="Search..."
                                        value="<?php echo e(request('search')); ?>">
                                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                                </div>

                                <?php if(request('status') || request('search')): ?>
                                <a href="<?php echo e(route('requests.index')); ?>" class="btn btn-sm btn-outline-secondary"
                                    title="Clear Filters">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                                <?php endif; ?>
                            </form>

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\ProcurementRequest::class)): ?>
                            <a href="<?php echo e(route('requests.create')); ?>" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus-circle me-1"></i> New Request
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div class="d-flex gap-2 mb-3">
                                
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    onclick="copyTable('#requestsTable')">
                                    Copy
                                </button>

                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    onclick="printTable('#requestsTable', 'Procurement Requests')">
                                    Print
                                </button>
                            </div>
                            <?php
                            $showRequestedBy = $requests->contains(function ($req) {
                            return $req->requested_by !== auth()->id();
                            });
                            ?>
                            <table id="requestsTable" class="table table-hover table-responsive-sm">
                                <thead>
                                    <tr>
                                        <th>Request #</th>
                                        <th>Project</th>
                                        <th>Requested By</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <?php if(auth()->user()->hasPermission('view-request-pricing')): ?>
                                        <th>Amount</th>
                                        <?php endif; ?>
                                        <th>Items</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo e(route('requests.show', $request)); ?>"
                                                class="fw-bold text-primary">
                                                <?php echo e($request->request_number); ?>

                                            </a>
                                        </td>
                                        <td>
                                            <div class="fw-bold"><?php echo e($request->project->name); ?></div>
                                            <small class="text-muted"><?php echo e($request->project->code); ?></small>
                                        </td>
                                        <?php if($showRequestedBy): ?>
                                        <td>
                                            <?php if($request->requested_by === auth()->id()): ?>
                                            <span class="text-muted">You</span>
                                            <?php else: ?>
                                            <?php echo e(optional($request->requestedBy)->firstname ?? 'N/A'); ?>

                                            <?php echo e(optional($request->requestedBy)->lastname); ?>

                                            <?php endif; ?>
                                        </td>
                                        <?php endif; ?>
                                        <td><?php echo e($request->request_date->format('M d, Y')); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo e($request->status->badgeClass()); ?>">
                                                <?php echo e($request->status->label()); ?>

                                            </span>
                                        </td>
                                        <?php if(auth()->user()->hasPermission('view-request-pricing')): ?>
                                        <td>
                                            <?php if($request->total_quoted_amount): ?>
                                            <span
                                                class="fw-bold text-success">₦<?php echo e(number_format($request->total_quoted_amount, 2)); ?></span>
                                            <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <?php endif; ?>
                                        <td><?php echo e($request->items->count()); ?></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view', $request)): ?>
                                                <a href="<?php echo e(route('requests.show', $request)); ?>"
                                                    class="btn btn-sm btn-info" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <?php endif; ?>

                                                <?php if($request->isEditable()): ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $request)): ?>
                                                <a href="<?php echo e(route('requests.edit', $request)); ?>"
                                                    class="btn btn-sm btn-primary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <?php endif; ?>

                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $request)): ?>
                                                <form action="<?php echo e(route('requests.destroy', $request)); ?>" method="POST"
                                                    onsubmit="return confirm('Are you sure?');" class="d-inline">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                                <p>No requests found for this project.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <?php echo e($requests->links('pagination::bootstrap-4')); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function tableToTSV(tableSelector) {
        const table = document.querySelector(tableSelector);
        if (!table) return '';

        const rows = Array.from(table.querySelectorAll('tr'));
        return rows.map(row => {
            const cells = Array.from(row.querySelectorAll('th,td'));
            return cells.map(cell => {
                // normalize whitespace, strip newlines/tabs to keep TSV clean
                return (cell.innerText || '')
                    .replace(/\s+/g, ' ')
                    .replace(/\t/g, ' ')
                    .trim();
            }).join('\t');
        }).join('\n');
    }

    async function copyTable(tableSelector) {
        const tsv = tableToTSV(tableSelector);

        try {
            await navigator.clipboard.writeText(tsv);
            alert('Table copied to clipboard.');
        } catch (e) {
            // fallback for older browsers
            const textarea = document.createElement('textarea');
            textarea.value = tsv;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            alert('Table copied to clipboard.');
        }
    }

    function printTable(tableSelector, title = 'Print') {
        const table = document.querySelector(tableSelector);
        if (!table) return;

        const printWindow = window.open('', '_blank');
        const styles = `
      <style>
        body { font-family: Arial, sans-serif; padding: 16px; }
        h2 { margin: 0 0 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; font-size: 12px; }
        th { background: #f5f5f5; text-align: left; }
        @media print { button { display: none; } }
      </style>
    `;

        printWindow.document.write(`
      <html>
        <head>
          <title>${title}</title>
          ${styles}
        </head>
        <body>
          <h2>${title}</h2>
          ${table.outerHTML}
          <script>
            window.onload = function () { window.print(); window.close(); };
          <\/script>
        </body>
      </html>
    `);

        printWindow.document.close();
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Ahmadx/Downloads/ppms/resources/views/requests/index.blade.php ENDPATH**/ ?>