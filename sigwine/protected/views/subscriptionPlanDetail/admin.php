<?php
/* @var $this SubscriptionPlanDetailController */
/* @var $model SubscriptionPlanDetail */

$this->breadcrumbs=array(
	'Subscription Plan Details'=>array('index'),
	'Manage',
);

$this->menu=array(
	/*array('label'=>'List Plan Detail', 'url'=>array('index')),*/
	array('label'=>'Create Plan Detail', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#subscription-plan-detail-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Subscription Plan Details</h1>
<?php //echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form array(
                    'header'=>'Product Name',
                    //'name'=>'touserid',
                    'type'=>'html',
                    'value'=>'SubscriptionPlanDetail::model()->getProductName($data->plan_id)'
                ),-->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'subscription-plan-detail-grid',
	'dataProvider'=>$model->search(),
	//'filter'=>$model,
	'columns'=>array(
		'detail_id',
		'Plan.plan_name',
		'Product.product_name',
                'month',
                'year',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>