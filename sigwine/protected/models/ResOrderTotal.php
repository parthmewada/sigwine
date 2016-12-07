<?php

/**
 * This is the model class for table "res_order_total".
 *
 * The followings are the available columns in table 'res_order_total':
 * @property integer $order_total_id
 * @property integer $user_id
 * @property integer $order_total
 * @property integer $payment_status
 * @property integer $order_status
 * @property integer $payment_response
 * @property integer $order_createdby
 * @property integer $order_createdon
 */
class ResOrderTotal extends CActiveRecord
{
	public $userSearch;
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ResOrderTotal the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'res_order_total';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, order_total, payment_status, order_status, payment_response, order_createdby, order_createdon', 'required'),
			array('user_id, order_total, payment_status, order_status, payment_response, order_createdby, order_createdon', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('order_total_id, user_id, userSearch , order_total, payment_status, order_status ', 'safe', 'on'=>'search'),
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
                    'users' => array(self::BELONGS_TO, 'Users', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'order_total_id' => 'Order Id',
			'user_id' => 'User Id',
			'order_total' => 'Order Total (¥)',
			'payment_status' => 'Payment Status',
			'order_status' => 'Order Status',
			'payment_response' => 'Payment Response',
			'order_createdby' => 'Order Createdby',
			'order_createdon' => 'Order Created on',
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
		$criteria->with = array('users');
		$criteria->compare('users.user_id',$this->user_id);
		$criteria->compare('users.first_name', $this->userSearch, true);
		$criteria->compare('order_total_id',$this->order_total_id);
		$criteria->compare('order_total',$this->order_total);
		$criteria->compare('payment_status',$this->payment_status);
		$criteria->compare('order_status',$this->order_status);
		$criteria->compare('payment_response',$this->payment_response);
		$criteria->compare('order_createdby',$this->order_createdby);
		$criteria->compare('order_createdon',$this->order_createdon);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
                        'sort'=>array(
                            'defaultOrder'=>'order_total_id DESC',
                        ),
		));
	}
}