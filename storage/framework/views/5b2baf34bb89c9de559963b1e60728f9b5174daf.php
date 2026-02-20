<?php $__env->startComponent('mail::message'); ?>
# New Project Assignment

Hello,

You have been assigned as **<?php echo e(ucwords(str_replace('_', ' ', $roleType))); ?>** for the following project:

<?php $__env->startComponent('mail::panel'); ?>
**Project:** <?php echo e($project->name); ?>  
**Code:** <?php echo e($project->code); ?>  
**Status:** <?php echo e(ucfirst($project->status)); ?>

<?php if($project->start_date): ?>  
**Start Date:** <?php echo e($project->start_date->format('M d, Y')); ?>

<?php endif; ?>
<?php echo $__env->renderComponent(); ?>

<?php if($project->description): ?>
**Description:**  
<?php echo e($project->description); ?>

<?php endif; ?>

<?php $__env->startComponent('mail::button', ['url' => route('projects.show', $project)]); ?>
View Project Details
<?php echo $__env->renderComponent(); ?>

Thank you for your service!

Best regards,  
<?php echo e(config('app.name')); ?>

<?php echo $__env->renderComponent(); ?>
<?php /**PATH /Users/Ahmadx/Downloads/ppms/resources/views/emails/project-assignment.blade.php ENDPATH**/ ?>