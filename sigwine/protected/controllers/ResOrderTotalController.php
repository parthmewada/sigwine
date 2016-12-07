<?php

class ResOrderTotalController extends Controller
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
				'actions'=>array('index','view','update','delete','changestatus'),
				'users'=>array('*'),
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
		$model=new ResOrderTotal;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ResOrderTotal']))
		{
			$model->attributes=$_POST['ResOrderTotal'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->order_total_id));
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

		if(isset($_POST['ResOrderTotal']))
		{
			$model->attributes=$_POST['ResOrderTotal'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->order_total_id));
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
		$dataProvider=new CActiveDataProvider('ResOrderTotal');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new ResOrderTotal('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ResOrderTotal']))
			$model->attributes=$_GET['ResOrderTotal'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return ResOrderTotal the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=ResOrderTotal::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param ResOrderTotal $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='res-order-total-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
        public function actionchangestatus(){
            $order_total_id = $_REQUEST['order_total_id'];
            $val = $_REQUEST['val'];
            $wb = new Webservice();
            
            $postu = array();
            $postu['order_status']=$val;
            $where =  "order_total_id = ".$order_total_id;
            $table = "res_order_total";
            $response = $wb->updateData($postu, '', '', $where ,$table);
            if($val=='Delivered'){
                $select="select u.first_name,u.last_name,u.email,o.order_total_id from res_order_total o 
                        left join users u ON u.user_id=o.user_id 
                        where o.order_total_id='".$_REQUEST["order_total_id"]."'";
                $row=$wb->getRowData($select);
                $to=$row["email"];
                $from="Sigwine Team";
                $orderno=$row["order_total_id"];
                $firstname=$row["first_name"];
                $lastname=$row["last_name"];
                $this->sendmail($to,$from, $orderno,$firstname, $lastname);
            }
            if($response['status'] == 1) {
                echo "success";
            }
            else {
                 echo "error";
            }

        }
        public function sendmail($to,$from, $orderno,$firstname, $lastname) {
            $body = '<body>
                        <p>Dear '.$firstname." ".$lastname . '</p>
                        <p>Thank you for shopping from Sigwine App</p>
                        <p>Your order '.$orderno.' has been successfully delivered at the mentioned shipping address.</p>
                        <p>Thanks,</p>
                        <p>Sigwine Team</p>                        
                    </body>';

            $headers = "From: SigWine <admin@sigwine.com>\r\n";
            $headers .= "Content-type: text/html\r\n";
            $subject = "Your Order ".$orderno." has been Delivered";
            // now lets send the email.
            
            if (mail($to, $subject, $body, $headers)) {
                return true;
            } else {
                return false;
            }
        }
}
