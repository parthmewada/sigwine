<?php
	/*==================================================================
	 PayPal Express Checkout Call
	 ===================================================================
	*/
require_once ("paypalfunctions.php");
$wb= new Webservice();
$PaymentOption = "PayPal";
if ( $PaymentOption == "PayPal" )
{
	/*
	'------------------------------------
	' The paymentAmount is the total value of 
	' the shopping cart, that was set 
	' earlier in a session variable 
	' by the shopping cart page
	'------------------------------------
	*/
	
	$finalPaymentAmount =  $_SESSION["Payment_Amount"];
	
	/*
	'------------------------------------
	' Calls the DoExpressCheckoutPayment API call
	'
	' The ConfirmPayment function is defined in the file PayPalFunctions.jsp,
	' that is included at the top of this file.
	'-------------------------------------------------
	*/

	//$resArray = ConfirmPayment ( $finalPaymentAmount ); Remove comment with ontime payment.

	$resArray = CreateRecurringPaymentsProfile();

	$ack = strtoupper($resArray["ACK"]);
	
	if( $ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING" )
	{
                $status="Approved";
                $response=$resArray["PROFILEID"];
                $redirectURL="success.php";
                // update order data to order table
		/*
		'********************************************************************************************************************
		'
		' THE PARTNER SHOULD SAVE THE KEY TRANSACTION RELATED INFORMATION LIKE 
		'                    transactionId & orderTime 
		'  IN THEIR OWN  DATABASE
		' AND THE REST OF THE INFORMATION CAN BE USED TO UNDERSTAND THE STATUS OF THE PAYMENT 
		'
		'********************************************************************************************************************
		*/
		// commented block to make recurring payment work 
		/*$transactionId		= isset($resArray["TRANSACTIONID"]) ? $resArray["TRANSACTIONID"] :  $resArray["PROFILEID"]; // ' Unique transaction ID of the payment. Note:  If the PaymentAction of the request was Authorization or Order, this value is your AuthorizationID for use with the Authorization & Capture APIs. 
		$transactionType 	= $resArray["TRANSACTIONTYPE"]; //' The type of transaction Possible values: l  cart l  express-checkout 
		$paymentType		= $resArray["PAYMENTTYPE"];  //' Indicates whether the payment is instant or delayed. Possible values: l  none l  echeck l  instant 
		$orderTime 		= $resArray["ORDERTIME"];  //' Time/date stamp of payment
		$amt			= $resArray["AMT"];  //' The final amount charged, including any shipping and taxes from your Merchant Profile.
		$currencyCode		= $resArray["CURRENCYCODE"];  //' A three-character currency code for one of the currencies listed in PayPay-Supported Transactional Currencies. Default: USD. 
		$feeAmt			= $resArray["FEEAMT"];  //' PayPal fee amount charged for the transaction
		$settleAmt		= $resArray["SETTLEAMT"];  //' Amount deposited in your PayPal account after a currency conversion.
		$taxAmt			= $resArray["TAXAMT"];  //' Tax charged on the transaction.
		$exchangeRate		= $resArray["EXCHANGERATE"];  //' Exchange rate if a currency conversion occurred. Relevant only if your are billing in their non-primary currency. If the customer chooses to pay with a currency other than the non-primary currency, the conversion occurs in the customer’s account.
		
		/*
		' Status of the payment: 
				'Completed: The payment has been completed, and the funds have been added successfully to your account balance.
				'Pending: The payment is pending. See the PendingReason element for more information. 
		*/
		// commented to make recurring payment work 
		//$paymentStatus	= $resArray["PAYMENTSTATUS"]; 

		/*
		'The reason the payment is pending:
		'  none: No pending reason 
		'  address: The payment is pending because your customer did not include a confirmed shipping address and your Payment Receiving Preferences is set such that you want to manually accept or deny each of these payments. To change your preference, go to the Preferences section of your Profile. 
		'  echeck: The payment is pending because it was made by an eCheck that has not yet cleared. 
		'  intl: The payment is pending because you hold a non-U.S. account and do not have a withdrawal mechanism. You must manually accept or deny this payment from your Account Overview. 		
		'  multi-currency: You do not have a balance in the currency sent, and you do not have your Payment Receiving Preferences set to automatically convert and accept this payment. You must manually accept or deny this payment. 
		'  verify: The payment is pending because you are not yet verified. You must verify your account before you can accept this payment. 
		'  other: The payment is pending for a reason other than those listed above. For more information, contact PayPal customer service. 
		*/
		// commented to make recurring payment work 
		//$pendingReason	= $resArray["PENDINGREASON"];  

		/*
		'The reason for a reversal if TransactionType is reversal:
		'  none: No reason code 
		'  chargeback: A reversal has occurred on this transaction due to a chargeback by your customer. 
		'  guarantee: A reversal has occurred on this transaction due to your customer triggering a money-back guarantee. 
		'  buyer-complaint: A reversal has occurred on this transaction due to a complaint about the transaction from your customer. 
		'  refund: A reversal has occurred on this transaction because you have given the customer a refund. 
		'  other: A reversal has occurred on this transaction due to a reason not listed above. 
		*/
		
		//$reasonCode		= $resArray["REASONCODE"]; 
		
                $arr=array("msg"=>"Thank you for your payment.","status"=>$ack);
		//echo "Thank you for your payment.";
	}
	else  
	{
		
		$status="Declined";
                $response="";
		//Display a user friendly Error on the page using any of the following error information returned by PayPal
		$ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
		$ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
		$ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
		$ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);
		$redirectURL="fail.php";
		//echo "GetExpressCheckoutDetails API call failed. ";
		//echo "Detailed Error Message: " . $ErrorLongMsg;
		//echo "Short Error Message: " . $ErrorShortMsg;
		//echo "Error Code: " . $ErrorCode;
		//echo "Error Severity Code: " . $ErrorSeverityCode;
                $arr=array("msg"=>$ErrorLongMsg,"status"=>$ack);
	}

        $sql="update res_order_total set payment_status='".$status."',payment_response='".$response."' where order_total_id='".$_SESSION["last_id"]."'";
        $last_id=$wb->executeQuery($sql);
        // Send Order Email To customer
	//$res=$wb->sendOrderEmail($_SESSION['last_id'],$_SESSION['user_id']);

  ?>
        <html>
        <link href="<?php echo Yii::app()->request->getBaseUrl(true); ?>/css/paypal.css" rel="stylesheet" type="text/css">
        <body>
                <div class="woocommerce" id="content">
                <?php if($status=='Approved'){ ?>    
                <form class="checkout" action='success.php' METHOD='POST'>
                    <h3 id="order_review_heading"><h2>&nbsp;</h2></h3>
                    <div id="order_review">
                        <table width="100%" style="text-align: center;">
                            <tr><td class="cart-subtotal" >
                            <img style="vertical-align: middle;" src="<?php echo Yii::app()->request->getBaseUrl(true); ?>/images/button_ok.png">&nbsp;Thank you! You have successfully paid.
                            </td></tr>
                        </table>
                        <div id="payment">
                            <div class="form-row place-order">
                                 <input type="submit" data-value="Go to Orders " value="Go to Orders" id="go_order" name="woocommerce_checkout_place_order" class="button alt">
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </form>
                <?php }else{ ?>
                <form class="checkout" action='fail.php' METHOD='POST'>
                    <h3 id="order_review_heading"><h2>&nbsp;</h2></h3>
                    <div id="order_review">
                        <table width="100%" style="text-align: center;">
                            <tr><td class="cart-subtotal" >
                            <img style="vertical-align: middle;" src="<?php echo Yii::app()->request->getBaseUrl(true); ?>/images/not_ok.png">&nbsp;Sorry! There is some error while processing your payment.
                            </td></tr>
                        </table>
                        <div id="payment">
                            <div class="form-row place-order">
                                 <input type="submit" data-value="Go to Cart " value="Go to Cart" id="place_order" name="woocommerce_checkout_place_order" class="button alt">
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </form>    
                <?php } ?>
             </div>
            </body>
        </html>
<?php
//$headers = "From: SigWine <admin@sigwine.com>\r\n";
//$headers .= "Content-type: text/html\r\n";
//$subject = "Your Order ".$_SESSION["last_id"]." has been placed";
//// now lets send the email.
//$to=$userInfo['email'];
//
//if (mail($to, $subject, $mailBody, $headers)) {
//    return true;
//} else {
//    return false;
//}
}		
?>
