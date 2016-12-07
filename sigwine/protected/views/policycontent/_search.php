<?php
/* @var $this PolicycontentController */
/* @var $model Policycontent */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'policycontent_id'); ?>
		<?php echo $form->textField($model,'policycontent_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'policy_title'); ?>
		<?php echo $form->textField($model,'policy_title',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'policy_code'); ?>
		<?php echo $form->textField($model,'policy_code',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'policy_description'); ?>
		<?php echo $form->textArea($model,'policy_description',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->