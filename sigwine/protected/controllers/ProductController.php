<?php

class ProductController extends Controller
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
			array('allow', // allow all users to perform 'index' and 'view' actions
                            'actions' => array('index', 'view', 'create', 'update', 'delete'),
                            'users' => array('*'),
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
		$model=new Product;
                $model1=new SubscriptionPlan;
              
		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);
                $path= realpath(Yii::app()->basePath . '/../images/product/');
                $videopath= realpath(Yii::app()->basePath . '/../images/product/video/');
                $thumbpath=realpath(Yii::app()->basePath . '/../images/product/thumb/');
		if(isset($_POST['Product'])){
			$model->attributes=$_POST['Product'];
                        if ($_FILES["Product"]["name"]["image"] != ""){
                            // Upload doctor image
                            $uploadedFile=CUploadedFile::getInstance($model,'image');
                            $filename=explode(".", $uploadedFile);
                            $fileext = $filename[count($filename) - 1];
                            $newfilename = time() . "." . $fileext;
                            $model->image = $newfilename;
                            $_POST['Product']["image"] = $newfilename;
                            if(!empty($uploadedFile)){
                                $uploadedFile->saveAs($path."/".$newfilename);
                            }
                            $image = new EasyImage($path."/".$newfilename);
                            $image->resize(100, 100);
                            $image->save($thumbpath."/".$newfilename);
                        }
                        if ($_FILES["Product"]["name"]["video"] != ""){
                            // Upload doctor image
                            $uploadedFile=CUploadedFile::getInstance($model,'video');
                            $filename=explode(".", $uploadedFile);
                            $fileext = $filename[count($filename) - 1];
                            $newfilename = str_replace(" ","-",$_POST['Product']['product_name'])."-".time().".".$fileext;
                            $model->video = $newfilename;
                            $_POST['Product']["video"] = $newfilename;
                            if(!empty($uploadedFile)){
                                $uploadedFile->saveAs($videopath."/".$newfilename);
                            }
                        }
			if($model->save()){
                            // add product to month and subscription plan
                            if(isset($_POST["SubscriptionPlan"])){
                               foreach($_POST["SubscriptionPlan"]["plan_id"] as $k=>$v){
                                    $model1=new SubscriptionPlanDetail;
                                    $model1->attributes=$_POST['SubscriptionPlan'];
                                    $model1->plan_id=$v;
                                    $model1->product_id=$model->product_id;
                                    $model1->month=$_POST["month"];
                                    $model1->year=$_POST["year"];
                                    $model1->save();
                                }
                            }
                            $this->redirect(array('view','id'=>$model->product_id));
                        }
		}
		$this->render('create',array(
			'model'=>$model,'model1'=>$model1,
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
                $path= realpath(Yii::app()->basePath . '/../images/product/');
                $thumbpath=realpath(Yii::app()->basePath . '/../images/product/thumb/');
                $videopath= realpath(Yii::app()->basePath . '/../images/product/video/');
              	
		if(isset($_POST['Product'])){ 
                    if(!empty($_FILES["Product"]["name"]["image"])){
                    
                        $old_image = $_POST['oldimage'];
                        $uploadedFile=CUploadedFile::getInstance($model,'image');
                        
                        $filename=explode(".", $uploadedFile);
                        $fileext = $filename[count($filename) - 1];
                        $newfilename = time() . "." . $fileext;
                        $model->image = $newfilename;
                        $_POST['Product']["image"] = $newfilename;
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
                    if ($_FILES["Product"]["name"]["video"] != ""){
                        
                        $old_video = $_POST['oldvideo'];
                        $uploadedFile=CUploadedFile::getInstance($model,'video');
                        $filename=explode(".", $uploadedFile);
                        $fileext = $filename[count($filename) - 1];
                        $newfilename = str_replace(" ","-",$_POST['Product']['product_name'])."-".time().".".$fileext;
                        $model->video = $newfilename;
                        $_POST['Product']["video"] = $newfilename;
                        if(!empty($uploadedFile)){
                            $uploadedFile->saveAs($videopath."/".$newfilename);
                            if (file_exists($videopath . $old_image)) {
                                  @unlink($videopath . $old_image);
                            }
                        }
                    }else{
                        $model->video = $_POST['oldvideo'];
                    }
		    $model->attributes=$_POST['Product'];	
		     if($model->save())
                        $this->redirect(array('view','id'=>$model->product_id));
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
		$dataProvider=new CActiveDataProvider('Product');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Product('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Product']))
			$model->attributes=$_GET['Product'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Product the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Product::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Product $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='product-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
