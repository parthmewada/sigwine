<?php
/* @var $this OrdersController */
/* @var $model Orders */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'orders-form',
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
		<?php echo $form->labelEx($model,'subscription_plan_id'); ?>
		<?php echo $form->textField($model,'subscription_plan_id'); ?>
		<?php echo $form->error($model,'subscription_plan_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'subscription_duration'); ?>
		<?php echo $form->textField($model,'subscription_duration',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'subscription_duration'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'order_total'); ?>
		<?php echo $form->textField($model,'order_total',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'order_total'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'order_qty'); ?>
		<?php echo $form->textField($model,'order_qty'); ?>
		<?php echo $form->error($model,'order_qty'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'type'); ?>
		<?php echo $form->textField($model,'type',array('size'=>1,'maxlength'=>1)); ?>
		<?php echo $form->error($model,'type'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'order_status'); ?>
		<?php echo $form->textField($model,'order_status',array('size'=>7,'maxlength'=>7)); ?>
		<?php echo $form->error($model,'order_status'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'order_date'); ?>
		<?php echo $form->textField($model,'order_date'); ?>
		<?php echo $form->error($model,'order_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'order_createdby'); ?>
		<?php echo $form->textField($model,'order_createdby'); ?>
		<?php echo $form->error($model,'order_createdby'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'address_id'); ?>
		<?php echo $form->textField($model,'address_id'); ?>
		<?php echo $form->error($model,'address_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'payment_method'); ?>
		<?php echo $form->textField($model,'payment_method',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'payment_method'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->