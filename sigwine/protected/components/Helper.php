<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of App
 *
 * @author lenovo
 */
define('ECOM_API_USER', 'ecomadmin');
define('ECOM_API_KEY', 'CreDENcys251apiUser');
//For dev.credensys.com
//define('ECOM_API_URL', 'http://dev.credencys.com/ecom/connect/');
//For magento.demo.phoenixmarketcity.com
define('ECOM_API_URL', 'http://magento.demo.phoenixmarketcity.com/index.php/connect/');

class Helper {

    public static function path($dir = '') {
        return YiiBase::getPathOfAlias("webroot") . '/' . $dir;
    }

    public static function url($dir = '') {
        return 'http://' . $_SERVER['HTTP_HOST'] . Yii::app()->baseUrl . '/' . $dir;
    }

    //for echo '<pre>';print_r.
    public static function pr($obj, $ex = 0) {
        echo "<pre>";
        print_r($obj);
        echo "</pre>";
        if ($ex)
            exit;
    }

    ## get upload path

    public static function getImageUploadPath($path = '', $secure = '') {
//        $basePath = Yii::app()->basePath;
        $basePath = $_SERVER['DOCUMENT_ROOT'] . Yii::app()->baseUrl . '/';
        if (!empty($secure)) {
            $basePath = $_SERVER['DOCUMENT_ROOT'] . Yii::app()->baseUrl . '/';
        }

        if (!empty($path)) {
            $basePath = $basePath;
        }
        return $basePath;
    }

    ## get upload path

    public static function getImageUrl($path = '', $secure = '') {
//        $basePath = Yii::app()->basePath;
        $baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . Yii::app()->baseUrl . '/';
        if (!empty($secure)) {
            $baseUrl = 'https://' . $_SERVER['HTTP_HOST'] . Yii::app()->baseUrl . '/';
        }

        if (!empty($path)) {
            $baseUrl = $baseUrl . $path;
        }
        return $baseUrl;
    }

    ## create newimage name with image_time().ext.

    public static function getNewImageName($imageName) {
        $imageName = strtolower($imageName);
        $paramArr = array();
        $extArr = explode('.', $imageName);
        $ext = array_pop($extArr);
        $tmpImgName = str_replace('.' . $ext, '', $imageName);
        $tmpImgName = preg_replace(array('/\s+/', '/[^A-Za-z0-9\-]/'), array('-', ''), $tmpImgName);
        $newImageName = $tmpImgName . '_' . time() . '.' . $ext;
        return $newImageName;
    }

    ## Upload Image to AWS server

    public static function uploadToS3($fileTempName, $newfilename) {
        // create AWS object for file upload
        $s3 = new S3('AKIAJKNGZAOVTCG7YZIQ', 'RLyC/84pvBfhRbcR3w4ugT96NqRQU4bp/vlKlc0Q');
        // connect AWS bucket
        $s3->putBucket("phoenixmarketcity-cdn", S3::ACL_PRIVATE);
        //move the file to AWS server
        if ($s3->putObjectFile($fileTempName, "phoenixmarketcity-cdn", $newfilename, S3::ACL_PRIVATE))
            return TRUE;
        else
            return FALSE;
    }

    ## Unlink Image  from AWS server

    public static function unlinkToS3($oldFileName) {
        // create AWS object for file upload
        $s3 = new S3('AKIAJKNGZAOVTCG7YZIQ', 'RLyC/84pvBfhRbcR3w4ugT96NqRQU4bp/vlKlc0Q');
        //unlink the file from AWS server
        $result = $s3->deleteObject('phoenixmarketcity-cdn', $oldFileName);
    }

    ##check for file exist

    public static function isExistAtS3($filePath, $return = false) {
        $s3 = new S3('AKIAJKNGZAOVTCG7YZIQ', 'RLyC/84pvBfhRbcR3w4ugT96NqRQU4bp/vlKlc0Q');
        return $s3->getObjectInfo('phoenixmarketcity-cdn', $filePath, $return);
    }

    ##get system parameters form main/config.php->params();

    public static function param($name, $foldername = '', $default = null) {
        if (isset(Yii::app()->params[$name])) {
            if (!empty($foldername)) {
                $path = Yii::app()->params[$name];
                return $path . $foldername . '/';
            }
            return Yii::app()->params[$name];
        } else {
            return $default;
        }
    }

    ## get session value by key

    public static function getSession($key) {
        $session = new CHttpSession();
        $session->open();
        return $session[$key];
    }

    ## set session

    public static function setSession($key, $value) {
        $session = new CHttpSession();
        $session->open();
        $session[$key] = $value;
    }

    public static function objtoarray($object, $flag = '0') {

        return json_decode(json_encode($object), $flag);

//        return CJSON::decode(CJSON::encode($object));
        //return json_decode(json_encode($object));
    }

    /*
     * For webservices
     * Check for require field
     *      */

    public static function checkRequiredField($requestPara = array(), $require = array()) {
        $errorFlag = 0;
        $msg = array();
        foreach ($require as $key => $val) {

            if (!isset($_POST[$val]) || $requestPara[$val] == '') {
                $errorFlag++;
                $msg[] = "$val is required!";
            }
        }
        return array('errors' => $errorFlag, 'msg' => $msg);
    }

    public static function isLoginCheck() {
        if (Helper::getLogedUserId() == '') {
            $controller = new Controller($argument = NULL);
            $controller->redirect(Yii::app()->getBaseUrl(true) . "/");
        }
    }

    public static function isLogin() {
        if (Helper::getLogedUserId() == '') {
            return '0';
        } else {
            return '1';
        }
    }

    public static function deleteFile($filename) {
        if (file_exists($filename)) {
            @unlink($filename);
            return true;
        } else {
            return false;
        }
    }

    //
    public static function manageDirPath($path) {
        if ($path != '') {
            if (!file_exists($path)) {
                mkdir($path, 0777);
                return "1";
            }
            return "1";
        }
        return "0";
    }

    public static function isDevDomain() {
        $curDomain = $_SERVER['HTTP_HOST'];
        $value = strpos($curDomain, "dev.credencys.com");
        if ($value !== false)
            return true;
        else
            return false;
    }

    public static function getLogedUserId() {
        $session = new CHttpSession();
        $session->open();
        return $session['uid'];
    }

    public static function deleterow($model, $id) {
        $model = $model::model()->findByPk($id);
        $model->is_active = 0;
        $model->is_delete = 1;
        if ($model->update())
            return true;
        else
            return false;
    }

    ## UI of cancel button to manage redirection

    public static function cancelButton($obj) {
        $lastUrl = str_replace("index.php/", "", Yii::app()->request->urlReferrer);
        $currentUrl = str_replace("index.php/", "", "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);

        if (strtolower($lastUrl) == strtolower($currentUrl)) {
            $url = Helper::param("siteurl") . $obj->id . "/admin";
        } else {
            $url = $lastUrl;
        }
        $return = '<a class="btn btn-danger btn-sm" id="btncancel" href="' . $url . '">Cancel</a>';
        return $return;
    }

## create slug

    public function createSlug($title) {
        $title = trim($title);
        $title = str_replace('&', 'and', $title);
        $title = str_replace('-', ' ', $title);
        $title = str_replace('_', ' ', $title);
        $title = preg_replace(array('/\s+/', '/[^A-Za-z0-9\-]/'), array('-', ''), $title);
        return $title;
    }

    /**
     * @desc curl http request
     * @param type $url
     * @param type $param(POST parameter)
     * @param type $header (Request header)
     * @return string     /
     */
    public static function requestCurl($url, $param = array(), $header = array()) {
        if ($url != '') {
            try {
                $client = new EHttpClient($url, array('maxredirects' => 0, 'timeout' => 60000));
                $client->setMethod(EHttpClient::POST);
                if (!empty($header))
                    $client->setHeaders($header);
                if (!empty($param))
                    $client->setParameterPost($param);
                //$client->setRawData($tokenJsonStr,'json');
                $response = $client->request();
            } catch (Exception $e) {
                $response = new EHttpResponse('Client side exception', array(), "", '1.1', PHP_EOL . 'Exception: ' . $e->getMessage() . PHP_EOL);
            }
            //Helper::pr($response,9);
            if ($response->getStatus() == '200')
                return $response->getBody();
            else if ($response->getStatus() == '207') {
                $resp = $response->getBody();
                //$resp = Helper::objtoarray(json_decode($resp));
                return $resp;
            } else if ($response->getStatus() == '201') {
                return '1'; ## if status 201, than insertion on db through api is successfull.
            } else {
                //Helper::pr($response,8);
                return 'error';
            }
        }
    }

    ## get time of INDIA
    ## $ymd : if date format needed in Y-m-d then it will be 1 else you wand y-m-d h:i:s then no need to set

    public static function GetDateTime($ymd = '') {
        $indiatimezone = new DateTimeZone("Asia/Kolkata");
        $date = new DateTime();
        $date->setTimezone($indiatimezone);
        if ($ymd) {
            return $date->format('Y-m-d');
        } else {
            return $date->format('Y-m-d h:i:s');
        }
    }

    /**
     * @desc Remove Directory Recursivly
     * @param type $dir
     * @return type
     */
    function recurseRmdir($dir) {
        if (substr($dir, strlen($dir) - 1, 1) != '/') {
            $dir .= '/';
        }
        $files = glob($dir . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::recurseRmdir($file);
            } else {
                unlink($file);
            }
        }
        return rmdir($dir);
    }

    /**

     * 
     * @param type $files : array of files
     * @param type $path : path where to upload image
     * @param type $max_file_size : max allowed size
     * @return string     /
     */
    public static function ImageUpload($files = '', $path = '', $max_file_size = '') {
        $allfiles = array();
        if (!empty($files) && !empty($path)) {
            $errors = array();
            $root = $files;
            foreach ($root['tmp_name'] as $key => $tmp_name) {
                $file_name = $root['name'][$key];
                $file_size = $root['size'][$key];
                $file_tmp = $root['tmp_name'][$key];
                $file_type = $root['type'][$key];
                if (!empty($max_file_size)) {
                    if ($file_size > $max_file_size) {
                        $errors[] = "File size must be less than $max_file_size";
                    }
                }
                if (!is_dir($path)) {
                    Helper::makeDirectory($path);
                }
                if (empty($errors) == true) {
                    if (is_dir($path . $file_name) == false) {
                        $file_name = time() . '_' . $file_name;
                        $allfiles[] = $file_name;
                        $testname = $path . $file_name;
                        $b = move_uploaded_file($file_tmp, $testname);
//                        echo $b;
//                        echo '<br>';
                    }
                } else {
                    print_r($errors);
                }
            }
        }
        return $allfiles;
    }

    public static function makeDirectory($path, $permission = 0777) {
        $path = $path;
        if (!is_dir($path)) {
            if (!file_exists($path)) {

                $old = umask(0);
                $isCreated = mkdir($path, $permission);
                umask($old);

                if ($isCreated) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**

     * @desc : insert multiple records in database at once
     * @param type $table_name : name of the table
     * @param type $array_key_value : key value pair of array key will be field of db and value will be value what we want to insert in that field make sure all the key value should match db fields
     * @return boolean   if succuss then return true else false  /
     * 
      //   $table_name = "table_booking";
     * for ex.     $array_key_value[] = array(
      //            'mall_id' => $mall_id,
      //            'service_id' => $service_id,
      //            'date' => $date,
      //            'time' => $time,
      //            'number_of_peoples' => $number_of_peoples,
      //            'is_pending' => 1,
      //            'customer_id' => $customer_id,
      //            'created_by' => $customer_id,
      //            'modified_by' => $customer_id,
      //            'customer_name' => $customer_name,
      //            'customer_phone' => $customer_phone,
      //            'customer_email' => $customer_email,
      //            'created_on' => new CDbExpression('NOW()'),
      //            'modified_on' => new CDbExpression('NOW()')
      //        );
     */
    public static function multiRecordInsert($table_name = '', $array_key_value = '') {
        if (!empty($table_name) && !empty($array_key_value)) {
            $connection = Yii::app()->db->getSchema()->getCommandBuilder();
            $command = $connection->createMultipleInsertCommand($table_name, $alertStatus);
            $result = $command->execute();
            if ($result) {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * @desc : Send mail with using PhpMailer lib
     * @Created by : Piyush Sutariya
     */
    public static function sendMailByMailer($content, $subject, $to, $from, $title = 'Sigwine', $attach_path = '', $type = "cc", $bc_arr = array()) {
	
        $mail = new PHPMailer();
        $mail->IsSMTP(); // telling the class to use SMTP
        //$mail->Host       = "mail.yourdomain.com"; // SMTP server
        //$mail->SMTPDebug = 1;                     // enables SMTP debug information (for testing)
        // 1 = errors and messages
        // 2 = messages only
                       // enable SMTP authentication
//        $mail->SMTPSecure = "tls";                 // sets the prefix to the servier
                 // sets the prefix to the servier
        //For dev cred
//        $mail->Host = "180.179.92.2";
//        $mail->Port = 25;
//        $mail->Username = "mailerscnk";
//        $mail->Password = "m@ssw0rd";
	$mail->SMTPAuth = true;  
        $mail->SMTPSecure = "ssl";        
	$mail->Host = "smtp.exmail.qq.com";
        $mail->Port = 465;
        $mail->Username = "app@sigwine.com";
        $mail->Password = "Sigwine2016";
        $from = 'app@sigwine.com';
        $mail->AddReplyTo($from);
        $mail->SetFrom($from, $title);

        $to = explode(",", $to);

        foreach ($to as $email) {
            $mail->addAddress($email);  // Add a recipient
        }
        $mail->Subject = $subject;
//        if ($attach_path != '') {
//            $mail->addAttachment($attach_path, 'Invoice');
//        }
        $mail->MsgHTML($content);
//        echo 'dskjfghsjdf';die;
//        Helper::pr($mail,1);
        // Add in to the cc
        
        if (count($bc_arr) > 0) {
            if ($type == 'cc') {
                foreach ($bc_arr as $k => $v) {                    
                    $mail->AddCC($v);
                }
            } else {
                foreach ($bc_arr as $k => $v) {
                    $mail->AddBCC($v);
                }
            }
        }
        if (!$mail->Send()) {
             echo "Mailer Error: " . $mail->ErrorInfo;exit;
            return false;
        } else {
            //echo "Email sent! pls check it out and activate your account";
            return true;
        }
    }

    /**
     * @param array $file : array of image file
     * @param stirng $imagepath : image path
     * @param stirng $imagepathThumb : image thumb path
     * @param stirng $imagepathMedium : image medium path
     * @param stirng $old_image
     * @param string $j for multiple image/
     */
    public static function uploadImage($image_name, $temp_name, $imagepath, $imagepathThumb, $imagepathMedium, $thumbSize, $mediumSize, $old_image = "", $j = "") {
        $time = time();
        if (isset($image_name) && $image_name != "") {
            $filename = explode(".", $image_name);
            $fileext = $filename[count($filename) - 1];
            $newfilename = $j . $time . "." . $fileext;

            //unlink old image
            if (isset($old_image) && $old_image != "") {
                if (file_exists($imagepath . $old_image) && $old_image != "") {
                    @unlink($imagepath . $old_image);
                    @unlink($imagepathThumb . $old_image);
                    @unlink($imagepathMedium . $old_image);
                }
            }

            @move_uploaded_file($temp_name, $imagepath . $newfilename);

            /**
             * @desc : Resize uploaded image
             */
            $originalFile = $imagepath . $newfilename;

            $imgResize = new ResizeImage();
            $imgResize->load($originalFile);
            //Thumb created
            $thumbSize = @explode("x", $thumbSize);
            $imgResize->resize($thumbSize[0], $thumbSize[1]);
            $imgResize->save($imagepathThumb . $newfilename);

            //Medium image created
            $mediumSize = @explode("x", $mediumSize);
            $imgResize->resize($mediumSize[0], $mediumSize[1]);
            $imgResize->save($imagepathMedium . $newfilename);

            return $newfilename;
        }
    }

    public static function dateRange($first, $last, $step = '+1 day', $format = 'Y-m-d') {

        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);

        while ($current <= $last) {

            $dates[] = date($format, $current);
            $current = strtotime($step, $current);
        }

        return $dates;
    }

}
