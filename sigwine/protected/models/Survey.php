<?php

/**
 * This is the model class for table "survey".
 *
 * The followings are the available columns in table 'survey':
 * @property integer $survey_id
 * @property string $survey_name
 * @property string $survey_start
 * @property string $survey_end
 * @property string $status
 * @property integer $created_on
 */
class Survey extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Survey the static model class
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
		return 'survey';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('survey_name,status', 'required'),
			array('survey_name', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('survey_id, survey_name, survey_start, survey_end, status, created_on', 'safe', 'on'=>'search'),
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
			'survey_id' => 'Survey',
			'survey_name' => 'Survey Name',
			'survey_start' => 'Survey Start',
			'survey_end' => 'Survey End',
			'status' => 'Status',
			'created_on' => 'Created On',
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

		$criteria->compare('survey_id',$this->survey_id);
		$criteria->compare('survey_name',$this->survey_name,true);
		$criteria->compare('survey_start',$this->survey_start,true);
		$criteria->compare('survey_end',$this->survey_end,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('created_on',$this->created_on);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}