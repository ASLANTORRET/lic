<?php

class QuestionsController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}


    public function actions()
    {
        return array(
            'toggle' => array(
                'class'=>'booster.actions.TbToggleAction',
                'modelName' => 'Questions',
            )
        );
    }

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			/*array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),*/

            array('deny', // allow admin user to perform 'admin' and 'delete' actions
                'actions'=>array('update', 'delete'),
                'users'=>array('partnermg5'),
            ),

			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model = new Questions;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Questions']))
		{
            foreach($_POST['Questions'] as $index=>$value){
                $model->$index=$value;
            }

            if($model->save()){

                if(isset($_POST['yt1']) && !isset($_POST['yt2'])){
                    $this->redirect(array('view','id'=>$model->id));
                }

                else{
                        $model=new Questions();
                }
            }
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Questions']))
		{
			$model->attributes=$_POST['Questions'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Questions');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Questions('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Questions']))
			$model->attributes=$_GET['Questions'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

    /**
     * Manages all models.
     */
    public function actionResults()
    {
        $model=new Results('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Results']))
            $model->attributes=$_GET['Results'];

        $this->render('results',array(
            'model'=>$model,
        ));
    }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Questions the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model = Questions::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Questions $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='questions-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

    public function actionDistributeSMS(){

        if($subs_number = Subscribers::model()->count($condition = "is_subscribed=:is", $params = array(":is"=>1))){

            $minDay = Profiles::model()->getMinDay();
            $maxDay = Profiles::model()->getMaxDay();

            if($maxDay !=false && $minDay != false){

                $questions_container = Questions::getBulkQuestions(array($minDay, $maxDay));

                if($subs_number > 1){

                    $subscriber_data = array();
                    $subscribers = Subscribers::model()->findAll($condition  . " ORDER BY id ASC", $params);

                    foreach($subscribers as $value){

                        $subscriber_data[0][]=$value->id;
                        $subscriber_data[1][$value->id][] = array("phone_number"=>$value->phone_number,
                                                                "shortcode"=>$value->shortcode,
                                                                "charging_id"=>$value->charging_id,
                                                                "category_id"=>$value->category_id);

                    }

                    $criteria = new CDbCriteria();
                    $criteria->addInCondition("subscriber_id",$subscriber_data[0]);
                    $criteria->select = "sex, age, relation, day, subscriber_id";
                    $criteria->order = "subscriber_id ASC";

                    $profiles = Profiles::model()->findAll($criteria);

                    //$nots = Scripts::getNotifications($profiles);

                    foreach($profiles as $value){

                        if(isset($questions_container[$value->sex][$value->age][$value->relation][$value->day])){

                            $subscriber_data[1][$value->subscriber_id][] = $questions_container[$value->sex][$value->age][$value->relation][$value->day];
                        }

                        else{
                            $subscriber_data[1][$value->subscriber_id][] = array(null, Yii::app()->params["messages"]["SMS_Default"]);
                        }

                        $subsriber_id = $value->subscriber_id;


                        Scripts::sendSMS_MT($subscriber_data[1][$subsriber_id][1][1],
                                            $subscriber_data[1][$subsriber_id][0]->phone_number,
                                            $subscriber_data[1][$subsriber_id][0]->shortcode,
                                            $subscriber_data[1][$subsriber_id][0]->charging_id);

                    }

                    $criteria->select = $criteria->order = null;

                    Profiles::model()->updateCounters(array("day" => 1), $criteria);


                    $param_arr_temp = $indexes_arr = array();

                    foreach($subscriber_data[1] as $index=>$value){

                        $index1 = ":cti" . $index;
                        $index2 = ":cgi" . $index;
                        $index3 = ":ci" . $index;
                        $index4 = ":ph" . $index;
                        $index5 = ":si" . $index;
                        $index6 = ":stt" . $index;
                        $index7 = ":sc" . $index;


                        $param_arr_temp[$index1] = $value[1][0];                    //questions id
                        $param_arr_temp[$index2] = $value[0]->charging_id;
                        $param_arr_temp[$index2] = $value[0]->category_id;
                        $param_arr_temp[$index4] = $value[0]->phone_number;
                        $param_arr_temp[$index5] = $index;
                        $param_arr_temp[$index6] = $value[1][1];                    //questions
                        $param_arr_temp[$index7] = $value[0]->shortcode;

                        $indexes_arr[] = "({$index1}, {$index2}, {$index3}, {$index4}, {$index5}, {$index6}, {$index7}, :nt, :stm)";

                    }

                    $add_params = array(":nt"=>"Рассылка", ":stm"=>date("Y-m-d H:i:s"));

                    $param_arr = array_merge($param_arr_temp, $add_params);
                    $parameters = $param_arr;

                    $values = implode(",", $indexes_arr);

                    $sql = "INSERT INTO tbl_messages_log(questions_id, charging_id, cat_id, phone_number, subscriber_id, sent_text, shortcode, note, sent_time) VALUES {$values}";


                    Yii::app()->db->createCommand($sql)->execute($parameters);
                }

                else{

                    $subscriber_data = array();

                    $subscribers = Subscribers::model()->find($condition  . " ORDER BY id ASC", $params);

                    $subscriber_data[0][] = $subscribers->id;
                    $subscriber_data[1][$subscribers->id][]=array("phone_number" => $subscribers->phone_number,
                                                                  "shortcode" => $subscribers->shortcode,
                                                                  "charging_id" => $subscribers->charging_id,
                                                                  "category_id" => $subscribers->category_id);


                    $criteria = new CDbCriteria();
                    $criteria->addInCondition("subscriber_id",$subscriber_data[0]);
                    $criteria->select = "sex, age, relation, day, subscriber_id";
                    $criteria->order = "subscriber_id ASC";

                    $profiles = Profiles::model()->find($criteria);

                    //$nots = Scripts::getNotifications($profiles);


                    if(isset($questions_container[$profiles->sex][$profiles->age][$profiles->relation][$profiles->day])){

                        $subscriber_data[1][ $profiles->subscriber_id ][] = $questions_container[ $profiles->sex ][ $profiles->age ][ $profiles->relation ][ $profiles->day ];
                    }

                    else{
                        $subscriber_data[1][$profiles->subscriber_id][] = array(null, Yii::app()->params["messages"]["SMS_Default"]);
                    }

                    $subsriber_id = $profiles->subscriber_id;


                    Scripts::sendSMS_MT($subscriber_data[1][$subsriber_id][1][1],
                                        $subscriber_data[1][$subsriber_id][0]["phone_number"],
                                        $subscriber_data[1][$subsriber_id][0]["shortcode"],
                                        $subscriber_data[1][$subsriber_id][0]["charging_id"]);


                    $criteria->select = $criteria->order = null;

                    Profiles::model()->updateCounters(array("day" => 1), $criteria);


                    $param_arr_temp = $indexes_arr = array();

                    foreach($subscriber_data[1] as $index=>$value){

                        $index1 = ":cti" . $index;
                        $index2 = ":cgi" . $index;
                        $index3 = ":ci" . $index;
                        $index4 = ":ph" . $index;
                        $index5 = ":si" . $index;
                        $index6 = ":stt" . $index;
                        $index7 = ":sc" . $index;


                        $param_arr_temp[$index1] = $value[1][0];                    //questions id
                        $param_arr_temp[$index2] = $value[0]["charging_id"];
                        $param_arr_temp[$index3] = $value[0]["category_id"];
                        $param_arr_temp[$index4] = $value[0]["phone_number"];
                        $param_arr_temp[$index5] = $index;
                        $param_arr_temp[$index6] = $value[1][1];                    //questions
                        $param_arr_temp[$index7] = $value[0]["shortcode"];

                        $indexes_arr[] = "({$index1}, {$index2}, {$index3}, {$index4}, {$index5}, {$index6}, {$index7}, :nt, :stm)";
                    }


                    $add_params = array(":nt"=>"Рассылка", ":stm"=>date("Y-m-d H:i:s"));
                    $param_arr = array_merge($param_arr_temp, $add_params);

                    $values = implode(",", $indexes_arr);


                    $sql = "INSERT INTO tbl_messages_log(questions_id, charging_id, cat_id, phone_number, subscriber_id, sent_text, shortcode, note, sent_time) VALUES {$values}";
                    $parameters = $param_arr;


                    Yii::app()->db->createCommand($sql)->execute($parameters);

                }
            }
        }
    }

        public function actionNotifications(){

            $subscriber_data = array();

            $notification_mssgs = array(Yii::app()->params["messages"]["fillSex"],
                Yii::app()->params["messages"]["fillAge"],
                Yii::app()->params["messages"]["fillRelation"]);


            if($subscriber_number = Subscribers::model()->count($sql = array("condition"=>"is_subscribed=:is", "params"=>array(":is" => 1)))){
                if($subscriber_number > 1){
                    $subscribers = Subscribers::model()->findAll($sql);
                    foreach($subscribers as $value){
                        $subscriber_data[0][] = $value->id;
                        $subscriber_data[1][$value->id][] = array("phone_number"=>$value->phone_number,
                                                                    "shortcode"=>$value->shortcode,
                                                                    "charging_id"=>$value->charging_id,
                                                                    "category_id"=>$value->category_id);
                    }

                    $criteria = new CDbCriteria();
                    $criteria->addInCondition("subscriber_id",$subscriber_data[0]);
                    $criteria->select = "sex, age, relation, day, subscriber_id";
                    $criteria->order = "subscriber_id ASC";

                    $profiles = Profiles::model()->findAll($criteria);

                    foreach($profiles as $value){

                        if($value->sex  == 0){

                            $subscriber_data[1][$value->subscriber_id][] = array(null, $notification_mssgs[0]);
                        }
                        else if($value->age  == 0){
                            $subscriber_data[1][$value->subscriber_id][] = array(null, $notification_mssgs[1]);
                        }
                        else if($value->relation  == 0){
                            $subscriber_data[1][$value->subscriber_id][] = array(null, $notification_mssgs[2]);
                        }
                        else{
                            //$result_arr[$value->subscriber_id] = "";
                        }

                        Scripts::sendSMS_MT($subscriber_data[1][$value->subscriber_id][1][1],
                            $subscriber_data[1][$value->subscriber_id][0]["phone_number"],
                            $subscriber_data[1][$value->subscriber_id][0]["shortcode"],
                            $subscriber_data[1][$value->subscriber_id][0]["charging_id"]);

                    }

                    $param_arr_temp = $indexes_arr = array();

                    foreach($subscriber_data[1] as $index=>$value){

                        $index1 = ":cti" . $index;
                        $index2 = ":cgi" . $index;
                        $index3 = ":ci" . $index;
                        $index4 = ":ph" . $index;
                        $index5 = ":si" . $index;
                        $index6 = ":stt" . $index;
                        $index7 = ":sc" . $index;


                        $param_arr_temp[$index1] = $value[1][0];                    //questions id
                        $param_arr_temp[$index2] = $value[0]["charging_id"];
                        $param_arr_temp[$index3] = $value[0]["category_id"];
                        $param_arr_temp[$index4] = $value[0]["phone_number"];
                        $param_arr_temp[$index5] = $index;
                        $param_arr_temp[$index6] = $value[1][1];                    //questions
                        $param_arr_temp[$index7] = $value[0]["shortcode"];

                        $indexes_arr[] = "({$index1}, {$index2}, {$index3}, {$index4}, {$index5}, {$index6}, {$index7}, :nt, :stm)";
                    }


                    $add_params = array(":nt"=>"Опрос", ":stm"=>date("Y-m-d H:i:s"));
                    $param_arr = array_merge($param_arr_temp, $add_params);

                    $values = implode(",", $indexes_arr);


                    $sql = "INSERT INTO tbl_messages_log(questions_id, charging_id, cat_id, phone_number, subscriber_id, sent_text, shortcode, note, sent_time) VALUES {$values}";
                    $parameters = $param_arr;


                    Yii::app()->db->createCommand($sql)->execute($parameters);

                }
                else{

                        $subscribers = Subscribers::model()->find($sql);

                        $subscriber_data[0][] = $subscribers->id;
                        $subscriber_data[1][$subscribers->id][] = array(
                            "phone_number"=>$subscribers->phone_number,
                            "shortcode"=>$subscribers->shortcode,
                            "charging_id"=>$subscribers->charging_id,
                            "category_id"=>$subscribers->category_id);


                        $criteria = new CDbCriteria();
                        $criteria->addInCondition("subscriber_id",$subscriber_data[0]);
                        $criteria->select = "sex, age, relation, day, subscriber_id";
                        $criteria->order = "subscriber_id ASC";

                        $profiles = Profiles::model()->find($criteria);


                         if($profiles->sex  == 0){

                             $subscriber_data[1][$profiles->subscriber_id][] = array(null, $notification_mssgs[0]);
                         }
                         else if($profiles->age  == 0){
                             $subscriber_data[1][$profiles->subscriber_id][] = array(null, $notification_mssgs[1]);
                         }
                         else if($profiles->relation  == 0){
                             $subscriber_data[1][$profiles->subscriber_id][] = array(null, $notification_mssgs[2]);
                         }
                         else{
                             //$result_arr[$value->subscriber_id] = "";
                         }



                         Scripts::sendSMS_MT($subscriber_data[1][$profiles->subscriber_id][1][1],
                             $subscriber_data[1][$profiles->subscriber_id][0]["phone_number"],
                             $subscriber_data[1][$profiles->subscriber_id][0]["shortcode"],
                             $subscriber_data[1][$profiles->subscriber_id][0]["charging_id"]);



                        $param_arr_temp = $indexes_arr = array();

                        foreach($subscriber_data[1] as $index=>$value){

                            $index1 = ":cti" . $index;
                            $index2 = ":cgi" . $index;
                            $index3 = ":ci" . $index;
                            $index4 = ":ph" . $index;
                            $index5 = ":si" . $index;
                            $index6 = ":stt" . $index;
                            $index7 = ":sc" . $index;


                            $param_arr_temp[$index1] = $value[1][0];                    //questions id
                            $param_arr_temp[$index2] = $value[0]["charging_id"];
                            $param_arr_temp[$index3] = $value[0]["category_id"];
                            $param_arr_temp[$index4] = $value[0]["phone_number"];
                            $param_arr_temp[$index5] = $index;
                            $param_arr_temp[$index6] = $value[1][1];                    //questions
                            $param_arr_temp[$index7] = $value[0]["shortcode"];

                            $indexes_arr[] = "({$index1}, {$index2}, {$index3}, {$index4}, {$index5}, {$index6}, {$index7}, :nt, :stm)";
                        }


                        $add_params = array(":nt"=>"Опрос", ":stm"=>date("Y-m-d H:i:s"));
                        $param_arr = array_merge($param_arr_temp, $add_params);

                        $values = implode(",", $indexes_arr);


                        $sql = "INSERT INTO tbl_messages_log(questions_id, charging_id, cat_id, phone_number, subscriber_id, sent_text, shortcode, note, sent_time) VALUES {$values}";
                        $parameters = $param_arr;


                        Yii::app()->db->createCommand($sql)->execute($parameters);

                    }

            }

    }

}
