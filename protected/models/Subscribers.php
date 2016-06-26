<?php

/**
 * This is the model class for table "tbl_subscribers".
 *
 * The followings are the available columns in table 'tbl_subscribers':
 * @property integer $id
 * @property integer $phone_number
 * @property integer $charging_id
 * @property integer $is_subscribed
 * @property string $subscribe_time
 * @property string $unsubscribe_time
 * @property string $subscribe_time_to
 * @property string $unsubscribe_time_to
 * @property string $subscribe_time_from
 * @property string $unsubscribe_time_from
 * @property integer $shortcode
 * @property integer $category_id
 */
class Subscribers extends CActiveRecord
{

    public $subscribe_time_from, $subscribe_time_to, $unsubscribe_time_from, $unsubscribe_time_to, $has_profile, $has_result;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_subscribers';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('phone_number, charging_id, is_subscribed, subscribe_time, shortcode, category_id', 'required'),
			array('charging_id, is_subscribed, shortcode, category_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, phone_number, charging_id, is_subscribed, subscribe_time, unsubscribe_time, subscribe_time_to, unsubscribe_time_to, shortcode, subscribe_time_from, unsubscribe_time_from, category_id, has_result,  has_profile', 'safe', 'on'=>'search'),
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
			'phone_number' => 'Номер',
			'charging_id' => 'Charging',
			'is_subscribed' => 'подписан?',
			'subscribe_time' => 'время подписки',
			'unsubscribe_time' => 'время отписки',
			'shortcode' => 'Короткий номер',
			'category_id' => 'Услуга',
            'has_profile' => 'Есть анкета?',
            'has_result' => 'Есть ответы?'
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
		$criteria->compare('phone_number',$this->phone_number);
		$criteria->compare('charging_id',$this->charging_id);
		$criteria->compare('is_subscribed',$this->is_subscribed);
		$criteria->compare('subscribe_time',$this->subscribe_time,true);
		$criteria->compare('unsubscribe_time',$this->unsubscribe_time,true);
		$criteria->compare('shortcode',$this->shortcode);
		$criteria->compare('category_id',$this->category_id);
        $criteria->compare('subscribe_time', ">=" . $this->subscribe_time_from,true);
        $criteria->compare('subscribe_time', "<=" . $this->subscribe_time_to,true);
        $criteria->compare('unsubscribe_time', ">=" . $this->unsubscribe_time_from,true);
        $criteria->compare('unsubscribe_time', "<=" . $this->unsubscribe_time_to,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination' => array(
                'pageSize' => 25
            )
		));
	}

    public static function getAttrByPhone($attr_name = null, $phone = null){

        if($attr_name == NULL){
            die( "First parameter is empty");
        }

        if($phone == NULL){
            die( "Second parameter is empty");
        }


        if(!Subscribers::model()->exists(array("condition" => "phone_number=:pn",
                                                "params" => array(":pn" => $phone)))){
            return array(null, "Not registered phone number");
        }

        $subscriber = Subscribers::model()->find(array("condition" => "phone_number=:pn",
                                         "params" => array(":pn" => $phone)));

        if(count($attr_name) == 1){

            return array($subscriber->$attr_name);
        }

        else{
            $temp_arr = array();

            foreach($attr_name as $value){
                $temp_arr[] = $subscriber->$value;
            }

            return $temp_arr;
        }
    }

    public static function getPhoneByPk($pk){

        $subscriber = Subscribers::model()->findByPk($pk);

        return $subscriber->phone_number;
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

    public function getAbonentStage($phone_number = null){

        //////////////////////////////////////////////////////////Message//////////////////////////////////////////////////////////

        $incorrect = Yii::app()->params['messages']['common']['SMS_incorrect'];
        $sex_mssg = Yii::app()->params['messages']['2']['FillSex'];
        $age_mssg = Yii::app()->params['messages']['2']['FillAge'];
        $relation_mssg = Yii::app()->params['messages']['2']['FillRelation'];

        //////////////////////////////////////////////////////////Message//////////////////////////////////////////////////////////

        if($phone_number == null){
            die("PHONE_NUMBER IS EMPTY");
        }

        else{
            $subscriber = $this->model()->getAttrByParam(array("is_subscribed" => 1, "phone_number" => $phone_number));

            if($subscriber[0] == false){

                return array(false);

            }
            else{
                $profile = Profiles::model()->getAttrByParam(array("subscriber_id" => $subscriber[1][0]->id));

                if($profile[0] == false){
                    return array(true, array($sex_mssg, $subscriber[1][0]->id));
                }
                else{

                    if($profile[1][0]->age == 0){
                        return array(true, array($age_mssg, $subscriber[1][0]->id));
                    }

                    if($profile[1][0]->relation == 0){
                        return array(true, array($relation_mssg, $subscriber[1][0]->id));
                }

                    else{
                        return array(true, array($incorrect, $subscriber[1][0]->id));
                    }
                }

            }

        }

    }

    //@todo Закончить функцию
    public static function hasProfile($pk){

        if(Profiles::model()->exists("subscriber_id=:si", array(":si" => $pk))){
            $profile = Profiles::model()->find("subscriber_id=:si", array(":si" => $pk));
            echo CHtml::link("есть", Yii::app()->controller->createUrl("/profiles/view", array("id" => $profile->id)), array("target" => '_blank'));
        }
    }

    public static function hasResult($pk){

        if(Results::model()->exists("subscriber_id=:si", array(":si" => $pk))){
            echo CHtml::link("есть", Yii::app()->controller->createUrl("/questions/results"), array("target" => '_blank'));
        }
    }
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Subscribers the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
