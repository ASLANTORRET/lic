<?php

/**
 * This is the model class for table "tbl_log_mt".
 *
 * The followings are the available columns in table 'tbl_log_mt':
 * @property integer $id
 * @property string $phone_number
 * @property integer $content_id
 * @property integer $charging_id
 * @property integer $cat_id
 * @property integer $subscriber_id
 * @property string $note
 * @property string $sent_text
 * @property string $sent_time
 * @property integer $shortcode
 */
class MessagesLog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_log_mt';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, phone_number, content_id, charging_id, subscriber_id, note, sent_text, sent_time, cat_id, shortcode', 'safe', 'on'=>'search'),
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
			'charging_id' => 'сhargingID',
            '$content_id' => 'Контент',
			'subscriber_id' => 'Подписчик',
			'note' => 'Заметки',
			'sent_text' => 'Отправленный текст',
            'sent_time' => 'Время отправки',
            'cat_id' => 'Категория',
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
		$criteria->compare('charging_id',$this->charging_id);
        $criteria->compare('cat_id',$this->cat_id);
        $criteria->compare('subscriber_id',$this->subscriber_id);
        $criteria->compare('shortcode',$this->shortcode, true);
		$criteria->compare('content_id',$this->content_id);
        $criteria->compare('sent_text',$this->sent_text, true);
        $criteria->compare('sent_time',$this->sent_time, true);


		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination' => array(
                'pageSize' => 20
            )
		));
	}


    public static function getDataByPk($pk, $columnn){

        if($category = MessagesLog::model()->findByPk($pk)){
            return $category->$columnn;
        }
        else{
            return "";
        }
    }

    public static function assignAttributes($attrubutes){
        $model = new MessagesLog();
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
