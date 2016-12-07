<?php
error_reporting(E_ALL);
class WebserviceController extends Controller {

    public $iraudio, $irvideo, $irimage, $irthumbimage, $resourceimage;

    public function __construct() {
        $this->iraudio = Yii::app()->getBaseUrl(true) . '/images/profile/';
    }

    /*
     * @desc : User login
     */

    public function actionlogin() {
        $wb = new Webservice();
        $fields = $_REQUEST;
        // Check device token
        if (isset($fields['device_token']) && $fields['device_token'] != '') {
            $post = array();
            $post['device_token'] = '';
            $where = "device_token = '{$fields['device_token']}'";
            $wb->updateData($post, '', '', $where, "users");
            $checkFlag=false;
            $login_type=$fields["login_type"];
            if($login_type=="1"){
                if (isset($fields['email']) && $fields['email'] != '') {
                    $checkFlag=true;
                }
            }else{
                $where="";
               if($login_type=="2"){
                    $post["facebook_id"]=$fields["uniq_id"];
                    $where=" facebook_id='".$fields["uniq_id"]."'";
               }else if($login_type=="3"){
                    $post["webo_id"]=$fields["uniq_id"];
                    $where=" webo_id='".$fields["uniq_id"]."'";
               }else if($login_type=="4"){
                    $post["wechat_id"]=$fields["uniq_id"];
                    $where=" wechat_id='".$fields["uniq_id"]."'";
               }
               $checkFlag=true;
            }
            if ($checkFlag) {
                if($login_type=="1"){
                   $response = $wb->userValidate($fields['email'], $fields['password']); 
                }else{
                   $response = $wb->userSocialValidate($where); 
                }
                if ($response['status'] == '1') {
                    $sql1 = "SELECT count(*) as tot FROM orders WHERE user_id = '" . $response['data']['user_id'] . "' 
                            and order_status='in_cart'";
                    $row1 = $wb->getRowData($sql1);
                    $cartTotal = $row1['tot'];
                    $response["cartTotal"]=$cartTotal;
                    $post = array();
                    $post['device_token'] = $fields['device_token'];
                    $post['device_type'] = $fields['device_type'];

                    $where = "user_id = {$response['data']['user_id']}";
                    $wb->updateData($post, '', '', $where, "users");
                }else{
                    $response = array('status' => '-2', 'data' => array('message' => 'Invalid username or password!'));
                }
           } else {
               if($login_type=="1"){
                    $response = array('status' => '0', 'data' => array('message' => 'Email is blank'));
               }else{
                    $response = array('status' => '0', 'data' => array('message' => 'Social login id is blank'));
               }
          }
        } else {
            $response = array('status' => '-1', 'data' => array('message' => 'devicetoken is blank'));
        }
        echo json_encode(array("response" => $response));
        die;
    }

    /*
     * @desc : User registration
     */

    public function actionregister() {
	
        $wb = new Webservice();
        $fields = $_REQUEST;
       // if email or devicetoken field is not blank
        if (isset($fields['email']) && $fields['email'] != '' && isset($fields['device_token']) && $fields['device_token'] != '') {
            $email = $fields['email'];
            // Check device token
            $doRegister=false;
            $login_type=$fields["login_type"];
            $updateData=false;
            $post = array();
            $post['first_name'] = (isset($fields['first_name']) ? $fields['first_name'] : "");
            $post['last_name'] = (isset($fields['last_name']) ? $fields['last_name'] : "");
            $post['device_token'] = $fields['device_token'];
            $post['device_type'] = $fields['device_type'];
            $post['email'] = $email;
            $post['password'] = $fields['password'];
            $post['created_on'] = date('Y-m-d h:i:s');
            if($login_type==1){
                $updateData=false;
                $sql = "SELECT user_id FROM users WHERE `email` LIKE '".$email."'";
                $row = $wb->getRowData($sql);
                if($row){
                    $doRegister=false;
                    $response = array('status' => '-2', 'data' => array('message' => 'email already exists'));
                }else{
                    $doRegister=true;
                }
            }else{
                
                 if($login_type=="2"){
                    $wh=" facebook_id='".$fields["uniq_id"]."'";
                    $post["facebook_id"]=$fields["uniq_id"];
                }else if($login_type=="3"){
                    $wh=" webo_id='".$fields["uniq_id"]."'";
                    $post["webo_id"]=$fields["uniq_id"];
                }else if($login_type=="4"){
                    $wh=" wechat_id='".$fields["uniq_id"]."'";
                    $post["wechat_id"]=$fields["uniq_id"];
                }
                
                $sql = "SELECT user_id FROM users WHERE $wh";
                $rows = $wb->getRowData($sql);   
                if($rows){
                    $response = array('status' => '-3', 'data' => array('message' => 'User with social login already exists'));
                    $doRegister=false;
                    $updateData=false;
                }else{
                    $sql = "SELECT user_id FROM users WHERE `email` LIKE '".$email."'";
                    $row = $wb->getRowData($sql);  
                    
                    if($row){
                        $user_id=$row["user_id"];
                        $doRegister=false;
                        $updateData=true;
                    }else{
                        $doRegister=true;
                        $updateData=false;
                   }
                }   
            }
            if($doRegister){
                $post1 = array();
                $post1['device_token'] = '';
                $where = "device_token = '{$fields['device_token']}'";
                $wb->updateData($post1, '', '', $where, "users");
                // Add user credentials and role
                $response = $wb->addData($post, '', '', 1, "users");
                $user_id = $response['data']['lastid'];
                if ($response["status"]==1) {
                    $response = array("status" => '1',"data"=>array("message"=>"User has been registered successfully!","user_id"=>$user_id));
                } else {
                    $response = array("status" => '-1',"data"=>array("message"=>"There is some error while registering user!"));
                }
                $this->sendRegistrationMail($email, $email, base64_encode($fields['password']) , $user_id);
            }
            if($updateData){
               $post1 = array();
               $post1['device_token'] = '';
               $where = "device_token = '{$fields['device_token']}'";
               $wb->updateData($post1, '', '', $where, "users");
                // Update user credentials and role
               $where = "user_id = $user_id";
               $response = $wb->updateData($post, '', '', $where, "users"); 
               if ($response["status"]==1) {
                    $response = array("status" => '1',"data"=>array("message"=>"updated data successfully","user_id"=>$user_id));
                } else {
                    $response = array("status" => '-1',"data"=>array("message"=>"updated failed"));
                }
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'Email or devicetoken are blank'));
        }
        echo json_encode(array("response" => $response));
        die;
    }

    /*
     * @desc : User Forgot password
     */

    public function actionForgotPassword() {
	
        $para = $_REQUEST;
    	$email_id = $para['email'];
        if (isset($email_id) && $email_id != '') {
            $user = Users::model()->findByAttributes(array('email' => $email_id));

            if ($user != null) {
                //if($user->ur_id == '3') { // parent condition
                $email = $user->email;
                $password = base64_decode($user->password);
		$u_id = $user->user_id;
                $to = $email;
                if ($this->sendForgetPassMail($to, $email, base64_encode($password), $u_id)) {
                    $finalresponse = array('response' => array('status' => '1', 'Message' => 'Mail Sent Successfully'));
                } else {
                    $finalresponse = array('response' => array('status' => '-1', 'Message' => 'There is some error while sending Email.'));
                }
            } else {
                $finalresponse = array('response' => array('status' => '-2', 'Message' => 'User is not registered'));
            }
        } else {
            $finalresponse = array('response' => array('status' => '-1', 'Message' => 'Enter Email Value'));
        }
        echo json_encode($finalresponse);
        die;
    }

    // send forgot Password mail
    public function sendForgetPassMail($to, $email, $password, $u_id) {
	
	$password = base64_decode($password);
	// case of social login.
	if($password == '')
	    $password = "Social logins do not use password, Please use any associated social account to login.";
	
	$wb = new Webservice();
        $sql = "select email_text from email_template where template_code='FORGOTPASSWORD'";
        $templateInfo = $wb->getRowData($sql);
	$body=$templateInfo['email_text'];
	$body=  str_replace('{EMAIL}', $email, $body);
        $body=  str_replace('{PASSWORD}', $password, $body);
        $from = "no-reply@sigwine.com";
        $headers = "From: SigWine<$from>\r\n";
        $headers .= "Content-type: text/html\r\n";
        $subject = "SigWine Password recovery email";
        // now lets send the email.
	if (Helper::sendMailByMailer($body, $subject , $to, $from, "Sigwine" , $attach_path = '', $type = "cc", $bc_arr = array()))
	{
            return true;
        } else {
            return false;
        }
    }
    public function sendRegistrationMail($to, $from, $password, $u_id) {
	
	$password = base64_decode($password);
	$wb = new Webservice();
        $sql = "select email_text from email_template where template_code='REGISTERING'";
        $templateInfo = $wb->getRowData($sql);
        $body = $templateInfo['email_text'];
        $body =  str_replace('{EMAIL}', $to, $body);
        $body =  str_replace('{PASSWORD}', $password, $body);
        $from = "no-reply@sigwine.com";
        $headers = "From: SigWine<$from>\r\n";
        $headers .= "Content-type: text/html\r\n";
        $subject = "Thank you for registering with SigWine!";
        // now lets send the email.
	
	if (Helper::sendMailByMailer($body, $subject , $to, $from, "Sigwine" , $attach_path = '', $type = "cc", $bc_arr = array()))
	{
            return true;
        } else {
            return false;
        }
    }
    //Register with facebook
    public function actionregisterWithFacebook() {
        $wb = new Webservice();
        $fields = $_REQUEST;
        // if email field is not blank
        if (isset($fields['email']) && $fields['email'] != '' && isset($fields['facebookid']) && $fields['facebookid'] != '') {
            $email = $fields['email'];
            $facebookid = $fields['facebookid'];
            $sql = "SELECT userid FROM users WHERE email = '$email'";
            $rowdetail1 = $wb->getRowData($sql);
            // Check email is already exist or not
            if ($row) {
                $response = array('status' => '-2', 'data' => array('message' => 'email already exists'));
            } //elseif ($rowdetail1)
            if ($rowdetail1) {
                $response = array('status' => '-3', 'data' => array('message' => 'facebook id already exists'));
            } else {
                // Add user credentials and role
                $post = array();
                $post['groupid'] = 2;
                $post['classid'] = 6;
                $post['typeid'] = 1;
                $post['statusid'] = 2;
                $post['email'] = $email;
                $post['facebookid'] = $facebookid;
                $post['created'] = date("Y-m-d H:i:s");
                $post['is_active'] = 1;
                $response = $wb->addData($post, '', '', 1, "users");
                if ($response['status'] == 1) {
                    $userid = $response['data']['lastid'];
                    //echo $userid;die;
                    // Add user detail
                    $post = array();
                    $post['userid'] = $userid;
                    $post['firstname'] = $fields['firstname'];
                    $post['lastname'] = $fields['lastname'];
                    $post['email'] = $email;
                    $post['mobile'] = $fields['mobile'];
                    $response = $wb->addData($post, '', '', 1, "user_detail");
                    //print_r($response);die;
                    if ($response['status'] == 1) {
                        $sql1 = "SELECT userid,is_active,is_premium,groupid FROM users WHERE userid = '$userid'";
                        $rowdata = $wb->getRowData($sql1);
                        // Send mail to user for account activate
                        $response = array('status' => '1', 'data' => array("userid" => $userid, "is_active" => $rowdata["is_active"],
                                "is_premium" => $rowdata["is_premium"], "groupid" => $rowdata['groupid'], "no_of_ir" => 1, 'message' => 'User account created successfully'));
                    }
                }
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'email address blank or facebookid blank'));
        }
        echo json_encode(array("response" => $response));
        die;
    }

    public function actionchackFacebookId() {
        $wb = new Webservice();
        $fields = $_REQUEST;
        // if facebook id is not blank
        if (isset($fields['facebookid']) && $fields['facebookid'] != '') {
            $facebookid = $fields['facebookid'];
            // Check facebook id is already exist or not
            $sql = "SELECT userid FROM users WHERE facebookid = '$facebookid'";
            $rowdetail = $wb->getRowData($sql);
            //print_r($rowdetail['userid']);die;
            if ($rowdetail) {
                $user_id = $rowdetail['userid'];
                $sql1 = "SELECT userid,is_active,is_premium,groupid FROM users WHERE userid = '$user_id'";
                $rowdata = $wb->getRowData($sql1);
                $response = array('status' => '1', 'data' => array("userid" => $user_id, "is_active" => $rowdata["is_active"],
                        "is_premium" => $rowdata["is_premium"], "groupid" => $rowdata['groupid'], "no_of_ir" => 1, 'message' => 'facebook id already exists'));
                //$response = array('status' => '1', 'data' => array('message' => 'facebook already exists'));
            } else {
                $response = array('status' => '-1', 'data' => array('message' => 'facebook id not exists'));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'facebookid blank'));
        }
        echo json_encode(array("response" => $response));
        die;
    }

    /* Add wine to favourite list */

    public function actionaddtoFavouriteList() {
        $wb = new Webservice();
        $fields = $_REQUEST;
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
            $user_id = $fields['user_id'];
            $product_id = $fields['product_id'];
            $sql = "SELECT product_id FROM favourite WHERE user_id = '$user_id' and product_id= '" . $product_id . "'";
            $row = $wb->getRowData($sql);
            if ($row) {
                $response = array('status' => '-1', 'data' => array('message' => 'Product is already exist in Favourite list'));
            } else {
                $post = array();
                $post['user_id'] = $fields['user_id'];
                $post['product_id'] = $fields['product_id'];
                $response = $wb->addData($post, '', '', 1, "favourite");
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id blank'));
        }
        echo json_encode(array("response" => $response));
        die;
    }

    /* Add wine to favourite list */

    public function actiongetFavouriteList() {
        $wb = new Webservice();
        $fields = $_REQUEST;
        $langPf=(isset($_REQUEST["language_preference"]) && $_REQUEST["language_preference"]!=''?$_REQUEST["language_preference"]:"en");

        if (isset($fields['user_id']) && $fields['user_id'] != '') {
            $user_id = $fields['user_id'];
            // $product_id=$fields['product_id'];
            $sql = "SELECT f.favourite_id,p.* FROM favourite f, product p WHERE p.product_id=f.product_id and f.user_id = '$user_id'";
            $row = $wb->getAllData($sql);
            if ($row) {
                $thumbpath = realpath(Yii::app()->basePath . '/../images/product/thumb/');
                $path = realpath(Yii::app()->basePath . '/../images/product/');
                $productthumbURL = Yii::app()->getBaseUrl(true) . '/images/product/thumb/';
                $productURL = Yii::app()->getBaseUrl(true) . '/images/product/';
                foreach ($row as $sk => $sv) {
                    $productArr[] = array("favourite_id" => $sv["favourite_id"],
                        "product_id" => $sv["product_id"],
                        "product_name" => ($langPf=="cn" && $sv["product_name_cn"]!=""?$sv["product_name_cn"]:$sv["product_name"]),
                        "image" => (file_exists($path . "/" . $sv["image"]) && $sv["image"] != "" ? $productURL . $sv["image"] : ""),
                        "thumb_image" => (file_exists($thumbpath . "/" . $sv["image"]) && $sv["image"] != "" ? $productthumbURL . $sv["image"] : ""),
                        "short_desc" => ($langPf=="cn" && $sv["short_desc_cn"]!=""?$sv["short_desc_cn"]:$sv["short_desc"]),
                        "long_desc" => ($langPf=="cn" && $sv["long_desc_cn"]!=""?$sv["long_desc_cn"]:$sv["long_desc"]),
                        "price" => $sv["price"],
                        "type" => ($langPf=="cn" && $sv["type_cn"]!=""?$sv["type_cn"]:$sv["type"]),
                        "varietal" => ($langPf=="cn" && $sv["varietal_cn"]!=""?$sv["varietal_cn"]:$sv["varietal"]),
                        "location" => ($langPf=="cn" && $sv["location_cn"]!=""?$sv["location_cn"]:$sv["location"]),
                        "country" => ($langPf=="cn" && $sv["country_cn"]!=""?$sv["country_cn"]:$sv["country"]),
                        "year" => $sv["year"],
                        "glassware" => ($langPf=="cn" && $sv["glassware_cn"]!=""?$sv["glassware_cn"]:$sv["glassware"]),
                        "flash_sale_product" => $sv["flash_sale_product"],
                        "product_type" => $sv["product_type"]);
                }
                $response = array('status' => '1', 'data' => $productArr);
            } else {
                $response = array('status' => '-1', 'data' => array('message' => 'No product is added to favourite'));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id blank'));
        }
        echo json_encode(array("response" => $response));
        die;
    }

    /* get favourite list array */

    public function actiongetFavouriteProduct($user_id) {
        $wb = new Webservice();
        $sql = "SELECT f.favourite_id,p.* FROM favourite f, product p WHERE p.product_id=f.product_id and f.user_id = '$user_id'";
        $row = $wb->getAllData($sql);
        $productArr = array();
        if($row){
          foreach ($row as $sk => $sv) {
              $productArr[] = $sv["product_id"];
          }
        }
        return $productArr;
    }

    /* Remove wine from Favourite list */

    public function actionremovefromFavouriteList() {
        $wb = new Webservice();
        $fields = $_REQUEST;
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
            $user_id = $fields['user_id'];
            // $product_id=$fields['product_id'];
            $sql = "DELETE FROM favourite WHERE user_id='" . $fields['user_id'] . "' and product_id = '" . $fields['product_id'] . "'";
            $response = $wb->deleteRecords($sql);
            if ($response == 1) {
                $response = array('status' => '1', 'data' => array('message' => 'deleted successfully'));
            } else {
                $response = array('status' => '-1', 'data' => array('message' => 'Product does not exist'));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id blank'));
        }
        echo json_encode(array("response" => $response));
        die;
    }

    /* Remove wine from Favourite list */
    public function actionsuggestionList() {
        $wb = new Webservice();
        $fields = $_REQUEST;
       
        $thumbpath = realpath(Yii::app()->basePath . '/../images/product/thumb/');
        $path = realpath(Yii::app()->basePath . '/../images/product/');
        $productthumbURL = Yii::app()->getBaseUrl(true) . '/images/product/thumb/';
        $productURL = Yii::app()->getBaseUrl(true) . '/images/product/';
        $langPf=(isset($_REQUEST["language_preference"]) && $_REQUEST["language_preference"]!=''?$_REQUEST["language_preference"]:"en");
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
            $user_id = $fields['user_id'];
            // $product_id=$fields['product_id'];
            $sql = "SELECT p.product_id as product_id,
                    GROUP_CONCAT(DISTINCT  p.product_id SEPARATOR  ',') as products_id,
                    GROUP_CONCAT(DISTINCT  p.type SEPARATOR  ',') as type,
                    GROUP_CONCAT(DISTINCT  p.varietal SEPARATOR  ',') as varietal,
                    GROUP_CONCAT(DISTINCT  p.location SEPARATOR  ',') as location,
                    GROUP_CONCAT(DISTINCT  p.country SEPARATOR  ',') as country,
                    GROUP_CONCAT(DISTINCT  p.year SEPARATOR  ',') as year,
                    GROUP_CONCAT(DISTINCT  p.glassware SEPARATOR  ',') as glassware
                FROM favourite f, product p WHERE p.product_id=f.product_id and f.user_id = '$user_id'";
            $row = $wb->getRowData($sql);

            if ($row) {
                $product_idArr = array_filter(explode(",", $row["products_id"]));
                $typeArr = array_filter(explode(",", $row["type"]));
                $varietalArr = array_filter(explode(",", $row["varietal"]));
                $locationArr = array_filter(explode(",", $row["location"]));
                $countryArr = array_filter(explode(",", $row["country"]));
                $yearArr = array_filter(explode(",", $row["year"]));
                $glasswareArr = array_filter(explode(",", $row["glassware"]));
                $product_id = implode(",", $product_idArr);
                $type = implode(",", $typeArr);
                $varietal = implode(",", $varietalArr);
                $location = implode(",", $locationArr);
                $country = implode(",", $countryArr);
                $year = implode(",", $yearArr);
                $glassware = implode(",", $glasswareArr);
                $select = "Select * from product where (
                            FIND_IN_SET(`type`,'" . $type . "') OR 
                            FIND_IN_SET(`varietal`,'" . $varietal . "') OR 
                            FIND_IN_SET(`location`,'" . $location . "') OR 
                            FIND_IN_SET(`country`,'" . $country . "') OR 
                            FIND_IN_SET(`year`,'" . $year . "') OR 
                            FIND_IN_SET(`glassware`,'" . $glassware . "')) AND product_id NOT IN (" . $product_id . ")  GROUP BY product_id";
                $rows = $wb->getAllData($select);
                if ($rows) {
                    foreach ($rows as $sk => $sv) {
                        $productArr[] = array(
                            "product_id" => $sv["product_id"],
                            "product_name" => ($langPf=="cn" && $sv["product_name_cn"]!=""?$sv["product_name_cn"]:$sv["product_name"]),
                            "image" => (file_exists($path . "/" . $sv["image"]) && $sv["image"] != "" ? $productURL . $sv["image"] : ""),
                            "thumb_image" => (file_exists($thumbpath . "/" . $sv["image"]) && $sv["image"] != "" ? $productthumbURL . $sv["image"] : ""),
                            "short_desc" => ($langPf=="cn" && $sv["short_desc_cn"]!=""?$sv["short_desc_cn"]:$sv["short_desc"]),
                            "long_desc" => ($langPf=="cn" && $sv["long_desc_cn"]!=""?$sv["long_desc_cn"]:$sv["long_desc"]),
                            "price" => $sv["price"],
                            "type" => ($langPf=="cn" && $sv["type_cn"]!=""?$sv["type_cn"]:$sv["type"]),
                            "varietal" => ($langPf=="cn" && $sv["varietal_cn"]!=""?$sv["varietal_cn"]:$sv["varietal"]),
                            "location" => ($langPf=="cn" && $sv["location_cn"]!=""?$sv["location_cn"]:$sv["location"]),
                            "country" => ($langPf=="cn" && $sv["country_cn"]!=""?$sv["country_cn"]:$sv["country"]),
                            "year" => $sv["year"],
                            "glassware" => ($langPf=="cn" && $sv["glassware_cn"]!=""?$sv["glassware_cn"]:$sv["glassware"]),
                            "flash_sale_product" => $sv["flash_sale_product"],
                            "product_type" => $sv["product_type"]);
                    }
                    $response = array('status' => '1', 'data' => $productArr);
                } else {
                    $response = array('status' => '-1', 'data' => array('message' => 'Product does not exist'));
                }
            } else {
                $response = array('status' => '-1', 'data' => array('message' => 'Product does not exist'));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id blank'));
        }
        echo json_encode(array("response" => $response));
        die;
    }
    /* Get favourite list with suggestion list*/
    public function actiongetFavouriteSuggestionList(){
        $wb = new Webservice();
        $fields = $_REQUEST;
        $productArr=array();
        $suggestionArr=array();
        $langPf=(isset($_REQUEST["language_preference"]) && $_REQUEST["language_preference"]!=''?$_REQUEST["language_preference"]:"en");
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
            $user_id = $fields['user_id'];
            // $product_id=$fields['product_id'];
            $sql = "SELECT f.favourite_id,p.* FROM favourite f, product p WHERE p.product_id=f.product_id and f.user_id = '$user_id'";
	    $row = $wb->getAllData($sql);
            if ($row) {
               
                $thumbpath = realpath(Yii::app()->basePath . '/../images/product/thumb/');
                $path = realpath(Yii::app()->basePath . '/../images/product/');
                $productthumbURL = Yii::app()->getBaseUrl(true) . '/images/product/thumb/';
                $productURL = Yii::app()->getBaseUrl(true) . '/images/product/';
                foreach ($row as $sk => $sv) {
	            $is_active = ($sv["status"] == 'Active' ) ? "1" : "0";
                    $productArr[] = array("favourite_id" => $sv["favourite_id"],
                        "product_id" => $sv["product_id"],
                        "product_name" => ($langPf=="cn" && $sv["product_name_cn"]!=""?$sv["product_name_cn"]:$sv["product_name"]),
                        "image" => (file_exists($path . "/" . $sv["image"]) && $sv["image"] != "" ? $productURL . $sv["image"] : ""),
                        "thumb_image" => (file_exists($thumbpath . "/" . $sv["image"]) && $sv["image"] != "" ? $productthumbURL . $sv["image"] : ""),
                        "short_desc" => ($langPf=="cn" && $sv["short_desc_cn"]!=""?$sv["short_desc_cn"]:$sv["short_desc"]),
                        "long_desc" => ($langPf=="cn" && $sv["long_desc_cn"]!=""?$sv["long_desc_cn"]:$sv["long_desc"]),
                        "price" => $sv["price"],
                        "type" => ($langPf=="cn" && $sv["type_cn"]!=""?$sv["type_cn"]:$sv["type"]),
                        "varietal" => ($langPf=="cn" && $sv["varietal_cn"]!=""?$sv["varietal_cn"]:$sv["varietal"]),
                        "location" => ($langPf=="cn" && $sv["location_cn"]!=""?$sv["location_cn"]:$sv["location"]),
                        "country" => ($langPf=="cn" && $sv["country_cn"]!=""?$sv["country_cn"]:$sv["country"]),
                        "year" => $sv["year"],
                        "glassware" => ($langPf=="cn" && $sv["glassware_cn"]!=""?$sv["glassware_cn"]:$sv["glassware"]),
                        "flash_sale_product" => $sv["flash_sale_product"],
			"is_active" => $is_active,
                        "product_type" => $sv["product_type"]);
                }
                $sql = "SELECT p.product_id as product_id,
                   GROUP_CONCAT(DISTINCT  p.product_id SEPARATOR  ',')  as products_id,
                   GROUP_CONCAT(DISTINCT  p.type SEPARATOR  ',') as type,
                   GROUP_CONCAT(DISTINCT  p.varietal SEPARATOR  ',') as varietal,
                   GROUP_CONCAT(DISTINCT  p.location SEPARATOR  ',') as location,
                   GROUP_CONCAT(DISTINCT  p.country SEPARATOR  ',') as country,
                   GROUP_CONCAT(DISTINCT  p.year SEPARATOR  ',') as year,
                   GROUP_CONCAT(DISTINCT  p.glassware SEPARATOR  ',') as glassware
                   FROM favourite f, product p WHERE p.product_id=f.product_id and f.user_id = '$user_id'";
                $row = $wb->getRowData($sql);
                if ($row) {
                    $product_idArr = array_filter(explode(",", $row["products_id"]));
                    $typeArr = array_filter(explode(",", $row["type"]));
                    $varietalArr = array_filter(explode(",", $row["varietal"]));
                    $locationArr = array_filter(explode(",", $row["location"]));
                    $countryArr = array_filter(explode(",", $row["country"]));
                    $yearArr = array_filter(explode(",", $row["year"]));
                    $glasswareArr = array_filter(explode(",", $row["glassware"]));
                    $product_id = implode(",", $product_idArr);
                    $type = addslashes(implode(",", $typeArr));
                    $varietal = addslashes(implode(",", $varietalArr));
                    $location = addslashes(implode(",", $locationArr));
                    $country = addslashes(implode(",", $countryArr));
                    $year = implode(",", $yearArr);
                    $glassware = addslashes(implode(",", $glasswareArr));
                    $select = "Select * from product where (
                                FIND_IN_SET(`type`,'" . $type . "') OR 
                                FIND_IN_SET(`varietal`,'" . $varietal . "') OR 
                                FIND_IN_SET(`location`,'" . $location . "') OR 
                                FIND_IN_SET(`country`,'" . $country . "') OR 
                                FIND_IN_SET(`year`,'" . $year . "') OR 
                                FIND_IN_SET(`glassware`,'" . $glassware . "')) AND product_id NOT IN (" . $product_id . ")  GROUP BY product_id";
                    $rows = $wb->getAllData($select);
                    if ($rows) {
                        foreach ($rows as $sk => $sv) {
			 $is_active = ($sv["status"] == 'Active' ) ? "1" : "0";	
                            $suggestionArr[] = array(
                                "product_id" => $sv["product_id"],
                                "product_name" => ($langPf=="cn" && $sv["product_name_cn"]!=""?$sv["product_name_cn"]:$sv["product_name"]),
                                "image" => (file_exists($path . "/" . $sv["image"]) && $sv["image"] != "" ? $productURL . $sv["image"] : ""),
                                 "thumb_image" => (file_exists($thumbpath . "/" . $sv["image"]) && $sv["image"] != "" ? $productthumbURL . $sv["image"] : ""),
                                "short_desc" => ($langPf=="cn" && $sv["short_desc_cn"]!=""?$sv["short_desc_cn"]:$sv["short_desc"]),
                                "long_desc" => ($langPf=="cn" && $sv["long_desc_cn"]!=""?$sv["long_desc_cn"]:$sv["long_desc"]),
                                "price" => $sv["price"],
                                "type" => ($langPf=="cn" && $sv["type_cn"]!=""?$sv["type_cn"]:$sv["type"]),
                                "varietal" => ($langPf=="cn" && $sv["varietal_cn"]!=""?$sv["varietal_cn"]:$sv["varietal"]),
                                "location" => ($langPf=="cn" && $sv["location_cn"]!=""?$sv["location_cn"]:$sv["location"]),
                                "country" => ($langPf=="cn" && $sv["country_cn"]!=""?$sv["country_cn"]:$sv["country"]),
                                "year" => $sv["year"],
                                "glassware" => ($langPf=="cn" && $sv["glassware_cn"]!=""?$sv["glassware_cn"]:$sv["glassware"]),
                                "flash_sale_product" => $sv["flash_sale_product"],
				"is_active" => $is_active,
                                "product_type" => $sv["product_type"]);
                        }
                    }
                }
                $response = array('status' => '1', 'data' => $productArr,'suggestionArr'=>$suggestionArr);
            } else {
                $response = array('status' => '-1', 'data' => array('message' => 'No product is added to favourite'));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id blank'));
        }
        echo json_encode(array("response" => $response));
        die;
    }

    /*
     *     Get Profile details
     */

    public function actiongetProfile() {
        $wb = new Webservice();
        $fields = $_REQUEST;
        $path = realpath(Yii::app()->basePath . '/../images/profile/thumb/');

        if (isset($fields['user_id']) && $fields['user_id'] != '') {
            // Check email is already exist or not
            $user_id = $fields['user_id'];
            $sql = "SELECT * FROM users WHERE user_id='{$user_id}'";
            $row = $wb->getRowData($sql);
            if ($row) {
                // Add user credentials and role
                $data = array();
                $billingAdd = $this->actiongetBillingAddress(true);
                $shippingAdd = $this->actiongetShippingAddress(true);
                $billingAddString = $this->retAddressString($billingAdd);
                $shippingAddString = $this->retAddressString($shippingAdd);
                $data['user_id'] = $row['user_id'];
                $data['first_name'] = $row['first_name'];
                $data['last_name'] = $row['last_name'];
                $data['password'] = base64_decode($row["password"]);
                $image = (file_exists($path . "/" . $row["profile_image"]) && $row["profile_image"] != "" ? Yii::app()->getBaseUrl(true) . "/images/profile/thumb/" . $row["profile_image"] : "");
                $data['profile_image'] = $image;
                $data['language_preference'] = $row['language_preference'];
                $data['billingAddress'] = $billingAddString;
                $data['shippingAddress'] = $shippingAddString;
                $response = array('status' => '1', 'data' => $data);
            } else {
                $response = array('status' => '-2', 'data' => array('message' => 'User is not registered'));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id is blank'));
        }
        echo json_encode(array("response" => $response));
        die;
    }

    function retAddressString($retArr) {
        $apStr = "";
        if(count($retArr)>0){
            $apStr.=$retArr["first_name"] . " " . $retArr["last_name"] . ", ";
            $apStr.=$retArr["address"] . ", ";
            $apStr.=$retArr["city"] . ", ";
            $apStr.=$retArr["postcode"] . ", ";
            $apStr.=$retArr["country"];
        }
        return $apStr;
    }

    public function actiongetBillingAddress($retArr = false) {
        $wb = new Webservice();
        $fields = $_REQUEST;
        $resArr = array();
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
            $billingAddress = $wb->getRowData("SELECT * from address where user_id='" . $fields['user_id'] . "' and is_billing=1");
            if ($billingAddress) {
                $resArr = array(
                    "user_id" => $billingAddress["user_id"],
                    "address_id" => $billingAddress["address_id"],
                    "label" => $billingAddress["label"],
                    "first_name" => $billingAddress["first_name"],
                    "last_name" => $billingAddress["last_name"],
                    "company_name" => $billingAddress["company_name"],
                    "address" => $billingAddress["address"],
                    "city" => $billingAddress["city"],
                    "state" => $billingAddress["state"],
                    "country" => $billingAddress["country"],
                    "postcode" => $billingAddress["postcode"],
                    "phone" => $billingAddress["phone"],
                    "is_billing" => $billingAddress["is_billing"],
                    "is_shipping" => $billingAddress["is_shipping"]);
                $billingAddString = $this->retAddressString($resArr);
                $response = array('status' => '1', 'Billing' => $billingAddString);
            } else {
                $response = array('status' => '-1', 'data' => array('message' => 'No Address found'));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id is blank'));
        }
        if ($retArr) {
            return $resArr;
        } else {
            echo json_encode(array("response" => $response));
            die;
        }
    }

    public function actiongetShippingAddress($retArr = false) {
        $wb = new Webservice();
        $fields = $_REQUEST;
        $resArr = array();
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
            $billingAddress = $wb->getRowData("SELECT * from address where user_id='" . $fields['user_id'] . "' and is_shipping=1");
            if ($billingAddress) {
                $resArr = array(
                    "user_id" => $billingAddress["user_id"],
                    "address_id" => $billingAddress["address_id"],
                    "label" => $billingAddress["label"],
                    "first_name" => $billingAddress["first_name"],
                    "last_name" => $billingAddress["last_name"],
                    "company_name" => $billingAddress["company_name"],
                    "address" => $billingAddress["address"],
                    "city" => $billingAddress["city"],
                    "state" => $billingAddress["state"],
                    "country" => $billingAddress["country"],
                    "postcode" => $billingAddress["postcode"],
                    "phone" => $billingAddress["phone"],
                    "is_billing" => $billingAddress["is_billing"],
                    "is_shipping" => $billingAddress["is_shipping"]);
                $shippingAddString = $this->retAddressString($resArr);
                $response = array('status' => '1', 'Billing' => $shippingAddString);
            } else {
                $response = array('status' => '-1', 'data' => array('message' => 'No Address found'));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id is blank'));
        }
        if ($retArr) {
            return $resArr;
        } else {
            echo json_encode(array("response" => $response));
            die;
        }
    }

    public function actionsetAsBillingAddress() {
        $wb = new Webservice();
        $fields = $_REQUEST;
        $user_id = $fields['user_id'];
        $post = array();
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
            if (isset($fields['address_id']) && $fields['address_id'] != '') {
                $where = " user_id='" . $fields['user_id'] . "'";
                $post['is_billing'] = '0';
                $response = $wb->updateData($post, '', '', $where, "address");
                $where = " user_id='" . $fields['user_id'] . "' and address_id='" . $fields['address_id'] . "'";
                $post['is_billing'] = '1';
                $response = $wb->updateData($post, '', '', $where, "address");
                if ($response['status'] == 1) {
                    $response = array('status' => '1', 'data' => array('message' => 'Billing address updated successfully!', 'address_id' => $fields['address_id']));
                } else {
                    $response = array('status' => '-2', 'data' => array('message' => 'There is some error while updating Address'));
                }
            } else {
                $response = array('status' => '-1', 'data' => array('message' => 'address_id is blank'));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id is blank'));
        }
        echo json_encode(array("response" => $response));
        die;
    }

    public function actionsetAsShippingAddress() {
        $wb = new Webservice();
        $fields = $_REQUEST;
        $user_id = $fields['user_id'];
        $post = array();
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
            if (isset($fields['address_id']) && $fields['address_id'] != '') {
                $where = " user_id='" . $fields['user_id'] . "'";
                $post['is_shipping'] = '0';
                $response = $wb->updateData($post, '', '', $where, "address");
                $post['is_shipping'] = '1';
                $where = " user_id='" . $fields['user_id'] . "' and address_id='" . $fields['address_id'] . "'";
                $response = $wb->updateData($post, '', '', $where, "address");
                if ($response['status'] == 1) {
                    $response = array('status' => '1', 'data' => array('message' => 'Shipping address updated successfully!', 'address_id' => $fields['address_id']));
                } else {
                    $response = array('status' => '-2', 'data' => array('message' => 'There is some error while updating Address'));
                }
            } else {
                $response = array('status' => '-1', 'data' => array('message' => 'address_id is blank'));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id is blank'));
        }
        echo json_encode(array("response" => $response));
        die;
    }

    /*
     * Update user profile
     */

    public function actionupdateProfile() {
	$wb = new Webservice();
        $fields = $_REQUEST;
	$user_id = $fields['user_id'];
        // if email field is not blank
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
            $sql = "SELECT * FROM users WHERE user_id ='$user_id'";
            $rows = $wb->getRowData($sql);
            if ($rows) {
                $user_id = $fields['user_id'];
                // Add user credentials and role
                $post = array();
                $where = 'user_id = "' . $fields['user_id'] . '"';
                $post['first_name'] = $fields['first_name'];
                $post['last_name'] = $fields['last_name'];
                /*$post['profile_image'] = $fields['profile_image'];*/
                $post['language_preference'] = $fields['language_preference'];
                $path = realpath(Yii::app()->basePath . '/../images/profile/');
                $thumbpath = realpath(Yii::app()->basePath . '/../images/profile/thumb/');
                if (isset($_FILES["profile_image"]) && (!empty($_FILES["profile_image"]["name"]))) {
                    // Upload doctor image
                    $file_name = basename($_FILES["profile_image"]["name"]);
                    $file_info = explode(".", $file_name);
                    $fileExt = $file_info[count($file_info) - 1];
                    $newfilename = time() . "." . $fileExt;
                    move_uploaded_file($_FILES['profile_image']['tmp_name'], $path . "/" . $newfilename);
                    $post["profile_image"] = $newfilename;
                    if (!empty($newfilename)) {
                        $image = new EasyImage($path . "/" . $newfilename);
                        $image->resize(250, 250);
                        $image->save($thumbpath . "/" . $newfilename);
                    }
                }
                $response = $wb->updateData($post, '', '', $where, "users");
                if ($response['status'] == 1) {
                    $response = array('status' => '1', 'data' => array('message' => 'User profile updated successfully', 'user_id' => $user_id));
                }
            } else {
                $response = array('status' => '-2', 'data' => array('message' => 'User is not registered'));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id is blank'));
        }
        echo json_encode(array("response" => $response));
        die;
    }
    public function actionsetLanguage() {
        $wb = new Webservice();
        $fields = $_REQUEST;
        $user_id = $fields['user_id'];
        $post['language_preference'] = $fields['language_preference'];
        $post = array();
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
            if (isset($fields['language_preference']) && $fields['language_preference'] != '') {
                $where = " user_id='" . $fields['user_id'] . "'";
               $post["language_preference"]=$fields["language_preference"];
                $response = $wb->updateData($post, '', '', $where, "users");
                if ($response['status'] == 1) {
                    $response = array('status' => '1', 'data' =>array('message' => 'Language preference updated successfully!'));
                } else {
                    $response = array('status' => '-2', 'data' => array('message' => 'There is some error while updating Language preference'));
                }
            } else {
                $response = array('status' => '-1', 'data' => array('message' => 'Language Preference is blank'));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id is blank'));
        }
        echo json_encode(array("response" => $response));
        die;
    }
    public function actiongetEducationList() {
        $wb = new Webservice();
        $sql = "SELECT * FROM `education`";
        $row = $wb->getAllData($sql);
        $resArr = array();
        $path = realpath(Yii::app()->basePath . '/../images/education/');
        $fileURL = Yii::app()->getBaseUrl(true) . '/images/education/';
        $imageURL = Yii::app()->getBaseUrl(true) . '/images/education/thumb/';
        $langPf=(isset($_REQUEST["language_preference"]) && $_REQUEST["language_preference"]!=''?$_REQUEST["language_preference"]:"en");
        if ($row) {
            $status = "1";
            $msg = "Data found!";
            foreach ($row as $rk => $rv) {
                $resArr[] = array(
                    "education_id" => $rv["education_id"],
                    "title" => ($langPf=="cn" && $rv["title_cn"]!=""?$rv["title_cn"]:$rv["title"]),
                    "thumb" => (file_exists($path . "/thumb/" . $rv["thumb"]) && $rv["thumb"] != "" ? $imageURL . $rv["thumb"] : ""),
                    "description" => $rv["description"],
                    "upload_type" => $rv["upload_type"],
                    "description" => ($langPf=="cn" && $rv["description_cn"]!=""?$rv["description_cn"]:$rv["description"]),
                    "file" => (file_exists($path . "/" . $rv["file"]) && $rv["file"] != "" ? $fileURL . $rv["file"] : ""));
            }
        } else {
            $status = '-1';
            $msg = 'No Education data found!';
        }
        $finalresponse = array('response' => array('status' => $status, 'Message' => $msg, 'data' => $resArr));
        echo json_encode($finalresponse);
        die;
    }

   public function actiongetAddress() {
        $wb = new Webservice();
        $fields = $_REQUEST;
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
            $sql = "SELECT * FROM `address` where user_id='" . $fields['user_id'] . "' AND is_delete = '0'";
            $row = $wb->getAllData($sql);
            $resArr = array();
            if ($row) {
                $billingAddString = "";
                foreach ($row as $rk => $rv) {
                    $billingAddString = "";
                    $addArr[] = array(
                        "user_id" => $rv["user_id"],
                        "address_id" => $rv["address_id"],
                        "label" => $rv["label"],
                        "first_name" => $rv["first_name"],
                        "last_name" => $rv["last_name"],
                        "company_name" => $rv["company_name"],
                        "address" => $rv["address"],
                        "city" => $rv["city"],
                        "state" => $rv["state"],
                        "country" => $rv["country"],
                        "postcode" => $rv["postcode"],
                        "phone" => $rv["phone"],
			"is_delete" => $rv["is_delete"],
                        "is_billing" => $rv["is_billing"],
                        "is_shipping" => $rv["is_shipping"]);
                }
                $response = array('status' => '1', 'data' => $addArr);
            } else {
                $response = array('status' => '-1', 'data' => array('message' => "No Address Found"));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id is blank'));
        }
        echo json_encode(array("response" => $response));
        die;
    }

    public function actionaddNewAddress() {
        $wb = new Webservice();
        $fields = $_REQUEST;
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
            $post = array();
            if (isset($fields['is_billing']) && $fields['is_billing'] == '1') {
                $post['is_billing'] = '0';
            }
            if (isset($fields['is_shipping']) && $fields['is_shipping'] == '1') {
                $post['is_shipping'] = '0';
            }
            if (isset($post) && count($post) > 0) {
                $where = " user_id='" . $fields['user_id'] . "'";
                $response = $wb->updateData($post, '', '', $where, "address");
            }

            $post = array();
            $post['user_id'] = $fields['user_id'];
           // $post['label'] = $fields['label'];
            $post['first_name'] = $fields['first_name'];
            $post['last_name'] = $fields['last_name'];
            $post['company_name'] = $fields['company_name'];
            $post['address'] = $fields['address'];
            $post['city'] = $fields['city'];
            $post['state'] = $fields['state'];
            $post['country'] = $fields['country'];
            $post['postcode'] = $fields['postcode'];
            $post['phone'] = $fields['phone'];
            $post['is_billing'] = $fields['is_billing'];
            $post['is_shipping'] = $fields['is_shipping'];
            $post['created_on'] = date("Y-m-d h:i:s");
            $response = $wb->addData($post, '', '', 1, "address");
            if ($response['status'] == 1) {
                $address_id = $response['data']['lastid'];
                $response = array('status' => '1', 'data' => array('message' => 'Address added successfully', 'address_id' => $address_id));
            } else {
                $response = array('status' => '-1', 'data' => array('message' => 'There is some error while adding data..Please try again!'));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id is blank'));
        }
        echo json_encode(array("response" => $response));
        die;
    }
    public function actionretrieveAddress() {
        $wb = new Webservice();
        $fields = $_REQUEST;
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
            $sql = "SELECT * FROM `address` where address_id='" . $fields['address_id'] . "'";
            $row = $wb->getRowData($sql);
            $resArr = array();
            if ($row) {
                $billingAddString = "";
                $addArr = array(
                    "user_id" => $row["user_id"],
                    "address_id" => $row["address_id"],
                    "label" => $row["label"],
                    "first_name" => $row["first_name"],
                    "last_name" => $row["last_name"],
                    "company_name" => $row["company_name"],
                    "address" => $row["address"],
                    "city" => $row["city"],
                    "state" => $row["state"],
                    "country" => $row["country"],
                    "postcode" => $row["postcode"],
                    "phone" => $row["phone"],
                    "is_billing" => $row["is_billing"],
                    "is_shipping" => $row["is_shipping"]);

                $response = array('status' => '1', 'data' => $addArr);
                
            } else {
                $response = array('status' => '-1', 'data' => array('message' => "No Address Found"));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id is blank'));
        }
        echo json_encode(array("response" => $response));
        die;
    }
      public function actionremoveAddress() {
	
	$wb = new Webservice();
        $fields = $_REQUEST;
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
            if (isset($fields['address_id']) && $fields['address_id'] != '') {
               $up="update orders set address_id=(select address_id from address where is_shipping=1 and user_id=".$fields["user_id"].") where address_id='".$fields["address_id"]."'";
                    $response1 = $wb->executeQuery($up);
		    
                    $upaddress ="update address set is_delete= '1' where address_id='".$fields["address_id"]."'";
                    $response2 = $wb->executeQuery($upaddress);
		    $response = array('status' => '1', 'data' => array('message' => 'Address has been removed!'));
            } else {
                $response = array('status' => '-1', 'data' => array('message' => "Address_id is blank"));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id is blank'));
        }
        echo json_encode(array("response" => $response));
        die;
    }
     public function actionupdateAddress() {
        $wb = new Webservice();
        $fields = $_REQUEST;
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
            if (isset($fields['address_id']) && $fields['address_id'] != '') {
                $post = array();
                if (isset($fields['is_billing']) && $fields['is_billing'] == '1') {
                    $post['is_billing'] = '0';
                }
                if (isset($fields['is_billing']) && $fields['is_shipping'] == '1') {
                    $post['is_shipping'] = '0';
                }
                if (isset($post) && count($post) > 0) {
                    $where = " user_id='" . $fields['user_id'] . "'";
                    $response = $wb->updateData($post, '', '', $where, "address");
                }
                $post = array();
                //$post['label'] = $fields['label'];
                $post['first_name'] = $fields['first_name'];
                $post['last_name'] = $fields['last_name'];
                $post['company_name'] = $fields['company_name'];
                $post['address'] = $fields['address'];
                $post['city'] = $fields['city'];
                $post['state'] = $fields['state'];
                $post['country'] = $fields['country'];
                $post['postcode'] = $fields['postcode'];
                $post['phone'] = $fields['phone'];
                $post['is_billing'] = $fields['is_billing'];
                $post['is_shipping'] = $fields['is_shipping'];
		$post['is_delete'] = '0';
                $post['created_on'] = date("Y-m-d h:i:s");
                $where = "user_id = '" . $fields['user_id'] . "' and address_id='" . $fields['address_id'] . "'";
                $response = $wb->updateData($post, '', '', $where, "address");
		if ($response['status'] == 1) {
                    $address_id = $fields['address_id'];
                    $response = array('status' => '1', 'data' => array('message' => 'Address updated successfully', 'address_id' => $fields['address_id']));
                } else {
                    $response = array('status' => '-1', 'data' => array('message' => 'There is some error while updating data..Please try again!'));
                }
            } else {
                $response = array('status' => '-2', 'data' => array('message' => 'address_id is blank'));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id is blank'));
        }
        echo json_encode(array("response" => $response));
        die;
    }
    public function actionSubscriptionPlanList() {
        $wb = new Webservice();
        $sql = "SELECT * FROM `subscription_plan` where status='Active'";
        $row = $wb->getAllData($sql);
        $resArr = array();
        $path = realpath(Yii::app()->basePath . '/../images/subscription/');
        $thumbpath = realpath(Yii::app()->basePath . '/../images/subscription/thumb/');
        $imageURL = Yii::app()->getBaseUrl(true) . '/images/subscription/';
        $imagethumbURL = Yii::app()->getBaseUrl(true) . '/images/subscription/thumb/';
        $langPf=(isset($_REQUEST["language_preference"]) && $_REQUEST["language_preference"]!=''?$_REQUEST["language_preference"]:"en");
        $priceArr=array();
        if ($row) {
            $status = "1";
            $msg = "Data found!";
            foreach ($row as $rk => $rv) {
                /*
                  $productArr=array();
                  $subsql="select spd.product_id as product_id,p.product_name,p.image,p.short_desc,p.long_desc,p.price,p.wine_color,p.wine_taste from subscription_plan_detail spd,product p where p.product_id=spd.product_id and spd.plan_id='".$rv['plan_id']."' group by spd.product_id";
                  $subrow = $wb->getAllData($subsql);
                  if($subrow){
                  foreach($subrow as $sk=>$sv){
                  $productArr[]=array("product_id"=>$sv["product_id"],
                  "product_name"=>$sv["product_name"],
                  "image"=>(file_exists($path."/".$sv["image"])?$productURL.$sv["image"]:""),
                  "short_desc"=>$sv["short_desc"],
                  "long_desc"=>"long_desc",
                  "price"=>$sv["price"],
                  "wine_color"=>$sv["wine_color"],
                  "wine_taste"=>$sv["wine_taste"]
                  );
                  }
                  }else{
                  $status='-1';
                  $msg='No products data found!';
                  } */
                // Get Plan Price duration with price
                $sql = "Select * from subscription_plan_price where plan_id='".$rv["plan_id"]."' 
                        order by subscription_plan_price_id asc";
                $res = $wb->getAllData($sql);
                $autoPriza=array();
                if($res){
                    $priceArr=array();
                    
                    foreach($res as $k=>$v){
                        $priceArr[]=array("duration"=>$v["duration"],"price"=>$v["price"]);
                        $autoPrizarr[$v["duration"]]=$v["price"];
                    }
                }
                $resArr[] = array(
                    "plan_id" => $rv["plan_id"],
                    "plan_name" => ($langPf=="cn" && $rv["plan_name_cn"]!=""?$rv["plan_name_cn"]:$rv["plan_name"]),
                    "plan_type" => $rv["plan_type"],
                    "price" => (isset($autoPrizarr["auto"])?$autoPrizarr["auto"]:"0"),
                    "priceArr"=>$priceArr,
                    "description" => ($langPf=="cn" && $rv["description_cn"]!=""?$rv["description_cn"]:$rv["description"]),
                    "image" => (file_exists($path . "/" . $rv["image"]) && $rv["image"] != "" ? $imageURL . $rv["image"] : ""),
                    "thumb_image" => (file_exists($thumbpath . "/" . $rv["image"]) && $rv["image"] != "" ? $imagethumbURL . $rv["image"] : ""));
            }
        } else {
            $status = '-1';
            $msg = 'No subscription plan found!';
        }
        $finalresponse = array('response' => array('status' => $status, 'Message' => $msg, 'data' => $resArr));
        echo json_encode($finalresponse);
        die;
    }


    public function actionmySubscriptionPlanList() {
        $wb = new Webservice();
        $fields = $_REQUEST;
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
             $sql = "SELECT sp.*,o.subscription_duration,o.order_total,o.order_qty, o.order_date,o.address_id
                    FROM `res_orders`  o, `subscription_plan`  sp 
                    WHERE sp.plan_id=o.subscription_plan_id AND o.user_id='" . $fields['user_id'] . "' 
                    AND o.type=1";
            $row = $wb->getAllData($sql);
	    $resArr = array();
            $path = realpath(Yii::app()->basePath . '/../images/subscription/');
            $thumbpath = realpath(Yii::app()->basePath . '/../images/subscription/thumb/');
            $imageURL = Yii::app()->getBaseUrl(true) . '/images/subscription/';
            $imagethumbURL = Yii::app()->getBaseUrl(true) . '/images/subscription/thumb/';
            $langPf=(isset($_REQUEST["language_preference"]) && $_REQUEST["language_preference"]!=''?$_REQUEST["language_preference"]:"en");

            if ($row) {
                $status = "1";
                $msg = "Data found!";
                $billingAddString = "";
                foreach ($row as $rk => $rv) {
                    $billingAddress = $wb->getRowData("SELECT * from address where user_id='" . $fields['user_id'] . "' 
                                                     and address_id='" . $rv["address_id"] . "'");
                    $addArr = array();
                    if ($billingAddress) {
                        $addArr = array("label" => $billingAddress["label"],
                            "first_name" => $billingAddress["first_name"],
                            "last_name" => $billingAddress["last_name"],
                            "company_name" => $billingAddress["company_name"],
                            "address" => $billingAddress["address"],
                            "city" => $billingAddress["city"],
                            "state" => $billingAddress["state"],
                            "country" => $billingAddress["country"],
                            "postcode" => $billingAddress["postcode"],
                            "phone" => $billingAddress["phone"],
                            "is_billing" => $billingAddress["is_billing"],
                            "is_shipping" => $billingAddress["is_shipping"]);
                        $billingAddString = $this->retAddressString($addArr);
                    }
                    $priceArr=array();
                     // Get Plan Price duration with price
                    $sql = "Select * from subscription_plan_price where plan_id='".$rv["plan_id"]."' 
                            order by subscription_plan_price_id asc";
                    $res = $wb->getAllData($sql);
                    $autoPriza=array();
                    if($res){
                        $priceArr=array();
                        foreach($res as $k=>$v){
                            $priceArr[]=array("duration"=>$v["duration"],"price"=>$v["price"]);
                            $autoPrizarr[$v["duration"]]=$v["price"];
                        }
                    }
                    $resArr[] = array(
                        "plan_id" => $rv["plan_id"],
                        "plan_name" => ($langPf=="cn" && $rv["plan_name_cn"]!=""?$rv["plan_name_cn"]:$rv["plan_name"]),
                        "priceArr"=>$priceArr,
                        "plan_type" => $rv["plan_type"],
                        "subscription_duration" => $rv["subscription_duration"],
                        "order_total" => $rv["order_total"],
                        "order_qty" => $rv["order_qty"],
                        "order_date" => $rv["order_date"],
                        "price" => (isset($autoPrizarr["auto"])?$autoPrizarr["auto"]:"0"),
                        "description" => ($langPf=="cn" && $rv["description_cn"]!=""?$rv["description_cn"]:$rv["description"]),
                        "image" => (file_exists($path . "/" . $rv["image"]) && $rv["image"] != "" ? $imageURL . $rv["image"] : ""),
                        "thumb_image" => (file_exists($thumbpath . "/" . $rv["image"]) && $rv["image"] != "" ? $imagethumbURL . $rv["image"] : ""),
                        "address" => $billingAddString);
                }
            } else {
                $status = '-1';
                $msg = 'No subscription plan found!';
            }
            $finalresponse = array('response' => array('status' => $status, 'Message' => $msg, 'data' => $resArr));
            echo json_encode($finalresponse);
            die;
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id is blank'));
            echo json_encode($response);
            die;
        }
    }

   
    /* Web service for Get Shopping Cart */

    public function actiongetShoppingCart() {
        $wb = new Webservice();
        $fields = $_REQUEST;
        
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
            $sql = "SELECT sp.*,o.subscription_duration,o.order_total,o.order_id,o.order_qty, o.order_date,o.address_id
                    FROM `orders`  o, `subscription_plan`  sp 
                    WHERE sp.plan_id=o.subscription_plan_id AND o.user_id='" . $fields['user_id'] . "' 
                    AND o.order_status='in_cart' AND o.type=1";
		
            $row = $wb->getAllData($sql);
            $resArr = array();
            $resArrS = array();
            $path = realpath(Yii::app()->basePath . '/../images/subscription/');
            $thumbpath = realpath(Yii::app()->basePath . '/../images/subscription/thumb/');
            $imageURL = Yii::app()->getBaseUrl(true) . '/images/subscription/';
            $imagethumbURL = Yii::app()->getBaseUrl(true) . '/images/subscription/thumb/';
            $langPf=(isset($_REQUEST["language_preference"]) && $_REQUEST["language_preference"]!=''?$_REQUEST["language_preference"]:"en");

            if ($row) {
                $status = "1";
                $msg = "Data found!";
                $billingAddString = "";
                foreach ($row as $rk => $rv) {
                  $billingAddString = "";
                    $billingAddress = $wb->getRowData("SELECT * from address where user_id='" . $fields['user_id'] . "' 
                                                     and address_id='" . $rv["address_id"] . "'");
                    $addArr = array();
                    if ($billingAddress) {
                        $addArr = array("label" => $billingAddress["label"],
                            "address_id" => $billingAddress["address_id"],
                            "first_name" => $billingAddress["first_name"],
                            "last_name" => $billingAddress["last_name"],
                            "company_name" => $billingAddress["company_name"],
                            "address" => $billingAddress["address"],
                            "city" => $billingAddress["city"],
                            "state" => $billingAddress["state"],
                            "country" => $billingAddress["country"],
                            "postcode" => $billingAddress["postcode"],
                            "phone" => $billingAddress["phone"],
                            "is_billing" => $billingAddress["is_billing"],
                            "is_shipping" => $billingAddress["is_shipping"]);
                        $billingAddString = $this->retAddressString($addArr);
                    }
                    $resArrS[] = array(
                        "order_id" => $rv["order_id"],
                        "plan_id" => $rv["plan_id"],
                        "plan_name" => ($langPf=="cn" && $rv["plan_name_cn"]!=""?$rv["plan_name_cn"]:$rv["plan_name"]),
                        "plan_type" => $rv["plan_type"],
                        "subscription_duration" => $rv["subscription_duration"],
                        "order_total" =>  $rv["subscription_duration"] * $rv["price"],
                        "order_qty" => $rv["order_qty"],
                        "order_date" => $rv["order_date"],
                        "price" => $rv["price"],
                        "description" => ($langPf=="cn" && $rv["description_cn"]!=""?$rv["description_cn"]:$rv["description"]),
                        "image" => (file_exists($path . "/" . $rv["image"]) && $rv["image"] != "" ? $imageURL . $rv["image"] : ""),
                        "thumb_image" => (file_exists($thumbpath . "/" . $rv["image"]) && $rv["image"] != "" ? $imagethumbURL . $rv["image"] : ""),
                        "address_id" => $billingAddress["address_id"],
                        "address" => $billingAddString);
                }
            }else{
                $resArrS=array();
            }
            /* Get Incart product info*/
            $sql = "select 		p.product_id,o.order_id,o.order_total,o.order_date,o.payment_method,o.order_qty,o.address_id,o.order_date,
                    p.product_name,p.price,p.image,o.flash_sale_id,p.product_name_cn,p.status  from orders o 
                     LEFT JOIN product p  ON p.product_id=o.product_id 
                    WHERE o.user_id='" . $fields['user_id'] . "' 
                    AND o.order_status='in_cart' AND o.type=2";
            $row = $wb->getAllData($sql);
            
            $path = realpath(Yii::app()->basePath . '/../images/product/');
            $thumbpath = realpath(Yii::app()->basePath . '/../images/product/thumb/');
            $imageURL = Yii::app()->getBaseUrl(true) . '/images/product/';
            $imagethumbURL = Yii::app()->getBaseUrl(true) . '/images/product/thumb/';
            if ($row) {
                $status = "1";
                $msg = "Data found!";
                $billingAddString = "";
                foreach ($row as $rk => $rv) {
		    
		    $billingAddString = "";
                    $addData=true;
                    $now=date("Y-m-d h:i:s");

                    if($rv["flash_sale_id"]!='0'){
                        // check 
                        $subsql = "select fs.* from  flash_sale fs
                                   where fs.sale_start_from<='".$now."' and fs.sale_end>='".$now."' 
                                  and fs.flash_sale_id='".$rv["flash_sale_id"]."'";
                        $subrow = $wb->getRowData($subsql);
                        if(!$subrow){
                             $addData=false;
                             $wb->executeQuery("Delete from orders where order_id='".$rv["order_id"]."'");
                        }
                    }
		   /* Delete product from order if product is inactive
		      Modified date : 20/4/2016	  */ 
 		    if($rv["status"]=='InActive'){
			        
			// Delete Product form order table
			$addData=false;
                 	$wb->executeQuery("Delete from orders where order_id='".$rv["order_id"]."'");                   
                    }
                    if($addData){
                        $billingAddress = $wb->getRowData("SELECT * from address where user_id='" . $fields['user_id'] . "' 
                                                        and address_id='" . $rv["address_id"] . "'");
                        $addArr = array();
                        $billingAddString="";
                        if ($billingAddress) {
                            $addArr = array("label" => $billingAddress["label"],
                                "address_id" => $billingAddress["address_id"],
                                "first_name" => $billingAddress["first_name"],
                                "last_name" => $billingAddress["last_name"],
                                "company_name" => $billingAddress["company_name"],
                                "address" => $billingAddress["address"],
                                "city" => $billingAddress["city"],
                                "state" => $billingAddress["state"],
                                "country" => $billingAddress["country"],
                                "postcode" => $billingAddress["postcode"],
                                "phone" => $billingAddress["phone"],
                                "is_billing" => $billingAddress["is_billing"],
                                "is_shipping" => $billingAddress["is_shipping"]);
                            $billingAddString = $this->retAddressString($addArr);
                        }
                        $resArr[] = array(
                            "order_id" => $rv["order_id"],
                            "product_id" => $rv["product_id"],
                            "product_name" => ($langPf=="cn" && $rv["product_name_cn"]!=""?$rv["product_name_cn"]:$rv["product_name"]),
                            "price" => $rv["price"],
                            "order_date" => date("d/m/Y", strtotime($rv["order_date"])),
                            "order_total" => $rv["order_total"],
                            "order_qty" => $rv["order_qty"],
                            "price" => $rv["price"],
                            "payment_method" => $rv["payment_method"],
                            "image" => (file_exists($path . "/" . $rv["image"]) && $rv["image"] != "" ? $imageURL . $rv["image"] : ""),
                            "thumb_image" => (file_exists($thumbpath . "/" . $rv["image"]) && $rv["image"] != "" ? $imagethumbURL . $rv["image"] : ""),
                            "address_id" => $billingAddress["address_id"],
                            "address" => $billingAddString);
                    }
                }
            }else{
                $resArr=array();
            }
            $dollarValue=0;
            $cartTotal=count($resArrS)+count($resArr);
            if(count($resArrS)==0 && count($resArr)==0){
                $status="-2";
                $msg="Sorry! Your shopping cart is empty.";
            }else{
                /*$from   = 'CNY';
                $to     = 'USD';
                $url = 'http://finance.yahoo.com/d/quotes.csv?e=.csv&f=sl1d1t1&s='. $from . $to .'=X';
                $handle = @fopen($url, 'r');
                $result=array();       
                if ($handle) {
                    $result = fgets($handle, 4096);
                    fclose($handle);
                    $allData = explode(',',$result); 
                    $dollarValue = $allData[1];
                }*/
            }
           $response = array('response' => array('status' => $status, 'Message' => $msg, 'subsciption' => $resArrS,'product'=>$resArr,"dollarValue"=>$dollarValue,"cartTotal"=>$cartTotal)); 
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id is blank'));
        }
        
        echo json_encode($response);
        die;
    }
    public function actiongetDollarValue() {
        $dollarValue=0;
        $from   = 'CNY'; /*change it to your required currencies */
        $to     = 'USD';
        $url = 'http://finance.yahoo.com/d/quotes.csv?e=.csv&f=sl1d1t1&s='. $from . $to .'=X';
        $handle = @fopen($url, 'r');
        $result=array();       
        if ($handle) {
            $result = fgets($handle, 4096);
            fclose($handle);
            $allData = explode(',',$result); /* Get all the contents to an array */
            $dollarValue = $allData[1];
        }
       $response = array('status' => '1', 'data' => array('dollarValue' =>$dollarValue));
       echo json_encode($response);
       die;
    }
    /* Web service for ReOrder Screen */

public function actiongetReOrder() {
	
	$wb = new Webservice();
	$currMonth = date('F');
   	$sql = "SELECT s.detail_id,s.plan_id,s.month,s.year,s.product_id,p.product_id,p.status FROM subscription_plan_detail s , product p Where s.product_id = p.product_id  and p.status = 'Active' GROUP BY s.month,s.year ORDER BY  year desc, FIELD(MONTH,'Top Selling Wine 热销酒款','1月, January','2月, February','3月, March','4月, April','5月, May','6月, June','7月, July',' 8月, August','9月, September',' 10月, October','11月, November','12月, December')"; 	

        $row = $wb->getAllData($sql);
	$yearArray = array(); 	
	foreach($row as $key=>$val){
		$yearArray[$val['year']][] = $val;	
	}
	foreach($yearArray as &$val){
		$s = new MultiDimensionSort('month', MultiDimensionSort::DESCENDING);
		usort($val, array($s, 'cmp'));
	}
	$dataYr = array();
	$yearArray2nd = $yearArray;
	$data = array();	
	foreach($yearArray as $key=>&$val){
		$dataYr[$key][0] = $val[count($val) -1];
		unset($val[count($val) -1]);
		$tempVal = array();
		foreach($val as $k=>$v){
			$t = array();
			$t= @explode("月,",$v['month']);	
			$tempVal[$t[0]] = $v;
		}
		$val = $tempVal;
		krsort($val);
		foreach($val as $k=>$v){
			$dataYr[$key][] = $v;
		}
		$data = array_merge($data,$dataYr[$key]);
	} 
	if (count($data)) {
	$status = '1';
	 $msg = "";
    	 } else {
            $status = '0';
            $msg = 'No data found!';
        }
		
        $finalresponse = array('response' => array('status' => $status, 'Message' => $msg, 'data' => $data));
        echo json_encode($finalresponse);
        die;
    }
    

    /* Web service for ReOrder Detail Screen */

    public function actiongetReOrderDetail() { 
        $wb = new Webservice();
        $productArr = array();
        $favArr = array();
        $fields = $_REQUEST;  
        $langPf=(isset($_REQUEST["language_preference"]) && $_REQUEST["language_preference"]!=''?$_REQUEST["language_preference"]:"en");

        if (isset($fields["user_id"]) && $fields["user_id"] != "") {
            $favArr = $this->actiongetFavouriteProduct($fields["user_id"]);
        }
        
        $flag = true;
        if (!isset($fields['month']) || $fields['month'] == "") {
            $flag = false;
            $msg = "Plese select Month";
            $status="-2";
        }
        if (!isset($fields['year']) || $fields['year'] == "") {
            $flag = false;
            $msg = "Plese select Year";
             $status="-2";
        }
       
        if ($flag) {
                  $subsql = "select spd.product_id as product_id,p.* from subscription_plan_detail spd,product p 
                    where p.product_id=spd.product_id AND spd.month='" . $fields['month'] . "'
                    AND spd.year='" . $fields['year'] . "' GROUP BY p.product_id";
           
	    $subrow = $wb->getAllData($subsql);
            if ($subrow) {
                 $thumbpath = realpath(Yii::app()->basePath . '/../images/product/thumb/');
                $path = realpath(Yii::app()->basePath . '/../images/product/');
                $productthumbURL = Yii::app()->getBaseUrl(true) . '/images/product/thumb/';
                $productURL = Yii::app()->getBaseUrl(true) . '/images/product/';
                foreach ($subrow as $sk => $sv) {
		    $is_favourite = (count($favArr) > 0 && in_array($sv["product_id"], $favArr) ? "1" : "0");
                    $is_active = ($sv["status"] == 'Active' ) ? "1" : "0";

		    $productArr[] = array("product_id" => $sv["product_id"],
                        "product_name" => ($langPf=="cn" && $sv["product_name_cn"]!=""?$sv["product_name_cn"]:$sv["product_name"]),
                        "image" => (file_exists($path . "/" . $sv["image"]) && $sv["image"] != "" ? $productURL . $sv["image"] : ""),
                         "thumb_image" => (file_exists($thumbpath . "/" . $sv["image"]) && $sv["image"] != "" ? $productthumbURL . $sv["image"] : ""),
                       	"short_desc" => ($langPf=="cn" && $sv["short_desc_cn"]!=""?$sv["short_desc_cn"]:$sv["short_desc"]),
                        "long_desc" => ($langPf=="cn" && $sv["long_desc_cn"]!=""?$sv["long_desc_cn"]:$sv["long_desc"]),
                        "price" => $sv["price"],
                        "type" => ($langPf=="cn" && $sv["type_cn"]!=""?$sv["type_cn"]:$sv["type"]),
                        "varietal" => ($langPf=="cn" && $sv["varietal_cn"]!=""?$sv["varietal_cn"]:$sv["varietal"]),
                        "location" => ($langPf=="cn" && $sv["location_cn"]!=""?$sv["location_cn"]:$sv["location"]),
                        "country" => ($langPf=="cn" && $sv["country_cn"]!=""?$sv["country_cn"]:$sv["country"]),
                        "year" => $sv["year"],
                        "glassware" => ($langPf=="cn" && $sv["glassware_cn"]!=""?$sv["glassware_cn"]:$sv["glassware"]),
                        "flash_sale_product" => $sv["flash_sale_product"],
                        "product_type" => $sv["product_type"],
			"is_active" => $is_active,	
                        "is_favourite" => $is_favourite);

                    	$status = '1';
                    	$msg = "";
 			$finalresponse = array('response' => array('status' => $status, 'Message' => $msg, 'data' => $productArr));

                }
            } else {
                $status = '-1';
                $productArr = array('message'=>'No products data found!');
 		$finalresponse = array('response' => array('status' => $status,'data' => $productArr));
            }
        }
       
        echo json_encode($finalresponse);
        die;
    }

    /* Add wine to favourite list */

    public function actionsubscribedTo() {
        $wb = new Webservice();
        $fields = $_REQUEST;
        $cartTotal = 0;
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
            if (isset($fields['plan_id']) && $fields['plan_id'] != '') {
                $sql = "SELECT count(*) as tot FROM orders WHERE user_id = '" . $fields['user_id'] . "' and subscription_plan_id= '" . $fields['plan_id'] . "'";
                $row = $wb->getRowData($sql);
                if ($row['tot'] > 0) {
                    $sql1 = "SELECT count(*) as tot FROM orders WHERE user_id = '" . $fields['user_id'] . "' and order_status='in_cart'";
                    $row1 = $wb->getRowData($sql1);
                    $cartTotal = $row1['tot'];
                    $response = array('status' => '-1', 'data' => array('message' => 'You have already added this subscription plan to your cart'));
                } else {
                    $post = array();
                    $address_id = '0';
                    $addressRow = $wb->getRowData("select address_id from address where user_id='" . $fields["user_id"] . "' and is_shipping=1");
                    if ($addressRow) {
                        $address_id = $addressRow["address_id"];
                    }
                    $post['user_id'] = $fields['user_id'];
                    $post['subscription_plan_id'] = $fields['plan_id'];
                    $post['subscription_duration'] = $fields['duration'];
                    $post['order_total'] = $fields['order_total'];
                    $post['order_qty'] = '1';
                    $post['type'] = '1';
                    $post['address_id'] = $address_id;
                    $post['order_date'] = date('Y-m-d h:i:s');
                    $post['order_createdby'] = $fields['user_id'];
                    $response = $wb->addData($post, '', '', 1, "orders");
                    $sql1 = "SELECT count(*) as tot FROM orders WHERE user_id = '" . $fields['user_id'] . "' and order_status='in_cart'";
                    $row1 = $wb->getRowData($sql1);
                    $cartTotal = $row1['tot'];
                }
            } else {
                $response = array('status' => '-2', 'data' => array('message' => 'Plan Id is blank'));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id is blank'));
        }
        echo json_encode(array("response" => $response, 'cartTotal' => $cartTotal));
        die;
    }

    /* Web service to get Order History */

    public function actiongetOrderHistory() {
        $wb = new Webservice();
        $fields = $_REQUEST;
        $favArr = array();
        $langPf=(isset($_REQUEST["language_preference"]) && $_REQUEST["language_preference"]!=''?$_REQUEST["language_preference"]:"en");

        if (isset($fields["user_id"]) && $fields["user_id"] != "") {
            $favArr = $this->actiongetFavouriteProduct($fields["user_id"]);
        }
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
             $sql = "select o.order_id,o.order_total,o.order_date,o.payment_method,o.order_qty,o.address_id,
                    o.order_date,p.product_name,p.price,p.product_id,p.image,p.location,p.country,p.year,p.product_type,p.long_desc 
                    from res_orders o 
                    LEFT JOIN product p  ON p.product_id=o.product_id 
                    WHERE o.user_id='" . $fields['user_id'] . "' 
                    AND o.type=2";
            $row = $wb->getAllData($sql);
            $resArr = array();
            $path = realpath(Yii::app()->basePath . '/../images/product/');
            $thumbpath = realpath(Yii::app()->basePath . '/../images/product/thumb/');
            $imageURL = Yii::app()->getBaseUrl(true) . '/images/product/';
            $imagethumbURL = Yii::app()->getBaseUrl(true) . '/images/product/thumb/';
            if ($row) {
                $status = "1";
                $msg = "Data found!";
                $billingAddString = "";
                foreach ($row as $rk => $rv) {
                    $billingAddress = $wb->getRowData("SELECT * from address where user_id='" . $fields['user_id'] . "' 
                                                    and address_id='" . $rv["address_id"] . "'");
                    $addArr = array();
                    if ($billingAddress) {
                        $addArr = array("label" => $billingAddress["label"],
                            "first_name" => $billingAddress["first_name"],
                            "last_name" => $billingAddress["last_name"],
                            "company_name" => $billingAddress["company_name"],
                            "address" => $billingAddress["address"],
                            "city" => $billingAddress["city"],
                            "state" => $billingAddress["state"],
                            "country" => $billingAddress["country"],
                            "postcode" => $billingAddress["postcode"],
                            "phone" => $billingAddress["phone"],
                            "is_billing" => $billingAddress["is_billing"],
                            "is_shipping" => $billingAddress["is_shipping"]);
                        $billingAddString = $this->retAddressString($addArr);
                    }
                    $is_favourite = (count($favArr) > 0 && in_array($rv["product_id"], $favArr) ? "1" : "0");
                    $resArr[] = array(
                        "order_id" => $rv["order_id"],
                        "product_id" => $rv["product_id"],
                        "product_name" => ($langPf=="cn" && $rv["product_name_cn"]!=""?$rv["product_name_cn"]:$rv["product_name"]),
                        "price" => $rv["price"],
                        "order_date" => date("d/m/Y", strtotime($rv["order_date"])),
                        "order_total" => $rv["order_total"],
                        "product_qty" => $rv["order_qty"],
                        "location" => $rv["location"],
                        "country" => $rv["country"],
                        "year" => $rv["year"],
                        "order_qty" => $rv["order_qty"],
			"product_type" => $rv["product_type"],
			"long_desc" => $rv["long_desc"],
                        "price" => $rv["price"],
                        "payment_method" => $rv["payment_method"],
                        "is_favourite" => $is_favourite,
                        "image" => (file_exists($path . "/" . $rv["image"]) && $rv["image"] != "" ? $imageURL . $rv["image"] : ""),
                        "thumb_image" => (file_exists($thumbpath . "/" . $rv["image"]) && $rv["image"] != "" ? $imagethumbURL . $rv["image"] : ""),
                        "address" => $billingAddString);
                }
            } else {
                $status = '-1';
                $msg = 'No Orders found!';
            }
            $response = array('response' => array('status' => $status, 'Message' => $msg, 'data' => $resArr));
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id is blank'));
        }
        echo json_encode($response);
        die;
    }

    /* Create web service to get all Glassware products */

    public function actiongetGlassware() {
        $wb = new Webservice();
        $favArr = array();
        $fields = $_REQUEST;
        $langPf=(isset($_REQUEST["language_preference"]) && $_REQUEST["language_preference"]!=''?$_REQUEST["language_preference"]:"en");

        if (isset($fields["user_id"]) && $fields["user_id"] != "") {
            $favArr = $this->actiongetFavouriteProduct($fields["user_id"]);
        }
        // AND spd.plan_id='" . $fields['plan_id'] . "' 
        $subsql = "select * from product where product_type='glassware' and flash_sale_product='no'";
        $subrow = $wb->getAllData($subsql);
        if ($subrow) {
            $msg = "Data found!";
            $path = realpath(Yii::app()->basePath . '/../images/product/');
            $thumbpath = realpath(Yii::app()->basePath . '/../images/product/thumb/');
            $videopath = realpath(Yii::app()->basePath . '/../images/product/video/');
            $videoURL = Yii::app()->getBaseUrl(true) . '/images/product/video/';
            $productURL = Yii::app()->getBaseUrl(true) . '/images/product/';
            $productthumbURL = Yii::app()->getBaseUrl(true) . '/images/product/thumb/';
            foreach ($subrow as $sk => $sv) {
                $is_favourite = (count($favArr) > 0 && in_array($sv["product_id"], $favArr) ? "1" : "0");
		$is_active = ($sv["status"] == 'Active' ) ? "1" : "0";
                
		$productArr[] = array("product_id" => $sv["product_id"],
                    "product_name" => ($langPf=="cn" && $sv["product_name_cn"]!=""?$sv["product_name_cn"]:$sv["product_name"]),
                    "image" => (file_exists($path . "/" . $sv["image"]) && $sv["image"] != "" ? $productURL . $sv["image"] : ""),
                    "thumb_image" => (file_exists($thumbpath . "/" . $sv["image"]) && $sv["image"] != "" ? $productthumbURL . $sv["image"] : ""),
                    "video" => (file_exists($videopath . "/" . $sv["video"]) && $sv["video"] != "" ? $videoURL . $sv["video"] : ""),
                    "short_desc" => ($langPf=="cn" && $sv["short_desc_cn"]!=""?$sv["short_desc_cn"]:$sv["short_desc"]),
                    "long_desc" => ($langPf=="cn" && $sv["long_desc_cn"]!=""?$sv["long_desc_cn"]:$sv["long_desc"]),
                    "price" => $sv["price"],
                    "type" => ($langPf=="cn" && $sv["type_cn"]!=""?$sv["type_cn"]:$sv["type"]),
                    "varietal" => ($langPf=="cn" && $sv["varietal_cn"]!=""?$sv["varietal_cn"]:$sv["varietal"]),
                    "location" => ($langPf=="cn" && $sv["location_cn"]!=""?$sv["location_cn"]:$sv["location"]),
                    "country" => ($langPf=="cn" && $sv["country_cn"]!=""?$sv["country_cn"]:$sv["country"]),
                    "year" => $sv["year"],
                    "glassware" => ($langPf=="cn" && $sv["glassware_cn"]!=""?$sv["glassware_cn"]:$sv["glassware"]),
                    "flash_sale_product" => $sv["flash_sale_product"],
                    "product_type" => $sv["product_type"],
		    "is_active" => $is_active,
                    "is_favourite" => $is_favourite);
                $status = '1';
            }
        } else {
            $status = '-1';
            $msg = 'No products data found!';
        }
        $response = array('status' => '0', 'data' => $productArr, "message" => $msg);
        echo json_encode($response);
        die;
    }

    /* Create web service to get all Flash sale products */

    public function actiongetFlashSale() {
	$wb = new Webservice();
        $favArr = array();
        $fields = $_REQUEST;
        if (isset($fields["user_id"]) && $fields["user_id"] != "") {
            $favArr = $this->actiongetFavouriteProduct($fields["user_id"]);
        }
        $langPf=(isset($_REQUEST["language_preference"]) && $_REQUEST["language_preference"]!=''?$_REQUEST["language_preference"]:"en");
        $productArr=array();
        $now = date("Y-m-d H:i:s");
        // AND spd.plan_id='" . $fields['plan_id'] . "' 
        $subsql = "select fs.*,p.* from  flash_sale fs
                    LEFT JOIN product p ON p.product_id=fs.product_id
                    where fs.sale_start_from <= '".$now."' AND fs.sale_end >= '".$now."' AND fs.status = 'active'";
      
	$subrow = $wb->getAllData($subsql);
         if ($subrow) {
            $msg = "Data found!";
            $path = realpath(Yii::app()->basePath . '/../images/product/');
            $productURL = Yii::app()->getBaseUrl(true) . '/images/product/';
            $thumbpath = realpath(Yii::app()->basePath . '/../images/product/thumb/');
            $productthumbURL = Yii::app()->getBaseUrl(true) . '/images/product/thumb/';
            foreach ($subrow as $sk => $sv) {
                $is_favourite = (count($favArr) > 0 && in_array($sv["product_id"], $favArr) ? "1" : "0");
                $productArr[] = array("product_id" => $sv["product_id"],
                    "flash_sale_id" => $sv['flash_sale_id'],
                    "flash_sale_title" => $sv['title'],
                    "sale_start_from" => $sv['sale_start_from'],
                    "sale_end" => $sv['sale_end'],
                    "product_name" => ($langPf=="cn" && $sv["product_name_cn"]!=""?$sv["product_name_cn"]:$sv["product_name"]),
                    "image" => (file_exists($path . "/" . $sv["image"]) && $sv["image"] != "" ? $productURL . $sv["image"] : ""),
                    "thumb_image" => (file_exists($thumbpath . "/" . $sv["image"]) && $sv["image"] != "" ? $productthumbURL . $sv["image"] : ""),
                    "short_desc" => ($langPf=="cn" && $sv["short_desc_cn"]!=""?$sv["short_desc_cn"]:$sv["short_desc"]),
                    "long_desc" => ($langPf=="cn" && $sv["long_desc_cn"]!=""?$sv["long_desc_cn"]:$sv["long_desc"]),
                    "price" => $sv["price"],
                    "type" => ($langPf=="cn" && $sv["type_cn"]!=""?$sv["type_cn"]:$sv["type"]),
                    "varietal" => ($langPf=="cn" && $sv["varietal_cn"]!=""?$sv["varietal_cn"]:$sv["varietal"]),
                    "location" => ($langPf=="cn" && $sv["location_cn"]!=""?$sv["location_cn"]:$sv["location"]),
                    "country" => ($langPf=="cn" && $sv["country_cn"]!=""?$sv["country_cn"]:$sv["country"]),
                    "year" => $sv["year"],
                    "glassware" => ($langPf=="cn" && $sv["glassware_cn"]!=""?$sv["glassware_cn"]:$sv["glassware"]),
                    "flash_sale_product" => $sv["flash_sale_product"],
                    "product_type" => $sv["product_type"],
                    "is_favourite" => $is_favourite);
                $status = '1';

            }
        } else {
            $status = '-1';
            $msg = 'No products data found!';
        }
        $response = array('status' =>$status, 'data' => $productArr,"message"=>$msg);
        echo json_encode($response);
        die;
    }
    /* Add wines to shopping list */

    public function actionaddWinesandGlasswareToCart() {
        $wb = new Webservice();
        $fields = $_REQUEST;
        $cartTotal = 0;
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
            if (isset($fields['product_id']) && $fields['product_id'] != '') {
                $sql = "SELECT count(*) as tot FROM orders WHERE user_id = '".$fields['user_id']."' and product_id='" . $fields['product_id'] . "'";
                $row = $wb->getRowData($sql);
                if ($row['tot'] > 0) {
                    $sql1 = "SELECT count(*) as tot FROM orders WHERE user_id = '" . $fields['user_id'] . "' and order_status='in_cart'";
                    $row1 = $wb->getRowData($sql1);
                    $cartTotal = $row1['tot'];
                    $post=array();
                    $post['order_qty'] = $fields['order_qty'];
                    $where = " where user_id = '" . $fields['user_id'] . "' and product_id= '" . $fields['product_id'] . "'";
                    $upSql="update orders set order_total='".$fields['order_total']."',order_qty=order_qty+".$post["order_qty"]." $where";
                    $response = $wb->executeQuery($upSql);
                    $response = array('status' => '2', 'data' => array('message' => 'Product Qty is updated in your cart!'));
                } else {
                    $post = array();
                    $address_id = '0';
                    $addressRow = $wb->getRowData("select address_id from address where user_id='" . $fields["user_id"] . "' and is_shipping=1");
                    if ($addressRow) {
                        $address_id = $addressRow["address_id"];
                    }
                    $post['user_id'] = $fields['user_id'];
                    $post['product_id'] = $fields['product_id'];
                    $post['order_total'] = $fields['order_total'];
                    $post['order_qty'] = $fields['order_qty'];
                    $post['type'] = '2';
                    $post['address_id'] = $address_id;
                    $post['order_date'] = date('Y-m-d h:i:s');
                    $post['order_createdby'] = $fields['user_id'];
                    $post['flash_sale_id'] = (isset($fields['flash_sale_id']) && $fields['flash_sale_id']!=""?$fields['flash_sale_id']:0);
                    $response = $wb->addData($post, '', '', 1, "orders");
                    $sql1 = "SELECT count(*) as tot FROM orders WHERE user_id = '" . $fields['user_id'] . "' and order_status='in_cart'";
                    $row1 = $wb->getRowData($sql1);
                    $cartTotal = $row1['tot'];
                }
            } else {
                $response = array('status' => '-2', 'data' => array('message' => 'Product Id is blank'));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id is blank'));
        }
        echo json_encode(array("response" => $response, 'cartTotal' => $cartTotal));
        die;
    }
    /* update product quentity in shopping list */

    public function actionupdateProductQty() {
        $wb = new Webservice();
        $fields = $_REQUEST;
        $cartTotal = 0;
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
            if (isset($fields['product_id']) && $fields['product_id'] != '') {
                $sql = "SELECT count(*) as tot FROM orders WHERE user_id = '".$fields['user_id']."' and product_id='" . $fields['product_id'] . "'";
                $row = $wb->getRowData($sql);
                if ($row['tot'] > 0) {
                    $sql1 = "SELECT count(*) as tot FROM orders WHERE user_id = '" . $fields['user_id'] . "' and order_status='in_cart'";
                    $row1 = $wb->getRowData($sql1);
                    $cartTotal = $row1['tot'];
                    $post=array();
                    $post['order_qty'] = $fields['qty'];
                    $where = " where user_id = '" . $fields['user_id'] . "' and product_id= '" . $fields['product_id'] . "'";
                    $upSql="update orders set order_total='".$fields['order_total']."',order_qty='".$fields['qty']."' $where";
                    $response = $wb->executeQuery($upSql);
                    $response = array('status' => '1', 'data' => array('message' => 'Product Qty is updated in your cart!'));
                } else {
                    $response = array('status' => '-1', 'data' => array('message' => 'Product has been removed from your cart!'));
                }
            } else {
                $response = array('status' => '-2', 'data' => array('message' => 'Product Id is blank'));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id is blank'));
        }
        echo json_encode(array("response" => $response, 'cartTotal' => $cartTotal));
        die;
    }
    /* remove product from shopping list */

    public function actionremoveProductFromCart() {
        $wb = new Webservice();
        $fields = $_REQUEST;
        $cartTotal = 0;
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
            if (isset($fields['product_id']) && $fields['product_id'] != '') {
                $sql = "SELECT count(*) as tot FROM orders WHERE user_id = '".$fields['user_id']."' and product_id='" . $fields['product_id'] . "'";
                $row = $wb->getRowData($sql);
                if ($row['tot'] > 0) {
                    $post=array();
                    $where = " where user_id = '" . $fields['user_id'] . "' and product_id= '" . $fields['product_id'] . "'";
                    $upSql="delete from orders $where";
                    $response = $wb->executeQuery($upSql);
                    $sql1 = "SELECT count(*) as tot FROM orders WHERE user_id = '" . $fields['user_id'] . "' and order_status='in_cart'";
                    $row1 = $wb->getRowData($sql1);
                    $cartTotal = $row1['tot'];
                    $response = array('status' => '1', 'data' => array('message' => 'Product has been removed from your cart!'));
                } else {
                    $response = array('status' => '-1', 'data' => array('message' => 'Product has already been removed from your cart!'));
                }
            } else {
                $response = array('status' => '-2', 'data' => array('message' => 'Product Id is blank'));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id is blank'));
        }
        echo json_encode(array("response" => $response, 'cartTotal' => $cartTotal));
        die;
    }
    /* remove item from shopping list */

    public function actionremoveFromCart() {
        $wb = new Webservice();
        $fields = $_REQUEST;
        $cartTotal = 0;
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
            if (isset($fields['order_id']) && $fields['order_id'] != '') {
                $sql = "SELECT count(*) as tot FROM orders WHERE user_id = '".$fields['user_id']."' and order_id='" . $fields['order_id'] . "'";
                $row = $wb->getRowData($sql);
                if ($row['tot'] > 0) {
                    $post=array();
                    $where = " where user_id = '" . $fields['user_id'] . "' and order_id= '" . $fields['order_id'] . "'";
                    $upSql="delete from orders $where";
                    $response = $wb->executeQuery($upSql);
                    
                    $response = array('status' => '1', 'data' => array('message' => 'Product has been removed from your cart!'));
                } else {
                    $response = array('status' => '-1', 'data' => array('message' => 'Product has already been removed from your cart!'));
                }
            } else {
                $response = array('status' => '-2', 'data' => array('message' => 'Order Id is blank'));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id is blank'));
        }
        $sql1 = "SELECT count(*) as tot FROM orders WHERE user_id = '" . $fields['user_id'] . "' and order_status='in_cart'";
        $row1 = $wb->getRowData($sql1);
        $cartTotal = $row1['tot'];
        echo json_encode(array("response" => $response, 'cartTotal' => $cartTotal));
        die;
    }
    /* set shipping address for product from shopping list */

    public function actionsetShippingAddressinCart() {
        $wb = new Webservice();
        $fields = $_REQUEST;
        $cartTotal = 0;
        if (isset($fields['order_id']) && $fields['order_id'] != '') {
            if (isset($fields['address_id']) && $fields['address_id'] != '') {
                $post=array();
                $where = " where order_id IN(".$fields['order_id'].")";
                $upSql="update orders set address_id = '".$fields['address_id']."' $where";
                $response = $wb->executeQuery($upSql);
                $response = array('status' => '1', 'data' => array('message' => 'Shipping address has been added to your cart product!'));
             } else {
                $response = array('status' => '-2', 'data' => array('message' => 'address id is blank'));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'order_id is blank'));
        }
        echo json_encode(array("response" => $response));
        die;
    }
    /* Get Survey data web service */
    public function actiongetSurveyData(){
        $wb = new Webservice();
        $optionArr = array();
        $fields = $_REQUEST;
        if (isset($fields['survey_id']) && $fields['survey_id'] != '') {
            $survey_id=$fields["survey_id"];
            $now = date("Y-m-d h:i:s");
            // AND spd.plan_id='" . $fields['plan_id'] . "' 
            $subsql = "SELECT s.survey_name,sq.*,so.* FROM survey s 
                      LEFT JOIN survey_question sq ON s.survey_id =sq.survey_id
                      LEFT JOIN survey_option so ON so.survey_question_id=sq.survey_question_id
                      WHERE s.survey_id='".$survey_id."' ORDER BY so.survey_option_id ASC";
            $subrow = $wb->getAllData($subsql);
            $surveyArr=array();
            if ($subrow) {
                $msg = "Data found!";
                foreach ($subrow as $sk => $sv) {
                    $question=$sv["question"];
                    $survey_name=$sv["survey_name"];
                    $question=$sv["question"];
                    $optionArr[$sv["survey_question_id"]]["question"]=$sv["question"];
                    $optionArr[$sv["survey_question_id"]]["options"][] = array("survey_option_id" => $sv["survey_option_id"],
                                        "survey_question_id" => $sv["survey_question_id"],
                                        "option" => $sv["option"]);
                     $status = '0';
                }
                $surveyArr=array("survey_id" => $fields["survey_id"],
                                 "survey_name" => $survey_name,
                                 "optionsArr"=>$optionArr);
            } else {
                $status = '-1';
                $msg = 'No survey data found!';
            }
        } else {
            $status = '1';
            $msg = 'survey_id is blank!';
        }
        $response = array('status' => $status, 'data' => $surveyArr,"message"=>$msg);
        echo json_encode($response);
        die;    
        
    }
    /* service to add survey result into database*/
    public function actionaddSurveyOptions(){
        $wb = new Webservice();
        $fields = $_REQUEST;
        $userid = $fields['user_id'];
        //$old_password = $fields['old_password'];
        $question_id = $fields['question_id'];
        $answer_id = $fields['answer_id'];
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
            //echo $fields['userid'];die;
            if (!empty($answer_id)) {
                $expQuestion=explode(",",$question_id);
                $expAnswer=explode(",",$answer_id);
                foreach($expQuestion as $qk=>$qv){
                    $sql="INSERT INTO `survey_result` 
                        (`user_id`, `survey_question_id`, `survey_option_id`) 
                         VALUES ('".$fields["user_id"]."', '".$qv."', '".$expAnswer[$qk]."')";
                     $wb->executeQuery($sql);
                }
                $response = array('status' => '1', 'data' => array('message' => 'You have submitted survey successfully!'));                
            } else {
                $response = array('status' => '-1', 'data' => array('message' => 'Please fill atleast one answer to submit survey'));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id is  blank'));
        }
        echo json_encode(array("response" => $response));
        die;
        
    }
     /*
     * change User Password
     */

    public function actionchangePassword() {
        $wb = new Webservice();
        $fields = $_REQUEST;
        $userid = $fields['user_id'];
        //$old_password = $fields['old_password'];
        $new_password = $fields['password'];
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
            //echo $fields['userid'];die;
            if (!empty($new_password)) {
                $post = array();
                $post['password'] = $new_password;
                $where = "user_id = $userid";
                $response = $wb->updateData($post, '', '', $where, "users");
                if ($response['status'] == '1') {
                    $response = array('status' => '1', 'data' => array('message' => 'Password Changed Successfully'));
                } else {
                    $response = array('status' => '-1', 'data' => array('message' => 'There is some error while changing password..Please try again!'));
                }
            } else {
                $response = array('status' => '-2', 'data' => array('message' => 'Password is blank'));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id is  blank'));
        }
        echo json_encode(array("response" => $response));
        die;
    }

    /*
     * View Profile detail
     */

    public function actionviewProfile() {
        $wb = new Webservice();
        $fields = $_REQUEST;
        // Check user id
        if (isset($fields['userid']) && $fields['userid'] != '') {
            $userid = $fields['userid'];
            //chack user id exist or not
            $sql = "SELECT userid FROM user_detail WHERE userid = '$userid'";
            $row = $wb->getRowData($sql);
            if ($row) {
                $post = array();
                $post["table"] = 'user_detail';
                $post["fields"] = 'userdetailid,userid,firstname,lastname,email,mobile';
                $post["beforeWhere"] = '';
                $post["afterWhere"] = 'userid = "' . $fields['userid'] . '"';
                $post["r_p_p"] = '';
                $post["start"] = '';
                $response = $wb->fetchData($post);
                //print_r($response['status']);die;
            } else {
                $response = array('status' => '-3', 'data' => array('message' => 'userid not exist'));
            }
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'userid blank'));
        }
        echo json_encode(array("response" => $response));
        die;
    }
   
    /*
     * @desc: send mail to attorney 
     */

    public function sendmail($to,$from, $orderno,$firstname, $lastname) {
        $body = '<body>
                    <p>Dear '.$firstname." ".$lastname . '</p>
                    <p>Thank you for shopping from Sigwine App</p>
                    <p>Your order '.$orderno.' has been successfully delivered at the mentioned shipping address.</p>
                    <p>Thanks,</p>
                    <p>Sigwine Team</p>                        
                </body>';

        $headers = "From: SigWine <$from>\r\n";
        $headers .= "Content-type: text/html\r\n";
        $subject = "Your Order ".$orderno." has been Delivered";
        // now lets send the email.
        if (mail($to, $subject, $body, $headers)) {
            return true;
        } else {
            return false;
        }
    }
    static public function executePayment2( $refresh_token, $correlation_id, $fee_amount )
    {
        $data = array(
            "amount" => array(
                "currency" => "USD",
                "total" => $fee_amount
            ),
            "is_final_capture" => "true"
        );

        $data_string = json_encode($data);
        $ch = curl_init( "https://api.paypal.com/v1/payments/authorization/4TD55050SV609544L/capture" );        

        curl_setopt_array( $ch, array(
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data_string),
                    'Authorization: Bearer ' . $refresh_token,      //Need to set $refresh_token
                    'Paypal-Application-Correlation-Id: ' . $correlation_id      //Need to set $correlation_id
                ),
                CURLOPT_POSTFIELDS => $data_string,
                CURLOPT_RETURNTRANSFER => true
            )
        );

        $result = curl_exec( $ch );   //Make it all happen and store response   
    }

      public function actionupdateOrder() {
	$wb = new Webservice();
	$fields = $_REQUEST;
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
	// check for order id and order total
	 // Insert into res_order_total table
            $user_id=$fields["user_id"];
            $order_total=$fields["order_total"];
            $payment_status=$fields["payment_status"];
            $order_status="Pending";
            $payment_response=$fields["payment_response"];
            $payment_method=$fields["payment_method"];
            $order_createdby=$fields["user_id"];
	    $discount_amt =  $fields["discount_amt"];	
            $order_createdon=date("Y-m-d h;i:s");
	    
            $sql="INSERT INTO res_order_total 
                    (user_id,order_total,payment_status,order_status,payment_response,order_createdby,order_createdon) 
                    values ('".$user_id."','".$order_total."','".$payment_status."','".$order_status."',
                   '".$payment_response."','".$order_createdby."','".$order_createdon."')";
            $last_id=$wb->executeQuery($sql,'db',2);
            $order_ids=explode(",",$fields["order_ids"]);
            $order_total_id=$last_id;
            foreach ($order_ids as $k=>$v){   
		
                // Insert into res_orders
                    $ins="INSERT INTO `res_orders` 
                        (`order_total_id`, `user_id`, `subscription_plan_id`, 
                        `subscription_duration`, `order_total`, `product_id`, `order_qty`, 
                        `type`, `order_status`, `order_date`, `order_createdby`, `billing_address_id`, 
                        `address_id`, `payment_method`) 
                        SELECT '".$order_total_id."',`user_id`, `subscription_plan_id`,
                            `subscription_duration`, `order_total`,`product_id`, `order_qty`,
                            `type`, `order_status`, `order_date`, 
                            `order_createdby`, `billing_address_id`, `address_id`, 
                            '".$payment_method."' FROM orders where order_id='".$v."'";   
               $wb->executeQuery($ins);
               $wb->executeQuery("DELETE FROM orders where order_id='".$v."'");
	       //echo "DELETE FROM orders where order_id='".$v."'";
	       //exit;		
            }
	    if ((!isset($fields['order_ids']) || $fields['order_ids'] == '') || (!isset($fields['order_total']) || ($fields['order_total'] < 0 || $fields['order_total'] == ''))) {  
		$response = array('status' => '-1', 'data' => array('message' => 'Order Details are Incorrect.')); 
	    }
	    else
	    {	
		$res= $wb->sendOrderEmail($order_total_id,$fields['user_id'],$discount_amt);
		$response = array('status' => '1', 'data' => array('message' => 'Order placed Successfully'));
	    }
	}
	else {
	    
	    $response = array('status' => '0', 'data' => array('message' => 'userid blank'));
        }
        echo json_encode($response);
        die;
    }
    
    public function init() { 
        //echo "in webservice";
        // die;
    }

    public function actionTestinfo()
    {
        // echo phpinfo();
        //echo $result;

        /*         * ************************* */
        //$bedge = intval($bedge + 1);
        $url = 'https://android.googleapis.com/gcm/send';

        $messages = array(
            "data" => "This is test message.",
            "count" => 1
        );
        //$messages = array("data" => $message);

        $registatoin_ids = "APA91bHZGXtdu5GiBOiHgllGPu0Kw9J3PjnKtbqpj_IRZVxjKlDFHAHVwpygqvUcZIr6GkIJpuBiqUSH3BwEnMtiTss5SSQlealddIY6QP8-OqDWr9VY2cg";
        $fields = array(
            'registration_ids' => $registatoin_ids,
            'data' => $messages,
        );

        $headers = array(
            'Authorization: key=AIzaSyBbLx8Uwy0xhnsoKzA4aiAmyRJDWgtyfrg',
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

      /* Web service for Get Discounted Shopping Cart */

    public function actiongetDiscountedShoppingCart() {
        $wb = new Webservice();
        $fields = $_REQUEST;

	$discountAmt = 0;
        $discountPerc = 0;
        if (isset($fields['user_id']) && $fields['user_id'] != '') {
           // Get All Order Count Form Order table for User
            
           $sqlallOrders = "Select count(*) As numofrows from res_order_total  where user_id = '". $fields['user_id'] ."' AND payment_status IN('Approved','Pending')";
	
            $CountOrders = $wb->getAllData($sqlallOrders);
            //print_r($CountOrders[0]['numofrows']);
            // Get Discount Amt For Order
            if($CountOrders[0]['numofrows'] == '0')
            {
                $sqlgetDiscount = "Select discount_perc from discount where status = 'Active'";
                  $rowDiscount = $wb->getAllData($sqlgetDiscount);
                  
                if($rowDiscount)
                  $discountPerc = $rowDiscount[0]['discount_perc'];
					            
		}  
            // Get Incart Items and Orders also
            $sql = "SELECT sp.*,o.subscription_duration,o.order_total,o.order_id,o.order_qty, o.order_date,o.address_id
                    FROM `orders`  o, `subscription_plan`  sp 
                    WHERE sp.plan_id=o.subscription_plan_id AND o.user_id='" . $fields['user_id'] . "' 
                    AND o.order_status ='in_cart' AND o.type=1";
            $row = $wb->getAllData($sql);

	    $resArr = array();
            $resArrS = array();
            $path = realpath(Yii::app()->basePath . '/../images/subscription/');
            $thumbpath = realpath(Yii::app()->basePath . '/../images/subscription/thumb/');
            $imageURL = Yii::app()->getBaseUrl(true) . '/images/subscription/';
            $imagethumbURL = Yii::app()->getBaseUrl(true) . '/images/subscription/thumb/';
            $langPf=(isset($_REQUEST["language_preference"]) && $_REQUEST["language_preference"]!=''?$_REQUEST["language_preference"]:"en");

            if ($row) {
                $status = "1";
                $msg = "Data found!";
                $billingAddString = "";
                foreach ($row as $rk => $rv) {
                  
                  if($discountPerc != '0')
                      $discountAmt =  (($rv["order_qty"] * $rv["price"]) * $discountPerc) / 100;
                            
                  $billingAddString = "";
                  $billingAddress = $wb->getRowData("SELECT * from address where user_id='" . $fields['user_id'] . "' 
                                                     and address_id='" . $rv["address_id"] . "'");
                    $addArr = array();
                    if ($billingAddress) {
                        $addArr = array("label" => $billingAddress["label"],
                            "address_id" => $billingAddress["address_id"],
                            "first_name" => $billingAddress["first_name"],
                            "last_name" => $billingAddress["last_name"],
                            "company_name" => $billingAddress["company_name"],
                            "address" => $billingAddress["address"],
                            "city" => $billingAddress["city"],
                            "state" => $billingAddress["state"],
                            "country" => $billingAddress["country"],
                            "postcode" => $billingAddress["postcode"],
                            "phone" => $billingAddress["phone"],
                            "is_billing" => $billingAddress["is_billing"],
                            "is_shipping" => $billingAddress["is_shipping"]);
                        $billingAddString = $this->retAddressString($addArr);
                    }
                    $resArrS[] = array(
                        "order_id" => $rv["order_id"],
                        "plan_id" => $rv["plan_id"],
                        "plan_name" => ($langPf=="cn" && $rv["plan_name_cn"]!=""?$rv["plan_name_cn"]:$rv["plan_name"]),
                        "plan_type" => $rv["plan_type"],
                        "subscription_duration" => $rv["subscription_duration"],
                        //"order_total" => $rv["subscription_duration"] * $rv["price"],
			"order_total" => $rv["order_total"],
                        "order_discount" => $discountAmt,
                        "order_qty" => $rv["order_qty"],
                        "order_date" => $rv["order_date"],
                        //"price" => $rv["subscription_duration"] * $rv["price"],
			"price" => round($rv["order_total"] / $rv["subscription_duration"], 2),
                        "description" => ($langPf=="cn" && $rv["description_cn"]!=""?$rv["description_cn"]:$rv["description"]),
                        "image" => (file_exists($path . "/" . $rv["image"]) && $rv["image"] != "" ? $imageURL . $rv["image"] : ""),
                        "thumb_image" => (file_exists($thumbpath . "/" . $rv["image"]) && $rv["image"] != "" ? $imagethumbURL . $rv["image"] : ""),
                        "address_id" => $billingAddress["address_id"],
                        "address" => $billingAddString);
                }
            }else{
                $resArrS=array();
            }
            /* Get Incart product info*/
            $sql = "select p.product_id,o.order_id,o.order_total,o.order_date,o.payment_method,o.order_qty,o.address_id,o.order_date,
                    p.product_name,p.price,p.image,o.flash_sale_id,p.product_name_cn,p.status  from orders o 
                     LEFT JOIN product p  ON p.product_id=o.product_id 
                    WHERE o.user_id='" . $fields['user_id'] . "' 
                    AND o.order_status='in_cart' AND o.type=2";
            $row = $wb->getAllData($sql);
            
            $path = realpath(Yii::app()->basePath . '/../images/product/');
            $thumbpath = realpath(Yii::app()->basePath . '/../images/product/thumb/');
            $imageURL = Yii::app()->getBaseUrl(true) . '/images/product/';
            $imagethumbURL = Yii::app()->getBaseUrl(true) . '/images/product/thumb/';
            if ($row) {
                $status = "1";
                $msg = "Data found!";
                $billingAddString = "";
                foreach ($row as $rk => $rv) {
		    
                     if($discountPerc != '0')
                        $discountAmt =  (($rv["order_qty"] * $rv["price"]) * $discountPerc) / 100;
                            
                    
		    $billingAddString = "";
                    $addData=true;
                    $now=date("Y-m-d h:i:s");

                    if($rv["flash_sale_id"]!='0'){
                        // check 
                        $subsql = "select fs.* from  flash_sale fs
                                   where fs.sale_start_from<='".$now."' and fs.sale_end>='".$now."' 
                                  and fs.flash_sale_id='".$rv["flash_sale_id"]."'";
                        $subrow = $wb->getRowData($subsql);
                        if(!$subrow){
                             $addData=false;
                             $wb->executeQuery("Delete from orders where order_id='".$rv["order_id"]."'");
                        }
                    }
		   /* Delete product from order if product is inactive
		      Modified date : 20/4/2016	  */ 
 		    if($rv["status"]=='InActive'){
			        
			// Delete Product form order table
			$addData=false;
                 	$wb->executeQuery("Delete from orders where order_id='".$rv["order_id"]."'");                   
                    }
                    if($addData){
                        $billingAddress = $wb->getRowData("SELECT * from address where user_id='" . $fields['user_id'] . "' 
                                                        and address_id='" . $rv["address_id"] . "'");
                        $addArr = array();
                        $billingAddString="";
                        if ($billingAddress) {
                            $addArr = array("label" => $billingAddress["label"],
                                "address_id" => $billingAddress["address_id"],
                                "first_name" => $billingAddress["first_name"],
                                "last_name" => $billingAddress["last_name"],
                                "company_name" => $billingAddress["company_name"],
                                "address" => $billingAddress["address"],
                                "city" => $billingAddress["city"],
                                "state" => $billingAddress["state"],
                                "country" => $billingAddress["country"],
                                "postcode" => $billingAddress["postcode"],
                                "phone" => $billingAddress["phone"],
                                "is_billing" => $billingAddress["is_billing"],
                                "is_shipping" => $billingAddress["is_shipping"]);
                            $billingAddString = $this->retAddressString($addArr);
                        }
                        $resArr[] = array(
                            "order_id" => $rv["order_id"],
                            "product_id" => $rv["product_id"],
                            "product_name" => ($langPf=="cn" && $rv["product_name_cn"]!=""?$rv["product_name_cn"]:$rv["product_name"]),
                            "price" => $rv["price"],
                            "order_date" => date("d/m/Y", strtotime($rv["order_date"])),
                            "order_total" => ($rv["order_qty"] * $rv["price"]),
                            "order_discount" => $discountAmt,
                            "order_qty" => $rv["order_qty"],
                            "price" => $rv["price"],
                            "payment_method" => $rv["payment_method"],
                            "image" => (file_exists($path . "/" . $rv["image"]) && $rv["image"] != "" ? $imageURL . $rv["image"] : ""),
                            "thumb_image" => (file_exists($thumbpath . "/" . $rv["image"]) && $rv["image"] != "" ? $imagethumbURL . $rv["image"] : ""),
                            "address_id" => $billingAddress["address_id"],
                            "address" => $billingAddString);
                    }
                }
            }else{
                $resArr=array();
            }
            $dollarValue=0;
            $cartTotal=count($resArrS)+count($resArr);
            if(count($resArrS)==0 && count($resArr)==0){
                $status="-2";
                $msg="Sorry! Your shopping cart is empty.";
            }else{
                /*$from   = 'CNY';
                $to     = 'USD';
                $url = 'http://finance.yahoo.com/d/quotes.csv?e=.csv&f=sl1d1t1&s='. $from . $to .'=X';
                $handle = @fopen($url, 'r');
                $result=array();       
                if ($handle) {
                    $result = fgets($handle, 4096);
                    fclose($handle);
                    $allData = explode(',',$result); 
                    $dollarValue = $allData[1];
                }*/
            }
           $response = array('response' => array('status' => $status, 'Message' => $msg, 'subsciption' => $resArrS,'product'=>$resArr,"dollarValue"=>$dollarValue,"cartTotal"=>$cartTotal,"discountpercentage"=>$discountPerc)); 
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id is blank'));
        }
        
        echo json_encode($response);
        die;
}

	public function actiongetDiscountedShoppingCart2() {
        $wb = new Webservice();
        $fields = $_REQUEST;
	
	
	$discountAmt = 0;
        $discountPerc = 0;
	$NetDiscount = 0;

        if (isset($fields['user_id']) && $fields['user_id'] != '') {
           // Get All Order Count Form Order table for User
            
           $sqlallOrders = "Select count(*) As numofrows from res_order_total  where user_id = '". $fields['user_id'] ."' AND payment_status IN('Approved','Pending')";
	
            $CountOrders = $wb->getAllData($sqlallOrders);
            //print_r($CountOrders[0]['numofrows']);
            // Get Discount Amt For Order
            if($CountOrders[0]['numofrows'] == '0')
            {
                $sqlgetDiscount = "Select discount_perc from discount where status = 'Active'";
                  $rowDiscount = $wb->getAllData($sqlgetDiscount);
                  
                if($rowDiscount)
                  $discountPerc = $rowDiscount[0]['discount_perc'];
					            
	    }  
            // Get Incart Items and Orders also
            $sql = "SELECT sp.*,o.subscription_duration,o.order_total,o.order_id,o.order_qty, o.order_date,o.address_id
                    FROM `orders`  o, `subscription_plan`  sp 
                    WHERE sp.plan_id=o.subscription_plan_id AND o.user_id='" . $fields['user_id'] . "' 
                    AND o.order_status ='in_cart' AND o.type=1";
            $row = $wb->getAllData($sql);

	    $resArr = array();
            $resArrS = array();
            $path = realpath(Yii::app()->basePath . '/../images/subscription/');
            $thumbpath = realpath(Yii::app()->basePath . '/../images/subscription/thumb/');
            $imageURL = Yii::app()->getBaseUrl(true) . '/images/subscription/';
            $imagethumbURL = Yii::app()->getBaseUrl(true) . '/images/subscription/thumb/';
            $langPf=(isset($_REQUEST["language_preference"]) && $_REQUEST["language_preference"]!=''?$_REQUEST["language_preference"]:"en");

            if ($row) {
                $status = "1";
                $msg = "Data found!";
                $billingAddString = "";
                foreach ($row as $rk => $rv) {
                  
                  if($discountPerc != '0')
                      $discountAmt =  (($rv["order_qty"] * $rv["price"]) * $discountPerc) / 100;
                            
                  $billingAddString = "";
                  $billingAddress = $wb->getRowData("SELECT * from address where user_id='" . $fields['user_id'] . "' 
                                                     and address_id='" . $rv["address_id"] . "'");
                    $addArr = array();
                    if ($billingAddress) {
                        $addArr = array("label" => $billingAddress["label"],
                            "address_id" => $billingAddress["address_id"],
                            "first_name" => $billingAddress["first_name"],
                            "last_name" => $billingAddress["last_name"],
                            "company_name" => $billingAddress["company_name"],
                            "address" => $billingAddress["address"],
                            "city" => $billingAddress["city"],
                            "state" => $billingAddress["state"],
                            "country" => $billingAddress["country"],
                            "postcode" => $billingAddress["postcode"],
                            "phone" => $billingAddress["phone"],
                            "is_billing" => $billingAddress["is_billing"],
                            "is_shipping" => $billingAddress["is_shipping"]);
                        $billingAddString = $this->retAddressString($addArr);
                    }
                    $resArrS[] = array(
                        "order_id" => $rv["order_id"],
                        "plan_id" => $rv["plan_id"],
                        "plan_name" => ($langPf=="cn" && $rv["plan_name_cn"]!=""?$rv["plan_name_cn"]:$rv["plan_name"]),
                        "plan_type" => $rv["plan_type"],
                        "subscription_duration" => $rv["subscription_duration"],
                        //"order_total" => $rv["subscription_duration"] * $rv["price"],
			"order_total" => $rv["order_total"],
                        "order_discount" => $discountAmt,
                        "order_qty" => $rv["order_qty"],
                        "order_date" => $rv["order_date"],
                        //"price" => $rv["subscription_duration"] * $rv["price"],
			"price" => round($rv["order_total"] / $rv["subscription_duration"], 2),
                        "description" => ($langPf=="cn" && $rv["description_cn"]!=""?$rv["description_cn"]:$rv["description"]),
                        "image" => (file_exists($path . "/" . $rv["image"]) && $rv["image"] != "" ? $imageURL . $rv["image"] : ""),
                        "thumb_image" => (file_exists($thumbpath . "/" . $rv["image"]) && $rv["image"] != "" ? $imagethumbURL . $rv["image"] : ""),
                        "address_id" => $billingAddress["address_id"],
                        "address" => $billingAddString);
                }
            }else{
                $resArrS=array();
            }
            /* Get Incart product info*/
            $sql = "select p.product_id,o.order_id,o.order_total,o.order_date,o.payment_method,o.order_qty,o.address_id,o.order_date,
                    p.product_name,p.price,p.image,o.flash_sale_id,p.product_name_cn,p.status  from orders o 
                     LEFT JOIN product p  ON p.product_id=o.product_id 
                    WHERE o.user_id='" . $fields['user_id'] . "' 
                    AND o.order_status='in_cart' AND o.type=2";
            $row = $wb->getAllData($sql);
            
            $path = realpath(Yii::app()->basePath . '/../images/product/');
            $thumbpath = realpath(Yii::app()->basePath . '/../images/product/thumb/');
            $imageURL = Yii::app()->getBaseUrl(true) . '/images/product/';
            $imagethumbURL = Yii::app()->getBaseUrl(true) . '/images/product/thumb/';
            if ($row) {
                $status = "1";
                $msg = "Data found!";
                $billingAddString = "";
		$i=0;
                foreach ($row as $rk => $rv) {
		    
                     if($discountPerc != '0')
                        $discountAmt =  (($rv["order_qty"] * $rv["price"]) * $discountPerc) / 100;
			$_SESSION['discountAmt'.$i] = $discountAmt;
			$NetDiscount += $discountAmt;  
			$_SESSION['NetDiscount']  = $NetDiscount;

		    $billingAddString = "";
                    $addData=true;
                    $now=date("Y-m-d h:i:s"); 

                    if($rv["flash_sale_id"]!='0'){
                        // check 
                        $subsql = "select fs.* from  flash_sale fs
                                   where fs.sale_start_from<='".$now."' and fs.sale_end>='".$now."' 
                                  and fs.flash_sale_id='".$rv["flash_sale_id"]."'";
                        $subrow = $wb->getRowData($subsql);
                        if(!$subrow){
                             $addData=false;
                             $wb->executeQuery("Delete from orders where order_id='".$rv["order_id"]."'");
                        }
                    }
		   /* Delete product from order if product is inactive
		      Modified date : 20/4/2016	  */ 
 		    if($rv["status"]=='InActive'){
			        
			// Delete Product form order table
			$addData=false;
                 	$wb->executeQuery("Delete from orders where order_id='".$rv["order_id"]."'");                   
                    }
                    if($addData){
                        $billingAddress = $wb->getRowData("SELECT * from address where user_id='" . $fields['user_id'] . "' 
                                                        and address_id='" . $rv["address_id"] . "'");
                        $addArr = array();
                        $billingAddString="";
                        if ($billingAddress) {
                            $addArr = array("label" => $billingAddress["label"],
                                "address_id" => $billingAddress["address_id"],
                                "first_name" => $billingAddress["first_name"],
                                "last_name" => $billingAddress["last_name"],
                                "company_name" => $billingAddress["company_name"],
                                "address" => $billingAddress["address"],
                                "city" => $billingAddress["city"],
                                "state" => $billingAddress["state"],
                                "country" => $billingAddress["country"],
                                "postcode" => $billingAddress["postcode"],
                                "phone" => $billingAddress["phone"],
                                "is_billing" => $billingAddress["is_billing"],
                                "is_shipping" => $billingAddress["is_shipping"]);
                            $billingAddString = $this->retAddressString($addArr);
                        }
                        $resArr[] = array(
                            "order_id" => $rv["order_id"],
                            "product_id" => $rv["product_id"],
                            "product_name" => ($langPf=="cn" && $rv["product_name_cn"]!=""?$rv["product_name_cn"]:$rv["product_name"]),
                            "price" => $rv["price"],
                            "order_date" => date("d/m/Y", strtotime($rv["order_date"])),
                            "order_total" => ($rv["order_qty"] * $rv["price"]),
                            "order_discount" => $discountAmt,
                            "order_qty" => $rv["order_qty"],
                            "price" => $rv["price"],
                            "payment_method" => $rv["payment_method"],
                            "image" => (file_exists($path . "/" . $rv["image"]) && $rv["image"] != "" ? $imageURL . $rv["image"] : ""),
                            "thumb_image" => (file_exists($thumbpath . "/" . $rv["image"]) && $rv["image"] != "" ? $imagethumbURL . $rv["image"] : ""),
                            "address_id" => $billingAddress["address_id"],
                            "address" => $billingAddString);
                    }
                }
            }else{
                $resArr=array();
            }
            $dollarValue=0;
            $cartTotal=count($resArrS)+count($resArr);
            if(count($resArrS)==0 && count($resArr)==0){
                $status="-2";
                $msg="Sorry! Your shopping cart is empty.";
            }else{
                /*$from   = 'CNY';
                $to     = 'USD';
                $url = 'http://finance.yahoo.com/d/quotes.csv?e=.csv&f=sl1d1t1&s='. $from . $to .'=X';
                $handle = @fopen($url, 'r');
                $result=array();       
                if ($handle) {
                    $result = fgets($handle, 4096);
                    fclose($handle);
                    $allData = explode(',',$result); 
                    $dollarValue = $allData[1];
                }*/
            }
           $response = array('response' => array('status' => $status, 'Message' => $msg, 'subsciption' => $resArrS,'product'=>$resArr,"dollarValue"=>$dollarValue,"cartTotal"=>$cartTotal,"discountpercentage"=>$discountPerc)); 
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'user_id is blank'));
        }
        
        echo json_encode($response);
        die;
}
    
   /* mail function call 
   public function actiontestmail()
   {	   
	//Helper::sendMailByMailer("test content", "test subject", "kinjal.shah@credencys.com", "webmaster@sigwine.com", "sigwine", $attach_path = '', $type = "cc", $bc_arr = array());
       $wb = new Webservice();
       if($wb->sendPushNotificationToAndroid(array('APA91bHZGXtdu5GiBOiHgllGPu0Kw9J3PjnKtbqpj_IRZVxjKlDFHAHVwpygqvUcZIr6GkIJpuBiqUSH3BwEnMtiTss5SSQlealddIY6QP8-OqDWr9VY2cg') , 'this is testing' , 'test')) {
	   echo 'sent';
       } else {
	   echo 'not sent';
       }
       die;
   }*/      
}
