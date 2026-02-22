<?php $__env->startComponent('mail::message'); ?>
# Delivery Recorded

A new delivery has been recorded for your project.

<?php $__env->startComponent('mail::panel'); ?>
**Request Number:** <?php echo e($delivery->request->request_number); ?>  
**Project:** <?php echo e($delivery->request->project->name); ?>  
**Material:** <?php echo e($delivery->requestItem->material->name); ?>  
**Quantity Delivered:** <?php echo e(number_format($delivery->quantity_delivered, 2)); ?> <?php echo e($delivery->requestItem->material->unit_of_measurement); ?>  
**Delivery Date:** <?php echo e($delivery->delivery_date->format('M d, Y')); ?>

<?php echo $__env->renderComponent(); ?>

<?php if($delivery->waybill_number): ?>
**Waybill Number:** <?php echo e($delivery->waybill_number); ?>

<?php endif; ?>

<?php if($delivery->invoice_number): ?>
**Invoice Number:** <?php echo e($delivery->invoice_number); ?>

<?php endif; ?>

<?php if($delivery->quality_notes): ?>
**Quality Notes:**  
<?php echo e($delivery->quality_notes); ?>

<?php endif; ?>

<?php $__env->startComponent('mail::button', ['url' => route('deliveries.show', $delivery)]); ?>
View Delivery Details
<?php echo $__env->renderComponent(); ?>

Best regards,  
<?php echo e(config('app.name')); ?>

<?php echo $__env->renderComponent(); ?>
<?php /**PATH /Users/Ahmadx/Downloads/archillery/resources/views/emails/delivery-recorded.blade.php ENDPATH**/ ?>