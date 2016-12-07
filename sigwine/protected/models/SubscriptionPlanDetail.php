<?php

/**
 * This is the model class for table "subscription_plan_detail".
 *
 * The followings are the available columns in table 'subscription_plan_detail':
 * @property integer $detail_id
 * @property integer $plan_id
 * @property integer $product_id
 * @property integer $display_order
 */
class SubscriptionPlanDetail extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SubscriptionPlanDetail the static model class
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
		return 'subscription_plan_detail';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('plan_id,product_id,month,year', 'required'),
			array('plan_id,display_order', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('detail_id, plan_id, product_id, display_order,month,year', 'safe', 'on'=>'search'),
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
                    'Plan' => array(self::BELONGS_TO, 'SubscriptionPlan', 'plan_id'),
                    'Product' => array(self::BELONGS_TO, 'Product', 'product_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'detail_id' => 'Detail',
			'plan_id' => 'Plan',
			'product_id' => 'Product',
                        'month' => 'Month',
                        'year' => 'Year',
			'display_order' => 'Display Order',
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

		$criteria->compare('detail_id',$this->detail_id);
		$criteria->compare('plan_id',$this->plan_id);
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('display_order',$this->display_order);
               // $criteria->group="plan_id";
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        public function getProductName($id){
            $wp = new Webservice();
            $sql="select spd.product_id as product_id,p.product_name,p.image,p.short_desc,p.long_desc,p.price,p.wine_color,p.wine_taste from subscription_plan_detail spd,product p where p.product_id=spd.product_id and spd.plan_id='".$id."' group by spd.product_id";
            $subrow = $wp->getAllData($sql);
            if($subrow){
                foreach($subrow as $sk=>$sv){
                    $productArr[]=$sv["product_name"];
                }
                return implode(",",$productArr);
             }
            else {
                return false;
            }
        }
}