<?php /* @var $this Controller */  
    $session = new CHttpSession;
    $session->open();
    $pid = ($session["pid"] != '') ? $session["pid"]: @$_GET["pid"] ; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->
          <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/font-awesome/css/font-awesome.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main_v2.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery-ui-1.10.3.custom/css/ui-lightness/jquery-ui-1.10.3.custom.css" />
        <script type="text/javascript" src="<?php echo Yii::app()->baseUrl.'/css/jquery-ui-1.10.3.custom/js/jquery-1.9.1.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo Yii::app()->baseUrl.'/css/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo Yii::app()->baseUrl.'/css/jquery-ui-1.10.3.custom/js/jquery.validate.js'; ?>"></script>
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<?php
    if (Yii::app()->user->id >= '1') {
        $page_class = "after_login";
        $status = 1 ;
    } else {
        $page_class = "before_login";
        $status = 0;
    }
    ?>
    <body class="<?php echo $page_class; ?>">
    <!--[if IE 8]>
  <style>
.grid-view table.items tr th, .grid-view table.items tr td {
  
   white-space: pre-wrap;;
   word-break:break-all;

}
  </style>
<![endif]-->

        <div id="wrap">
            <div class="side_bar_border"> &nbsp;</div>
            <div id="header">
                <div id="logo"><?php // echo CHtml::encode(Yii::app()->name);  ?>
                <div class="for_logo">
                    <a href="#"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/logo1.png" /></a>
                    </div>
                    
                    <div class="right_content">
                    <?php if ($status == 1) { ?>
                    <div class="up_right_links">
                        <div id="upper_links" class="up_links" >
                            <a href="<?php echo Yii::app()->request->baseUrl;?>/index.php/site/index"><i class="fa fa-home"></i></a>
                            <span class="mybtn"><i class="fa fa-bars"></i></span>
                        </div>
                        <div id="login">
                            <?php
                            $user = Yii::app()->user; // just a convenience to shorten expressions

                            $this->widget('zii.widgets.CMenu', array(
                                'items' => array(
                                    array('label' => 'Login', 'url' => array('/site/login'), 'visible' => !Yii::app()->user->isGuest),
                                     array('label'=>'', 'url'=>array('/site/logout?rid='.Yii::app()->user->id), 
									 'linkOptions'=>array('class'=>'fa fa-sign-out logout', 'title'=>'Logout') ,
                                    'visible'=>(Yii::app()->user->id == '1' || Yii::app()->user->id == '2' || Yii::app()->user->id == '3' || Yii::app()->user->id == '4'))
                                ),
                            ));
                            ?>
                        </div>
                    </div>
                    <?php } ?>
                    <span class="usernname">Welcome , <?php echo $session["fullname"]; ?></span>
                    </div>
                </div>
            </div><!-- header -->
            <div id="mainmenu"  class="mainnav">
                <?php
                    $cContro = Yii::app()->controller->id ;
                    $act = Yii::app()->controller->action->id ;
                ?>
                
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
                            array('label'=>'Home', 
                                        'url'=>array('/site/index'), 
                                        'visible'=>Yii::app()->user->id == '1',
                                        'itemOptions'=>array('class'=> ($cContro == 'Home') ? 'active':'')
                                ),
                            array('label'=>'Manage Profile', 
                                        'url'=>array('/userMaster/update/1'), 
                                        'visible'=>Yii::app()->user->id == '1',
                                        'itemOptions'=>array('class'=> ($cContro == 'userMaster') ? 'active':'')
                                ),
                                array('label'=>'Manage App User', 
                                        'url'=>array('/Users/admin'), 
                                        'visible'=>Yii::app()->user->id == '1',
                                        'itemOptions'=>array('class'=> ($cContro == 'Users') ? 'active':'')
                                ),
                                array('label'=>'Manage Wines & glassware', 
                                           'url'=>array('/Product/admin'), 
                                           'visible'=>Yii::app()->user->id == '1',
                                           'itemOptions'=>array('class'=> ($cContro == 'Product') ? 'active':'')
                                ),
                                array('label'=>'Manage Subscription Plan', 
                                        'url'=>array('/SubscriptionPlan/admin'), 
                                        'visible'=>Yii::app()->user->id == '1',
                                        'itemOptions'=>array('class'=> ($cContro == 'SubscriptionPlan') ? 'active':'')
                                ),
                                array('label'=>'Subscription Plan Detail',
                                        'url'=>array('/SubscriptionPlanDetail/admin'), 
                                        'visible'=>Yii::app()->user->id == '1',
                                        'itemOptions'=>array('class'=> ($cContro == 'SubscriptionPlanDetail') ? 'active':'')
                                ),
                                array('label'=>'Manage Orders & Reorders',
                                            'url'=>array('/resOrderTotal/admin'), 
                                            'visible'=>Yii::app()->user->id == '1',
                                            'itemOptions'=>array('class'=> ($cContro == 'Orders') ? 'active':'')
                                ),
                                array('label'=>'Exclusive Offers & Events',
                                        'url'=>array('/FlashSale/admin'), 
                                        'visible'=>Yii::app()->user->id == '1',
                                        'itemOptions'=>array('class'=> ($cContro == 'FlashSale') ? 'active':'')
                                ),
                                 array('label'=>'Manage Education',
                                        'url'=>array('/Education/admin'), 
                                        'visible'=>Yii::app()->user->id == '1',
                                        'itemOptions'=>array('class'=> ($cContro == 'Education') ? 'active':'')
                                ),
                                array('label'=>'Manage Survey',
                                    'url'=>array('/Survey/admin'), 
                                    'visible'=>Yii::app()->user->id == '1',
                                    'itemOptions'=>array('class'=> ($cContro == 'Survey') ? 'active':'')
                                ),
                                array('label'=>'Reports',
                                    'url'=>array('/Reports/index'), 
                                    'visible'=>Yii::app()->user->id == '1',
                                    'itemOptions'=>array('class'=> ($cContro == 'Reports') ? 'active':'')
                                ),
                                array('label'=>'Manage Templates',
                                    'url'=>array('/EmailTemplate/admin'), 
                                    'visible'=>Yii::app()->user->id == '1',
                                    'itemOptions'=>array('class'=> ($cContro == 'EmailTemplate') ? 'active':'')
                                ),
				array('label'=>'Push Notifications',
                                    'url'=>array('/Users/sendnotifications'), 
                                    'visible'=>Yii::app()->user->id == '1',
                                    'itemOptions'=>array('class'=> ($cContro == 'Notifications') ? 'active':'')
                                ),
                              	array('label'=>'Manage Policy Content',
                                    'url'=>array('/policycontent/admin'), 
                                    'visible'=>Yii::app()->user->id == '1',
                                    'itemOptions'=>array('class'=> ($cContro == 'policycontent') ? 'active':'')
                                ),
				array('label'=>'Manage Discount',
                                    'url'=>array('/discount/update/1'),
                                    'visible'=>Yii::app()->user->id == '1',
                                    'itemOptions'=>array('class'=> ($cContro == 'discount') ? 'active':'')
                                ),
                                           
			),
                    )); ?>
            </div><!-- mainmenu -->
            <?php if(isset($this->breadcrumbs)):?>
                    <?php $this->widget('zii.widgets.CBreadcrumbs', array(
                            'links'=>$this->breadcrumbs,
                            'homeLink'=> (Yii::app()->user->id == '1') ?
                                        '<a href="'.Yii::app()->baseUrl.'/index.php/site/index">Home</a>' : '')); ?>
            <!-- breadcrumbs -->
            <?php endif?>
            <div class="main_container">
                <?php echo $content; ?>
            </div>
            <div class="clear"></div>
        </div><!-- page -->
        <div id="footer">
		Copyright &copy; <?php echo date('Y'); ?> 
All Rights Reserved to <a href="" target="_blank"> SigWine </a>
  <!-- <span class="foot"><br /></span>
&nbsp; &nbsp; Powered By <a href="http://www.credencys.com/" target="_blank">Credencys Solutions Inc.</a>-->
		
	</div>
        <!-- footer -->
    <script type="text/javascript">
$(document).ready(function(){
	var winWidth = $(window).width();
  if (winWidth <768) { $("#mainmenu:not(:eq(1))").hide();}
  $(".mybtn").click(function(){
   // $("#mainmenu").toggleClass("main_nav1");
	$("#mainmenu").slideToggle();
  });
});
</script>
    </body>
</html>
