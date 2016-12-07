<?php
/* @var $this SubscriptionPlanDetailController */
/* @var $model SubscriptionPlanDetail */
/* @var $form CActiveForm */
?>
<div class="form">
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'subscription-plan-detail-form',
    'enableAjaxValidation' => false,
));
?>
<p class="note">Fields with <span class="required">*</span> are required.</p>
<?php echo $form->errorSummary($model); ?>
<div class="row">
    <?php echo $form->labelEx($model, 'Select Month'); ?>
    <?php
    echo CHtml::activeDropDownList($model, 'month', array("" => "Please select", "January" => "January",
        "February" => "February",
        "March" => "March",
        "April" => "April",
        "May" => "May",
        "June" => "June",
        "July" => "July",
        "August" => "August",
        "September" => "September",
        "October" => "October",
        "November" => "November",
        "December" => "December"
    ));
    ?>
    <?php echo $form->error($model, 'month'); ?>
</div>
<div class="row">
    <?php echo $form->labelEx($model, 'Enter Product'); ?>
    <br/>
<?php 
    $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                'attribute'=>'product_id',
                'model'=>$model,
                'sourceUrl'=>array('subscriptionPlanDetail/List'),
                'name'=>'product_id',
                'options'=>array(
                'minLength'=>'3',
        ),
        'htmlOptions'=>array(
          'size'=>45,
          'maxlength'=>45,
        ),
  ));
?>
</div>
<div>
// Checkbox list
</div>
<div class="row">
<?php echo $form->labelEx($model, 'Select Plan'); ?>
<?php echo CHtml::activeDropDownList($model, 'plan_id', CHtml::listData(SubscriptionPlan::model()->findAll(array('condition' => "status = 'Active'")), 'plan_id', 'plan_name'), array('empty' => 'Select Plan')); ?>
<?php echo $form->error($model, 'plan_id'); ?>
</div><!--
    <?php if ($model->isNewRecord) { ?>
    <div class="row">
     <?php echo $form->labelEx($model, 'Select Product'); ?>
    <?php echo CHtml::activeDropDownList($model, 'product_id[]', CHtml::listData(Product::model()->findAll(array('condition' => "product_type='wine' and status = 'Active' and flash_sale_product='no'")), 'product_id', 'product_name'), array('empty' => 'Select Product', "multiple" => "multiple")); ?>
    <?php echo $form->error($model, 'product_id'); ?>
    </div>
    <?php } else { ?>
    <div class="row">
    <?php echo $form->labelEx($model, 'Select Product'); ?>
    <?php echo CHtml::activeDropDownList($model, 'product_id', CHtml::listData(Product::model()->findAll(array('condition' => "product_type='wine' and status = 'Active' and flash_sale_product='no'")), 'product_id', 'product_name'), array('empty' => 'Select Product')); ?>
    <?php echo $form->error($model, 'product_id'); ?>
    </div>
<?php } ?>-->

<!--<div class="row">
<?php echo $form->labelEx($model, 'Display Order'); ?>
<?php echo $form->textField($model, 'display_order'); ?>
<?php echo $form->error($model, 'display_order'); ?>
</div>-->

<div class="row buttons">
<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
</div>

<?php $this->endWidget(); ?>

</div><!-- form -->