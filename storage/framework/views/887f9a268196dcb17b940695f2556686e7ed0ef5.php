<?php $__env->startSection('title', 'Projects Dashboard | PPMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-body" x-data="{ viewMode: 'grid' }">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Projects Dashboard</h4>
                    <p class="mb-0">Overview of all construction projects</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Projects</li>
                </ol>
            </div>
        </div>

        <!-- Alerts -->
        <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-x-circle me-2"></i>
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-xl-3 col-sm-6">
                <div class="card card-accent card-accent-primary">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="d-block mb-1 text-muted">Total Projects</span>
                            <h3 class="mb-0"><?php echo e(number_format($stats['total'])); ?></h3>
                        </div>
                        <div class="avatar-lg rounded-circle bg-primary-light text-primary d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px;">
                            <i class="bi bi-folder fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card card-accent card-accent-success">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="d-block mb-1 text-muted">Active</span>
                            <h3 class="mb-0"><?php echo e(number_format($stats['active'])); ?></h3>
                        </div>
                        <div class="avatar-lg rounded-circle bg-success-light text-success d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px;">
                            <i class="bi bi-play-circle fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card card-accent card-accent-info">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="d-block mb-1 text-muted">Completed</span>
                            <h3 class="mb-0"><?php echo e(number_format($stats['completed'])); ?></h3>
                        </div>
                        <div class="avatar-lg rounded-circle bg-info-light text-info d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px;">
                            <i class="bi bi-check-circle fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card card-accent card-accent-warning">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="d-block mb-1 text-muted">Total Budget</span>
                            <h4 class="mb-0">₦<?php echo e(number_format($stats['total_budget'] / 1000000, 1)); ?>M</h4>
                        </div>
                        <div class="avatar-lg rounded-circle bg-warning-light text-warning d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px;">
                            <i class="bi bi-wallet2 fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary" :class="{ 'active': viewMode === 'list' }"
                        @click="viewMode = 'list'">
                        <i class="bi bi-list-ul"></i> List
                    </button>
                    <button type="button" class="btn btn-outline-primary" :class="{ 'active': viewMode === 'grid' }"
                        @click="viewMode = 'grid'">
                        <i class="bi bi-grid-fill"></i> Grid
                    </button>

                </div>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\Project::class)): ?>
                <a href="<?php echo e(route('projects.create')); ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> New Project
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Grid View -->
        <div class="row" x-show="viewMode === 'grid'">
            <?php $__empty_1 = true; $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title mb-1">
                                    <a href="<?php echo e(route('projects.show', $project)); ?>"
                                        class="text-info"><?php echo e($project->name); ?></a>
                                </h5>
                                <span class="text-muted small"><?php echo e($project->code); ?></span>
                            </div>
                            <span class="badge badge-<?php echo e($project->getStatusBadgeClass()); ?>">
                                <?php echo e(ucfirst(str_replace('_', ' ', $project->status))); ?>

                            </span>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">Location</small>
                            <span><?php echo e($project->location ?? 'Not specified'); ?></span>
                        </div>

                        <?php if(auth()->user()->hasPermission('view-request-pricing')): ?>
                        <div class="mb-3">
                            <small class="text-muted d-block">Budget</small>
                            <span class="fw-bold text-success">
                                <?php if($project->budget): ?>
                                ₦<?php echo e(number_format($project->budget, 2)); ?>

                                <?php else: ?>
                                <span class="text-muted fw-normal">N/A</span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <?php endif; ?>

                        <div class="d-flex align-items-center justify-content-between mt-4">
                            <div class="users-group">
                                <?php $__currentLoopData = $project->siteManagers->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="avatar-group-item" title="<?php echo e($sm->firstname); ?> <?php echo e($sm->lastname); ?>">
                                    <span class="avatar-title rounded-circle bg-primary text-white small"
                                        style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                        <?php echo e(substr($sm->firstname, 0, 1)); ?><?php echo e(substr($sm->lastname, 0, 1)); ?>

                                    </span>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php $__currentLoopData = $project->procurementOfficers->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="avatar-group-item" title="<?php echo e($po->firstname); ?> <?php echo e($po->lastname); ?>">
                                    <span class="avatar-title rounded-circle bg-danger text-white small"
                                        style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                        <?php echo e(substr($po->firstname, 0, 1)); ?><?php echo e(substr($po->lastname, 0, 1)); ?>

                                    </span>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <a href="<?php echo e(route('projects.show', $project)); ?>" class="btn btn-sm btn-outline-primary">
                                View Details
                            </a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $project)): ?>
                            <a href="<?php echo e(route('projects.destroy', $project)); ?>" class="btn btn-sm btn-danger"
                                title="Delete"
                                onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this project?')) { document.getElementById('delete-form-<?php echo e($project->id); ?>').submit(); }">
                                <i class="bi bi-trash"></i>
                            </a>
                            <form id="delete-form-<?php echo e($project->id); ?>" action="<?php echo e(route('projects.destroy', $project)); ?>"
                                method="POST" class="d-none">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-folder-x display-1 text-muted"></i>
                <h5 class="mt-3">No projects found</h5>
            </div>
            <?php endif; ?>
        </div>

        <!-- List View -->
        <div class="row" x-show="viewMode === 'list'" style="display: none;">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="projectsTable" class="table table-bordered table-hover w-100">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Location</th>
                                        <?php if(auth()->user()->hasPermission('view-request-pricing')): ?>
                                        <th>Budget</th>
                                        <?php endif; ?>
                                        <th>Status</th>
                                        <th>Site Manager</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo e(route('projects.show', $project)); ?>"
                                                class="fw-bold text-primary">
                                                <?php echo e($project->code); ?>

                                            </a>
                                        </td>
                                        <td><?php echo e($project->name); ?></td>
                                        <td><?php echo e($project->location ?? 'N/A'); ?></td>
                                        <?php if(auth()->user()->hasPermission('view-request-pricing')): ?>
                                        <td>
                                            <?php if($project->budget): ?>
                                            ₦<?php echo e(number_format($project->budget, 2)); ?>

                                            <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <?php endif; ?>
                                        <td>
                                            <span class="badge badge-<?php echo e($project->getStatusBadgeClass()); ?>">
                                                <?php echo e(ucfirst(str_replace('_', ' ', $project->status))); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <?php $__currentLoopData = $project->siteManagers->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="small"><?php echo e($sm->firstname); ?> <?php echo e($sm->lastname); ?></div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view', $project)): ?>
                                                <a href="<?php echo e(route('projects.show', $project)); ?>"
                                                    class="btn btn-sm btn-info" title="View"><i
                                                        class="bi bi-eye"></i></a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $project)): ?>
                                                <a href="<?php echo e(route('projects.edit', $project)); ?>"
                                                    class="btn btn-sm btn-primary" title="Edit"><i
                                                        class="bi bi-pencil"></i></a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $project)): ?>
                                                <a href="<?php echo e(route('projects.destroy', $project)); ?>"
                                                    class="btn btn-sm btn-danger" title="Delete"
                                                    onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this project?')) { document.getElementById('delete-form-<?php echo e($project->id); ?>').submit(); }">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                                <form id="delete-form-<?php echo e($project->id); ?>"
                                                    action="<?php echo e(route('projects.destroy', $project)); ?>" method="POST"
                                                    class="d-none">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-light {
        background-color: rgba(13, 110, 253, 0.1);
    }

    .bg-success-light {
        background-color: rgba(25, 135, 84, 0.1);
    }

    .bg-info-light {
        background-color: rgba(13, 202, 240, 0.1);
    }

    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.1);
    }

    .users-group {
        display: flex;
    }

    .avatar-group-item {
        margin-right: -10px;
        border: 2px solid #fff;
        border-radius: 50%;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        $('#projectsTable').DataTables({
            responsive: true,
            pageLength: 25,
            order: [
                [0, 'asc']
            ],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                '<"row"<"col-sm-12"tr>>' +
                '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search projects..."
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Ahmadx/Downloads/archillery/resources/views/projects/index.blade.php ENDPATH**/ ?>