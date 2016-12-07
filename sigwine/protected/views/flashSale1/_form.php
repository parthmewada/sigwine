<link href="<?php echo Yii::app()->getBaseUrl(true); ?>/js/jquery-ui-timepicker-addon.css" rel="stylesheet" />
<script src="<?php echo Yii::app()->getBaseUrl(true); ?>/js/jquery-ui-timepicker-addon.js"></script>
<script src="<?php echo Yii::app()->getBaseUrl(true); ?>/js/form-extended.js"></script>
<?php
/* @var $this FlashSaleController */
/* @var $model FlashSale */
/* @var $form CActiveForm */
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'flash-sale-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'product_id'); ?>
                <?php echo CHtml::activeDropDownList($model, 'product_id', CHtml::listData(Product::model()->findAll(array('condition' => "status = 'Active'")), 'product_id', 'product_name'), array('empty' => 'Select Product')); ?>
		<?php echo $form->error($model,'product_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'sale_start_from'); ?>
		 <div data-date-autoclose="true" data-date-format="yyyy-mm-dd" data-date="<?php echo date("Y-m-d");?>" data-auto-close="true" class="input-group date" id="dp-ex-1">
                 <?php echo $form->textField($model, 'sale_start_from'); ?>
                 <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                 </div>
		<?php echo $form->error($model,'sale_start_from'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'sale_end'); ?>
		<div data-date-autoclose="true" data-date-format="yyyy-mm-dd" data-date="<?php echo date("Y-m-d");?>" data-auto-close="true" class="input-group date" id="dp-ex-2">
                 <?php echo $form->textField($model,'sale_end'); ?>
                 <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
		<?php echo $form->error($model,'sale_end'); ?>
	</div>
	<div class="row">
            <?php echo $form->labelEx($model, 'status'); ?>
            <?php echo CHtml::activeDropDownList($model, 'status', array('Active' => 'Active', 'InActive' => 'InActive')); ?>
            <?php echo $form->error($model, 'status'); ?>
        </div>
        <div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->