<?php
/* @var $this SurveyController */
/* @var $model Survey */

$this->breadcrumbs=array(
	'Surveys'=>array('index'),
	'Manage',
);

$this->menu=array(
	/*array('label'=>'List Survey', 'url'=>array('index')),*/
	array('label'=>'Create Survey', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#survey-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Surveys</h1>
<style>
    .button-column{color:#6d6d6d !important}
</style>
<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php //echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'survey-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'survey_id',
		'survey_name',
		'status',
		'created_on',
		array(
			'class'=>'CButtonColumn',
                        'header' => 'Action'
		),
                array
                (
                    'header'=>'#',    
                    'class'=>'CButtonColumn',
                    'template'=>'{Result}',
                    'buttons'=>array
                    (
                        'Result' => array
                        (
                            'header'=>'Result',
                            'url'=>'Yii::app()->createUrl("Survey/result", array("id"=>$data->survey_id))',
                       ),
                     ),
               ),
	)
        ,
)); ?>
