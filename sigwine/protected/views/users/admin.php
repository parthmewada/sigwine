<?php
/* @var $this UsersController */
/* @var $model Users */

$this->breadcrumbs=array(
	'Users'=>array('index'),
	'Manage',
);

$this->menu=array(array('label'=>'Create Users', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#users-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<h1>Manage App Users</h1>
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

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'users-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
                array('header'=>"User Id", 'name'=>'user_id', 'value'=>'$data->user_id'),
                array('header'=>"Name", 'name'=>'first_name', 'value'=>'$data->first_name'),
		'email',
                /*array('name'=>'password','value'=>'base64_decode($data->password)'),
		'profile_image',
		'language_preference',
		'created_on',
		'last_modified_on',
		*/
                array
                   (
                       'header'=>'Social Login',    
                       'class'=>'CButtonColumn',
                       'template'=>'{facebook}&nbsp;{webo}&nbsp;{wechat}',
                       'buttons'=>array
                       (
                          'facebook' => array
                          (
                               'visible'=>'($data->facebook_id!="")',
                               'imageUrl'=>Yii::app()->request->getBaseUrl(true).'/images/facebook.png', 
                               'options'=>array("class"=>"socialcls")
                               
                          ),
                           'webo' => array
                          (
                               'visible'=>'($data->webo_id!="")',
                               'imageUrl'=>Yii::app()->request->getBaseUrl(true).'/images/weibo.jpg', 
                               'options'=>array("class"=>"socialcls")
                               
                          ),
                           'wechat' => array
                          (
                               'visible'=>'($data->wechat_id!="")?true:false;',
                               'imageUrl'=>Yii::app()->request->getBaseUrl(true).'/images/wechat.png', 
                               'options'=>array("class"=>"socialcls")
                               
                          ),
                       ),
                  ),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
