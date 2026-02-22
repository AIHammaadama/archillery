<?php $__env->startSection("title"); ?>
Login | PPMS
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid min-vh-100 p-0">
    <div class="row min-vh-100 g-0 login-bg">
        
        <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center">
            <div class="text-center px-5">
                <br><br><br>
                <img src="<?php echo e(asset('/images/logo-login.png')); ?>" style="width: 500px;">
            </div>
        </div>

        
        <div class="col-lg-6 d-flex align-items-center justify-content-center bg-info-light ">
            <div class="w-100" style="max-width: 400px;">
                <h3 class="text-center text-white mb-4" style="margin-top: 100px;">Welcome back</h3>
                <p class="text-danger text-center">Sign In to PPMS - Built for efficiency, transparency, and control.
                </p>
                <?php if($msg = Session::get('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo e($msg); ?>

                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                <?php endif; ?>

                <div class="card shadow-sm login-card">
                    <div class="card-body">
                        <form method="POST" action="<?php echo e(route('login')); ?>">
                            <?php echo csrf_field(); ?>

                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    name="email" value="<?php echo e(old('email')); ?>" placeholder="Enter your email" required
                                    autofocus>
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    name="password" placeholder="Enter your password" required>
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="mb-3 text-end">
                                <?php if(Route::has('password.request')): ?>
                                <a href="<?php echo e(route('password.request')); ?>" class="text-danger" style="font-size: 14px;">
                                    Forgot password?
                                </a>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="btn btn-secondary btn-lg w-100">
                                Login
                            </button>
                        </form>
                    </div>
                </div>
                <small class="text-dark text-center d-block mt-3 mb-3">
                    Secure access • Authorized personnel only
                </small>
                <small class="text-dark text-center d-block mb-2">
                    &copy; <?php echo e(date("Y")); ?>. Archillery Build Project & Procurement Management System. All rights
                    reserved.
                </small>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.guest', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Ahmadx/Downloads/archillery/resources/views/auth/login.blade.php ENDPATH**/ ?>