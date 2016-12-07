<?php
/* @var $this NotificationController */
/* @var $model Notification */

$this->breadcrumbs=array(
	'Notifications'=>array('index'),
	$model->notification_id,
);

$this->menu=array(
	array('label'=>'Manage Notification', 'url'=>array('admin')),
);
?>

<h1>View Notification #<?php echo $model->notification_id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
	    
		array(
		    'label'=>'ID',
		    'name'=>'notification_id',
                    'value'=>$model->notification_id,
                    
                ),
		array(
		    'name'=>'user_id',
                    'value'=>$model->useremail->email,
                    
                ),
		array(
		    'label'=>'Message Text',
		    'name'=>'notification',
                    'value'=>$model->notification,
                    
                ),
		array(
		    
		    'label'=>'Message Date',
		    'name'=>'cdate',
                    'value'=>$model->cdate,
                    
                ),
	),
)); ?>
