<?php

/**
 * This is the model class for table "education".
 *
 * The followings are the available columns in table 'education':
 * @property integer $education_id
 * @property string $title
 * @property string $description
 * @property string $file
 * @property string $upload_type
 * @property integer $display_order
 */
class Education extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Education the static model class
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
		return 'education';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, description, upload_type,thumb,title_cn, description_cn', 'required'),
			array('display_order', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>255),
                        array('file', 'file', 'allowEmpty'=>true,'maxFiles'=> 100),
                        array('thumb', 'file', 'allowEmpty'=>true,'maxFiles'=> 100),
                        // The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('title', 'safe', 'on'=>'search'),
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
			'education_id' => 'Education',
			'title' => 'Title',
			'description' => 'Description',
                        'title_cn' => 'Title (Chinese)',
			'description_cn' => 'Description (Chinese)',
			'file' => 'File',
                        'thumb' => 'Thumb',
			'upload_type' => 'Upload Type',
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

		$criteria->compare('education_id',$this->education_id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('file',$this->file,true);
		$criteria->compare('upload_type',$this->upload_type,true);
		$criteria->compare('display_order',$this->display_order);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}