<?php
/* @var $this UsersController */
/* @var $model Users */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'users-form',
	'enableAjaxValidation'=>false,
        'htmlOptions' => array('enctype' => 'multipart/form-data')
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>
        <div class="row">
		<?php echo $form->labelEx($model,'first_name'); ?>
		<?php echo $form->textField($model,'first_name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'first_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'last_name'); ?>
		<?php echo $form->textField($model,'last_name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'last_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->textField($model,'password',array('size'=>60,'maxlength'=>255,"value"=>base64_decode($model->password))); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<!--<div class="row">
		<?php echo $form->labelEx($model,'login_type'); ?>
		<?php echo $form->textField($model,'login_type'); ?>
		<?php echo $form->error($model,'login_type'); ?>
	</div>
        -->
	<div class="row">
		<?php echo $form->labelEx($model,'profile_image'); ?>
		<?php echo $form->fileField($model,'profile_image'); ?>
		<?php echo $form->error($model,'profile_image'); ?>
	</div>
        <?php if ($model->profile_image!='') { ?>
            <div class="row">
                <label for="">User Existing Image</label>
                <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/profile/thumb/<?php echo $model->profile_image; ?>" height="40" width="40"/>
                <input type="hidden" name="oldimage"  value="<?php echo $model->profile_image; ?>">
           </div>
        <?php }else{ ?>
            <input type="hidden" name="oldimage"  value="">
        <?php }?>
	<div class="row">
		<?php echo $form->labelEx($model,'language_preference'); ?>
                <?php echo CHtml::activeDropDownList($model, 'language_preference',array('en' => 'English', 'cn' => 'Chinese'));?>
		<?php echo $form->error($model,'language_preference'); ?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->