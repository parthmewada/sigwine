<?php
/* @var $this SubscriptionPlanDetailController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Subscription Plan Details',
);

$this->menu=array(
	array('label'=>'Create SubscriptionPlanDetail', 'url'=>array('create')),
	array('label'=>'Manage SubscriptionPlanDetail', 'url'=>array('admin')),
);
?>

<h1>Subscription Plan Details</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
