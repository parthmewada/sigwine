<?php

class SurveyController extends Controller {

    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';

    /**
     * @return array action filters
     */
    public function filters() {
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
    public function accessRules() {
        return array(
            array('allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array('index', 'view', 'create', 'update', 'delete', 'result', 'deleteQuestion'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('create', 'update'),
                'users' => array('@'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('admin', 'delete'),
                'users' => array('admin'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id) {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }
    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionResult($id) {
        $this->render('result', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate() {
        $model = new Survey;
        $wb=new Webservice();
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Survey'])) {
            $model->attributes = $_POST['Survey'];
            $model->created_on = date('Y-m-d h:i:s');
           
            if ($model->save()) {
                if (isset($_POST['question'])) {
                    foreach ($_POST['question'] as $ps => $pv) {
                        $surveyQuestion = new SurveyQuestion;
                        $surveyQuestion->question = $pv;
                        $surveyQuestion->survey_id = $model->survey_id;

                        if ($surveyQuestion->save()) {
                            $exp = $_POST['option'][$ps];
                            foreach ($exp as $po => $so) {
                                if (!empty($so)) {
                                    $surveyOption = new SurveyOption;
                                    $surveyOption->survey_question_id = $surveyQuestion->survey_question_id;
                                    $surveyOption->survey_id = $model->survey_id;
                                    $surveyOption->option = $so;
                                    $surveyOption->save();
                                }
                            }
                        }
                    }
                    $msg=array("msg"=>$model->survey_name,"id"=>$model->survey_id,"type"=>"survey");
                    // Send Push Notification To IOS Users
                    $wb->actionpushNotificationToIos($model->survey_name,"survey",$model->survey_id);
                    // Send Push Notification To Anroid Users
                    $wb->actionpushNotificationToAndroid($msg);
                }
                $this->redirect(array('view', 'id' => $model->survey_id));
            }
        }
        $this->render('create', array(
            'model' => $model,
        ));
    }
    /*Send push notification about survey*/
    public function actionpushNotificationToIos() {
        $wb = new Webservice();
        //$msg = "send successfully";
        $type = "notified";
        $todaydate = date('Y-m-d');
        $corenttime = date('h:i:s');
        $sql = "SELECT device_token  FROM users where device_typeid =2";
        $deviceTokendata = $wb->getAllData($sql);
        $badge_count = 0;
        foreach ($deviceTokendata as $key => $value) {
            $deviceToken[] = $value['notification_token'];
        }
        //@desc Get Alert details from alert
        $sql = "SELECT alertid,name,alert_description,frequency,Duration,start_date,start_time,is_corelate,days_from_expiration,user_type,duration_notification_sent,notification_send_ios FROM alert";
        $data = $wb->getAllData($sql);
        $send_count = 1;
        foreach ($data as $key => $value) {
            //@Get notification time
            $newtime = $this->getNotificationtime($value['start_date'], $value['start_time'], $value['frequency'], $value['Duration'], $value['notification_send_ios']);
            //@Get notification date
            $isSentPushnotification = $this->getNotificationdate($value['start_date'], $value['frequency'], $value['Duration'], $value['notification_send_ios']);
            //compare today date and store date
            if ($isSentPushnotification == $todaydate) {
                foreach ($deviceToken as $key => $val) {
                    //print_r($deviceToken);die;
                    $notis = $wb->sendPushNotificationToIos($val, $value['alert_description'], $type, $badge_count);
                }
            }
            if ($newtime == $corenttime) {
                foreach ($deviceToken as $key => $val) {
                    $notis = $wb->sendPushNotificationToIos($val, $value['alert_description'], $type, $badge_count);
                }
            }
            //update count here duration_notification_sent code here
            if ($notis) {
                $post = array();
                $post['notification_send_ios'] = $value['notification_send_ios'] + 1;
                $where = "alertid = '" . $value['alertid'] . "'";
                $wb->updateData($post, '', '', $where, "alert");
            }
        }

        // $notis = $wb->sendPushNotificationToIos($deviceToken,$msg,$type);

        if ($notis) {
            $response = array('status' => '1', 'data' => array('message' => 'Notification send successfully'));
        } else {
            $response = array('status' => '0', 'data' => array('message' => 'Notification sending fail'));
        }
        echo json_encode(array("response" => $response));
        die;
    }
    
    
    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $wb = new Webservice();
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        $sql = "Select * from survey_question where survey_id='" . $id . "' order by survey_question_id asc";
        $question = $wb->getAllData($sql);
        $quArr = array();
        if ($question) {
            //echo "<pre>";
            foreach ($question as $qk => $qv) {
                $sql1 = "Select * from survey_option where survey_id='" . $id . "' and  survey_question_id='" . $qv['survey_question_id'] . "' order by survey_option_id asc";
                $option = $wb->getAllData($sql1);
                $quArr[] = array("question" => $qv, "option" => $option);
            }
        }
        if (isset($_POST['Survey'])) {
            $model->attributes = $_POST['Survey'];
            if ($model->save()) {
                // Delete survey question and option from database
                $delOption = "Delete from survey_option where survey_id='" . $model->survey_id . "'";
                $wb->deleteRecords($delOption);
                $delQuestion = "Delete from survey_question where survey_id='" . $model->survey_id . "'";
                $wb->deleteRecords($delQuestion);
                if (isset($_POST['question']) && count($_POST['question']) > 0) {
                    foreach ($_POST['question'] as $ps => $pv) {
                        if (!empty($pv)) {
                            $surveyQuestion = new SurveyQuestion;
                            $surveyQuestion->question = $pv;
                            $surveyQuestion->survey_id = $model->survey_id;

                            if ($surveyQuestion->save()) {
                                $exp = $_POST['option'][$ps];
                                foreach ($exp as $po => $so) {
                                    if (!empty($so)) {
                                        $surveyOption = new SurveyOption;
                                        $surveyOption->survey_question_id = $surveyQuestion->survey_question_id;
                                        $surveyOption->survey_id = $model->survey_id;
                                        $surveyOption->option = $so;
                                        $surveyOption->save();
                                    }
                                }
                            }
                        }
                    }
                }
                $this->redirect(array('view', 'id' => $model->survey_id));
            }
        }

        $this->render('update', array(
            'model' => $model,
            'quArr' => $quArr,
        ));
    }

    public function actiondeleteQuestion() {
        $wb = new Webservice();
        $delOption = "Delete from survey_option where survey_question_id='" . $_REQUEST['survey_question_id'] . "'";
        $wb->deleteRecords($delOption);
        $delQuestion = "Delete from survey_question where survey_question_id='" . $_REQUEST['survey_question_id'] . "'";
        $wb->deleteRecords($delQuestion);
        echo "success";
        exit;
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id) {
        $wb = new Webservice();
        $delOption = "Delete from survey_option where survey_id='" . $id . "'";
        $wb->deleteRecords($delOption);
        $delQuestion = "Delete from survey_question where survey_id='" . $id . "'";
        $wb->deleteRecords($delQuestion);
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    /**
     * Lists all models.
     */
    public function actionIndex() {
        $dataProvider = new CActiveDataProvider('Survey');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {
        $model = new Survey('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Survey']))
            $model->attributes = $_GET['Survey'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Survey the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        $model = Survey::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param Survey $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'survey-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
