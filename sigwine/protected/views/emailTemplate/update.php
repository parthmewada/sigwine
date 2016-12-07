<?php
/* @var $this EmailTemplateController */
/* @var $model EmailTemplate */

$this->breadcrumbs=array(
	'Email Templates'=>array('admin'),
	$model->template_id=>array('view','id'=>$model->template_id),
	'Update',
);

$this->menu=array(
	/*array('label'=>'List EmailTemplate', 'url'=>array('index')),
	array('label'=>'Create EmailTemplate', 'url'=>array('create')),*/
	array('label'=>'View EmailTemplate', 'url'=>array('view', 'id'=>$model->template_id)),
	array('label'=>'Manage EmailTemplate', 'url'=>array('admin')),
);
?>

<h1>Update EmailTemplate <?php echo $model->template_id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>