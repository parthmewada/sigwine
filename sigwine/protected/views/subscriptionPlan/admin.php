<?php
/* @var $this SubscriptionPlanController */
/* @var $model SubscriptionPlan */

$this->breadcrumbs=array(
	'Subscription Plans'=>array('index'),
	'Manage',
);

$this->menu=array(
	/*array('label'=>'List SubscriptionPlan', 'url'=>array('index')),*/
	array('label'=>'Create SubscriptionPlan', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#subscription-plan-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Subscription Plans</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php // echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'subscription-plan-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'plan_id',
		'plan_name',
		'plan_type',
		'status',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
