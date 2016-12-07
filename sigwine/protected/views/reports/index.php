<h1>Reports</h1>
<?php
$static = array(
 'all'     => '-- All --'
);
$selected = array('0' => array('selected' => 'selected'));

if($pid != '') {
    $selected = array($pid => array('selected' => 'selected'));
   
} 
$sid=(isset($_REQUEST["sid"]) && $_REQUEST["sid"]!=""?$_REQUEST["sid"]:"all");
if(!isset($_REQUEST["order"])) 
	$order_date = '';
if(!isset($_REQUEST["payment"]))
	$payment_date = '';

?>
<style type="text/css">
    .tftable {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #a9a9a9;border-collapse: collapse;}
    .tftable th {font-size:12px;background-color:#b8b8b8;border-width: 1px;padding: 8px;border-style: solid;border-color: #8C0E3A;text-align:left;}
    .tftable tr {background-color:#ffffff;}
    .tftable td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #a9a9a9;}
    .intftable td{border-width: 0px;}
    .buttons{text-align: center;}
    .buttons{
        background: #8D0E3A none repeat scroll 0 0;
        border: medium none !important;
        color: #fff;
        cursor: pointer;
        padding: 7px 15px;
    }
</style>
<table width="45%" style="width:45%;">
    <tr>
        <td>View Subscription Reports</td>
        <td><?php echo CHtml::activeDropDownList(SubscriptionPlan::model(), 'plan_id',
                $static + CHtml::listData(SubscriptionPlan::model()->findAll(),"plan_id", "plan_name"),
                array('onChange' => 'reportFilter(this.value);','options' => $selected));
        ?>
        </td>
        <td><input type="button" class="buttons" value="Export" onclick="export_data()"></td>
    </tr>
    <tr>
        <td>View Orders</td>
        <td>
            <select id="datefilter" name="datefilter" onchange="orderFilter(this.value)">
                <option value="">-- Select Days --</option>
                <option value="today"  <?php if($order_date == 'today'){ echo "selected";}?>>Today</option>
                <option value="last 7 day" <?php if($order_date == 'last 7 day'){ echo "selected";}?>>Last 7 Days</option>
                <option value="this month" <?php if($order_date == 'this month'){ echo "selected";}?>>This Month</option>
            </select>
        </td>
        <td><input type="button" class="buttons" value="Export" onclick="export_orderdata()"></td>
    </tr>
    <tr>
        <td>View Payment</td>
        <td>
            <select id="datefilter1" name="datefilter1" onchange="paymentFilter(this.value)">
                <option value="">-- Select Days --</option>
                <option value="today"  <?php if($payment_date == 'today'){ echo "selected";}?>>Today</option>
                <option value="last 7 day" <?php if($payment_date == 'last 7 day'){ echo "selected";}?>>Last 7 Days</option>
                <option value="this month" <?php if($payment_date == 'this month'){ echo "selected";}?>>This Month</option>
            </select>
        </td>
        <td>
            <input type="button" class="buttons" value="Export" onclick="export_paymentdata()">
        </td>
    </tr>
</table>
<div class="grid-view" id="driver-grid">
	    
            <?php if(isset($data)) {  if(count($data)>0 || (isset($_REQUEST["sid"]))){?>
            <table class="items">
                  <thead><tr><th  colspan="7" id="grid_c0">View Subscription</th></tr>    
                    <tr>
                        <th id="grid_c0">#</th>
                        <th id="grid_c1">User Id</th>
                        <th id="grid_c2">User Name</th>
                        <th id="grid_c3">Subscription Name</th>
                        <th id="grid_c4">Subscription Type</th>
                        <th id="grid_c5">Duration (Month)</th>
                        <th id="grid_c6">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $j=1;
                    if(count($data)>0){?>
                        <?php for($i=0; $i<count($data); $i++){?>
                        <tr class="<?php if($i%2==0){echo "odd";}else{echo "even";}?>">
                            <td><?php echo $j++; ?></td>
                            <td><?php echo $data[$i]['user_id']?></td>
                            <td><?php echo $data[$i]['user_name']?></td>
                            <td><?php echo $data[$i]['subscription_name']?></td>
                            <td><?php echo ($data[$i]['plan_type']=='two_bottles'?"Two Bottles":"Three Bottles");?></td>
                            <td><?php echo $data[$i]['duration']?></td>
                            <td><?php echo $data[$i]['orderdate']?></td>
                        </tr>
                        <?php }?>
                    <?php }else{?>
                        <tr class="even"><td colspan="7" style="text-align: center;">No record found!</td></tr>
                    <?php }?>
                </tbody>
            </table>
	    <?php } } ?>
            <?php if(isset($_REQUEST["order"]) && $_REQUEST["order"]!=""){?>
            <table class="items">
                  <thead><tr><th  colspan="7" id="grid_c0">View Orders</th></tr>    
                    <tr>
                        <th id="grid_c0">#</th>
                        <th id="grid_c1">User Id</th>
                        <th id="grid_c2">User Name</th>
                        <th id="grid_c3">Product Name</th>
                        <th id="grid_c4">Order Qty</th>
                        <th id="grid_c5">Order Total(&yen;)</th>
                        <th id="grid_c6">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $j=1;
                    if(count($orderdata)>0){?>
                        <?php for($i=0; $i<count($orderdata); $i++){?>
                        <tr class="<?php if($i%2==0){echo "odd";}else{echo "even";}?>">
                            <td><?php echo $j++; ?></td>
                            <td><?php echo $orderdata[$i]['user_id']?></td>
                            <td><?php echo $orderdata[$i]['user_name']?></td>
                            <td><?php echo $orderdata[$i]['product_name']?></td>
                            <td><?php echo $orderdata[$i]['order_qty'];?></td>
                            <td><?php echo $orderdata[$i]['order_total']?></td>
                            <td><?php echo $orderdata[$i]['orderdate']?></td>
                        </tr>
                        <?php }?>
                    <?php }else{?>
                        <tr class="even"><td colspan="7" style="text-align: center;">No record found!</td></tr>
                    <?php }?>
                </tbody>
            </table>
	    <?php } ?> 
            <?php if(isset($paymentdata)) { if(isset($_REQUEST["payment"]) && $_REQUEST["payment"]!=""){?>
            <table class="items">
                  <thead><tr><th  colspan="7" id="grid_c0">View Payments</th></tr>    
                    <tr>
                        <th id="grid_c0">#</th>
                        <th id="grid_c1">User Id</th>
                        <th id="grid_c2">User Name</th>
                        <th id="grid_c3">Order Total(&yen;)</th>
                        <th id="grid_c4">Order Status</th>
                        <th id="grid_c5">Payment Status</th>
                        <th id="grid_c6">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $j=1;
                    if(count($paymentdata)>0){?>
                        <?php for($i=0; $i<count($paymentdata); $i++){?>
                        <tr class="<?php if($i%2==0){echo "odd";}else{echo "even";}?>">
                            <td><?php echo $j++; ?></td>
                            <td><?php echo $paymentdata[$i]['user_id']?></td>
                            <td><?php echo $paymentdata[$i]['user_name']?></td>
                            <td><?php echo $paymentdata[$i]['order_total']?></td>
                            <td><?php echo $paymentdata[$i]['order_status'];?></td>
                            <td><?php echo $paymentdata[$i]['payment_status']?></td>
                            <td><?php echo $paymentdata[$i]['order_createdon']?></td>
                        </tr>
                        <?php }?>
                    <?php }else{?>
                        <tr class="even"><td colspan="7" style="text-align: center;">No record found!</td></tr>
                    <?php }?>
                </tbody>
            </table>
	    <?php } } ?> 
</div>
<script type="text/javascript">
   
    function reportFilter(id){ 
        window.location.href='<?php echo Yii::app()->baseUrl?>/index.php/Reports/subscriptionreport/?sid='+id;
    }
    function orderFilter(id){ 
        window.location.href='<?php echo Yii::app()->baseUrl?>/index.php/Reports/orderreport/?order='+id;
    }
    function paymentFilter(id){ 
        window.location.href='<?php echo Yii::app()->baseUrl?>/index.php/Reports/paymentreport/?payment='+id;
    }
    function export_data(){
       window.location.href='<?php echo Yii::app()->baseUrl?>/index.php/Reports/export/?sid=<?php echo $sid?>';
    }
    function export_orderdata(){
       <?php  if(isset($order_date) && $order_date!=""){  ?>
       window.location.href='<?php echo Yii::app()->baseUrl?>/index.php/Reports/exportorder/?order=<?php echo $order_date?>';
       <?php }else{ ?>
           alert("Please select days to view order report!");
           return false;
       <?php } ?>
    }
    function export_paymentdata(){
         <?php if(isset($payment_date) && $payment_date!=""){  ?>
            window.location.href='<?php echo Yii::app()->baseUrl?>/index.php/Reports/exportpayment/?payment=<?php echo $payment_date?>';
        <?php }else{ ?>
           alert("Please select days to view payment report!");
           return false;
       <?php } ?>
    }
</script>