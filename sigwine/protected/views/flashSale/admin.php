<?php
/* @var $this FlashSaleController */
/* @var $model FlashSale */

$this->breadcrumbs=array(
	'Exclusive'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Exclusive', 'url'=>array('index')),
	array('label'=>'Create Exclusive', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#flash-sale-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Exclusive</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php //echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php 
echo $model->product_id;
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'flash-sale-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'flash_sale_id',
		'title',
                'Product.product_name',
		'sale_start_from',
		'sale_end',
		'status',
		array(  
                        'header'=>'Action',
			'class'=>'CButtonColumn',
		),
               /* array
                (
                    'header'=>'#',    
                    'class'=>'CButtonColumn',
                    'template'=>'{Publish}',
                    'buttons'=>array
                    (
                       'Publish' => array
                       (
                            'header'=>'Publish',
                            'visible'=>'date("Y-m-d",strtotime($data->sale_start_from))==date("Y-m-d")',
                            'url'=>'Yii::app()->createUrl("flashSale/sendPush", array("id"=>$data->flash_sale_id))',
                       ),
                    ),
               ),*/
	),
)); ?>
