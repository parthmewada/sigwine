<?php
/* @var $this SubscriptionPlanController */
/* @var $model SubscriptionPlan */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'subscription-plan-form',
	'enableAjaxValidation'=>false,
        'htmlOptions' => array('enctype' => 'multipart/form-data')
));
$durationArr=array("3"=>"3 Months","6"=>"6 Months","9"=>"9 Months","12"=>"12 Months","auto"=>"Auto",);
?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'plan_name'); ?>
		<?php echo $form->textField($model,'plan_name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'plan_name'); ?>
	</div>
        <div class="row">
		<?php echo $form->labelEx($model,'plan_name_cn'); ?>
		<?php echo $form->textField($model,'plan_name_cn',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'plan_name_cn'); ?>
	</div>
        <div class="row">
		<?php echo $form->labelEx($model,'plan_type'); ?>
                <?php echo CHtml::activeDropDownList($model, 'plan_type',array('two_bottles' => 'Two Bottles', 'three_bottles' => 'Three Bottles'));?>
		<?php echo $form->error($model,'plan_type'); ?>
	</div>
        <!--   
            <div class="row">
		<?php echo $form->labelEx($model,'price'); ?>
		<?php echo $form->textField($model,'price',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'price'); ?>
	</div>
        -->
        <div class="row"><?php echo $form->labelEx($model,'price'); ?>
            <table id="tab" width="100%">
            <?php
                if (!$model->isNewRecord) {
                    $i = 1;
                    if (isset($quArr)) {
                        foreach ($quArr as $qk => $qv) {
                            $bindS = "";
                        ?>
                        <tr class="surQue surveyQu_<?php echo $qv['duration']; ?>">
                        <td width="5%" class="odds"><?php echo $i; ?></td>
                        <td width="30%">Duration:&nbsp;
                            <select style="min-width:100px !important;" type="text" name="duration[<?php echo $i ?>]" value="<?php echo $qv['duration']; ?>">
                                <?php foreach($durationArr as $k=>$v){ ?> 
                                <option value="<?php echo $k ?>" <?php echo ($qv["duration"]==$k?"selected='selected'":""); ?>><?php echo $v; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td width="55%">Price(¥)&nbsp;<input style="min-width:100px !important;" type="text" name="price[<?php echo $i ?>]" value="<?php echo $qv['price']; ?>"><a href="javascript:void(0);" onclick="DeleQuestion('<?php echo $qv['subscription_plan_price_id'] ?>')">&nbsp;&nbsp;Delete</a>
                        </td></tr>
                    <?php $i++;
                    }
                }
            } ?>
            </table>
         <a href="javascript:void(0);" onclick="addPrice();">+ Add Price <span class="required">*</span></a>   
        </div>
        <div class="row">
		<?php echo $form->labelEx($model,'image'); ?>
		<?php echo $form->fileField($model,'image'); ?>
		<?php echo $form->error($model,'image'); ?>
        </div>
        <?php if ($model->image!='') { ?>
            <div class="row">
                <label for="">Existing Image</label>
                <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/subscription/thumb/<?php echo $model->image; ?>" height="40" width="40"/>
                <input type="hidden" name="oldimage"  value="<?php echo $model->image; ?>">
           </div>
        <?php }else{ ?>
            <input type="hidden" name="oldimage"  value="">
        <?php }?>
        <div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
                <br/><br/>
                <?php echo $form->textArea($model,'description',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>
         <div class="row">
		<?php echo $form->labelEx($model,'description_cn'); ?>
                <br/><br/>
                <?php echo $form->textArea($model,'description_cn',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'description_cn'); ?>
	</div>    
  	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo CHtml::activeDropDownList($model, 'status',array('Active' => 'Active', 'InActive' => 'InActive'));?>
		<?php echo $form->error($model,'status'); ?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('onclick'=>'return validation();')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<script src="<?php echo Yii::app()->getBaseUrl(true);?>/lib/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
<?php if($model->isNewRecord){ ?>
    addPrice();
<?php } ?>
    /*
CKEDITOR.replace('SubscriptionPlan[description]', {
    extraPlugins: 'colorbutton,colordialog'
});*/
function addPrice() {
    var lenV = $('table#tab tr.surQue').length;
    lenV = lenV + 1;
    var str = '<tr class="surQue temp_remove' + lenV + '">\n\
    <td class="odds" width="5%">' + lenV + '</td>\n\
    <td width="30%">Duration&nbsp;\n\
    <select style="min-width:100px !important;" type="text" value="" name="duration[' + lenV + ']">';
           <?php foreach($durationArr as $k=>$v){ ?> 
           str+='<option value="<?php echo $k ?>"><?php echo $v; ?></option>';
           <?php } ?>
    str+='</select></td>\n\
    <td width="55%">Price (¥)&nbsp;<input style="min-width:100px !important;" type="text" value="" name="price[' + lenV + ']">\n\
    <a href="javascript:void(0);" onclick="Deletemp(' + lenV + ')">&nbsp;&nbsp;Delete</a></td></tr>';
    $("table#tab").append(str);
}
function Deletemp(len) {
    $("tr.temp_remove" + len).remove();
}
function validation(){
   var le=$("tr.surQue").length;
   
   if(le=='0'){
        alert("Please enter atleast one question for survey!");
        return false;        
   }else{
        var flag1=false;
        $("input[name^=price]").each(function(){
          if($(this).val()!=""){
            flag1=true;
          }
          if(isNaN($(this).val())==true && $(this).val()!=""){
              alert("Please enter numeric value in Price");
              return false;
          }
        });
        if(flag1!=true){
            alert("Please enter value in price");
            $("input[name^=price]:eq(0)").focus();
            return false;
        }
   }
   return true;
}
function DeleQuestion(survey_question_id) {
    // var name = document.getElementById('UserPermission_permission_name').value;
    $.ajax({
        type: "POST",
        url: "<?php echo Yii::app()->createUrl('survey/deleteQuestion'); ?>",
        data: {survey_question_id: survey_question_id},
        success: function(data) {
            $("tr.surveyQu_" + survey_question_id).remove();
        },
        error: function(xhr) {
        }
    });
}
</script>
