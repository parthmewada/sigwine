<?php

class HomeController extends Controller {

    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }
    /**
     * @desc : access rule 
     */
     public function accessRules() {
        $session=new CHttpSession;
        $session->open();
          return array(  array('allow',  // allow all users to perform 'index' and 'view' actions
                                'actions'=>array('index','view'),
                                'users'=>array($session["fullname"]),
			),
                        array('deny',  // deny all users
				'users'=>array('*'),
			)
              );
     }
    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {
        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/layouts/main.php'
        $session = new CHttpSession;
        $session->open();
        $this->render('index', array('username' => $session["fullname"]));
    }

}