<?php
/* @var $this FlashSaleController */
/* @var $model FlashSale */

$this->breadcrumbs=array(
	'Exclusive'=>array('index'),
	$model->title,
);

$this->menu=array(
	/*array('label'=>'List FlashSale', 'url'=>array('index')),*/
	array('label'=>'Create FlashSale', 'url'=>array('create')),
	array('label'=>'Update FlashSale', 'url'=>array('update', 'id'=>$model->flash_sale_id)),
	array('label'=>'Delete FlashSale', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->flash_sale_id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Exclusive', 'url'=>array('admin')),
);
?>

<h1>View Exclusive #<?php echo $model->title; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'flash_sale_id',
		'title',
		 array("name"=>"product_id","value"=>Product::model()->getProductData($model->product_id)),
		'sale_start_from',
		'sale_end',
		'status',
	),
)); ?>
