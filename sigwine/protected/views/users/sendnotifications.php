<style>
.overlay-bg{ position: fixed; width: 100%; height: 100%; display: none; background: rgba(0,0,0, 0.3); left: 0; top: 0; z-index: 100; }
.loader { background: url(<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif) no-repeat; position: absolute;
    top: 50%;
    left: 50%;
    width: 55px;
    height: 55px;
    margin:0px;
    -webkit-animation:spin 2s linear infinite;
    -moz-animation:spin 2s linear infinite;
    animation:spin 2s linear infinite;
}
.savedata
{
    background: #8d0e3a none repeat scroll 0 0;
    border: medium none !important;
    color: #c0b494;
    cursor: pointer;
    padding: 7px 15px;
}
</style>
<?php
/* @var $this UsersController */
/* @var $model Users */

$this->breadcrumbs=array(
	'Users'=>array('index'),
	'Manage',
);

$this->menu=array(array('label'=>'View Notifications', 'url'=>array('notification/admin')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#users-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<h1>Manage Notifications</h1>
<?php //echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'send-notification',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
        'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>
    <p class="note">Fields with <span class="required">*</span> are required.</p>
    <div class="row" id="my-msg" style="display:none;"></div>
	<?php echo $form->errorSummary($model); ?>
	<div class="row">
	    <label for="users">Send Notifications:</label>
	    <select name="ddusers" id="ddusers" >
		<option value="">Choose Option</option>
		<option value="1">All Users</option>
		<option value="2">Selected Users</option>
	    </select>
	</div>
	<div class="row" id="show-notification-grid" style="display:none;">
	<?php $this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'users-grid',
		'dataProvider'=>$model->search(),
		'filter'=>$model,
		'columns'=>array(
		       array(
			'id' => 'autoId',
			'class' => 'CCheckBoxColumn',
			'selectableRows' => '50',
			'visible'=> true,
		      ),
			array('header'=>"Name", 'name'=>'first_name', 'value'=>'$data->first_name'),
			'email',
		),
	)); ?>	
	</div>	    
	<div class="row">
	    <label for="notifications" style="vertical-align:top;">Notifications</label>
	    <textarea name="notifications" id="notifications" rows="6" cols="50"></textarea>
	</div>
	<div class="row buttons">
		 <?php echo CHtml::Button('Send Notification', array('class' => 'savedata')); ?>
	</div>
<?php $this->endWidget(); ?>
</div>
<script>	
 $('.savedata').on("click", function () {
        var Array = $('#users-grid').yiiGridView('getChecked', 'autoId');
        var msg = $( "#notifications" ).val();
	 $('#loader-div-p').show();
	//var type = $(this).attr('status');
         $.ajax({
                url: "<?php echo Yii::app()->baseUrl; ?>/index.php/Users/ajaxsendNotificaton/",
                type: "POST",
                data: { ids: Array, messege:msg},
                success: function (data) {
		            if(data == 'success') {
                            document.getElementById('my-msg').style.display = 'block';
                            $("#my-msg").html("Notification Send Successfully!");
                        } else {
                            document.getElementById('my-msg').style.display = 'block';
                            $("#my-msg").html("Please Try Again!");
                        }
                        //$.fn.yiiGridView.update('users-grid');
			$('#loader-div-p').hide();
                },
                error: function () {
                    //alert("Something wrong happen ! Please try again!")
                }
            });
       
    });
 $('#ddusers').on("change", function () {
 {
    var val = $("#ddusers").val();
    if(val == 2)
	$('#show-notification-grid').css('display','block');
    else
	$('#show-notification-grid').css('display','none');
 }
 }); 
</script>	
