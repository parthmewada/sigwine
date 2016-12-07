<?php
/* @var $this NotificationController */
/* @var $model Notification */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'discount-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>
	<?php  if($model->isNewRecord) { ?>
	<div class="row">
		<?php echo $form->labelEx($model,'discount_perc'); ?>
		<?php echo $form->textField($model,'discount_perc'); ?>
		<?php echo $form->error($model,'discount_perc'); ?>
	</div>
	<?php } else { ?>
		<div class="row">
		<?php echo $form->labelEx($model,'discount_perc'); ?>
		<?php echo $form->textField($model,'discount_perc'); ?>
		<?php echo $form->error($model,'discount_perc'); ?>
	</div>
	<?php } ?>
        <div class="row">
            <?php echo $form->labelEx($model, 'status',array('label'=>'Status')); ?>

            <?php echo CHtml::activeDropDownList($model, 'status', array('Active' => 'Active', 'Inactive' => 'InActive')); ?>
            <?php echo $form->error($model, 'status'); ?>
        </div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
