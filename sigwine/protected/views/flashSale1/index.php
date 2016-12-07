<?php
/* @var $this FlashSaleController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Exclusive',
);

$this->menu=array(
	array('label'=>'Create Exclusive', 'url'=>array('create')),
	array('label'=>'Manage Exclusive', 'url'=>array('admin')),
);
?>

<h1>Exclusive</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
