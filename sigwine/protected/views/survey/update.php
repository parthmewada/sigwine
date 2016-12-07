<?php
/* @var $this SurveyController */
/* @var $model Survey */

$this->breadcrumbs=array(
	'Surveys'=>array('index'),
	$model->survey_id=>array('view','id'=>$model->survey_id),
	'Update',
);

$this->menu=array(
	/*array('label'=>'List Survey', 'url'=>array('index')),*/
	array('label'=>'Create Survey', 'url'=>array('create')),
	array('label'=>'View Survey', 'url'=>array('view', 'id'=>$model->survey_id)),
	array('label'=>'Manage Survey', 'url'=>array('admin')),
);
?>

<h1>Update Survey <?php //echo $model->survey_id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model,'quArr'=>$quArr)); ?>