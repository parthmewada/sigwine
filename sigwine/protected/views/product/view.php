<?php
/* @var $this ProductController */
/* @var $model Product */

$this->breadcrumbs=array(
	'Wine & Glassware'=>array('index'),
	$model->product_name,
);

$this->menu=array(
	/*array('label'=>'List Product', 'url'=>array('index')),
        array('label'=>'Delete Product', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->product_id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Create Product', 'url'=>array('create')),
	array('label'=>'Update Product', 'url'=>array('update', 'id'=>$model->product_id)),
	*/
	array('label'=>'Manage Wines & Glassware', 'url'=>array('admin')),
);

?>

<h1>View Wine & Glassware #<?php echo $model->product_name; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'product_id',
		'product_name',
		'image',
		'short_desc',
		array("name"=>"long_desc","value"=>SubscriptionPlan::model()->strtoHTML($model->long_desc)),
		'price',
		'product_type',
		'type',
		'varietal',
                'location',
		'country',
                'year',
                'glassware',
		'status',
	),
)); ?>
