<?php

/**
 * This is the model class for table "flash_sale".
 *
 * The followings are the available columns in table 'flash_sale':
 * @property integer $flash_sale_id
 * @property string $title
 * @property integer $product_id
 * @property string $sale_start_from
 * @property string $sale_end
 * @property string $status
 */
class FlashSale extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return FlashSale the static model class
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
		return 'flash_sale';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, product_id, sale_start_from, sale_end, status', 'required'),
			array('product_id', 'numerical', 'integerOnly'=>true),
                        array('sale_start_from', 'checkValidation'),
                        array('sale_end', 'checkValidation'),
			array('title', 'length', 'max'=>255),
			array('status', 'length', 'max'=>8),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('flash_sale_id, title, product_id, sale_start_from, sale_end, status', 'safe', 'on'=>'search'),
		);
	}
        public function checkValidation($attribute_name,$params){
            $extraCond="";
            if(isset($this->flash_sale_id)){
                $extraCond=" AND flash_sale_id!='".$this->flash_sale_id."'";
            }
            $subsql = "select fs.*,p.* from  flash_sale fs
                     LEFT JOIN product p ON p.product_id=fs.product_id
                     where fs.sale_start_from<='".$this->sale_start_from."' 
                     and fs.sale_end>='".$this->sale_end."' $extraCond";
            $sum =  $this->model()->findBySql($subsql);

            $row=$sum->attributes;
             if($row){
                $this->addError($attribute_name,'Flash sale is already running from '.$row["sale_start_from"].' to '.$row['sale_end'].'.Please enter valid Sale Start From date!');
             }
	}
        public function checkEndValidation($attribute_name,$params){
            $extraCond="";
            if(isset($this->flash_sale_id)){
               $extraCond=" AND flash_sale_id!='".$this->flash_sale_id."'";
            }
            $sum =  $this->model()->findBySql("SELECT * 
                    FROM `flash_sale` WHERE (sale_start_from between '$this->sale_start_from' 
                        and '$this->sale_end') $extraCond");
            $row=$sum->attributes;
	    if($row){
                $this->addError('sale_end','Flash sale is already running from '.$row["sale_start_from"].' to '.$row['sale_end'].'.Please enter valid Sale End date!');
	    }
	}
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
                    'Product' => array(self::BELONGS_TO, 'Product', 'product_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'flash_sale_id' => 'Exclusive',
			'title' => 'Title',
			'product_id' => 'Product',
			'sale_start_from' => 'Sale Start From',
			'sale_end' => 'Sale End',
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

		$criteria->compare('flash_sale_id',$this->flash_sale_id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('sale_start_from',$this->sale_start_from,true);
		$criteria->compare('sale_end',$this->sale_end,true);
		$criteria->compare('status',$this->status,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}