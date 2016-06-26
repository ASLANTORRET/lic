<?php

/**
 * This is the model class for table "tbl_questions".
 *
 * The followings are the available columns in table 'tbl_questions':
 * @property integer $id
 * @property string $question
 * @property string $category_id
 * @property string $variant1
 * @property string $variant2
 * @property string $variant3
 * @property string $response1
 * @property string $response2
 * @property string $response3
 * @property string $character1
 * @property string $character2
 * @property string $character3
 * @property string $create_time
 * @property string $update_time
 */
class Questions extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_questions';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('question, category_id, variant1, variant2, variant3, response1, response2, response3, character1, character2, character3', 'required'),
			array('id, question, category_id, variant1, variant2, variant3, response1, response2, response3, character1, character2, character3, create_time, update_time', 'safe', 'on'=>'search'),
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
			'question' => 'Вопрос',
            'category_id' => 'Категория',
            'status' => 'Статус',

            'variant1' => 'Вар.№1',
            'variant2' => 'Вар.№2',
            'variant3' => 'Вар.№3',

            'response1' => 'Ответ№1',
            'response2' => 'Ответ№2',
            'response3' => 'Ответ№3',

            'character1' => 'Хар.№1',
            'character2' => 'Хар.р№2',
            'character3' => 'Хар.№3',

            'create_time' => 'Время создания',
            'update_time' => 'Время посл. обновления',
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
		$criteria->compare('question',$this->question,true);
        $criteria->compare('category_id',$this->category_id,true);

		$criteria->compare('variant1',$this->variant1,true);
        $criteria->compare('variant2',$this->variant2,true);
        $criteria->compare('variant3',$this->variant3,true);

        $criteria->compare('response1',$this->response1,true);
        $criteria->compare('response2',$this->response2,true);
        $criteria->compare('response3',$this->response3,true);

        $criteria->compare('character1',$this->character1,true);
        $criteria->compare('character2',$this->character2,true);
        $criteria->compare('character3',$this->character3,true);

        $criteria->compare('create_time',$this->create_time, true);
        $criteria->compare('update_time',$this->update_time, true);


		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>40
            )
		));
	}

    public static function getShortVersionQ($pk){
        $length = 225;
        if(Questions::model()->findByPk($pk)){
            $content_length = strlen($content = Questions::model()->findByPk($pk)->question);
            echo mb_substr($content, 0 , (int)$length, "utf-8") . (($content_length>=$length)?("<a href='". $pk ."' title='" . $content ."'>...></a>"):"");
        }
    }

    public static function getShortVersion($content){

        $length = 100;

        $content_length = strlen($content);
        echo mb_substr($content, 0 , (int)$length, "utf-8") . (($content_length>=$length)?("<a href='' title='" . $content ."'>...></a>"):"");

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


        if($number = Questions::model()->count($sql)){

            if($options != null){
                $sql = array_merge($sql, $options);
            }

            if($number > 1){

                $result_arr = array();

                $resultSet = Questions::model()->findAll($sql);

                foreach($resultSet as $value){
                    $result_arr[]  = $value;
                }

                return array($withRecord = true,$result_arr);
            }

            else{
                $resultSet = Questions::model()->find($sql);

                return array($withRecord = true, array($resultSet));
            }
        }
        else{
            return array($withRecord = false);
        }

    }

    protected function beforeSave(){
        if(parent::beforeSave()){

            $today = new DateTime(date("Y-m-d H:i:s"));
            $today->setTimezone(new DateTimeZone('Asia/Almaty'));

            if($this->isNewRecord){

                $this->create_time = $this->update_time = $today->format("Y-m-d H:i:s");
            }
            else{
                $this->update_time = $today->format("Y-m-d H:i:s");
            }

            return true;
        }
        else{
            return false;
        }
    }

    public static function getCharacter($pk, $characterID){

        if($characterID != NULL){
            return Questions::getDataByPk($pk, "character"  . $characterID);
        }

    }

    public static function getDataByPk($pk, $columnn){

        if($category = Questions::model()->findByPk($pk)){

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
