<?php
/* @var $this UserMasterController */
/* @var $model UserMaster */
/* @var $form CActiveForm */
?>
<script>

$(document).ready(function(){
    $("#user-master-form").validate(/*{
     errorPlacement: function(error, element) {
     // $("#errorSummary").show();
     //error.appendTo($("div#errorSummary"));
     }
     }*/);
        // add unique rules
    jQuery.validator.addClassRules("unique", {
        required: true,
        email: true,
        remote: "<?php echo Yii::app()->request->baseUrl; ?>/index.php/userMaster/checkunique?user_name_old="+$("#user_name_old").val(),
    });
    jQuery.extend(jQuery.validator.messages, {
        remote: "User Already Exist,Plase try another user !",
    });

});


</script>
<style>
.before_login #content {
    width: 500px !important;
}
</style>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-master-form',
	'enableAjaxValidation'=>false,
        'htmlOptions' =>array(
                            'enctype' => 'multipart/form-data',
                        ),
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>
        
	<div class="row">
		<?php //echo $form->labelEx($model,'user_name'); ?>
		<?php echo $form->textField($model,'user_name',array('size'=>60,'maxlength'=>255, 'class'=> 'unique',"placeholder"=>"User Name")); ?>
		<?php echo $form->error($model,'user_name'); ?>
	</div>
        <?php if(!$model->isNewRecord ) { ?>
        <input type="hidden" id="user_name_old" value="<?php echo $model->user_name;?>" />
        <?php } else { ?>
        <input type="hidden" id="user_name_old" value="" />
        <?php } ?>
	<div class="row">
		<?php //echo $form->labelEx($model,'user_pass'); ?>
		<?php echo $form->passwordField($model,'user_pass',array('size'=>60,'maxlength'=>255, 'class'=> 'required',"placeholder"=>"Password")); ?>
		<?php echo $form->error($model,'user_pass'); ?>
	</div>
       
	<div class="row">
		<?php //echo $form->labelEx($model,'first_name'); ?>
		<?php echo $form->textField($model,'first_name',array('size'=>60,'maxlength'=>255, 'class'=> 'required',"placeholder"=>"First Name")); ?>
		<?php echo $form->error($model,'first_name'); ?>
	</div>

	<div class="row">
		<?php //echo $form->labelEx($model,'last_name'); ?>
		<?php echo $form->textField($model,'last_name',array('size'=>60,'maxlength'=>255, 'class'=> 'required',"placeholder"=>"Last Name")); ?>
		<?php echo $form->error($model,'last_name'); ?>
	</div>

	<div class="row">
		<?php //echo $form->labelEx($model,'user_image'); ?>
             <?php echo CHtml::activeFileField($model, 'user_image',array('class'=> ($model->isNewRecord!='1') ? '' : 'required',"placeholder"=>"Image")); ?>
		<?php //echo $form->textField($model,'user_image',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'user_image'); ?>
	</div>
        <?php if($model->isNewRecord!='1'){ ?>
            <div class="row">
                <?php echo CHtml::image(Yii::app()->request->baseUrl.'/images/user/'.$model->user_image,"user_image",array("width"=>200)); ?>  
            </div>
        <?php  } ?>
	<div class="row">
		<?php //echo $form->labelEx($model,'department_id'); ?>
		<?php //    echo $form->textField($model,'department_id'); 
                     $criteria = new CDbCriteria;
                   $criteria->condition = 'is_active = "0" AND is_delete = "0" ';
                    $static = array(
                     ''     => '-- Select Department --'
                    );
                    echo CHtml::activeDropDownList($model, 'department_id',$static + CHtml::listData(DepartmentMaster::model()->findAll($criteria), 'department_id', 'department_name'),array('class' => 'required',"placeholder"=>"Department")); 
               ?>
		<?php echo $form->error($model,'department_id'); ?>
	</div>

	<div class="row">
		<?php //echo $form->labelEx($model,'designation_id'); ?>
		<?php //echo $form->textField($model,'designation_id'); ?>
                <?php //    echo $form->textField($model,'department_id'); 
                    $criteria = new CDbCriteria;
                    $criteria->condition = 'is_active = "0" AND is_delete = "0" ';
                    $static = array(
                     ''     => '-- Select Designation --'
                    );
                    echo CHtml::activeDropDownList($model, 'designation_id',$static + CHtml::listData(DesignationMaster::model()->findAll($criteria), 'designation_id', 'degignation_name'),array('class' => 'required',"placeholder"=>"Designation")); 
                ?>
		<?php echo $form->error($model,'designation_id'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>&nbsp;&nbsp;&nbsp;<input type="button" class="reg-link" value="Login" onclick="window.location='<?php echo Yii::app()->getBaseUrl(true)."/index.php"; ?>'" />
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->