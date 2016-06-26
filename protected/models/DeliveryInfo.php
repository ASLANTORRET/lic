<?php

/**
 * This is the model class for table "tbl_delivery_info".
 *
 * The followings are the available columns in table 'tbl_delivery_info':
 * @property integer $id
 * @property string $sms_id
 * @property string $phone_number
 * @property integer $dlv_status
 * @property integer $dstk_category_id
 * @property integer $sender_shortcode
 * @property integer $charging_id
 * @property integer $service_type
 */
class DeliveryInfo extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */

    public $number,$total, $dlvrd;

	public function tableName()
	{
		return 'tbl_delivery_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('sms_id, phone_number, dlv_status, id, service_type, charging_id, sender_shortcode, dstk_category_id, number, total, dlvrd', 'safe'),
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
			'sms_id' => 'СМС ID',
			'phone_number' => 'Номер телефона',
            'service_type' => 'Тип',
            'charging_id' => 'Стоимость',
            'sender_shortcode' => 'Номер отправки'
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function parseDlvInfo($sms_id){

       $curl = curl_init();
       curl_setopt($curl, CURLOPT_URL, "http://srv2.zerogravity.kz/dlr_query.php?sms_id=" . $sms_id);
       curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
       $result = curl_exec ($curl);
       curl_close ($curl);

       return $result;
    }

    public static function updateDlvSt($cat_id){

        $non_dlv_numbers = array();

        if($dlv_numb = DeliveryInfo::model()->count($sql = array("condition" => "category_id=:ci", "params" => array(":ci" => $cat_id)))){

            if($dlv_numb > 1){

                $dlvs = DeliveryInfo::model()->findAll($sql);

                foreach($dlvs as $value){

                    $result = DeliveryInfo::model()->parseDlvInfo($value->sms_id);
                    $status = json_decode($result, true);

                    if(isset($status["dlv_status"])){

                        $value->dlv_status = $status["dlv_status"];

                        if($value->dlv_status == 0){
                            $non_dlv_numbers[] = $value->phone_number;
                        }

                    }
                    else if(isset($status["error"])){

                        if($status["error"] == "1"){

                            $value->dlv_status = "1";
                        }
                    }
                    /*else{
                        echo "Upps";
                        die();
                    }*/

                    $value->save(false);
                }
            }
            else{

                $value = DeliveryInfo::model()->find($sql);
                $result = DeliveryInfo::model()->parseDlvInfo($value->sms_id);

                $status = json_decode($result, true);

                if(isset($status["dlv_status"])){

                    $value->dlv_status = $status["dlv_status"];

                    if($value->dlv_status == 0){
                        $non_dlv_numbers[] = $value->phone_number;
                    }

                }

                $value->save(false);
            }
        }

        return $non_dlv_numbers;                           //returns array("phone" => delivery_status) array

    }
}
