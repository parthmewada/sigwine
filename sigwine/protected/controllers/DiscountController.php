<?php

class DiscountController extends Controller
{
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Discount');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

        public $layout='//layouts/column2';
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	} 
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
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Product the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model= Discount::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
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
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		
                $model=new Discount('search');
               	$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Discount']))
			$model->attributes=$_GET['Discount'];
                
                $this->render('admin',array(
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
        
        public function actionUpdate($id)
	{
		$model=$this->loadModel($id);
                // Uncomment the following line if AJAX validation is needed
		//$this->performAjaxValidation($model);
		if(isset($_POST['Discount']))
		{
                    $model->attributes=$_POST['Discount'];
                    if($model->save())
			$this->redirect(array('view','id'=>$model->discount_id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}
        
        /**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Discount();
               // Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);
                if(isset($_POST['Discount'])){
			$model->attributes=$_POST['Discount'];
                        if($model->save()){
                           $this->redirect(array('view','id'=>$model->discount_id));
                        }
		}
		$this->render('create',array(
			'model'=>$model,
		));
	}
        
        /**
	 * Performs the AJAX validation.
	 * @param Product $model the model to be validated
	 */
	
        /**
	 * Performs the AJAX validation.
	 * @param Product $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='Discount-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
        
}