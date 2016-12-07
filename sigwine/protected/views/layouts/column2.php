<?php /* @var $this Controller */ ?>

<?php $this->beginContent('//layouts/main'); ?>
<div class="span-19">
	<div id="content">
		<?php echo $content; ?>
	</div><!-- content -->
</div>
<div class="span-5 last">
	<div id="sidebar">
    <div class="ac_heading">Operations  <span class="pull_right"><i class="fa fa-caret-down"></i></span></div>
    <br/>
      <div class="content">
	<?php
		$this->beginWidget('zii.widgets.CPortlet', array(
			//'title'=>'Operations',
		));
		$this->widget('zii.widgets.CMenu', array(
			'items'=>$this->menu,
			'htmlOptions'=>array('class'=>'operations'),
		));
		$this->endWidget();
	?>
     </div>
	</div><!-- sidebar -->
</div>
<?php $this->endContent(); ?>
<script type="text/javascript">
$(".content:not(:eq(2))").hide();
$(document).click( function(e){
	if($(e.target).attr('class')=='ac_heading')
	{$('.content').slideToggle();}
	else
	{ $('.content').slideUp()
	}
});

</script>
