<?php
/* @var $this ResOrderTotalController */
/* @var $model ResOrderTotal */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'res-order-total-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'user_id'); ?>
		<?php echo $form->textField($model,'user_id'); ?>
		<?php echo $form->error($model,'user_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'order_total'); ?>
		<?php echo $form->textField($model,'order_total'); ?>
		<?php echo $form->error($model,'order_total'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'payment_status'); ?>
		<?php echo $form->textField($model,'payment_status'); ?>
		<?php echo $form->error($model,'payment_status'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'order_status'); ?>
		<?php echo $form->textField($model,'order_status'); ?>
		<?php echo $form->error($model,'order_status'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'payment_response'); ?>
		<?php echo $form->textField($model,'payment_response'); ?>
		<?php echo $form->error($model,'payment_response'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'order_createdby'); ?>
		<?php echo $form->textField($model,'order_createdby'); ?>
		<?php echo $form->error($model,'order_createdby'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'order_createdon'); ?>
		<?php echo $form->textField($model,'order_createdon'); ?>
		<?php echo $form->error($model,'order_createdon'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->