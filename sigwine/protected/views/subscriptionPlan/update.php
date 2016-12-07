<?php
/* @var $this SubscriptionPlanController */
/* @var $model SubscriptionPlan */

$this->breadcrumbs=array(
	'Subscription Plans'=>array('index'),
	$model->plan_name=>array('view','id'=>$model->plan_id),
	'Update',
);

$this->menu=array(
	/*array('label'=>'List SubscriptionPlan', 'url'=>array('index')),*/
	array('label'=>'Create SubscriptionPlan', 'url'=>array('create')),
	array('label'=>'View SubscriptionPlan', 'url'=>array('view', 'id'=>$model->plan_id)),
	array('label'=>'Manage SubscriptionPlan', 'url'=>array('admin')),
);
?>

<h1>Update SubscriptionPlan #<?php echo $model->plan_name; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model,'quArr'=>$quArr)); ?>