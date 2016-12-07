<?php
/* @var $this UserMasterController */
/* @var $model UserMaster */

$this->breadcrumbs=array(
	'User Masters'=>array('index'),
	'Manage Super Users',
);

$this->menu=array(
//	array('label'=>'List UserMaster', 'url'=>array('index')),
	//array('label'=>'Create UserMaster', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#user-master-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<h1>Manage Super Users</h1>
<style type="text/css">
#sidebar{display:none;}
</style>
<?php //echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'user-master-grid',
	'dataProvider'=>$model->search(),
	'filter'=>null,
	'columns'=>array(
                'first_name',
		'last_name',
		'user_name',
		/*'user_pass',*/
             array(            // display 'create_time' using an expression
                'name'=>'user_pass',
                'value'=>'base64_decode($data->user_pass)',
            ),
		
		/*
		'user_image',
		'created_by',
		'created_on',
		'modified_by',
		'modified_on',
		'is_active',
		'is_delete',
		*/
		array(
                    'class' => 'CButtonColumn',
                    'template' => '{update}&nbsp;&nbsp;{view} ',
                    'header' => 'Action',
                ),
	),
)); ?>
