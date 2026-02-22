<?php $__env->startSection('title', 'Vendors | PPMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Vendors</h4>
                    <p class="mb-0">Manage vendor information</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Vendors</li>
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

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">All Vendors</h4>
                        <?php if(auth()->user()->hasPermission('create-vendors')): ?>
                        <a href="<?php echo e(route('vendors.create')); ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> New Vendor
                        </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="vendorsTable" class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Location</th>
                                        <th>Rating</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><a href="<?php echo e(route('vendors.show', $vendor)); ?>"
                                                class="fw-bold text-primary"><?php echo e($vendor->code); ?></a></td>
                                        <td><?php echo e($vendor->name); ?></td>
                                        <td><?php echo e($vendor->phone); ?></td>
                                        <td><?php echo e($vendor->state?->state ?? 'N/A'); ?></td>
                                        <td>
                                            <?php if($vendor->rating): ?>
                                            <div class="d-flex align-items-center">
                                                <span class="me-1"><?php echo e(number_format($vendor->rating, 1)); ?></span>
                                                <?php for($i = 1; $i <= 5; $i++): ?> <i
                                                    class="bi bi-star<?php echo e($i <= $vendor->rating ? '-fill text-warning' : ''); ?>">
                                                    </i>
                                                    <?php endfor; ?>
                                            </div>
                                            <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><span
                                                class="badge badge-<?php echo e($vendor->status === 'active' ? 'success' : ($vendor->status === 'suspended' ? 'danger' : 'secondary')); ?>"><?php echo e(ucfirst($vendor->status)); ?></span>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('vendors.show', $vendor)); ?>"
                                                class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                                            <?php if(auth()->user()->hasAnyRole(['admin', 'super_admin',
                                            'procurement_officer'])): ?>
                                            <a href="<?php echo e(route('vendors.edit', $vendor)); ?>"
                                                class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></a>
                                            <?php endif; ?>
                                            <?php if(auth()->user()->hasAnyRole(['admin', 'super_admin',
                                            'director'])): ?>
                                            <a href="<?php echo e(route('vendors.destroy', $vendor)); ?>"
                                                class="btn btn-sm btn-danger" title="Delete"
                                                onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this vendor?')) { document.getElementById('delete-form-<?php echo e($vendor->id); ?>').submit(); }">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                            <form id="delete-form-<?php echo e($vendor->id); ?>"
                                                action="<?php echo e(route('vendors.destroy', $vendor)); ?>" method="POST"
                                                class="d-none">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                            </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="bi bi-shop display-1 text-muted"></i>
                                            <h5 class="mt-3">No vendors found</h5>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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
    $(document).ready(function() {
        $('#vendorsTable').DataTable({
            responsive: true,
            pageLength: 25,
            order: [
                [1, 'asc']
            ],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                '<"row"<"col-sm-12"B>>' +
                '<"row"<"col-sm-12"tr>>' +
                '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            buttons: [{
                    extend: 'copy',
                    className: 'btn btn-sm btn-secondary',
                    text: '<i class="bi bi-clipboard"></i> Copy'
                },
                {
                    extend: 'csv',
                    className: 'btn btn-sm btn-secondary',
                    text: '<i class="bi bi-file-earmark-csv"></i> CSV'
                },
                {
                    extend: 'excel',
                    className: 'btn btn-sm btn-secondary',
                    text: '<i class="bi bi-file-earmark-excel"></i> Excel'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-sm btn-secondary',
                    text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                    orientation: 'landscape'
                },
                {
                    extend: 'print',
                    className: 'btn btn-sm btn-secondary',
                    text: '<i class="bi bi-printer"></i> Print'
                }
            ],
            initComplete: function() {
                const searchInput = $('#vendorsTable_filter input');

                searchInput
                    .addClass('form-control form-control-sm')
                    .attr('placeholder', 'Search requests..')
                    .css({
                        'width': '180px', // 👈 makes it wide
                        'height': '32px', // 👈 slim height
                        'padding': '0.25rem 0.5rem',
                        'font-size': '0.875rem'
                    });

            },
            columnDefs: [{
                    orderable: false,
                    targets: [6, -1]
                } // Disable sorting on rating and actions columns
            ],
            language: {
                searchPlaceholder: "Search vendors...",
                lengthMenu: "Show _MENU_ vendors",
                info: "Showing _START_ to _END_ of _TOTAL_ vendors",
                zeroRecords: "No matching vendors found"
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Ahmadx/Downloads/archillery/resources/views/vendors/index.blade.php ENDPATH**/ ?>