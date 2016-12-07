<?php
/* @var $this ResOrderTotalController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Res Order Totals',
);

$this->menu=array(
	/*array('label'=>'Create ResOrderTotal', 'url'=>array('create')),*/
	array('label'=>'Manage Orders', 'url'=>array('admin')),
);
?>

<h1>Res Order Totals</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
