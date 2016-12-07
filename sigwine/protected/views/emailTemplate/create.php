<?php
/* @var $this EmailTemplateController */
/* @var $model EmailTemplate */

$this->breadcrumbs=array(
	'Email Templates'=>array('admin'),
	'Create',
);

$this->menu=array(
	/*array('label'=>'List EmailTemplate', 'url'=>array('index')),*/
	array('label'=>'Manage EmailTemplate', 'url'=>array('admin')),
);
?>

<h1>Create EmailTemplate</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>