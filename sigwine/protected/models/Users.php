<?php

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property integer $user_id
 * @property string $email
 * @property string $password
 * @property integer $login_type
 * @property string $first_name
 * @property string $last_name
 * @property string $profile_image
 * @property string $language_preference
 * @property string $created_on
 * @property string $last_modified_on
 */
class Users extends UserMaster
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Users the static model class
	 */
	 public $notifications;
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'users';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('email, password, first_name, last_name', 'required'),
			array('login_type', 'numerical', 'integerOnly'=>false),
			array('email, password, first_name, last_name', 'length', 'max'=>255),
                        array('email', 'email','message'=>"Please enter valid email address"),
                        //array('email', 'uniqueEmail','message'=>'Email already exists!'),
                        array('profile_image', 'file', 'allowEmpty'=>true,'maxFiles'=> 100,  'types'=>'jpg,jpeg,gif,png'),
			//array('language_preference', 'length', 'max'=>2),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('email, first_name, last_name', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'user_id' => 'Users',
			'email' => 'Email',
			'password' => 'Password',
			'login_type' => 'Login Type',
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'profile_image' => 'Profile Image',
			'language_preference' => 'Language Preference',
			'created_on' => 'Created On',
			'last_modified_on' => 'Last Modified On',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;
		$criteria->compare('email',$this->email,true);
		$criteria->compare('first_name',$this->first_name,true);
		$criteria->compare('last_name',$this->last_name,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}