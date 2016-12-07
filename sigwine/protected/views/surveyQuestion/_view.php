<?php
/* @var $this SurveyQuestionController */
/* @var $data SurveyQuestion */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('survey_question_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->survey_question_id), array('view', 'id'=>$data->survey_question_id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('survey_id')); ?>:</b>
	<?php echo CHtml::encode($data->survey_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('question')); ?>:</b>
	<?php echo CHtml::encode($data->question); ?>
	<br />


</div>