<?php
/* @var $this DiscountController */
/* @var $model Discount */

$this->breadcrumbs=array(
	'Discount'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Discount', 'url'=>array('index')),
	array('label'=>'Manage Discount', 'url'=>array('admin')),
);
?>

<h1>Create Discount</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>

