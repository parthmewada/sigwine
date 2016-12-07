<?php
/* @var $this FlashSaleController */
/* @var $data FlashSale */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('flash_sale_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->flash_sale_id), array('view', 'id'=>$data->flash_sale_id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('title')); ?>:</b>
	<?php echo CHtml::encode($data->title); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('product_id')); ?>:</b>
	<?php echo CHtml::encode($data->product_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('sale_start_from')); ?>:</b>
	<?php echo CHtml::encode($data->sale_start_from); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('sale_end')); ?>:</b>
	<?php echo CHtml::encode($data->sale_end); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('status')); ?>:</b>
	<?php echo CHtml::encode($data->status); ?>
	<br />


</div>