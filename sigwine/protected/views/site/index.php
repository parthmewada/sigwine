<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
//$data = json_decode(Yii::app()->bitly->shorten('http://www.betaworks.com')->getResponseData(),true);

//$shorturl = ($data["status_code"]== '200') ? $data["data"]["url"] : '';
//print_r($shorturl);
?>

<h1>Welcome <?php echo $username ;?></h1>
<div class="form clearfix" style="margin-top: 25px;">
    	
        <ul class="dash-user clearfix">
        	<li class="bg1 first">
                <a href="<?php echo Yii::app()->baseUrl?>/index.php/Users/admin">
            	<div class="info_box_body">
                    <?php $tuser=Users::model()->findAll(array('select'=>'user_id')); 
                        $totaluser=count($tuser);
                    ?>
                	<span class="count-txt"><?php echo $totaluser?></span>
                    <span class="icn-user"><img src="<?php echo Yii::app()->request->baseUrl?>/images/user-icn.png" alt="" /></span>
                </div>
                <div class="info_box_footer">
                 	<span>Total Users</span>
                </div>
                </a>
            </li>
             <li class="bg4">
                <a href="<?php echo Yii::app()->baseUrl?>/index.php/Product/admin?Product[product_type]=wine">
            	<div class="info_box_body">
                       <?php $tproduct=Product::model()->findAll('product_type="wine"',array('select'=>'product_id')); 
                             $totalproduct=count($tproduct);
                       ?>
                	<span class="count-txt"><?php echo $totalproduct?></span>
                   <span class="icn-user"><img src="<?php echo Yii::app()->request->baseUrl?>/images/wine.png" alt="" /></span>
                </div>
                <div class="info_box_footer">
                 	<span>Total Wines</span>
                </div>
                </a>
            </li>
            <li class="bg3">
                <a href="<?php echo Yii::app()->baseUrl?>/index.php/Product/admin?Product[product_type]=glassware">
            	<div class="info_box_body">
                       <?php $tproduct=Product::model()->findAll('product_type="glassware"',array('select'=>'product_id')); 
                             $totalproduct=count($tproduct);
                       ?>
                	<span class="count-txt"><?php echo $totalproduct?></span>
                   <span class="icn-user"><img src="<?php echo Yii::app()->request->baseUrl?>/images/glassware-icon.png" alt="" /></span>
                </div>
                <div class="info_box_footer">
                 	<span>Total Glasswares</span>
                </div>
                </a>
            </li>
<!--            <li class="bg3">
                <a href="<?php echo Yii::app()->baseUrl?>/index.php/serviceprovided/admin">
            	<div class="info_box_body">
                        <?php $tsubscription=Users::model()->findAll(array('select'=>'user_id')); 
                            $totalsubscription=count($tuser);
                        ?>
                	<span class="count-txt"><?php echo $totalsubscription?></span>
                    <span class="icn-user"><img src="<?php echo Yii::app()->request->baseUrl?>/images/driver-icn.png" alt="" /></span>
                </div>
                <div class="info_box_footer">
                 	<span>Total Subscriptions</span>
                </div>
                </a>
            </li>-->
           
            <li class="bg2">
                <a href="<?php echo Yii::app()->baseUrl?>/index.php/resOrderTotal/admin">
            	<div class="info_box_body">
                        <?php $torder=ResOrderTotal::model()->findAll(array('select'=>'order_total_id')); 
 
                           $totalorder=count($torder);
                       ?>
                	<span class="count-txt"><?php echo $totalorder?></span>
                    <span class="icn-user"><img src="<?php echo Yii::app()->request->baseUrl?>/images/booking-icn.png" alt="" /></span>
                </div>
                <div class="info_box_footer">
                 	<span>Total Orders</span>
                </div>
                </a>
            </li>
            <!--<li class="bg5 last">
            	<div class="info_box_body">
                	<span class="count-txt"><?php //echo $totalpreride?></span>
                    <span class="icn-user"><img src="<?php //echo Yii::app()->request->baseUrl?>/images/pre-booking-icn.png" alt="" /></span>
                </div>
                <div class="info_box_footer">
                 	<span>Today Pre-Booking</span>
                </div>
            </li>
             <li class="bg3">
                 <a href="<?php //echo Yii::app()->baseUrl?>/index.php/DriverProfile/admin"><div class="info_box_body">
                	<span class="count-txt"><?php //echo $totalpendingdriver?></span>
                    <span class="icn-user"><img src="<?php //echo Yii::app()->request->baseUrl?>/images/driver-icn.png" alt="" /></span>
                </div>
                <div class="info_box_footer">
                 	<span>Drivers Pending Approval</span>
                </div></a>
            </li>-->
        </ul>
</div>