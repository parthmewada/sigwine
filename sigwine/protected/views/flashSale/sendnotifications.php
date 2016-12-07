<?php
/* @var $this FlashSaleController */
/* @var $model FlashSale */

$this->breadcrumbs=array(
	'Exclusive'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Exclusive', 'url'=>array('index')),
	array('label'=>'Create Exclusive', 'url'=>array('create')),
);


?>

<h1>Send PushNotification</h1>


<div class="view">

	<b><?php echo CHtml::encode($model->getAttributeLabel('flash_sale_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($model->flash_sale_id), array('view', 'id'=>$model->flash_sale_id)); ?>
	<br />

	<b><?php echo CHtml::encode($model->getAttributeLabel('title')); ?>:</b>
	<?php echo CHtml::encode($model->title); ?>
	<br />

	<b><?php echo CHtml::encode($model->getAttributeLabel('product_id')); ?>:</b>
	<?php echo CHtml::encode($model->product_id); ?>
	<br />

	<b><?php echo CHtml::encode($model->getAttributeLabel('sale_start_from')); ?>:</b>
	<?php echo CHtml::encode($model->sale_start_from); ?>
	<br />

	<b><?php echo CHtml::encode($model->getAttributeLabel('sale_end')); ?>:</b>
	<?php echo CHtml::encode($model->sale_end); ?>
	<br />

	<b><?php echo CHtml::encode($model->getAttributeLabel('status')); ?>:</b>
	<?php echo CHtml::encode($model->status); ?>
	<br />


</div>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'send-ios-notifications',
        'action' => Yii::app()->createUrl('flashSale/sendiosnotifications', array("id"=>$model->flash_sale_id)),
	'enableAjaxValidation'=>false,
    
)); ?>

<div class="row buttons">
    <?php echo CHtml::submitButton('Send IOS Notification'); ?>
</div>

<?php $this->endWidget(); ?>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'send-anroid-notifications',
        'action' => Yii::app()->createUrl('flashSale/sendanroidnotifications',array("id"=>$model->flash_sale_id)),
	'enableAjaxValidation'=>false,
)); ?>

<div class="row buttons">
 <?php echo CHtml::submitButton('Send Anroid Notification'); ?>
</div>

<?php $this->endWidget(); ?>