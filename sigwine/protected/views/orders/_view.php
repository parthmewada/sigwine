<?php
/* @var $this OrdersController */
/* @var $data Orders */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('order_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->order_id), array('view', 'id'=>$data->order_id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('user_id')); ?>:</b>
	<?php echo CHtml::encode($data->user_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('subscription_plan_id')); ?>:</b>
	<?php echo CHtml::encode($data->subscription_plan_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('subscription_duration')); ?>:</b>
	<?php echo CHtml::encode($data->subscription_duration); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('order_total')); ?>:</b>
	<?php echo CHtml::encode($data->order_total); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('order_qty')); ?>:</b>
	<?php echo CHtml::encode($data->order_qty); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('type')); ?>:</b>
	<?php echo CHtml::encode($data->type); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('order_status')); ?>:</b>
	<?php echo CHtml::encode($data->order_status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('order_date')); ?>:</b>
	<?php echo CHtml::encode($data->order_date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('order_createdby')); ?>:</b>
	<?php echo CHtml::encode($data->order_createdby); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('address_id')); ?>:</b>
	<?php echo CHtml::encode($data->address_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('payment_method')); ?>:</b>
	<?php echo CHtml::encode($data->payment_method); ?>
	<br />

	*/ ?>

</div>