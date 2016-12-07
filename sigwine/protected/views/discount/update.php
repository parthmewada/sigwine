<?php
/* @var $this NotificationController */
/* @var $model Notification */


$this->breadcrumbs=array(
	'Discount'=>array('Admin'),
	$model->discount_id=>array('view','id'=>$model->discount_id),
	'Update',
);

$this->menu=array(
	array('label'=>'View Discount', 'url'=>array('view', 'id'=>$model->discount_id)),
	array('label'=>'Manage Discount', 'url'=>array('admin')),
);
?>

<h1>Update Discount <?php echo $model->discount_id; ?></h1>


<?php  $this->renderPartial('_form', array('model'=>$model)); ?>
