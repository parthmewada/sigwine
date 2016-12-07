<?php
/* @var $this SubscriptionPlanDetailController */
/* @var $data SubscriptionPlanDetail */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('detail_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->detail_id), array('view', 'id'=>$data->detail_id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('plan_id')); ?>:</b>
	<?php echo CHtml::encode($data->plan_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('product_id')); ?>:</b>
	<?php echo CHtml::encode($data->product_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('month')); ?>:</b>
	<?php echo CHtml::encode($data->month); ?>
	<br />


</div>