<?php
/* @var $this PolicycontentController */
/* @var $data Policycontent */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('policycontent_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->policycontent_id), array('view', 'id'=>$data->policycontent_id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('policy_title')); ?>:</b>
	<?php echo CHtml::encode($data->policy_title); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('policy_code')); ?>:</b>
	<?php echo CHtml::encode($data->policy_code); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('policy_description')); ?>:</b>
	<?php echo CHtml::encode($data->policy_description); ?>
	<br />


</div>