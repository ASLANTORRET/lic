<?php

/**
 * This is the model class for table "tbl_results".
 *
 * The followings are the available columns in table 'tbl_results':
 * @property integer $id
 * @property string $subscriber_id
 * @property string $question_id
 * @property string $character
 * @property string $note
 * @property string $sent_date
 * @property string $answer_date
 * @property string $category_id
 */
class Results extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_results';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, subscriber_id, question_id, character, note, sent_date, answer_date, category_id', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
            'subscriber_id' => 'Подписчик',
            'question_id' => 'Вопрос',
            'character' => 'Характер',
            'answer_date' => 'Время ответа',
            'sent_date' => 'Время отправки',
            'note' => 'Заметка',
            'category_id' => 'Категория'
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('subscriber_id',$this->subscriber_id);
		$criteria->compare('question_id',$this->question_id);
        $criteria->compare('category_id',$this->question_id);
        $criteria->compare('character',$this->character);
        $criteria->compare('note',$this->note,true);
        $criteria->compare('sent_date',$this->sent_date,true);
        $criteria->compare('answer_date',$this->answer_date,true);


		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>40
            )
		));
	}


    public function getAttrByParam($condition = null, $options = null){

        $condition_params = $condition;

        if($condition_params == NULL){
            die( "Condition is empty");
        }


        $keys = array_keys($condition_params);

        foreach($keys as $index => $value){
            $keys[$index] = $value . "=:" . $value;
        }

        $condition = implode(" AND ", $keys);

        $sql = array("condition" => $condition,
            "params" => $condition_params);


        if($number = $this::model()->count($sql)){

            if($options != null){
                $sql = array_merge($sql, $options);
            }

            if($number > 1){

                $result_arr = array();

                $resultSet = $this::model()->findAll($sql);

                foreach($resultSet as $value){
                    $result_arr[]  = $value;
                }

                return array($withRecord = true,$result_arr);
            }

            else{
                $resultSet = $this::model()->find($sql);

                return array($withRecord = true, array($resultSet));
            }
        }
        else{
            return array($withRecord = false);
        }

    }


    public static function getDataByPk($pk, $columnn){

        if($category = Results::model()->findByPk($pk)){

            return $category->$columnn;
        }
        else{

            return "";
        }
    }

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MessagesLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
