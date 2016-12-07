<?php
<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
date_default_timezone_set('Asia/Chongqing');

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Sigwine App Admin Panel',
        'defaultController' => 'site/login',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
                'ext.easyimage.EasyImage',
		'ext.mailer.PHPMailer',
		'ext.mailer.POP3',
		'ext.mailer.SMTP',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'admin',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			//'ipFilters'=>array('127.0.0.1','64.64.20.42','202.131.112.18','dev.credencys.com'),
		),
		
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
                //this is the json webservice extention use to create webservice and other database 
                // related opearation
                'JsonWebservice'=>array(
                        'class'=>'application.extensions.JsonWebservice.JsonWebservice',
                ),
		// uncomment the following to enable URLs in path-format
		
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                                'payment' => array('site/page/view/index'),
                                'payment/expresscheckout.php' =>array('site/page/view/expresscheckout'),
                                'payment/order_confirm.php' =>array('site/page/view/order_confirm'),
                                'payment/review.php' =>array('site/page/view/review'),
                                'payment/success.php' =>array('site/page/view/success'),
                                'payment/fail.php' =>array('site/page/view/fail'),
                                'returnpolicy' =>array('site/page/view/returnpolicy'),
                                'privacypolicy' =>array('site/page/view/privacypolicy'),
                                'termandcondition' =>array('site/page/view/termandcondition'),
                                 'contactus' =>array('site/page/view/contactus'),
                                
                            
			),
		),
		'bitly' => array(
                    'class' => 'application.extensions.bitly.VGBitly',
                    'login' => 'o_jlh2l1ug0', // login name
                    'apiKey' => 'R_37971bbfd2b246b8be31196d232d9f40', // apikey 
                    'format' => 'json', // default format of the response this can be either xml, json (some callbacks support txt as well)
                ),
                
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=SigwineApp',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => 'Sigwine@2016',
			'charset' => 'utf8',
			'class'   => 'CDbConnection'      
		),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				
				array(
					'class'=>'CWebLogRoute',
				),
				
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
                'SubscriptionPlan'=>array("two_bottles"=>"Two Bottles Subscription","three_bottles"=>"Three Bottles Subscription")
            
	),
);