<?php

class FlashSaleController extends Controller
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
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','sendiosnotifications','sendanroidnotifications'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','admin','index','view','sendPush','create','update','admin','delete','sendnotifications','sendanroidnotifications'),
				'users'=>array('admin'),
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

        public function actionSendnotifications($id)
	{
		
             $this->render('sendnotifications',array(
			'model'=>$this->loadModel($id),
		));
	}
        
        public function actionSendiosnotifications($id)
        {
            #### Send Notification to IOS users ####
            $wb=new Webservice();
            $model=$this->loadModel($id);
            
            // For Ios users
            $message="Flash Sale begins ".$model->sale_start_from;
            $wb->actionpushNotificationToIos($message,"flash","");
            
            $this->redirect(array('admin'));
           
        }
        
        public function actionSendanroidnotifications($id)
        {
            $wb=new Webservice();
            $model=$this->loadModel($id);
            
            // For Ios users
            $message="Flash Sale begins ".$model->sale_start_from;
            $msg=array("msg"=>$message,"id"=>"","type"=>"flash");
            $wb->actionpushNotificationToAndroid($msg);
            
            $this->redirect(array('admin'));
        }      
        public function actionCreate()
	{
		$model=new FlashSale;
                $model1=new Product;
                // Uncomment the following line if AJAX validation is needed
	
		if(isset($_POST['FlashSale'])){
                    
                    //$this->performAjaxValidation($model);
                    
                    // $sql="select * from flash_sale where sale_start_from"
                    $model->attributes=$_POST['FlashSale'];
		    if($model->save()){
			//$this->redirect(array('sendnotifications','id'=>$model->flash_sale_id));
			$this->redirect(array('admin'));
                    }
		}
		$this->render('create',array(
			'model'=>$model,
                        'model1'=>$model1
		));
	} 
	/*public function actionsendPush($id){
            $wb=new Webservice();
            $model=$this->loadModel($id);
            $message="Flash Sale has been starting from ".$model->sale_start_from;
            $msg=array("msg"=>$message,
                        "id"=>"","type"=>"flash");
            // Send Push Notification To IOS Users
            $wb->actionpushNotificationToIos($message,"flash","");
            // Send Push Notification To Anroid Users
            $wb->actionpushNotificationToAndroid($msg);
            $this->redirect(array('admin'));
        }*/
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

		if(isset($_POST['FlashSale']))
		{
			$model->attributes=$_POST['FlashSale'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->flash_sale_id));
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
		$dataProvider=new CActiveDataProvider('FlashSale');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new FlashSale('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['FlashSale']))
			$model->attributes=$_GET['FlashSale'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return FlashSale the loaded model
	 * @throws CHttpException
	 */

	public function loadModel($id)
	{
		$model=FlashSale::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param FlashSale $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='flash-sale-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
