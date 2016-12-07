<?php
/* @var $this DiscountController */
/* @var $data Discount */
?>

<div class="view">

    <b><?php echo CHtml::encode($data->getAttributeLabel('Discount ID')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->discount_id), array('view', 'id'=>$data->discount_id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Percentage')); ?>:</b>
	<?php echo CHtml::encode($data->discount_perc); ?>
	<br />
</div>
