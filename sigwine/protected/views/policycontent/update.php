<?php
/* @var $this PolicycontentController */
/* @var $model Policycontent */

$this->breadcrumbs=array(
	'Policycontents'=>array('index'),
	$model->policycontent_id=>array('view','id'=>$model->policycontent_id),
	'Update',
);

$this->menu=array(
	/*array('label'=>'List Policycontent', 'url'=>array('index')),*/
	//array('label'=>'Create Policycontent', 'url'=>array('create')),
	array('label'=>'View Policycontent', 'url'=>array('view', 'id'=>$model->policycontent_id)),
	array('label'=>'Manage Policycontent', 'url'=>array('admin')),
);
?>

<h1>Update Policycontent <?php echo $model->policycontent_id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>