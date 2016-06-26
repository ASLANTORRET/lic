<?php

/**
 * This is the model class for table "tbl_log_mo".
 *
 * The followings are the available columns in table 'tbl_log_mo':
 * @property integer $id
 * @property string $sms_details
 * @property integer $subscriber_id
 * @property string $sent_time
 * @property string $phone_number
 */
class LogMO extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_log_mo';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, phone_number, subscriber_id, sent_time, sms_details', 'safe', 'on'=>'search'),
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
			'subscriber_id' => 'Подписчик',
			'note' => 'Заметки',
            'sent_time' => 'Время отправки',
            'message' => 'Текст',
            'shortcode' => 'КН'
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
		$criteria->compare('phone_number',$this->phone_number,true);
		$criteria->compare('note',$this->note,true);
        $criteria->compare('subscriber_id',$this->subscriber_id);
        $criteria->compare('message',$this->message, true);
        $criteria->compare('sent_time',$this->sent_time, true);
        $criteria->compare('shortcode',$this->shortcode, true);


		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>40
            )
		));
	}


    public static function getDataByPk($pk, $columnn){

        if($category = LogMO::model()->findByPk($pk)){

            return $category->$columnn;
        }
        else{

            return "";
        }
    }

    public static function assignAttributes($attrubutes){

        $model = new LogMO();

        foreach($attrubutes as $index=>$value){

            $model->$index = $value;
        }

        $model->save(false);
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
