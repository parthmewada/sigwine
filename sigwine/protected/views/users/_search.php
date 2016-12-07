<?php
/* @var $this UsersController */
/* @var $model Users */
/* @var $form CActiveForm */
?>

<div class="wide form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
<div class="row">
        <?php echo $form->label($model,'email'); ?>
        <?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>255)); ?>
</div>
<div class="row">
        <?php echo $form->label($model,'first_name'); ?>
        <?php echo $form->textField($model,'first_name',array('size'=>60,'maxlength'=>255)); ?>
</div>
<div class="row">
        <?php echo $form->label($model,'last_name'); ?>
        <?php echo $form->textField($model,'last_name',array('size'=>60,'maxlength'=>255)); ?>
</div>
<div class="row">
        <?php echo CHtml::submitButton('Search'); ?>
</div>
<?php $this->endWidget(); ?>
</div><!-- search-form -->