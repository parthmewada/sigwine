<?php
/* @var $this SurveyController */
/* @var $model Survey */
/* @var $form CActiveForm */
?>
<!--<link href="<?php echo Yii::app()->getBaseUrl(true); ?>/js/jquery-ui-timepicker-addon.css" rel="stylesheet" />
<script src="<?php echo Yii::app()->getBaseUrl(true); ?>/js/jquery-ui-timepicker-addon.js"></script>
<script src="<?php echo Yii::app()->getBaseUrl(true); ?>/js/form-extended.js"></script>-->
<div class="form">
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'survey-form',
    'enableAjaxValidation' => false,
));
?>
    <p class="note">Fields with <span class="required">*</span> are required.</p>
    <?php echo $form->errorSummary($model); ?>
    <div class="row">
        <?php echo $form->labelEx($model, 'survey_name'); ?>
        <?php echo $form->textField($model, 'survey_name', array('size' => 60, 'maxlength' => 255)); ?>
        <?php echo $form->error($model, 'survey_name'); ?>
    </div>
    <!--    <div class="row">
        <?php echo $form->labelEx($model, 'survey_start'); ?>
        <div data-date-autoclose="true" data-date-format="yyyy-mm-dd" data-date="<?php echo date("Y-m-d"); ?>" data-auto-close="true" class="input-group date" id="dp-ex-1">
            <?php echo $form->textField($model, 'survey_start'); ?>
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        </div>
        <?php echo $form->error($model, 'survey_start'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model, 'survey_end'); ?>
        <div data-date-autoclose="true" data-date-format="yyyy-mm-dd" data-date="<?php echo date("Y-m-d"); ?>" data-auto-close="true" class="input-group date" id="dp-ex-2">
            <?php echo $form->textField($model, 'survey_end'); ?>
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        </div>
        <?php echo $form->error($model, 'survey_end'); ?>
    </div>-->
    <div class="row">
        <?php echo $form->labelEx($model, 'status'); ?>
        <?php echo CHtml::activeDropDownList($model, 'status', array('active' => 'Active', 'inactive' => 'InActive')); ?>
        <?php echo $form->error($model, 'status'); ?>
    </div>
    <div class="row">
        
        <table id="tab" width="100%">
            <?php
            if (!$model->isNewRecord) {
                $i = 1;

                if (isset($quArr)) {
                    foreach ($quArr as $qk => $qv) {
                        $bindS = "";
                        ?>
                        <tr class="surQue surveyQu_<?php echo $qv['question']['survey_question_id'] ?>">
                            <td width="5%" class="odds"><?php echo $i; ?></td>
                            <td width="15%">Question:&nbsp;</td>
                            <td width="80%"><input type="text" name="question[<?php echo $i ?>]" value="<?php echo $qv['question']['question'] ?>"><a href="javascript:void(0);" onclick="DeleQuestion('<?php echo $qv['question']['survey_question_id'] ?>')">&nbsp;&nbsp;Delete</a>
                            </td></tr>
                        <?php
                        $opC = 0;
                        if (is_array($qv['option']) && count($qv['option']) > 0) {
                            ?>

                            <tr class="surveyQu_<?php echo $qv['question']['survey_question_id'] ?>"><td>&nbsp;</td>
                                <td>Options&nbsp;</td><td>
                                    <?php
                                    $j = 1;
                                    foreach ($qv['option'] as $ok => $vk) {
                                        $opC++;
                                        ?>
                                        <input style="min-width:100px !important;" type="text" name="option[<?php echo $i ?>][]" value="<?php echo trim($vk['option']); ?>"/>
                                    <?php
                                    }
                                    if ($opC < 4) {
                                        for ($k = $opC; $k < 4; $k++) {
                                            ?> <input style="min-width:100px !important;"   type="text" name="option[<?php echo $i ?>][]" value=""/>
                    <?php }
                }
                ?>
              </td></tr><?php } ?>
            <?php $i++;
        }
    }
} ?>
        </table>
        <a href="javascript:void(0);" onclick="addQuestion();">+ Add Question <span class="required">*</span></a>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('onclick'=>'return validation();')); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<script>
function validation(){
   var le=$("tr.surQue").length;
   if($("input#Survey_survey_name").val()=="" || $("input#Survey_survey_name").val()==undefined ){
        alert("Please etner value in Survey Name!");
        return false;
   }
   if(le=='0'){
        alert("Please enter atleast one question for survey!");
        return false;        
   }else{
        var flag=false;
        $("input[name^=question]").each(function(){
          if($(this).val()!=""){
            flag=true;
          }
        });
        if(flag!=true){
            alert("Please enter value in question");
            return false;
        }
   }
   return true;
}
function addQuestion() {
    var lenV = $('table#tab tr.surQue').length;
    lenV = lenV + 1;
    var str = '<tr class="surQue temp_remove' + lenV + '"><td class="odds" width="5%">' + lenV + '</td><td width="15%">Question:</td><td width="80%"><input type="text" value="" name="question[' + lenV + ']"><a href="javascript:void(0);" onclick="Deletemp(' + lenV + ')">&nbsp;&nbsp;Delete</a></td></tr>\n\
<tr  class="temp_remove' + lenV + '"><td>&nbsp;</td>\n\
<td>Options </td>\n\
<td><input type="text" style="min-width:100px !important;"  name="option[' + lenV + '][]" value=""/>&nbsp;&nbsp;'
            + '<input type="text" style="min-width:100px !important;"  name="option[' + lenV + '][]" value=""/>&nbsp;&nbsp;'
            + '<input type="text" style="min-width:100px !important;"  name="option[' + lenV + '][]" value=""/>&nbsp;&nbsp;'
            + '<input type="text" style="min-width:100px !important;"  name="option[' + lenV + '][]" value=""/></td></tr>';
    $("table#tab").append(str);
}
function Deletemp(len) {
    $("tr.temp_remove" + len).remove();
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