<?php
/* @var $this SurveyController */
/* @var $data Survey */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('survey_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->survey_id), array('view', 'id'=>$data->survey_id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('survey_name')); ?>:</b>
	<?php echo CHtml::encode($data->survey_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('survey_start')); ?>:</b>
	<?php echo CHtml::encode($data->survey_start); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('survey_end')); ?>:</b>
	<?php echo CHtml::encode($data->survey_end); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('status')); ?>:</b>
	<?php echo CHtml::encode($data->status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('created_on')); ?>:</b>
	<?php echo CHtml::encode($data->created_on); ?>
	<br />


</div>