<?php
/* @var $this PolicycontentController */
/* @var $model Policycontent */

$this->breadcrumbs=array(
	'Policycontents'=>array('admin'),
	'Create',
);

$this->menu=array(
//	array('label'=>'List Policycontent', 'url'=>array('index')),
	array('label'=>'Manage Policycontent', 'url'=>array('admin')),
);
?>

<h1>Create Policycontent</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>