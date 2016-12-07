<?php
/* @var $this FlashSaleController */
/* @var $model FlashSale */

$this->breadcrumbs=array(
	'Exclusive'=>array('index'),
	$model->title=>array('view','id'=>$model->flash_sale_id),
	'Update',
);

$this->menu=array(
	/*array('label'=>'List Exclusive', 'url'=>array('index')),*/
	array('label'=>'Create Exclusive', 'url'=>array('create')),
	array('label'=>'View Exclusive', 'url'=>array('view', 'id'=>$model->flash_sale_id)),
	array('label'=>'Manage Exclusive', 'url'=>array('admin')),
);
?>

<h1>Update Exclusive <?php echo $model->title; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>