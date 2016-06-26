<?php

/**
 * This is the model class for table "tbl_stats_delivered".
 *
 * The followings are the available columns in table 'tbl_stats_delivered':
 * @property integer $id
 * @property string $shortcode_smscost
 * @property integer $in_money
 * @property integer $in_quantity
 * @property integer $date
 */
class StatsDelivered extends CActiveRecord
{

    public $start_date, $end_date;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'tbl_stats_delivered';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('shortcode_smscost, in_money, in_quantity, date, start_date, end_date, id', 'safe'),
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
            'shortcode_smscost' => 'Номер отпр._Стоимость',
            'in_money' => 'В деньгах(тнг.)',
            'in_quantity' => 'В количестве',
            'date' => 'Дата',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
        );
    }

    public function search(){

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('shortcode_smscost',$this->shortcode_smscost);
        $criteria->compare('in_money',$this->in_money);
        $criteria->compare('in_quantity',$this->in_qunatity);
        $criteria->compare('date',$this->date,true);
        $criteria->compare('date',">=" . $this->date_from,true);
        $criteria->compare('date',"<=" . $this->date_to,true);

        return new CActiveDataProvider($this, array(
            'sort'=>array(
                'defaultOrder'=>'id DESC',
            ),
            'pagination' => array(
                'pageSize' => 30,
            ),
            'criteria'=>$criteria
        ));
    }

    public static function collectDlvrdStats(){
        $charging_cost = Yii::app()->params['sh_charging_cost'];

        $shortcode_charging_arr_str = $shortcode_charging_arr = $parameters = array();

        foreach($charging_cost as $key=>$value){
            $shortcode_charging_arr = explode("_", $key);

            $index1 = ":ss" . $shortcode_charging_arr[0];
            $index2 = ":ci" . $shortcode_charging_arr[1];

            $parameters[$index1] = $shortcode_charging_arr[0];
            $parameters[$index2] = $shortcode_charging_arr[1];

            $shortcode_charging_arr[0] = $index1;
            $shortcode_charging_arr[1] = $index2;

            $shortcode_charging_arr_str[] = "(" . implode(",", $shortcode_charging_arr) . ")";
        }
        $today = date("Y-m-d");
        $yesterday = date('Y-m-d', strtotime($today . ' - 1 day'));

        $parameters[":ds"] = 1;
        $condition = implode("," , $shortcode_charging_arr_str);
        $sql = "SELECT charging_id, sender_shortcode, COUNT(*) as 'number' FROM tbl_delivery_info where (sender_shortcode, charging_id) IN ({$condition}) AND dlv_status=:ds GROUP BY charging_id, sender_shortcode";
        $di = DeliveryInfo::model()->findAllBySql($sql, $parameters);

        foreach($di as $value){
            $sd = new StatsDelivered();
            $smscost = $charging_cost[$value->sender_shortcode . "_" . $value->charging_id];
            $sd->shortcode_smscost = $value->sender_shortcode . "_" . $smscost;
            $sd->in_quantity = (int) $value->number;
            $sd->in_money = $sd->in_quantity * (int)$smscost;
            $sd->date = $yesterday;
            $sd->save(false);
        }
        //print_r($di);
        //echo count($resultSet);
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

}
