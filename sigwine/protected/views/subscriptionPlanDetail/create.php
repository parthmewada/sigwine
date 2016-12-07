<?php
/* @var $this SubscriptionPlanDetailController */
/* @var $model SubscriptionPlanDetail */

$this->breadcrumbs=array(
	'Subscription Plan Details'=>array('index'),
	'Create',
);

$this->menu=array(
	/*array('label'=>'List SubscriptionPlanDetail', 'url'=>array('index')),*/
	array('label'=>'Manage Plan Detail', 'url'=>array('admin')),
);
?>

<h1>Create SubscriptionPlanDetail</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model,'model1'=>$model1,)); ?>