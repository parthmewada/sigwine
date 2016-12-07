<?php

/**
 * This is the model class for table "product".
 *
 * The followings are the available columns in table 'product':
 * @property integer $product_id
 * @property string $product_name
 * @property string $image
 * @property string $short_desc
 * @property string $long_desc
 * @property string $price
 * @property string $product_type
 * @property string $flash_sale_product
 * @property string $wine_color
 * @property string $wine_taste
 * @property string $status
 */
class Product extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Product the static model class
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
		return 'product';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			/*array('product_name,short_desc, long_desc, price, product_type, status,product_name_cn,short_desc_cn, long_desc_cn', 'required'),*/
			array('product_name, price, wine_color,product_type, wine_taste,type,varietal,location,country,year,glassware,type_cn,varietal_cn,
                            location_cn,country_cn,glassware_cn,product_name_cn', 'length', 'max'=>255),
      			array("price",'numerical', 'integerOnly'=>true),
			array("long_desc,long_desc_cn,status,short_desc,short_desc_cn","safe"),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('product_id, product_name, image, short_desc, long_desc,
                            video, price, product_type, flash_sale_product, wine_color, 
                            wine_taste,type,varietal,location,country,year,glassware,status,
                            product_name_cn,short_desc_cn, long_desc_cn,type_cn,varietal_cn,
                            location_cn,country_cn,glassware_cn', 'safe', 'on'=>'search'),
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
			'product_id' => 'Product',
			'product_name' => 'Product Name',
                        'product_name_cn' => 'Product Name (Chinese)',
			'image' => 'Image',
			'short_desc' => 'Short Desc',
                        'short_desc_cn' => 'Short Desc (Chinese)',
			'long_desc' => 'Long Desc',
                        'long_desc_cn' => 'Long Desc (Chinese)',
			'price' => 'Price (¥)',
			'product_type' => 'Product Type',
			'flash_sale_product' => 'Flash Sale Product',
                        'video' => 'Detail Video',
			'wine_color' => 'Wine Color',
			'type' => 'Type',
                        'type_cn' => 'Type (Chinese)',
                        'location' => 'Location',
                        'location_cn' => 'Location (Chinese)',
                        'country' => 'Country',
                        'country_cn' => 'Country (Chinese)',
                        'glassware' => 'Glassware',
                        'glassware_cn' => 'Glassware (Chinese)',
                        'varietal' => 'Varietal',
                        'varietal_cn' => 'Varietal (Chinese)',
			'year' => 'Year',
			'status' => 'Status',
		);
	}
        public function getProductData($id){
            //$wb=new Webservice();
            //$sele="select * from product where product_id='".$id."'";
            //$userData=$wb->getRowData($sele);
            //return $userData["product_name"];
	    $data = Product::model()->findByPk($id);
	    return $data->product_name;
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

		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('product_name',$this->product_name,true);
		$criteria->compare('image',$this->image,true);
		$criteria->compare('short_desc',$this->short_desc,true);
		$criteria->compare('long_desc',$this->long_desc,true);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('product_type',$this->product_type,true);
		$criteria->compare('flash_sale_product',$this->flash_sale_product,true);
		$criteria->compare('wine_color',$this->wine_color,true);
		$criteria->compare('wine_taste',$this->wine_taste,true);
		$criteria->compare('status',$this->status,true);
                
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        
}
