<?php
/* @var $this SurveyQuestionController */
/* @var $model SurveyQuestion */

$this->breadcrumbs=array(
	'Survey Questions'=>array('index'),
	$model->survey_question_id=>array('view','id'=>$model->survey_question_id),
	'Update',
);

$this->menu=array(
	/*array('label'=>'List SurveyQuestion', 'url'=>array('index')),*/
	array('label'=>'Create SurveyQuestion', 'url'=>array('create')),
	array('label'=>'View SurveyQuestion', 'url'=>array('view', 'id'=>$model->survey_question_id)),
	array('label'=>'Manage SurveyQuestion', 'url'=>array('admin')),
);
?>

<h1>Update SurveyQuestion <?php echo $model->survey_question_id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>