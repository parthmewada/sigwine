<?php /* @var $this Controller */ ?>
<?php  

if(isset($_GET["view"]) && @$_GET["view"] = 'scrumboard') {  ?>
<?php echo $content; ?>    
<?php } else { ?>
    <?php $this->beginContent('//layouts/main'); ?>
<div id="content">
	<?php echo $content; ?>
</div><!-- content -->
<?php $this->endContent(); ?>
<?php } ?>
