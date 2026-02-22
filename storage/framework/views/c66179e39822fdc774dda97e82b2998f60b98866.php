<?php $__env->startSection("title"); ?>
Profile
<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>
<?php
$user = Auth::user();
?>
<div class="content-body">
    <div class="container-fluid">
        <div class="page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo e(route('profile')); ?>">Profile</a></li>
                <li class="breadcrumb-item active">Edit your information</li>
            </ol>
        </div>

        <?php if($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if(!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fa fa-check-circle me-2"></i>
            <?php echo e($success); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if($msg = Session::get('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fa fa-check-circle me-2"></i>
            <?php echo e($msg); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php elseif($msg = Session::get('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fa fa-exclamation-circle me-2"></i>
            <?php echo e($msg); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <form autocomplete="off" action="<?php echo e(route('profile.update')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo method_field('PATCH'); ?>
            <?php echo csrf_field(); ?>
            <div class="row">
                <!-- Profile Photo Card -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Profile Photo</h4>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <img id="profilePreview" <?php if(!empty($user->photo)): ?>
                                src="<?php echo e(asset('storage/profile/'.$user->photo)); ?>"
                                <?php else: ?>
                                src="https://www.pngfind.com/pngs/m/610-6104451_image-placeholder-png-user-profile-placeholder-image-png.png"
                                <?php endif; ?>
                                alt="Profile Image"
                                class="rounded-circle"
                                width="130"
                                height="130"
                                style="object-fit: cover;">
                            </div>
                            <h4 class="mb-1"><?php echo e($user->firstname.' '.$user->lastname); ?></h4>
                            <p class="text-muted mb-2"><?php echo e($user->email); ?></p>
                            <span class="badge bg-primary"><?php echo e(ucfirst($user->role->name)); ?></span>

                            <hr class="my-3">

                            <p class="text-muted small mb-2">Max Size: 2MB | Formats: JPEG, JPG, PNG</p>
                            <div class="mb-3">
                                <input type="file" name="photo" id="image" onchange="showPreview(event)"
                                    class="form-control form-control-sm">
                            </div>
                            <button onclick="clearProfilePic(event)" type="button"
                                class="btn btn-sm btn-outline-danger">
                                <i class="fa fa-trash me-1"></i> Remove Photo
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Personal Details Card -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Personal Details</h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="firstname" id="firstname" placeholder="First Name"
                                        value="<?php echo e(!empty($user->firstname) ? $user->firstname : old('firstname')); ?>"
                                        class="form-control" readonly>
                                    <?php $__errorArgs = ['firstname'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="lastname" id="lastname" placeholder="Surname"
                                        value="<?php echo e(!empty($user->lastname) ? $user->lastname : old('lastname')); ?>"
                                        class="form-control" readonly>
                                    <?php $__errorArgs = ['lastname'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Other Names</label>
                                    <input type="text" name="othername" id="othername" placeholder="Other Name"
                                        value="<?php echo e(!empty($user->othername) ? $user->othername : old('othername')); ?>"
                                        class="form-control">
                                    <?php $__errorArgs = ['othername'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" name="phone" id="phone" placeholder="Phone Number"
                                        value="<?php echo e(!empty($user->phone) ? $user->phone : old('phone')); ?>"
                                        class="form-control">
                                    <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" id="email" placeholder="Email"
                                        value="<?php echo e(!empty($user->email) ? $user->email : old('email')); ?>"
                                        class="form-control">
                                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Change Password Card -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Change Password</h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label"><?php echo e(__('New Password')); ?></label>
                                    <input type="password" name="password" id="password"
                                        placeholder="Enter new password"
                                        class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        autocomplete="new-password">
                                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><?php echo e(__('Confirm Password')); ?></label>
                                    <input type="password" name="password_confirmation"
                                        placeholder="Confirm new password"
                                        class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        autocomplete="new-password">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="d-flex justify-content-end gap-2 mb-2">
                        <?php if($user->nin_verified != 1): ?>
                        <button type="submit" class="btn btn-primary btn-md">
                            <i class="fa fa-save me-1"></i> Save Changes
                        </button>
                        <?php endif; ?>
                        <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-outline-danger btn-md">
                            <i class="fa fa-times me-1"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function showPreview(event) {
    if (event.target.files.length > 0) {
        const src = URL.createObjectURL(event.target.files[0]);
        const preview = document.getElementById("profilePreview");
        preview.src = src;
    }
}

function clearProfilePic(event) {
    event.preventDefault();

    document.getElementById('image').value = '';

    document.getElementById("profilePreview").src =
        'https://www.pngfind.com/pngs/m/610-6104451_image-placeholder-png-user-profile-placeholder-image-png.png';
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Ahmadx/Downloads/archillery/resources/views/dashboard/profile.blade.php ENDPATH**/ ?>