<?php
/* @var $this SubscriptionPlanController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Subscription Plans',
);

$this->menu=array(
	array('label'=>'Create SubscriptionPlan', 'url'=>array('create')),
	array('label'=>'Manage SubscriptionPlan', 'url'=>array('admin')),
);
?>

<h1>Subscription Plans</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
