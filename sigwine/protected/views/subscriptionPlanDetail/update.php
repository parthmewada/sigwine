<?php
/* @var $this SubscriptionPlanDetailController */
/* @var $model SubscriptionPlanDetail */

$this->breadcrumbs=array(
	'Subscription Plan Details'=>array('index'),
	$model->detail_id=>array('view','id'=>$model->detail_id),
	'Update',
);

$this->menu=array(
	/*array('label'=>'List SubscriptionPlanDetail', 'url'=>array('index')),*/
	array('label'=>'Create SubscriptionPlanDetail', 'url'=>array('create')),
	array('label'=>'View SubscriptionPlanDetail', 'url'=>array('view', 'id'=>$model->detail_id)),
	array('label'=>'Manage Plan Detail', 'url'=>array('admin')),
);
?>

<h1>Update SubscriptionPlanDetail <?php echo $model->detail_id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>