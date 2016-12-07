<?php
/* @var $this ProductController */
/* @var $model Product */
/* @var $form CActiveForm */
?>
<div class="form">
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'product-form',
    'enableAjaxValidation' => false,
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
));
?>
<p class="note">Fields with <span class="required">*</span> are required.</p>
<?php echo $form->errorSummary($model); ?>
<div class="row">
    <?php echo $form->labelEx($model, 'product_type'); ?>
<?php echo CHtml::activeDropDownList($model, 'product_type', array('wine' => 'Wine', 'glassware' => 'Glassware'),array('onchange'=>'changeDropdown(this.value)')); ?>
<?php echo $form->error($model, 'product_type'); ?>
</div>
<?php if($model->isNewRecord){?>
<div class="row hideGlass">
    <?php // echo $form->labelEx($model, 'Select Month'); ?>
    <label for="SubscriptionPlan_Select_Month">Select  Month</label>
    <?php
        $month= array("" => "Please select",
                    "January" => "January",
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
                    "December" => "December",
		    "Top Selling Wine" => "Top Selling Wine");
    ?>
    <select name="month" id="month">
            <?php foreach($month as $m=>$v){ ?>
            <option value="<?php echo $m; ?>"><?php echo $v; ?></option>
            <?php } ?>
    </select>
    <?php 
        $year=date('Y');
        $yearArr=array("" => "Please select",
                    $year-1=>$year-1,
                    $year=>$year,
                    $year+1=>$year+1,
                    $year+2=>$year+2,
                    $year+3=>$year+3,
                    $year+4=>$year+4,

                ); ?>
    &nbsp;&nbsp;Year 
    <select name="year" id="year">
            <?php foreach($yearArr as $m=>$v){ ?>
            <option value="<?php echo $m; ?>"><?php echo $v; ?></option>
            <?php } ?>
    </select>
    <?php // echo $form->error($model, 'month'); ?>
</div>
<div class="row hideGlass">
 <?php echo $form->labelEx($model1, 'Select Plan'); ?>  
 <?php $list = CHtml::listData(SubscriptionPlan::model()->findAll(array('condition' => "status = 'Active'")), 'plan_id', 'plan_name');?>
 <?php echo $form->dropDownList($model1, 'plan_id[]', $list, array('empty' => 'Select Plan', 'class' => '',"multiple"=>"multiple"));?>
 <?php echo $form->error($model1, 'plan_id[]'); ?>
</div>
<?php } ?>
<div class="row">
<?php echo $form->labelEx($model, 'product_name'); ?>
<?php echo $form->textField($model, 'product_name', array('size' => 60, 'maxlength' => 255)); ?>
<?php echo $form->error($model, 'product_name'); ?>
</div>
<div class="row">
<?php echo $form->labelEx($model, 'product_name_cn'); ?>
<?php echo $form->textField($model, 'product_name_cn', array('size' => 60, 'maxlength' => 255)); ?>
<?php echo $form->error($model, 'product_name_cn'); ?>
</div>
<div class="row">
    <?php echo $form->labelEx($model, 'image'); ?>
    <?php echo $form->fileField($model, 'image'); ?>
    <?php echo $form->error($model, 'image'); ?>
</div>
<?php if ($model->image != '') { ?>
    <div class="row">
        <label for="">Product Existing Image</label>
        <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/product/thumb/<?php echo $model->image; ?>" height="40" width="40"/>
        <input type="hidden" name="oldimage"  value="<?php echo $model->image; ?>">
    </div>
<?php } else { ?>
    <input type="hidden" name="oldimage"  value="">
<?php } ?>
<div class="row">
    <?php echo $form->labelEx($model, 'short_desc'); ?>
<?php echo $form->textArea($model, 'short_desc', array('size' => 60, 'maxlength' => 255)); ?>
<?php echo $form->error($model, 'short_desc'); ?>
</div>
<div class="row">
    <?php echo $form->labelEx($model, 'short_desc_cn'); ?>
<?php echo $form->textArea($model, 'short_desc_cn', array('size' => 60, 'maxlength' => 255)); ?>
<?php echo $form->error($model, 'short_desc_cn'); ?>
</div>

<div class="row">
<?php echo $form->labelEx($model, 'long_desc'); ?>
<?php echo $form->textArea($model, 'long_desc', array('rows' => 6, 'cols' => 50)); ?>
<?php echo $form->error($model, 'long_desc'); ?>
</div>
<div class="row">
<?php echo $form->labelEx($model, 'long_desc_cn'); ?>
<?php echo $form->textArea($model, 'long_desc_cn', array('rows' => 6, 'cols' => 50)); ?>
<?php echo $form->error($model, 'long_desc_cn'); ?>
</div>
<div class="row">
    <?php echo $form->labelEx($model, 'price'); ?>
<?php echo $form->textField($model, 'price', array('size' => 60, 'maxlength' => 255)); ?>
<?php echo $form->error($model, 'price'); ?>
</div>
<div class="row hideWine">
    <?php echo $form->labelEx($model, 'video'); ?>
    <?php echo $form->fileField($model, 'video'); ?>
    <?php echo $form->error($model, 'video'); ?>
</div>
<?php if ($model->video != '') { ?>
    <div class="row">
        <label for="">Product Existing Video</label>
         <video width="220" height="180" controls>
            <source src="<?php echo Yii::app()->request->baseUrl; ?>/images/product/video/<?php echo $model->video; ?>" >
          </video> 
         <input type="hidden" name="oldvideo"  value="<?php echo $model->video; ?>">
    </div>
<?php } else { ?>
    <input type="hidden" name="oldvideo"  value="">
<?php } ?>
<!--<div class="row">
<?php echo $form->labelEx($model, 'flash_sale_product'); ?>
<?php echo $form->checkBox($model,'flash_sale_product',array('value'=>1,'uncheckValue'=>0,'checked'=>($model->flash_sale_product=="yes")?true:"")); ?>
<?php echo $form->error($model, 'flash_sale_product'); ?>
</div>-->

<div class="row hideGlass">
<?php echo $form->labelEx($model, 'type'); ?>
<?php echo $form->textField($model, 'type', array('size' => 60, 'maxlength' => 255)); ?>
<?php echo $form->error($model, 'type'); ?>
</div>
<div class="row hideGlass">
    <?php echo $form->labelEx($model, 'type_cn'); ?>
<?php echo $form->textField($model, 'type_cn', array('size' => 60, 'maxlength' => 255)); ?>
<?php echo $form->error($model, 'type_cn'); ?>
</div>
<div class="row hideGlass">
    <?php echo $form->labelEx($model, 'varietal'); ?>
<?php echo $form->textField($model, 'varietal', array('size' => 60, 'maxlength' => 255)); ?>
    <?php echo $form->error($model, 'varietal'); ?>
</div>
<div class="row hideGlass">
    <?php echo $form->labelEx($model, 'varietal_cn'); ?>
<?php echo $form->textField($model, 'varietal_cn', array('size' => 60, 'maxlength' => 255)); ?>
    <?php echo $form->error($model, 'varietal_cn'); ?>
</div>
<div class="row hideGlass">
    <?php echo $form->labelEx($model, 'location'); ?>
<?php echo $form->textField($model, 'location', array('size' => 60, 'maxlength' => 255)); ?>
    <?php echo $form->error($model, 'location'); ?>
</div>
<div class="row hideGlass">
    <?php echo $form->labelEx($model, 'location_cn'); ?>
<?php echo $form->textField($model, 'location_cn', array('size' => 60, 'maxlength' => 255)); ?>
    <?php echo $form->error($model, 'location_cn'); ?>
</div>
<div class="row hideGlass">
    <?php echo $form->labelEx($model, 'country'); ?>
<?php echo $form->textField($model, 'country', array('size' => 60, 'maxlength' => 255)); ?>
    <?php echo $form->error($model, 'country'); ?>
</div>
<div class="row hideGlass">
    <?php echo $form->labelEx($model, 'country_cn'); ?>
<?php echo $form->textField($model, 'country_cn', array('size' => 60, 'maxlength' => 255)); ?>
    <?php echo $form->error($model, 'country_cn'); ?>
</div>
<div class="row hideGlass">
    <?php echo $form->labelEx($model, 'year'); ?>
<?php echo $form->textField($model, 'year', array('size' => 60, 'maxlength' => 255)); ?>
    <?php echo $form->error($model, 'year'); ?>
</div>
<div class="row">
    <?php echo $form->labelEx($model, 'glassware'); ?>
<?php echo $form->textField($model, 'glassware', array('size' => 60, 'maxlength' => 255)); ?>
    <?php echo $form->error($model, 'glassware'); ?>
</div>
<div class="row">
    <?php echo $form->labelEx($model, 'glassware_cn'); ?>
<?php echo $form->textField($model, 'glassware_cn', array('size' => 60, 'maxlength' => 255)); ?>
    <?php echo $form->error($model, 'glassware_cn'); ?>
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
<script src="<?php echo Yii::app()->getBaseUrl(true); ?>/lib/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
<?php if($model->isNewRecord){?>
    changeDropdown("wine");
<?php }else{ ?>
    changeDropdown("<?php echo $model->product_type;?>");
<?php } ?>
function changeDropdown(val){
    if(val=="glassware"){
        $("div.hideGlass").hide();
        $("div.hideWine").show();
    }
    if(val=='wine'){
        $("div.hideGlass").show();
        $("div.hideWine").hide();
    }
    return false;
}
/*    CKEDITOR.replace('Product[long_desc]', {
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
*/
</script>
