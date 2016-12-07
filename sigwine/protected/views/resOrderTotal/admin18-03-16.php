<?php
/* @var $this ResOrderTotalController */
/* @var $model ResOrderTotal */

$this->breadcrumbs=array(
	'Manage Orders'=>array('index')
	
);

$this->menu=array(
	/*array('label'=>'List Orders', 'url'=>array('index')),
	array('label'=>'Create Orders', 'url'=>array('create')),*/
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#res-order-total-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Orders</h1>
<style>
    .button-column{color:#6d6d6d !important}
    .main_container .span-5{display:none;}
</style>
<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php // echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'res-order-total-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'order_total_id',
                array('header'=>"User Id", 'name'=>'user_id', 'value'=>'$data->user_id'),
                array('header'=>"User Info", 'name'=>'user_id', 'value'=>'$data->users->first_name'),
		'order_total',
		'payment_status',
                array(
                'header'=>'Order Status',
                'name'=>'order_status',
                'type'=>'raw',
                 'filter'=>false,
                'value'=>'CHtml::dropDownList("","order_status",
                    array("Pending"=>"Pending","Delivered"=>"Delivered"),
                    array("id"=>"order_status_$data->order_total_id","onchange"=>"changestatus(this.value,$data->order_total_id)",
                    "options" => array(ResOrderTotal::model()->findByAttributes(array("order_total_id" => "$data->order_total_id"))->order_status => array("selected" => true)),
                    ))',
                ),
		
		/*
		'order_createdby',*/
		'order_createdon',
		array(
                    'class' => 'CButtonColumn',
                    'template' => '{view} ',
                    'header' => 'Action',
                ),
	),
)); ?>
<script type="text/javascript">
function changestatus(val,id){
    var r = confirm("Do you want to "+val+" this Order?");
    if(r == true){
            $.ajax({
                type: "POST",
                url:  "<?php echo Yii::app()->createUrl('resOrderTotal/changestatus'); ?>",
                data: {order_total_id:id,val:val,expire:0},
                success: function(msg){
                    if(msg == 'success'){
                        alert("Your order status has been changed successfully.");
                    }else{
                        alert("Your order status has not been changed...please try again");
                    }
                    return false;
                },
                error: function(xhr){
                  alert("failure"+xhr.readyState+this.url)
                }
           });
    }
    else {
        if(val=="Pending"){
            $("select#order_status_"+id+" option[value='Delivered']").attr("selected","selected");
        }else{
             $("select#order_status_"+id+" option[value='Pending']").attr("selected","selected");
        }
        return false;
    }
}
</script>