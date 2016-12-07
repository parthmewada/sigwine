<?php

class SubscriptionPlanController extends Controller
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
				'actions'=>array('index','view','create','update','admin','delete'),
				'users'=>array($session["uname"]),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
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

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new SubscriptionPlan;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
                
		if(isset($_POST['SubscriptionPlan']))
		{       
                   if(!empty($_FILES["SubscriptionPlan"]["name"]["image"])){
                        // Upload doctor image
                        $old_image = $_POST['oldimage'];
                        $uploadedFile=CUploadedFile::getInstance($model,'image');

                        $filename=explode(".", $uploadedFile);
                        $fileext = $filename[count($filename) - 1];
                        $newfilename = time() . "." . $fileext;
                        $model->image = $newfilename;
                        $_POST['SubscriptionPlan']["image"] = $newfilename;
                        if(!empty($uploadedFile)){
                            $uploadedFile->saveAs($path."/".$newfilename);

                            $image = new EasyImage($path."/".$newfilename);
                            $image->resize(250, 250);
                            $image->save($thumbpath."/".$newfilename);
                            /**
                             * @desc : Delete existing images
                             */
                            if (file_exists($path . $old_image)) {
                                  @unlink($path . $old_image);
                            }
                        }
                    }else{
                        $model->image = $_POST['oldimage'];
                    }
                    $model->attributes=$_POST['SubscriptionPlan'];
                    if($model->save()){
                        if (isset($_POST['duration'])) {
                            $price=$_POST['price'];
                            foreach ($_POST['duration'] as $ps => $pv) {
                                $subscriptionPlanPrice = new SubscriptionPlanPrice;
                                $subscriptionPlanPrice->plan_id=$model->plan_id;
                                $subscriptionPlanPrice->price = $price[$ps];
                                $subscriptionPlanPrice->duration = $pv;
                                $subscriptionPlanPrice->save();
                            }
                        }
                        $this->redirect(array('view','id'=>$model->plan_id));
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
		$wb = new Webservice();
                // Uncomment the following line if AJAX validation is needed
                // $this->performAjaxValidation($model);
                $sql = "Select * from subscription_plan_price where plan_id='" . $id . "' 
                        order by subscription_plan_price_id asc";
                $question = $wb->getAllData($sql);
                $quArr = array();
                if ($question) {
                    //echo "<pre>";
                    foreach ($question as $qk => $qv) {
                        $quArr[] = array("duration" => $qv["duration"], "price" => $qv["price"],"subscription_plan_price_id"=>$qv["subscription_plan_price_id"]);
                    }
                }
               
               $path=realpath(Yii::app()->basePath . '/../images/subscription/');
               $thumbpath=realpath(Yii::app()->basePath . '/../images/subscription/thumb/');
		if(isset($_POST['SubscriptionPlan'])){
                    
                    if(!empty($_FILES["SubscriptionPlan"]["name"]["image"])){
                       // Upload doctor image
                       $old_image = $_POST['oldimage'];
                       $uploadedFile=CUploadedFile::getInstance($model,'image');

                       $filename=explode(".", $uploadedFile);
                       $fileext = $filename[count($filename) - 1];
                       $newfilename = time() . "." . $fileext;
                       $model->image = $newfilename;
                       $_POST['SubscriptionPlan']["image"] = $newfilename;
                       if(!empty($uploadedFile)){
                           $uploadedFile->saveAs($path."/".$newfilename);

                           $image = new EasyImage($path."/".$newfilename);
                           $image->resize(250, 250);
                           $image->save($thumbpath."/".$newfilename);
                           /**
                            * @desc : Delete existing images
                            */
                           if (file_exists($path . $old_image)) {
                                 @unlink($path . $old_image);
                           }
                       }
                   }else{
                       $model->image = $_POST['oldimage'];
                   }

                   $model->attributes=$_POST['SubscriptionPlan'];
                   if($model->save()){
                       $delOption = "Delete from subscription_plan_price where plan_id='" . $id . "'";
                       $wb->deleteRecords($delOption);
                       if (isset($_POST['duration'])) {
                            $price=$_POST['price'];
                            foreach ($_POST['duration'] as $ps => $pv) {
                                $subscriptionPlanPrice = new SubscriptionPlanPrice;
                                $subscriptionPlanPrice->plan_id=$model->plan_id;
                                $subscriptionPlanPrice->price = $price[$ps];
                                $subscriptionPlanPrice->duration = $pv;
                                $subscriptionPlanPrice->save();
                            }
                        }
                       $this->redirect(array('view','id'=>$model->plan_id));
                   }
		}

		$this->render('update',array(
			'model'=>$model,
                        'quArr' => $quArr,
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
		$dataProvider=new CActiveDataProvider('SubscriptionPlan');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new SubscriptionPlan('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['SubscriptionPlan']))
			$model->attributes=$_GET['SubscriptionPlan'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return SubscriptionPlan the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=SubscriptionPlan::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param SubscriptionPlan $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='subscription-plan-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
