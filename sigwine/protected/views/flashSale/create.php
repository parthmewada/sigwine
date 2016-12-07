<?php
/* @var $this FlashSaleController */
/* @var $model FlashSale */

$this->breadcrumbs=array(
	'Exclusive'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Exclusive', 'url'=>array('index')),
	array('label'=>'Manage Exclusive', 'url'=>array('admin')),
);
?>

<h1>Create Exclusive</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>