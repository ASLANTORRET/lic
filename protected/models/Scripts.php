<?php
/**
 * Created by PhpStorm.
 * User: Aslan
 * Date: 06.10.15
 * Time: 16:13
 */

class Scripts {

    public static function aoc($phone_number,$categoryObj){

        $categoryID = $categoryObj->id;

        $nav_data["2"] = array(                                                 //Сервис Любовь - Морковь
            Yii::app()->params['messages']["2"]['Info'],
            array(true, array(7)),
            array(true, array( 8, 10 ))
        );

        $nav_data["3"] = array(                                                 //Сервис Дневник здоровья
                        Yii::app()->params['messages']["3"]['Info'],
                        array(true, array(40)),
                        array(true, array( 41, 42 ))
        );

        if(Subscribers::model()->exists($sql = array("condition"    =>  "phone_number=:phn AND is_subscribed=:is AND category_id=:ci",
                                                     "params"       =>  array(":is"     =>  1,
                                                                              ":phn"    => $phone_number,
                                                                              ":ci"     => $categoryID)))){

            $hiddencats =  $nav_data[$categoryID][1];

        }

        else{

            $hiddencats =  $nav_data[$categoryID][2];

        }

        return array(
            $categoryObj->parent_category_id,
            array($query_result = $nav_data[$categoryID][0]),
            $showCats = true,
            $showBack = true,
            $hiddencats
        );
    }


    public static function sex($phone_number,$categoryObj){

        $showCats = true;

        $category_id = $categoryObj->id;
        $service_id = $categoryObj->service_id;

        $nav_data["2"] = array(                                                 //Сервис Любовь - Морковь
            Yii::app()->params['messages']["2"]['Age'],
        );

        $nav_data["3"] = array(                                                 //Сервис Дневник здоровья
            Yii::app()->params['messages']["3"]['Age'],
        );

        if($subscribedAbonent = Subscribers::model()->find(
            array('condition'=>'phone_number=:phone_number
                  AND is_subscribed=:is_subscribed
                  AND category_id=:ci',

                'order'=>'id DESC',

                'params'=>array(':phone_number'  => $phone_number,
                                ':is_subscribed' => 1,
                                ':ci'            => $service_id)))){

            $subscriberID = $subscribedAbonent->id;
            $catID_paramID = Yii::app()->params["catID_paramID"];


            if(!Profiles::model()->exists(array("condition" => "subscriber_id=:si AND category_id=:ci", "params" => array(":si" => $subscriberID, ":ci" => $service_id)))){

                $profile = new Profiles();
                $profile->sex = (int)$catID_paramID[$category_id];
                $profile->category_id = $service_id;
                $profile->status = 1;
                $profile->day = 1;
                $profile->sex_cat = $category_id;
                $profile->subscriber_id =$subscriberID;
                $profile->create_time = date("Y-m-d H:i:s");
                $profile->save(false);
            }
            else{

                $sql = array("condition" => "subscriber_id=:si AND category_id=:ci", "params" => array(":si" => $subscriberID, ":ci" => $service_id));

                $profile = Profiles::model()->find($sql);
                $profile->sex = (int)$catID_paramID[$category_id];
                $profile->sex_cat = $category_id;
                $profile->day = 1;
                $profile->update_time = date("Y-m-d H:i:s");
                $profile->subscriber_id =$subscriberID;
                $profile->save(false);
            }
        }

        return array(
            $categoryObj->parent_category_id,
            array( $query_result = $nav_data[$service_id][0]),
            $showCats,
            $showBack = false,
            array(false)
        );
    }
    public static function age($phone_number,$categoryObj){

        $nav_data["2"] = Yii::app()->params['messages']['2']['Relation'];
        $nav_data["3"] = Yii::app()->params['messages']['3']['Physics'];

        $showCats = true;

        $category_id = $categoryObj->id;
        $service_id = $categoryObj->service_id;

        if($subscribedAbonent = Subscribers::model()->find(
            array('condition'=>'phone_number=:phone_number
                  AND is_subscribed=:is_subscribed
                  AND category_id=:ci',
                'order'=>'id DESC',

                'params'=>array(':phone_number'=>$phone_number,
                                ':is_subscribed'=>1,
                                ':ci'=>$service_id)))){

            $subscriberID = $subscribedAbonent->id;


            if(Profiles::model()->exists($sql = array("condition"=>"subscriber_id=:si", "params"=>array(":si"=>$subscriberID)))){

                $catID_paramID = Yii::app()->params["catID_paramID"];
                $profile = Profiles::model()->find($sql);
                $profile->age = (int)$catID_paramID[$category_id];
                $profile->age_cat = $category_id;
                $profile->day = 1;
                $profile->update_time = date("Y-m-d H:i:s");
                $profile->subscriber_id =$subscriberID;
                $profile->save(false);
            }
        }

        return array(
            $categoryObj->parent_category_id,
            array($nav_data[$service_id]),
            $showCats,
            $showBack = false,
            array(false)
        );
    }

    public static function relation($phone_number,$categoryObj){

        $query_result = Yii::app()->params['messages']['2']['FilledProfile'];

        $showCats = false;

        $category_id = $categoryObj->id;
        $service_id = $categoryObj->service_id;

        if($subscribedAbonent = Subscribers::model()->find(
            array('condition'=>'phone_number=:phone_number
                  AND is_subscribed=:is_subscribed
                  AND category_id=:ci',
                'order'=>'id DESC',

                'params'=>array(':phone_number'=>$phone_number,
                    ':is_subscribed'=>1,
                    ':ci'=>$service_id)))){

            $subscriberID = $subscribedAbonent->id;

            if(Profiles::model()->exists($sql = array("condition"=>"subscriber_id=:si", "params"=>array(":si"=>$subscriberID)))){
                $catID_paramID = Yii::app()->params["catID_paramID"];
                $profile = Profiles::model()->find($sql);
                $profile->relation = (int)$catID_paramID[$category_id];
                $profile->relation_cat = $category_id;
                $profile->subscriber_id =$subscriberID;
                $profile->update_time = date("Y-m-d H:i:s");
                $profile->day = 1;
                $profile->save(false);
            }
            return array(
                $subscribedAbonent->category_id,
                array($query_result),
                $showCats,
                $showBack = true,
                array(false)
            );
        }
    }

    public static function physics($phone_number,$categoryObj){

        $query_result = Yii::app()->params['messages']['3']['FilledProfile'];

        $showCats = false;

        $service_id = $categoryObj->service_id;
        $category_id = $categoryObj->id;

        if($subscribedAbonent = Subscribers::model()->find(
            array('condition'=>'phone_number=:phone_number
                  AND is_subscribed=:is_subscribed
                  AND category_id=:ci',
                'order'=>'id DESC',

                'params'=>array(':phone_number'=>$phone_number,
                    ':is_subscribed'=>1,
                    ':ci'=>$service_id)))){

            $subscriberID = $subscribedAbonent->id;

            if(Profiles::model()->exists($sql = array("condition"=>"subscriber_id=:si", "params"=>array(":si"=>$subscriberID)))){
                $catID_paramID = Yii::app()->params["catID_paramID"];
                $profile = Profiles::model()->find($sql);
                $profile->physics = (int)$catID_paramID[$category_id];
                $profile->physics_cat = $category_id;
                $profile->subscriber_id = $subscriberID;
                $profile->category_id = $service_id;
                $profile->day = 1;
                $profile->update_time = date("Y-m-d H:i:s");
                $profile->save(false);
            }
            return array(
                $subscribedAbonent->category_id,
                array($query_result),
                $showCats,
                $showBack = true,
                array(false)
            );
        }
    }


    public static function profile($phone_number,$categoryObj){

        $category_id = $categoryObj->service_id;

        $nav_data["2"] = array(                                                 //Сервис Любовь - Морковь
            "19_0",
            array("relation", "Status")
        );

        $nav_data["3"] = array(                                                 //Сервис Дневник здоровья
            "69_0",
            array("physics", "Uroven' podgotovki")
        );

        //////////////////////////////////////////////////////////Message//////////////////////////////////////////////////////////

        $not_subscribed = Yii::app()->params['messages']['common']['NotSubscribed'];
        $sex_mssg = Yii::app()->params['messages'][$categoryObj->parent_category_id]['Sex'];
        $age_mssg = Yii::app()->params['messages'][$categoryObj->parent_category_id]['Age'];
        $relation_mssg = Yii::app()->params['messages'][$categoryObj->parent_category_id][ucwords($nav_data[$category_id][1][0])];

        //////////////////////////////////////////////////////////Message//////////////////////////////////////////////////////////


        $query_result = null;
        $showCats = $showBack = true;

        if($subscribedAbonent = Subscribers::model()->find(
            array('condition'=>'phone_number=:phone_number
                  AND category_id=:category_id
                  AND is_subscribed=:is_subscribed',

                'params'=>array(':phone_number'=>$phone_number,
                    ':category_id'=>$category_id, ':is_subscribed'=>1)))){
            $subscriberID = $subscribedAbonent->id;

            if(Profiles::model()->exists($sql = array("condition"=>"subscriber_id=:si", "params"=>array(":si"=>$subscriberID)))){

                $profile = Profiles::model()->find($sql);

                if($profile->age == 0){

                    $catID = $profile->sex_cat . "_0";
                    $query_result = $age_mssg;
                    $showCats = true;
                    $showBack = false;
                }
                else if($profile->relation == 0 && $category_id == 2
                        || $profile->physics == 0 && $category_id == 3){

                    $catID = $profile->age_cat . "_0";
                    $query_result = $relation_mssg;
                    $showCats = true;
                    $showBack = false;
                }
                else{
                    $catID = $nav_data[ $category_id ][ 0 ];

                    $sex = Yii::app()->params["system"]["sex"][$profile->sex];
                    $age = Yii::app()->params["system"]["age"][$category_id][$profile->age];

                    $third_var = $nav_data[ $category_id ][ 1 ][ 0 ];                     // Third parameter name (relation, physics)
                    $third_var_text = $nav_data[ $category_id ][ 1 ][ 1 ];                // Third parameter text (Status, Uroven' pogotovki)

                    $third_param = Yii::app()->params["system"][$third_var][$profile->sex][$profile->$third_var];

                    $query_result = "Vasha anketa:<br>Pol: {$sex}; <br>Vozrast:{$age}, <br>{$third_var_text}: {$third_param}";
                }
                return array(
                    $categoryObj->parent_category_id,
                    array($query_result, $catID),
                    $showCats,
                    $showBack,
                    array(false)

                );
            }
            else{
                $query_result = $sex_mssg;
                $showCats = true;
                $showBack = false;
            }
        }
        else{
            $query_result = $not_subscribed;
            $showCats = false;
        }

        return array(
            $categoryObj->parent_category_id,
            array($query_result),
            $showCats,
            $showBack,
            array(false)
        );
    }

    public static function editProfile($phone_number,$categoryObj){

        $category_id = $categoryObj->parent_category_id;
        $service_id = $categoryObj->service_id;


        $nav_data["2"] = array(                                                 //Сервис Любовь - Морковь
            "8_0",
        );

        $nav_data["3"] = array(                                                 //Сервис Дневник здоровья
            "41_0",
        );

        $query_result = Yii::app()->params['messages'][$service_id]['Sex'];

        /*if($subscribedAbonent = Subscribers::model()->find(
            array('condition'=>'phone_number=:phone_number
                  AND category_id=:category_id
                  AND is_subscribed=:is_subscribed',

                'params'=>array(':phone_number'=>$phone_number,
                    ':category_id'=>$category_id, ':is_subscribed'=>1)))){

        }*/
        $showCats = true;

        return array(
            $categoryObj->parent_category_id,
            array($query_result, $nav_data[$service_id][0]),
            $showCats,
            $showBack = false,
            array(false)
        );
    }
    public static function unsubscribe($phone_number,$categoryObj){

        //////////////////////////////////////////////////////////Message//////////////////////////////////////////////////////////

        $not_subscribed = Yii::app()->params['messages']['common']['NotSubscribed'];
        $unsubscribe_mssg = Yii::app()->params['messages'][$categoryObj->parent_category_id]['UnsubscribeByUSSD'];

        //////////////////////////////////////////////////////////Message//////////////////////////////////////////////////////////

        $category_id = $categoryObj->service_id;
        $showCats = true;

        if($subscribedAbonent = Subscribers::model()->find(
            array('condition'=>'phone_number=:phone_number
                  AND category_id=:category_id
                  AND is_subscribed=:is_subscribed',

                  'params'=>array(':phone_number'=>$phone_number,
                    ':category_id'=>$category_id, ':is_subscribed'=>1)))){

            $subscribedAbonent->is_subscribed = 0;
            $subscribedAbonent->unsubscribe_time = date("Y-m-d H:i:s");
            $subscribedAbonent->save();

            if(Profiles::model()->exists($condition = "subscriber_id=:si AND status=:s", $params = array(":si"=>$subscribedAbonent->id, ":s"=>1))){
                Profiles::model()->updateAll(array("status"=>0), array("condition"=>$condition, "params"=>$params));
            }

            $query_result = $unsubscribe_mssg;
        }
        else{
            $query_result = $not_subscribed;

        }

        return array(
            $categoryObj->parent_category_id,
            array($query_result),
            $showCats,
            $showBack = true,
            array(false)
        );
    }

    public static function unsubscribeBySMS($phone_number,$category_id){

        //////////////////////////////////////////////////////////Message//////////////////////////////////////////////////////////

        $not_subscribed = Yii::app()->params['messages']['common']['NotSubscribed'];
        $unsubscribe_mssg = Yii::app()->params['messages'][$category_id]['UnsubscribeBySMS'];

        //////////////////////////////////////////////////////////Message//////////////////////////////////////////////////////////

        if($subscribedAbonent = Subscribers::model()->find(
            array('condition'=>'phone_number=:phone_number
                  AND category_id=:category_id
                  AND is_subscribed=:is_subscribed',

                'params'=>array(':phone_number'=>$phone_number,
                    ':category_id'=>$category_id, ':is_subscribed'=>1)))){

            $subscribedAbonent->is_subscribed = 0;
            $subscribedAbonent->unsubscribe_time = date("Y-m-d H:i:s");
            $subscribedAbonent->save();

            $query_result = $unsubscribe_mssg;

            $subscriberID = $subscribedAbonent->id;

            return array(true, array($query_result, $subscriberID));
        }
        else{
            $query_result = $not_subscribed;

            return array(false, array($query_result));
        }


    }

    public static function subscribe($phone_number,$categoryObj){

        $current_parent_id = $categoryObj->service_id;

        $nav_data["2"] = array(                                                 //Сервис Любовь - Морковь
            '8_0'                                                                   // Anketa ID
        );

        $nav_data["3"] = array(                                                 //Сервис Дневник здоровья
            '41_0'                                                                 // Anketa ID
        );

        //////////////////////////////////////////////////////////Message//////////////////////////////////////////////////////////

            $query_result = Yii::app()->params['messages'][$current_parent_id]['Sex'];
            $subscribeInfo = Yii::app()->params['messages'][$current_parent_id]['SMS_Subscribe'];
            $subscribeInfoUSSD = Yii::app()->params['messages'][$current_parent_id]['USSD_Subscribe'];
            $subscribedAlready = Yii::app()->params['messages']['common']['SubscribedAlready'];                    // Сообщение "Вы уже подписаны"
            $default = Yii::app()->params['messages']['common']['SMS_default'];
            $URL_TEXT = "В течение суток Вы можете загрузить 1 ед. контента здесь:";
        //////////////////////////////////////////////////////////Message//////////////////////////////////////////////////////////

        $showCats = true;
        $showBack = false;

        if(!Subscribers::model()->exists(array('condition'=>'phone_number=:phone_number AND category_id=:category_id
                                                            AND is_subscribed=:is_subscribed',
            'params'=>array(':phone_number'=>$phone_number, 'category_id'=>$current_parent_id,
                ':is_subscribed'=>1)))){

            $charging_id = null;

            if($cat = Categories::model()->findByPk($current_parent_id)){                                               //Assigns the shortcode column value of the service category
                $charging_id = $cat->charging_id;
            }

            if($notActiveSubscriber = Subscribers::model()->find(array('condition'=>'phone_number=:phone_number
                                                                                    AND category_id=:category_id
                                                                                    AND is_subscribed=:is_subscribed',
                                                                                    'limit'=>1,
                                                                                    'params'=>array(':phone_number'=>$phone_number,
                                                                                    'category_id'=>$current_parent_id,
                                                                                     ':is_subscribed'=>0)))){

                $notActiveSubscriber->is_subscribed = 1;

                if($charging_id!=null){

                    //@todo Откомментить если необходимо сохранить стоимость
                    $notActiveSubscriber->charging_id = $charging_id;
                    $notActiveSubscriber->subscribe_time = date("Y-m-d H:i:s");
                    $notActiveSubscriber->unsubscribe_time = null;
                    $notActiveSubscriber->save();

                    $content_arr = Content::getContent($notActiveSubscriber);
                    $content_id = null;

                    //@todo Написать алгоритм определения отправителя
                    if($content_arr[0] == true){

                        $url = Scripts::generateLinks($notActiveSubscriber->category_id, 1);
                        $SMSContent = $content_arr[1][1] . "\n" . $URL_TEXT .  $url->link[0]->short;

                        $content_id = $content_arr[1][0];

                    }

                    else{
                        $SMSContent =  $default;
                    }

                    $free_charging = "10";


                    /*Sends subscriptions info*/

                    Scripts::sendSMS_MT($subscribeInfo, $notActiveSubscriber->phone_number, $notActiveSubscriber->shortcode, $free_charging);

                    MessagesLog::assignAttributes(array(
                        "content_id"    =>  null,
                        'charging_id'   =>  $free_charging,
                        'cat_id'        =>  $notActiveSubscriber->category_id,
                        'phone_number'  =>  $notActiveSubscriber->phone_number,
                        'subscriber_id' =>  $notActiveSubscriber->id,
                        'sent_text'     =>  $subscribeInfo,
                        'shortcode'     =>  $notActiveSubscriber->shortcode,
                        'note'          =>  'Подписка',
                        'sent_time'     =>  date("Y-m-d H:i:s")));



                    /*Sends content*/

                    Scripts::sendSMS_MT($SMSContent, $notActiveSubscriber->phone_number, $notActiveSubscriber->shortcode, $notActiveSubscriber->charging_id);

                    MessagesLog::assignAttributes(array("content_id"    =>  $content_id,
                                                        'charging_id'   =>  $notActiveSubscriber->charging_id,
                                                        'cat_id'        =>  $notActiveSubscriber->category_id,
                                                        'phone_number'  =>  $notActiveSubscriber->phone_number,
                                                        'subscriber_id' =>  $notActiveSubscriber->id,
                                                        'shortcode'     =>  $notActiveSubscriber->shortcode,
                                                        'sent_text'     =>  $SMSContent,
                                                        'note'          =>  'Подписка',
                                                        'sent_time'     =>  date("Y-m-d H:i:s")));


                    if(Profiles::model()->exists($condition = "subscriber_id=:si AND status=:s AND category_id=:ci", $params = array(":si"=>$notActiveSubscriber->id, ":s"=>0, ":ci" => $current_parent_id))){
                        $query_result = $subscribeInfoUSSD;
                        $showCats = false;
                        $showBack = true;
                    }

                }
            }
            else{

                $subscriber = new Subscribers();
                $subscriber->phone_number = $phone_number;
                $subscriber->category_id  = $current_parent_id;
                $subscriber->subscribe_time = date("Y-m-d H:i:s");
                $subscriber->unsubscribe_time = null;                
                $subscriber->is_subscribed = 1;
                $subscriber->shortcode = $cat->shortcode;

                if($charging_id!=null){

                    $subscriber->charging_id = $charging_id ;
                    $subscriber->save(false);

                    $content_arr = Content::getContent($subscriber);
                    $content_id = null;

                    if($content_arr[0] == true){
                        $url = Scripts::generateLinks($subscriber->category_id, 1);
                        $SMSContent = $content_arr[1][1] . "\n" . $URL_TEXT .  $url->link[0]->short;
                        $content_id = $content_arr[1][0];
                    }else{
                        $SMSContent = $default;
                    }

                    $free_charging = "10";


                    /*Sends subscriptions info*/

                    Scripts::sendSMS_MT($subscribeInfo, $subscriber->phone_number, $subscriber->shortcode, $free_charging);

                    MessagesLog::assignAttributes(array(
                        "content_id"    =>  null,
                        'charging_id'   =>  $free_charging,
                        'cat_id'        =>  $subscriber->category_id,
                        'phone_number'  =>  $subscriber->phone_number,
                        'subscriber_id' =>  $subscriber->id,
                        'sent_text'     =>  $subscribeInfo,
                        'shortcode'     =>  $subscriber->shortcode,
                        'note'          =>  'Подписка',
                        'sent_time'     =>  date("Y-m-d H:i:s")));


                    /*Sends content*/

                    Scripts::sendSMS_MT($SMSContent, $subscriber->phone_number, $subscriber->shortcode, $subscriber->charging_id);

                    MessagesLog::assignAttributes(array("content_id"=>$content_id,
                        'charging_id'   =>  $subscriber->charging_id,
                        'cat_id'        =>  $subscriber->category_id,
                        'phone_number'  =>  $subscriber->phone_number,
                        'subscriber_id' =>  $subscriber->id,
                        'shortcode'     =>  $subscriber->shortcode,
                        'sent_text'     =>  $SMSContent,
                        'note'          =>  'Подписка',
                        'sent_time'     =>  date("Y-m-d H:i:s")));

                }
            }

        }
        else{

            $query_result = $subscribedAlready;
            $showCats = false;
            $showBack = true;
        }

        return array(
            $categoryObj->parent_category_id,
            array($query_result,
                $nav_data[$current_parent_id][0]
            ),
            $showCats,
            $showBack,
            array(false)
        );
    }

    //@todo Закончить AOC

    public static function subscribeBySMSAOC($phone_number, $categoryObj, $message){

        $current_parent_id = $categoryObj;

        //////////////////////////////////////////////////////////Message//////////////////////////////////////////////////////////

        $subscribeInfo = Yii::app()->params['messages'][$current_parent_id]['SMS_Subscribe'];
        $subscribeAOC = Yii::app()->params['messages']['common']['SMS_AOC'];
        $subscribedAlready = Yii::app()->params['messages']['common']['SubscribedAlreadySMS'];                    // Сообщение "Вы уже подписаны"
        $default = Yii::app()->params['messages']['common']['SMS_default'];

        //////////////////////////////////////////////////////////Message//////////////////////////////////////////////////////////


        if(!Subscribers::model()->exists(array('condition'  =>  'phone_number=:phone_number
                                                                AND category_id=:category_id
                                                                AND is_subscribed=:is_subscribed',

                                                  'params'  =>  array(':phone_number'   =>  $phone_number,
                                                                      'category_id'     =>  $current_parent_id,
                                                                      ':is_subscribed'  =>  1
                                                                    )))){

            $charging_id = null;

            if($cat = Categories::model()->findByPk($current_parent_id)){
                $charging_id = $cat->charging_id;
            }

            if($notActiveSubscriber = Subscribers::model()->find(array('condition'=>'phone_number=:phone_number
                                                                                    AND category_id=:category_id
                                                                                    AND is_subscribed=:is_subscribed',
                                                                        'limit'   =>  1,
                                                                        'params'  =>    array(':phone_number'   =>  $phone_number,
                                                                                              'category_id'     =>  $current_parent_id,
                                                                                              ':is_subscribed'  =>  0)))){

                /*if(){

                }*/

                if(in_array($message, array("love", "fit"))){
                    $notActiveSubscriber->is_subscribed = 0;
                    $infoMessage = $subscribeAOC;
                }
                else{
                    $notActiveSubscriber->is_subscribed = 1;
                    $infoMessage = $subscribeInfo;
                }


                if($charging_id!=null){

                    //@todo Откомментить если необходимо сохранить стоимость
                    $notActiveSubscriber->charging_id = $charging_id;
                    $notActiveSubscriber->subscribe_time = date("Y-m-d H:i:s");
                    $notActiveSubscriber->unsubscribe_time = null;
                    $notActiveSubscriber->save();


                    /*Sends subscriptions info*/

                    $free_charging = "10";

                    Scripts::sendSMS_MT($infoMessage, $notActiveSubscriber->phone_number, $notActiveSubscriber->shortcode, $free_charging);

                    MessagesLog::assignAttributes(array(
                        "content_id"    =>  null,
                        'charging_id'   =>  $free_charging,
                        'cat_id'        =>  $notActiveSubscriber->category_id,
                        'phone_number'  =>  $notActiveSubscriber->phone_number,
                        'subscriber_id' =>  $notActiveSubscriber->id,
                        'sent_text'     =>  $subscribeInfo,
                        'shortcode'     =>  $notActiveSubscriber->shortcode,
                        'note'          =>  'Подписка',
                        'sent_time'     =>  date("Y-m-d H:i:s")));


                    /*Sends content*/
                    if(!in_array($message, array("love", "fit"))){

                        $content_arr = Content::getContent($notActiveSubscriber);
                        $content_id = null;

                        //@todo Написать алгоритм определения отправителя
                        if($content_arr[0] == true){

                            $SMSContent = $content_arr[1][1];
                            $content_id = $content_arr[1][0];

                        }

                        else{
                            $SMSContent =  $default;
                        }


                        Scripts::sendSMS_MT($SMSContent, $notActiveSubscriber->phone_number, $notActiveSubscriber->shortcode, $notActiveSubscriber->charging_id, $notActiveSubscriber->category_id);

                        MessagesLog::assignAttributes(array("content_id"=>$content_id,
                            'charging_id'   =>  $notActiveSubscriber->charging_id,
                            'cat_id'        =>  $notActiveSubscriber->category_id,
                            'phone_number'  =>  $notActiveSubscriber->phone_number,
                            'subscriber_id' =>  $notActiveSubscriber->id,
                            'sent_text'     =>  $SMSContent,
                            'note'          =>  'Подписка',
                            'sent_time'     =>  date("Y-m-d H:i:s")));
                    }


                    /*  if(Profiles::model()->exists($condition = "subscriber_id=:si AND status=:s", $params = array(":si"=>$notActiveSubscriber->id, ":s"=>0))){
                          $query_result = $subscribeInfoUSSD;
                      }*/

                }
            }
            else if(in_array($message, array("love", "fit"))){

                $subscriber = new Subscribers();
                $subscriber->phone_number = $phone_number;
                $subscriber->category_id  = $cat->id;
                $subscriber->subscribe_time = date("Y-m-d H:i:s");
                $subscriber->unsubscribe_time = null;
                $subscriber->is_subscribed = 0;
                $subscriber->shortcode = $cat->shortcode;


                if($charging_id!=null){

                    $subscriber->charging_id = $charging_id ;
                    $subscriber->save();

                    $free_charging = "10";

                    /*Sends subscriptions info*/

                    Scripts::sendSMS_MT($subscribeAOC, $subscriber->phone_number, $subscriber->shortcode, $free_charging);

                    MessagesLog::assignAttributes(array(
                        "content_id"    =>  null,
                        'charging_id'   =>  $free_charging,
                        'cat_id'        =>  $subscriber->category_id,
                        'phone_number'  =>  $notActiveSubscriber->phone_number,
                        'subscriber_id' =>  $notActiveSubscriber->id,
                        'sent_text'     =>  $subscribeAOC,
                        'shortcode'     =>  $notActiveSubscriber->shortcode,
                        'note'          =>  'Подписка',
                        'sent_time'     =>  date("Y-m-d H:i:s")));

                }
            }

        }
        else{

            $query_result = $subscribedAlready;

            $free_charging = "10";
            $subscriber = Subscribers::model()->getAttrByParam(array("phone_number" => $phone_number, "category_id" => $current_parent_id), array("select" => "shortcode"));

            $shortcode = $subscriber[1][0]->shortcode;
            $category_id = $current_parent_id;
            $subscriberID = $subscriber[1][0]->id;

            /*Sends subscriptions info*/

            Scripts::sendSMS_MT($query_result, $phone_number, $shortcode, $free_charging);

            MessagesLog::assignAttributes(array(
                "content_id"    =>  null,
                'charging_id'   =>  $free_charging,
                'cat_id'        =>  $category_id,
                'phone_number'  =>  $phone_number,
                'subscriber_id' =>  $subscriberID,
                'sent_text'     =>  $query_result,
                'shortcode'     =>  $shortcode,
                'note'          =>  'Подписка',
                'sent_time'     =>  date("Y-m-d H:i:s")));

        }
    }

    public static function subscribeBySMS($phone_number, $categoryObj){

        //////////////////////////////////////////////////////////Message//////////////////////////////////////////////////////////

        $subscribeInfo = Yii::app()->params['messages'][$categoryObj]['SMS_Subscribe'];
        $subscribedAlready = Yii::app()->params['messages']['common']['SubscribedAlreadySMS'];                    // Сообщение "Вы уже подписаны"
        $default = Yii::app()->params['messages']['common']['SMS_default'];
        $URL_TEXT = "В течение суток Вы можете загрузить 1 ед. контента здесь:";

        //////////////////////////////////////////////////////////Message//////////////////////////////////////////////////////////

        $current_parent_id = $categoryObj;


        if(!Subscribers::model()->exists(array('condition'=>'phone_number=:phone_number AND category_id=:category_id
                                                            AND is_subscribed=:is_subscribed',
            'params'=>array(':phone_number'=>$phone_number, 'category_id'=>$current_parent_id,
                ':is_subscribed'=>1)))){

            $charging_id = null;

            if($cat = Categories::model()->findByPk($current_parent_id)){
                $charging_id = $cat->charging_id;
            }

            if($notActiveSubscriber = Subscribers::model()->find(array('condition'=>'phone_number=:phone_number
                                                                                    AND category_id=:category_id
                                                                                    AND is_subscribed=:is_subscribed',
                'limit'=>1,
                'params'=>array(':phone_number'=>$phone_number,
                    'category_id'=>$current_parent_id, ':is_subscribed'=>0)))){

                /*if(){

                }*/

                $notActiveSubscriber->is_subscribed = 1;

                if($charging_id!=null){

                    //@todo Откомментить если необходимо сохранить стоимость
                    $notActiveSubscriber->charging_id = $charging_id;
                    $notActiveSubscriber->subscribe_time = date("Y-m-d H:i:s");
                    $notActiveSubscriber->unsubscribe_time = null;
                    $notActiveSubscriber->save();

                    $content_arr = Content::getContent($notActiveSubscriber);
                    $content_id = null;

                    //@todo Написать алгоритм определения отправителя
                    if($content_arr[0] == true){

                        $url = Scripts::generateLinks($notActiveSubscriber->category_id, 1);
                        $SMSContent = $content_arr[1][1] . "\n" . $URL_TEXT .  $url->link[0]->short;
                        $content_id = $content_arr[1][0];

                    }

                    else{
                        $SMSContent =  $default;
                    }

                    $free_charging = "10";


                    /*Sends subscriptions info*/

                    Scripts::sendSMS_MT($subscribeInfo, $notActiveSubscriber->phone_number, $notActiveSubscriber->shortcode, $free_charging);

                    MessagesLog::assignAttributes(array(
                        "content_id"    =>  null,
                        'charging_id'   =>  $free_charging,
                        'cat_id'        =>  $notActiveSubscriber->category_id,
                        'phone_number'  =>  $notActiveSubscriber->phone_number,
                        'subscriber_id' =>  $notActiveSubscriber->id,
                        'sent_text'     =>  $subscribeInfo,
                        'shortcode'     =>  $notActiveSubscriber->shortcode,
                        'note'          =>  'Подписка',
                        'sent_time'     =>  date("Y-m-d H:i:s")));



                    /*Sends content*/

                    Scripts::sendSMS_MT($SMSContent, $notActiveSubscriber->phone_number, $notActiveSubscriber->shortcode, $notActiveSubscriber->charging_id, $notActiveSubscriber->category_id);

                    MessagesLog::assignAttributes(array("content_id"=>$content_id,
                        'charging_id'   =>  $notActiveSubscriber->charging_id,
                        'cat_id'        =>  $notActiveSubscriber->category_id,
                        'phone_number'  =>  $notActiveSubscriber->phone_number,
                        'shortcode'     =>  $notActiveSubscriber->shortcode,
                        'subscriber_id' =>  $notActiveSubscriber->id,
                        'sent_text'     =>  $SMSContent,
                        'note'          =>  'Подписка',
                        'sent_time'     =>  date("Y-m-d H:i:s")));


                  /*  if(Profiles::model()->exists($condition = "subscriber_id=:si AND status=:s", $params = array(":si"=>$notActiveSubscriber->id, ":s"=>0))){
                        $query_result = $subscribeInfoUSSD;
                    }*/

                }
            }
            else{

                $subscriber = new Subscribers();
                $subscriber->phone_number = $phone_number;
                $subscriber->category_id  = $cat->id;
                $subscriber->subscribe_time = date("Y-m-d H:i:s");
                $subscriber->unsubscribe_time = null;
                $subscriber->is_subscribed = 1;
                $subscriber->shortcode = $cat->shortcode;


                if($charging_id!=null){

                    $subscriber->charging_id = $charging_id ;
                    $subscriber->save();

                    $content_arr = Content::getContent($subscriber);
                    $content_id = null;

                    if($content_arr[0] == true){

                        $url = Scripts::generateLinks($subscriber->category_id, 1);
                        $SMSContent = $content_arr[1][1] . "\n" . $URL_TEXT .  $url->link[0]->short;
                        $content_id = $content_arr[1][0];

                    }else{
                        $SMSContent = $default;
                    }

                    $free_charging = "10";


                    /*Sends subscriptions info*/


                    Scripts::sendSMS_MT($subscribeInfo, $subscriber->phone_number, $subscriber->shortcode, $free_charging);

                    MessagesLog::assignAttributes(array(
                        "content_id"    =>  null,
                        'charging_id'   =>  $free_charging,
                        'cat_id'        =>  $subscriber->category_id,
                        'phone_number'  =>  $subscriber->phone_number,
                        'subscriber_id' =>  $subscriber->id,
                        'sent_text'     =>  $subscribeInfo,
                        'shortcode'     =>  $subscriber->shortcode,
                        'note'          =>  'Подписка',
                        'sent_time'     =>  date("Y-m-d H:i:s")));


                    /*Sends content*/

                    Scripts::sendSMS_MT($SMSContent, $subscriber->phone_number, $subscriber->shortcode, $subscriber->charging_id, $subscriber->category_id);

                    MessagesLog::assignAttributes(array("content_id"=>$content_id,
                        'charging_id'   =>  $subscriber->charging_id,
                        'cat_id'        =>  $subscriber->category_id,
                        'phone_number'  =>  $subscriber->phone_number,
                        'subscriber_id' =>  $subscriber->id,
                        'shortcode'     =>  $subscriber->shortcode,
                        'sent_text'     =>  $SMSContent,
                        'note'          =>  'Подписка',
                        'sent_time'     =>  date("Y-m-d H:i:s")));

                }
            }

        }
        else{

            $query_result = $subscribedAlready;

            $free_charging = "10";
            $subscriber = Subscribers::model()->getAttrByParam(array("phone_number" => $phone_number, "category_id" => $current_parent_id), array("select" => "shortcode"));

            $shortcode = $subscriber[1][0]->shortcode;
            $category_id = $current_parent_id;
            $subscriberID = $subscriber[1][0]->id;

            /*Sends subscriptions info*/


            Scripts::sendSMS_MT($query_result, $phone_number, $shortcode, $free_charging);

            MessagesLog::assignAttributes(array(
                "content_id"    =>  null,
                'charging_id'   =>  $free_charging,
                'cat_id'        =>  $category_id,
                'phone_number'  =>  $phone_number,
                'subscriber_id' =>  $subscriberID,
                'sent_text'     =>  $query_result,
                'shortcode'     =>  $shortcode,
                'note'          =>  'Подписка',
                'sent_time'     =>  date("Y-m-d H:i:s")));

        }
    }

    //@todo Поменять параметры
    public static function sendSMS_MT($message, $phone, $shortcode, $charging_id = null, $category_id = null){

        $smsc = "KZ-KCELL-". $shortcode."-SMS";
        $message = preg_replace('/[\r\t\a\e]/u', '', $message);
        $message = trim($message);
        $message = str_replace("\n", "%0A", $message);
        $message = str_replace("+", "%2B", $message);
        $message = str_replace("#", "%23", $message);
        $message = str_replace("&", "%26", $message);
        $message = str_replace(" ", "+", $message);
        $message = str_replace("|", "", $message);


        $receiver = "?sender=".$shortcode."&to=" .$phone . "&smsc=" . $smsc . "&message=" .$message . "&charging_id=" . $charging_id;

        $curl = curl_init();
        curl_setopt ($curl, CURLOPT_URL, "http://195.189.29.202/sendsms.php" . $receiver);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec ($curl);
        curl_close ($curl);

        /////////////////////Delivery Info////////////////////////

        $di = new DeliveryInfo();
        $di->phone_number = $phone;
        $di->charging_id = (int) $charging_id;
        $di->sender_shortcode = (int)$shortcode;

        if($category_id != null){
            $di->category_id = $category_id;
        }

        $dlv_status = json_decode($result, true);

        if(isset($dlv_status["uid"])){
            $di->sms_id = $dlv_status["uid"];
        }

        $di->save(false);

        return $result;
    }


    public static function generateLinks($catID, $qn = 1){

        $cat_token_arr = array(
            "2" => "llOkijr",
            "3" => "plo2jki"
        );

        $url = "http://goodlook.kz/games/genLinksPls?qn={$qn}&t={$cat_token_arr[$catID]}";
        $curl = curl_init();

        curl_setopt ($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec ($curl);
        curl_close ($curl);

        if(!isset($result)){
            die("The result var is empty");
        }

        $normal_arr = json_decode($result);

        $short_arr = Scripts::sqeezer(array_values($normal_arr));

        return $short_arr;
    }

    public static function sqeezer($links = null)
    {
        $timeout = 500;

        ini_set('max_execution_time', $timeout);

        if($links == null){
            die("The first parameter is missed");
        }

        if(COUNT($links) < 1){
            die("The first parameter is empty");
        }

        $xml = new SimpleXMLElement('<items/>');

        foreach ($links as $link)
        {
            $item = $xml->addChild('link');
            $item->addChild('original', $link);
            $item->addChild('valid','3');
        }

        $sxml = (string)$xml->asXML();

        $url = 'http://s5.kz/api/multy';

        $post_data = array('xml' => $sxml);

        $stream_options = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded' . "\r\n",
                'timeout' => $timeout,
                'content' => http_build_query($post_data)));

        $context  = stream_context_create($stream_options);
        $response = file_get_contents($url, 0, $context);

        $in_xml = simplexml_load_string($response);

        return $in_xml;
    }

    public static function getNotifications($profiles = null){

        $notification_mssgs = array(Yii::app()->params["messages"]['2']["fillSex"],
                                    Yii::app()->params["messages"]['2']["fillAge"],
                                    Yii::app()->params["messages"]['2']["fillRelation"]);


        $result_arr = array();

        if($profiles == NULL){
            return "NO PARAMETERS";
        }

        if(count($profiles) > 1){
            foreach($profiles as $value){

                if($value->sex  == 0){

                    $result_arr[$value->subscriber_id] = $notification_mssgs[0];
                }
                else if($value->age  == 0){
                    $result_arr[$value->subscriber_id] = $notification_mssgs[1];
                }
                else if($value->relation  == 0){
                    $result_arr[$value->subscriber_id] = $notification_mssgs[2];
                }
                else{
                    $result_arr[$value->subscriber_id] = "";
                }

            }
        }
        else{

             if($profiles->sex  == 0){

                 $result_arr[$profiles->subscriber_id] = $notification_mssgs[0];
             }
             else if($profiles->age  == 0){
                 $result_arr[$profiles->subscriber_id] = $notification_mssgs[1];
             }
             else if($profiles->relation  == 0){
                 $result_arr[$profiles->subscriber_id] = $notification_mssgs[2];
             }
             else{
                 $result_arr[$profiles->subscriber_id] = "";
             }

        }

        return $result_arr;
    }
} 