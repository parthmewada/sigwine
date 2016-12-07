<?php
require_once ("paypalfunctions.php");

$paymentAmount = $_REQUEST["payment_amount"];
$discountAmount = $_REQUEST["discount_amount"];

$order_total = $_REQUEST['order_total'];
$user_id = $_REQUEST['user_id'];
$subscription_plan_id = $_REQUEST['subscription_plan_id'];
$plan_name = $_REQUEST['plan_name'];
$subscription_duration = $_REQUEST['subscription_duration'];
$address_id = $_REQUEST['address_id'];

$_SESSION["user_id"] = $user_id;
$_SESSION["subscription_plan_id"] = $subscription_plan_id;
$_SESSION["plan_name"] = $plan_name;
$_SESSION["subscription_duration"] = $subscription_duration;
$_SESSION["address_id"] = $address_id;
$_SESSION["Payment_Amount"] = $paymentAmount;
$_SESSION["discount_amount"] = $discountAmount;

$desc="Recurring Payment for ".$plan_name." subscription plan ($".$paymentAmount." Monthly)";
$_SESSION["desc"]=$desc;
$currencyCodeType="USD";
$paymentType = "Sale";
$returnURL = "http://".$_SERVER["HTTP_HOST"]."/appadmin/index.php/payment/review.php";
$cancelURL = "http://".$_SERVER["HTTP_HOST"]."/appadmin/";
// INSERT ORDER DATA TO ORDER TABLE
$wb= new Webservice();

$payment_status="Pending";
$order_status="Pending";
$payment_response="";
$order_createdby=$_SESSION["user_id"];
$order_createdon=date("Y-m-d h;i:s");
$sql="INSERT INTO res_order_total 
        (user_id,order_total,payment_status,order_status,payment_response,order_createdby,order_createdon) 
        values ('".$user_id."','".$order_total."','".$payment_status."','".$order_status."',
       '".$payment_response."','".$order_createdby."','".$order_createdon."')";
$last_id=$wb->executeQuery($sql,'db',2);
$_SESSION['last_id']=$last_id;
$ins="INSERT INTO `res_orders` 
        (`order_total_id`, `user_id`, `subscription_plan_id`, 
        `subscription_duration`, `order_total`, `product_id`, `order_qty`, 
        `type`, `order_status`, `order_date`, `order_createdby`, `billing_address_id`, 
        `address_id`, `payment_method`) 
        VALUES(".$last_id.",'".$user_id."','".$subscription_plan_id."','auto','".$order_total."',
            '0','1','1','Pending','".$order_createdon."','".$user_id."','".$address_id."','".$address_id."','Paypal')";
$last_id=$wb->executeQuery($ins);
$resArray = CallShortcutExpressCheckout ($paymentAmount, $currencyCodeType, $paymentType, $returnURL, $cancelURL,$desc);
$ack = strtoupper($resArray["ACK"]);
if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING"){
    RedirectToPayPal ( $resArray["TOKEN"] );
}else{
    $ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
    $ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
    $ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
    $ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);

    echo "SetExpressCheckout API call failed. ";
    echo "Detailed Error Message: " . $ErrorLongMsg;
    echo "Short Error Message: " . $ErrorShortMsg;
    echo "Error Code: " . $ErrorCode;
    echo "Error Severity Code: " . $ErrorSeverityCode;
}
?>
