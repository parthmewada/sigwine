<?php

/* @var $this ResOrderTotalController */
/* @var $model ResOrderTotal */

$this->breadcrumbs = array(
    'Orders' => array('index')
);

$this->menu = array(
    /* array('label'=>'List ResOrderTotal', 'url'=>array('index')),
      array('label'=>'Create ResOrderTotal', 'url'=>array('create')),
      array('label'=>'Update ResOrderTotal', 'url'=>array('update', 'id'=>$model->order_total_id)),
      array('label'=>'Delete ResOrderTotal', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->order_total_id),'confirm'=>'Are you sure you want to delete this item?')),
     */
    array('label' => 'Manage Orders', 'url' => array('admin')),
);
?>
<h1>View Order</h1>
<style type="text/css">
    .tftable {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #a9a9a9;border-collapse: collapse;}
    .tftable th {font-size:12px;background-color:#b8b8b8;border-width: 1px;padding: 8px;border-style: solid;border-color: #a9a9a9;text-align:left;}
    .tftable tr {background-color:#ffffff;}
    .tftable td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #a9a9a9;}
    .intftable td{border-width: 0px;}
    .buttons{text-align: center;}
    .buttons input[type="button"] {
        background: #8d0e3a none repeat scroll 0 0;
        border: medium none !important;
        color: #fff;
        cursor: pointer;
        padding: 7px 15px;
    }
</style>

<table class="tftable" border="1">

    <tr>
        <td width="25%"><b>Order Id:</b> <?php echo $model->order_total_id; ?></td>
        <td width="25%"><b>User Id:</b> <?php echo $model->user_id; ?></td>
        <td  width="25%"><b>Order Date:</b> <?php echo $model->order_createdon; ?></td>
        <td  width="25%"><b>Payment Status:</b> <?php echo $model->payment_status; ?></td>
    </tr>
    <?php
    $wb = new Webservice();
    $sql = "select * from users where user_id='" . $model->user_id . "'";
    $userInfo = $wb->getRowData($sql);

   ?>
    <tr>
        <td colspan="4">
            <b>User Info</b><br/>
            <?php echo $userInfo['first_name'] . " " . $userInfo['last_name']; ?><br/>
            <?php echo $userInfo['email']; ?><br/><br/>
          </td>
    </tr>
    <?php
    $sql = "SELECT r.*,a.*,sp.*,
            IF(r.type=1, sp.plan_name, p.product_name) AS name,IF(r.type=1,'-',p.price) AS price
            FROM res_orders r 
            LEFT JOIN `subscription_plan` sp ON (r.subscription_plan_id = sp.plan_id AND r.type=1)
	    LEFT JOIN `product` p ON (r.product_id = p.product_id AND r.type=2) 
            LEFT JOIN `address` a ON (a.address_id = r.address_id) 
            WHERE r.order_total_id = '" . $model->order_total_id . "'";
   
    $productInfo = $wb->getAllData($sql);
   ?>
    <tr>
        <td colspan="4">
            <b>Product Info</b>
        </td>
    </tr>
    <tr><td colspan="4">
            <?php if ($productInfo) { $i=1; $netAmt=0; ?>
                <table width="100%" border="1" class="tftable">
                    <tr><td>#</td><td><b>Name</b></td><td><b>Qty</b></td><td><b>Price(&yen;)</b></td><td><b>Subtotal(&yen;)</b></td></tr>
                    <?php foreach ($productInfo as $pk => $pv) { ?>
                        <?php if ($pv["type"] == 1) { ?>
                            <tr>
                                <td><b><?php echo $i++; ?></b></td>
                                <td><b><?php echo $pv["name"]; ?></b><br/>
                                    <i>(Subscribed for <?php echo $pv["subscription_duration"]; ?>Month)</i></td>
                                <td><?php echo $pv["order_qty"]; ?></td>
                                <td><?php echo $pv["price"]; ?></td>
                                <td><?php echo $pv["price"]; $netAmt+= $pv["price"];?></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td colspan="4"><b>Address:</b><br>
                                    <?php echo $pv["first_name"] . " " . $pv["last_name"]; ?><br/>
                                    <?php echo $pv["address"]; ?><br/>
                                    <?php echo $pv["city"]; ?>,<?php echo $pv["state"]; ?><br/>
                                    <?php echo $pv["country"]; ?>-<?php echo $pv["postcode"]; ?><br/>
                                    <?php echo $pv["phone"]; ?>
                                </td>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td><b><?php echo $i++; ?></b></td>
                                <td><b><?php echo $pv["name"]; ?></b><br/></td>
                                <td><?php echo $pv["order_qty"]; ?></td>
                                <td><?php echo $pv["price"]; ?></td>
                                <td><?php echo $ordertotal =  (int)$pv["order_qty"] * (int)$pv["price"]; $netAmt+= $ordertotal;?></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td colspan="4"><b>Address:</b><br>
                                    <?php echo $pv["first_name"] . " " . $pv["last_name"]; ?><br/>
                                    <?php echo $pv["address"]; ?><br/>
                                    <?php echo $pv["city"]; ?>,<?php echo $pv["state"]; ?><br/>
                                    <?php echo $pv["country"]; ?>-<?php echo $pv["postcode"]; ?><br/>
                                    <?php echo $pv["phone"]; ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php  } ?>
                    <tr>
                        <td colspan="4" style="text-align: right;"><b>Total(&yen;)</b>:</td><td><?php echo $netAmt ?></td>
                    </tr>
               </table>    
            <?php } ?>
        </td></tr>
    <tr>
        <td><b>Payment Status</b>:</td><td colspan="3"><?php echo $model->payment_status; ?></td>
    </tr>
    <tr>
        <td><b>Payment Response</b>:</td><td colspan="3"><?php echo $model->payment_response; ?></td>
    </tr>
    <tr>
        <td><b>Order Status</b>:</td>
        <td colspan="3">
            <select name="order_status" id="order_status_<?php echo $model->order_total_id; ?>" onchange="changestatus(this.value,'<?php echo $model->order_total_id; ?>');">
                <option <?php echo ($model->order_status=='Pending'?"selected='selected'":""); ?> value="Pending">Pending</option>
                <option <?php echo ($model->order_status=='Delivered'?"selected='selected'":""); ?> value="Delivered">Delivered</option>
            </select>
        </td>
    </tr>
    <tr>
        <td colspan="4" align="center" class="buttons"><input type="button" onclick="window.location.href='<?php echo Yii::app()->request->baseUrl;?>/index.php/resOrderTotal/admin'" value="Go to Orders"></td>
    </tr>
</table>
<script type="text/javascript">
function changestatus(val,order_total_id){
    var r = confirm("Do you want to "+val+" this Order?");
    if(r == true){
            $.ajax({
                type: "POST",
                url:  "<?php echo Yii::app()->createUrl('resOrderTotal/changestatus'); ?>",
                data: {order_total_id:order_total_id,val:val,expire:0},
                success: function(msg){
                    if(msg == 'success'){
                        alert("Your order status has been changed successfully.");
                    }else{
                        alert("Your order status has not been changed...please try again");
                    }
                    return false;
                },
                error: function(xhr){
                  alert("failure"+xhr.readyState+this.url)
                }
           });
    }
    else {
       if(val=="Pending"){
            $("select#order_status_"+id+" option[value='Delivered']").attr("selected","selected");
        }else{
             $("select#order_status_"+id+" option[value='Pending']").attr("selected","selected");
        }
        return false;
    }
}
</script>
