<?php
/* @var $this SubscriptionPlanController */
/* @var $model SubscriptionPlan */

$this->breadcrumbs=array(
	'Subscription Plans'=>array('index'),
	$model->plan_name,
);

$this->menu=array(
	/*array('label'=>'List SubscriptionPlan', 'url'=>array('index')),*/
        array('label'=>'Manage SubscriptionPlan', 'url'=>array('admin')),
	array('label'=>'Create SubscriptionPlan', 'url'=>array('create')),
	array('label'=>'Update SubscriptionPlan', 'url'=>array('update', 'id'=>$model->plan_id)),
	array('label'=>'Delete SubscriptionPlan', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->plan_id),'confirm'=>'Are you sure you want to delete this item?'))
	,
);
?>

<h1>View Subscription Plan #<?php echo $model->plan_name; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'plan_id',
		'plan_name',
                'description:html',
		'plan_type',
		'status',
	),
)); ?>
