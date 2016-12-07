<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle=Yii::app()->name . ' - Login';
/*$this->breadcrumbs=array(
	'Login',
);*/

?>
<script>
$(document).ready(function(){
    $("#login-form").validate();/*{
            errorPlacement: function(error, element) {
               // $("#errorSummary").show();
                //error.appendTo($("div#errorSummary"));
            }
        }*/
   
    getDropdownProject = function() {
        $("#old-dp").show();
        $('.ajax-triger').show();
        $("#p_id_old").css({'class':'required'});
        $("#new-dp").html();
        $("#new-dp").hide();
        var uname = $("#LoginForm_username").val();
        $.post( "<?php  echo CController::createUrl('site/projectDropdown'); ?>", 
            { act: "getdropdown", u_name: uname })
            .done(function( data ) {
                $("#p_id_old").css({'class':''});
                $('.ajax-triger').hide();
                $("#old-dp").hide();
                $("#new-dp").show();
                $("#new-dp").html(data);
            }
        ); 
    }
});

</script>
<h1>Scrumboard Login </h1>

<p>Please fill out the following form with your login credentials:</p>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>FALSE,
	'clientOptions'=>array(
		'validateOnSubmit'=>FALSE,
        
	),
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>
        
        

	<div class="row">
		
		<?php echo $form->textField($model,'username', array("class" => "required" ,
                            'onblur' => 'getDropdownProject()',"placeholder"=>"Username")); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>
	<div class="row">
		
		<?php echo $form->passwordField($model,'password', array("class" => "required", "placeholder"=>"Password")); ?>
		<?php echo $form->error($model,'password'); ?>

	</div>
       <div class="row" id="old-dp">
		
		<?php //echo $form->textField($model,'p_id'); 
                    $static5 = array(
                        '' => '-- Select Project --'
                    );
                    echo  CHtml::dropDownList("p_id_old", null, $static5 , array("class" => "required"));
                    
      
               ?>
           <img class="ajax-triger" title="..." alt="..." src="/wasta/images/ajax-loader.gif" style="display: none;">
		<?php echo $form->error($model,'project_name'); ?>
	</div>
        <div class="row" id="new-dp">
            
        </div>
        <div>
            
        </div>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Login'); ?>&nbsp;<input type="button" class="reg-link" value="Registration" onclick="window.location='<?php echo Yii::app()->getBaseUrl(true)."/index.php/userMaster/usercreate"; ?>'" />
	</div>
        

<?php $this->endWidget(); ?>
</div><!-- form -->
