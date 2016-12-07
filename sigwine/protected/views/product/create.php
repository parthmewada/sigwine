<?php
/* @var $this ProductController */
/* @var $model Product */

$this->breadcrumbs=array(
	'Wine & Glassware'=>array('index'),
	'Create',
);

$this->menu=array(
	/*array('label'=>'List Product', 'url'=>array('index')),*/
	array('label'=>'Manage Product', 'url'=>array('admin')),
);
?>

<h1>Create Wine & Glassware</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model,'model1'=>$model1)); ?>