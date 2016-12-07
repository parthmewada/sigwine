<?php
/* @var $this SurveyController */
/* @var $model Survey */

$this->breadcrumbs=array(
	'Surveys'=>array('index'),
	'Create',
);

$this->menu=array(
	/*array('label'=>'List Survey', 'url'=>array('index')),*/
	array('label'=>'Manage Survey', 'url'=>array('admin')),
);
?>

<h1>Create Survey</h1>
<font style="color:red;"><?php echo (isset($errMsg) && $errMsg!=''?$errMsg:""); ?></font>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>