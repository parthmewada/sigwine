<?php
/* @var $this ProductController */
/* @var $model Product */
/* @var $form CActiveForm */
?>
<style>
.showerr
{
    bottom: 5px;
    float: left !important;
    font-size: 13px;
    left: 21%;
    position: absolute;
    width: auto;
    color:red;	
}
</style>
<div class="form form-validation">
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

    <?php echo $form->labelEx($model, 'product_type',array('label'=>'Product Type <span class="required">*</span>')); ?>
<?php echo CHtml::activeDropDownList($model, 'product_type', array('wine' => 'Wine', 'glassware' => 'Glassware'),array('onchange'=>'changeDropdown(this.value)')); ?>
<?php echo $form->error($model, 'product_type'); ?>
</div>
<?php if($model->isNewRecord){?>
<div class="row hideGlass">
    <?php // echo $form->labelEx($model, 'Select Month'); ?>
    <label for="SubscriptionPlan_Select_Month">Select  Month</label>
    <?php
        $month= array("" => "Please select",
                    "1月, January" => "1月, January",
                    "2月, February" => "2月, February",
                    "3月, March" => "3月, March",
                    "4月, April" => "4月, April",
                    "5月, May" => "5月, May",
                    "6月, June" => "6月, June",
                    "7月, July" => "7月, July",
                    "8月, August" => "8月, August",
                    "9月, September" => "9月, September",
                    "10月, October" => "10月, October",
                    "11月, November" => "11月, November",
                    "12月, December" => "12月, December",
		    "Top Selling Wine 热销酒款" => "Top Selling Wine 热销酒款");
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
 <?php echo $form->labelEx($model1, 'Select Plan',array('label' => 'Select Plan <span class="required">*</span>')); ?>
 
 <?php $list = CHtml::listData(SubscriptionPlan::model()->findAll(array('condition' => "status = 'Active'")), 'plan_id', 'plan_name');?>
 <?php echo $form->dropDownList($model1, 'plan_id[]', $list, array('empty' => 'Select Plan', 'class' => '',"multiple"=>"multiple"));?>
 <?php echo $form->error($model1, 'plan_id[]'); ?>
</div>


<?php } ?>
<div class="row">
<?php echo $form->labelEx($model, 'product_name',array('label'=>'Product Name <span class="required">*</span>')); ?>

<?php echo $form->textField($model, 'product_name', array('size' => 60, 'maxlength' => 255)); ?>
<?php echo $form->error($model, 'product_name'); ?>
</div>
<div class="row">
<?php echo $form->labelEx($model, 'product_name_cn',array('label'=>'Product Name (Chinese) <span class="required">*</span>')); ?>

<?php echo $form->textField($model, 'product_name_cn', array('size' => 60, 'maxlength' => 255)); ?>
<?php echo $form->error($model, 'product_name_cn'); ?>
</div>
<div class="row">
    <?php echo $form->labelEx($model, 'image'); ?>
    <?php echo $form->fileField($model, 'image',array('onchange'=>'validateMedia(this);')); ?>
    <?php echo $form->error($model, 'image'); ?>
   <div id="errimg" class="showerr"></div>
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
    <?php echo $form->labelEx($model, 'short_desc',array('label'=>'Short Desc <span class="required">*</span>')); ?>
<?php echo $form->textArea($model, 'short_desc', array('size' => 60, 'maxlength' => 255)); ?>
<?php echo $form->error($model, 'short_desc'); ?>
</div>
<div class="row">
    <?php echo $form->labelEx($model, 'short_desc_cn',array('label'=>'Short Desc (Chinese) <span class="required">*</span>')); ?>

<?php echo $form->textArea($model, 'short_desc_cn', array('size' => 60, 'maxlength' => 255)); ?>
<?php echo $form->error($model, 'short_desc_cn'); ?>
</div>

<div class="row">
<?php echo $form->labelEx($model, 'long_desc',array('label'=>'Long Desc <span class="required">*</span>')); ?>
<?php echo $form->textArea($model, 'long_desc', array('rows' => 6, 'cols' => 50)); ?>
<?php echo $form->error($model, 'long_desc'); ?>
</div>
<div class="row">
<?php echo $form->labelEx($model, 'long_desc_cn',array('label'=>'Long Desc (Chinese)
 <span class="required">*</span>')); ?>

<?php echo $form->textArea($model, 'long_desc_cn', array('rows' => 6, 'cols' => 50)); ?>
<?php echo $form->error($model, 'long_desc_cn'); ?>
</div>
<div class="row">
    <?php echo $form->labelEx($model, 'price',array('label'=>'Price <span class="required">*</span>')); ?>

<?php echo $form->textField($model, 'price', array('size' => 60, 'maxlength' => 255)); ?>
<?php echo $form->error($model, 'price'); ?>
</div>
<div class="row hideWine">
    <?php echo $form->labelEx($model, 'video'); ?>
    <?php echo $form->fileField($model, 'video' , array('onchange'=>'validatevideoMedia(this);')); ?>
    <?php echo $form->error($model, 'video'); ?>
   <div id="errvdo" class="showerr"></div>	
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
<?php echo $form->error($model, 'type_cn', array('class'=>'errmsg')); ?>
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
<?php echo $form->labelEx($model, 'status',array('label'=>'Status <span class="required">*</span>')); ?>

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
</script>
 <!--Jquery validation-->
<script type="text/javascript">
function validateMedia(data) {
        var _URL = window.URL || window.webkitURL;
	
	fileName = data.files[0].name;
	fileExtension = fileName.split('.').pop();
	if(fileName != '' && (fileExtension == "jpg" || fileExtension == "jpeg" || fileExtension == "png"))
	{
            if (data.files[0].size > 2*1024*1024) {
		    $('#errimg').html('Please select file less than 2Mb.');	
		    $(data).val('');
		}	
        }
	else
	{
		 $('#errimg').html('Please select .jpg or .png Image file');
		 $(data).val('');	
	}
}
function validatevideoMedia(data) {
        var _URL = window.URL || window.webkitURL;
	
	fileName = data.files[0].name;
	fileExtension = fileName.split('.').pop();
	if(fileName != '' && (fileExtension == "mp4" || fileExtension == "avi"))
	{
            if (data.files[0].size > 2*1024*1024) {
		    $('#errvdo').html('Please select file less than 2Mb.');	
		    $(data).val('');
		}	
        }
	else
	{
		 $('#errvdo').html('Please select .mp4 or .avi Image file');
		 $(data).val('');	
	}
}
</script>
<script>
    $(document).ready(function () {
	$('#errimg').html('');
	$('#product-form').validate({
            rules:
                    {
                        'Product[product_type]':
                                {
                                    required: true,
                                },
			'SubscriptionPlan[plan_id][]':
                                {
                                    required: true,
                                },
			'Product[product_name_cn]':
				{ 
				    required: true,
				},
			'Product[short_desc]':
				{
				   required: true,
				},
			'Product[short_desc_cn]':
				{
				   required: true,
				},
			'Product[long_desc]':
				{
				   required: true,
				},
			'Product[long_desc_cn]':
				{
				   required: true,
				},
			'Product[price]':
				{
				   required: true,
				},
			'Product[price]':
				{
				   required: true,
				},

			
                    },
            messages:
                    {
                        'Product[product_type]':
                                {
                                    required: "Product Type cannot be blank.",
                                },
 			'SubscriptionPlan[plan_id][]':
                                {
                                    required: "SubscriptionPlan cannot be blank.",
                                },
			'Product[product_name_cn]':
				{ 
				     required: "Product Name (Chinese) cannot be blank.",
				},
			'Product[short_desc]':
				{
				   required: "Short Description cannot be blank.",
				},
			'Product[short_desc_cn]':
				{
				   required: "Short Description (Chinese) cannot be blank.",
				},
			'Product[long_desc]':
				{
				   required: "Long Description cannot be blank.",
				},
			'Product[long_desc_cn]':
				{
				    required: "Long Description (Chinese) cannot be blank.",
				},
			'Product[price]':
				{
				   required: "Price cannot be blank.",
				},
			'Product[status]':
				{
				   required: "Status cannot be blank.",
				},
	
                    },
        });
    });
</script>
