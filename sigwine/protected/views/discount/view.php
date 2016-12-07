<?php
/* @var $this NotificationController */
/* @var $model Notification */

$this->breadcrumbs=array(
	'Discount'=>array('index'),
	$model->discount_id,
);

$this->menu=array(
	array('label'=>'Edit Discount', 'url'=> $this->createUrl('discount/update/1')),
);
?>

<h1>View Discount #<?php echo $model->discount_id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
	    
		array(
		    'label'=>'ID',
		    'name'=>'discount_id',
                    'value'=>$model->discount_id,
                    
                ),
		array(
		    
		    'label'=>'Percentage',
		    'name'=>'discount_perc',
                    'value'=>$model->discount_perc,
                    
                ),
            array(
		    
		    'label'=>'Status',
		    'name'=>'status',
                    'value'=>$model->status,
                    
                ),
	),
)); ?>
