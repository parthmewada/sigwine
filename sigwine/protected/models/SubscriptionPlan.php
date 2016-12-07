<?php

/**
 * This is the model class for table "subscription_plan".
 *
 * The followings are the available columns in table 'subscription_plan':
 * @property integer $plan_id
 * @property string $plan_name
 * @property string $plan_type
 * @property string $price
 * @property string $status
 */
class SubscriptionPlan extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SubscriptionPlan the static model class
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
		return 'subscription_plan';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('plan_name, plan_type,description,status,plan_name_cn,description_cn', 'required'),
			array('plan_name, price', 'length', 'max'=>255),
			array('plan_type', 'length', 'max'=>13),
			array('status', 'length', 'max'=>8),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('plan_id, plan_name, plan_type, price, status,image,plan_name_cn,description_cn', 'safe', 'on'=>'search'),
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
			'plan_id' => 'Plan',
			'plan_name' => 'Plan Name',
                        'description' => 'Description',
                        'plan_name_cn' => 'Plan Name (Chinese)',
                        'description_cn' => 'Description (Chinese)',
			'plan_type' => 'Plan Type',
                        'image' => 'Image',
			'price' => 'Price (¥)',
			'status' => 'Status',
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

		$criteria->compare('plan_id',$this->plan_id);
		$criteria->compare('plan_name',$this->plan_name,true);
		$criteria->compare('plan_type',$this->plan_type,true);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('status',$this->status,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        function strtoHTML($str){
          return htmlspecialchars_decode($str);
        }
}