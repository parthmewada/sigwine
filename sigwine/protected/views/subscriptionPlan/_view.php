<?php
/* @var $this SubscriptionPlanController */
/* @var $data SubscriptionPlan */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('plan_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->plan_id), array('view', 'id'=>$data->plan_id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('plan_name')); ?>:</b>
	<?php echo CHtml::encode($data->plan_name); ?>
	<br />
        <b><?php echo CHtml::encode($data->getAttributeLabel('plan_type')); ?>:</b>
	<?php echo CHtml::encode($data->plan_type); ?>
	<br />
        <b><?php echo CHtml::encode($data->getAttributeLabel('image')); ?>:</b>
	<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/subscription/thumb/<?php echo $data->image; ?>" height="40" width="40"/>
	<br />
        <b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
	<?php echo CHtml::encode($data->description); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('plan_type')); ?>:</b>
	<?php echo CHtml::encode($data->plan_type); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('price')); ?>:</b>
	<?php echo CHtml::encode($data->price); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('status')); ?>:</b>
	<?php echo CHtml::encode($data->status); ?>
	<br />


</div>