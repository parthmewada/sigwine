<?php
/* @var $this SurveyQuestionController */
/* @var $model SurveyQuestion */

$this->breadcrumbs=array(
	'Survey Questions'=>array('index'),
	$model->survey_question_id,
);

$this->menu=array(
	//array('label'=>'List SurveyQuestion', 'url'=>array('index')),
	array('label'=>'Create SurveyQuestion', 'url'=>array('create')),
	array('label'=>'Update SurveyQuestion', 'url'=>array('update', 'id'=>$model->survey_question_id)),
	array('label'=>'Delete SurveyQuestion', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->survey_question_id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage SurveyQuestion', 'url'=>array('admin')),
);
?>

<h1>View SurveyQuestion #<?php echo $model->survey_question_id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'survey_question_id',
		'survey_id',
		'question',
	),
)); ?>
