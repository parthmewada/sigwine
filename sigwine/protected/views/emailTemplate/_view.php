<?php
/* @var $this EmailTemplateController */
/* @var $data EmailTemplate */
?>

<div class="view">
    <b><?php echo CHtml::encode($data->getAttributeLabel('template_id')); ?>:</b>
    <?php echo CHtml::link(CHtml::encode($data->template_id), array('view', 'id'=>$data->template_id)); ?>
    <br />
    <b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
    <?php echo CHtml::encode($data->description); ?>
    <br />
    <b><?php echo CHtml::encode($data->getAttributeLabel('template_code')); ?>:</b>
    <?php echo CHtml::encode($data->template_code); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('email_text')); ?>:</b>
    <?php echo CHtml::encode($data->email_text); ?>
    <br />
</div>