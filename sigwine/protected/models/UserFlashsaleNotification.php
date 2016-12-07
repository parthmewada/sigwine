<?php

/**
 * This is the model class for table "user_flashsale_notification".
 *
 * The followings are the available columns in table 'user_flashsale_notification':
 * @property integer $ufid
 * @property integer $user_id
 * @property integer $flash_sale_id
 * @property string $email
 * @property string $device_type
 * @property string $device_token
 * @property integer $send_flag
 * @property string $sale_start_from
 */
class UserFlashsaleNotification extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_flashsale_notification';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, flash_sale_id, email, device_type, device_token, send_flag, sale_start_from', 'required'),
			array('user_id, flash_sale_id, send_flag', 'numerical', 'integerOnly'=>true),
			array('email', 'length', 'max'=>100),
			array('device_type', 'length', 'max'=>25),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('ufid, user_id, flash_sale_id, email, device_type, device_token, send_flag, sale_start_from', 'safe', 'on'=>'search'),
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
			'ufid' => 'Ufid',
			'user_id' => 'User',
			'flash_sale_id' => 'Flash Sale',
			'email' => 'Email',
			'device_type' => 'Device Type',
			'device_token' => 'Device Token',
			'send_flag' => 'Send Flag',
			'sale_start_from' => 'Sale Start From',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('ufid',$this->ufid);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('flash_sale_id',$this->flash_sale_id);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('device_type',$this->device_type,true);
		$criteria->compare('device_token',$this->device_token,true);
		$criteria->compare('send_flag',$this->send_flag);
		$criteria->compare('sale_start_from',$this->sale_start_from,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserFlashsaleNotification the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
