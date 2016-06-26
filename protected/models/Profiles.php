<?php

/**
 * This is the model class for table "tbl_profile".
 *
 * The followings are the available columns in table 'tbl_profile':
 * @property integer $id
 * @property integer $subscriber_id
 * @property integer $sex
 * @property integer $age
 * @property integer $relation
 * @property integer $day
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $status

 */
class Profiles extends CActiveRecord
{

    public $phone_number, $shortcode, $charging_id, $category_id;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_profiles';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('subscriber_id, sex, age,  day', 'required'),
			array('subscriber_id, sex, age,  day', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, subscriber_id, sex, age, day, sex_cat, age_cat, status_cat, relation, create_time, update_time, status, phone_number, charging_id, shortcode, category_id, physics', 'safe', 'on'=>'search'),
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
			'subscriber_id' => 'ID Подписчика ',
			'sex' => 'Пол',
            'age' => 'Возраст',
            'sex_cat' => 'Пол категория',
            'relation'=>'Статус отношений',
            'physics' => 'Уровень подготовки',
            'age_cat' => 'Возраст категория',
            'status_cat' => 'Статус категория',
            'create_time'=>'Время создания',
            'update_time'=>'Время послед. обновления',
			'day' => 'День',
            'status' => 'Статус'
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
        $criteria->compare('category_id',$this->category_id);
		$criteria->compare('sex',$this->sex);
        $criteria->compare('relation', $this->relation);
        $criteria->compare('physics', $this->physics);
		$criteria->compare('age',$this->age);
		$criteria->compare('day',$this->day);
        $criteria->compare('create_time',$this->create_time, true);
        $criteria->compare('update_time',$this->update_time, true);
        $criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize' => 25
            )
		));
	}

    /*protected function beforeSave(){
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
    }*/

    //@todo Закончить функцию
    public function getMaxDay($CAT_ID){

        if($max_day = $this->getDbConnection()->createCommand('SELECT MAX(day) FROM tbl_profiles WHERE subscriber_id IN (SELECT id FROM tbl_subscribers WHERE is_subscribed = 1 AND category_id = ' . $CAT_ID.')')->queryScalar()){
            return $max_day;
        }
        else{
            return false;
        }
    }

    public function getMinDay($CAT_ID){

        if($min_day = $this->getDbConnection()->createCommand('SELECT MIN(day) FROM tbl_profiles WHERE subscriber_id IN (SELECT id FROM tbl_subscribers WHERE is_subscribed = 1  AND category_id = ' . $CAT_ID.')')->queryScalar()){
            return $min_day;
        }
        else{
            return false;
        }
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

        if($category = Categories::model()->findByPk($pk)){
            return $category->$columnn;
        }
        else{
            return "";
        }
    }

    public static function assignAttributes($attrubutes){
        $model = new Profiles();
        foreach($attrubutes as $index=>$value){
            $model->$index = $value;
        }
        $model->save(false);
    }

    public static function getAttrBySubID($attr_name = null, $subID = null){

        if($attr_name == NULL){
            die( "First parameter is empty");
        }

        if($subID == NULL){
            die("Second parameter is empty");
        }


        if(!Profiles::model()->exists(array("condition" => "subscriber_id=:si",
            "params" => array(":si" => $subID)))){
            return array(null, "Not registered subscriber_id");
        }

        $profile = Profiles::model()->find(array("condition" => "subscriber_id=:si",
            "params" => array(":si" => $subID)));


        if(count($attr_name) == 1){

            return array($profile->$attr_name);
        }

        else{
            $temp_arr = array();

            foreach($attr_name as $value){
                $temp_arr[] = $profile->$value;
            }

            return $temp_arr;
        }

    }




	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Profiles the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
