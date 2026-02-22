<div class="deznav">
    <div class="deznav-scroll">
        <ul class="metismenu font-sans" id="menu">
            
            <li>
                <a class="ai-icon" href="<?php echo e(route('dashboard')); ?>" aria-expanded="false">
                    <i class="flaticon-381-networking fs-26"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            
            <?php if(auth()->user()->hasPermission('view-projects')): ?>
            <li>
                <a class="ai-icon" href="<?php echo e(route('projects.index')); ?>" aria-expanded="false">
                    <i class="mdi mdi-home-modern fs-26"></i>
                    <span class="nav-text">Projects</span>
                </a>
            </li>
            <?php endif; ?>

            
            <?php if(auth()->user()->hasPermission('view-requests')): ?>
            <li>
                <a class="ai-icon" href="<?php echo e(route('requests.index')); ?>" aria-expanded="false">
                    <i class="mdi mdi-progress-clock fs-26"></i>
                    <span class="nav-text">My Requests</span>
                </a>
            </li>
            <?php endif; ?>

            
            <?php if(auth()->user()->hasPermission('process-purchase-request') ||
            auth()->user()->hasPermission('approve-purchase-request')): ?>
            <hr>
            <li class="nav-label first">Procurement Queues</li>
            <?php endif; ?>

            
            <?php if(auth()->user()->hasPermission('process-purchase-request')): ?>
            <li>
                <a class="ai-icon" href="<?php echo e(route('approvals.procurement-queue')); ?>" aria-expanded="false">
                    <i class="mdi mdi-clock-alert-outline fs-20"></i>
                    <span class="nav-text">Procurement</span>
                    <?php
                    $pendingCount = \App\Models\ProcurementRequest::whereIn('status', ['submitted',
                    'pending_procurement', 'procurement_processing'])
                    ->whereHas('project.assignments', function($q) {
                    $q->where('user_id', auth()->id())
                    ->where('role_type', 'procurement_officer')
                    ->where('is_active', true);
                    })->count();
                    ?>
                    <?php if($pendingCount > 0): ?>
                    <span class="badge badge-rounded badge-warning"><?php echo e($pendingCount); ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <?php endif; ?>

            
            <?php if(auth()->user()->hasPermission('approve-purchase-request')): ?>
            <li>
                <a class="ai-icon" href="<?php echo e(route('approvals.director-queue')); ?>" aria-expanded="false">
                    <i class="mdi mdi-check-circle-outline fs-26"></i>
                    <span class="nav-text">Approvals</span>
                    <?php
                    $approvalCount = \App\Models\ProcurementRequest::where('status', 'pending_director')->count();
                    ?>
                    <?php if($approvalCount > 0): ?>
                    <span class="badge badge-rounded badge-info"><?php echo e($approvalCount); ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <?php endif; ?>

            
            <?php if(auth()->user()->hasPermission('view-deliveries')): ?>
            <li>
                <a class="ai-icon" href="<?php echo e(route('deliveries.all')); ?>" aria-expanded="false">
                    <i class="mdi mdi-truck-delivery fs-26"></i>
                    <span class="nav-text">Deliveries</span>
                    <?php
                    $deliveryCount = \App\Models\ProcurementRequest::whereIn('status', ['approved',
                    'partially_delivered'])->count();
                    ?>
                    <?php if($deliveryCount > 0): ?>
                    <span class="badge badge-rounded badge-success"><?php echo e($deliveryCount); ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <?php endif; ?>

            
            <?php if(auth()->user()->hasPermission('view-vendors') || auth()->user()->hasPermission('view-materials')): ?>
            <hr>
            <li class="nav-label first">Resources</li>
            <?php endif; ?>

            
            <?php if(auth()->user()->hasPermission('view-vendors')): ?>
            <li>
                <a class="ai-icon" href="<?php echo e(route('vendors.index')); ?>" aria-expanded="false">
                    <i class="fa fa-users fs-26"></i>
                    <span class="nav-text">Vendors</span>
                </a>
            </li>
            <?php endif; ?>

            
            <?php if(auth()->user()->hasPermission('view-materials')): ?>
            <li>
                <a class="ai-icon" href="<?php echo e(route('materials.index')); ?>" aria-expanded="false">
                    <i class="mdi mdi-package-variant fs-26"></i>
                    <span class="nav-text">Materials</span>
                </a>
            </li>
            <?php endif; ?>

            
            <?php if(auth()->user()->hasPermission('manage-users') || auth()->user()->hasPermission('view-audits')): ?>
            <hr>
            <li class="nav-label first">Administration</li>
            <?php endif; ?>

            
            <?php if(auth()->user()->hasPermission('manage-users')): ?>
            <li>
                <a class="ai-icon" href="<?php echo e(route('users')); ?>" aria-expanded="false">
                    <i class="fa fa-gear fs-26"></i>
                    <span class="nav-text">User Settings</span>
                </a>
            </li>
            <?php endif; ?>

            
            <?php if(auth()->user()->hasPermission('view-audits')): ?>
            <li>
                <a class="ai-icon" href="<?php echo e(route('audits')); ?>" aria-expanded="false">
                    <i class="mdi mdi-watch fs-26"></i>
                    <span class="nav-text">Audit Logs</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</div><?php /**PATH /Users/Ahmadx/Downloads/archillery/resources/views/partials/admin-sidebar.blade.php ENDPATH**/ ?>