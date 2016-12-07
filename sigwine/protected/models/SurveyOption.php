<?php

/**
 * This is the model class for table "survey_option".
 *
 * The followings are the available columns in table 'survey_option':
 * @property integer $survey_option_id
 * @property integer $survey_id
 * @property integer $survey_question_id
 * @property string $option
 */
class SurveyOption extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SurveyOption the static model class
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
		return 'survey_option';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('survey_id, survey_question_id, option', 'required'),
			array('survey_id, survey_question_id', 'numerical', 'integerOnly'=>true),
			array('option', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('survey_option_id, survey_id, survey_question_id, option', 'safe', 'on'=>'search'),
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
			'survey_option_id' => 'Survey Option',
			'survey_id' => 'Survey',
			'survey_question_id' => 'Survey Question',
			'option' => 'Option',
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

		$criteria->compare('survey_option_id',$this->survey_option_id);
		$criteria->compare('survey_id',$this->survey_id);
		$criteria->compare('survey_question_id',$this->survey_question_id);
		$criteria->compare('option',$this->option,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}