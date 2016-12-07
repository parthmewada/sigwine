<?php
/**
 * @desc : Yii Web Service Extentions
 * @author: aNKIT kHAMBHATA
 * @version: 1.0
 */

define("GOOGLE_API_KEY","AIzaSyDuENzYHWy0PrHEly_Ajd5rNr14eWGYM7c");
class JsonWebservice extends CApplicationComponent {

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
    public function addData($table, $post, $nfield, $uniqueField, $lastIdStatus = 1,$fieldEncode = array(),$dataobj = 'db') {
        $neglectField = array();
        $uniqueQuery = '';
        /**
         * @desc: Filter data array(s) here
         */
        $dataArray = $this->generateArrayForQuery($post,$neglectField,'add',$fieldEncode) ;
        $f = $dataArray["f"];
        $d = $dataArray["d"];
        $combine = $dataArray["combine"];
        if ($uniqueField != '') {
            /**
             * @desc: Create Unique Field condition
             */
            $uniqueFieldArray = @explode(',',$uniqueField);
            $unquieFieldCount = count($uniqueFieldArray);
            for($k = 0 ; $k < $unquieFieldCount ;$k++ ) {
                $uniqueFiedCon[] = "({$uniqueFieldArray[$k]} LIKE '%{$combine[$uniqueFieldArray[$k]]}%')";
            }
            $uniqueCondition = (@count($uniqueFiedCon) > 1) ? @implode(' AND ', $uniqueFiedCon) : "{$uniqueFiedCon[0]}";
            $uniqueQuery = "SELECT * FROM $table where $uniqueCondition";
        }
        if ($nfield != '') {
            $neglectField = @explode(",", $nfield);
        }
        $status = 0;
        if ($uniqueQuery != '') {
            $num = $this->getCount($uniqueQuery, $dataobj);
            if ($num > 0) {
                $status = "-3";
            } else {
                $sql = "insert into " . $table . "(" . @implode(',', $f) . ") values (" . @implode(',', $d) . ")";
            }
        } else {
            $sql = "insert into " . $table . "(" . @implode(',', $f) . ") values (" . @implode(',', $d) . ")";
        }
        if ($status == 0) {
            if ($lastIdStatus == 0) {
                $lastId = $this->executeQuery($sql, $dataobj, 0);
                if ($lastId > 0) {
                    $response = array("status" => '1', "lastid" => $lastId);
                } else {
                    $response = array("status" => '-1');
                }
            } else {
                if ($this->executeQuery($sql,$dataobj)) {
                    $response = array("status" => '1');
                } else {
                    $response = array("status" => '-1');
                }
            }
        } else {
            $msg = '';
            if ($uniqueField != '') {
                $msg = str_replace(',','+',$uniqueField)." should be unique.";
            }
            $response = array("status" => "$status","error_msg" => $msg);
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
    public function updateData($table, $post, $nfield, $uniqueField, $where, $fieldEncode = array(),$dataobj = 'db') {
        $neglectField = array();
        $uniqueQuery = '';
        $uniqueFieldValue = '';
        $uniqueFieldOldValue = '';
//        if ($uniqueField != '') {
//            $uniqueFieldValue = $post[$uniqueField];
//            $uniqueFieldOldValue = $post[$uniqueField . "_old"];
//            $uniqueQuery = "SELECT * FROM $table where $uniqueField LIKE '%$uniqueFieldValue%'";
//        }

        if ($nfield != '') {
            $neglectField = @explode(",", $nfield);
            if(!in_array($uniqueField."_old", $neglectField))
                @array_push($neglectField,$uniqueField."_old");

        }
        $dataArray = $this->generateArrayForQuery($post,$neglectField,'update',$fieldEncode) ;
        $f = $dataArray["f"];
        $status = 0;
        if ($uniqueField != '') {
            if ($uniqueFieldValue == $uniqueFieldOldValue) {
                $status = 0;
            } else {
                $num = $this->getCount($uniqueQuery, $dataobj);
                if ($num > 0) {
                    $status = "-2";
                }
            }
        } else {
            $status = 0;
        }
        if ($status == 0) {
            $sql = "UPDATE $table SET " . implode(",", $f) . " WHERE $where";
            if ($this->executeQuery($sql, $dataobj)) {
                $response = array("status" => '1');
            } else {
                $response = array("status" => '-1');
            }
        } else {
            $response = array("status" => '-2');
        }
        return $response;
    }
    /**
     *@desc : This function is generate data and field array
     * for insert and updat operation of database
     * @param type $post : data array
     * @param type $neglectField : neglectefield
     * @param type $type :
     * @return type
     */
    protected function generateArrayForQuery($post,$neglectField,$type='add',$fieldEncode = array()) {
        $f = array();
        $d = array();
        $response = array();

        foreach ($post as $key => $val) {
            $val = addslashes($val);
            if (!in_array($key,$neglectField)) {
                if(@array_key_exists("fields", $fieldEncode)) {
                    if (in_array($key, $fieldEncode["fields"])) {
                        $fieldEncodeFunctions = $fieldEncode["encodefunction"][$key];
                        $values = "'" . $fieldEncodeFunctions($val) . "'";
                        $cv = $fieldEncodeFunctions($val);
                    } else {
                        $values = "'" . ($val) . "'";
                        $cv = ($val);
                    }
                } else {
                    $values = "'" . ($val) . "'";
                    $cv = ($val);
                }
                if ($type == 'update') {
                    $f[] = $key . "=" . $this->filterData($values);
                } else if ($type == 'add') {
                    $f[] = $key;
                    $d[] = $this->filterData($values);
                    $vc[] = $cv;
                }
            }
        }
        if ($type == 'add') {
            $response["f"] = $f;
            $response["d"] = $d;
            $response["combine"] = array_combine($f, $vc);
        } else if ($type == 'update') {
            $response["f"] = $f;
        }
        return $response;
    }
    /**
     * @desc : Purpose to Validate User while login
     * @param type $table : name of table
     * @param type $where : where condition
     * @return string : repose array
     */
    public function userValidate($table, $where ,$dataobj = 'db') {
        $sql = "SELECT * FROM
                    $table
                WHERE
                    1=1 AND  $where";
        $num = $this->getCount($sql,$dataobj);
        $uid = '';
        if ($num > 0) {
            $response = array("status" => "1");
        } else {
            $response = array("status" => "-1");
        }
        return $response;
    }
    /**
     * @desc : Function is used to fetch The data
     * from the database table
     * @param type $post : post array
     * @return string
     */
    public function fetchData($post, $dataobj = 'db') {
        $table = $post["table"];
        $fields = $post["fields"];
        $beforeWhere = $post["beforeWhere"];
        $afterWhere = ($post["afterWhere"] == '') ? " 1=1 " : $post["afterWhere"];
        $recordPerPage = $post["r_p_p"];
        $start = ($post["start"] == '') ? 0 : $post["start"];
        $nextStart = ($post["start"] != '' && $post["start"] != 'all') ? ($start + $recordPerPage) : '';
        $limit = '';
        if ($start == 'all') {
            $limit = '';
        } else {
            $limit = " LIMIT $start , $recordPerPage ";
        }
        $connection = Yii::app()->$dataobj;
        $sqlTotal = "select $fields from $table  $beforeWhere where $afterWhere ";
        $totalRecords = $this->getTotalRecords($sqlTotal, $dataobj);
        $sql = "$sqlTotal $limit";
        if ($this->getCount($sql, $dataobj) > 0) {
            $list = $connection->createCommand($sql)->queryAll();
            $response = array("status" => "1",
                "data" => $list,
                "nextStart" => "$nextStart",
                "totalRecord" => "$totalRecords",
                "queryString" => "$sql"
            );
        } else {
            $response = array("status" => "-3", "queryString" => "$sql");
        }
        return $response;
    }

    /**
     * @desc : Count total number of recoreds
     * @param type $sql : mysql query statement
     * @param type $dataobj : data object
     */
    public function getTotalRecords($sql, $dataobj = 'db') {
        return $this->getCount($sql, $dataobj);
    }

    /**
     * @desc : get total number of the row from fetch records
     * @param type $sql : mysql query statement
     * @return type
     */
    protected function getCount($sql, $dataobj = 'db') {
        try {
            $connection = Yii::app()->$dataobj;
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $rowCount = $dataReader->rowCount;
            return $rowCount;
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    /**
     * @desc : Count the distance between two lat logn
     * @param type $lat1 : latitude of from
     * @param type $lon1 : longitude of Form
     * @param type $lat2 : latitude of To
     * @param type $lon2 : longitude of To
     * @param type $unit : K = kilometer , N = miles
     * @return type : float
     */
    public function getDistanceFromLatLng($lat1, $lon1, $lat2, $lon2, $unit) {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    /**
     * @desc : User to execute mysql query statement
     * @param type $sql : mysql query statement
     * @param type $dataobj: data object in case of more than tow
     * @param type $status : if 0 : last insert ID 1 : return true/fase
     * @return boolean
     */
    public function executeQuery($sql, $dataobj = 'db', $status = 1) {
        try {
            $connection = Yii::app()->$dataobj;
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            /**
             * @desc : with Following Syntaxt we get last insert id
             */
            if ($status == 0) {
                $lastInsertID = Yii::app()->$dataobj->getLastInsertID();
            }

            if ($dataReader) {
                if ($status == 0) {
                    return $lastInsertID;
                } else {
                    return true;
                }
            } else {
                return false;
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }
    /**
     * @desc : Get single Row data
     * @param type $sql : mysql query statement
     * @param type $dataobj : data object , in case of more than two database
     * @return boolean
     */
    public function getRowData($sql, $dataobj = 'db') {
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
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }
    public function p($d) {
        echo '<pre>';
        print_r($d);
        echo '</pre>';
    }
    /**
     * @desc : Send Json Response
     * @param type $response @de
     */
    public function response($response){
        $data = array("response" => $response);
        return json_encode($data);

    }
    /**
     *
     * @param type $deviceToken : Phone Device Token
     * @param string $message : message to be send
     * @param type $certificate : IOS certificate
     * @return boolean
     */
    public function sendPushNotificationToIos($deviceToken, $message = '', $type = '', $bedge = 0) {
        $bedge = intval($bedge + 1);
        if ($deviceToken != '0') {
            $ctx = stream_context_create();
            $passphrase = '12345';
            
            stream_context_set_option($ctx, 'ssl', 'local_cert', Yii::app()->basePath.DIRECTORY_SEPARATOR.'MorphDev.pem');
            //stream_context_set_option($ctx, 'ssl', 'local_cert', Yii::app()->basePath . DIRECTORY_SEPARATOR . $certificate);
            stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
            $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
            
            if($type == 'test'){
                $body['aps'] = array(
                    'badge' => $bedge,
                    'f_id' => $feed_id,
                    'bar_name' => $venue_name,
                    'request type' => $type,
                    'alert' => $message['message'],
                    'sound' => 'default'
                );
            } else {
                $body['aps'] = array(
                    'badge' => $bedge,
                    'alert' => $message['message'],
                    'sound' => 'default'
                );
            }
            $payload = json_encode($body);
            //print_r($payload);exit;
            $msg = chr(0) . @pack('n', 32) . @pack('H*', $deviceToken) . @pack('n', strlen($payload)) . $payload;
            $result = fwrite($fp, $msg, strlen($msg));
            $flag = 0;
            if (!$result) {
                $flag = 1;
            }
            fclose($fp);
            if ($flag == 0) {
                return true;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
    
    /**
     * @desc
     * @param type $registatoin_ids : array of register ids
     * @return boolean
     */
    public function sendPushNotificationToAndroid($registatoin_ids, $message, $type = '', $bedge = 0)
    {
        $bedge = intval($bedge + 1);
        $url = 'https://android.googleapis.com/gcm/send';
        
        if($type == 'new_post') {
            $messages = array(
                    "data" => $message,
                    'group_id' => $group_id,
                    'request type' => $type,
                    'group_name' => $grp_name,
                    "count" => $bedge
                );
        } else {
            $messages = array(
                "data" => $message,
                "count" => $bedge);
        }
        //$messages = array("data" => $message);

        $fields = array(
            'registration_ids' => $registatoin_ids,
            'data' => $messages,
        );

        $headers = array(
            'Authorization: key=' . GOOGLE_API_KEY,
            'Content-Type: application/json'
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
        $res = json_decode($result, true);
        
        if ($res["success"] == '1') {
            return true;
        } else {
            return false;
        }
    }
    /**
     * @desc :Function user to generate image from base64 code
     * @param type $image : image base64 code
     * @param type $imagepath : image path
     * @param type $ext : image extention
     * @return string|boolean
     */
    public function base64image($image,$imagepath,$ext = 'jpg', $extra = ""){
        //$image = "/9j/4AAQSkZJRgABAgAAAQABAAD//gAEKgD/4gIcSUNDX1BST0ZJTEUAAQEAAAIMbGNtcwIQAABtbnRyUkdCIFhZWiAH3AABABkAAwApADlhY3NwQVBQTAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA9tYAAQAAAADTLWxjbXMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAApkZXNjAAAA/AAAAF5jcHJ0AAABXAAAAAt3dHB0AAABaAAAABRia3B0AAABfAAAABRyWFlaAAABkAAAABRnWFlaAAABpAAAABRiWFlaAAABuAAAABRyVFJDAAABzAAAAEBnVFJDAAABzAAAAEBiVFJDAAABzAAAAEBkZXNjAAAAAAAAAANjMgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAB0ZXh0AAAAAEZCAABYWVogAAAAAAAA9tYAAQAAAADTLVhZWiAAAAAAAAADFgAAAzMAAAKkWFlaIAAAAAAAAG+iAAA49QAAA5BYWVogAAAAAAAAYpkAALeFAAAY2lhZWiAAAAAAAAAkoAAAD4QAALbPY3VydgAAAAAAAAAaAAAAywHJA2MFkghrC/YQPxVRGzQh8SmQMhg7kkYFUXdd7WtwegWJsZp8rGm/fdPD6TD////bAEMACQYHCAcGCQgICAoKCQsOFw8ODQ0OHBQVERciHiMjIR4gICUqNS0lJzIoICAuPy8yNzk8PDwkLUJGQTpGNTs8Of/bAEMBCgoKDgwOGw8PGzkmICY5OTk5OTk5OTk5OTk5OTk5OTk5OTk5OTk5OTk5OTk5OTk5OTk5OTk5OTk5OTk5OTk5Of/CABEIAZUC0AMAIgABEQECEQH/xAAbAAACAwEBAQAAAAAAAAAAAAACAwABBAUGB//EABkBAAMBAQEAAAAAAAAAAAAAAAABAgMEBf/EABkBAAMBAQEAAAAAAAAAAAAAAAABAgMEBf/aAAwDAAABEQIRAAABRAtQZLIUhMELBJIpdhZrsRrsGrKWFmogksglkSA0Lg20oU9Ez2N9LJB1VjlXQrqGwqlpqVpGs89aLqcseFZrprR57cSrOOxYs9Mqswh2C7boV4ZtzCVDqpCHTQRlAFMEBo6aCFAGjoFw6AIYsGnChMZBqFgsGioQ0UAJcbGFEAS5j3aDzsSfFEkyAQjCyJGMgAR2AEVpjDjKKRK5LHdXAly0Vcg4VWBSonckAiAhlKoCAoIYVgIFbQHBYxiBl6FrjQU4ahdlAhhab1ricq6qV2UaEGi0FHTS4ccrjKBcKMC2MmkWaxuFUTcKxRUCqihIaVAQtCVQKqxBBZmz1Oai0PtJKXWqxOJJCdaSQ61QHFnpGqYUj6t8QGd+cAR+hvzNB6gvJiHrZ5Cg9lfi6H7UfGUHtZ4iD9sXirD2geMIPYF41gesPyi0/ZTxBUvbD4yhe1ni6D2teLgvaX4xg/XTyNC9fXkYHrb8iTXrZ5VqfpJwmJdicroCOVEENRl1VCg3TVVI1BsQlSmXKpKhIGiGhHdVTUsKAxoR+YuodbTRYn0mxPPLA3nzoLpVgsWpK4MoMA4EBkCAUGMKDAOBAOpYVd0lchDEmUiMEhmB0iRrlSKphK823LQgqfcopywCxJphxUvUm1oqjqksijBZDlqtwDG6sWs+YLnu5uXSOpr4JB6CcfQT0K54M6c5lB0y5UF1q5NB2K5EDrjyKF2K5EDq1y6H065kDnVJPTZBYjkkkq4yrKmqKiCFGoVTWBmjVsqSCkkC7GMshtBVZgJEMjCoxqHQDQsXYGWdgM2Z2TUQ4AtbjRiBlaQh2d1JVXTCu2IIW1LU4BBhp0zSB1oAVtGpWwCGCNYMz0xVKGJC3dXm9iEK7zqXFgFnTVzwZtnOWzpjiproXzKH0R58DYlAhpLJQ8ckz2qjgDcgWSyA7EguXEDpQ8It0ApneC0dFYYodVI2UCQqATpgWdlD1JJqaDKMy0wakBeDc0ZrRooYhtncNSdKqMj4u4SJFczRChkhygGqKpCmUy3IXNaKWwAW5TVCa2MUQhR1YdPoeb9fnGItGEzlKXRuHFFdpbG9mNaxCJDQFGIxoxGMkHiq5G8KWK5VoGjsBO4Eq6FchDhygkWbRbcLEPQ5SZ1egMN9JSMZOISmDYFdwCoWgsNCmAYGxVuELhGhB0prTloGtSwsQD1rDlu3mHNvvbIfk79tE/EH7dgeAX9CMfz3R9BKX4xvq1yeWD0mKpw7EiTaNHMc9DfzMTn0s8sAd7n88bjdjWNFCQ0VRUMKMRhLocqxCquh5GAWe91IFMXAK4SKFoAEq6LlQGnn0IhrtpdwmPYsodMZYWY3LNZPRzh6C6Wa9tBjPfoT549dsPgp9NA8yPdba88z0QJciuo9Hnsvs0NeSv2rGsvaVr59lkylQsomquxC6qIugyifmRlvA8UVtyAtgaZMVBGNXTBq6boboY1Yt1UEqDdDobgxoxGIlGxE6TCioeUauNytbQqMFFkFgdVAqSBKYIJfVNMJbAKXAA6oOgzH6nK83ZDRzbZcfYuXh1RCrQUpo7yJcbw5y3n0b5Z1lvmawdaXDh7iNMw6BHmscjy2FjJrXow7Fem0mrKpY6BsDMvbQudn7IuPPD6Eaz84n0XLvHnL0q05kU0aFA6h5x0A2kXCUmmg2sWiNcOhgLBGujoY1cHQlQ8lyRuu2iFXVsuMWBEowcorSTGA2ZrsUjYEfOlJm6LNmV4upqPDZlorLR9IBmikg5YoauLGxJGXHEuQmyhhRwm+oKdC0z02Jgp6mh0o2tS6jLkgSSglSk5VKQYJVUNzqGsV5duXTmUDRvFVNoEC8W0RgsWLAGIMB0sWUUqjFsBYAxE6GumCMBOis1LZG4QxCVDZZJaAVdiu6ICuaEZNFagTv2drHTnbunfNtmfdRawbRSacpzlZZXA2ZOFzUwXPHcpxjjsl4uirctKbVWXaQmmsTGNUi6Bnj6CPAmpLodyQVXBGV1BXJGVLpAiyDXm2iHDw9rna8OMSHblETEBGxZQGIwBgulUQuhq6HQyiqq6YNXRVDdDwmFx0EQ2A0VBd1QQqsUKngrpj3sqw9Z+vm3oxHHVooENEz206gOlQ3VTLIhRqKaetfIrPrzxF9HH7DP5eVHqk+csXbzcy2tOnm7jT0hXr5ejHWyh4r1G5s9MWyTOKhE4AQ4AldBcqhFBoZVVAygEDysJLjYfVc/Xl4QbcuvIqiqkAmIwBgNrExGAmLoBMW10VFDRCMauh46aEdFXCYhy4DrWwSibuRg7wdbHTRpz9Dk6IGhcVmVqTQsTFzZru5YaWAyotrVQxAAQuK5vTy1HkpmZ28zbVZDCVZJ2sQdu5Gqdfc7uX1ePsGxtVCGwYSY06LOpuSBJIEq4CrOlQyCghuBQGIUMgpn0JJ52Lpc/biSsD1woTW3Q2DdAQjoLp0FWA6qRuqkGNEJSJWjLoxE4RRTDpZdhaBt0Hvw0rqhfNu1sZFKWxaqqIWpIQCcIK4zfCdEekf5S9n9B3fNPU5HrM4vjLLzDx3Hmh9Jk3zwuVlH2A47HPXTylB6Z3k9U37LreE7GWno78u6dvRn5yh+jHzrU/RFx+osmtztcHJKmSQJJAglACjktQug0BqAWYWA4Tj2KrHlZe4nTHjk7JpkxYEyluW6WJgMBYLoBYIwE6AJKdZrky3bmZYDuW8C24d8Vv6HO7HJ1TRj0Zt9ZqBiwEpg1bUlWB2MCvG+0RtHzm/VYd9uX2X3F6uf0vMPkj+Yrfl1p39iTyk9zoR4EvcvR4I/YqDxwe5aHg+v6Zyrzfsc3Il+pC7y1VDpFHCZV1YPi2XEkgSSBJIEkgSSBKuAKXoQpYZzNiSqs8qdtVHFrsrqXizsRv4hHv+RceUDrc7XPOLQoCig1i1Y8VGyN6dTEGxWlGjp30OTqEtS8dKU1KqwtdI4uNGSiBkXHLLWLWVG67rBsaJkXKDibYKPqbdckdLgMmeri0bYvAjsaU+APouGw93L9FGmItgzeQNOGotXn+5rl1ez5Hv5bb5z7l9C8sDVWYqWhuGg6EzaGXJE5JAkkCSQJJAVn1ql81HWVWXMMycBQwBoiElyaF2dHmtK09ArJuWmHn+gprwuH6Ti0j5+vrYNceZvTrnqzFXaI5XS53cy16WgT4uoqlJqz6cdA0MuDsKY2kXSbFQlg641nFud4Hyl4OjAPRr4l4uzVVNvOPCun1xeR1S+4rz0Dt6POddMcfZ57MQdWqXMvfYB2eR0JH7eT2Yjqt1zLXM48TnSpSahnR43La9dl8x0m/TF53rZ67JIqkkCSQJJAi2RC8uwE+Tj7/ADbxwsA3mB3EwkWJiUjUuvLnZ6Ds+EetPdV5XbGvb5uPqp/N+lnPRrQA659Xv8PocnV0nZy59XgVKsqNiLWenIuRz3i2z06A9ETjTjUlqUErAxPmXirXzl7ZMFJDOAsCtRGttqlRQTTLJroSw6RI5jepgGfQ4y2uvjyETvdy4T2JzegSPbHqZ3e3z2EXsB8hqK9FxOhwVYd3Nriug7A2ddUWylJIElWEq4FUQoWRimlOvGLnZ/QYaz5VacV5EAQmCJMXmaqxaWhTVZrHz3oYtm4t7ZrFqSIdHveWvLT0OXjbVQufoQugbN5NmLXcdvnIzw9wcpFLs8823l57bzNvVzFNvrM55PTHNOO3idXqLX57n+jeMu+VYtpjZ2QAsEekcrFUbVvO7GyblQLGAq09LhdBL0HEy4XG/PzGVvtPnAV6Hs+G6kZ+ldjmO3bYMi3tx0n1T5uoeiLZRJIFXIioFpkOaMcSWoitEDmcvvkR5ofS058tj9fjqfLB28WkYpqFnmTcddCYO5HPJxBmHeKdbsuqK6gc0873Z8WqkHS5jU8r92Nm9XCjnsYapzzOu1uvL0uLz8NZbF5I+jX0OJBe66Hz/wBHny8hHa4Nb6LSxhrJaI/O5hwSUyXQSpAWBgtJpx6Ws+S6rXY8hhjl0rbyORta2bOR6HEfr836DHbXETHTJ0MPM3y9jPK7IffnExD9Hr8xyqXvPOZVq3LMlS873AndmzXHs3/OiI+j8zw+Ol9GP50kPf8AI5KQ18/rHOvJ525W2WVxFU6Rz6oq9XOtVkvc2oPPmwB0sufVayd3G6a2hgKK6WhXEh9Hd5nta44FLDXFIyPUm0wFq1pBPR50F7DzDFzkJ6ItM1aFti2NSC2QS6aIJrWI8RaUNitI0XY231D5mvGyXMtIZRXM6GXsZ1g6uX0OGubZz7y06fHXuax8v0zKjzujQFTOjjTF6hVqVZ9O3BNFhw6tciFXOtd7RyX51fO7KGa9+RmGvIzdHznVht2cl9OWjPePVDnaVTS06865yFuuRdmWzpqhRWQbK16ThznxXWyq0tFkzMqdDsKHOnI7NcyVG9L8zIZpbkYMksvVlpF1IFXIySQJLgVCtAS4ypehGaenRlfn73uqeVfouANc19AOKfqG5aeX6nTwKegGXfnXKy+mFnjer0ddGNGlUvsO4x5vLzPQc/WeO7q6KXA6q2D1IWzOgvLpudChzS2d/wAl2U8vb8yVLoczVioTtxBpkeiMVZj288TUIKpbpygPVrUvO82gyqMi+7gDJ3ONrT9BwH5s9EA3n74DV9Knyp6rlRXJl1rnCpyaa0qBc6UT519HfL4O9voc9PFn65k6+K09LHrgrpIqV6jzGjp5X5TN6l22fkfTac8Wvrq5mVmT0s2r856lVwdacGufpF4deb0KNcse0vPnevBm6FLnJ72UOBj15+vnDUzpQ8WPo89sdnKc51qzpbZpQprrdjj9/k6fJV6nja5oIHOdvFrHS0ax2hlynqdKLmC4bne+55/QRY3bOb2ctMyephUpPKWkLuRrTh1vVYL3Eqyp0E5Eek+LwV1Bh8WaW2qSrG5Z0+L0CupgavPVLK6jLdkmW+1WOxbB2ZFjzM/XRtknq+f7iZczqlFczuczmNeqZy+nz68Xj9vF05Zt2Y7ne/mJy1wJ6KujHnm4Kj0Gjy3oufY65bB7F85NG8sDWtAaKkLTyKc90uenPTHl38zqw1nh6KMTdOJndd5as9e1kHQQ/d57qxVYtot829R6RltegMfVW+UzO7NKoQq62M5akNVA1g45SY7s3Tk5nXw6opr8aJvZy+iTXH26eZU1s5ktdbmk1Cb3KV59RgqUvTgrPtFzevnWzJ0jw0c7yHVm+kHkd9wHM9Bj3y0KZ2c742fRnqcmhmloM+Yap24Ch6cC84PHmetc4V7sc1zF9itIyt0dHPTyy9mLfnFrEtN3ZPRZNvN3Jxvz2L07tl5fV6TKn51Gl++YhVpkugavbzGqmo7NxpzMzd158zvas+G/P6eHoOOfmJFzo63F72euHldbjXkb1M0fY5HW5WeiRdWuDX49OWui6CUhVDrlqdndFt4vX44A/O/bOEJifqwb8duXvw7LerF0cUtHc4vrzPxPovOdBX0Rzrzu+lzdwetIZx1fnfQ+L2PSKHbhr5bDswehxkvVyqbm5tNzpSejK+R0udvrPWCpG3dYc59OJs5PT0VVq6KrzZ7V3jazXDjMuhPAzPNcPdLO/O7fAizP6nNozaMzmAdaZ51OTQ0qNK0aMqrYOYCv/8QALRAAAgICAQMDAwQDAQEBAAAAAQIAAwQREhATIRQgMQUiMBUjMkAkM0FQNEL/2gAIAQAAAQUCmuvGcIPEB9mpqHrv2nfXc3Nzc37tTU+Jvpqampr2ATUI9+v/ABt9Nezc5TlOXXXTfQCAdRD+MCa6a6nU1NdAAZ8TW4BqPNezxvwRwUgj+nxJmpqcT+YCa9+pqa6667m/65mp4E3PE3D7dQTYhhmoYNQ/j0ZwMAnOb2YZrcZRNTU1+Lc3/wCHr+1rpqa6ibnKHpubm+m+m/Zqa9u+u/7G4WAnJZyWc0nNZ3EE79U9RVDdWIL6jBbWZyWbE2J46eP/ABD7NzfQWLAR+HfXc5Cd1IcioT1iQ5s9a09a8OZZPV2z1Vs9TbO/aZ3bJ3HnNpzaczORm+gA9qwicYdghjOZnMzm85mdxp3Ggczm87lk7rid+yeotnqboMq6erugzbYM+euEXNQkMGH9nfXkZ3GE7rzm07jTuvBkWT1Lz1Tw5Lw32GFiZvrv2bm5vrub92pxMCzgJxnAjpvq8E17QIDCR037RCOqsyQZds9ZYJ62PmWErmWCDMBnqZ6ieoE9QJ6gTvrO+s7yTuLO4s5rOQmxOQm//B17fHQNN9UeFNz4M+I/yBNTjD1BGt+enz0+ZxgEIitGEHQzftrM1+Pc3NzkZyM5GcjOR/u66CMAJ89FhMBhGooitG0Z5EHkFJuP4g8zh7AOmhB4PFWnanHUPiAz5gOpv7tQ+YfYJiVta/o7oMO2HDeeisg+n2GegsjYTCemnplhoqEFVE7WNO3hwrh7PpJvGhNUDVCd1fzAGf8AdKYV/Hr2A7moR0MPyr66CN8/MWGN4h8qJ56DoJqHXUMVKuDCOmoeitNiHRnH2YVr0v6/Ihzcgxr7GHNpyM30Y6FZm/66wjlPgiaBjVke/XUdFX7TXNaHg9PmcYRK21AY0Wa30K7gUx10V+H+R0BE/wCMuumuqsROYMOx7/8AsEwkFjHH0e1XOxVy7Kb7KQYbsq4V5OTi2B0xbxDgWAPjugKma/qDXT5mypiPuPVCpHvEHTc8GCf9sXqfPTcB8QQjcAIjnUH3DUaCABpxAn/5b266BiJsT5nwT89B12RF5isjw0PmEzkwhsed6wQ3Wwu5mzCf6A9u9TU5TluKZ5Uq0efJ7YnbI6a6Dovyfnzre4YJqAzW4YPPTQnLU3ynOM+4P4CN8wTc3ucCZwaBGnBp2XhrYTjBU5i4txi4d0/Trp+mXbb6c4K/SvCYGHpsfAWdn6fEq+nbSzFqhzqwpvxnV1xTHGLP8bi/Dl/ZMHgamiDrojdCIDuKSJ4MZDOM4wgQeJ89dT5hG+ghG5odNQ66GDpuV43dr9BbP0+6fp98X6e8TCcT0e56JIMOqekrno6oMetYBqbhtQQ5FYjZCRzY07TGLjKHvoqVFpsI7WOsYLsj+oR7R7N9NxWhEBh6CCCERZxmiIpEKT/s/wCTyCD5GpqETi04tOLQK84kztCdjYOO84tNNOJmC37ABizlOU379xtx1hUwiGJWGGhr37/oD3ceh6DqvyNxvkTjAJvwPMWECamjNagXcKQVmClzDjvFxrTBg2GegOxgz0YnpRHx2Eppu4tjWE+kGnx1UUqjRxpFYwV7C1iKmp89QPbuFo0PGNxh1D01017d/wBP46n2bm4YBCIein2EdB4g+O2dY+ITForAt7JnbewJiqJ26xNIJsTc2J4h1N1znXOaTmk5JNieCFSduFBPE7oE7ogvnfErsDe4gztw1ztmNWY1TQUuY1ZEI6amvx6/JvoDD137Nzc3NdNwddbmjKfDYz8UVmc9sGBVWbmzCzRVBBAEJScljPNmeYvibE2s3EJLBQIWAncWGwRm3CGg3NRauU7KwLqa/DsdH4mWBYYfZqampqamvzkTU37R4nzNAjXs+YfB+IPMA3FqMWvwECmuljBpRv2HULze+hE1OM4zhOE4zUXwfmEdPEJSeJ4grEA179zc3NwtDYBO+NNYTD/a3qbE8Ga6Efaeg6b9uuUFe4lTbSqL4iVWNErVJubm5uFwIGh89dzc3OU302fZQZ8mamoQIo+736mpqFDDW0NLQ0NOw0alxCNe3U1Nfh1+P466MU+Ou+gh6AAgbEqqaJWTBjAqtKL0PsYknthfw69gi0wKF6He/ummnEysfm1NdHrBllJWH8h/ob6bh+em4PM1sESmomV1DdVCwaA9xmumugrgrEKQrCIu2XtJOHipSwFc3xiXhj7NichFI/okSwR0h/riHprXX56AwdP+1puKiyrGG+CjrucpubnKFvYFm1E5mM/EcwYWqhtoE7+Ki+sohzoc5zDlWGG1jMNiLlLMvmeZ5nmeYlbCcWg37tfi8GPWJZVCuvfr83x137wNkITFxrOVFdiRE2dhZ3FncWc5ubm+mpxmpxnxNzc3PqTFapszc37sP/6sVd43bnCcZwlSAN+Pc3N+3RgJ39pltCxkAh95/Jqa6ET46CFempXsNRqt13ZPuWahQGdsTh7dzfUN4OvZnjljfhxNnJwv9J9u/wCrZ90PHbMuz03N/nM3NgzU8iAzcDQJyiJqIm5VQYmLUp6bhMJ9w6jTAoZozXTI/wBXLzvrubnIQtNmY13auxXCv7wfwamoR7T0AniNqPXuWJN6gIMP9EpuFSIs+Yy66UgGBZwIetWqas7LWpXN+zXuv+oUVH9WaV/VEaJYtgqfUIhHS7IrruCNY2mlWLk2EYOSZ+n6hxap2MRVPoQUox2Xs1rMe6q1PUJrvpO+k7qznOYncEBm5v8ADr37hhYxhuPXCsB1N9CPbr8NZjnzNQLuCtYqgSuksPKQffKKzVGqrc6hX2a6ifU3dMTqrMjYWb3YlhEsKorZcosDx77KGfNYl8rYFqwuTO/ZGsLjiBNgQWs0ouKt6XuN+ntF+mOR+m2z0F4npctZ6fLMx0vqPQfj1NTUKiFQIdT4h3Au46LGWFdTepy6H8gMP8dwGL8TVZSgfaqai8YOrD2qCB0sVbEzcE0dUpd5Xh8Q2bVWL8s5C2PekYFg3bWEmbM+YF89pjOxbO1dCls+6DmZvni1/UbFOLYltRh6ef6ep2lhUCEdDCkYR0ldas2JQTVb9Pj0uhKGcZr3/IgM7ZnAQTcpZqim0cHaisCb1Oc7kLTc372mVi1sEamqc8p4uG7RcXHrF50CQJ9xNP0/JtifSEEXBxK4LMOqH6jirMbNS9l+oOZ+pvBnkz1dRnLCMTGxOd+LTkLx9CylXXrrr8QfnZo7mczNiEichCUm0MZUExrgpVw0ZQwuwAZZh3rGVhOJnEzR66gmtEGI56fMor5utDbFK8mO20IfbvoJvrblhW/yrYuGkStK+mXmdiF7ciyxWtuo+lQWYeJHzmM55VpXDLTtYpHGoROaZdDkr28qdnIgpu1wsnFFjgMvdYTB3S/Tx7RAfzPGPTjBqbE+2aWaBhUaot7UTIQwEdHrV4cVZkYb2R8HIE4EFug8N8zgIPHSmprTXjlZUjr0blP+dN+3c3Nzc303Ny7N4NYz3PV9Ot36mnFW22y2BErlSWQ1FotNSTc3NavwiEfpyUQ31CX/AFCmpHyGtbHuXMXGsbhTl02izIr41WvbUEt16cmemgxyIa7hN3JK7kf8rLucY40PLFgBPE0IQ00YROE4GU2GqDKSA7HW7GrtF+LbVNmeN9kdmeYvyt/34wYVqvHq3Qn27m5ubm5oib6ZVzbVWsaquvETJynunxO4KlS51sp+pIQfqlEP1UT9VeH6pkGYtj2HK2KzkXNOTHqBMSpb7cnKdFtt1kJSltteDSsbFHP/AClge6Gy+G/IWfqCKUvqeWVpYOb0Eefx/EIBna0bQwnKbE51gc1nJJtd6hBhEMS5qSPqBldi2DoRuWYGPZKn2OHNvgpnOyryeYlDKF2B03H+CfZucpvoASTUEgtWuPcWnITOchV25QJi1W2Nc3S1SVg+SBrrX+3BUMik4OUp9JkEegyY2HcoroyqnbGvLYuPt7yHmGyPUGUQ3KDznqwJ6iwz1Bj3Ah6KmKvl0yv6mNVqAuLb3qPyMnINjkQjjFPKeFPIEc4xh6FtQsZy1EvURM2yH6iAtX1NGItqsgr7bBY9PGamI2nU7g2JyJ6GN7GMa2B5WecSpVD2+S5nnozcRx71ij0y2ObT18gllM/Y3vG1zx9d2oT1VgnndwIu5WCc3m2aUHKobvfUY1uZOIaf4zTePXT+oaj/AFDKaEHjRXdaqYlIgVRNCcEnBBKXXIZ8ftzG0tCvv8PnoRBy6MeIISyPX58T9szVM3WJtIeJnCGsQqJxh2JygMYVsljsqcpyExOOltsnKD+e+h1DDDHI1sGUUNbNLRU7bbfRSCzZWNWbrluh+422M7cwIObQ81m5yg207VpnZsnZadkTt0iN6fXFXsYcCBmCHKyDCbGnGcZxE1KxsVruVitJjUfc1wWWZwEOc5PrXEr+oGVZaWTOrbGvawWJXx7Y1AeMDg/j0JrQuXlFHi6gmdh1jPO4OnkTt84ykQIWhQifdPmECA8ZzawmvcIKmq5q4mVZWyfUHBOVStbZ9ejl5Nkd83eOHqiZBdRYbIzeaq6zX3acTHqzu8brqjX6qji1ldguDGE7PxGbiKqy5p+n2QYE9C0v+m5CwpZObT5mpxnGahht3O9ZryYPHup/2IdLRqy63ICh7WctbUkOW09ZbBmRGR5jZe4cc0xjqtH2mzBFPGKwb8eunmHcatXlmMGhotB9FaSMcqXpedtoVI6BSYUE0IDwnPw3GxK2rQdyqK6aLkzuhZTZYR6lmUHdiVIjPcqsblLLysyLbKnnp6+3YLKnPKwqSlmQVShf5cpi4bXmsVY6Nlqs/UFleepiZAaZFFOUuZjvXZ8H2N8H5A97Sk/vctSp+3WXJNt2+gRjCrDoDqJaXmPl7mOq0AWCbGy5gMXQHOBwfduAyzJpqJyagFvrac1m+hU8vmEJCAwRdNZSVLgceQmtTipm2Sc2snE6RLeLDZ+xZ3DNkxMWwyrKqZVyKGWw1g8mMYI05hj+8I+QjRK+5O6ap3aovdcWur00o9j4mPzNuSElmQ7w2id2C6VZREqytx+GTVkIyuD7G/CTFOnY7eXP0rXXVx0Tc/iWdXTFtVSL6orCC2vdttpfEye4vJd8ocjiFyQazluYt6kPaNXZFr2BVeen4s1WYs3kA05F6vR9WJNN9d0VlYmPm0IwzMcwZFPE/UMMS36ljtGfIcO1qy6i6t9KlXMmbsZSPPKuNS/c47SmwVju4UN1d89OePMqRl1JEyKrbAtfeHqNkdpDbXFWxxriGbxi5JqezIJllkZi3tquZDXZ4+oDkp8QN1J6D3noPLbjHZHzro/yfmUZVtEbIeyU241dVW1yF4sP4ztefU10y/Ia1sbJJbHt7zpVkAUvfhnJf7EtzLJTXbUcsC0VC5VtXddVzVwX0mM1LQ49ZBpZXNd1IszrnVSzQ8uIt0abO6ftpsqyi8P86vC9uDisSycQZ261ndMBrQXOrWUItlhT6csvOPyW2tZkOLLJj381ssDymwCLSttli0xRXWuYiY9fLQJhO+gE4QrCOmJf2yV3Snnpub6hpynKcpynOcpynLxxZY7+OityG9Q7jHqIlhVRfYkxRZkhsO4irGcB63AyKqaj3EFKXdtrcvlLLcmUIhlq1LNqJy2A4D2i1Jj0+pS2vGxT/hOFPGVVd+Ninjr0qcleY2NRx9Hilb6kojDuqKLYvdRWfuQElhUGcqisvchUvLKhSwndE0ltZDVN0WgtBQ1Zs7gciD77rbUopDF4WYPlsl4jnr8Tz0YdaMiystvuNXznYediydtoK526pxxp/hwthTv4wn6gwnrp66yNl5DQszexW4wONF1jHl1Q6bsUmDEbkMmmpD9TAn6hzjfUOJxr3umTj0Vz9PxGORiCwdzLtHKyWWdqLlvpMuLkCZJZ4u6rGByTZg3s92PbU9fKuqrIbbv3o9ZpsFi3jVmsvGFiL3YAZ9iweOniGxVnfWC5TDYI+lCupg1OR2VrBZF0G0mIwOM23F3byIKnS24Bq6xAiaptXjaEEJ30HyZsaj+xMi1Af64g8nHd1btVrCEYvg2hRVYa9mV3/bXnWJC+P3se2ki770a9pZZ3ICYaL+NeRZXEzEedzwXrMS8qWyWVlvpvOQrVlP8A6DXXYqVM2QMeW02I9jKp5yvbQvsQREdhVXxnFWDg1N221aFlYsjK2gHKq6KtfF1ryOyj8mi4t2r6jWobTchLHE3qM/JOqmDUJ1Cd/j176quZ/T6Qtv08BMbs7Y0LYr45joyNXjPYKsSswYyLNJHx0tBxbhK7mSODYqd4U3DmoS1YilHtrRWNNcPCDcNstrqeV44ErftDJNFj2hXCgLDbockZkBaaTeSYLm1UmR2XZ+5yvYKtwW613muUJ+6uvZsr0laVsFcUv6qPkO0R7Fl+RbbO2RPADO4NbsZYOR4Fp9qzBxO6HSmuOduW7zWcVX379wgGy+DkJUqFp2nERC7Pj2ILaHqH07AOQczGxGrbC+22mykwSpe7i4bKpxrkuuv+nbtxMTt2cau5l099Vu7bjj3T4jaiGW644IY2Kp5WW6iBmllNlTu3Ms9bRrGi2FYpV5tklrtujKNbXDkK7A0uOiHGk4sy4tbzEr9PVkhTkHSylVpKZVnesxq2BryOXdsgtedxpYeU5rqqlivpad3JSsGlm9kvPLHfbAbbGsBaa+ajGrqXIyOcAErvfkTeoCPdGNKqemNQC9mPilbMNkr6rTYwNZUBSZ6fVfpXhorEGDU4FLY91N54LUgMz/miy1LLC/Jc7I36ku4RWryRzlgqx5lsBTiZxrD5WPaPpw7eX9SsKWLkLFLrdzGNU/7lPfZpVlI071TROOxUtpbjSlmYFXv6cZlE9SolzYdwfH4WdpomIZXZXxbiwawAuQ0pqshBB5tyF1c+yY2e4iXBo9VbTKrewpj84MXJEuxb0CO6EchaUw+O6uXKtRSqafKjtuE9KULAcFIrnZ8HSlVQx720dytY1CkFSp1pu2xnZVYEWDxOFhdktQE86+BdRjeexWB3QaVTZrN2mYtH/almMdYldgl602TfGdybM9ARLFS+vIxnAr4sqUoH5PyGu9bQeBsdakxcOysYWMoFOOpzMTIybau2YxCrj4XdBxcetm+nYsvxyl5psDNySIxlf+s6rJKg7YznD253CCGv5GwWz0vOU4rUztU8UIrnknLHDIWBCS1BEbYleQ9cpza2Xu85fW9E9Rckp+pWLLGW5XyKmWzHVouo3bWEmeTFqZicciF9TuvF4mIFVdzJ+6agjNqchEfc5CIauS5W2484a52uM34Cxq67A11RTSVo1ul86VtFbeQVzLv3lx7H7vdSaNg7yTv1Gd7g1SuZ+62bl472tfiti4rbU05waU/a1FlNZ+oEbpu7UryREvqaW2uMhloLaqctZclllljs22mjrTEdoEcdAEiVP/i/vF2eHc7mj3NzxK7TXUM1mHKK9qTv7HfJmQyvX5gcxbozhownEwEqRktq3IR0q7ZKlEhtUnvIsLVvKaRviiTlynmtq8nYcJaGxkMalUqxH2LLOJW8mEgEP5t/kRqAlicG+Ck91fpggwuNquiTJJKNddr92yLh2a/Y4VIjx8Ze1YwaHHJAxrjPupbuLptwW6iftztuuRwJnrMeu0WqVB5Cy6muU2DITNxDinlylJTMx1xrGB/duvx9zslDWK7J3HqBVuI0DzDuMRzVYluOFtYqEuyAuFWAaxUtuQWgvOqLawlqV8IQDNeU+bCEfGqNsWuvjdjoxyK3qbRg1AF0azNQlBOU8EMIEHL0qkHHsn3KfvKbm5RkhpWoi0hoFXkvic1Wd7bWLdZFo7Y7nlnYwIxCgufFSo234FXTkKBjqZWnblxIgaB/H2tC9lVdzWMBxhlVtlbWWLZKsYa9QvEXK69udgLARvHeytsZq6mtyFrBdWXEQXX209gnkjUPbkO1GXELVWX43dtxuKJmNkbxqzYLa0e00rSruthXuApkKBxHK7Mt5DLuB5Hljip6r8lYLykbbT06MEppEr/jkVcLT56NqYlHdfQ0H2kyNGqjExrlXCxic3BRKRyE/mvYaGsTgYRx6KdGvKrAty6uI8mvi73XCkdyu5MdA0IjWcYzl4jGwhABbtAGPLUr1ugtaW/bFS9xgg2r6hOwnkAgB7OU3AdRDHbiGJJghi+YjskC1q8xj4tQOqoioN22Ph9wWFkPrbN93mWybLqlmE3HJm4PIuzG9RVZzFmPXadmWMXa6pUleQ9aF3eUBRLe287HKz4mH4D28Yr6cVgteoqjW8Ty8mkXtZ9P4hMRe3VRVybym53DAdrkt9mJaabF8qyjiw1lZDkk9WG4dddSsdw7899+PqGn/8QAKhEAAgIABQUAAgICAwAAAAAAAAECEQMQEiExEyAwQVEyQCJhBEIUUnH/2gAIAQIRAT8BfbsPtWxZZfa42PDQ4igKBLDKNIoWODRRRRRRRRRRRp8N50UV5KKKLouyism8qGiiijSKBSLLNjY2yrKxjLLLL8N+Su2yy8r7a7aKKNPjo2NjTfGT8dIo0lFFFFGk0mk0mn9RZV3WKhrK+y4+zXH0av6NRbLL8tfO2vA1779Red5SicZJo1ee868TkNmllSNDOmdM6R0jTH6fxL/obaOpJHWkOX9F/pVnRRpZokdNmg0lDijpsiq7W6J4pKaeV/rqmRo02UkWOZLGZ1mdRik2UbEptcHUkJz952UPDTP+NFmJgaeM7L/SQl8I4f3Kxyy2HpHpKQkiyhrvc38JTxfhPV/t+rGDYsNCRQxvKholtsbkbFJCKRt4XT5MXC+fpRVkcP28rLNQ8rJMeJJmqQ5SfsogqYoM6bOm7EqzvsooaMT/AB73Q4NeeMHYk1z4ZIcDSUxJigR4zrtvtlwTi+5eCMBKi86yfwSHEYykfxKibGqPwg4/7FYX0rD+ihh/9iSSew+2y8mSVjwyivFFWI3ZXayEHJWa8OBjYkXtFZaS19Nvo3AuH0VfRbeVtZNGkUIyRLCa4OOe5bkYV4FNoliXyPEHM1yfBob5Z04jw0YcUkUhwgONfiYc0OZqNTNRfe4FZ8CxPpszQh4Xw4Kygnm+1slMlIvVlFUhuuzfL/wtWfxQ5UdQ6kkKaYn3USh8OMnkmLENaNmcZYeyNs2SkbjZOX01bH5GlmlibRqNRbYpMd/TqUPEy1vhFxjyddfDrr4KcZcEVT8DJYfw4L7qN17IzcTXfsr3Yn9NitxyMSEWSiSnGB1LIzh7NhlsRdIcrzS2JLTwRw22LCijpxHh1wRtrfK/C8NM6SHhlZUxIo0m3osTQjS+TqMbMWTW0SGB7ZoQ8KLJQeE9iD1IoSH2IkiKpFFFFEIJrceFH4ONFLLYSostGlXsUJjpFJnRixooia2hRscvWSekUmxtIkyt+yUdSpmHhuOdWaDQaTSVkmPKMbIL6fxvkel8GmTHYtkJ/wBm9HI5NCafI18NConHSyOxZZTNy2XlKQ/ryYl5dEhIcaFBs6Vcs06WWaCER2WNCw/otS4LLPW5HQKaRJ7G5RwamWy4rJkEk9ycrGKLY8NrOimaWYeFqF/i/WOKi6KjZtI0Cik6YtqRpipCqSG6OSjZF2PgakRTGJmvassNRe6J4bW4rRJr4K2Vfs1/ChUiO/JKKz5NP1mlmn6U/RoG16IUS3RhwTdkWk2SxFRp+ko0yDXA1ZdOpCUfRJexG3wcDci9QqLSNY5L2KSNUSXIqLrgWO/Y2vRGVrcpS5FDfY5ODZkeRzNyhIjH6aq2GrGkhSodPgUF/scfiStEXZ/aI4je51L5HFWJW9xq3RVGpexKhyiRerkbS4P5MStUxnByKOo6e9DwmleW+Ua9miPKYlqFhafZDgkQ/GyfJhLcx/WXrKXJEnnh78mD+Ri/mejDZN7CyfJJbDbF+RH8jESIclb5QFhxaEkNIiR/I9i4ZPOxmtn/xAApEQACAgEDBAICAgMBAAAAAAAAAQIREgMQIRMgMUEwUSJAMmEUQnFS/9oACAEBEQE/Adr7F2vkoruTMjIyMhSLLMhSLLLL7L2ssv4rL2v56KLL7LEyyyyzItlFd9bUUUUUYmLMGYMwMDpnTOmYGBgYowMDpnTMDAwMGYMxZX7t9l/e0RsT3S3bL2xMUUikUikUUUUUUV+o9r2fa1IT2Ynu8r4EpmMvsxl9lP7K/sx/sxKKXxX9/DfYn6POzFvjstq2k2i2cn5H5CyL+VrZ2ZF/ColGcTKP2PUXo6x1jrnXf0dSQpyLf2R8eTHL2dJChXsS/Svey0ZxOpE60TqHURmLWZ1yTt7V2KJFfszbiSk35MqLbKMTExKK3SKRwX2ZHUZGdl/qtkpEtS90t+Tk5+OjGIq/VnqpD1GXfet6fyxl+lOaRPU+t6MSt4q2dJHTRgjFDXA2WX8Nli1KFK/ivss1Jok1utr7I+S97G+B72X2V3R+VslMlKzjt0tO+WdOJLTa2ivsyf0fkfkUzGX2SXJSMStl212IUxfHIn5H3acsTqDkzTS9FDnFHV/ozl9Gc/o6kvoc79DqXz2ZGTTFq/YpX3PgnqWX20UWWyGn9l/Q4X/IySOoyM7JumZMUpD48k4uyijEr4E+2traFq/Yn9EmWzUarkb2RRRW9Gnp+x/keCjoxOlEcV6I1RaLRL7Y/Bcve9JmLK70+yjEcDFjtDbkJM1Vct0IjElRiJCgx71twcHG2NmJRSQ3fgwZgxL0OVl/ApHnbwLsyofPolpRkdNL0OTrwf8AB3Rw1yLTVEXjxs5FM5QpX2V2MaEiiiS7L7LHvk0ZsWoKVllryMsyRK35MaMWho/pHTEuTliW7QvHxWWO/Q/s1HTFMVy9Ek0VfhiTo97fkvRkq5KR0/s6dmKuhTcXyLgtMf8A0xTXA5UiMPdnC5JVIxSQouXJj99y3vfkorZwEtpyS8mpLj8RrUrwKMo8sWrBLkWPoabY016E7ZVPzwJRfskpeiMvvyScr8mm8ldklZRQ5JHCKW8Ics8cJlNcCXYvhboWtEcyM7HqRR1/ozUkPjyLVfgnqIWJhzZBpeh6v/keMvRiNJEVzwakWShJkUVY5exNSZikUcstURlfg1MmqRCGPLIjkkR1ovezND1Io1NVrwf5P0KTatDzceBZaflnVJSlONpDWSbFKbgiUXBp2Y2U0ex3N8GLj6FN3yiOJKS9ERxXkx9lGtn4ZDVT4G4+yEXXDsk0vRdGC9lpDtk+CMm2Vt4Mn6RkOf0Zr2ZMUX7JpkeCUpLgaYoGTfvwRnxVmopCk14GnONolmvJBuqZK/CKl9imcE44lMxMT8v9Rxb9nTbIeB2jz5HoL0JP2iemk/xFcfBl97SpiyPXJ07OCyTfonJ+mYuXki8fRbfgcRJryZt+D/oqZJNCf2iWilxZ0KRGfHJKeKuJF0rHKxRHz5Iwn7J/h4I5PyZRRKSTtCZ5JyrhCbR1qjbFrxk6LONpJ+jOftDeJKWa5RqeiPkn5o0nZLwaXvb2Mj4GaY9tTjwangh/A1HVGqlwQ5Zqe9tKKd2fxuiJ5jyPwQZP+Il+OzFNpmTF4JH+my8ojstokYo//8QAPxAAAQMCAwUECAYCAAYDAQAAAQACEQMhEiIxEDJBUWETcYGRBCAjMDNAQqFQUmJygpI0sRQkQ3CiwWDR4fD/2gAIAQAABj8C/wCxWnr3Wv8A8tv6t/8AsGOq1C3gt4LeC3gt8L4gW+Fvhb4WoWq1Wv4br77VbwW8uKsxbgW6FwWq3lvrfK3yt8reK3itStStfdaq5Wq1W8VvFbx81qfNbxVnlb7lvnzW+5b5W+VvreXAq7FuFAEEKxn53VarVareK3lquC4Lgt5a/iWUwtQuC0WWwV7rRaLRaLRaFaFaHZqtVqtVqtfw2FZX2x83HyOq1Wv4Lf1595yPv3BoutxXGzgtW+au9nmviU/NXrMX+RTXxx4BfH+y+Of6r4z/ACW9VVu1W5U81ZjvNfD+6+E35+fXgq3yd/euLOIhat8l8RXctStfm9fn77L+tf39x6jm4gFBKu8jwUdt9lHafZfF+yJY7F0WZmHqVgY1zv1RZCWRPNfEpH+SmWeB+ZupCjipatPdRshQrbZXX3F1Hqx8lKb/AMsx1kZ9FjuW6QtNlnO818R3mrVHea+I7zV3fNwVMqVKutfc29x199otFotNmmyzSV8Ny+Gt0LQINtJWeoGq5xeK3HHxWj/Nbp8SslvFZb9FmLVvgLKTK0cSsunz0+4tp69vc32x6mKVYLRaBX1XBXK1XHbZgWmzeWqgPhZPSwPBZ/SWlZq7SESx2LpKk4B/JZq1+isbfgVldSPWut7ZdW9a+2y09S612WutFpsa0NuPeby3wt8Lht3wCuvz1vl9FYLdXALVXet9byyiVZjfFfQFd1+ixYoCkGQvZtuocyCpwhbvvtPwe3ubepPBS5boW7Pcg3DACzLRaercrVarUritFuq7ZVmbNNunvdFotFcfhV/XgssrWVySrDbpt47LevHqaKzdmi3VpH45YKC0KwkrPbooHrW93Pymq1R/DrqAr2C6+rr7+PkdVvLXbp+FWUqzfFZjBW762ELn766tsn8c0U8FA1Ukz8lLnHuW6FksUMpEq6sz7rh1gzHqarX5S/4JZSFcE9y/SrNHvLqw2S4wF8Rkd6+It4rCGmO9ZafmrbNVco/qYUDOq19XeWv4nZWQylaZViOzX37D+v3VPqVTvw+ZvskfgAeviW/Kufvb+qehn3VKNcSI/K9w+/ztvm9dsOjqpawBRHvdYPqkRM2hcvctedAVVZ+apIt8zf5+yLCMw4ITTLgeSlrhhQxOAn3cCXn9KtRb5r2jC3uuppvDu5QdtrrBUmW8IkrDTaXHkFGF09yhtF/iIW5h/c6F7T0qi08pX+ZT/wBq/pLyeYaviVj5LEz0au9vMvRJ9BGECfiLBTlo4AmfJag+OzX1NVb5PRafKx6l9syLLE44vBFoKwCS3/Sl7A7v2a+4dg42n1JaSDzCwVLP4Hmo4Iudoi2nTNQH7rHSpUW8JglHP6Ow82i5X+U7+LVBdVf+50K9IP7yVZjW9wWoHdCh9SRylXK3isJe6/ElDCBk6JxpmKdTMFaqz+qtUp/1K36X3VjS8yrYD3PWrf7qHBuHodPlLbLrQ/J3232ZQ8v7kGD6dbo6YeSge8LHCWnVYqcup/622BUvMK4Lo6puHQaAKHktMaKa3pbf2zKyy7whWhaq7lxKtTefBfAf5L4Dv6rccP4q64lcqlK/VMDoc3ieKxsM/M6erotFhJhOpv0mxXszKgiPd6q66LTZomu0lZsOsmNSp3Vvn32JsMf9ioLQ48IWSlgHWy9tWPc1YsI/c9d54KygLdwj9S9pWJ6NCuz+xX/RCynyaiwBwgTLl/ivUf8ACulf4x/ss1D7hXYB5IVG078wi0huLgeKbUbMbtQT90HNMg6fL228VulaLjss5Z3dysZUOAK9m6Ct0nuV5WnuY2xLZ6rC54dF9Fii67MtdHP3uBgL38mq5FIeZUvLn95WRgbswtbLuqGNxKw02lx6LF6Q/wDi1QzDP6blZQGfuuiMTndGL2gazq9y+PTB/aVl9IpA/tICh18TfMKQMbjZoX+S1vcxf5XnTCu+ge+mr+j0H9zoWb0Wqzqy6mm7tS3ho4L2rsTdJi46FH0fVhzUz05fNcQtCVuLcK3Ct1WR0MqJjv25mgrIS1WqT/GFuEqDquW2+zTZYGOakNAPMrM6dmU/Iwxod1KlxxOQxuDBx6Ls/Rm4jxKzuJ6L2pM/kbvKWUadIc6mYr2lao7xwhWpjbhAy0326SqIm7wdt3NHir1af9lLXCo7gAhVfUPa8hYNCIcIq6Twf/8Aq7PEMdK7CiZwYbHEsj2k/uhTSw2tJMhXrf1ar16vmvjVv7K1er5rLWB/c1ZqYd+wqAc3I2PvtAv/AErreW8tQtQtWreC1C1ViD626AVuSOYUf+kF2heJJsNlloi0XxcrQhiK1Pv77LI09OawgSVicc/NYRZvLZi+o6dEKgOYL21nfpVmvKtR8yrUm+asGDwXavN3PAKpHS5V6r/NXLvP1ML6gptiZKHozQ0BsaBU64/6gzJ9Nxw1CJY7mFeX96x0nGi/9Oh8F/0n/wDivg/+StQH91/inwcorMdS71lqNKzDxUVTip/n5d/vpmdt0Oa3VoQuPiua12yEMVK3MFS10+puweiiysLc1ZOFZzcMWaG6rCwXUuDcXNZjf1NfcwEMQcT0WWn5q8bAAYQa25KJ48Spdpy2gj16LDYl8lOpBzW1mPtP1L4D/C6/x6nkvheZC0DjyaZKD2Uakj9KLv8AhqgB4YUavpbXNZ+riUI3Yyqn2mtJ29pHRbzUBquXeo1PRfDIHM2V6lMfyUGrRcOqyVqTf5LKXEDxCHb0iGn6honVPR/ScuoH0hMqEQT7u2yJ2aYluQuC0Wu3XbxV1lc0jkVuX70A9uFZXhDEdVMwNFOK2zSdk+7y3WKqfBZbDot7bJWF7oxcUcdn8VfTgPUlpWeiw/Zbjx4rdqz+5f4o/sVb0Zg+6yBjP2tWLinyC10yrPcr1D5rfe5E0adSTxhCYb3uCv6VTb/Ne09LHgCVhNapHRqjNUY4zEwV7P0dg77qzm0x+kLtPS31CODfzKfgNPAC6ktxnm66s0BaBbjfJboCr0XgZXaJxpDMN6nwcFTw2Ee96bN1dUcsrRcVq5bu3rtsR6mMarBwds1RyyeZK3W+BnZJOvubNsoY1TtguhYW08fUppawMHJQszpi2zKxx7grscO8bbAlfCctyPFXNMd7levS+5X+RI/S1WfVJ4WCOKi7GAMU1ICGH0Njp0Il0qR6PSp94AXxC3uV6jvP1ag6TsNR1w3/AGu3rb5uG/lWqts//VdQm+mUbj6wm12HohC1WvvsoBK4LLbuUlaLclThjZI2WXDZfbdbygrKVLeKGKCEHyTPAKzKiHY0W34zKwuxiV2lT0huH8sypDSR+bgsLdQi1whyjFmP2WIOJanksMDqnFgw1G3HVBzntHSLqaV+akcFGzqVEOPQar6KX3KzekVSsnpNUd90XQH/ALdUcOvFp1W8fXw9B4qA4juKufXI5gqOK/RS06lddn5isoA7lqs1NpU0nZvylGlU7rp/ZQ70d29TPDuRdQGMPywDxQkEO4hWCudlvea7czQVldHRYZ8SjJFlvQssO7vU3dmhRBClsFZhHJQWh06r4ayj7KAgCJ7lkpBCGFzuU2TXu7GmBwmSi9lRoYT1hYqLqVS27FwmvNK3Nw0CeWDLEaWhRZ1QWjim1tRyA0WKlTsDvALOGTzsE3/mbHjGic2Mb/zgztxvy0/uVDGwFrs12Zt7g4ahYamp3X/mUesO73LV3KArrC3TZp6gP1D7rC7UqreJ3CdLIPG68Yll28Qhy9zD6jQeSnFPcrOW8PPbwWhWiKswTzRfMzwhSZWUFfUru2XViicJwqwhRAJ2WVxl70GVKen/APXXw2lT2bTPkszVuBN7HVvkgDUY1sRbRBr6ZyaRqiQ4gWsDcprYDGce5Go8N/SXNRIZSe0cmiyLmUTTaT+a3ksLGlzuQWKpuDhz2awFrK0Wi3lqjTfofsi12+3Xr6w7vctK0i+yBsnj6tkCQR3pofu4k+liljTlIUY1ZRMLewDhCdicDBsomSrSuaxi7Vk9HcR1shIIdy5KwLu5HegfTpCOOlE8SF7OsWE6XkFXrM8lepT/AKrE6rDRfC36ui9qzD4LK8E8llcDGzCXz3XXxQi/GyO9H2gPgjhovngrUTdQ5hCwubdS/e5SomFnMq7YHNNa2k0niXKDQvyasIZB5rC9745MU9kSeqDOzw91lY1X4bHDCylZsTirUAH8CSpfW9sOOBODhTdH1DXwQc108NP9lEvDqjuZWIM8SVvtxINeddei9mAWxxHBFX9cObulMrDVtiunqju95KG2dpFMwD0We6ls1H/rFgnzkB+rgF/kNce5XNOfJSabHLNQfTKkEMHKE/tA028lPbuxxyyhVQXhz3aQsNWi7BzCa/0avdx3VZ0dSpws6w7VNzXHVAdo1zFHZmr3WXtBWH7gvitPeviDwKs7zXs6gA0lY6fpAt1hYX1sf2X035o5A5vGCvZN7M81FSn5aFHCLjSVBeWlZnfdVMZs76jsmboCFDoDUTdw8kAQO/EicbSCU4gADosLn4BzQl9R3ivY+ZK4lYmjhsiozHHRBrCZndKLMeJ/2Rc4btl2ZjGQpNSf2tQbgl7xmLuAUNt7nC7dKcJlrhYot9UetqtVqrKS0gdVA9S1wrqfUsFlc4IuLWyNHxCgVafVZq9+i+Mwt/K9qvTwOPGMTU+mKUSPouPNTTtbimPZlqBBjmPYD9PNW9HJDfqPFeyeWnjxWJoLv3FTgZ4JrWnDPNWh0rHVq4xpgabDvWGpSk66ysjmUzwOhC9r6XScOmqc5kRyf9QRBptqeNwi3shfXG2VlEHqVnoTzJK+C2E19GtFN+krUuPAgLdkeSh3o8tQYDkGg5lQTbiEcZwjzWFmKVYFNZ/5K9SZ0cNFJaD3r4bPJRgA/aoDZ8Nu9HNA4MeLd6rC8YT+XY3tNZu5EUrPnZBPFds98OiGt5+7GAlOdB/+kHtLc2olb1L+61p/3C3mf2V6tJvir+lN8GlX9Kd4U18auf4haekO8QFl9Fn9z1DfR/RwP2L/ABvR/wCiysot7mBfEPhZXJPq6rWfUmJ6LG1720+GJNiHtm8Ijeg2hWofdaYeqyUxPMlAHDTOsQbprsEMLsxag4Nt0dYpxo0mfwKs0Veo+lEVt5B1NzRHA8VrSd3NW6yeSuwJrqNIAtdOq7QNdSJ5IOc5uM80AKIb+oGywls8ZCb7csPcnYqnaNA4mF7Cuwt/K65Qq0aQJGoWNhH7TwWUwO9Y4zN4NWWW/ZZ6n3WjpVgruAWqvOzWO9aSsQKusqvBHVD2QTnCxCxeErFRYco8Su3cQHTx4oOGQ8xomk5m8wncwsxDRzUnNyhPp1DlPCECzF/L3bg2o4B2vX5mwV5LO5S2P4lQ9s9CpbDx904wQG8OewY3Seqs9r28ii57HM4iDZYmZf8ASPZ1ML/sV7T0V2LniK3C2Oqyyp7N0KzvArPLD9lYyO9XpeIWQ+Eqd+Nb3CwTrpiTezy//aBd6KHHjFllMHg16whjGubvYdAviO8EcLpB4Soc0+SsITtJVrbcqlwGJQ5oKLSMvBA4TDtJC0zdFlHmm44xHkoaAe9Pa5kk6GdFhNmjkixj3X4IvIUgfdD2mM8baLioLAVDdgEacfmdcI4nkm4/Sbu0hqc6k/GW6iIRFWOhKjAxzeYVmtbww4botcCCFiGnVDHVM8llv3rQTzHBYjGLm1S2r5rs6jYjkFLXAov7Ow4cShvMm/ehhAc0oOws/wBpuI79xK3WnuUQspWZqxCfBSBDSg185TZwQeIxcY0QGEdFuNauCytAPQIezxLMymOsISTHC8qzo68VjqPpt5YlLch/SdVdzz33U5ioeSY5rLvIKQRh6rHTeCFLqmEotBxN5rdWuEdELeaAcYAXIK0dyOLeQngi63crbuztKsx9Lf8A2iTI6ArG+YPBZGBgHVQLuP29/A1XaOp5f9LKJW45Bo1UxLeYQJ3TxXaVLUv9oNcG0z9JaiQ4zw6qKjC2b32wykSAsDn4XD8yqNAzayNCnlrxGuFY3CzQSZTa8Zo1QqjNUGuFQ7yAUEd0rVddjjoO/VF8ZRbvKnQdEbnlhQY1mKget2It15JgNQCn9TdD4ob78Gk6LVcwrEg9VqVduLqjiBLTw5IVaDg7oFiwDqps6n+XRQrjLxCyBwTRe9yn63K7Ora1isTbu6otrNNRp4QsVKoGn8rlZj/BRihb2zFFlEoycKvICik4u6rmuCCsJUalX4rh4qG3JKL6xDj+VANZEaAKTBPNTSDidJWI02Hxui8uDANSSiKbnudz0G0dsIadLotA7N/Ndp2lN3Sb+piDTHNTIPcVYEouLs35VeOvRfGv3IdnXuQhjdh/K4IgG3NS1rR3bGkazwVy4DiE+rbBoQTqmw/ThwT3kzPCE3NrpCY2o4RTFkzFD3u1whOLGi7eSwVTl4dE8NaDLZcSEB0IVPTDCHBYabiL25J1QkF8eaL6rj20yDyQxku5cwoeLreHirELs3OAg2IOq5NCllMu66Jpqv11ACyfZquTCHax+6NE5gcTHGFwWcj/ANKA5k8tAstu64UOauCxBrx1AU4oKvB7ws/o7D3WUsJY7gsL1Kltig05Wt6Ls5xdWogVG+axOaXN5hZHOb3IPqUxUL+abhIb0cjOKmP03ChtWW8UXHRRTE9SszsR25WzzUX6hA4pEKcWZQbOWKtUt+RupWCl7NnRc1mUBxCgrKuizOXErJA7k04TJ0WIhH6X6QvaHDGhQhze8FTObmngG5UNcoFUDhEKHtBUSSDosdMSOI5IVBomAiXHioiysoaJdwCxVav9Uzs4xty2KJIDA2Jvr3Ijdwmx5LFTcKxB4KOyBQpBje/VdpTqdo4EzGia6pTg8AeSDhSbdfDHmp7ICLyFJLMA07lqWRw1lOArHA7gQh2lZ3MINeQbdxUw5v8AJFjMw4FQQoKw8OKwyOz5Sszfshp5LX7qMWE96nCC7mrFYsYPRYHCegWEU3NKOOoO4LMMXG5Xs2tE8QFx8SnBWlXwNViCohcxyKh5c08zdQYcxezqRT4LekKHw4LGwgP5rDXoS8amYKxUCS3keCvU+yMF07YhSGnxUGm2VAMIF09ytsEN8VfZC02XKJ81hawYUeY4K5QuGzpCspKxeZXZgmOaxE66dU4HXkpi2zWHK6GE5gojC7mVbRp8yjgjGx1ldwCgYnnoEyqAQNOq9qXHoSnRThvPknEvuBA5LFOIuObkECD3EKHMh3TiiazBiNxJWDG1p5LIQ5xGYLCXOb04ISWmVvaI1GE06LvqcLFHUngYyrs2gzyKGCcXNTUDQ7mhjf8AyWXOrw1fEBKxC44rjdHFfAVLqgA6LLr1Wo2arUtKe8AHnGqIAwyolZTIUlqgYQhmzhWOy6022JCDXgPbyKwhsd/BRUHisttmiy2WISeUqXXXIKQbq4UlqgZStx+L8ywnggIJlZ2NK5lXUjir6rC3VaAjnKwF7B1myntpHQL4+WLqAzF1cmmnbCVNgFEuceSxVqnZtRs/FwK+MA5E4nPcLoNbuhYqZxhbqzs80HYSt/FT1Vl21MYqbt5vJOFLPiGI2smnK0auGFYm04afspbBbEzK6LNUYiHtyvth6IOY4up/cK67F5DHt3XIt7INY3V0TKwtp4Br0QV6rWxzRkmebV2Yfjpu4FZMo5Jtemwhk3H5SiRJPBAveGHoF2nxBpKllEK5DGLUklYuxI+6iYA4I2Hes7D4LGzO3pwWs7LLMpYVNSzVZjfFeyODvUO+ymFotVbZY329Fd0BSyrI7llGILQgrFI7pV9kFsLE8ypMwrBcgoJU4NF071jqeQWi1W6SoGq5lX4qG3lGlUMjgJsFJcSrPf5qS45rLXYDCimpc+Z2WUsch9POFjbWjwWGCDzWAnzUsRc9wDeOFSEDbC4xBWJ1WBo0FAgyXFF5pYo/Snuf8ObtFkHFznUODeRUtp/aVhuKmuVQ+m6oPBBp9EaOjgg+kwta/wDToUylFVh/VpKdhs3ou0xHHzlS+eqmRhC0zcJQLsT2G0NK7IUag5hTjF+XArDNgUSHa81iTXOqB8c1gGh4wrPxLNDj3LE5jW9yxNbPQlHCAD3Ilu7/AK22Uv8Ahj7qwVhsnqnR2luEqOyf3ysVGZGongtVCuQFvFRIV9mkrdw+CsMR6hYoA7k3tIQHZMLf2rNTDerRBCxG6habAwx3woFkcBw9ykme/ZoizFhEcFl5I4keisANnJQAo9W/qy1OL2Y/HZ3LkhUILuiZeJUdoR9SwYpAKtYDksUZgTeeCmzW/l2N6g+o8RamYjmtIWmF35mojiOKYySFxnmhCLi4ysWbwKbld/ZYMR0lEJ/giMITQWNIOqgSFMSsrY2PBtHJWqnyQeSSVODd5qfUhOOvBBQ3KizhKmw7vcSLKDqp0UG8LQL/xAApEAEAAgIBBAEEAgMBAQAAAAABABEhMUEQUWFxgSCRobHB0TDw8eFA/9oACAEAAAE/IaGeEpgRTzPaGUlsLh0XgroMIPMcQYKvSIIamkOlTvMpc+etRgu5ZfdHwjn6cqVKisM6lMqVKlSpXRUrpUqVK6V0qVKlSpUqVKlSpWJXSpXV6PSuuYL0jMuZUIVKRgcVLuHUfQTEqUlLxLlveZ7whPfTMYXPLGK6ElF9BuTCV3gEDhBuW82gFwTCVKgZzCmE4zMFKVxRqJK6VKldK6p1qVElSoaBFjkl2JF1E6V0ro/R8yvMA7wJSFSyblYlMplpaEBKYxVQh5dAszKZUqVCXCZmZmVDpcvow6GJlmecIUcdFbldxFlSpUIGZREMowEEI3BbiVKelfXdxCAmYAE2MSjUYYlFcULOs9jHpR36/MPczC5h1X0MyvpJUqVKgdb6X0qBK6V1PqylVOemJjozM9y5UrrbFbl9fiB3OjM9SS7FSpUpKxXeWYNEw6WPmMWxi+uUV0voMIsg1KQrvCu8+YMIMuWS5cuDLl/TiY6X5lneexFgudJ/0onv7k/7cP8A347334hv78/6sGt+7NJ9yaRfmDab5njfePaS+xCu8uGpjuQTv9ZL+lrpiLMS+0tlfRjqy+idBqbainaYehCuieMVpGXBgy5cGDLhAy55SvjEN/enCfiI6FP/AHGf9ydr70VoEW4/aW/+J5UZL/sz/uz/AL8/7s/70tz+SW7pfmdxPiX1t8TJ4jnuYa05hFMX+8p5feZv5JX/AHxmiUt4H5n/AKGd77k4U1P9nQ+1DuUC4PxDbCn/AJzH/wByXcHl1L4Q8S4/SvW5f03FjLix6L0uPQKaYd/DWcW5w7+UwD0oDg/EP+aeCF6p8TnHxNs35ntLl/4AuX1FmZiXDMEuOh3WMTPcQXshHhKlX5jp8TJjyl3x0uEaszjqVCtxSWticzMuX2mRMnaBRHJiZ5fSHKU9k5VviBrOc1h6o5qfU7ZZlNZ82PfwXcGQSBOUnjzx54P3nj/eNWk8CU7y5cWLL+o6XLlwZf130v6Lm4HTjjoEKIULYYydBXaX943M1tMPOUaorUq1w2luNddCITjoYYbKNuw6VUThBgC7dFbKNQNJcTYY6MPFMwnnLlwplS5eHRUTrXR6X46PQuTys8703nf4FuU/QTMp7SvH+CuhmFEvoOBUqOS4JiAe0V1UpLMpqZobK7IS28SgqVHvLGXzS/aK4IkEsqjYvmVnpfHR0SyYCRHuHtfSL6dB7CLYblMjTLVZkgIwdS+0eial4AFzHdpX0zdx94IY/KUVJFV0a7wRCGrD8xNz9+chw5FJR23qKNrIKW+QIBsvtGqrnvWcCPmQN17s05Plv6DEvtKzElsC5T3gSodIqZQ8RiGTP+EhmHRXMyFCoBpalb3KCYGOsxXYZ3ISudJjknIy6JMCHG55emSQvtY9BUDWpoXFbMcMKuYU2TsyWayRC6qpYZ31MU95SRquDWlxpGAyo/5diy411VOwXqiV52V6+9F+X3l/cXvL2LSX0XLlxei/RUqJK6nQehPcqmNMNzavMtojvUZQLMn0EoldpnKhcFGYB2hGZicg47T7CUdxAxDhUtqObi4uSE6mUdEeHDLqDOsy/Hp4DZJshzK7xfKGYTKGTPQnQXEB04nsEx3iYr8wMxq2c4gjmCXgTXRaA7LauFj7C6oB4x5cZe8NcwNoXBlEcDMr1HuwXRd4bRAZXlqXg7ytI5TU1VrDS51r/AnQhGHqW1qXCKhUe5ENynvNExL5czMpFz90iWYB5iBqBcCpbA7wTKBB4DLhgqhjRKM5QsrzM3iBwbIJ2qLfPMqgc+5jmAbcdAVXMgiw0geZgQjLTLFV8y0QnkwlvQjhHpD2pjQzG8hKZcRnEDKl1qXKNVCY4FWnM4AvKcQU4F7lRhieZprENTeoS5WTScobb42gz0ZiO+lc31fqOqSk6DHqMrpV1PuJrMNJD8kqihggK0hUD4E42SN6zL9p8RdG7EVazBPyhtgg5SPLofAw/eV4UJW0gheopFStFaqKDeCI4RstMSOZm+jpmsFHATEuc7pPIl5gmyizxH69cRbH2IsKWZbv7s1MPe43xBdmiZT2qoLHXclluBzzZy3ximLsOMmYkud1S/BezuJW2+Bualg74IAo9zuiZla7Jjo9W/petQJUcSo5hMdG463C4B2QvcIh6QvhLvZE1LdcRRszOZxFDtO+SoKuBckMoKN9I5Z3DDmacJ3rHKjibBK7biLtmwM5OSHeABIJzUd0R36TBvoWIdFFxfmIcoPmFx+yVzCE8NIcH8QuyoLumdwzZYsoAteJwwTGKUgekXYbvaRnDc3OcC8PMqCXthmKo0MoaMUvOUqGXsNywLu6BPiX4nxGMfoa6PV+gCDxCpYOZx09onJ0XFQi4LMTUDcU90FGwj3gvW5rMGKtm5rAOIs7iVNGUWrESsrmY3lUclm5YJd2ZZGjLTuljHBHXPtoN4kKKJslNQZcE+c4LQ7LNRsbZuUcs8i404gO0pL8S+ly4p3qPgIniSd2d8osFRQvbmUq16HM1LlsuXKRZcZjo9KiMz9LQajEl5zPUq5da6ZZXxKdQYsjTqGVTKi7oQ3eIDSa1LGZRziU5HStepammeJY55hyEqZJ48xeieETbSP1+c2VEM6vRK+EaS7+oSsWYovylgVuiyrGO7iuG8Syo8WEw/BAuF8TgD7R8jEMJU+OhR0uX1DHbD2sv5TxTwiPQjHobly/EfCX4ikU7TEx0xHcxMRqWTHf6WpSugZg74lQyVAqOVzPiAXhFHEobmmCA3iWYYn3l2YITmgpdRXzqVbjmcKq5halHEyL5YApD7CYAdoTiDDVM7BDhJSeSXwYqYJRGDEZZNeGPMhxQtL5UHZnog3ZUt5hTxDgRvgJZAMS+iSpUVzPaK8x7fTuE5a9LnhiR6FRPEej0ejK+gkqV9RMCpbLtzOahjUcpsuFRuYTKMHIlZnivprp2sMvxUSNixHvLiYnCR/cyKWEe2MCMAOWbicZhDvOwlk9pTZ64GAhzNzSzbM9k7pNaFiU8GAd3gZFHmVE40tKlfTiKTxTnEOyoNoy+Jh0MZuIjDLL0klRlR+h+nWpZqLIQeYuZ56L4TGR0BrCx6DABBsluQy2jDre4zMGah5lUiZxU7IazR03FJZMGWDyY3crM9pZPTo9579JWE4cAAYQWxTtBtkxOwnhATKmYGB9V/QHyhG4OtE0F56KldxLlRI+okfXRldEiRIyujKidE6MqVCyWMTgpgPmG4Gj0bnfzPUwxGmVNTmYKqJhAGtSjO0RA7cerL7oy9RtoF0MejHGCXUtLdNu0tLe8IDBz4lktvK41Qys1ieUT2ndQqU9Ll/RXRaLiuZ0ZsdpDSEF5+pYpMyuuIiMMJMRJUSYmI9CdUlRlSoJVxBlMfubiDmBhzDkjhmnD05egI6jd3UF8Q48okMQeJgv4kJKck0wuLPCNzMZayrhzLTPVPEorUQlQCfOa8QahcCuNxMLgFCab5hQtqNp7p4ZS2nW+ty5f00Ri09zscyGKgj1WXGMYx99LS5csjXVZfU/Et1Nnme5aTIjh1HAWUy4lUypYxXQAdYY51GaK4LxIKBrqyokqo4AbDM+egIjnUo5zO0SiHKpK+DQTu/JE2F2q18kXl9sPEr5Pio8RXkkoBeml98l+Z8kx3JjsiUeeKMMt7dK+i25f011qXmIurTnIUelRiR6MYkSPR6XMdHo9DeJg5g7Mw4YpY1Bm8koEb12mLMRcu+INBHsjGjDDJURbLtk7zApVArQHqXHzlfo1nSiBcK+J0gpqJQe8tQy/cLwe7vic8eIfemI1afMOah8TXLOczfv3hmOj7QsmsYDN5rvl98WCyA5YLbl4joYI2yprpfQ+sdFCJgaemG7JczE6MxGURhIjMnVj1YsercO0wYtmGBmmJWo+JxiWdphBEKMAifsmCabUy83LeqhjL0zxMbamUw6CAiF3Ly5LrG++i0vNoS6usdK8u7WDUuWy5crW8h9yOzwPxiU7y5GVdpYXZr6qlSutynUtlst6NljG4yo0oh0Q7nENOI3LleIkYy0uPRlSpUrpXTLjoN7roAq5ues2J0u5CEuGnESTjdx2qBnLXqcrjec6jHCR7Uayqi5hUsgJ4Qjd3cDDKF5GNEuXMp4Ivpf1XD2IM5G+gFVMTcId0H/AA3n6cxuW9KzdzhuVAN4hGRYd4x0omkbIroWMYxIx/wW4j3QwnEbEbdS2YamKGrICFVn6jgWwUAnwgEDKsJqMv3iRiQJUro4lsJIT+Y9JUpbSNBzKuI20S0GX0VOlbqcUXv5euYkFVKeCDc3ElTzMwuXXRH66SsWZlefoI9QPdNriGCweDOYgiRnjozMWLF6vWpUo30SwZmMGXblwzO5mAbahSpuU8sVIjIFSqzGA7pbuForFjnowdC5XiVZEbj+H3jxo8uYpbvilXF5a+I+bcvyQryzK0FQ9QsaNGsVKqbalaEHaINgrJ8iWp+YnmvuQgT6QtL9H+rxOWFenXbMtih8IATslAZRy+FV194D9eJ7fxDIOFZHmPvLP/Uu1AOcz3fad0QEtYntD/FJ0ZqV3jXMo6uXIqRzScscibSrKvoJEjEipUSMSMvpbN6NyhnEKSWlShc7acbUwSGUXF7oHL7wAJvTUVLFm4IIjSLqUCgxPMlSonmHlNTEwxK8JcY2D9Bor5INK/gP/cVFCYh3y7PwfymU67WfeVzdaL8BHrh8hcEh/al+JSbJcYhNQnauUlBfAS+fdaxHjnxDWiJaGGkRvqZNfy7SoCDRdPYLlrkHlSwL8f8ApPOnoS/duNj8hImL9Kv6l5V7iM7lR8f4q+gu4hmcS0qN4Ze5fnHSyGLumyxB7SjzHOphNx4y/UhEOnEpbrJKN/hCsudwJouBD1n8iDT5peFWSRhGodMx2IK6JMWJUSVKgAW2VAlYo0GLPmfPvK7Zldtx78IggYPHM0QFeUqItjlZ4mR+DlxxqFwfbT4MTSfnwTiKd2eGfE2yzifauH7iYcT/ADiJp9qB38xGuyeyEcAeMzQbVQ0h2fzKx9hdztcxwFy1VQHeKpfITugMPzKxluDWi/8AOhKYpxONSjtFeMEN7bhLuE4/GdhSxq+kNQGadqspGD7BlxftAcMV2ZYdR+lYJZpl4wGVtaMyWFrmA1FJawLQIYKluVOwVXdx6hXht34jBlTzMGGKOheWb3MpczC+hjmOehzzTdeyexXFuBUJSr0TKof9MxWlrNlh7leYW5cT4mwMNgV4Cd0GbVSsuoM/f5lwwDwDMdYwXA4hVy3qrNN4iTb5Ufz7EEu5jzdLuW9QgkbnchdiLAo+YI0scsDx8pWW1rvOI5dwz0VKmBbqOv8AMxThE6Y8sG4TLEKOXxBlhfzDGtls3/EY3PHYTV0PgHmE2nYXE4MHKuK0A+Z5X2j2MxLZKOzK7GYKC9RcKPczb4YQrkFRrZUsIrLCoaQ2CPc25DNcRDRUed9ElExCLlq3LQV5l1H5dhC6js7Ghsr9B+J+IInmXvedD+5W83edHxARkUAnaHN37YJT4TZ8zDWffb7E0B/GfeZY3bofFxszsV020ewlU5CpG7E5GObav6PLDd6BSzZXypsn9P6Y5Qju37Ry/QB/Ef72CofHNzFXry0B4eT9S47BzK5W56Y6LnzMx6Af8jF9zDYEwYYr2ZZ2yBMo8sQwwX3R7iNpJrWAnhEQBq+6J2ujhNncb6fk4IHKvjJBsK7Ui0+YMWzQcQOACkTs3NaVA8kBAS+0xVSSdAicRXCe1dO2HuLyinE+Za23HEWXDqO7oYwsKL7EPceljeV4rlwDcV+iWugWhyP7loH7i+XmL/GMH2lcgWsq99oyuxJ+GC0H2PwCPXd7uX8zDQHomWwTyQ1xq+B16uKwUvp7QzkzKeY/koJo18I15df78RAi2IARTsH5IvTZXOO4+JkEKlVmEQkw8UI2FX1z1uXhv7BU5D9A/iVlA/JvP9In1DM8xk+zNaBsvwS/8W40QFkuJdybBviDUuObmghVv7obL2UrH+ZzPxzJeSZ6KV2VPiaMHZLQNndgWCI9KLlfMaj3AI+rR4JeZhFbExQKnK/1KrEBFj5YbC4XmCJDGlCwhZWqzN56Zl4rFl9brmW6jSZShYr302rQVdQh9MQNUmglYjy7fBF8quj+WCCPMy/08ynmljMNeYWMJ+jCc6i8b9rpBFWrAUShLpRmNW/5x2F7UqUdiI6LfEWFDX6oYBktGdldpXzAIrD3jXgeP/eGJm7vnj7Tz3n7WkDcD3FUo5z8GfyUJuk9LAFs9uJz8azK1e8aJ6Z6gHP/AE5jBYiPP+JBj4TAS1J+FxgvmVFEuZbBMYL5eJ37kv4nuWAK+kVsr0ZyB6ueCIqqZuj4dS7gLehKPj1IUgkuPnOG2ztUcm/laqhryp3Pct4ptxXBBYoeGZiQfCDcuWlqVTOZ2mMtJbzHsjlC3MWFgVZm0vsiGK+/CI38UeAm1QtpgMHlRMph7PBMwjhF+IsOKw3XQ2CG1iV0C2jL4hclSXYi7MttA1V95W1PSn4mE+5Jh0vQL+Zf/GX7RDmXw2hKTWWURhmliEkdTRoPEz20E7Pynag8wgWnsRxta94aFvDnHKO4IPzFNt2LTw4hy6XnEXzKpQY9otxTxns+BI5oHIf4260+ZbdT9+VEGi/Md4A5nEqN3FsZVxM0GUOFxkvETzia2vvMyN7wOaRcZnZICkBFI/AQgBO3iGts1YwkjfCtQsylDBaFbLiUZHtE2El2mfDNW6riXHEu4kzLlE4rg1uIqs/Er6HyjOotQ/aYq5lVuGuohXDftxDJg4p/Etrw+DpREnGj+GbcfH9IGeh2IVd4mMKv7TW+eW09xa/tKLneQGDkK33NpaBclkCUA9zipxXpJ4Vj1w5tyfMa9byb7xmDPn+ost8TMJDk5izmRAaAAoVYgrCILV73P9CHe7YK/wB8xMUPLGL9URff2pd/Qm+esVHD76pxwzWXz3jSpXpp3gD+Zf8Ag21joCZlCiHZUz2JvEngnNkIZg8RDmwcx5j4T+gua2XzK8hT7nc5iDFjAOz6YHt948pEmWPYsp5+0MrdjmPwnMS1UuIQLAFF6EotHybkNZ2qCUlnedtlO0pFUvMyxq5e6m1jppzLJTKXiEql5labNvDB1eLvzLUN8QVdXkxg2hPLP2JCXdQmxTnYLhrfOVGykexIfodlOn4P6JgqObg+8zTswR8yuIvHA4lLqdsEQOmPd/zE6BdnHcz3HsysOxKcQabJV8TFCUp77vwAi4g5k2f+5mGMYZoGIIizX9pkVXM7S+iAWBaU3Z/cNtY0nZ7TBRSecJqermVj6ab3iYjAxN+WYCL81FK9kJKndwuJ0FrtKFGvuFGB5QtPyo2XUKVQfLORr5nDvllvmLDhjlgQ2mNhcpRXACCtlQKYhwPDzFjByKmKo3W5hQdgWzKq+IUKWv7ECFZ4xWfcswuaUPwgVp+kVpKrZUC9ljFCO9XAYNbC8vqBwjh/hAQaDn0l1xi1Wjhy0/mVtADlqbVBpP8AcKhu47dr+psoXr88LkvCETM56iI5m23pfDLgSjQo/EAcA+MTOyvt+j1gqAVmeR4d41h3bARvzvlYKYhLl9HEsk/4pWnbEJvi/llCuEauLn+7k/W1U7sWYI9V+pQrO5/TGIb5AgoWQSnsoob+it9xsbGbh5hdULEAd3a7g2X1PSutNgSrbKSrbxCjG4dmJQUEMAiwWzLTF5bjSwvVx7XoQ5LwmyZTZEeBnLq7kNu5YGfzL4I+dsxN1yTZj7jUU1JdtnpjRhfeMTn7MV2z1N1Z3VNC4cn8xzmnag6iTeQmROBRU7HeDRBoXkpiEy259jmN7gAuDLjazSPUxC785vt3iAWkQuVxzoLb8wUSY0E+cddikrT2xbwQjD7Lc/o/uUCc3d9s3APESdxGgkE4Zgyh2EBFPEmPfzMotnDBuXL6UtBclHW/oRKGihebEE8zWWZg3HFlfv1ZtDpYmSf40AnbHtBMayR5K98RGyIfac18sYZYtTKNnhhlDn69/RfRyZbxDrDq2YEpt7TVnziDaWC3EuAuFJgU1eSATm+JWBnzHxCkXqPYWuPouuKqOn7eLsJb9TxSXrGK/wBoWsVd5RLIzcOghMPsUW1R6hi7vaKCfLG0S/eImBDwmOYd1qFB4vIY0tI47f8Ak4XDI/3GjEBAv9njxLIAWYfAluowWPsssNct9+SqA7kv+rl1SEcovxF/YRkqUGOHci1IZomMUXK2+3qDUbrAaJfXIR2U8XKNo9R0q3hmOx68jyu5Owr+LhhfRqy8/XL0kE2S7BB0ZxKC7PPSuM/pLWbhGt9FYyU7TK4uwUkYkwXbREKGFl4icFfqbaWWtqmcIfP95QWAFq5wkNkUZRrsw+bC4KzRZRKJo2LWnfQG8Fut+PuU6D1nszAFryPmIM5gWHqZ+n/vtG3enDGL02YfRMgg/Z8zWRLeQRULbA3UtxE1wbrQmPL+ziJJV5gxWOw8+oVZdwC2YYTIojVj7VLb6ZKbIN1K1sgcFF6JRAA55mQcZFJygTl/qPVUGzZAoY7X+ErRdxqVWNzvmX0BwfzQybxkADmA1tvPNeYanfQNSgNJ4FXyUWPv+54tlzaMCCrVq/6ail0+H/kzoBqj9og6XsB/cuv99sU5h3IAp4+EcV2se7yWO5+kjmyVPrwdpu38mcTNjbJOLpcxQZada6YlkozSPJqJBW2ruUDMs+6Gy8wV0TN3LU09QXVOgAOwO0rrYlENnrmH4eYrRa4VZMWUW65lYFnqriqr1lzuBNypvT6eZTp+H8o7VoRr7phNlAP4kyEGSxVfiMHXnZXm41HKDYpx2dTMZWqCc6/KILUGwWxyhlB4lJfC1ewe5eAx5Q+ZlqHwhuKXtS9NXfKAF/wUkx63V5YlR41T/wBjq0beP6iqvnnUEKA4N37ndQU0/wCEeb+3niLnsDDKKpz3sQe2rf7me79pV2eRLH7qYNozZAjgNX9kqoZequJAUTd/MszrBpKSzLUXKIhymEOHDGUsCBwdocYWLvoJt+d+ZmG8Ka+8YUW6/Qhva0d5ZvNop1BKb7bLlNl9seLzEiFK4m1Zct6WQHM7OSUV2eljk3wicED8QVXZqEvLy5eniYOms9YrxKarHwysZ7FexF1qaRVzuruzbDDcqXklgtU2MsRpXaUj9kvptGJbfPaXAatudzSMifxbhOmOV49cwte/Y/Mp1uU2LfESm4f7KQdsXDmVzC5HQ7XESVmab+3iNU2s3vvmbHDGoe2UHhYzJZF5zQfaIRVdhCIcdrGLFgwKxnzDam92f2hyzHoRKfZSwHj4TWGSSMNldzxO1GB9hbLjf7guVADUv6o0qYbNX1xFeypeLGG8YyuGHsEqV8/GVBU/vLjbv3X8kvI7HEAkLWLMsEu5MQBwKxmPYgXbR5ZVbNHYHqMMj8Y8hw5S74R0YVts9NzMor2XUU1fD+VQXrTdHEQ8J5zDcWjBT/2ZbCtuWbBrNrzOKDTiYggE7N3LlBXfoZegFzCVWfPQgchvBGwE3biGrR2AUzufaRDcDvfYS2vkL/qU/tqB7XsmH6BfzBfuQd6u7v6lCo+Mo9pxW2ia1phEjsT9JmPeN/Q6jlrfEJnJ2I6t+3QgEiTa5gVUzMG/6lisRbgQX4YGFTBoeWOKgi8sRrA98CO8ATdogj3lxfjAExDV9ldx/iAxSVW/7p7HzmOEpdwgFs7ZRDcHiZ+J8R/IQcviWzgurUf+R1kfGCIqzkSWI4UHCT0Bgwh9yF2T7RKnIgo/uCJTBp+IfNvaZi5Glo0lSLl5T1Foy8MOcp4LM7S+UsKxdpi5JjgvpnBXYYU3n1HceiNCp8xhwHhh2gwXtXNSUWtvlqUI2AJQUWmFgMopMn8zUvnvXb1Mu1FIX/4jAj6bDMYakzmZ05aswTvobP3NKRgcvcJZR2UxLHTRNIZlw+jkg/Ro3Cvz/wDMQ2hnPaBqydglWuSm8DzORfEEQ7lUUnzLN8EI7+uD5TQ0nxF0GVys/iI1432fMz0z/QSDGe8No2ybAuGaO7uSEM8ZVqnN+szGtmzMazJ7SDYdyZjmtuS0fyY4NfMqbZunYhBqdzs/xLhC6B+JTmi6Me3mWQD1x+yCrKcnB7lp5IoKUvDxQlq77rr8SgA9mMER9OZUwQeWZbB2i53A35lUo94iuDz2hTMcNkTk3y8TXr3CI1Z84H2lxqO7gQAV5A7IyCdtCVYJ5yWOUYOXzLE5wOoNQ/qIBB+6EFarpT1iWJaGJN4smpY5zB5Q6OP3o9bSmeiDFy+hL/wqgeejnrsyOVMQPk8iuMRZyhMXheZRPlQB/FxwUKsMj4iFCpEyTFQ71KYBmgSrW95pK0fHEy9RVWO8SxsvVjOmZxJU5CAKbwZlf3S+IcpHvKErr5Rhd8OfUWoVvsihQCsiQV4znCCmGqmLMH3zGCr/ACRfOTYpYDBdmWCIpYnXvxM8gaPyqLGgyCaupAsKebnuBtQxINoduIu6fjBKE1h4UMxCcIwINjBfmLTr7/MmPqoUIeTmODlYDqVGFjdQnAjUWkuTB5parn4iD2Tmc8t9pjwTwzDkDSi7K3ONnMqAgm0eIGDwMd0KsNM8HiCJa1bzAsLPZqGG8NrgJR7+eWY1tMX7S4ZHcKzEfkeJhOOT9pXKbjcMC47+m3eXfWuguKQKtBzL1O6m32IjSPsRMrQbxAaymNd1W3T57Qo74HHh7QCKL6/5mhCSUz5LAx8+IAYincdB2nEvEDT/ADHgUNafHiVjTmngxKD6xRnyE2QYKdu05DX/AEmHzaA2cS5xvOBDWu0umrZzNHVTfV17EHmx5qUNBzBspnYJyhkddgBAufQZZbBuDmeFlwG2ack/6REO4jYUBV2kTZauAmOrwdOUDUB5Yi2Dwag1IpKnN7j4nNh4mYBJ3MoXkQHA8Ez7X0MDWq4GXiJtZwwPsNh2gS57nZ/yCaOJrUYtPfX1O1lk/wBqGTa+X6ljdhw21MDG/dynP8Sovsa3H+FkPMzGspLsh8WNH5jF67tS9aD7cQd6EOKmla1j1P6wgCWI5bl5h3DKZkbvYjMXZJWrmjj/ANhI7iFB8Qo1DKuPiaJPI0TCMFu8Gd6zvtGbjtICjMMtBb2lnXgGZV2dFs+Y4BAtNKepT2lSoBScY6XOSo3SHglxYHZm+0KBpzsxv7dnCWCZGrIZXXup4HbHw1qF3wt95wkOBt7lJ3QjcdYdD9EwVQaANPUqye+qvE1JpfhiIVUVd+7/ABFLsAK/3KX8B0QlLDXOs+GJckoDZ5iJNrgb1C+8pq9xQqFVXxFhBrlBhFJdW4xKVMUbp2qYSn2X7DBigeWJqEvhSzRvmCSDOg9ob76pFJYNiEVNpfX4lAHXsuAlP3VDSxcAo/KVtboNxJnD77wTOcEspg2tMwWSgX5nxHVRNPDAXRNM34bGRGO+fMGsmPIRm97tkbJE4XZ95gQadzBqR8znfbrhZUVqD9zcqUJOTB7gdI1yahL2Bf8AJalPJlO8ZNfeJdEnY3n3MFlMKgWiXAS7PUqmUcW4mRgF+27BoiuNHbphThS4gYqDjYwbHXUkvW04WStBPZd36hlMt3PZ2mEPCKv3OZtPeX3YP3PmDMkfX1sTUoF1vVk/0N/Eo83JGq2F61EhpTvulRqbQ3DIF93XuVzGCzSf3LY7hF34iNkNuhK1W7PHxL9TC44jIF+NwQMB2PzALF3FPxMx9yFxapPNlRcClyHPERsA4c1wwFDhnlrxGmkmPJF2uccPmVeSoxWe3feFTPJ+JBdMC01YhzPgoDvXMLWM03WIOz9uWL73v3MvLYGWeJTzBNNXiUSQvCyiB3jbMtNGK+yGSsNdnkd4By7/AEbhhYYvznV09vmMc+xxeYEoJ5mxa33wEQXBnECVIyoPduktUMvR94aNF/hHwctcp2WDm7gymF8gf1MDSS8AHEUQ8sdz4iBlHDW5UBjnn/8AJdTzlkbK7gYO5RgRlgg1xL8VMo298z9RFTygmOxI7MhwvvKEtG6gYpfy/wBuUqXsLyR6hPYpnYzQN+GBU4YYE0GduyCcU+I+CASJocBoY9pfBqXkvIbmTeuKM1JHclZgOxElorT3epaMRl1uvVxIsoPCY6fE28Q1hGvCuMZVVAeYyxgL4TA6hL5YNURSuwnpA0RLNwXRd+oUQsvduZswauZgLMA18zL7ZuVUbPXqNxqB2uIipFu7uUDBqwjDO02FsqCh7B5iRWuG6q8wDzlB9z1LkdITfi4gX4x2QbhWagXA6gKQwe7TuvvxKe0NY12M5K3D7LhnDBU9M5TXAqWphMd/+I2znyFVHgtsXqo8YpbMcNzeB8ofQzshgFKc6IdICruPcdKNXKdwO8LENuSztGsclnHpgsHb034lhAV3LUQsyuwlhbxbirRmoZ60YOyLDYPsYazdZMoMaH6NQLqqfkkTtc3bFStGzzC2hXLRb3Co8iaGq9oAzwbfmKyqfM9ghNVFmQjnAxdyw33JY2wjZV+oSYD6gWLQViEywI0hm65lOcUjOItwHEuSncEQLb5qcj8JZALbK3BJu4EwQX2WKJRUagm/3u8BsL2ZRS141Cz4HMupXuwifaW2bh6gU07OZfUHBqo71ZcWjwYLIXxCV8juO0C2WxA5pN10fLHKobkYWEK2SmRBk7XLmh1s/wCS+J3VUKbpaEMzWN4DMKyVW22/Ymkd+JR8Q0wBVJbLywBWKPMyQOapUTtawnxKxuDm5yRfCXVLvEttOGyeZT9f3LUYQGfvuXA0ltFDAc3bvKBGB5fNHaHmVYRQQRSL4J3FtlU/At+XmJSLpcv+8xObp7mLiLNju+GXZTg/J5JnkqsBSe9cMs4W1hGo4wFd7EIbRw1cMLJZgYXAgXY38xvObF+C54QN8ajIqasT5lIZzyfJM+B2riPGxUlVKjx7i/UM4J5iiHahC5JfSNrhe8qiw1m+UJecHepkPwRpXOBKBmQFrMwdzndgHbo1swc0Xe0xkSrHRmISp0wcoVgJWsXniWENQIMXb3ifcORXiU435R4PNyz/ADNLT7gmsgcR7BeyK7oK8YgxiDicqO39wg+4bgqD4vMrwyZ4uN2ukuN6a0/uYPPYGEuLaOWNVWHBdz+iS3CPBKTL2Q5k7/iKlxoJcS+d1/mfYXYh6H8YrcIMdYnKfCW5YYnOO9yoZXcaSCbdaHBLN3cAPD1Bmr3ZkI8RkfEy0C0LikzmE6N3FGD4QAD0+XCSh3CN/FRGovDATaw0ZgFefhu4wdhAZxBpgryuCxhbYePUGgLVzfa8kxqw3b+CGDS2FNfMy0XZeR7hkk7l+7HD9ulEMCBjF+Bi3PiNqG25c7FsVq+IvIvZiYhMlhDLemDhXmDNAuwviaMyqf3E3xS8w5ZZKoHysVjWSCyFuyK2s7gA4V8Sg2ODsRPLbm5StcwcpSjPa2ZpU3hfaUWIYTEiVS2aPwlnqiaKMDKy1bzCAk/I7RDCx5lAjnl3B5ziFnID6I7exBMlBylU7h0lr8IMu0AVb79oWQB4ftTJRDvHwtBXBFJRThlOt4GETv3GKK8eDpN/i9H8wc+WMooaG8csECnw3A1/OZNxXaGUfDKahvErFo2jcbdVgI1MEozfoPKYxyye8G0aC3zGK3S1MAE8TelzJrCGqJ7iMpVZmPESnmWZ/Ea0zWpYFctvfTizg1UVuquOGXee802Sue8dY+svG1bIrIq8qIQmjMO0FiHOl5mQ2GJTrrFekqF53iaa1LcQtUN/MpqtYlSq63qXRcvqKh7w4TzL7oqvwTn/APBGhW7MuZywDucEu8zAm+8zo28zIxJ4YASDq/8AiUUAWinIjMIbRGoWnMDSAK3LQqOHcJxeKQMKFGrhym8JG5TTyhLY9ynmY8zTy5VkPxHkBuqMS1YxzGg1Ey6vUy0b/EyaOwu4h1bLgmFN4g8ZTUA0C9Coq+YvTk8Q0sKnNQALqe0gIVUWz+o2oPhMROxcAusk/9oADAMAAAERAhEAABCNuqoRAWYrP6QRhNrf17v6NTgAcOgeaSAYX4e+TeZY8I3uQE0hnbPpzvzSM7w5XQxUosS4aktFbX6QKsugaD2ufSzk72SIJM/QNOkCq/MYO5khDNRWDuyVKBwI3rj1NnHwc+4qtRQDBIDGCF+rBLjA8Jd1+x2Qmwv5cOUpAlWekNSoN1E8/b5wlbrnUcfx/uxLU/68udf5vrkpHGejmF7gIfilP6Inykeiu456hab7v2nwFmclumQobsx7sNuPHa3eQffYmgRBM9NbxQAzDhXrLsPJjF60LJUTWm3F+rna7nTWpgoow5s7eWSoVUmkQpAA6JjTaUHdUHA8chJNYX0G2Tscn+WNpgrm57eYlNumkuV1NJV0l1EHeqC3vWOvyj0Sj9gAYOE0f0/0aXWFUNsKC6BgCP6SQdHTqlPySiecoNeTR3bzmMlD5ecmR8Wq7csdptrjjuDXoIpuTAyHHvC7qJr/AG/xFg3wnxw+Y95HYs5uFMahga4hQ37z73xMLkLvtSasTAk8aZNUPExRDVR5WML7p+CjTMm17Pendqt6B7eC4Oq1ur/rO1++tIVxSP8Axz7BOyLfRYLcxyHRfFgZIte7CXw3qS7988P7rvvvvj/kyS5MfzDvkm6P7278fybvRDQTponyebwr9yJfgGXfvvvvuOT1ZSVagPbfjR4S4fkFLjUzBVHWuveaDB63i7+wtfvvvqZQROIO/ZKIN5JSlVVi+ZN+B+LUbALW+1mi0mXJKCOvvvvG0uW3iuQr0qLhSDuUEtA6cZ1SKuBmKWhlHQ91DfpZ6s+naJSdfNNx3ec8fpX+LSkW5TyarViSmgiAU/lzYdevnMOW8IIuqVcAd6AIOdIkwwEFjDClpQaFo7F5W9vX0pM3utifiU+M16b3B1qZ1aZdjFywLBgqKxmNAHesHoJDY+UeplN1hsCU9SnOlAR4KbBTJ5jZaADboTnxpJCqLLDACDbq4mGLsq+EnYumCtPvxTBNHY+J6on9RzNNbkuliyMB8Udf5Wnhb1xI+F/E0/Hci9A0HnKHhyO2cA1Qmz8gunIW7WCcvetZHV9ymqAnQrOpND819bkm7dwErr/CxmMfYI+BXz1QB/4FnW5GQBFThU3/AOLurBmPscoXxZqCuMpMRARh3UzCbdPVx4M49GhqlhA2vBb7BTPzz//EACgRAQEBAAICAQMEAgMBAAAAAAEAESExEEFRIGGRcYGhsdHhwfDxMP/aAAgBAhEBPxASbBYnVnu+6XxYw53avNi9+DvzahbLc2b3N7kg4sep2E/WDNu/MX5Ubtzn08vDzb3w2+rXqz1JZYefvKTwnJM4iHy8MfB3YQXJ44htfDHVwdI8U0hhaSG82d2N5udvbb6lZFnMl1ayRMdFiYzwFnlKaxwmate5V4jbGxtbmNlfVq5lY2RubmdnbGxkbVjc+5ctPDlIhmLb8zy26XSCzxhYsWbFh9GWWeQ8AFvwgDZy3SCSLZfGQSeyTTm+QgyGc2bMmzHg182rXz4fL4yyx+nLqOrHwTyeXZ0b4PBMA4ZKY5LXuA9yb02zJcanDAwf/U/A/mMNtQpW7g879B4GdmzPHRZvhwnhm6t5jL3L7ksaGy7bbsIsMpbattHZz/p/1a2HksZwS/BG+/ofO/Q74MSxnufcstudnmTxmNmzxI8WtpzkfFCd2/UPOYT7se2QOY565/cnLwPzPZhPon8W3CDa+lyhthPJ9GWPgZ5+j5EOdXZCtfEOcEfFb9krcZZAXG2YWkxxt2Uu7IBth/6zPX82nxPA4iI2II2GGI+vS6t8JlseyAOC328RBIXoFo9WvxcJto5W32vnMJ7/AImekH3uvBD6grZqsXJSeAECGG3wMP0L43wM+R827MOZgdHgBKM/KC7hiHu0Rq5SZBZ5AlzoinFU8TX2nz1EMMMd/wDwXy/EpA7gJ1cC+CS3PJXyl9C05YPC2Cfks+V74855OLWAZCHa2QwwwxG2xH0vhVxBBhIm/ZJbifhOGkd6ufXPxM6zT2sQZLBn02sEZbCWJdssYhReMuRPcxBHgiDwH0MGzI08P2hWWbNky2ZJE4lJUlKZt0tAHxkiRPoIc+E8DcXcDLL3ERpCI8Bs2RzI8txviFvN3YWLAkLMeIg2xlykOFsgzyTlwgDjlLOU+pfj/dud/wAf7k/6MxFpD39ApHgftFzmy9zZsouEQQxDHh3wqwiZmcwhyZDuvGWE+dLcOC+Tr+f8ELwd/P8Ai1Xm+TPT/CH3mruEjI52tHl0jks+IL1Jn1oPLGnEhzhg97BblFsj4A8m2lkXDLM68L68bLLA8cftzMtW23UHccSnM/5v7gOeZ45dNlOyV2X7PIM2AcOZWcl9r+pL2P8A39ID19TyZNm7B7Lds56hVpM9IQleMvkSPBhz+sx4LMz6DkttiSPuVIfTqTeAjAIe1sMidXKx9xvXg3LAh9mR6WPvJHem3we/pGw9zbsN4XPtsWjIOkx3zCXzxn6ZTeCfbcHb1JsMsOCHVmcWOvSd6kzjacHUFcfa9kuaA6Zk1hlm2p3aJxJPXINcB1D3KwH1tHOrON+oYadWHMT2L7I5YebNe4Sxspx3B0QBF2X2xHPkW+ZY1zmEcLJzIlR5+MuoIDHuyun8TjODcOnSSdTCemBxTd5LLmMwXtcw5uUef1lOiZ65Qb97PUG/RicwL0SI8k48J4p9FmaXC7hjHiL97k8tsjH+VyOOZO7NXix5Sj83DGey2gM6icS1q4gHJY3NHizwF0mUgAJzHCA93bC44sHEE8MKE4OJY3DbHuTrHLc7d/WW5JZAuSgJBdHj9rRDbRzI+JRjAtuLgEamE3KV1uN+bkM6ncyYgSDUebhNsJy8hm5Wffgfcj14TrLuqF6jTFLOLlRk9EDwRRhYQPHMhuf4uI1rGB6++P6h9BsmCSRpx+ueB8IYuXZAOW3ssHqPAi5hlw4zHgLDvxm3XhPrDepL1a+7II3Xk0a4ICdQU2XeGH2RsxnTB/MOarYi0l5CTJVh9NZhO7DUoeeoCYE6OC04gjDqeCXK8Q9vdoJmA9fM+ZnC+9+A3rwS9Q3VhdcZOocv8wHLqzrIStsTu5dN21vkk/jJSC8XHIbubHsnTuJwG2ckp5jx3P1D5Led2ajdhAy2diPBv5j435lBgtJNNYm3pGNLV4LgbjkQH0XsC2doJxWHkdPcJ4S9pmvuPb4/5mjgYaxLjwOH+50+C+2Q8GB3kggbj94G69fadnMIeJAx6DuC7L3yyuzIPjLIiF34bl1RDBs5V5Ng+P5nsC1xNeAZY+9yYSTj1cnHMmupPvuHUYDp/MDiu6N2arGuSXPw+0E3hA9mkuY4s4NiPwQGjiA0Ezg4QhdDENcHFwZC6Ow+BdhWKS4FiaM/AQRyyB2OrZhKcO7SINk9PCHA2WIaslWB34h4yD9rjCeUmnqITnYvWZlwPugbhyn+82sg8ueLdvN0Rcrdm+I8IwoRIDFhre1sU5vhQjguG5Nw+AD9a7R41cCR93//xAAnEQEBAQACAgEDBQEBAQEAAAABABEhMRBBUSBhcYGRobHB8NEw8f/aAAgBAREBPxDMkbRLvPjFteM+LghBl5GZ4LcuLHqHEcS7lzzuYXycvDbbfDbFk8NttlsZGxsjiJq18OLi23wv1bDLvgYjxPMENy8AvDHg4tC4RysbWN8DZnhnkY+DV9ryG/m3835X5WPmz82T3Y+Y9zZ7G/O/K18z91+Vv58wlLO/GWQWQeMubPGWW+eLCwty3yW+N8tnWNnDFyWtg+DLN2DrxpBa6bc4sPq+xPxQEeKzZ8+/HccT4LbbbY58blm+Fy3Y+0NmsnuOwcRljh2D6tly3mR9RIGM6gpxIcMY9ScR9TL5BcsP/wAF/wBAt/8AC38rh7YHz5tt8Phhk9xlyaQ2sstqNvEO8xE29Ze5pqMb1IeIu2ZZIXZE4ukHEYmZ7hvf9R91+dvzJDthW22+Hxt3Z4OZDLQiwI9PDLZ4hm2XINiOYgXCy/pD7WZ+JYPUt1NuiX3x+ksc/ttOtWOyYbpl9kp7g+nPBJZ4PDw3Hfh5u2Tz3dOlmC9yHbJ+4D3ZDTL5p46WDksfFpJYwp4tg22lPcWfewiLfG222+c85ZYxpLtnHjLqDZjlHqlnca9wIMD5s2Pi4bGF78WDyy6hEkeEQbYbYfo2221h8547shzuM6vay8C5eWyVssYIYtgsl1DFp4Z2z5g+2Pkn62222xEf/Drxvhcl42A4mPErtbHMGeNhhvMB1IDhcWy2trevOw222Ficltw+C48EfQWfUoQHdrxHXuCE9RAHVngCID3FBWHq5kApZgTtlllmW22yrle5B0iGPB4222NQ+UEInPFsPhoRqfLweGlpIuQXffJmA+U219J3MIgs+jfK5D8wwxnVyV7kwZ378bC2xH0Sh1cydRzY8iE6mqTG7HsZ204TkG9yPm/OTJfQhMzwM86vQy3wPEWfSOdzNDju0aLA8O3Nn0J0gJoTu3JTs/bcJu32Ke8v2H73y/zn7R9Rj8SerjyO/VkwLGwvi3mZzct4gdGG22XJJA1uQjDXmO4LLI1AsHRaTrvWwOG7i/S6YXxXRZeCLa4EECLkIea8QX3B+YZ02J9Le70y+pt+92cz7DdWzSXYKTByQODYmv2SKtsY8BcywiBmpKw6gBhA7YR2x9RTBNYZ+zcXDYf6rQEtHVBz1B9o7nEh95xz9KW5EGM5m34jX1ENT5xPwQfTYAzuAYs3ERvUfMpbbcshwRrmX1ajYJgPFoQ7JbA9yfKMdWrjIB6C9iRjqfkzy/cigkwuq/SYYxBPLMkfF8kfCzLe2y97HzOMsJc9QEjGDDLLpr83AID7ZIbxlxPFiOesgc/zzMgc22XBac2vpH3uLjxleYA68bLi0hOr7rNwzw89yY9WPq492+cdXA5gPTJnIxB6rCX0t3EDmaeG3Vo47+IK10+084J8xP8AYROiCHPF3rU6hLJNs+ZbrxvjbbfGeo4gt2uDhsUxHM2xLvLdT90m/aEPZ/cvWhHoo2o4P/frDoIfwQmBkcQPds5hlxBaDtjAfzN0zwGKfZc50lwzLk0n9f8AJE0w45yC5s4HUMRWxweB22TYI2kY+5Fv5/a5+LPh/ML4j5TIdXz+QHjGrKxC8MP2uAHG1BBx83Y//P8Ablv7XYmfm3o/oRuG/wBt/e1azCM/9LtEMWbkSLHn/vz4IO2ewIOhlDbA6YUOZRvTGKgDEqcx4dtL4ze/o2XwHJlub/dg5ltzMu5bL0uOYLxXbZKMSS5BNk8Epzib02yC4WMvB/lyyQh9Pi4zuWKiE253/EgYTDpyxGresSV1JPwzykteHg93JtzEhv4idb7C/e2UO4Hd927SxXmT8F6dfE8WNmWR1OObvGG3cRuTO4KafX4+I7Qz5tOJcGp7BxYeNWRpLHcY47PXrm254Y54ng6ZYfBZKMm7rm6hmB/SQ+C2dnUsHEnGWAbG6u3KQvBkH+yB7eYfSOCJfaYcr4HENZ6iF8ygO5T1DHeX7PxChcmcb3cjyy1njHuLHHT6z/ZI1x+9znObDbUSYd4kOBhvTOYA8MPzct2/bwtJHuLaRhwy3YLAn72mGP8AE+Qv+SQ2ODzZeSI6uFi5gA2CCTjqNjUHV+lgFwDGC8wH8yJgs37/AHngc5R5GMeCdU5/24fU/pPY5SNh/V9ftMcmp97+hPiQd5Yjnn8SHPU3kMkm4z4jnR/ti2AMS5AQJu29I/nTe7AIYYNJz3H3RdhQDk4NS5Rc+Z8w8Jv2pBzd0NN3yTOsSni7PgS81okTukQduIIA6mcJXXhl02KAHJgbPnEpldI8RQHieTmW6uk8sYXbl+O7+Qd3KWcl/8QAKBABAAICAgICAgIDAQEBAAAAAQARITFBUWFxgZEQobHB0eHw8SAw/9oACAEAAAE/EA2XMobXAcxEwBFeEqdmMKKzzDXiLa/UsfMD1Buqi23Es7u4OIhrUQYt/wBI9szPf4EAhUKgZDmEFo12q5RYTbc8y4nlHLDM8qK3REayzNq4NFOZWaJkCLVrCGqRTxUvmXXTFDmft+I3NTSxW7FQli3MabZpj8H8ebLTb8axM56R8Pwr8UBqNJlETb8GFw2/D4EflH/RMJXufwiHuV9RN+4PmeibSsYIWvO4FXUDphTVE5ZiCcblkALcpW6MkrqLCI9QK9agNaio3WwEpuIFM0EC+Liu8Qqe7h8mOrdeImI77lGRZfj8AFF+WdBhvBi9QICcwLmcmEArNwNZlBxAtLIBR1BSgTzARLZeAiOU1youjEteojFRmhM4J6xg0ComBshyxxRODKQA7iJGuZQ6mcTwwDBfgkbtsq5VfgSUdR2/IcoZw63T1Ksk4AvxAa2lHFe4hH0iEKXj7iYDqUHzKlBhJ5zXMobqCpmxH/0iWLGGARJqskHpUp7VBkvU4pZVY+YXVmJWdxT3BuYiVzAjDmYQDEXLp3LUtHEPsg/EGbqekFbmOuY8SwbhyxFUMJStwqeUQxeoc3zALm4lurm+4GnCXssA9QYDIQqUAg74+I+ZElp6iq1MHzDtMipZjUMAt1HCxcF6INxnqUlYZqewcQtHEUOuYoXUbjCPqJ7iXKxEZVwIIqYlbKjqNvBCQccQWICWZJZAbKnRHG1cEosD1cramSLvJUMMiwJslD7lYraf9cXH+4Y5RPChTm+4wULB7OPEyb28BL5csB4l4w/qB5VUucw6agtXULeoAbm1ZlHmUsgOoi9Q70Sgag/hbBaiWzKVwtKgDuZJbFmEq9ys4lQPMRyJi3EYAleUKOD7ljxEl7hzuJe0AFBiNHUYqZxllTFWhCykLczB2gJySqYm9Z9TDn4lVxLLpijUzEMQWKYvGIgrdyrdMoAu6MQOq+Yku9+YaG4cLG2XcJKz7YqXUWG58sXevEV6jd9zvEFoXNhGvUQHGXjEA7hRKuJ+2YNwK1mCbxEhh+CCUZJOG9REA59Q8yZNsJLcMpcDuVYCPlhnuYOYVV4l0j5Q9kocD5h1PuU/3QW9lC5Uo2HhNITyIG8/rP8AVMxB31mrb6y9iABjrGU1u/SMIwcViui6M/qYBlA9zq+yJmj8zJs+4qcEtf5JoU+4o8n3CUOq+IZjME8JSouItyytMr6g3yso6lekF8yvlF1SmKn9RTmVWbl+CUcyv/cQ4cRKzKXALXUdauduCX4lKkKI78ksduYmTRKCzNShAjt5jTa89TQ/fKD4RguNTB5liYhSoNZV89yirmkslLmEIDIPbHcGvMLxYtbT0uX+H8VLWryHUMYMH5T/ACxzin1DeMfQn9nghlNPhqKfizXbUfeYq/dnl/nL1sXKoo7PmZbT7YgtC9TFtUDeZS6mOiUPBEHNcMzGqhGJbxPiDPA9M2hBq2HZJxaqADf7Qa1OO8VaH3gnCfKX6rPafaJeW5QHI5mr7WKzHelFGJETH3QEyn4gc/eELup0mZP1imNoV5Z7Y/WENkquJ7jq/KtWQUWZdT9fh9sQwXLY7jSKzgruXF84jrcVljxLYup9IvcF+Y6r8HrEsdxlwpAm4D0wdANeZdU4VX0RDz93BH9krT5ErbyrzAikcdH0zbA8RsyHpU2neVLvL7lYSZVLjcuIgIRjEylR0qIsDtRBDq2Wda6htKgLYLirfEsQQgCxb8Mso0HSylT1R2CFPJLwo9HiXTdJzB3KKMuSgUwA5iBYsIPqI4ckWTOu4JUJQ2oDsleBTmYLV5ilGuoiUVOo0gApWbVqwtPXDDpQ28R0yY4hzYPUVv8AyLfsnuRcsFFS8xUplMh3EdamDJiP1mDB8vmtgRy+YNv9p/oGA/yzRKD0k00PNTa+4R99FMHzMsiz7SDXwe5f/ll2QV7iOGMeWW6Y1q0/F1Lz+bcTT5mWdSncKZgMpD8J5/F1L/FXqphubmJdOI2hcJrcANrLBVIrF3NoG4KpnqIbigDV4ZYKSumBCkpbiGuOYGZWBnHj/MR5TUCFM3HFVnEOiPCoegpNsamW13EZaPmAu2o2uYwFL6ihWZrrsmygvEEm6om1uZYG8WfuLKcuI8LCZRisfoTKOPWZlXOKS4LSvUSEu01BaSkollwY7xOxUFdVEUtBMhpz1FnEslITmK8zN5grXMsOmI6ZlGEJUaz7lisQ0Fj/ALqIf5pVyPmKQwbPzBCCSxYHTAxDDhuIq1JVKSUh4gxdTbBB+0aciYzK9yklfjH/ALCoB3mUQWzQTgWxw6lLGOZ4NxUaDwcSwhV4gNC07/EBtt6lGgOlQWnL9QWgc4mVYdIVC/EI1VbcdJYRschIG1ZBNKcZm4LSLcQcqqajwZlF5mGBLKbqNA+I2M5YV3iqZbDkVeIVSwPEAK48x26+EVbWbXJ2RS0OSc4qydRL/OYZ0+YDNZiLGx2RXcVoKxCVuogIuh1LKH8QSbb2wJYI4thRkTLWkbTSXpEOQskwy3vAIjYWs2Y4flAW3nY3EBfl3ixR1tBh9AgxUChqsw0U6zdIBYNhBmVapG7s9zKKLCMPdf1LQk82Vyvy7ZgdRTI5gOc3MlcQTzc4uERdMp1UFVRd9QA6XiUsl3kYC7DogyFCKn4qe43xMfMtYjKER7TELXxAwmIgTlcoqR4mQpwyytcm+5VK2XAMloxKbZzGDAZrZZvqNigvnEwFDWI1oB0ETWI9xBXRDMNo7t2vUzcKmSNYaZVcCoavE2Z4My+GTuisQILX4gKDWy4s7fuZjL3mD421nUMLSiOS2WNSiTHkl7wXqw3NJA2EI5DVVELD1UDwvKEXOepbREcwLnB6lXcVrVdz7IARYpHIK/1KhK70H8SxY13TzPoPa4Ct5JWVYvC67m0UpRUPkHeYrPmLiufyF+p4Ql5jfPUW34lCJvqfJLdQxLXmoouqyQTGbjUtwQy1ygRBDE5ijcvmA8wRTSfuKR258x7BXbFDh9cRxN+prTUp8wsLUmWF8sFlMWrhBRcJsrY8pwixtRicSvuDOq+kKFRZkqXqycMD5sMm1dQxp9TmJJYKouJLaPmaIfNxVkBkO5YhteY1EWyMFS1GJnhtO4kWqIDAYuK4EKt4TAqwQtgRDIGeSSFhxUr4gYxCIrDhlLBepUwWckOyB7lKwR0OJij+pgDUaMBBkCcZ5llyRbSvNxJhgPuagVKOxEjyMXLlFq+pY60GIhObp9TWvyc5U20T4uCbEXQoeqM3MVHgJFwLwdxUODYFLq3xCHUgFvbuZQyA+yuotkPhibJEepUbMqpVfhwQWcb/ABbKoyR21KLlYhqnUTwmNTCZ6omwoJO2CKm4TkuYkuAQGLFpIWCgdjph3CAyVhmKLH4GLBMdQSUxEByi2T9xAtNxR5ljg14lJeWHG0JY1phuAgwbyGamAL6ZlUoyx7SdR0K0iAPzi7gDIxlVU54lBRd9CJQGTdw+nLcSdHOo+WzgOZejaGmMbKPUYUo4XZB7jgFNo6kC4IChYC/VoESl5lkMqRI+IZKMTP6hAM73M5GfRlQWp0xXBuPSU45gNoiSGETZqC8MzIczNy1KisoWBWVkvNWTQTd3uXUQMB/CFiBu3cqDcDnZCXJXzMoj5iWrqm1ziq8osbZSvCXL4dqNSKrW4zGbX1iLfLEI0S/yv8L+C7xNpVeoFy73Ai7lOcRKvMK9jMM01LvvxC0F3KDZa5M6ZYQWJqOOL4fMpqJarjpneJXGbIytpy1CE0f1FFUfW5uDmagth8iFdtPiWhVPdTFkX4nA2PzF2d6qbyl8kTgNuDBXIZgZVQ2XEboX+4ZYbrUuLCkiBY28Rc5ockFN9h3LlAeyYduQC4QbYdiJltI0BR4IgsuAgrC4bEBxKNcwKys8QgKpcRuYDon+EJsDD4jn1epUNo53Cm2jcD4b6IeTOrWHkAbWUaiqLAb7i+8g5MNU6XxIr7QuwcL3CMfCmVd3DdBM7fwSlONoses5lKoDq0EdLtRWUjQIfbHttcHY57cagd5M4g9YIEIbPt+pn8FA0Th2DmPRL0XWJuIlyeiO4o45i2TJgxKfw/gZ/AXMvcoKYV3B2xLFJk8RcDPmdaxArgPMGsVGgRySzYo9QKoCAMQaCN3pjqMdkIfZEC7GtQmTZ1CyjazXUo7hxKZUuLA23FZy1mwjgCS2YW9upehQ9TEXi401XTuMLA8NQNWXiC00C5IaWuhjNAiVLckrsIIMXNQVSzMwr7rjtwKcY1KMC9WzrDllLaOGYwPg7gmU23jOWAWMQsoVAm4pg4m6mktUGoTMAQK2acEtZUU2yhgd0mdWeIvaZLS9y1qh0DLACuEWFlXjbg6VVUAYjhEKiQE2LmUlnPUNprkxJSlK0Gz0wuXsIUPPmc+KIvImVDAKZW2Yrt4qWEGM499w1QNIq4MsbYhTX0mlWvRFLyiZtl/MA8b3AHEUfEvEaxieCNSmfwsVRblxFcTGvcKXubIrVmuJgMDxCswH1GncpeI5iu4t/jPFbNrGr4hrYWITg7lixqWDARIoyqYHg4mJnbUVC2lw+GNZ5/TKE0dnDEtMB5mEZnRM0WNIwm7DHiE7XSmAxZySkWDuAAZXLklA1Lg6YqZW1U6JcHmB46itw5DuXtoUgZTBCdi8ZgcAhtBirMXAFaJyRQJfVxhVTdRBNO8RYsFeJf7/ABDegIFdwOPfWpS1teJhF4jl8QA5hflDwqKT0/AmU+0uGl/EY03sI239CMtqdjE9dbi4QaILOeJVwcBkil4uJDefZMjGIvu4pesRerlRyxSG9xHUQ8YiescdTbiJfEVKkw4jcdTcSZBS+5gajGsEFt6lNtwR8M1MNNUygKjzFA0E6ii4HI8RDe0HnIxGHJKU3hjBMvMQt22MfsplhC7qWkIjmozHBGfEo2lOszF68yhOTWTshBTV7PMwpa8rJQIXh5jXUONRs6lXZKS8otnDEKK+UW5oaoNwwnkjXupepUKust6m1ovxEtUVkazEZmbRuXoN/qXrCbMXhgzRKQMrfD4m5DUdwCQma5uLud0jBbPzVEY2YpkRcFm36YhBm9GIUBD7uVIh5fE8FQk0c1E8ZnrGhdQMhVwVv9Rg5qgsQXzE9IiKoJEcpFLefwqpSZi2z9S3UZBCS7VzAwzsor7i8LqKVF7QEJDCpueRlrlE8xGVGWc4ZqKgXu1eJZQafMES1mNarKw5mzVS7d5nmE4iDs94mlvCp/eEI1iXqLbyk1DmZL0nM0C0DmFZArcJesSwytbIqCL5ZTcmmVeAOIULDrDFyph0nmF8KxaYGdJlho+yU0WeWZkqaqGUEmNQV4l1D+CHTUt2IgKGZpgTUR8wNCHgICrweIrOH1LDC+2IFbwwocXoXDuS7QJockEi6uaitcYxVcR1rE5dAQ0kiZ4bXULS4TuAjfUAoQS27y2u/wAQrgMyL/SWMj0R6jwcxGqIuopjsUYzHtmNqB4iPUv1EviCjUrxBGwx7aiViVUBifMay/xbFeY7l16xtlmpfTBNKPU2BkjAaRl1Bt5jupa5YeUtqFBzHllE6jDdy6G2VBmx4jVhfuFyHiYXrySqUiNJKIM0Ejh5GYVH9krKSeOIeEHHcFk6xQpqbBdE9uVND6lEZ7jmkdqJBFd8RwtUtlnmdyogn3LFy9wEyXElX+5ScicQfiHLLmyzEKAX9QDzKhSZ/uYP+gJcafxNUYmfSvlYu/HiM6fciFt5DErAObQNKV9yjWAeYW7lnmFIYly4v4FdLMmF8kUeLu5ayzFTtDkY+ZY6mV4ZQIUMzYg1LCI4tmyIrG4nMVBgoZkweJgJq5jqHxO8T7jUxcL0YdkHql00pHwaTxFbo+JdhkOICGKDwQFM5HuWOOVTwReajuDUTBx0xKy1zLbuUGCzqXgc3iMNMMs8DiVBS3NRlaeIYIS9w5QeBlgu26AuBnV9sIBD9xlL+D4ZuUVCrQTqclQ5Z4ElhsI41NHioX6epbdmGWXjcHSIXT+pdZUG6NsArYlxQBiD0s+IAWHJqMKNfEVrSj1DwXbggKQLSBKlfm4klIwxmzSF3REqePbEqqe0LSq8jL05T5kZUgjVRUCQ2TLlI+0qoTonWRa4grhjVjS9wDwRy/AlbiMpguPpF1ZD9pnqZLkjK7O7gBtodRFOG4kbhtVmTaIVsjdE3NKNcoDvlsmhZGGgoGLu0sMYhQKvzCzCGaV9zZBrmJQUcnEw6zlZDgz47gAHueYDdBzl95ixZLVd1EqK+hmdpeY6saeISgXjLLeH4HYxVy56S6ZmlPciuyco0ng9QW5Rk6IWbOoUdQ8FcRF4DG9IK8COrWnmXX5B/G4lj1nojnbLRIEO0trudS5WVQLJm/yIiU+jE8uomPEsGNtyh+H/AIIRE7VPJG1xCZYRuJ8IM1FX3OwjuIxIwBj5R8ooB3xDdRZqXZ5QEa9JxOVCLSYKmDWT/EyOuoOTE8MsBxKIslKB9XM60+mAEiniOxaN5gj8SoAt6qKCQ0ooMEBGxxKdQOWUES8PMC4lKYkGACq5brzbTUtYvULIGdQEzCeyOuiK/wBRWM+gjQugeSpQyxDpdPzEWo1XTDRZTUdQh0SoM8xRqFsXNuWks7fiKbV7Zitt8xUGVplRw6/DfMzLSxxcE8SvzRyfi6czJcdcGXNFnlIw0SOGVNOKlvEuri9Rr3HGImc6XmU8wXrCc1f7iEy4iK5im7IOFyiONMS4qL6jic0faAdof7Ew6S7YS6FZgqQXApKxh5hoC2g2wI6eoXzzDpj4hogXGCwYRJnlQwRa4i9RcdwqQ7MDBRgOIpFgOYh1AqIySA4qUInaoDcAYK4LgNqEBsUG8YQsWoAwheoRB3qtrqsZflgmUc4X+2AAKeQ9aEYgyBW2hyPjp5JZsF0pf3K4B7VUUAYk7HXh7j4I1t+yW/8AJMOj3N8ZRq8wMtdQtbId3HWiHmSgihFoleFV5gWCMrr85Sqg8cSq2y0wuXH/AAI/bUQFTmNILidEtqfz5nwgDTC8QBzBfbUa6qa5i8Roxt4YnbKRg/FtFdkTohNdByPcAQKZbMukB3iUC4Hk7iErkwPBGSgLtCUIIBja8VNxlEYGoXu0ZK1LJRhBbMg0Qbw8QylaNZgHCLeFRZ1KNicBmLYsm3coVMSzmWMZgTZkFz+2aKEygSqQv2yuQLjX+CWY/oVEFbxoh+4Soy7xXllKa+CGW2T1QLl4UvfEvyr+5ciR7UpXWJ/wFMGC5b2DEVF2dRPI69x0K+ZyVqZYkoBgCjXQwSshR6IYCIQW2LpFxmXeib5lTcCXL/DOIEd/lKu08xA1g6gEie48nKRsgR7nLEpfEeqoFQfTNCyJswxbrv1F9SrwQ1wRL8R8yk+iPX4u1OfMVVhZKciogqo/qYK1hIw+YK5EAZe9ku82eY469ghEhTNeIhQvI8QxQ01RZM6gxppfEqCmIDTxDmj74ieKfEaZICbRa3CBfAQLtqAzuWIQypBaljN13AWCDEdpZ0C50lSyCsvrCf3Bdg5BqUcp8xbmUgRWibA7i0rD7lDLd1DOPgiPyk2PFJ8n+kAxSWMIkVzUSNUiI2raUSj8VEvj8KdSpA/FDmINXHpLdTxfgaoi5tJWnsgQwO+pYIDdkaWBei5Rkr3LbJO4hsikyXG217gDtgxiW4gF5h2zM7oxzMron0ja8jFD/uKeoqokV5jBDkkGOBIdVKPksXuNNiYBwpqaI2rmpaWO4WJYZ3UbKmox5y1EqYQ3RZXqL1gsPLD09TGUUC1A6iJaK+JvFC6UNWa/Gm2IFm4BqA3AJtHDRkIQh4Sp9gqXsRlXcHcVWOmv7iFf1LhA29dwxOIrAxzCQAjQX0Zf1cNXewcFkPpg1lgNovEi2xLeJRAJ/wDgjluNuso9y/w6lt4IOLHlcS/NRLsBxcQCG95gLiYFMJrYDbcFFsoguxqDINkcU2ke1zomVU/qZ73BnOJZgSBi8RrqbdRDr6ig7Y15jXcTzAgXNZWopQruecrSTI3rkYVhc8RWzLhiYOzmImA+JoATuopGjBjtLOA3DHEHNLzZykJwWCLkXqWDZFvZ8fgNK5lvMRxUwwpwyxxKVFTDMb8UQEpyDpmnodkRszGrkiTiJvVKApFxeOL+JqAI2WB7hVLeoHzXuH18xzGoNbkJUaJfNyisvuCYomvCx+jLSEQI5I68xNKqLp1FV6gfKLwnkjdtiPmC/wD2pxFnqcJZQxcE1eUSPp+4F1cQYT1BpbV4Ym1V8y1g1MhW2lnKTQEQchBEMuphyX6jdYgQzTFNivUXP9y+M3FtdReanhmfUWL4lLj4iRgdGDPUSkV5imM+SWhgS8xk0zGqp6hyK8kSJsMRPCQLq1j0GU7JAJXLQxb34lHvi2HbGDZRr+EUNZOGc5i8Yiit5/mJzTDxLOCW3lA5DMxznRFXq+Pq5efbUz3C0fIYYmKy3Z7bEItB74lXAjkSCUAw/bQNHcYQ5HBoAYbFyxVswCnIaiFVJag4SoYtHNgDu6ECBZy9PO7hQS5Ld8bIOoFDm0d9IqhEKl+ovzcCDD5C6GsCjm7ErEvKqoN5DFRTFYw5u2+8owDdiVf+Zfn9XEaWFugOYUbNOmymGAEOqLiplD7RpwDziEgK0jhjTpKnOoNl/wD4IWKazK8SuotKLjZtfEqtSHuNYS4qWdLnIqj6L4hOgeiaW/iD4Ndsr33EZDcaAG1nKXBnbcVvbAR7xOCYFuA3A2ReIiKSxUnipZ4ZglFq4MiWaxAsA7qWLV8xOwJ1EFiCttOmCBVVQFufKFLAzslkoWpCSl55VhiboFDxKwACiBnBmbpeppFXNIBu0C2YmSYlgWuo7gIbKZ+9X5msBR+CpmTsUEjr141+Hx/KMCqap4mIq7XbqvMFWbBkCGGOK5gOANNb4sOuYdnAncDAv3UTDXxcdrGghYI3kHxZ/cJaEbAeChLrtlPQM7X2trBz/nlogIFaLs9EYyDgyjoFDQDX8x1W+6D81eIbSVj3hz5LeYhVvBRNuSsVzXiNrb6UyfAxaWdoP9IU0EaCl+4UAdqrgLYk3+jSLCcUqIdRz5KdV2CZIHMDyY1AFrrqW/8A4Lm4hlIllOSyARgpBpIRDD2MRIUgCgD6lCwXyEciL4eJvECKL3FOD4g6qQU07xLbL8Rrk3DmlQ37jfdxfUfiJZ/Ma017nYJ2IleYL5XGK7ZiFV7ecOIGGyDS0eIZslqoi8YjoTF1I9q9SkobZWVpNwqa03LsgGirkoqH4fizmI9hLXVwO45NM98xCWS2VfE92YBbuCP8zkmjS26ps8/cQguryGIcQvpDVq+EbhzGtRftRp9zIyAPRi2bpQMnX2YzeRGl8FcPH3AcyYCC8V09Q5f3S/pi2winWDmN2fTgf4Ay3SOs4Q+h/oIz+Z/6io6Vpr9Q31lh/UeOh4Q/mCOpA/pM+l67oQXN6+EI1dE8JDk7czNSQli6TiAbwfEoZb+JZyFI2yc8EyG8vEeGnCAaWOWPVSBs/wD1S5UgSNLYfDKRsODc3tK4mbY8kwFB1dS0UfFwS1CAdDMMKiloPt8S0x4KmxfpiFtB0xwu4pVxAs71jcD/AI4+oXKGypnfEfccf+ReqiDVEyKBsjQ2ScS2FSp8eYmduHCoZYXzBhlK1mV2A6lmIBqB48+otg0XByntCBw6xYeVfeK37lKMB3GMIks6oin+CF8j6lPaVlq6OZQ25vzKe4bK4spBLatjl3ilSXmnDXUcTZwZXqpRwwrA7Df6io0ZQx9v8REBdW53Zr4mdmJVmdjg6JYiDtC2VYaC0voIkNwW1XrctzPwl6FtgdivjvwofqUYVAKh9EIoM978TD/mrN5o8Rq9KBIk1usMS0BqhZZSD8/2aApYbSj9zBsW0f35mjZSbBzRZ8yuJA7TQ1dcjAbUCXAN8D6cQVIxWwMdsVDRpWyAvmgcQppYO949Rr3LslCdix/N51+df/TKZSrZY0RysKdPOJoIQ6IHXc9TijHLDASHeFwQiN4VALAO9QtFPFBPmKRSWFvsi6UhbXETCyqFxZekxJvigt8RSJ7AjFHbfUWKF4cxDIdzuXGIlMvygxMLfLNO/mEJQLpGsTHK5EqWWFHIwzN1HJvyS7urdR4Zc+csefEZ81ul0XEbBdxXDAliUKyjgZdlxGl0wB7jcTJfJEKNohzL61GvdxmN2W3y8QFaGaOD0Y/cWgp3fR/lMB56t+9zF2u3uAgFBrS/k7QEK4sCOhggsaXxXn17ZWGhwSntwfH3K5Wtxnn/AGgnBo7L4D5YNlnVKPsFvxFEpFeoDyMIDK6mzTQNjEXV4V4UahQti9dbCQRdgeDm7giHKuV8H+5Wu/XgpYAbVo/RBPk6/wA0Gp7ACVvkMT6jsjm2HDAwspw/hPHGplZJZh61BZ8AuPGY52FQG+Z018TDQVBs0mWqfiBfPiLOw+ZgLyf/AKaQ1oy9RZgH1cMrk6lQXfsJtDbLKsmZFfBAWibvIsJLY6qXKQHLG0i2G4TIA0X/AFGSyDQ0ffUpwQxarmU/dh8QzMRQ6dUV9Rj6mJEe2D9gi9A9DP7m+BTSyaACt7mWhfONQK8XmGgTDQy8zP5eCZYfSFDkFLCEDBmAraHDGN51hr5nMKq35pcNAxapY1aBDGWh4mnnqNS3nEs41A9wELuKDECr2ieJbzDAUVtBfvuVG1eoAYiUsSuVwEFrPAr9ZsgxE0ZAXQX8ESkTEPU6t9EUdOxx2bfQoilOXCo+AQ2iSpQf+Ky+IbmxTPG3wP1B/Lhv0MaXfjW+bQHLugmJHWEBE9MdhPh7K+FGImpnWwbPZ7jSCjsLIgXh7xP1gL+5+6X/ADRkYxYl9riFi2uKfnlXVa5mTcQkYNOhTPbUYA00RpYb6PhviKbBbM512XiWaNvwU2NOYCylNwMNFe6iwQ6P7VdxGYzX9TlLBHLlqn9SlGeEv5kA0BoVflEOQ5v/AJr0xdfsMHyzPWDf/wBrUuYMSVUJIe0GraxTFmBwM/MvAhAFgqr8zFArypqtQZcQcIuLCFmeGAexDZNy05u7nKB3YlkOmXBKe2eg83DxmsRsSJwVUQ4D3Chv5RXdzUG5eYWLEyIZwC/lijbjqUKsHbA2sVrsV3NNB2g/OoGAFlwQyJOtGArcEhKpbaHBcBRdSXbn+pdRtwQZY9wGopS/3BvMPF5lt4YbGFscQptIh5lDMu6HMSk2irdQOa1ALV9QgG1W82bPUQcdf8v/ACO6vBsnfy+ds42cwHyc+tTECg5Y+COHh7PLviKuulmnz3KSlWUfF4YzVdzQ/bLeWd/4SVG57RP0A3/ll4tGBAGjGnBLrIFGQiUjH6R20TNGvLL/AHALaW+ZQaH1EW6paN45xKw2BUpoWlrC7ebeAHYYztYNdh/I19nftm8MjRZFNNxPNEBBvCP6gx93Hh4ARMHdl73KUHmxfikgjWdFH8E/ooX+pWVvfv4+KqMv2lDZiwVX7hgQZLL7DJDfhCr4B1BkgCBsT/8AGqm0LlKA06IAITqmoM1K6Vo7ED9IiArrxL8A5M5jQKhBTFaX+JqInQWVlMIBLuwv4jlwusX8wRRk4Wlrs+ncsDZ0uIOqwqy0TeZVcU7rbOOS2ceyJz1GX/DYxlLfmDG41OHnXdx1EoLUHl8xDrQqWbpgRFwbk1bxUDqftChTmLiQgtTgZWciJo+pko2ExMR6iX0VbWLs6ly6J2liwueJ8RBofMTBYPklb3cZGFFEMBrdMD2ZWL7aWxV2nANRmHa6izklKE69R4I5Bzdr7lJl8t/9fyx31HcB/wB9zA4HQalolVGRGxF15jqeSIYofURVKjEKjQZK+oHRp8ML85hmT7nwFrAfMpmR2afNoLAfgP3Bl23SB8jG4QBATvYB3Cq8Q5OE5E2R5YCdjR4lZOwDiHl9S0CqBq2f3FXYLhwB1a8lmKibegMcTMgFrIRVge+EPNwtPGLp2VuWTe4WvsMSpI9V9EtjhSsr4SOCF11jxXVwrEC2A+LLE8wCApRV3h36tgVo4QlXcyZvIm4iBaqwRTH1/wDIV/8AKYhXI4pHkBeQxCJZcrafesfuNnoYaJaQCZY/zL1DGKTeDyjqcJhxF4bXFG9tUxt5fgpO0V83LSsGNCKcXicCJaG1jlDV238MeF+IU+YQiMWQ+PEQwGVFF4jIaBX+SJqSGFYZV9S8wjR8RMEKhVISwOwjpl6oCwg4C5gUT7MUWtzlUYWpXLEeCzDdwAXT1DLb+pVK3kGpbCnH90rB3jBqZBL3On3Cm/5QeMOjl4JYA1UWLgCG+5kMnR7YbhVl6WY/yfMrvME2S5cl1MOh7Ev2EWvmiVfaLmV1HLr5YZLGAscL2r5l2xxlb/MEUhsFvpUxJD3v09YXXzKeqrba3iNKwWRAcvzKQRwViuD6MJ5Jg+gKggjDNRxY1Hczo9z5P9RZ5RBX+jHLFyn8hUwIGq4+rWAd/g4I5yGCzNlzT6HYCEiDoUPbcCDG2Rlwv8BlmMQ8ER8b+V8TleN0vdOL+IcAjAGVNqfZYkbTsc36igyYFOn1Ksz6FN8B2I37hqrmTuGjzWT/ADFTjIe2c+ZaGgN2xAJZSf8AzbL/ABeCnlcqYo1mMJ+RNy9DwWwmUW+KBmWuRdR4AeuppaNLzDhOm1RPprCnOFlbWsbXQtpZwu+MRJsGs4JaDc+QjrihzDo4LRzMyn1Laoe2D728swjdCPDPUtei2M2aTqKWdOLwRzgteYToCD7cRGmNAog+sTGOcWS4hDFBaJqWrTiKbASw5CUNixyzB2wLagsgfEMs6bZpUxoFeHmUpAMgy+Vi5lei7l1V/uYX5gitrlXjzGh7q5L5pbX4ioEtaHwv8SsUs8qqub4mBHLGw4ggUfLKnkZB/EpYitifxEy2TlILtv43MhdpaYtgZ/5M2xHdV9supSc/07lK3ebQ+qS61zcXwyJlAMZK4ycEw6jugAVhdpncO1qLsc03VjuIR3jCPFrjQCUxAeckUXbnI/3MzL+2Hl+WCMBgBhVdSlIs1ZtH9RCHyrwQVL4b707lA1lSd4zv+GiF0h3l3FFCmLO5hNOWc/qWDLkyIMwZQS6tf5Ey2Vxe5Zl24cF9JvphUBDkWh6tZMrR3L51dS7AuRgrNvNEfpvyQRasv8ClXUNC35h+RuFq/RHCsQWMj7lEle1It1HmoPiU5TkGrljs55kUGFdD/rmRBWQUxOKGEXllttapuaFDxKgI5K4reJ6wlnkDbQE6hcDcHKE9Q5Q90SwWgmWrupcCu3icXEIY9MZvDjvyS6hyZDwqCY7UYF9QZFIVUjv3LNKz9D3wfMHQqcHH7hTQ3mA+QD7lCJdQHkAwEZGg5wXWDm+oBJRsw+0wMCN5TvwRzE6qodU/1KSw1RZcuFr7iAsADL6scHmMNVUAquvQysqmWTLTLhuzEuqToFfFHMdOitA3XhxMtBXkXMKu3UETOCsTVJqqs/7wKIa/1m8sYCNmz9tgZXvspv1TCHSqfSogBCjWTvmfTBZFv2Mb+Io4c2fhiLn7qxh/2oWd4gXLT8Srgx4lC6xBU0oaDQ5bxqCowQSB4CghvtpsLDo/lEDmU6KlHVS78TEumaIUq7ymm9K9sW2qqTPc+6379R2LRR4zXev8S0FehEHwZZSrK4Qp/Mv2pVUuKgAEOS4+aMqVfUr+b0wiSbwFORINtFwc6HmvJBaLfLwtl0cl80Md3IAZEwfmV1BealhK1cURBZxBLgLWzk/+czAgqSjsIglSiqZWoV5cbgwVRyRoUctVNAnC9Q9WDdSrV9BgYi0MAS4HbDSvWCll0MB5Zah/k+oUN/cT3DhWmog/hQzfygHpdZjkSeDRD2K6S0cRCTecSnq4sc+1SnzyS+0tLKas1HBBYWFE/pjQA6EB85uW6+Fn/UrDrVwz3XMysWctBvK3MHKPOatx/MpIGcNTsKCX2OkxGXsDvjEr4S95ALTxDIPHLDT4LvLguAcuLlfZwqkuGyJlFkMF+cSxVhRHOji2Bw3AiAr8kwFfVQI82mLbtV+R3Dunjv8AAsS+mC4u0DWnQC8B3KvBAEtmcbIhTMVvtz4jtewyvexZ8cFqY0D08wn7JOSkO3HpVvw8+meErMRx1krFGp2EfkFcS/dTOb9QbHEzyqRC0zmDjf1N9Sn7mAV/GO4MoTU5aERvpgIUUl7HROYNdjzF7k2dlQNWtpyl3otf3ALKHlqGrhNowjWlj7iiawt0h55T+Iu64q9HFPd68RukpEphX2t1CxXUDVgf4lWY36IALfBLGst5zFW0bMlRFMlLXma6y6DBZcuX+F1NBwSqWhrJOw75+hKxAWAV9RQCrxm/cTpDoESnDuZWVqZDE1WZaqoYLBge+AJI4PjWCXMbbg7WZ/qbAoPXKlKl8VAWRmstjQMucURUVThiUYCnDsZoDdtdepqFCtsP+4Ct6SrDyXqUUMAyqvKstaC21c+IFgBgBRFhnqeiLc2sZTyDW4AeI66Bya3BZTLaEMG2peB4ufhOK/mYoNiwIHeIUynxVMcAiaKjZR2cnkuFAzDAsqhd+iXYCq31jkK7qbIQozZG1WHZfmZQDnADg5V9xQfcf0KWWXuRRDreRu+K1D/wICw5XYeLgMuItNGVgNRSKh/wcwjQYMAODxLwH0V/mML8ASnI9sBWnwqJDA3gYQFXWTUVXIWfoeQYThbca4n2ShVhz7hTTz1KL4mDqL5iLrqzlCikgl9M6snuqdS01Gu6lC5mDAvllTiig6fEKsiCZA78waGdEfFFlHB1LoIFhMsvQhcYtKc9w28Bj3HMvY4oK/qFUVF2xpp4c5gPA3oM3bwSlaKQtuh9MQVbaixWdJi5sw8jqGW11ZH2MLXEOoTiVpn6ZWu8wxeoMgti5fhnUBWrNVz5l3dkXL8Gb8RpZBrNfvXzL2sZyy5LMPxGgJsBf2S4YihgV4LXc2B42b8rse2E9+vPAtcDEG1YqvyRwobQPhCKxJ3ZGgdW7eC5yLBpUuj/ACIS4EBLGmuvMX0tFpeagww8x6CqbJ6U5lWeudf7gU2/i5ybuBYrIgv2MsGBe4HkpuOr5BoPSqsTD2LOOeKSovdmvmPLbB7OMGpQgxXC93zLdEw0F87ZnlzgGfiUfyrYv6aNIeZxNI5C8ssCMV2Vpc2/UULdNdV5XXxBLAtZre7c+42KlamTgOH2RyK2PEUZywEtItGnA7PcZNrdePouse5aUYUZ88DLxtwsOi6VR4Sjl7wkYLchm2UWHk4ctF2/tUrQFTUx0cPEJ6op1dBdRIT1TNf5HqDcDIFDt8l6lMsCBs0cGwfe5nBTmATWNPMt7o6MH/xpsaYjK0o9R+YWOX16lCrGnby9N/cILmNv8kPCxLpsbld3LkGcTCqoaRfCHMBef1MqzrxGu2VCPBGVZ9SqK7LRsikksW1XcbFYC2XNyueQBEAMNfUBKhQ0Ezqk0YYlQCr5jtgdZahyKzzEQiQ8YHYeLzXctIvFJ6/4XuK5PIcKzrBTwQVjNqtP6hmw1J2Pgbg0Fm4q7E0xRUiKW3WwEsjlo92cqLYFgzPwvhwsDuSnXKc8bVjRPR6jSqea+pkpA4vYGHHENRSC5U27GNw8u2gFf1cU5mVn7XeF/iXEYPtyimdvDwOl1ePMBpHHmW1aR6jwPZaR5G/dRzKG8reuIBAtmov9y7Km7Eesx3k3Ku4cGEl0ZQVCuAcMMHWNaL7KfniLDJBVT0aELQPS+c7I62bb2erYfqI4EuUp89vJGMjbq1tPWMkBHSkoEfqXFBWkf9ERCUJYU9dq64lc9VlXr3AFJbHAD4lMirzfv+o2MlZLaOs3czmTtpn7XESLAqn5MpBtkY03zbXqI5mdv2+dwg5FyYLoO3zGWcZAt20Y+IPrzALVVXAEthnlF33UGAiGymL/AIg2SpOqHgPLX3LM5gia4BNKR9RQpauTDuoBkIGlU5TnL8TKoopbDGdHrmCijk2tYWqmSK8DphbX9oP+ljl599xwQ4LYnMP6mJzdRuMIYXcDPcS7CFiRgYMaDx5iiWgF82+TcVhTLfh6ipN2buVaih1LvMsFxQn5iGTEAeJdobhtk/M41PbGywPiEjf2TKlH5haDhMjCGArYocXAbCPs8QYJAbGCctGQaufKIUPNMxIBsLqX7Vonkv4ZXU0S8Adl8yRQ0NV2moIClay5tpb4hMyYhq7WnqUNowbvdLrxGdRqP6KPPqLpLsP+/wDD1KVAPtaLajkf1DTK2qLt6L4mSJNkL/NyQyOV2x2Nq56mQtq+mUumvHUOPKFDmG+fmY7Gyn7s4Imjtt34bzMOShRTrJkuWx5SxnsdeYKNDaeRkK/UxzWNm3q8p9TkxKLPpP3AqEwfRqKfmK3PMHcaOWJSgoCCHoXXDuCAUu3cefG8Sunhhl8U5iIZybwAqnzK+Mk0IdNymhbdWN3eJgPH7ZGuPcIsjnKHqYIkAieNzYxy0F3Y34uWqQhi/R5i2ApqC+TGO5bvjO2Onm4ZXdgMHzB+Vhan5o2eJSJzkLyzhEqOOpzIew7jasNZLcQkuxgb7I7s0QNjN3r4jC0HLgglIODYewNxWWp+pb17i07CAAeg4lO2UqTAukI1UqhFHkMdLixYKj2K32YgmFVCYW7YiS5s+12EtPCk0gWXF3o6hdrrT3GGDuCg8zAuZTB6jbzlhdKqe+fwq5mqCDm3qtMtZWdxV3nqZ1gKtdpfDv7gdAl0R4odn+SV6Mnx2c3voM77/wCPBPmyL37Yi5D/ANlxaonwn6uU83/kukqwBR/cW2I7A5yz9xPeFq19hhivbAfoR239uv3+BqXcthadS2ByIqbIbgog3AGAaD8EuKurBsHMyyagXz56W5mLJ3O3NnJXJKoVglD1FADef8DczCT2Mr4HRMIziw3/AHLTgh+c3CVxnE1u11DNTi6cUEsmnN1Hzn+GJl6k7OpNhhkl4oAYRjJf3AheFbbHC8XEi0u21i67OmogsMqQvlZgT3oBPJ2SpdtLQaIBil9dW78jEa6quc6zQ24YRe4VAhgCrJkAgc/A5gy93S9nWPJNbZS0re3IjDnJ1PfpCPzFiICzZ4MYRA4MBHnLT0kVAkKhDY05GVB3dGM4rj+YqrxdJMqLBnqZSDlxn33M/wCWrLTW36hDl9rAhuFAqqiArHV5/U7s+b/crRUaGXzOWW9C2NrFWKAYAL8bH8dTKSKvtvkPUp1w03z5jSBWDsqBoClYFg7zuUOgWNi+NQ9r+yEiUgBB3j+axDyIaRcDyfEdIy4UA6mwmPNS5aGPs6TCCXY7NvGC+GWAArV1TKkOOQvoDNxIFagVfduCBU1QtXv0CopfaGsvCbIS3isRqOk8wWGsDcZQKrXMQ21vUX+PyKCKJmyMAFlKJWb3iIy3Aoxolfmn/wCKlP8A80ypTASX3+aYDbqAIK1YW/UsYdDIx4gXINRP9iuY4Q4lIBfV1HcXwDq3/mPb/tSU8ji/5lA+WLFGnwStChSMDpbAXCaMGl1iU9mhcF1bCRTxrHb+NB3ESAUt8KXyTBsecTinS9kEMArYDm8zIcGSBeRJYbgIOjn15mcQ2gU84bh6l4Su/shI6BKfOyKc37AH1/7BGcnWt6Yi8vsk8l/PUyIEbs2ereDEBhewEXmzz1DFq7H48tK4iwQPPvUgPPUvTuU2aoGX3bBwQLU+1OXiHSA7Bw9XmzxLw2YCvUIEPkn8wORKgFQEsqJQ1WwZRZmyjb8wtEL4zKK6OCtMcrS3gVfth7JaoIPHnzNInA4PCZIpbLnisG5KD7xpq4IBW8MfMcYhmh9jK2MQbXveoWAFXpg2PLxDzYRQ8t9nEuCkcDtcW1OtsMu9mN1LcC9UcPB1FYYGmo9kIotReJPLmFLg5EcxRcPaVz4aiDA23Z6FzUcMGH9stHN+4hvUYMEeCYtW7g62WxV4fhVnmNjP4MRlSosMsE8S5xAvAY0eJTSV5GBLcO1ch5rnxE0aafqruvMrdNUCq9Xa11CjIgO3yNMNml/xEMyf7WcFG22/UwqVGTyShN/VL9G49aCrkem2UZTill56l3kdg8jsxvcjf84OfMyHhGO6FFMxhRhx9wmDzNAxOYm9GfmEK3A3XYHZx3BIYBbBLoacbDM4nKo/Zr1EBiWpZ13QS8mSYnJk2eZUAJTB7OvSQDQMVgw8WcQho4KLD2w/1LU35U+tkQghdyJ0+YJXSM6dF5CEIVCqOhvL+YaCUu5IlV5OrleSFwHmujsja7pgy+e4tErDbnr/ANgA3hRP6Fwu6+r+VnMCqXgCvj1Cu1nIFdYYqyzmToK58xIwNa+0NMLDONYuz5laCNhZV6h5sZBQfgnJH9nfiJdOY3QnpmGApvTKiVoKH1uc/KJVuXsR3RNIL9pb6AC8PHEL0aLFhn5lqBCkWX5j6CgXAHTnZARgGABaqwNdQTmN8+VszgFzP6Dt7ZelVhvu8AirAuZD2lmihqi38V+5UqU5T88f3MndLysenqYKkLEcP/he5SNfwQ3WZeqV4IQ61v3EpoNobO0/1K5gErHgPcVbzEWcBPJiLeiP4uoHCtlfx8flwvcZKlAtXQShQMkhdhkJzSGm2XCTSvh6jklaoFQ9Q9U7K3ople5W8hiaSXRte52O6WfRz7SlhqJ4ROT3BWILU6u6PpnmA6qBqzk7ni4ipsfMwnD59t/7cT9nSCWeV79woRi3UBb/AJmMbMqB9rV9TJwAYwKjlvbK8ZZzBSimlhtM66Ny5arniWsbauB5OR8ROefV01RtdygRYxbHquGWATXdVPG/5IEqgpS0/wCI9AEZX64JTMuCxRYV47mIKFhgfNGZaDzbpsvgp3KfmMeF3kJyEOBLCV5rvQ/3CeC1Tm45PJxKqiLnNeU7YoxNRFAfEOEBdv6eIGWB1gfCYZVreyvTyStAMS09IZSbtgByuy/4lcE2DF3/AGGchIqR71zFBLBNLwWZ7gsWS1fBZ/MFyjrPPWd7nL6lgPdwliUBx6roSDNJoyL3ZLWILQofJh6dSwJYYqeDUyQo3E8I6qFlwvFei8283UdjAqlNd3gwKXFi09+YxCgKMLEXaJyJz8hMAGiqny1cIiFeGQ+odImybn9XFpryk8lsVEHaD6aiVS1WMPMZkTIJjqYQcAXinRGpdRsvB8sKQ8oH1/EPcgm4kVpG1Sw3XqXDKQ0PfXzBACnK8DWV6ldq4ztKOY1B23PSKgIu4tVU4r3BTG4qXxea8RWGDybVjM1ZKTDy059dTNItdGbhbwmgXKlHTAkZpN0yvhLZ+JyTAzrB7W/ibsjnUE6l4tEmqUY3HiAeU8maW9hGaqlC122dHxLqOgFiHlrfogaELk0+U1oAU4t5NemDzM2QNNKdY7jViBK3fefMFoGlaUrWo8gIs3RZTIyAZvkcWUEI3y0xOcpi8y1Y5xkKO8rwKUEdOFRwdWlgDFOKlWJg23xZbQ5A1Yuzm9dJbR4MbfK+HG5kA4gg5HzzDoJj5pyOFSjvWoAoXmX4hLaARjQuOSFqCfxHQ3Y7xrmOhPnYXjeKgUwau1hTRfO4744B7Co/8lM4vmFednw4glat1U1yeYMYdV+4r4Cq25DcGGte3Cc+ocMOFXUaMG4eSirAdo5jlb2iQGicdxSG1WW3vGJ2MIa36Mza0/P6z5xKTl2hixx1pTEXIkWr19lmJSvxFYzteT1AdKphDsB4mobLNjyP5MwBCsDflGoEUtutfFcn8QtDRQSPAPIwJi3wPh/qOADRVIJKlVmPpqKulFsHwx8yu46dvxrHiAFJXZSIbFTM7amz4lPe5/bkDu4NLCwVzzy+5Qy2pZFmzxKsQxaT2b+RKBDyZJkLrUWJ3bruGBFnah5DYPG5TbpC1XdOahmoZIdCqX3xGdCZYcv+I2RkpLB6P8z/AKcAOoDSulB1X1Kw6XGLLu3+ojxdGm9LqXt2tnjQBqoEA9tcPS/3HRFaqHw+DHFlEByORj+Uwo4bzzW7hpWVtXXqXhZ4c0jq5iCi0Fn9x00cGqOyAULZk+yJQEA4G7XflC1FRgKrxffqKW4Utj3zH0zAC3wvmNSombU/ozGb3CFvhTUpDWEHbaePaZWCtEdrHH8S2Tlgx9O5fgNunHnhiWypY4HLF7i1JOwDmCEgKlBxbKwC/ZTt+OEQ9oE05N8sdXpNuFvYyhmd15tgO/8AEtIAtqgy5n48QQHAAIVUvYoyy0WkjnZXg18wAopQEQGr6PMPBiC8JyA/tMkr+5pQpa3+sx1INtFVL91+4KgCinw3+GCFoTAi8tdVXWYGgBQ9E68O7ZJWNGvGMMdNUo74lBoq+86bq/Ep8+UDk9wKKK1DPNOc6lJHAgbXECcCqXe/wI1GkFoaOaNH7IgYOQvPwniCWwKzZpVnJM+IR5obV7VarzAAMumQd08eYxtPooOrdXHVDZVT1eotMjV4jMRxR9pxGkDlpNrKpazxLxGLCi/5EEGVWAH+CCQynAAfiWOk1kPjzKUgW22fCwRulfa5z14ldnWTh+KhyAUt48NviGcolXwz/ExAogt/OlBKyFsWnmqlIL5GTq1vctCGUbS/Bs9QHESm0plIG5y90Iz1BitfohBQuV0vzuGpEbqmM8lmw/ZmbRXhB0ULr3E9rUFb7E1Eim6CZ7U+F77gShtlLux4mpMlADvzCynVcleSt+4kUM+u2M34YLfSRMW9b/mCBb8E/wAwXFvIdL58QEVItUB6gwbWBi6hRaBp4hXYSWfBCV1NoJLMNGMP/MoAJYyXlf0wOBkXdtRyTVFNk9QdyG5/A9QRRKNrCfEsJBU6dS0KU0h/UwpFkE38zLLeKI66GbSrAvC0fP8A5DqKFVp5viUJKpx0F6lWwVh1WUzs9ShYAWsBba1yy7ISlsPvmIY6bBqo1Cu6AMvlDiKAomc++Ib8hb4uoRKxoTR7fULAdBfwjktpxcZG0YjNcef5iAoKBd/TzNiBVR2+YxURnXoOXGJYGJi64vTaiUSTtClLe5bT4qOrTh3vJXcA2XSBrrnMpV047PmJeb68DOByi27YtKDCmOdbqUhiBY0L8Flt6GCzEqUVdHbnF6l1gg2c5s/xCd/SqneHb+5aQcSsqaNVfuLeFF9wXQ+2OVAOmFvjWZ51BVR5aF3e9Pibx02Sz3zCBWFtQc5hK70VFKcF8PiJoOiXsSy1tlzmQLMMlrt0jLt1gCueTqUS4y0UaWwrjuC68wlPrWtRxdeVVHef6lcLpot/N9RL1WFyeb/qWKhga9pyeScExZT+lm5Ev1xO61M2AulvpVbjMD6L18KJUIWLtX3zEmyV2jn1MQ3pv0Q8mYSL5qK00fENDFnuJnMVFdFrnzEA1tm193FzENiiryQOnNWC+/M9xAv8PcsoXStqOriO0088wvi27bQXadKv1E50KZXqTq9nzH6BvgefXcvgPppmpB2W+xJSm4WMx6rcK0OgLdPiBU+81b8/7lyBVCCv3ChRd5sDXxGSzAh8jzDaN0FfnEo7gOFfg2/MzsBQofowSw/tjZBxVnNT6YZUZKpR48eJTz22C+RmUsB0B1TBKAWN/wDXUvN9txocX12ygV6isHoeow2hDCh99RFpsrhlpm/IzFHwNn1eLgypKU5f8RhtiiJusF+yFLxS3V8CbZ+4hvyuoCBj5L2qNOpUJvKJ8h26g7WRY0VWDrxABQA8vnJC5E4RT5ox8sKwlaOHFn6YVUHg9m2g/Vy37mLT0rVeIEbzQp4wMwgQtWXt/wBSiFFlPbUvIdSwtergFwLF4J4RlcV7wcfcZyYIWfhecXLAHsJX/HcvNDD14eDnsikRY32CWZtmAzQYpDgHxUvvhulAqy8r97jgCEHkC3cTLLNJxErJwInGiU+CmSIunoqwGqJZAKegkjBQsNNnmALO83W//T4iFtgBoydm8RLJblIxm9dR7d6hya4pgqrgVuq8AZgjBN4vl+aqXl4G0NIiU59R66VQDmuDKpAyXiOQ7LzCgaWugLxo9v8AEugmad6U77qXAbQ1CdZMeSGjEQK/eNktAj0zfuG2EtdBqHoplS0PjUzdaygj8wER9NFPutwR7jKK6f6jgsvk+3mEm0cmj5iTkoaQjK7heCq9zWbbM6h5gdNH+YqI6ivT0Q2BuoEPbmD8tNyH0GpaNFwsO/8AM88GGGDozco4jooJdcxdocK2IuFq+SpexrMj5XHNJ7VFWQHayQtgxjavTslyI5lyiQuwfS7IixHIhn4uL2xgRhzibAV+I13e5VY9EhIuFrZuv5g1cgo2v+eIjo2tQjwdQNRD/vQG0XpQLJRM3RWLeuWNxQRMBe3yjChOqup/cZIxo/aU4l4QVDoB0RcFdBhiFHjBYr8S1Y2ANdr0ERrB3SOM8eD+ZWKLaOvECPaooyDs8TAyKl5Sfw7j9pd40j1tpQmD4lzgDARZLD3uMBb7inVSYrIy4Bc8h/qOHAXQyLycwQ9A3/ZKB7gG/mCm/LKMi5UiPhGKdfwQW1DpneOOBw0wyTm3FOM6I8FeR0fPEdi5bY9J4r4mh5WMF9mRezECskRXnjPqZAKGj0ydTb8lAF5vh0fMV71OYdonRCtQMWjgxuNxsJqTqhoTJz7sjTDyZYnWdKlM4LaIR9wMTecKzEhAIB9V5M1tEVZ4xLL13RAimg0MKi0eeGm4aWmKQPndAPeam+ijtOBd5iKThC1+TUa0RWhVcvbL7CKtBuwbWWJmm4F0NnExuNKaE5Dt5uCkEJJcWJwt1EqHhCga+GG/NRbOg4JUlaDy5hFneq1779XLgkUyHROb8zYFYTKfIxUwbR1d8ZhWABCoeq0QjKyWEFkIPDfWxheLfKVtp/EpBT5LKtlDcUa38xpDyNS8AiuugP8AMbqgYMsepzHJVQriHxV9BuOoondXZSF+OIh+bOY94MQQKDALdXiY11wJyK7P4hBbTzhgei9c4ZQxyW2p3iJ3DXGMRXFFkuWBJrB4lnkOr3DWLuAlAMpkeo4OPFr8ssQFsOhAEB0Apf7TPEVYB41uKh68EWacbPDCraKq4GtvcJn1m8nEqQ29rylKpqDSHmgTiTz35hYMQHt/MbtVWA+Dr4gFbwVj7iCy25iCGINvGY9miqOA6eNQK4LsWfdhoqiGH/COZTYYv5lK2FV/buWqdNF4mAwDRKTrZS1+2MEjE3bM2DfcAyui+ouYo5thlBhBbcRRkVaSgX1AsER2UgpzEiXd68QHkPuOOyPPsJfXWt9WzQVC6tyloNXLF22V8PEQWReT+e4YEZGZ9RBeECzP9y3kUE10ayUMsS1kwDfBxLfLK8FMtpV9tZmJMCggtxFXmKyGslsbW3+pcNQL61dRtQormqamRlZXPcQtZ4zHy5yLh0RirCdrG+DqVoAwBuMKV5EtYbTTBMWiq2nZASWrCxnxL4Ez4p1LPWWdwYE2C1yuGvCPJ/TL2tqgtfygazAoOTPiOIsYUKuMPM7O7zHnZZq5L1VypxJg0cb78yvUGW1ee4sW2NAU39x602Oi4jqyDzKE9cRZMAdjRleYsoGkmseEidJSNEEYNZi/ypSUlyVA8VqaABWhmFVEBQVM5qkwxaFgN8Ia6VVwPD4SMlAVFuDAyUIi84cZgUDTmEHBXUsTjo5A9S5qwc8YlkHGoQLrbEEw5Vhm8zCdCmc8w2fNoLJigBejGc8v1MfE1hal4DezdHUuI6pQYLn/2Q==";
        if(isset($image) && $image != "")
        {
            $current_time = time();
            //$output_file = $imagepath.$current_time.'.'.$ext;
            if($extra != "")
            {
                $output_file = $imagepath.$current_time.$extra.'.'.$ext;
            }
            else
            {
                $output_file = $imagepath.$current_time.'.'.$ext;
            }
            $decodedimage = base64_decode($image);
            $error_upload = 0;
            if(!$fp = @fopen($output_file, 'w')){
                $error_upload = 1;
            }
            if($error_upload != 1) {
                if(@fwrite($fp, $decodedimage)===false) {
                    $error_upload = 1;
                }
            }
            @fclose($fp);
            if($error_upload == 0) {
                if($extra != "")
                {
                    $final_image = $current_time.$extra.'.'.$ext;
                }
                else
                {
                    $final_image = $current_time.'.'.$ext;
                }
                return $final_image;
            } else {
                return FALSE;
            }
        }
        else
        {
            return FALSE;
        }
    }
    /**
     * @desc : Check unique Operation for Update Webservice
     * @param type $uniqueField : unique fields .
     * If multiple than comma separate
     * @param type $post: post data array
     * @param type $table : db table name
     * @param type $neglectField : neglect field , multiple with comman seperate
     * @param type $fieldEncode : field encode array
     * @return boolean
     */
    public function checkUnqiueForUpdate($post,$table,$uniqueField,$neglectField,$fieldEncode) {
        $dataArray = $this->generateArrayForQuery($post,$neglectField = array(),'add',$fieldEncode);
        $pk = $this->getPrimaryKeyOfTable($table);
        if($pk) {
            $post = $dataArray["combine"];
            $uniqueFieldArray = explode(',',$uniqueField);
            $unquieFieldCount = count($uniqueFieldArray);
            for($k = 0 ; $k < $unquieFieldCount ;$k++ ) {
                $uniqueFiedCon[] = "({$uniqueFieldArray[$k]} LIKE '%{$post[$uniqueFieldArray[$k]]}%')";
            }
            $uniqueCondition = (count($uniqueFiedCon) > 1) ? @implode(' AND ', $uniqueFiedCon) : "{$uniqueFiedCon[0]}";
            $sql = "SELECT * From $table Where $uniqueCondition AND $pk != {$post[$pk]}";
            $countD = Yii::app()->JsonWebservice->getCount($sql,$dataobj = 'db1');
            if($countD > 0) {
                return FALSE ;
            } else {
                return true;
            }
        } else {
            return FALSE;
        }
    }
    /**
     * @desc : Get Primary key of Table
     * @param type $table : table name
     * @return boolean
     */
    public function getPrimaryKeyOfTable($table) {
        $sql = "SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'";
        $response = Yii::app()->JsonWebservice->getRowData($sql, $dataobj = 'db1');
        if($response) {
            return $response["Column_name"];
        } else {
            return false;
        }
    }
}