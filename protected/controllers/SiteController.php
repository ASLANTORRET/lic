<?php

class SiteController extends Controller
{

    public $defaultAction = 'index';
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{

		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}



    public function actionIndex(){

        $this->layout = '//layouts/index';
        $this->redirect("site/login");
    }



    /**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
                //$this->redirect(array("site/login"));
				$this->render('error', $error);
		}
	}

    public function actionSMS(){

        if(!isset($_GET[ 'phone' ]) || !isset($_GET[ 'message' ]) || !isset($_GET[ 'shortcode' ])){
            die("EMPTY PARAMETER");
        }

        $shortcode_catID = array("3012"=>'2', "3013"=>'3');


        $phone = $_GET['phone'];
        $message = trim(strtolower($_GET['message']));
        $shortcode = $_GET['shortcode'];
        $sent_time = date("Y-m-d H:i:s");

        $category_id = $shortcode_catID[$shortcode];

        $subscribeOffer = Yii::app()->params["messages"][$category_id]["SubscribeOffer"];
        $info = Yii::app()->params["messages"][$category_id]["Info"];

    //////////////////////LOG MO/////////////////////////////////////

        LogMO::assignAttributes(array("phone_number"=> $phone,
            "message"=> $message,
            "shortcode"=> $shortcode,
            "subscriber_id"=> null,
            "sent_time"=> $sent_time));

        //////////////////////////////////////Subscription by SMS///////////////////////

        if(in_array($message, array("love", "Love", "Лове", "Лов", "ЛОВ", "лове", "лов", "da", "Да", "ДА", "да")) && $shortcode == "3012"){

            $withAOC = false;

            switch($withAOC){

                case false:
                    Scripts::subscribeBySMS($phone, $category_id);
                    break;
                case true:
                    Scripts::subscribeBySMSAOC($phone, $category_id, $message);
            }

            die();

        }

        if(in_array($message, array("fit", "фит", "Фит", "ФИТ","fit.", "фит.", "Фит.", "ФИТ.", "da", "Да", "ДА", "да")) && $shortcode == "3013"){

            $withAOC = false;

            switch($withAOC){

                case false:
                    Scripts::subscribeBySMS($phone, $category_id);
                    break;
                case true:
                    Scripts::subscribeBySMSAOC($phone, $category_id, $message);
            }

            die();

        }

    //////////////////////////////////////Unsubscribe by SMS///////////////////////

        if(in_array($message, array("stop", "стоп", "cтоп", "стoп", "Стоп", "СТОП", "СТОП fit", "СТОП love", "СТОП Fit", "СТОП Love", "СТОП FIT", "СТОП LOVE", "СTOП", "Cтoп", "стап", "stap", "Cтап", "ctap", "ctop", "ctoп", "Стоп love", "стоп love", "ctop love", "ctoп love", "Ctop love", "Ctoп love", "Cтоп love", "cтоп love", "Стоп lоvе", "стоп lоvе", "ctop lоvе", "ctoп lоvе", "Ctop lоvе", "Ctoп lоvе", "Cтоп lоvе", "cтоп lоvе", "Стоп fit", "стоп fit", "ctop fit", "ctoп fit", "Ctop fit", "Ctoп fit", "Cтоп fit", "cтоп fit", "Стоп", "стоп", "ctop", "ctoп", "Ctop", "Ctoп", "Cтоп", "cтоп"))){

            if(in_array($shortcode, array('3012', '3013'))){

                $subscriberID = null;
                $catID = $shortcode_catID[$shortcode];

                $result = Scripts::unsubscribeBySMS($phone, $catID);

                if($result[0] == true){

                    $subscriberID = $result[1][1];

                    MessagesLog::assignAttributes(array(
                            "phone_number" => $phone,
                            "content_id" => null,
                            "charging_id"  => 10,
                            "cat_id"       => $catID,
                            "subscriber_id"=> $subscriberID,
                            "note"          => "Отписка",
                            "sent_text"     => $result[1][0],
                            "sent_time"    => date("Y-m-d H:i:s"),
                            "shortcode"   => $shortcode
                        )
                    );

                    Scripts::sendSMS_MT($result[1][0], $phone, $shortcode, "10");

                    die();

                }

                else{

                    //////////////////////LOG MT/////////////////////////////////////

                    MessagesLog::assignAttributes(array(
                            "phone_number" => $phone,
                            "content_id" => null,
                            "charging_id"  => 10,
                            "cat_id"       => $category_id,
                            "subscriber_id"=> null,
                            "note"          => "Некорректный MO(не подписан)",
                            "sent_text"     => $subscribeOffer,
                            "sent_time"    => date("Y-m-d H:i:s"),
                            "shortcode"   => $shortcode
                        )
                    );

                    //echo $subscribeOffer;
                    Scripts::sendSMS_MT($subscribeOffer, $phone, $shortcode, "10");
                    die();

                }
            }
        }

    ///////////////////////////RESTRICTIONS///////////////////////////////


         if(strlen($message) > 1){                                               //INCORRECT Length

             $response_msg  = Subscribers::model()->getAbonentStage($phone);

             if($response_msg[0] == true){

             //////////////////////LOG MT/////////////////////////////////////

                 MessagesLog::assignAttributes(array(
                         "phone_number" => $phone,
                         "content_id" => null,
                         "charging_id"  => 10,
                         "cat_id"       => $category_id,
                         "subscriber_id"=> $response_msg[1][1],
                         "note"          => "Некорректный MO",
                         "sent_text"     => $response_msg[1][0],
                         "sent_time"    => date("Y-m-d H:i:s"),
                         "shortcode"   => $shortcode
                     )
                 );

                 Scripts::sendSMS_MT($response_msg[1][0], $phone, $shortcode, "10");

                 //die($response_msg[1][0]);
                 die();


             }
             else{

             //////////////////////LOG MT/////////////////////////////////////

                 MessagesLog::assignAttributes(array(
                         "phone_number" => $phone,
                         "content_id" => null,
                         "charging_id"  => 10,
                         "cat_id"       => $category_id,
                         "subscriber_id"=> null,
                         "note"          => "Некорректный MO",
                         "sent_text"     => $subscribeOffer,
                         "sent_time"    => date("Y-m-d H:i:s"),
                         "shortcode"   => $shortcode
                     )
                 );

                 Scripts::sendSMS_MT($subscribeOffer, $phone, $shortcode, "10");

                 die();
                 //die($subscribeOffer);
             }
         }

         if(is_numeric($message) == FALSE){                                     //INCORRECT type

             $response_msg  = Subscribers::model()->getAbonentStage($phone);

             if($response_msg[0] == true){

                 //////////////////////LOG MT/////////////////////////////////////

                 MessagesLog::assignAttributes(array(
                         "phone_number" => $phone,
                         "content_id" => null,
                         "charging_id"  => 10,
                         "cat_id"       => $category_id,
                         "subscriber_id"=> $response_msg[1][1],
                         "note"          => "Некорректный MO",
                         "sent_text"     => $response_msg[1][0],
                         "sent_time"    => date("Y-m-d H:i:s"),
                         "shortcode"   => $shortcode
                     )
                 );

                 Scripts::sendSMS_MT($response_msg[1][0], $phone, $shortcode, "10");

                 die();
                 //die($response_msg[1][0]);
             }
             else{

                 //////////////////////LOG MT/////////////////////////////////////

                 MessagesLog::assignAttributes(array(
                         "phone_number"   => $phone,
                         "content_id"     => null,
                         "charging_id"    => 10,
                         "cat_id"         => $category_id,
                         "subscriber_id"  => null,
                         "note"           => "Некорректный MO",
                         "sent_text"      => $subscribeOffer,
                         "sent_time"      => date("Y-m-d H:i:s"),
                         "shortcode"      => $shortcode
                     )
                 );

                 Scripts::sendSMS_MT($subscribeOffer, $phone, $shortcode, "10");

                 die();

                 //die($subscribeOffer);
             }
         }

         $message = (int) $message;

         if($message < 1 || $message > 3){                                                      //OUT OF RANGE

             $response_msg  = Subscribers::model()->getAbonentStage($phone);

             if($response_msg[0] == true){

                 //////////////////////LOG MT/////////////////////////////////////

                 MessagesLog::assignAttributes(array(
                         "phone_number" => $phone,
                         "content_id" => null,
                         "charging_id"  => 10,
                         "cat_id"       => $category_id,
                         "subscriber_id"=> $response_msg[1][1],
                         "note"          => "Некорректный MO",
                         "sent_text"     => $response_msg[1][0],
                         "sent_time"    => date("Y-m-d H:i:s"),
                         "shortcode"   => $shortcode
                     )
                 );
                 Scripts::sendSMS_MT($response_msg[1][0], $phone, $shortcode, "10");

                 die();

                 //die($response_msg[1][0]);
             }
             else{
                 //////////////////////LOG MT/////////////////////////////////////

                 MessagesLog::assignAttributes(array(
                         "phone_number" => $phone,
                         "content_id" => null,
                         "charging_id"  => 10,
                         "cat_id"       => $category_id,
                         "subscriber_id"=> null,
                         "note"          => "Некорректный MO",
                         "sent_text"     => $subscribeOffer,
                         "sent_time"    => date("Y-m-d H:i:s"),
                         "shortcode"   => $shortcode
                     )
                 );

                 Scripts::sendSMS_MT($subscribeOffer, $phone, $shortcode, "10");

                 die();

                 //die($subscribeOffer);
             }

         }

    $subscriber_data = Subscribers::model()->getAttrByParam(array("phone_number" => $phone, "is_subscribed" => 1, "category_id"=>$category_id));

    if($subscriber_data[0] == false){

        MessagesLog::assignAttributes(array(
                "phone_number" => $phone,
                "content_id" => null,
                "charging_id"  => 10,
                "cat_id"       => $category_id,
                "subscriber_id"=> null,
                "note"          => "Некорректный MO",
                "sent_text"     => $subscribeOffer,
                "sent_time"    => date("Y-m-d H:i:s"),
                "shortcode"   => $shortcode
            )
        );

        Scripts::sendSMS_MT($subscribeOffer, $phone, $shortcode, "10");

        die();

        //die($subscribeOffer);
    }

    else{

        $subscriber_id = $subscriber_data[1][0]->id;

    }

    ///////////////////////////RESTRICTIONS///////////////////////////////


    $sql = array("condition"=>"subscriber_id=:si", "params"=> array(":si"=>$subscriber_id));

    if(!Profiles::model()->exists($sql)){

        $sex_cat = 0;

        if($message > 2){
            $message = Yii::app()->params["messages"][$shortcode_catID[$shortcode]]["FillSex"];

            Scripts::sendSMS_MT($message, $phone, $shortcode, "10");

            die();

            //die("$message");
        }

        $resultSet = Categories::model()->getAttrByParam(array("script_url"=>"sex"), array("select" => "id", "order" => "id ASC"));


        if($resultSet[0] == false){
            die("Category not found");
        }

        else{
            foreach($resultSet[1] as $index => $value){
                if($index == ($message - 1)){
                    $sex_cat = $value->id;
                }
            }
        }

        Profiles::assignAttributes(array(

            "subscriber_id" => $subscriber_id,
            "sex" => $message,
            "category_id" => $category_id,
            "age" => 0,
            "relation" => 0,
            "physics" => 0,
            "sex_cat" => $sex_cat,
            "day" => 1,
            "create_time" => date("Y-m-d H:i:s"),
            "update_time" => date("Y-m-d H:i:s"),
            "status" => 1

        ));

        $message = Yii::app()->params["messages"][$category_id]["SexConfirmed"];

        Scripts::sendSMS_MT($message, $phone, $shortcode, "10");

        die();

        //die($message);
    }

    $profile = Profiles::model()->find($sql);

      if($profile->sex != 0 && $profile->age != 0 && ($profile->relation != 0 || $profile->physics != 0)){
        //ОПРОС
          $resultSet = Results::model()->getAttrByParam(array("subscriber_id" => $profile->subscriber_id), array("order" => "sent_date DESC", "limit" => 1));

          if($resultSet[0] == false){

              if(isset( Yii::app()->params["messages"][$category_id]["ProfileAlreadyFilled"] )){

                  $message = Yii::app()->params["messages"][$category_id]["ProfileAlreadyFilled"];

                  Scripts::sendSMS_MT($message, $phone, $shortcode, "10");

                  MessagesLog::assignAttributes(array(
                          "phone_number" => $phone,
                          "content_id" => null,
                          "charging_id"  => 10,
                          "cat_id"       => $category_id,
                          "subscriber_id"=> null,
                          "note"          => null,
                          "sent_text"     => $message,
                          "sent_time"    => date("Y-m-d H:i:s"),
                          "shortcode"   => $shortcode
                      )
                  );

                  die();

                  //die($message);

              }
          }
          else{

              $resultSet[1][0]->character = $message;

              $resultSet[1][0]->answer_date = date("Y-m-d H:i:s");
              $resultSet[1][0]->save(false);

              $question_id = $resultSet[1][0]->question_id;

              $question = Questions::model()->findByPk($question_id);

              $columnname = "response" . $message;
              $message = $question->$columnname;

              Scripts::sendSMS_MT($message, $phone, $shortcode, "10");

              MessagesLog::assignAttributes(array(
                      "phone_number" => $phone,
                      "content_id" => null,
                      "charging_id"  => 10,
                      "cat_id"       => $category_id,
                      "subscriber_id"=> null,
                      "note"          => null,
                      "sent_text"     => $message,
                      "sent_time"    => date("Y-m-d H:i:s"),
                      "shortcode"   => $shortcode
                  )
              );

              die();

              //die($message);
          }

      }
      else{

          /*if($profile->sex == 0){

              if($message > 2){
                  die("INCORRECT MESSAGE:Value");
              }

              $profile->sex = $message;

              $resultSet = Categories::model()->getAttrByParam(array("script_url"=>"sex"), array("select" => "id", "order" => "id ASC"));

              if($resultSet[0] == true){
                  foreach($resultSet[1] as $index => $value){
                      if($index == ($message - 1)){
                          $profile->sex_cat = $value->id;
                          $profile->day = 0;
                      }
                  }
              }
          }

          else*/
              if($profile->age == 0){

              $profile->age = $message;

              $resultSet = Categories::model()->getAttrByParam(array("script_url"=>"age", "parent_category_id" => $profile->sex_cat), array("select" => "id", "order" => "id ASC"));

              if($resultSet[0] == true){
                  foreach($resultSet[1] as $index => $value){
                      if($index == ($message - 1)){
                          $profile->age_cat = $value->id;
                          $profile->day = 1;
                      }
                  }

                  $message = Yii::app()->params["messages"][$category_id]["AgeConfirmed"];

                  Scripts::sendSMS_MT($message, $phone, $shortcode, "10");

                  MessagesLog::assignAttributes(array(
                          "phone_number" => $phone,
                          "content_id" => null,
                          "charging_id"  => 10,
                          "cat_id"       => $category_id,
                          "subscriber_id"=> null,
                          "note"          => null,
                          "sent_text"     => $message,
                          "sent_time"    => date("Y-m-d H:i:s"),
                          "shortcode"   => $shortcode
                      )
                  );
              }

          }

          else {

              if($profile->relation == 0 && $profile->category_id == 2){

                  $profile->relation = $message;

                  $resultSet = Categories::model()->getAttrByParam(array("script_url"=>"relation", "parent_category_id" => $profile->age_cat), array("select" => "id", "order" => "id ASC"));

                  if($resultSet[0] == true){
                      foreach($resultSet[1] as $index => $value){
                          if($index == ($message - 1)){
                              $profile->relation_cat = $value->id;
                              $profile->day = 1;
                          }
                      }

                      $id_text = array("1" => "RelationConfirmedMan",
                          "2" => "RelationConfirmedWoman"
                      );
                      $message = Yii::app()->params["messages"][$category_id][$id_text[$profile->sex]];

                      Scripts::sendSMS_MT($message, $phone, $shortcode, "10");

                      MessagesLog::assignAttributes(array(
                              "phone_number" => $phone,
                              "content_id" => null,
                              "charging_id"  => 10,
                              "cat_id"       => $category_id,
                              "subscriber_id"=> null,
                              "note"          => null,
                              "sent_text"     => $message,
                              "sent_time"    => date("Y-m-d H:i:s"),
                              "shortcode"   => $shortcode
                          )
                      );
                  }

              }

              if($profile->physics == 0 && $profile->category_id == 3){

                  $profile->physics = $message;

                  $resultSet = Categories::model()->getAttrByParam(array("script_url"=>"physics", "parent_category_id" => $profile->age_cat), array("select" => "id", "order" => "id ASC"));

                  if($resultSet[0] == true){
                      foreach($resultSet[1] as $index => $value){
                          if($index == ($message - 1)){
                              $profile->physics_cat = $value->id;
                              $profile->day = 1;
                          }
                      }

                      $id_text = array("1" => "PhysicsConfirmedMan",
                          "2" => "PhysicsConfirmedWoman"
                      );
                      $message = Yii::app()->params["messages"][$category_id][$id_text[$profile->sex]];

                      Scripts::sendSMS_MT($message, $phone, $shortcode, "10");

                      MessagesLog::assignAttributes(array(
                              "phone_number" => $phone,
                              "content_id" => null,
                              "charging_id"  => 10,
                              "cat_id"       => $category_id,
                              "subscriber_id"=> null,
                              "note"          => null,
                              "sent_text"     => $message,
                              "sent_time"    => date("Y-m-d H:i:s"),
                              "shortcode"   => $shortcode
                          )
                      );
                  }

              }
          }

          $profile->save(false);
      }


        //ussd.kz/messages/sms?phone=77078307490&message=sds&shortcode=111
    }

	/**
	 * Displays the login page
	 */
    public function actionLogin()
    {

        $this->layout = "//layout/triplets";

        /*if (!defined('CRYPT_BLOWFISH')||!CRYPT_BLOWFISH)
            throw new CHttpException(500,"This application requires that PHP was compiled with Blowfish support for crypt().");*/

        $model=new LoginForm;

        // if it is ajax validation request
        if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if(isset($_POST['LoginForm']))
        {
            $model->attributes=$_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if($model->validate() && $model->login()){
                $this->redirect(array('categories/admin'));
            }
        }
        // display the login form

        $this->render('login',array('model'=>$model));
    }

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect($this->createUrl('login'));
	}

    public function actionCheckDlvr(){

        Scripts::sendSMS_MT("test", "77012273248", "3013", "10");
        Scripts::sendSMS_MT("Сабина, тестовое сообщение", "77018387313", "3013", "10");
        //77018387313

    }

    public function actionGetMaxDay(){
        echo Subscribers::model()->getMaxDay();
    }
}