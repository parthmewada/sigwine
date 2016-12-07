<?php
    $wb=new Webservice();
    $discountAmt = 0;
    $discountPerc = 0;	
    $order_total = $_REQUEST['order_total'];
    $user_id = $_REQUEST['user_id'];
    $subscription_plan_id = $_REQUEST['subscription_plan_id'];
    $row=$wb->getRowData("select plan_name from subscription_plan where plan_id='".$subscription_plan_id."'");
    $plan_name = $row['plan_name'];
    $checkRow=false;
    //$checkRow=$wb->getRowData("SELECT *  from res_orders where subscription_plan_id='".$subscription_plan_id."' and user_id='".$user_id."'");
    if(!$checkRow){
        $flag=true;
            
        $subscription_duration = $_REQUEST['subscription_duration'];
        $address_id = $_REQUEST['address_id'];
        $_SESSION['Payment_Amount'] = $order_total;
        $_SESSION['plan_name'] = $plan_name;
	#### Dicount Amount code ###
	$sqlallOrders = "Select count(*) As numofrows from res_order_total  where user_id = '". $user_id ."' AND payment_status IN('Approved','Pending')";
	
	$CountOrders = $wb->getAllData($sqlallOrders);
      	if($CountOrders[0]['numofrows'] == '0')
         {
            $sqlgetDiscount = "Select discount_perc from discount where status = 'Active'";
            $rowDiscount = $wb->getAllData($sqlgetDiscount);
             	    
            if($rowDiscount)
              $discountPerc = $rowDiscount[0]['discount_perc'];
	}  
	###### END CODE ######	        
	$dollarValue=(isset($_REQUEST["currancy_rate"]) && $_REQUEST["currancy_rate"]!=""?$_REQUEST["currancy_rate"]:"1");
        if($dollarValue>0){
            $dollarValue=  number_format($dollarValue,2);
        }else{
            $dollarValue=1;
        }
        $payment_amount=round(($order_total*$dollarValue),2);
	 if($discountPerc != '0')
              $discountAmt =  ($payment_amount * $discountPerc) / 100;
 	 }else{
	$flag=false;
    }
    //$payment_amount=1;
    ?>    
<html>
    <link href="<?php echo Yii::app()->request->getBaseUrl(true); ?>/css/paypal.css" rel="stylesheet" type="text/css">
    <body>
        <div class="woocommerce" id="content">
          <?php if(!$flag){ ?>    
                <form class="checkout" action='success.php' METHOD='POST'>
                    <h3 id="order_review_heading"><h2>&nbsp;</h2></h3>
                    <div id="order_review">
                        <table width="100%" style="text-align: center;">
                            <tr><td class="cart-subtotal" >
                            <img style="vertical-align: middle;" src="<?php echo Yii::app()->request->getBaseUrl(true); ?>/images/button_ok.png">&nbsp;You have already subscribed to <?php echo $plan_name; ?>! 
                            </td></tr>
                        </table>
                        <div id="payment">
                            <div class="form-row place-order">
                                 <input type="submit" data-value="Go to Orders " value="Go to Orders" id="place_order" name="woocommerce_checkout_place_order" class="button alt">
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </form>
                <?php }else{ ?>   
            <form class="checkout" action='expresscheckout.php' METHOD='POST'>
            <input type="hidden" name="payment_amount" value="<?php echo $payment_amount; ?>"/> 	
	    <!-- <input type="hidden" name="payment_amount" value="0.1"/> -->		
	    <input type="hidden" name="discount_amount" value="<?php echo $discountAmt; ?>"/>
            <input type="hidden" name="order_total" value="<?php echo $order_total; ?>"/>
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>"/>
            <input type="hidden" name="subscription_plan_id" value="<?php echo $subscription_plan_id; ?>"/>
            <input type="hidden" name="address_id" value="<?php echo $address_id; ?>"/>
            <input type="hidden" name="subscription_duration" value="<?php echo $subscription_duration; ?>"/>
	    <input type="hidden" name="plan_name" value="<?php echo $plan_name; ?>"/>
	    <h3 id="order_review_heading">Your order</h3>
            <div id="order_review">
                <table class="shop_table">
                    <thead>
                        <tr>
                            <th class="product-name">Product</th>
                            <th class="product-total">Total</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="cart-subtotal">
                            <th>Cart Subtotal</th>
                            <td><span class="amount">&yen;<?php echo $order_total; ?></span> / month</td>
                        </tr>
                        <tr class="order-total">
                            <th>Order Total</th>
                            <td><strong><span class="amount">&yen;<?php echo $order_total; ?></span> / month</strong> </td>
                        </tr>
			<tr class="order-total-paypal">
                            <th>Paypal USD Conversion<?php if($discountAmt != '0') {?><br>Discount Amount in USD is: <?php } ?> <br>Today's rate is: USD <?php echo $dollarValue; ?>= &yen;1 </th>
                            <td> <?php if($discountAmt != '0') { echo "$ ".$discountAmt."<br>"; } ?>$ <?php echo $payment_amount = $payment_amount - $discountAmt ;?></td>
                        </tr>
                    </tfoot>
                    <tbody>
                        <tr class="cart_item">
                            <td class="product-name">
                                <?php echo $plan_name; ?><strong class="product-quantity">* 1</strong>															</td>
                            <td class="product-total">
                            <span class="subscription-price"> <span class="amount">&yen;<?php echo $order_total; ?></span> / month</span>							</td>
                        </tr>
                    </tbody>
                </table>
                <div id="payment">
                    <ul class="payment_methods methods">
                        <li class="payment_method_paypal">
                            <input type="radio" data-order_button_text="Proceed to PayPal" checked="checked" value="paypal" name="payment_method" class="input-radio" id="payment_method_paypal">
                            <label for="payment_method_paypal">PayPal  <img alt="Credit Card &amp; PayPal " src="../../images/paypal.png"></label>
                            <div class="payment_box payment_method_paypal"><p>You will be charged in USD.</p></div>
                            <div class="payment_box payment_method_paypal"><p>Your payment will be deducated by PayPal each month till you stop it.</p></div>
                        </li>
                    </ul>
                    <div class="form-row place-order">
                         <input type="submit" data-value="Sign Up Now " value="Proceed to PayPal" id="place_order" name="woocommerce_checkout_place_order" class="button alt">
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </form>
                <?php } ?>
     </div>
    </body>
</html>
