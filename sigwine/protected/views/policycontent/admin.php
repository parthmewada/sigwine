<?php
/* @var $this PolicycontentController */
/* @var $model Policycontent */

$this->breadcrumbs=array(
	'Policycontents'=>array('admin'),
	'Manage',
);

$this->menu=array(
//	array('label'=>'List Policycontent', 'url'=>array('index')),
	array('label'=>'Create Policycontent', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#policycontent-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Policycontents</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'policycontent-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'policycontent_id',
		'policy_title',
		'policy_description:html',
		array(
                    'class'=>'CButtonColumn',
                    'template' => '{update}&nbsp;&nbsp;{view} ',
                    'header' => 'Action',
		),
	),
)); ?>
