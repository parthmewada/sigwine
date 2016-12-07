<?php
/* @var $this ResOrderTotalController */
/* @var $data ResOrderTotal */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('order_total_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->order_total_id), array('view', 'id'=>$data->order_total_id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('user_id')); ?>:</b>
	<?php echo CHtml::encode($data->user_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('order_total')); ?>:</b>
	<?php echo CHtml::encode($data->order_total); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('payment_status')); ?>:</b>
	<?php echo CHtml::encode($data->payment_status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('order_status')); ?>:</b>
	<?php echo CHtml::encode($data->order_status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('payment_response')); ?>:</b>
	<?php echo CHtml::encode($data->payment_response); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('order_createdby')); ?>:</b>
	<?php echo CHtml::encode($data->order_createdby); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('order_createdon')); ?>:</b>
	<?php echo CHtml::encode($data->order_createdon); ?>
	<br />

	*/ ?>
</div>