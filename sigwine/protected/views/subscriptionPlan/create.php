<?php
/* @var $this SubscriptionPlanController */
/* @var $model SubscriptionPlan */

$this->breadcrumbs=array(
	'Subscription Plans'=>array('index'),
	'Create',
);

$this->menu=array(
	/*array('label'=>'List SubscriptionPlan', 'url'=>array('index')),*/
	array('label'=>'Manage SubscriptionPlan', 'url'=>array('admin')),
);
?>

<h1>Create SubscriptionPlan</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>