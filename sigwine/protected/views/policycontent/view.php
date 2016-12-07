<?php
/* @var $this PolicycontentController */
/* @var $model Policycontent */

$this->breadcrumbs=array(
	'Policycontents'=>array('admin'),
	$model->policycontent_id,
);

$this->menu=array(
	/*array('label'=>'List Policycontent', 'url'=>array('index')),
	array('label'=>'Create Policycontent', 'url'=>array('create')),
	array('label'=>'Update Policycontent', 'url'=>array('update', 'id'=>$model->policycontent_id)),
	array('label'=>'Delete Policycontent', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->policycontent_id),'confirm'=>'Are you sure you want to delete this item?')),*/
	array('label'=>'Manage Policycontent', 'url'=>array('admin')),
);
?>

<h1>View Policycontent #<?php echo $model->policycontent_id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'policycontent_id',
		'policy_title',
		
		'policy_description',
	),
)); ?>
