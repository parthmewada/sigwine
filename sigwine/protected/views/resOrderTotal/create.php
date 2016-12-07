<?php
/* @var $this ResOrderTotalController */
/* @var $model ResOrderTotal */

$this->breadcrumbs=array(
	'Res Order Totals'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List ResOrderTotal', 'url'=>array('index')),
	array('label'=>'Manage ResOrderTotal', 'url'=>array('admin')),
);
?>

<h1>Create ResOrderTotal</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>