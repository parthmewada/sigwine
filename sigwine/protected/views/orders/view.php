<?php
/* @var $this OrdersController */
/* @var $model Orders */
//echo "<pre>";
//print_r($userData);die;
$this->breadcrumbs=array(
	'Orders'=>array('index'),
	$model->order_id,
);

$this->menu=array(
	//array('label'=>'List Orders', 'url'=>array('index')),
	array('label'=>'Create Orders', 'url'=>array('create')),
	array('label'=>'Update Orders', 'url'=>array('update', 'id'=>$model->order_id)),
	array('label'=>'Delete Orders', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->order_id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Orders', 'url'=>array('admin')),
);
?>
<h1>View Orders #<?php echo $model->order_id; ?></h1>
<table width="100%" border="1" class="detail-view">
    <tr><td>
            Order Id : <?php echo $model->order_id; ?>
        </td><td>Order Date: <?php echo $model->order_date; ?></td></tr>
    <tr class="odd"><td colspan="2"><strong>User Info</strong></td></tr>
    <tr><td>
            User : <?php echo $model->order_id; ?>
        </td><td>Order Date: <?php echo $model->order_date; ?></td></tr>
</table>
<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'order_id',
		'user_id',
                array(
                    'name'=>'user_id',
                     'value' => Users::model()->getUserData($model->user_id),
                 ),
		'subscription_plan_id',
		'subscription_duration',
		'order_total',
		'order_qty',
		'type',
		'order_status',
		'order_date',
		'order_createdby',
		'address_id',
		'payment_method',
                //array("name"=>"user_id","value"=>$userData['first_name']),
	),
)); ?>

