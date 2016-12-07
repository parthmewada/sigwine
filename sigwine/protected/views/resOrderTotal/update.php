<?php
/* @var $this ResOrderTotalController */
/* @var $model ResOrderTotal */

$this->breadcrumbs=array(
	'Res Order Totals'=>array('index'),
	$model->order_total_id=>array('view','id'=>$model->order_total_id),
	'Update',
);

$this->menu=array(
	array('label'=>'List ResOrderTotal', 'url'=>array('index')),
	array('label'=>'Create ResOrderTotal', 'url'=>array('create')),
	array('label'=>'View ResOrderTotal', 'url'=>array('view', 'id'=>$model->order_total_id)),
	array('label'=>'Manage ResOrderTotal', 'url'=>array('admin')),
);
?>

<h1>Update ResOrderTotal <?php echo $model->order_total_id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>