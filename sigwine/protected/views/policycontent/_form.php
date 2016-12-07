<?php
/* @var $this PolicycontentController */
/* @var $model Policycontent */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'policycontent-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'policy_title'); ?>
		<?php echo $form->textField($model,'policy_title',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'policy_title'); ?>
	</div>

	<div class="row" style="display: none;">
		<label class="required" for="Policycontent_policy_code">Content Code <span class="required">*<br/><span style="font-size: 12px;">Do not change Content Code</span></span></label>
                 <?php echo $form->textField($model,'policy_code',array('size'=>60,'maxlength'=>255,'readonly'=>(!$model->isNewRecord?'true':''))); ?>
		<?php echo $form->error($model,'policy_code'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'policy_description'); ?><br/><br/>
		<?php echo $form->textArea($model,'policy_description',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'policy_description'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<script src="<?php echo Yii::app()->getBaseUrl(true); ?>/lib/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
    CKEDITOR.replace('Policycontent[policy_description]', {
        filebrowserBrowseUrl: '<?php echo Yii::app()->getBaseUrl(true); ?>/lib/kcfinder/browse.php?type=files',
        filebrowserImageBrowseUrl: '<?php echo Yii::app()->getBaseUrl(true); ?>/lib/kcfinder/browse.php?type=images',
        filebrowserFlashBrowseUrl: '<?php echo Yii::app()->getBaseUrl(true); ?>/lib/kcfinder/browse.php?type=flash',
        filebrowserUploadUrl: '<?php echo Yii::app()->getBaseUrl(true); ?>/lib/kcfinder/upload.php?type=files',
        filebrowserImageUploadUrl: '<?php echo Yii::app()->getBaseUrl(true); ?>/lib/kcfinder/upload.php?type=images',
        filebrowserFlashUploadUrl: '<?php echo Yii::app()->getBaseUrl(true); ?>/lib/kcfinder/upload.php?type=flash'
    });
    var editor = CKEDITOR.instances.long_desc;

    editor.on('instanceReady', function()
    {
        var writer = editor.dataProcessor.writer;
        writer.indentationChars = '';
        writer.lineBreakChars = '';
    });
</script>