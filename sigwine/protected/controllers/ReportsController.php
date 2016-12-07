<?php

class ReportsController extends Controller {

   
    // Uncomment the following methods and override them if needed
    /*
      public function filters()
      {
      // return the filter configuration for this controller, e.g.:
      return array(
      'inlineFilterName',
      array(
      'class'=>'path.to.FilterClass',
      'propertyName'=>'propertyValue',
      ),
      );
      }

      public function actions()
      {
      // return external action classes, e.g.:
      return array(
      'action1'=>'path.to.ActionClass',
      'action2'=>array(
      'class'=>'path.to.AnotherActionClass',
      'propertyName'=>'propertyValue',
      ),
      );
      } */
     public function accessRules()
    {
            $session=new CHttpSession;
            $session->open();
            return array(
                    array('allow',  // allow all users to perform 'index' and 'view' actions
                            'actions'=>array('index','Reports','subscriptionreport'),
                            'users'=>array($session["uname"]),
                    ),
                    array('deny',  // deny all users
                            'users'=>array('*'),
                    ),
            );
    }
    public function actionIndex()
    {
            $session=new CHttpSession;
            $session->open();
            if(!empty($session["uname"])){
                $dataProvider=$this->actionsubscriptionreport();
            }else{
                header("location:".Yii::app()->request->getBaseUrl(true));
                exit;
            }
           $this->render('index',array(
                'data'=>$dataProvider,
            ));
    }
   
    public function actionsubscriptionreport() {
	
	$pid = '';
        if (isset($_GET['sid']) && $_GET['sid'] != 'all') {
            $pid = $_GET['sid'];
            $cond = ' AND plan_id =' . $pid;
        } else {
            $cond = '';
        }
	  if(isset($_GET['order']) && $_GET['order']!=''){
            $datefilter = $_GET['order'];
        }
	else {
	    $datefilter	= '';
	}
	
	$wb = new Webservice();
        $data = array();
	$postb = "SELECT sp.*,o.subscription_duration,o.order_total,o.order_qty,sp.plan_name,sp.plan_type,
                    o.order_date,o.address_id,u.first_name,u.last_name,u.user_id
                    FROM `res_orders` o
                    LEFT JOIN `subscription_plan` sp ON o.subscription_plan_id=sp.plan_id
                    LEFT JOIN users u ON o.user_id=u.user_id
                    WHERE o.type=1 ".$cond;
        $response = $wb->getAllData($postb);
	if($response){
            foreach ($response as $key => $val) {
                $data[$key]['user_name'] = $val['first_name'] . " " . $val['last_name'];
                $data[$key]['user_id'] = $val['user_id'];
                $data[$key]['duration'] = $val['subscription_duration'];
                $data[$key]['orderdate'] = $val['order_date'];
                $data[$key]['subscription_name'] = $val['plan_name'];
                $data[$key]['plan_type'] = $val['plan_type'];
            }
        }
	else {
	    $data = array();
	}
	$this->render('index', array(
            'pid' => $pid, 'date' => $datefilter, 'data' => $data
        ));
    }
    public function actionorderreport() {
        $pid = '';
	$datefilter = '';
        if (isset($_GET['sid']) && $_GET['sid'] != 'all') {
            $pid = $_GET['sid'];
            $cond = ' AND plan_id =' . $pid;
        } else {
            $cond = '';
        }
        if(isset($_GET['order']) && $_GET['order']!=''){
            $datefilter = $_GET['order'];
        }
	if($datefilter == 'today'){
            $fromdate = date("Y-m-d h:i:s");
            $todate = date("Y-m-d h:i:s");
        }else if($datefilter == 'yesterday'){
           $fromdate = date("Y-m-d h:i:s",  strtotime("-1 day"));
           $todate = date("Y-m-d h:i:s");
        }else if($datefilter == 'last 7 day'){
           $fromdate = date("Y-m-d h:i:s",  strtotime("-8 days"));
           $todate = date("Y-m-d h:i:s");
        }else{	
           $datefilter = 'this month';
           $fromdate = date("Y-m-d h:i:s",  mktime(0,0,0,date("m"),1,date("Y")));
           $todate = date("Y-m-d h:i:s");
        }
        $wb = new Webservice();
        $data = array();
	$sql = "select o.order_id,o.order_total,o.order_date,o.payment_method,o.order_qty,o.address_id,
                o.order_date,p.product_name,p.price,p.image,p.location,p.country,p.year,u.user_id,u.first_name,u.last_name
                from res_orders o 
                LEFT JOIN product p  ON p.product_id=o.product_id 
                LEFT JOIN users u ON o.user_id=u.user_id 
                WHERE o.type=2 AND o.order_date >='".$fromdate."' AND o.order_date<='".$todate."'";
       
	$response = $wb->getAllData($sql);
        if($response){
            foreach ($response as $key => $val) {
                $data[$key]['user_name'] = $val['first_name'] . " " . $val['last_name'];
                $data[$key]['user_id'] = $val['user_id'];
                $data[$key]['product_name'] = $val['product_name'];
                $data[$key]['order_qty'] = $val['order_qty'];
                $data[$key]['order_total'] = $val['order_total'];
                $data[$key]['orderdate'] = $val['order_date'];
            }
        }
	$this->render('index', array(
            'pid' => $pid, 'order_date' => $datefilter, 'orderdata' => $data
        ));
    }
    public function actionpaymentreport() {
        $pid = '';
        $datefilter = '';
        if(isset($_GET['payment']) && $_GET['payment']!=''){
            $datefilter = $_GET['payment'];
        }
        if($datefilter == 'today'){
            $fromdate = date("Y-m-d h:i:s");
            $todate = date("Y-m-d h:i:s");
        }else if($datefilter == 'yesterday'){
           $fromdate = date("Y-m-d h:i:s",  strtotime("-1 day"));
           $todate = date("Y-m-d h:i:s");
        }else if($datefilter == 'last 7 day'){
           $fromdate = date("Y-m-d h:i:s",  strtotime("-8 days"));
           $todate = date("Y-m-d h:i:s");
        }else{
           $datefilter = 'this month';
           $fromdate = date("Y-m-d h:i:s",  mktime(0,0,0,date("m"),1,date("Y")));
           $todate = date("Y-m-d h:i:s");
        }
        
        $wb = new Webservice();
        $data = array();
        $sql = "select o.order_total_id,o.order_total,o.order_createdon,o.payment_status,o.order_status,
                o.payment_response,u.first_name,u.last_name,u.user_id
                from res_order_total o 
                LEFT JOIN users u ON o.user_id=u.user_id 
                WHERE o.order_createdon >='".$fromdate."' AND o.order_createdon<='".$todate."'";
        
        $response = $wb->getAllData($sql);
        if($response){
            foreach ($response as $key => $val) {
                $data[$key]['user_name'] = $val['first_name'] . " " . $val['last_name'];
                $data[$key]['user_id'] = $val['user_id'];
                $data[$key]['order_createdon'] = $val['order_createdon'];
                $data[$key]['order_total'] = $val['order_total'];
                $data[$key]['order_status'] = $val['order_status'];
                $data[$key]['payment_status'] = $val['payment_status'];
                $data[$key]['payment_response'] = $val['payment_response'];
            }
        }
	else {
	    $response = array();
	}
        $this->render('index', array(
            'pid' => $pid, 'payment_date' => $datefilter, 'paymentdata' => $data
        ));
    }
    /*
     * @desc : Generate Excel file
     */

    public function actionexport() {
        require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "excelworksheet" . DIRECTORY_SEPARATOR . "Worksheet.php");
        require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "excelworksheet" . DIRECTORY_SEPARATOR . "Workbook.php");
        $filename = 'subscription_report';
        // Creating a workbook
        $XLSfilename = $filename;
        $XLSfile = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'export' . DIRECTORY_SEPARATOR . $XLSfilename . '.xls';
        $workbook = new Workbook($XLSfile);
        // Creating the second worksheet
        $worksheet2 = & $workbook->add_worksheet('Export Passenger Report');
        // Format for the headings
        $formatot = & $workbook->add_format();
        $formatot->set_bold('1');
        $formatot->set_size(10);
    
        $worksheet2->write_string(0, 0, "#", $formatot);
        $worksheet2->write_string(0, 1, "User Id", $formatot);
        $worksheet2->write_string(0, 2, "User Name", $formatot);
        $worksheet2->write_string(0, 3, "Subscription Name", $formatot);
        $worksheet2->write_string(0, 4, "Subscription Type", $formatot);
        $worksheet2->write_string(0, 5, "Duration", $formatot);
        $worksheet2->write_string(0, 6, "Date", $formatot);
        
        $pid = '';

        $wb = new Webservice();
        $data = array();
        $pid = '';
        if (isset($_GET['sid']) && $_GET['sid'] != 'all') {
            $pid = $_GET['sid'];
            $cond = " AND plan_id ='".$pid."'";
        } else {
            $cond = '';
        }
        $postb = "SELECT sp.*,o.subscription_duration,o.order_total,o.order_qty,sp.plan_name,sp.plan_type,
                    o.order_date,o.address_id,u.first_name,u.last_name,u.user_id
                    FROM `res_orders` o
                    LEFT JOIN `subscription_plan` sp ON o.subscription_plan_id=sp.plan_id
                    LEFT JOIN users u ON o.user_id=u.user_id
                    WHERE o.type=1 ".$cond;
        
        $response = $wb->getAllData($postb);
        $i=1;
        if ($response) {
            $colscnt = 1;
            foreach ($response as $key => $val) {
                $formatot = & $workbook->add_format();
                $formatot->set_bold('1');
                $formatot->set_size(10);
                $worksheet2->write_string($colscnt, 0, $i++, $formatot);
                $worksheet2->write_string($colscnt, 1, $val['user_id'], $formatot);
                $worksheet2->write_string($colscnt, 2, $val['first_name']." ".$val['last_name'], $formatot);
                $worksheet2->write_string($colscnt, 3, $val['plan_name'], $formatot);
                $worksheet2->write_string($colscnt, 4, $val['plan_type'], $formatot);
                $worksheet2->write_string($colscnt, 5, $val['subscription_duration'], $formatot);
                $worksheet2->write_string($colscnt, 6, $val['order_date'], $formatot);
                //Count update
                $colscnt++;
            }
        }
        //*************************************************************************************************************************
        $workbook->close();
        // Download 
        $size = filesize($XLSfile);
        ob_end_clean();

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment; filename=\"" . $XLSfilename . ".xls\";");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . $size);

        // Spill the contents
        $fp = fopen($XLSfile, "rb");
        while (!feof($fp)) {
            echo fread($fp, 8192);
        }
        fclose($fp);
        exit;
    }
    public function actionexportorder() {
        require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "excelworksheet" . DIRECTORY_SEPARATOR . "Worksheet.php");
        require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "excelworksheet" . DIRECTORY_SEPARATOR . "Workbook.php");
        $filename = 'order_report';
        // Creating a workbook
        $XLSfilename = $filename;
        $XLSfile = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'export' . DIRECTORY_SEPARATOR . $XLSfilename . '.xls';
        $workbook = new Workbook($XLSfile);
        // Creating the second worksheet
        $worksheet2 = & $workbook->add_worksheet('Export Passenger Report');
        // Format for the headings
        $formatot = & $workbook->add_format();
        $formatot->set_bold('1');
        $formatot->set_size(10);
    
        $worksheet2->write_string(0, 0, "#", $formatot);
        $worksheet2->write_string(0, 1, "User Id", $formatot);
        $worksheet2->write_string(0, 2, "User Name", $formatot);
        $worksheet2->write_string(0, 3, "Product Name", $formatot);
        $worksheet2->write_string(0, 4, "Order Qty", $formatot);
        $worksheet2->write_string(0, 5, "Order Total(Yuan)", $formatot);
        $worksheet2->write_string(0, 6, "Date", $formatot);
        
        $pid = '';
        
        if(isset($_GET['order']) && $_GET['order']!=''){
            $datefilter = $_GET['order'];
        }
        if($datefilter == 'today'){
            $fromdate = date("Y-m-d h:i:s");
            $todate = date("Y-m-d h:i:s");
        }else if($datefilter == 'yesterday'){
           $fromdate = date("Y-m-d h:i:s",  strtotime("-1 day"));
           $todate = date("Y-m-d h:i:s");
        }else if($datefilter == 'last 7 day'){
           $fromdate = date("Y-m-d h:i:s",  strtotime("-8 days"));
           $todate = date("Y-m-d h:i:s");
        }else{
           $datefilter = 'this month';
           $fromdate = date("Y-m-d h:i:s",  mktime(0,0,0,date("m"),1,date("Y")));
           $todate = date("Y-m-d h:i:s");
        }
        
        $wb = new Webservice();
        $data = array();
        $sql = "select o.order_id,o.order_total,o.order_date,o.payment_method,o.order_qty,o.address_id,
                o.order_date,p.product_name,p.price,p.image,p.location,p.country,p.year,u.user_id,u.first_name,u.last_name
                from res_orders o 
                LEFT JOIN product p  ON p.product_id=o.product_id 
                LEFT JOIN users u ON o.user_id=u.user_id 
                WHERE o.type=2 AND o.order_date >='".$fromdate."' AND o.order_date<='".$todate."'";
        $response = $wb->getAllData($sql);
        $i=1;
        if ($response) {
            $colscnt = 1;
            foreach ($response as $key => $val) {
                $formatot = & $workbook->add_format();
                $formatot->set_bold('1');
                $formatot->set_size(10);
                $worksheet2->write_string($colscnt, 0, $i++, $formatot);
                $worksheet2->write_string($colscnt, 1, $val['user_id'], $formatot);
                $worksheet2->write_string($colscnt, 2, $val['first_name']." ".$val['last_name'], $formatot);
                $worksheet2->write_string($colscnt, 3, $val['product_name'], $formatot);
                $worksheet2->write_string($colscnt, 4, $val['order_qty'], $formatot);
                $worksheet2->write_string($colscnt, 5, $val['order_total'], $formatot);
                $worksheet2->write_string($colscnt, 6, $val['order_date'], $formatot);
                //Count update
                $colscnt++;
            }
        }
        //*************************************************************************************************************************
        $workbook->close();
        // Download 
        $size = filesize($XLSfile);
        ob_end_clean();

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment; filename=\"" . $XLSfilename . ".xls\";");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . $size);

        // Spill the contents
        $fp = fopen($XLSfile, "rb");
        while (!feof($fp)) {
            echo fread($fp, 8192);
        }
        fclose($fp);
        exit;
    }
    public function actionexportpayment() {
        require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "excelworksheet" . DIRECTORY_SEPARATOR . "Worksheet.php");
        require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "excelworksheet" . DIRECTORY_SEPARATOR . "Workbook.php");
        $filename = 'payment_report';
        // Creating a workbook
        $XLSfilename = $filename;
        $XLSfile = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'export' . DIRECTORY_SEPARATOR . $XLSfilename . '.xls';
        $workbook = new Workbook($XLSfile);
        // Creating the second worksheet
        $worksheet2 = & $workbook->add_worksheet('Export Passenger Report');
        // Format for the headings
        $formatot = & $workbook->add_format();
        $formatot->set_bold('1');
        $formatot->set_size(10);
    
        $worksheet2->write_string(0, 0, "#", $formatot);
        $worksheet2->write_string(0, 1, "User Id", $formatot);
        $worksheet2->write_string(0, 2, "User Name", $formatot);
        $worksheet2->write_string(0, 3, "Order Total(Yuan)", $formatot);
        $worksheet2->write_string(0, 4, "Order Status", $formatot);
        $worksheet2->write_string(0, 5, "Payment Response", $formatot);
        $worksheet2->write_string(0, 6, "Date", $formatot);
        
        $pid = '';
        
        if(isset($_GET['payment']) && $_GET['payment']!=''){
            $datefilter = $_GET['payment'];
        }
        if($datefilter == 'today'){
            $fromdate = date("Y-m-d h:i:s");
            $todate = date("Y-m-d h:i:s");
        }else if($datefilter == 'yesterday'){
           $fromdate = date("Y-m-d h:i:s",  strtotime("-1 day"));
           $todate = date("Y-m-d h:i:s");
        }else if($datefilter == 'last 7 day'){
           $fromdate = date("Y-m-d h:i:s",  strtotime("-8 days"));
           $todate = date("Y-m-d h:i:s");
        }else{
           $datefilter = 'this month';
           $fromdate = date("Y-m-d h:i:s",  mktime(0,0,0,date("m"),1,date("Y")));
           $todate = date("Y-m-d h:i:s");
        }
        
        $wb = new Webservice();
        $data = array();
        $sql = "select o.order_total_id,o.order_total,o.order_createdon,o.payment_status,o.order_status,
                o.payment_response,u.first_name,u.last_name,u.user_id
                from res_order_total o 
                LEFT JOIN users u ON o.user_id=u.user_id 
                WHERE o.order_createdon >='".$fromdate."' AND o.order_createdon<='".$todate."'";
      
        $response = $wb->getAllData($sql);
        $i=1;
        if ($response) {
            $colscnt = 1;
            foreach ($response as $key => $val) {
                $formatot = & $workbook->add_format();
                $formatot->set_bold('1');
                $formatot->set_size(10);
                $worksheet2->write_string($colscnt, 0, $i++, $formatot);
                $worksheet2->write_string($colscnt, 1, $val['user_id'], $formatot);
                $worksheet2->write_string($colscnt, 2, $val['first_name']." ".$val['last_name'], $formatot);
                $worksheet2->write_string($colscnt, 3, $val['order_total'], $formatot);
                $worksheet2->write_string($colscnt, 4, $val['order_status'], $formatot);
                $worksheet2->write_string($colscnt, 5, $val['payment_response'], $formatot);
                $worksheet2->write_string($colscnt, 6, $val['order_createdon'], $formatot);
                //Count update
                $colscnt++;
            }
        }
        //*************************************************************************************************************************
        $workbook->close();
        // Download 
        $size = filesize($XLSfile);
        ob_end_clean();

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment; filename=\"" . $XLSfilename . ".xls\";");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . $size);

        // Spill the contents
        $fp = fopen($XLSfile, "rb");
        while (!feof($fp)) {
            echo fread($fp, 8192);
        }
        fclose($fp);
        exit;
    }
}