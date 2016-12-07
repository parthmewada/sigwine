<?php
/* @var $this SurveyController */
/* @var $model Survey */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'survey_id'); ?>
		<?php echo $form->textField($model,'survey_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'survey_name'); ?>
		<?php echo $form->textField($model,'survey_name',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'survey_start'); ?>
		<?php echo $form->textField($model,'survey_start'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'survey_end'); ?>
		<?php echo $form->textField($model,'survey_end'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'status'); ?>
		<?php echo $form->textField($model,'status',array('size'=>8,'maxlength'=>8)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'created_on'); ?>
		<?php echo $form->textField($model,'created_on'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->