<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ProcurementRequestController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReceiptController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| All users go through the same authenticated routes.
| Feature access is controlled via permissions.
|
*/

// Root route
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Auth::routes(); // no email verification

// All authenticated users
Route::middleware(['auth'])->group(function () {

    // Admin-style routes controlled via permissions
    Route::prefix('PMS')->middleware('permission:access-admin-dashboard')->group(function () {

        /* ---------------- Dashboard & Profile ---------------- */
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
        Route::patch('/update-profile', [UserController::class, 'update_profile'])->name('profile.update');

        /* ---------------- User Management (Permission-Controlled) ---------------- */
        Route::get('/users', [DashboardController::class, 'users'])
            ->middleware('permission:manage-users')
            ->name('users');

        Route::post('/new-user', [UserController::class, 'new_user'])
            ->name('new-user')
            ->middleware('permission:manage-users');

        Route::patch('/update-user', [UserController::class, 'update_user'])
            ->name('update-user')
            ->middleware('permission:manage-users');

        Route::delete('/delete-user', [UserController::class, 'delete_user'])
            ->name('delete-user')
            ->middleware('permission:manage-users');

        Route::get('/search-users', [DashboardController::class, 'search_users'])
            ->name('search-users')
            ->middleware('permission:manage-users');

        /* ---------------- Permissions Routes ---------------- */
        Route::get('/permissions', [UserController::class, 'index'])
            ->middleware('permission:manage-permissions')
            ->name('permissions.index');

        Route::post('/permissions', [UserController::class, 'create_permission'])
            ->middleware('permission:manage-permissions')
            ->name('permissions.store');

        Route::patch('/permissions/{permission}', [UserController::class, 'edit_permission'])
            ->middleware('permission:manage-permissions')
            ->name('permissions.update');

        Route::delete('/permissions/{permission}', [UserController::class, 'delete_permission'])
            ->middleware('permission:manage-permissions')
            ->name('permissions.delete');

        Route::get('/permissions/search', [UserController::class, 'search_permissions'])
            ->middleware('permission:manage-permissions')
            ->name('permissions.search');

        /* ---------------- Roles Routes ---------------- */
        Route::get('/roles', [UserController::class, 'index'])
            ->middleware('permission:manage-roles')
            ->name('roles.index');

        Route::post('/roles', [UserController::class, 'create_role'])
            ->middleware('permission:manage-roles')
            ->name('roles.store');

        Route::patch('/roles/{role}', [UserController::class, 'edit_role'])
            ->middleware('permission:manage-roles')
            ->name('roles.update');

        Route::delete('/roles/{role}', [UserController::class, 'delete_role'])
            ->middleware('permission:manage-roles')
            ->name('roles.delete');

        Route::put('/roles/{role}/permissions', [UserController::class, 'updatePermissions'])
            ->middleware('permission:manage-roles')
            ->name('roles.permissions.update');

        /* ---------------- Audits ---------------- */
        Route::get('/audits', [DashboardController::class, 'audit'])
            ->middleware('permission:manage-audits')
            ->name('audits');
        Route::get('/audits/search', [DashboardController::class, 'search_audits'])
            ->middleware('permission:manage-audits')
            ->name('search-audits');
        Route::get('/audit/{id}/view', [DashboardController::class, 'view_audit'])
            ->middleware('permission:manage-audits')
            ->name('view-audit');

        /* ---------------- Projects ---------------- */
        Route::middleware('permission:view-projects')->group(function () {
            Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
            Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
            Route::get('/projects/{project}/requests', [ProjectController::class, 'requests'])->name('projects.requests');
            Route::get('/projects/{project}/attachments/{index}', [ProjectController::class, 'viewAttachment'])->name('projects.attachment.view');
            Route::get('/projects/expenses/{expense}/receipt', [ProjectController::class, 'viewExpenseReceipt'])->name('projects.expenses.receipt.view');
        });

        Route::middleware('permission:create-projects')->group(function () {
            Route::get('/projects/create/new', [ProjectController::class, 'create'])->name('projects.create');
            Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
        });

        Route::middleware('permission:edit-projects')->group(function () {
            Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
            Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        });

        Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])
            ->middleware('permission:delete-projects')
            ->name('projects.destroy');

        Route::middleware('permission:edit-projects')->group(function () {
            Route::delete('/projects/{project}/attachment', [ProjectController::class, 'deleteAttachment'])
                ->name('projects.delete-attachment');
            Route::post('/projects/{project}/expenses', [ProjectController::class, 'storeExpense'])
                ->name('projects.expenses.store');
        });

        /* ---------------- Materials ---------------- */
        Route::middleware('permission:view-materials')->group(function () {
            Route::get('/materials', [MaterialController::class, 'index'])->name('materials.index');
            Route::get('/materials/{material}', [MaterialController::class, 'show'])->name('materials.show');
        });

        Route::middleware('permission:manage-materials')->group(function () {
            Route::get('/materials/create/new', [MaterialController::class, 'create'])->name('materials.create');
            Route::post('/materials', [MaterialController::class, 'store'])->name('materials.store');
            Route::get('/materials/{material}/edit', [MaterialController::class, 'edit'])->name('materials.edit');
            Route::put('/materials/{material}', [MaterialController::class, 'update'])->name('materials.update');
            Route::delete('/materials/{material}', [MaterialController::class, 'destroy'])->name('materials.destroy');
        });

        /* ---------------- Procurement Requests ---------------- */
        Route::middleware('permission:view-requests')->group(function () {
            Route::get('/requests', [ProcurementRequestController::class, 'index'])->name('requests.index');
            Route::get('/requests/{request}', [ProcurementRequestController::class, 'show'])->name('requests.show');
            Route::get('/receipts/{receipt}/download', [ReceiptController::class, 'download'])->name('receipts.download');
            Route::post('/requests/{request}/receipts', [ReceiptController::class, 'store'])->name('requests.receipts.store');
        });

        Route::middleware('permission:create-purchase-request')->group(function () {
            Route::get('/requests/create/new', [ProcurementRequestController::class, 'create'])->name('requests.create');
            Route::post('/requests', [ProcurementRequestController::class, 'store'])->name('requests.store');
        });

        Route::middleware('permission:edit-purchase-request')->group(function () {
            Route::get('/requests/{request}/edit', [ProcurementRequestController::class, 'edit'])->name('requests.edit');
            Route::put('/requests/{request}', [ProcurementRequestController::class, 'update'])->name('requests.update');
        });

        Route::post('/requests/{request}/submit', [ProcurementRequestController::class, 'submit'])
            ->middleware('permission:create-purchase-request')
            ->name('requests.submit');

        Route::delete('/requests/{request}', [ProcurementRequestController::class, 'destroy'])
            ->middleware('permission:delete-purchase-request')
            ->name('requests.destroy');

        /* ---------------- Vendors ---------------- */
        Route::middleware('permission:view-vendors')->group(function () {
            Route::get('/vendors', [VendorController::class, 'index'])->name('vendors.index');
            Route::get('/vendors/{vendor}', [VendorController::class, 'show'])->name('vendors.show');
        });

        Route::middleware('permission:manage-vendors')->group(function () {
            Route::get('/vendors/create/new', [VendorController::class, 'create'])->name('vendors.create');
            Route::post('/vendors', [VendorController::class, 'store'])->name('vendors.store');
            Route::get('/vendors/{vendor}/edit', [VendorController::class, 'edit'])->name('vendors.edit');
            Route::put('/vendors/{vendor}', [VendorController::class, 'update'])->name('vendors.update');
            Route::delete('/vendors/{vendor}', [VendorController::class, 'destroy'])->name('vendors.destroy');

            // Material assignments
            Route::get('/vendors/{vendor}/materials/assign', [VendorController::class, 'assignMaterials'])->name('vendors.materials.assign');
            Route::post('/vendors/{vendor}/materials', [VendorController::class, 'storeMaterialAssignments'])->name('vendors.materials.store');

            Route::get('/vendors/{vendor}/materials/{material}/price', [VendorController::class, 'getMaterialPrice'])->name('api.vendors.material-price');
        });

        /* ---------------- Approvals ---------------- */
        // Procurement processing routes
        Route::middleware('permission:process-purchase-request')->group(function () {
            Route::get('/approvals/procurement-queue', [ApprovalController::class, 'procurementQueue'])
                ->name('approvals.procurement-queue');
        });

        // Vendor assignment routes - checked by policy in controller
        Route::middleware('permission:assign-vendors')->group(function () {
            Route::get('/approvals/{request}/assign-vendors', [ApprovalController::class, 'assignVendors'])
                ->name('approvals.assign-vendors');
            Route::post('/approvals/{request}/save-assignments', [ApprovalController::class, 'saveVendorAssignments'])
                ->name('approvals.save-assignments');
        });

        // Director approval routes
        Route::middleware('permission:approve-purchase-request')->group(function () {
            Route::get('/approvals/director-queue', [ApprovalController::class, 'directorQueue'])
                ->name('approvals.director-queue');
            Route::post('/approvals/{request}/approve', [ApprovalController::class, 'approve'])
                ->name('approvals.approve');
        });

        Route::middleware('permission:reject-purchase-request')->group(function () {
            Route::post('/approvals/{request}/reject', [ApprovalController::class, 'reject'])
                ->name('approvals.reject');
            Route::post('/approvals/{request}/send-back', [ApprovalController::class, 'sendBack'])
                ->name('approvals.send-back');
        });

        // Director: Edit vendor assignments
        Route::middleware('permission:edit-purchase-request')->group(function () {
            Route::get('/approvals/{request}/edit-assignment', [ApprovalController::class, 'editAssignment'])
                ->name('approvals.edit-assignment');
            Route::post('/approvals/{request}/edit-assignment', [ApprovalController::class, 'updateAssignment'])
                ->name('approvals.update-assignment');
        });

        /* ---------------- Deliveries ---------------- */
        Route::middleware('permission:view-requests')->group(function () {
            // Top-level deliveries page listing all approved requests
            Route::get('/deliveries', [DeliveryController::class, 'allDeliveries'])
                ->name('deliveries.all');
            Route::get('/requests/{request}/deliveries', [DeliveryController::class, 'index'])
                ->name('deliveries.index');
            Route::get('/deliveries/{delivery}', [DeliveryController::class, 'show'])
                ->name('deliveries.show');
        });

        Route::middleware('permission:record-deliveries')->group(function () {
            Route::get('/requests/{request}/deliveries/create', [DeliveryController::class, 'create'])
                ->name('deliveries.create');
            Route::post('/requests/{request}/deliveries', [DeliveryController::class, 'store'])
                ->name('deliveries.store');
        });

        Route::middleware('permission:verify-deliveries')->group(function () {
            Route::get('/deliveries/{delivery}/verify', [DeliveryController::class, 'verify'])
                ->name('deliveries.verify');
            Route::post('/deliveries/{delivery}/verify', [DeliveryController::class, 'processVerification'])
                ->name('deliveries.process-verification');
        });

        Route::middleware('permission:record-deliveries')->group(function () {
            Route::delete('/deliveries/{delivery}/attachment', [DeliveryController::class, 'deleteAttachment'])
                ->name('deliveries.delete-attachment');
        });

        Route::middleware('permission:verify-deliveries')->group(function () {
            Route::get('/deliveries/{delivery}/update-status', [DeliveryController::class, 'showUpdateStatus'])
                ->name('deliveries.update-status');
            Route::post('/deliveries/{delivery}/update-status', [DeliveryController::class, 'updateStatus'])
                ->name('deliveries.update-status.store');
        });

        /* ---------------- Reports (PDF Generation) ---------------- */
        Route::middleware('permission:view-procurement-reports')->group(function () {
            // Reports dashboard
            Route::get('/reports', [ReportController::class, 'index'])
                ->name('reports.index');
        });

        Route::middleware('permission:view-requests')->group(function () {
            // Request detail report
            Route::get('/reports/requests/{request}/detail', [ReportController::class, 'requestDetail'])
                ->name('reports.request-detail');

            // Delivery receipt
            Route::get('/reports/deliveries/{delivery}/receipt', [ReportController::class, 'deliveryReceipt'])
                ->name('reports.delivery-receipt');
        });

        Route::middleware('permission:view-projects')->group(function () {
            // Project summary report
            Route::get('/reports/projects/{project}/summary', [ReportController::class, 'projectSummary'])
                ->name('reports.project-summary');
        });

        Route::middleware('permission:view-projects')->group(function () {
            // Vendor transaction report
            Route::get('/reports/vendors/{vendor}/transactions', [ReportController::class, 'vendorTransactions'])
                ->name('reports.vendor-transactions');
        });

        /* ---------------- Notifications ---------------- */
        Route::get('/notifications', [NotificationController::class, 'page'])
            ->name('notifications');
    });

    // API routes (JSON responses, no PPMS prefix)
    Route::prefix('api')->group(function () {
        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])
            ->name('api.notifications.index');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
            ->name('api.notifications.read');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
            ->name('api.notifications.read-all');
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])
            ->name('api.notifications.destroy');

        // Materials search for cart
        Route::get('/materials/search', [MaterialController::class, 'search'])
            ->name('api.materials.search');

        // Get LGAs by state
        Route::get('/states/{state}/lgas', [ProjectController::class, 'getLgas'])
            ->name('api.states.lgas');

        Route::get('/vendors/{vendor}/materials/{material}/price', [VendorController::class, 'getMaterialPrice'])
            ->name('api.vendors.material-price');
    });
});
