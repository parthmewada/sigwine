<?php
/* @var $this UserMasterController */
/* @var $model UserMaster */


$this->menu=array(
	//array('label'=>'List UserMaster', 'url'=>array('index')),
//	array('label'=>'Manage UserMaster', 'url'=>array('admin')),
);
?>

<h1>User Registration</h1>

<?php echo $this->renderPartial('_form_1', array('model'=>$model)); ?>