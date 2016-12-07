<?php
/**
 * ContactForm class.
 * ContactForm is the data structure for keeping
 * contact form data. It is used by the 'contact' action of 'SiteController'.
 * android apikey = AIzaSyA3AKbhjucUsBH89ariEZpE4_QtLQaPKWQ
 * sender id : project:1002839703115:access
 */
define("GOOGLE_DRIVER_KEY","AIzaSyBbLx8Uwy0xhnsoKzA4aiAmyRJDWgtyfrg");
class Webservice {
    /**
     * @desc : Filter Data to prevent sql injunction
     * @param type $variable : value
     * @return type
     */
    public function filterData($variable) {
        $variable = strip_tags(trim($variable));
        return $variable;
    }

    /**
     * @desc : This is add webservice
     * @param type $post : post array
     * @param type $nfield : neglect file from post array
     * @param type $uniqueField : unique file on which you want
     * to perform unique operation
     * @return type
     */
    public function addData($post, $nfield, $uniqueField , $lastIdStatus = 1 , $table) {
        $neglectField = array();
        $uniqueQuery = '';
        if ($uniqueField != '') {
            $uniqueFieldValue = $post[$uniqueField];
            $uniqueQuery = "SELECT * FROM $table where $uniqueField LIKE '$uniqueFieldValue'";
        }
        if ($nfield != '') {
            $neglectField = explode(",", $nfield);
        }
        foreach ($post as $key => $val) {
            if (!in_array($key, $neglectField)) {
                if ($key == 'password') {
                    $values = "'" . base64_encode($val) . "'";
                } else {
                    $values = "'" . $val . "'";
                }
                $f[] = $key;
                $d[] = $this->filterData($values);
            }
        }
        $status = 0;
        if ($uniqueQuery != '') {
            $num = $this->getCount($uniqueQuery);
            if ($num > 0) {
                $status = "-2";
            } else {
                $sql = "insert into " . $table . "(" . implode(',', $f) . ") values (" . implode(',', $d) . ")";
            }
        } else {
            $sql = "insert into " . $table . "(" . implode(',', $f) . ") values (" . implode(',', $d) . ")";
        }
        
        if ($status == 0) {
            $lastId = $this->executeQuery($sql,'db',0) ;
            if ($lastId > 0) {
                $response = array("status" => '1',"data"=>array("message"=>"add data successfully","lastid"=>$lastId));
            } else {
                $response = array("status" => '-1',"data"=>array("message"=>"inserting failed"));
            }
        } else {
            $response = array("status" => "$status","data"=>array("message"=>"email or username already exists"));
        }
        return $response;
    }
    /**
     * @desc : Used to Update Data
     * @param type $post : post data array
     * @param type $nfield : neglect filed array
     * @param type $uniqueField : Database unique filed name
     * @param type $where : mysql where condition after where clause
     * @return string
     */
    public function updateData($post, $nfield, $uniqueField , $where , $table){

        $neglectField = array();
        $uniqueQuery = '';
        $uniqueFieldValue = '';
        $uniqueFieldOldValue = '';
        if ($uniqueField != '') {
            @array_push($neglectField, $uniqueField."_old");
            $uniqueFieldValue = $post[$uniqueField];
            $uniqueFieldOldValue = $post[$uniqueField."_old"];
            $uniqueQuery = "SELECT * FROM $table where $uniqueField LIKE '%$uniqueFieldValue%'";
        }
        if ($nfield != '') {
            $neglectField = explode(",", $nfield);
        }


        foreach ($post as $key => $val) {
            if (!in_array($key, $neglectField)) {
                if ($key == 'password') {
                    $values = "'" . base64_encode(trim($val)) . "'";
                } else {
                    $values = "'" . $val . "'";
                }
                $f[] = $key."=".$this->filterData($values);
            }
        }

        $status = 0;
        if($uniqueField != '') {
          if($uniqueFieldValue == $uniqueFieldOldValue){
              $status = 0;
          } else {
            $num = $this->getCount($uniqueQuery,'db');
            if ($num > 0) {
                $status = "-2";
            }
          }
        } else {
            $status = 0;
        }
        if($status == 0){
            $sql = "UPDATE $table SET ".  implode(",",$f)." WHERE $where";
            if ($this->executeQuery($sql,'db',1)) {
                $response = array("status" => '1',"data"=>array("message"=>"update success"));
            } else {
                $response = array("status" => '-1',"data"=>array("message"=>"updating failed"));
            }
        } else {
            $response = array("status" => '-2');
        }
        return $response ;
    }
    /**
     * @desc : user validation function
     * @param type $user : user name
     * @param type $pass : password
     * @return string
     */
    public function userValidate($user, $pass) {
        
        $pass = base64_encode($pass);
        $connection = Yii::app()->db;
        $sql = "SELECT user_id,language_preference FROM users WHERE email = '".$user."' AND password = '".$pass."'";
        $data = $this->getRowData($sql);
        if ($data) {
            $user_id = $data["user_id"];
            $response = array("status" => "1", "data"=>array("user_id" => $user_id,'language_preference'=>$data["language_preference"],"message"=>"Logged in successfully"));
        } else {
            $response = array("status" => "-1" ,"data"=>array("message"=>"Incorrect email or password"));
        }
        return $response;
    }
    /**
     * @desc : user validation function
     * @param type $user : user name
     * @param type $pass : password
     * @return string
     */
    public function userSocialValidate($where) {
        $connection = Yii::app()->db;
        $sql = "SELECT user_id,language_preference FROM users WHERE $where";
        $data = $this->getRowData($sql);
        if ($data) {
            $user_id = $data["user_id"];
            $response = array("status" => "1", "data"=>array("user_id" => $user_id,'language_preference'=>$data["language_preference"],"message"=>"Logged in successfully"));
        } else {
            $response = array("status" => "-1" ,"data"=>array("message"=>"Incorrect email or password"));
        }
        return $response;
    }
    /**
     * @desc : Function is used to fetch The data
     * from the database table
     * @param type $post : post array
     * @return string
     */
    public function fetchData($post ,$dataobj = 'db') {
        $table = $post["table"];
        $fields = $post["fields"];
        $beforeWhere = $post["beforeWhere"];
        $afterWhere = ($post["afterWhere"] == '') ? " 1=1 " : $post["afterWhere"] ;
        $recordPerPage = $post["r_p_p"];
        $start = ($post["start"] == '')? 0 :$post["start"];
        $nextStart = ($post["start"] != '' && $post["start"] != 'all')?($start+$recordPerPage) : '';
        $limit = '';
        if($start == 'all'){
            $limit = '';
        } else {
            $limit = " LIMIT $start , $recordPerPage ";
        }
        $connection = Yii::app()->$dataobj;
        if($afterWhere == 'HAVING distance < 25 ORDER BY distance'){
            $sqlTotal = "select $fields from $table  $beforeWhere $afterWhere " ;
        }else{
            $sqlTotal = "select $fields from $table  $beforeWhere where $afterWhere " ;
        }
        $totalRecords = $this->getTotalRecords($sqlTotal,$dataobj);
        $sql = "$sqlTotal $limit";
        if ($this->getCount($sql,$dataobj) > 0) {
            $list = $connection->createCommand($sql)->queryAll();
            $response = array("status" => "1",
                            "data" =>$list ,
                            "totalrecord"=>$totalRecords,
                            );
        } else {
            $response = array("status"=> "-3","data"=>array("message"=>"record not found","totalrecord"=>$totalRecords));
        }
        return $response ;
    }
    public function getTotalRecords($sql,$dbobj = 'db'){
        return $this->getCount($sql,$dbobj);
    }
    public function deleteRecords($sql){
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        if($command->execute()){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @desc : get total number of the row from fetch records
     * @param type $sql : mysql query statement
     * @return type
     */
    public function getCount($sql,$dataobj = 'db') {
        try {
            $connection = Yii::app()->$dataobj;
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $rowCount = $dataReader->rowCount;
            return $rowCount;
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    /**
     * @desc : User to execute mysql query statement
     * @param type $sql : mysql query statement
     * @return boolean
     */
    public function executeQuery($sql , $dataobj = 'db' , $status = 1) {
        try {
            $connection = Yii::app()->$dataobj;
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();

            /**
             * @desc : with Following Syntaxt we get last insert id
             */
            $lastInsertID = Yii::app()->$dataobj->getLastInsertID();
            if ($dataReader) {
                if($status == 1){
                    return true;
                }else{
                    return $lastInsertID;
                }
            } else {
                return false;
            }
        } catch (Exception $e){
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }
    public function getRowData($sql , $dataobj = 'db'){
        try {
            $connection = Yii::app()->$dataobj;
            $command = $connection->createCommand($sql);
            $dataReader = $command->queryRow();

            /**
             * @desc : with Following Syntaxt we get last insert id
             */
            Yii::app()->db->getLastInsertID();
            if ($dataReader) {
                return $dataReader;
            } else {
                return false;
            }
        } catch (Exception $e){
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }
    public function getAllData($sql , $dataobj = 'db'){
        try {
            $connection = Yii::app()->$dataobj;
            $command = $connection->createCommand($sql);
            $dataReader = $command->queryAll();
            if ($dataReader) {
                return $dataReader;
            } else {
                return false;
            }
        } catch (Exception $e){
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }
     /*
     * send PushNotification To Android
     */
    public function actionpushNotificationToAndroid($msg) {
        
        //$msg = "send successfully";
        $type = "notified";
        $sql = "SELECT device_token FROM users where device_type = 1 and device_token!=''";
        $deviceTokendata = $this->getAllData($sql);
        $badge_count = 0;
        foreach ($deviceTokendata as $key => $value) {
            $deviceToken[] = $value['device_token'];
        }
       
        foreach ($deviceToken as $key => $val) {
          //  $notis = $this->sendPushNotificationToIos($val,$alert,$type,$survey_id);
            $notis = $this->sendPushNotificationToAndroid(array($val), $msg, $type);
        }
        
        if ($notis) {
            $response = array('status' => '1', 'data' => array('message' => 'Notification send successfully'));
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'Notification sending fail'));
        }
        return $response;
    }
    /*
     * send PushNotification To Android
     */

    public function actionpushNotificationToIos($alert,$type,$survey_id) {
       
        //$msg = "send successfully";
        $type = "notified";
        $sql = "SELECT device_token FROM users where device_type = 2";
        $deviceTokendata = $this->getAllData($sql);
        $badge_count = 0;
        foreach ($deviceTokendata as $key => $value) {
            $deviceToken[] = $value['device_token'];
        }
        foreach ($deviceToken as $key => $val) {
            $notis = $this->sendPushNotificationToIos($val,$alert,$type,$survey_id);
        }
        if ($notis) {
            $response = array('status' => '1', 'data' => array('message' => 'Notification send successfully'));
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'Notification sending fail'));
        }
       return $response;
    }
    public function sendPushNotificationToIos($deviceToken,$message,$type,$id=""){
        $path = getcwd().DIRECTORY_SEPARATOR;
        $badge_count="";
        $pemfile = $path.'SW_APNS_Dev.pem';
        //echo $pemfile;
        if($deviceToken != '0') {
            $ctx = stream_context_create();
            $passphrase = '';
            stream_context_set_option($ctx, 'ssl', 'local_cert', $pemfile);
            stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
            $fp = @stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
            $body['aps'] = array(
                'id' => $id,
                'alert' => $message,
                'sound' => 'default'
            );

            //print_r($body);die;
            $payload = json_encode($body);
            $msg = @chr(0) . @pack('n', 32) . @pack('H*', $deviceToken) . @pack('n', strlen($payload)) . $payload;

            $result = @fwrite($fp, $msg, strlen($msg));

            $flag = 0;
            if(!$result){
                $flag =1 ;
            }

            @fclose($fp);
            if($flag == 0){
                return true;
            } else {
		return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    public function sendPushNotificationToAndroid($registatoin_ids , $message , $type) {
       

        $url = 'https://android.googleapis.com/gcm/send';
        $messages = array("data" => $message);

        $fields = array('registration_ids' => $registatoin_ids, 'data' => $messages, 'type' => $type);
        /**
         * @desc : type 1 = driver
         * type 2 = passenger
         */
        $key = GOOGLE_DRIVER_KEY ;
        //echo $key ;
        $headers = array(
            'Authorization: key=' . $key,
            'Content-Type: application/json',
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);
        //echo $result;
        $res = json_decode($result,true);
       
        if($res["success"] == '1'){
            return true;
        } else {
            return false ;
        }
    }
    /*
     * Add Activity log by admin and sub admin
     */
    public function addActivityLog($userid,$activity,$object) {
            $sql = "INSERT INTO user_activity_log (userid,activity,object,activity_create_date) VALUES ('" . $userid . "','".$activity."','".$object."','" . date("Y-m-d H:i:s") . "')";
            $this->executeQuery($sql);
    }
    public function sendOrderEmail($orderId,$user_id){
        $wb = new Webservice();
        $fields = $_REQUEST;
        $sql = "select * from users where user_id='" . $user_id. "'";
        $userInfo = $wb->getRowData($sql);
        
        $sql = "select * from res_order_total where order_total_id='".$orderId."'";
        $orderTotalInfo = $wb->getRowData($sql);
        $ORDER_ID=$orderId;
        $USER_ID=$user_id;
        $ORDER_DATE=$orderTotalInfo['order_createdon'];
        $PAYMENT_STATUS=$orderTotalInfo['payment_status'];
        $USER_INFO=$userInfo['first_name'] . ' ' . $userInfo['last_name'].'<br/>
                 '.$userInfo['email'];
        
   
    //$sql="select * from res_orders where order_total_id='".$model->order_total_id."'
    //    and user_id='".$model->user_id."'";
    $sql = "SELECT r.*,a.*,
            IF(r.type=1, sp.plan_name, p.product_name) AS name,IF(r.type=1, '-', p.price) AS price
            FROM res_orders r 
            LEFT JOIN `subscription_plan` sp ON (r.subscription_plan_id = sp.plan_id AND r.type=1)
            LEFT JOIN `product` p ON (r.product_id = p.product_id AND r.type=2) 
            LEFT JOIN `address` a ON (a.address_id = r.address_id) 
            WHERE r.order_total_id = '" .$orderId."'";
   $productInfo = $wb->getAllData($sql);
   
   $mailBody=' 
                <tr><td colspan="4">';
                if ($productInfo) {
                    $i=1;
                    $mailBody.=' <table width="100%" border="1" class="tftable">
                        <tr><td>#</td><td><b>Name</b></td><td><b>Qty</b></td><td><b>Price(&yen;)</b></td><td><b>Subtotal(&yen;)</b></td></tr>';
                        foreach ($productInfo as $pk => $pv) { 
                            if ($pv["type"] == 1) { 
                                $mailBody.='<tr>
                                    <td><b>'.$i++.'</b></td>
                                    <td><b>'.$pv["name"].'</b><br/>
                                    <i>(Monthly auto subscription)</i></td>
                                    <td>'.$pv["order_qty"].'</td>
                                    <td>'.$pv["price"].'</td>
                                    <td>'.$pv["order_total"].'</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td colspan="4"><b>Address:</b><br>
                                    '.$pv["first_name"] .' ' . $pv["last_name"].'<br/>
                                    '.$pv["address"].'<br/>
                                    '.$pv["city"].','.$pv["state"].'<br/>
                                    '.$pv["country"].' - '.$pv["postcode"].'<br/>
                                    '.$pv["phone"].'
                                    </td>
                                </tr>';
                             } else { 
                            $mailBody.='<tr>
                                <td><b>'.$i++.'</b></td>
                                <td><b>'.$pv["name"].'</b><br/></td>
                                <td>'.$pv["order_qty"].'</td>
                                <td>'.$pv["price"].'</td>
                                <td>'.$pv["order_total"].'</td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td colspan="4"><b>Address:</b><br>
                                    '.$pv["first_name"] . ' ' . $pv["last_name"].'<br/>
                                    '.$pv["address"].'<br/>
                                    '.$pv["city"].','.$pv["state"].'<br/>
                                    '.$pv["country"].'-'.$pv["postcode"].'<br/>
                                    '.$pv["phone"].'
                                </td>
                            </tr>';
                         } 
                     } 
                   $mailBody.= '<tr>
                        <td colspan="4" style="text-align: right;"><b>Total(&yen;)</b>:</td><td>'.$pv['order_total'].'</td>
                    </tr>
                </table>';    
                } 
        $mailBody.='</td></tr>';
        $PRODUCT_INFO=$mailBody;
        $PAYMENT_STATUS=$orderTotalInfo['payment_status'];
        $PAYMENT_RESPONSE=$orderTotalInfo['payment_response'];
        $ORDER_STATUS=$pv['order_status'];
        $sql = "select email_text from email_template where template_code='ORDER_EMAIL'";
        $templateInfo = $wb->getRowData($sql);
        $emailBody=$templateInfo['email_text'];
        $emailBody=  str_replace('{ORDER_ID}', $ORDER_ID, $emailBody);
        $emailBody=  str_replace('{USER_ID}', $USER_ID, $emailBody);
        $emailBody=  str_replace('{ORDER_DATE}', $ORDER_DATE, $emailBody);
        $emailBody=  str_replace('{PAYMENT_STATUS}', $PAYMENT_STATUS, $emailBody);
        $emailBody=  str_replace('{USER_INFO}', $USER_INFO, $emailBody);
        $emailBody=  str_replace('{PRODUCT_INFO}', $PRODUCT_INFO, $emailBody);
        $emailBody=  str_replace('{PAYMENT_STATUS}', $PAYMENT_STATUS, $emailBody);
        $emailBody=  str_replace('{PAYMENT_RESPONSE}', $PAYMENT_RESPONSE, $emailBody);
        $emailBody=  str_replace('{ORDER_STATUS}', $ORDER_STATUS, $emailBody);
        
        $headers = "From: SigWine <admin@sigwine.com>\r\n";
        $headers .= "Content-type: text/html\r\n";
        $subject = "Your Order ".$_SESSION["last_id"]." has been placed";
        // now lets send the email.
        $to=$userInfo['email'];

        if (mail($to, $subject, $emailBody, $headers)) {
            return true;
        } else {
            return false;
        }
    }
}

