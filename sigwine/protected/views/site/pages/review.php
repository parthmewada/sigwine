<?php

if (isset($_REQUEST['token']))
{
	$token = $_REQUEST['token'];
}
if ( $token != "" )
{

	require_once ("paypalfunctions.php");
        
	$resArray = GetShippingDetails( $token );
	
        $ack = strtoupper($resArray["ACK"]);
        
	if( $ack == "SUCCESS" || $ack == "SUCESSWITHWARNING") 
	{
		// insert in to res_order and res_order_total
  		/*$wb= new Webservice();
 		$updatesql="Update res_order_total set payment_status = 'Approved' where order_total_id = ".$_SESSION['last_id'];
        	$Updateid=$wb->executeQuery($updatesql,'db',2); */
		
		/*
		' The information that is returned by the GetExpressCheckoutDetails call should be integrated by the partner into his Order Review 
		' page		
		*/
		$email 			= $resArray["EMAIL"]; // ' Email address of payer.
		$payerId 		= $resArray["PAYERID"]; // ' Unique PayPal customer account identification number.
		$payerStatus		= $resArray["PAYERSTATUS"]; // ' Status of payer. Character length and limitations: 10 single-byte alphabetic characters.
		//$salutation		= $resArray["SALUTATION"]; // ' Payer's salutation.
		$firstName		= $resArray["FIRSTNAME"]; // ' Payer's first name.
		//$middleName		= $resArray["MIDDLENAME"]; // ' Payer's middle name.
		$lastName		= $resArray["LASTNAME"]; // ' Payer's last name.
		//$suffix		= $resArray["SUFFIX"]; // ' Payer's suffix.
		$cntryCode		= $resArray["COUNTRYCODE"]; // ' Payer's country of residence in the form of ISO standard 3166 two-character country codes.
		//$business		= $resArray["BUSINESS"]; // ' Payer's business name.
		//$shipToName		= $resArray["SHIPTONAME"]; // ' Person's name associated with this address.
		//$shipToStreet		= $resArray["SHIPTOSTREET"]; // ' First street address.
		//$shipToStreet2	= $resArray["SHIPTOSTREET2"]; // ' Second street address.
		//$shipToCity		= $resArray["SHIPTOCITY"]; // ' Name of city.
		//$shipToState		= $resArray["SHIPTOSTATE"]; // ' State or province
		//$shipToCntryCode	= $resArray["SHIPTOCOUNTRYCODE"]; // ' Country code. 
		//$shipToZip		= $resArray["SHIPTOZIP"]; // ' U.S. Zip code or other country-specific postal code.
		//$addressStatus 		= $resArray["ADDRESSSTATUS"]; // ' Status of street address on file with PayPal   
		//$invoiceNumber	= $resArray["INVNUM"]; // ' Your own invoice or tracking number, as set by you in the element of the same name in SetExpressCheckout request .
		//$phonNumber		= $resArray["PHONENUM"]; // ' Payer's contact telephone number. Note:  PayPal returns a contact telephone number only if your Merchant account profile settings require that the buyer enter one. 
	} 
	else  
	{
		//Display a user friendly Error on the page using any of the following error information returned by PayPal
		$ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
		$ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
		$ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
		$ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);
		
		echo "GetExpressCheckoutDetails API call failed. ";
		echo "Detailed Error Message: " . $ErrorLongMsg;
		echo "Short Error Message: " . $ErrorShortMsg;
		echo "Error Code: " . $ErrorCode;
		echo "Error Severity Code: " . $ErrorSeverityCode;
	}
}
?>
<html>
<link href="<?php echo Yii::app()->request->getBaseUrl(true); ?>/css/paypal.css" rel="stylesheet" type="text/css">
    
<body>
        <div class="woocommerce" id="content">
        <form class="checkout" action='order_confirm.php' METHOD='POST'>
            <h3 id="order_review_heading"><h2>Confirm your information</h2></h3>
            <div id="order_review">
                <table class="shop_table">
                    <tfoot>
                        <tr class="cart-subtotal">
                            <th>Name</th>
                            <td><span class="amount"><?php echo $lastName." ".$firstName; ?></span></td>
                        </tr>
                        <tr class="cart-subtotal">
                            <th>Email</th>
                            <td><span class="amount"><?php echo $email; ?></span></td>
                        </tr>
                    </tfoot>
                </table>
                <div id="payment">
                    <div class="form-row place-order">
                         <input type="submit" data-value="Sign Up Now " value="Confirm" id="place_order" name="woocommerce_checkout_place_order" class="button alt">
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </form>
     </div>
    </body>
</html>
