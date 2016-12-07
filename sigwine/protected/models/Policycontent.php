<?php

/**
 * This is the model class for table "policycontent".
 *
 * The followings are the available columns in table 'policycontent':
 * @property integer $policycontent_id
 * @property string $policy_title
 * @property string $policy_code
 * @property string $policy_description
 */
class Policycontent extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Policycontent the static model class
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
		return 'policycontent';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('policy_title, policy_code, policy_description', 'required'),
			array('policy_title, policy_code', 'length', 'max'=>255),
                        array('policy_code', 'unique'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('policycontent_id, policy_title, policy_code, policy_description', 'safe', 'on'=>'search'),
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
			'policycontent_id' => 'Policycontent',
			'policy_title' => 'Policy Title',
			'policy_code' => 'Policy Code',
			'policy_description' => 'Policy Description',
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

		$criteria->compare('policycontent_id',$this->policycontent_id);
		$criteria->compare('policy_title',$this->policy_title,true);
		$criteria->compare('policy_code',$this->policy_code,true);
		$criteria->compare('policy_description',$this->policy_description,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}