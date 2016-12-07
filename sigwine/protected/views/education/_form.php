<?php
/* @var $this EducationController */
/* @var $model Education */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'education-form',
	'enableAjaxValidation'=>false,
        'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>
	<p class="note">Fields with <span class="required">*</span> are required.</p>
	<?php echo $form->errorSummary($model); ?>
	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>
        <div class="row">
		<?php echo $form->labelEx($model,'title_cn'); ?>
		<?php echo $form->textField($model,'title_cn',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'title_cn'); ?>
	</div>
        <div class="row">
		<?php echo $form->labelEx($model,'upload_type'); ?>
                <select name="Education[upload_type]" id="upload_type">
                    <option value="">Please select</option>
                    <option value="text" <?php echo ($model->upload_type=='text'?"selected='selected'":""); ?>>Text</option>
                    <option value="audio" <?php echo ($model->upload_type=='audio'?"selected='selected'":""); ?>>Audio</option>
                    <option value="video" <?php echo ($model->upload_type=='video'?"selected='selected'":""); ?>>Video</option>
                    <option value="doc" <?php echo ($model->upload_type=='doc'?"selected='selected'":""); ?>>Document</option>
                </select>
            <!--<?php echo $form->textField($model,'upload_type',array('size'=>60,'maxlength'=>255)); ?>-->
            <?php echo $form->error($model,'upload_type'); ?>
	</div>
        <div class="row">
            <?php echo $form->labelEx($model, 'thumb'); ?>
            <?php echo $form->fileField($model, 'thumb'); ?>
            <?php echo $form->error($model, 'thumb'); ?>
        </div>
        <?php if ($model->thumb != '') { ?>
            <div class="row">
                <label for="">Product Existing Image</label>
                <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/education/thumb/<?php echo $model->thumb; ?>" height="40" width="40"/>
                <input type="hidden" name="oldthumb"  value="<?php echo $model->thumb; ?>">
            </div>
        <?php } else { ?>
            <input type="hidden" name="oldthumb"  value="">
        <?php } ?>
        <div class="row">
            <?php echo $form->labelEx($model, 'file'); ?>
            <?php echo $form->fileField($model, 'file'); ?>
            <?php echo $form->error($model, 'file'); ?>
        </div>
        <?php if ($model->file != '') { ?>
            <div class="row">
                <label for="">Existing File</label>
                <?php if($model->upload_type=='audio'){ ?>
                    <audio controls>
                      <source src="<?php echo Yii::app()->request->baseUrl; ?>/images/education/<?php echo $model->file; ?>" type="audio/ogg">
                    </audio
                <?php }elseif ($model->upload_type=='video') {?>
                     <video width="220" height="180" controls>
                        <source src="<?php echo Yii::app()->request->baseUrl; ?>/images/education/<?php echo $model->file; ?>" >
                      </video> 
       
                <?php } else{?>
                     <a href="<?php echo Yii::app()->request->baseUrl; ?>/images/education/<?php echo $model->file; ?>" target="_blank"/><?php echo $model->file; ?></a> 
                <?php } ?>
                
                <input type="hidden" name="oldimage"  value="<?php echo $model->file; ?>">
            </div>
        <?php } else { ?>
            <input type="hidden" name="oldimage"  value="">
            <?php } ?>
	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model,'description',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>
        <div class="row">
		<?php echo $form->labelEx($model,'description_cn'); ?>
		<?php echo $form->textArea($model,'description_cn',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'description_cn'); ?>
	</div>
<!--	<div class="row">
		<?php echo $form->labelEx($model,'display_order'); ?>
		<?php echo $form->textField($model,'display_order'); ?>
		<?php echo $form->error($model,'display_order'); ?>
	</div>-->

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

