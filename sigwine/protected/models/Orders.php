<?php

/**
 * This is the model class for table "orders".
 *
 * The followings are the available columns in table 'orders':
 * @property integer $order_id
 * @property integer $user_id
 * @property integer $subscription_plan_id
 * @property string $subscription_duration
 * @property string $order_total
 * @property integer $order_qty
 * @property string $type
 * @property string $order_status
 * @property string $order_date
 * @property integer $order_createdby
 * @property integer $address_id
 * @property string $payment_method
 */
class Orders extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Orders the static model class
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
		return 'orders';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, subscription_plan_id, subscription_duration, order_total, order_qty, type, order_status, order_date, order_createdby, address_id, payment_method', 'required'),
			array('user_id, subscription_plan_id, order_qty, order_createdby, address_id', 'numerical', 'integerOnly'=>true),
			array('subscription_duration, order_total, payment_method', 'length', 'max'=>255),
			array('type', 'length', 'max'=>1),
			array('order_status', 'length', 'max'=>7),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('order_id, user_id, subscription_plan_id, subscription_duration, order_total, order_qty, type, order_status, order_date, order_createdby, address_id, payment_method', 'safe', 'on'=>'search'),
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
			'order_id' => 'Order',
			'user_id' => 'User',
			'subscription_plan_id' => 'Subscription Plan',
			'subscription_duration' => 'Subscription Duration',
			'order_total' => 'Order Total',
			'order_qty' => 'Order Qty',
			'type' => 'Type',
			'order_status' => 'Order Status',
			'order_date' => 'Order Date',
			'order_createdby' => 'Order Createdby',
			'address_id' => 'Address',
			'payment_method' => 'Payment Method',
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

		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('subscription_plan_id',$this->subscription_plan_id);
		$criteria->compare('subscription_duration',$this->subscription_duration,true);
		$criteria->compare('order_total',$this->order_total,true);
		$criteria->compare('order_qty',$this->order_qty);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('order_status',$this->order_status,true);
		$criteria->compare('order_date',$this->order_date,true);
		$criteria->compare('order_createdby',$this->order_createdby);
		$criteria->compare('address_id',$this->address_id);
		$criteria->compare('payment_method',$this->payment_method,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}