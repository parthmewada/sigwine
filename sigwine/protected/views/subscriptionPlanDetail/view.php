<?php
/* @var $this SubscriptionPlanDetailController */
/* @var $model SubscriptionPlanDetail */

$this->breadcrumbs=array(
	'Subscription Plan Details'=>array('index'),
	$model->detail_id,
);

$this->menu=array(
        array('label'=>'Manage Plan Detail', 'url'=>array('admin')),
	/*array('label'=>'List SubscriptionPlanDetail', 'url'=>array('index')),*/
	array('label'=>'Create Plan Detail', 'url'=>array('create')),
	array('label'=>'Update SubscriptionPlanDetail', 'url'=>array('update', 'id'=>$model->detail_id)),
	array('label'=>'Delete SubscriptionPlanDetail', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->detail_id),'confirm'=>'Are you sure you want to delete this item?')),
    )
?>

<h1>View SubscriptionPlanDetail #<?php echo $model->plan_id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'detail_id',
		'plan_id',
		'product_id',
		'display_order',
	),
)); ?>
