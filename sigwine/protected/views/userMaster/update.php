<?php
/* @var $this UserMasterController */
/* @var $model UserMaster */

$this->breadcrumbs=array(
	'User Masters'=>array('index'),
	$model->first_name=>array('view','id'=>$model->u_id),
	'Update',
);

$this->menu=array(
	/*array('label'=>'List UserMaster', 'url'=>array('index')),
	array('label'=>'Create UserMaster', 'url'=>array('create')),
	array('label'=>'View UserMaster', 'url'=>array('view', 'id'=>$model->u_id)),*/
	array('label'=>'Manage UserMaster', 'url'=>array('admin')),
);
?>
<style>
div#sidebar{display: none;}
</style>
<h1>Update Super Admin - <?php echo $model->first_name; ?></h1>
<?php $this->renderPartial('_form', array('model'=>$model)); ?>