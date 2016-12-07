<?php
/* @var $this PolicycontentController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Policycontents',
);

$this->menu=array(
//	array('label'=>'Create Policycontent', 'url'=>array('create')),
	array('label'=>'Manage Policycontent', 'url'=>array('admin')),
);
?>

<h1>Policycontents</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
