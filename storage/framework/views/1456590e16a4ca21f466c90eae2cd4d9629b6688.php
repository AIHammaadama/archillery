<?php $__env->startSection('title', 'Materials Catalog | PPMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Materials Catalog</h4>
                    <p class="mb-0">Browse construction materials and supplies</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Materials</li>
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

        <!-- Materials catalog -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">All Materials (<?php echo e($materials->total()); ?>)</h4>
                        <?php if(auth()->user()->hasAnyRole(['admin', 'super_admin'])): ?>
                        <a href="<?php echo e(route('materials.create')); ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> New Material
                        </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <form method="GET" action="<?php echo e(route('materials.index')); ?>" class="row mb-4">
                            <div class="col-md-5">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search materials by name, code, or description..."
                                        value="<?php echo e(request('search')); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="category" class="form-control">
                                    <option value="all">All Categories</option>
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($cat); ?>" <?php echo e(request('category') === $cat ? 'selected' : ''); ?>>
                                        <?php echo e($cat); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="is_active" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="1" <?php echo e(request('is_active') === '1' ? 'selected' : ''); ?>>Active
                                    </option>
                                    <option value="0" <?php echo e(request('is_active') === '0' ? 'selected' : ''); ?>>Inactive
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn w-100 form-control btn-primary text-light">
                                    <i class="bi bi-funnel"></i> Filter
                                </button>
                            </div>
                        </form>

                        <!-- Materials grid -->
                        <?php if($materials->count() > 0): ?>
                        <div class="row g-3">
                            <?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card material-card h-100 card-accent ">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h5 class="card-title mb-1">
                                                    <a href="<?php echo e(route('materials.show', $material)); ?>"
                                                        class="text-secondary">
                                                        <?php echo e($material->name); ?>

                                                    </a>
                                                </h5>
                                                <p class="small text-muted mb-0"><?php echo e($material->code); ?></p>
                                            </div>
                                            <?php if($material->is_active): ?>
                                            <span class="badge badge-success">Active</span>
                                            <?php else: ?>
                                            <span class="badge badge-secondary">Inactive</span>
                                            <?php endif; ?>
                                        </div>

                                        <?php if($material->category): ?>
                                        <span class="badge badge-danger mb-2"><?php echo e($material->category); ?></span>
                                        <?php endif; ?>

                                        <?php if($material->description): ?>
                                        <p class="card-text small text-muted mb-2">
                                            <?php echo e(Str::limit($material->description, 80)); ?>

                                        </p>
                                        <?php endif; ?>

                                        <div
                                            class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top">
                                            <div>
                                                <?php if($material->unit_of_measurement): ?>
                                                <small class="text-muted">
                                                    <i class="bi bi-box"></i>
                                                    <?php echo e(ucfirst($material->unit_of_measurement)); ?>

                                                </small>
                                                <?php endif; ?>
                                            </div>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('materials.show', $material)); ?>"
                                                    class="btn btn-sm btn-info" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <?php if(auth()->user()->hasAnyRole(['admin', 'super_admin'])): ?>
                                                <a href="<?php echo e(route('materials.edit', $material)); ?>"
                                                    class="btn btn-sm btn-primary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <?php endif; ?>
                                                <?php if(auth()->user()->hasAnyRole(['admin', 'super_admin',
                                                'director'])): ?>
                                                <a href="<?php echo e(route('materials.destroy', $material)); ?>"
                                                    class="btn btn-sm btn-danger" title="Delete"
                                                    onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this material?')) { document.getElementById('delete-form-<?php echo e($material->id); ?>').submit(); }">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                                <form id="delete-form-<?php echo e($material->id); ?>"
                                                    action="<?php echo e(route('materials.destroy', $material)); ?>" method="POST"
                                                    class="d-none">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <!-- Pagination -->
                        <?php if($materials->hasPages()): ?>
                        <div class="mt-4">
                            <?php echo e($materials->links('pagination::bootstrap-4')); ?>

                        </div>
                        <?php endif; ?>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-box-seam display-1 text-muted"></i>
                            <h5 class="mt-3">No materials found</h5>
                            <p class="text-muted">
                                <?php if(request()->hasAny(['search', 'category', 'is_active'])): ?>
                                Try adjusting your filters.
                                <a href="<?php echo e(route('materials.index')); ?>">Clear filters</a>
                                <?php elseif(auth()->user()->hasAnyRole(['admin', 'super_admin'])): ?>
                                <a href="<?php echo e(route('materials.create')); ?>">Add your first material</a>
                                <?php else: ?>
                                The materials catalog is empty.
                                <?php endif; ?>
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .material-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid var(--border-color);
    }

    .material-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .material-card .card-title a {
        text-decoration: none;
    }

    .material-card .card-title a:hover {
        color: var(--color-primary) !important;
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Ahmadx/Downloads/archillery/resources/views/materials/index.blade.php ENDPATH**/ ?>