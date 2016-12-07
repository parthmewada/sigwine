<?php
/* @var $this ResOrderTotalController */
/* @var $model ResOrderTotal */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'order_total_id'); ?>
		<?php echo $form->textField($model,'order_total_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'user_id'); ?>
		<?php echo $form->textField($model,'user_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'order_total'); ?>
		<?php echo $form->textField($model,'order_total'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'payment_status'); ?>
		<?php echo $form->textField($model,'payment_status'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'order_status'); ?>
		<?php echo $form->textField($model,'order_status'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'payment_response'); ?>
		<?php echo $form->textField($model,'payment_response'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'order_createdby'); ?>
		<?php echo $form->textField($model,'order_createdby'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'order_createdon'); ?>
		<?php echo $form->textField($model,'order_createdon'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->