<?php
/**
 * Created by PhpStorm.
 * User: Aslan
 * Date: 28.07.15
 * Time: 18:23
 */

class DeliveryController extends Controller {
    public function actionCheckDlvSt(){

        $status_arr = array("false"=>"0","true"=>"1");
        if($dlv_numb = DeliveryInfo::model()->count()){
            if($dlv_numb > 1){
                $dlvs = DeliveryInfo::model()->findAll();
                foreach($dlvs as $value){
                    $result = DeliveryInfo::model()->parseDlvInfo($value->sms_id);
                    $status = json_decode($result, true);

                    if(isset($status["dlv_status"])){
                        $value->dlv_status = $status["dlv_status"];
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
                $value = DeliveryInfo::model()->find();
                $result = DeliveryInfo::model()->parseDlvInfo($value->sms_id);

                $status = json_decode($result, true);

                if(isset($status["dlv_status"])){
                    $value->dlv_status = $status["dlv_status"];
                }

                $value->save(false);
            }
    //            $this->actionUpdateDlv();
            StatsDelivered::collectDlvrdStats();
        }
    }

    /* public function actionFillPhNumbers(){
         $subs = Subscribers::model()->findAll(array("group"=>'phone_number', "select"=>'phone_number'));
         foreach($subs as $sub){
             $sd = new StatsDelivery();
             $sd->ph_number = $sub->phone_number;
             $sd->save(false);
         }
     }*/

} 