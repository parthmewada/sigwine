
<?php
/* @var $this UserMasterController */
/* @var $model UserMaster */

$this->breadcrumbs=array(
	'User Masters'=>array('index'),
	$model->first_name,
);

$this->menu=array(
	/*array('label'=>'List UserMaster', 'url'=>array('index')),
	array('label'=>'Create UserMaster', 'url'=>array('create')),
	array('label'=>'Update UserMaster', 'url'=>array('update', 'id'=>$model->u_id)),
	array('label'=>'Delete UserMaster', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->u_id),'confirm'=>'Are you sure you want to delete this item?')),
         */
	array('label'=>'Manage UserMaster', 'url'=>array('admin'))
);
?>

<h1>View UserMaster #<?php echo $model->first_name; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
                'first_name',
		'last_name',
		'user_name',
                array("name"=>"user_pass",'value'=>base64_decode($model->user_pass)),
		
	),
)); ?>
