<?php

class UsersController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
            $session=new CHttpSession;
            $session->open();	
            return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','create','update','admin','delete','sendnotifications','ajaxsendNotificaton'),
				//'users'=>array($session["uname"]),
			    'users'=>array('*'),
			),
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('Testinfo'),
				'users'=>array('*'),
			    //'users'=>array('*'),
			),
			
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Users;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
                $path= realpath(Yii::app()->basePath . '/../images/profile/');   
                $thumbpath=realpath(Yii::app()->basePath . '/../images/profile/thumb/');
		if(isset($_POST['Users'])){
			$model->attributes=$_POST['Users'];
                        if ($_FILES["Users"]["name"]["profile_image"] != ""){
                            // Upload doctor image
                            $uploadedFile=CUploadedFile::getInstance($model,'profile_image');
                            $filename=explode(".", $uploadedFile);
                            $fileext = $filename[count($filename) - 1];
                            $newfilename = time() . "." . $fileext;
                            $model->profile_image = $newfilename;
                            $_POST['Users']["profile_image"] = $newfilename;
                            if(!empty($uploadedFile)){
                                $uploadedFile->saveAs($path."/".$newfilename);
                                $image = new EasyImage($path."/".$newfilename);
                                $image->resize(100, 100);
                                $image->save($thumbpath."/".$newfilename);
                                /**
                                 * @desc : Delete existing images
                                 */
                                if (file_exists($path . $old_image)) {
                                      @unlink($path . $old_image);
                                }
                            }
                        }
                        $model->password = base64_encode($_POST['Users']["password"]);
                        if($model->save()){
                           $this->redirect(array('view','id'=>$model->user_id));
                        }
		}
		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
           
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
                $path= realpath(Yii::app()->basePath . '/../images/profile/');
                $thumbpath=realpath(Yii::app()->basePath . '/../images/profile/thumb/');
		if(isset($_POST['Users']))
		{       
			$model->attributes=$_POST['Users'];
                        if(!empty($_FILES["Users"]["name"]["profile_image"])){
                        // Upload doctor image
                        $old_image = $_POST['oldimage'];
                        $uploadedFile=CUploadedFile::getInstance($model,'profile_image');
                        
                        $filename=explode(".", $uploadedFile);
                        $fileext = $filename[count($filename) - 1];
                        $newfilename = time() . "." . $fileext;
                       
                        $model->profile_image = $newfilename;
                        $_POST['Users']["profile_image"] = $newfilename;
                        if(!empty($uploadedFile)){
                                $uploadedFile->saveAs($path."/".$newfilename);
                                $image = new EasyImage($path."/".$newfilename);
                                $image->resize(250,250);
                                $image->save($thumbpath."/".$newfilename);
                                /**
                                 * @desc : Delete existing images
                                 */
                                if (file_exists($path . $old_image)) {
                                      @unlink($path . $old_image);
                                }
                       }
                    }else{
                        $model->profile_image = $_POST['oldimage'];
                    }
                    $model->attributes=$_POST['Users'];
                    $model->password = base64_encode($_POST['Users']["password"]);
                    if($model->save())
                        $this->redirect(array('view','id'=>$model->user_id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Users');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Users('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Users']))
			$model->attributes=$_GET['Users'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Users the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Users::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Users $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='users-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public function actionSendNotifications()
	{
	    
	    $model=new Users('search');
	    if(isset($_GET['Users']))
			$model->attributes=$_GET['Users'];
	    $this->render('sendnotifications',array(
			'model'=>$model,
		  ));  
	}
	
public function actionAjaxsendNotificaton()
{
	   
	    set_time_limit( 0 );
	    $model=new Users();              
            $model->unsetAttributes();
            $wb = new Webservice();
	    
	    
    if(isset($_POST) && !empty($_POST))
    {
	$an_message=array("msg"=>$_POST['messege'],"id"=>"-1","type"=>"notification");
	$message=$_POST['messege'];
	// when check box are checked
	if(empty($_POST['ids']))
	{    

	 $post["table"] = 'users';
	 $post["fields"] = 'device_token,device_type,user_id';
	 $post["beforeWhere"] = '';
	 $post["afterWhere"] = 'device_token != ""';
	 $post["r_p_p"] = '1';
	 $post["start"] = 'all';
	 $response = Yii::app()->JsonWebservice->fetchData($post, $dataobj = 'db');
	if($response['status']==1){
	    //loop here for each user 
	    foreach($response["data"] as $dt) 
	    {	 
		$device_type = $dt["device_type"];
		$u_id = $dt["user_id"];
		$devicetoken = $dt["device_token"];
		if($devicetoken != "")
		{   if ($device_type == 1)
		    { 
			if( $wb->actionpushNotificationToAndroid($an_message)) {
			 // ADD NOTIFICATION CODE 
			    $posta['user_id']=$u_id;
			    $posta['device_token']= $devicetoken;
			    $posta['notification']= $_POST['messege'];
			    $posta['cdate']= gmdate("Y-m-d H:i:s");
			    $table = 'notification';

			    $response = Yii::app()
					->JsonWebservice
					->addData($table, $posta, $nfield='', $uniqueField='', $lastIdStatus = 1,$fieldEncode='',$dataobj = 'db');  
				echo "success";    
			}
			else{
			    $response = array("status"=>'-1');
			    echo "Fail";
			}
		    }
		    else if($device_type == 2) {  

			if($wb->sendPushNotificationToIos($devicetoken,$message,"new","-1")){

			    $response = array('status' => '1', 'data' => array('message' => 'Notification send successfully'));
			    
				    // ADD NOTIFICATION CODE  
				    $posta['user_id']=$u_id;
                                    $posta['device_token']= $devicetoken;
                                    $posta['notification']= $_POST['messege'];
                                    $posta['cdate']= gmdate("Y-m-d H:i:s");
                                    $table = 'notification';

                                    $response = Yii::app()
                                                ->JsonWebservice
                                                ->addData($table, $posta, $nfield='', $uniqueField='', $lastIdStatus = 1,$fieldEncode='',$dataobj = 'db'); 
			    
			}
			else{
			    $response = array("status"=>'-1');
			    echo "Fail";
			}
		    } // END ELSE IF
		    else{
//                         
                            $response = array("status"=>'2');
                            echo "fail";
                        } 
		} 
	    }	
	}
	}// end if post id
	else
	{
	   //check box notification code   
	  foreach($_POST['ids'] as $key=>$value)
	   {
		$post["table"] = 'users';
		$post["fields"] = 'device_token,device_type,user_id';
		$post["beforeWhere"] = '';
		$post["afterWhere"] = 'user_id = "'.$value.'"';
		$post["r_p_p"] = '1';
		$post["start"] = 'all';
		$response = Yii::app()->JsonWebservice->fetchData($post, $dataobj = 'db');
		if($response['status']==1){

		    $device_type = $response["data"]["0"]["device_type"];
		    $u_id = $response["data"]["0"]["user_id"];
		    $devicetoken = $response["data"]["0"]["device_token"];
		    if ($device_type == 1)
		    {
			 if($wb->actionpushNotificationToAndroid($an_message)) {
			    // ADD NOTIFICATION CODE 
			    $posta['user_id']=$u_id;
			    $posta['device_token']= $devicetoken;
			    $posta['notification']= $_POST['messege'];
			    $posta['cdate']= gmdate("Y-m-d H:i:s");
			    $table = 'notification';

			    $response = Yii::app()
					->JsonWebservice
					->addData($table, $posta, $nfield='', $uniqueField='', $lastIdStatus = 1,$fieldEncode='',$dataobj = 'db'); 
				echo "success";    
			}
			else{
			    $response = array("status"=>'-1');
			    echo "Fail";
			}
		    
		    }// End Device type if
		    else if($device_type == 2)
		    { 	
			if($wb->sendPushNotificationToIos($devicetoken,$message,"push notification","-1")){
			    $response = array('status' => '1', 'data' => array('message' => 'Notification send successfully'));
			    
			      // ADD NOTIFICATION CODE 
			    $posta['user_id']=$u_id;
			    $posta['device_token']= $devicetoken;
			    $posta['notification']= $_POST['messege'];
			    $posta['cdate']= gmdate("Y-m-d H:i:s");
			    $table = 'notification';

			    $response = Yii::app()
					->JsonWebservice
					->addData($table, $posta, $nfield='', $uniqueField='', $lastIdStatus = 1,$fieldEncode='',$dataobj = 'db'); 
			    echo "done";
			    }else{
			    $response = array("status"=>'-1');
			    echo "Fail";
			}
			
		    }
		    else{
//                         
                            $response = array("status"=>'2');
                            echo "fail";
                        }

		} // end if
	    }// end foreach 
	}   
    }
    else
    {
//      echo 'coming';die;
	$response = array("status"=>'2');
	//Yii::app()->user->setFlash('success', "Notification sending Failed");
	echo "fail";
    }
}

public function actionTestinfo()
{
	$registatoin_ids = (array) $_POST['registatoin_ids'];
	$message = $_POST['message'];
	
	$messages = array("data" => $message);
	$fields = array('registration_ids' => $registatoin_ids , 'data' => $messages, 'type' => $type);
        /**
         * @desc : type 1 = driver
         * type 2 = passenger
         */
	/* pushY API Key For Live Server */
        $key = 'cb1ec4526d744b9c0b6da926bb1ed9d742829634786d562af9bbe84ebac98f1d' ;
	$url = 'https://pushy.me/push?api_key=' . $key;
        //echo $key ;
        
	$headers = array(
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
	
	if($res["success"] >= '1'){
             $response = array('status' => '1', 'data' => array('message' => 'Notification send successfully'));
        } else {
            $response = array('status' => '-1', 'data' => array('message' => 'Notification not send.'));
        }
}	
               
}
        
        
	

