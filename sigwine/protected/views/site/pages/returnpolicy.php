<?php
$wb=new Webservice();
$row=$wb->getRowData("select policy_title,policy_description from policycontent where policy_code='RETURNPOLICY'");
$policy_title = $row['policy_title'];
$policy_description = $row['policy_description'];
?>
<html>
    <link href="<?php echo Yii::app()->request->getBaseUrl(true); ?>/css/paypal.css" rel="stylesheet" type="text/css">
    <body>
        
        <div class="woocommerce" id="content">
            <h3 id="order_review_heading"><?php echo $policy_title; ?></h3>
            <div id="order_review">
                <table class="shop_table">
                    <thead>
                        <tr>
                            <th class="product-name"><?php echo $policy_description; ?></th>
                        </tr>
                    </thead>
                </table>
            </div>
     </div>
    </body>
</html>