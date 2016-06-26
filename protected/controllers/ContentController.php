<?php

class ContentController extends Controller
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
                'modelName' => 'Content',
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
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions'=>array('distributeSMSLIC', 'distributeSMSDiary', 'notificationsLIC','imitateNotificationsLIC', 'notificationsDiary', 'Rebill', 'notificationsSubsInfo', 'deliveredStats'),
                'ips'=>array('80.242.212.186', '127.0.0.1', '80.242.214.210', '147.30.27.181', '5.34.31.34'),
            ),

            /*array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions'=>array('create','update'),
                'users'=>array('@'),
            ),*/

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

    public function actionParse(){

        $html_source = file_get_contents("http://kolesa.kz/a/show/25502099");

        preg_match_all('/<span id="ya_share1"(.*?)<\/span>/s', $html_source, $phone_block);

        preg_match_all('/data-desc="(.*?)"/s', $phone_block[1][0], $phone_in_text);

        print_r($phone_in_text[0][1][0]);


    }
    /**
     * Creates a new models.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model=new Content;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        //$model->category_id = 2;

        if(isset($_POST['Content']))
        {
            foreach($_POST['Content'] as $index=>$value){
                $model->$index=$value;
            }

            if($model->save()){

                if(isset($_POST['yt1']) && !isset($_POST['yt2'])){
                    $this->redirect(array('view','id'=>$model->id));
                }
                else{

                    $model=new Content;

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

        if(isset($_POST['Content']))
        {
            foreach ($_POST['Content'] as $index=>$value) {

                $model->$index = $value;

            }

            if($model->save())
                $this->redirect(array('view','id'=>$model->id));
        }

        $this->render('update',array(
            'model'=>$model,
            'service_type' => $model->category_id
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
        $dataProvider=new CActiveDataProvider('Content');
        $this->render('index',array(
            'dataProvider'=>$dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model=new Content('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Content']))
            $model->attributes=$_GET['Content'];

        $this->render('admin',array(
            'model'=>$model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Content the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model=Content::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param Content $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax']==='content-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    /*public function actionDistributeSMS(){

        if($subs_number = Subscribers::model()->count($condition = "is_subscribed=:is", $params = array(":is"=>1))){

            $minDay = Profiles::model()->getMinDay();
            $maxDay = Profiles::model()->getMaxDay();


            if($maxDay !=false && $minDay != false){

                $content_container = Content::getBulkContent(array($minDay, $maxDay));

                if($subs_number > 1){

                    $subscriber_data = array();
                    $subscribers = Subscribers::model()->findAll($condition  . " ORDER BY id ASC", $params);

                    foreach($subscribers as $value){

                        if(Content::checkMNP($value->phone_number) == 02){

                            $subscriber_data[0][]=$value->id;
                            $subscriber_data[1][$value->id][] = array("phone_number"=>$value->phone_number,
                                "shortcode"=>$value->shortcode,
                                "charging_id"=>$value->charging_id,
                                "category_id"=>$value->category_id);

                        }
                    }

                    $criteria = new CDbCriteria();
                    $criteria->addInCondition("subscriber_id",$subscriber_data[0]);
                    $criteria->select = "sex, age, relation, day, subscriber_id";
                    $criteria->order = "subscriber_id ASC";

                    $profiles = Profiles::model()->findAll($criteria);

                    //$nots = Scripts::getNotifications($profiles);

                    foreach($profiles as $value){

                        if(isset($content_container[$value->sex][$value->age][$value->relation][$value->day])){

                            $subscriber_data[1][$value->subscriber_id][] = $content_container[$value->sex][$value->age][$value->relation][$value->day];
                        }

                        else{
                            $subscriber_data[1][$value->subscriber_id][] = array(null, Yii::app()->params["messages"]["common"]["SMS_default"]);
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


                        $param_arr_temp[$index1] = $value[1][0];                    //content id
                        $param_arr_temp[$index2] = $value[0]->charging_id;
                        $param_arr_temp[$index2] = $value[0]->category_id;
                        $param_arr_temp[$index4] = $value[0]->phone_number;
                        $param_arr_temp[$index5] = $index;
                        $param_arr_temp[$index6] = $value[1][1];                    //content
                        $param_arr_temp[$index7] = $value[0]->shortcode;

                        $indexes_arr[] = "({$index1}, {$index2}, {$index3}, {$index4}, {$index5}, {$index6}, {$index7}, :nt, :stm)";

                    }

                    $add_params = array(":nt"=>"Рассылка", ":stm"=>date("Y-m-d H:i:s"));

                    $param_arr = array_merge($param_arr_temp, $add_params);
                    $parameters = $param_arr;

                    $values = implode(",", $indexes_arr);

                    $sql = "INSERT INTO tbl_log_mt(content_id, charging_id, cat_id, phone_number, subscriber_id, sent_text, shortcode, note, sent_time) VALUES {$values}";


                    Yii::app()->db->createCommand($sql)->execute($parameters);
                }

                else{

                    $subscriber_data = array();

                    $subscribers = Subscribers::model()->find($condition  . " ORDER BY id ASC", $params);

                    if(Content::checkMNP($subscribers->phone_number) != 02){

                        die("There is no KCELL abonents");

                    }

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


                    if(isset($content_container[$profiles->sex][$profiles->age][$profiles->relation][$profiles->day])){

                        $subscriber_data[1][ $profiles->subscriber_id ][] = $content_container[ $profiles->sex ][ $profiles->age ][ $profiles->relation ][ $profiles->day ];
                    }

                    else{
                        $subscriber_data[1][$profiles->subscriber_id][] = array(null, Yii::app()->params["messages"]["common"]["SMS_default"]);
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


                        $param_arr_temp[$index1] = $value[1][0];                    //content id
                        $param_arr_temp[$index2] = $value[0]["charging_id"];
                        $param_arr_temp[$index3] = $value[0]["category_id"];
                        $param_arr_temp[$index4] = $value[0]["phone_number"];
                        $param_arr_temp[$index5] = $index;
                        $param_arr_temp[$index6] = $value[1][1];                    //content
                        $param_arr_temp[$index7] = $value[0]["shortcode"];

                        $indexes_arr[] = "({$index1}, {$index2}, {$index3}, {$index4}, {$index5}, {$index6}, {$index7}, :nt, :stm)";
                    }


                    $add_params = array(":nt"=>"Рассылка", ":stm"=>date("Y-m-d H:i:s"));
                    $param_arr = array_merge($param_arr_temp, $add_params);

                    $values = implode(",", $indexes_arr);


                    $sql = "INSERT INTO tbl_log_mt(content_id, charging_id, cat_id, phone_number, subscriber_id, sent_text, shortcode, note, sent_time) VALUES {$values}";
                    $parameters = $param_arr;


                    Yii::app()->db->createCommand($sql)->execute($parameters);

                }
            }
        }
    }*/

    public function actionRebill(){

        $REBILL_TOKENS =
            array(
                "ZTP7zYuM" => "actionDistributeSMSLIC",
                "5VGrU6jy" => "actionDistributeSMSDiary"
            );

        if(!isset($_GET["token"])){
            die("The first parameter is empty");
        }

        $token = $_GET["token"];

        if(!array_key_exists($token, $REBILL_TOKENS)){
            die("The first parameter is incorrect");
        }

        $this->$REBILL_TOKENS[$token](true);

    }

    /**
     * Cleans delivery Info
     */

    public function actionDeliveredStats(){

        StatsDelivered::collectDlvrdStats();
        echo "Done";

    }

    public function actionCleanDlvInfo(){

        DeliveryInfo::model()->deleteAll();
        echo "Done";

    }

    public function actionStatsDlvrd(){
        $criteria = new CDbCriteria();
        $model = new StatsDelivered();
        if(isset($_GET['StatsDelivered'])){

            if($_GET['StatsDelivered']['start_date'] != NULL && $_GET['StatsDelivered']['end_date'] != NULL){

                $model->start_date = $start_date = $_GET['StatsDelivered']['start_date'];
                $model->end_date = $end_date = $_GET['StatsDelivered']['end_date'];

                $criteria->condition = "date >= :start_date AND date <=:end_date";
                $criteria->params = array(":start_date"=>$start_date, ":end_date"=>$end_date);
            }
        }

        $dataProvider = new CActiveDataProvider("StatsDelivered", array(
            "criteria"=>$criteria,
            'pagination'=>false,
            "sort"=>array(
                "defaultOrder"=>"shortcode_smscost ASC"
            )));

        $this->render('statdlvrd',array(
            'dp'=>$dataProvider,
            'model'=>$model
        ));
    }

    /**
     * Sends subscription info
     */

    public function actionSendInfo(){

        if($subs_number = Subscribers::model()->count($condition = "is_subscribed=:is AND category_id=:ci AND (TIMESTAMPDIFF(DAY,date(subscribe_time),CURRENT_TIMESTAMP()))%30=:st", $params = array(":is"=>1, ":st" => 0))){

            //COMPLETE
        }

    }
    public function actionImitateSMSLIC( $is_rebill_enabled = false ){

        ////////////////////////////////////////LOG///////////////////////////////////////////////////

        $file = fopen('script_logs/script_center.log', 'a');
        $date_file = date("Y-m-d H:i:s").";" . Yii::app()->createAbsoluteUrl(Yii::app()->request->url) . ";";
        $id = mb_substr(md5($date_file), 0, 7,  "utf-8");
        $status = "\tstarted;\n";
        fwrite($file, $date_file . $id  .';'. $status);
        fclose($file);

        ////////////////////////////////////////LOG///////////////////////////////////////////////////



        $CAT_ID = 2;                                //Love is Carrots ID
        $URL_TEXT = "В течение суток Вы можете загрузить 1 ед. контента здесь: ";

        $non_dlvrd_numbers = array();

        if($is_rebill_enabled  == true){

            $non_dlvrd_numbers = DeliveryInfo::updateDlvSt($CAT_ID);                            //returns array of phone number and related status

        }


        if($subs_number = Subscribers::model()->count($condition = "is_subscribed=:is AND category_id=:ci AND (TIMESTAMPDIFF(DAY,date(subscribe_time),CURRENT_TIMESTAMP()))%2=:st AND date(subscribe_time)!=:stn", $params = array(":is"=>1, ":ci" => $CAT_ID, ":st" => 0, ":stn" => date("Y-m-d"))))
        {
            //$minDay = Profiles::model()->getMinDay($CAT_ID);
            /*$maxDay = Profiles::model()->getMaxDay($CAT_ID);


            if( $maxDay == false ){

                die("The max day is empty");

            }*/

            $content_container = Content::getBulkContentAdv(/*$maxDay, */$CAT_ID);
            $urls = Scripts::generateLinks($CAT_ID, $subs_number);

            if($subs_number > 1){

                $subscriber_data = array();
                $subscribers = Subscribers::model()->findAll($condition  . " ORDER BY id ASC", $params);

                foreach($subscribers as $value){

                    if (file_exists("script_logs/stop_" . $id . ".txt")) {
                        die();
                    }

                    if(Content::checkMNP($value->phone_number) == 02){

                        if($is_rebill_enabled  == true){

                            if(!in_array($value->phone_number, $non_dlvrd_numbers)){

                                $fileTerminated = fopen('script_logs/script_center.log', 'a');
                                $date_file_end = date("Y-m-d H:i:s").";" . Yii::app()->createAbsoluteUrl(Yii::app()->request->url) . ";";
                                fwrite($fileTerminated, $date_file_end . $id . ";\tstopped: non-delivered number not found\n");
                                fclose($fileTerminated);

                                die("non-delivered number not found");
                            }
                            else{

                                $subscriber_data[0][]=$value->id;
                                $subscriber_data[1][$value->id][] = array("phone_number"=>$value->phone_number,
                                    "shortcode"=>$value->shortcode,
                                    "charging_id"=>$value->charging_id,
                                    "category_id"=>$value->category_id,
                                    "subscribe_time"=>$value->subscribe_time);

                            }
                        }
                        else{

                            $subscriber_data[0][]=$value->id;
                            $subscriber_data[1][$value->id][] = array("phone_number"=>$value->phone_number,
                                "shortcode"=>$value->shortcode,
                                "charging_id"=>$value->charging_id,
                                "category_id"=>$value->category_id,
                                "subscribe_time"=>$value->subscribe_time);

                        }

                    }
                }

                if(!isset($subscriber_data[0])){
                    die("There is no any receiver");
                }

                $criteria = new CDbCriteria();
                $criteria->addInCondition("subscriber_id",$subscriber_data[0]);
                $criteria->select = "sex, age, relation, physics, day, subscriber_id";
                $criteria->order = "subscriber_id ASC";

                $profiles = Profiles::model()->findAll($criteria);

                //$nots = Scripts::getNotifications($profiles);

                foreach($profiles as $index => $value){

                    if (file_exists("script_logs/stop_" . $id . ".txt")) {
                        die();
                    }


                    if(isset($content_container[$value->sex][$value->age][$value->relation][$value->physics][$value->day])){

                        $SMSContent = $content_container[$value->sex][$value->age][$value->relation][$value->physics][$value->day];

                        if($index <= count($urls->link)){
                            $SMSContent[1] = $SMSContent[1] . "\n" . $URL_TEXT .  $urls->link[$index]->short;
                        }

                        $subscriber_data[1][$value->subscriber_id][] = $SMSContent;
                    }

                    else{
                        $subscriber_data[1][$value->subscriber_id][] = array(null, Yii::app()->params["messages"]["common"]["SMS_default"]);

                        //Когда отсутствуте контент будет отправлено сандартное сообщение с нулевым чаржингом (10) и без указания категории, чтобы избежать повторной оптравки механизмом ребиллинга

                        $subscriber_data[1][$value->subscriber_id][0]["charging_id"] = "10";
                        $subscriber_data[1][$value->subscriber_id][0]["category_id"] = null;
                    }

                    $subsriber_id = $value->subscriber_id;


                    /*Scripts::sendSMS_MT($subscriber_data[1][$subsriber_id][1][1],
                        $subscriber_data[1][$subsriber_id][0]["phone_number"],
                        $subscriber_data[1][$subsriber_id][0]["shortcode"],
                        $subscriber_data[1][$subsriber_id][0]["charging_id"],
                        $subscriber_data[1][$subsriber_id][0]["category_id"]);*/

                }

                $profiles_number = count($profiles);

                //if($profiles_number < $subs_number){                              // IF there are subscribers without profile

                //$url_iterator = $profiles_number;

                $array_keys = array_keys($subscriber_data[1]);

                foreach($subscriber_data[1] as $index => $value){

                    if(!isset($value[1])){

                        if (file_exists("script_logs/stop_" . $id . ".txt")) {
                            die();
                        }

                        $sub_date = strtotime(explode(" ", $value[0]["subscribe_time"])[0]);
                        $date = strtotime(date("Y-m-d"));

                        $content_day = ($date - $sub_date)/(60*60*24*2);

                        if(isset($content_container[0][0][0][0][$content_day])){

                            $SMSContent = $content_container[0][0][0][0][$content_day];
                            $url_index = array_search($index, $array_keys);

                            if($url_index <= count($urls->link)){
                                $SMSContent[1] = $SMSContent[1] . "\n" . $URL_TEXT .  $urls->link[$url_index]->short;
                            }

                            $subscriber_data[1][$index][1] = $SMSContent;

                        }

                        else{
                            $subscriber_data[1][$index][1] = array(null, Yii::app()->params["messages"]["common"]["SMS_default"]);

                            //Когда отсутствуте контент будет отправлено сандартное сообщение с нулевым чаржингом (10) и без указания категории, чтобы избежать повторной оптравки механизмом ребиллинга

                            $subscriber_data[1][$index][0]["charging_id"] = "10";
                            $subscriber_data[1][$index][0]["category_id"] = null;
                        }

                        /*Scripts::sendSMS_MT($subscriber_data[1][$index][1][1],
                            $subscriber_data[1][$index][0]["phone_number"],
                            $subscriber_data[1][$index][0]["shortcode"],
                            $subscriber_data[1][$index][0]["charging_id"],
                            $subscriber_data[1][$index][0]["category_id"]);*/

                    }
                }

                //}

                $criteria->select = $criteria->order = null;

                //Profiles::model()->updateCounters(array("day" => 1), $criteria);


                $param_arr_temp = $indexes_arr = array();

                foreach($subscriber_data[1] as $index=>$value){

                    $index1 = ":cti" . $index;
                    $index2 = ":cgi" . $index;
                    $index3 = ":ci" . $index;
                    $index4 = ":ph" . $index;
                    $index5 = ":si" . $index;
                    $index6 = ":stt" . $index;
                    $index7 = ":sc" . $index;

                    if(isset($value[1])){
                        $param_arr_temp[$index1] = $value[1][0];                    //content id
                        $param_arr_temp[$index6] = $value[1][1];                    //content
                    }
                    else{
                        $param_arr_temp[$index1] = 0;                    //content id
                        $param_arr_temp[$index6] = "контент";                    //content
                    }
                    $param_arr_temp[$index2] = $value[0]["charging_id"];
                    $param_arr_temp[$index3] = $value[0]["category_id"];
                    $param_arr_temp[$index4] = $value[0]["phone_number"];
                    $param_arr_temp[$index5] = $index;

                    $param_arr_temp[$index7] = $value[0]["shortcode"];

                    $indexes_arr[] = "({$index1}, {$index2}, {$index3}, {$index4}, {$index5}, {$index6}, {$index7}, :nt, :stm)";

                }

                $add_params = array(":nt"=>"Рассылка", ":stm"=>date("Y-m-d H:i:s"));

                $param_arr = array_merge($param_arr_temp, $add_params);
                $parameters = $param_arr;

                $values = implode(",", $indexes_arr);

                $sql = "INSERT INTO tbl_log_mt(content_id, charging_id, cat_id, phone_number, subscriber_id, sent_text, shortcode, note, sent_time) VALUES {$values}";

                Yii::app()->db->createCommand($sql)->execute($parameters);
            }

            else{

                $subscriber_data = array();

                $subscribers = Subscribers::model()->find($condition  . " ORDER BY id ASC", $params);

                if(Content::checkMNP($subscribers->phone_number) != 02){

                    die("There is no any KCELL provided phone number owner");

                }

                if($is_rebill_enabled  == true){

                    if(!in_array($subscribers->phone_number, $non_dlvrd_numbers)){

                        $fileTerminated = fopen('script_logs/script_center.log', 'a');
                        $date_file_end = date("Y-m-d H:i:s").";" . Yii::app()->createAbsoluteUrl(Yii::app()->request->url) . ";";
                        fwrite($fileTerminated, $date_file_end . $id . ";\tstopped: non-delivered number not found\n");
                        fclose($fileTerminated);

                        die("There is no any non delivered message1");
                    }
                    else{

                        $subscriber_data[0][]=$subscribers->id;
                        $subscriber_data[1][$subscribers->id][] = array("phone_number"=>$subscribers->phone_number,
                            "shortcode"=>$subscribers->shortcode,
                            "charging_id"=>$subscribers->charging_id,
                            "category_id"=>$subscribers->category_id,
                            "subscribe_time"=>$subscribers->subscribe_time);

                    }
                }
                else{

                    $subscriber_data[0][]=$subscribers->id;
                    $subscriber_data[1][$subscribers->id][] = array("phone_number"=>$subscribers->phone_number,
                        "shortcode"=>$subscribers->shortcode,
                        "charging_id"=>$subscribers->charging_id,
                        "category_id"=>$subscribers->category_id,
                        "subscribe_time"=>$subscribers->subscribe_time);

                }

                $criteria = new CDbCriteria();

                $criteria->addInCondition("subscriber_id", $subscriber_data[0]);
                $criteria->select = "sex, age, relation, physics, day, subscriber_id";
                $criteria->order = "subscriber_id ASC";


                if(Profiles::model()->exists($criteria)){

                    $profiles = Profiles::model()->find($criteria);

                    //$nots = Scripts::getNotifications($profiles);

                    if(isset($content_container[$profiles->sex][$profiles->age][$profiles->relation][$profiles->physics][$profiles->day])){

                        $SMSContent = $content_container[ $profiles->sex ][ $profiles->age ][ $profiles->relation ][ $profiles->physics ][ $profiles->day ];

                        if(count($urls->link) >= 1){

                            $SMSContent[1] = $SMSContent[1] . "\n" . $URL_TEXT .  $urls->link->short;
                        }

                        $subscriber_data[1][ $profiles->subscriber_id ][] = $SMSContent;
                    }

                    else{

                        $subscriber_data[1][$profiles->subscriber_id][] = array(null, Yii::app()->params["messages"]["common"]["SMS_default"]);

                        //Когда отсутствуте контент будет  отправлено сандартное сообщение с нулевым чаржингом (10) и без указания категории, чтобы избежать повторной оптравки механизмом ребиллинга

                        $subscriber_data[1][$profiles->subscriber_id][0]["charging_id"] = "10";
                        $subscriber_data[1][$profiles->subscriber_id][0]["category_id"] = null;

                    }

                    $subsriber_id = $profiles->subscriber_id;


                    /*Scripts::sendSMS_MT($subscriber_data[1][$subsriber_id][1][1],
                        $subscriber_data[1][$subsriber_id][0]["phone_number"],
                        $subscriber_data[1][$subsriber_id][0]["shortcode"],
                        $subscriber_data[1][$subsriber_id][0]["charging_id"],
                        $subscriber_data[1][$subsriber_id][0]["category_id"]);*/


                }
                else{

                    $subsriber_id = $subscribers->id;

                    if(!isset($subscriber_data[1][$subsriber_id][1])){

                        $sub_date = strtotime(explode(" ", $subscriber_data[1][$subsriber_id][0]["subscribe_time"])[0]);
                        //$sub_date = strtotime(explode(" ", $value[0]["subscribe_time"])[0]);
                        $date = strtotime(date("Y-m-d"));

                        $content_day = ($date - $sub_date) / (60*60*24*2);

                        if(isset($content_container[0][0][0][0][$content_day])){

                            $SMSContent = $content_container[0][0][0][0][$content_day];

                            $SMSContent[1] = $SMSContent[1] . "\n" . $URL_TEXT .  $urls->link[0]->short;

                            $subscriber_data[1][$subsriber_id][1] = $SMSContent;
                        }

                        else{
                            $subscriber_data[1][$subsriber_id][1] = array(null, Yii::app()->params["messages"]["common"]["SMS_default"]);

                            //Когда отсутствуте контент будет отправлено сандартное сообщение с нулевым чаржингом (10) и без указания категории, чтобы избежать повторной оптравки механизмом ребиллинга

                            $subscriber_data[1][$subsriber_id][0]["charging_id"] = "10";
                            $subscriber_data[1][$subsriber_id][0]["category_id"] = null;
                        }

                        /*Scripts::sendSMS_MT($subscriber_data[1][$subsriber_id][1][1],
                            $subscriber_data[1][$subsriber_id][0]["phone_number"],
                            $subscriber_data[1][$subsriber_id][0]["shortcode"],
                            $subscriber_data[1][$subsriber_id][0]["charging_id"],
                            $subscriber_data[1][$subsriber_id][0]["category_id"]);*/

                    }
                }

                $criteria->select = $criteria->order = null;

                //Profiles::model()->updateCounters(array("day" => 1), $criteria);


                $param_arr_temp = $indexes_arr = array();

                foreach($subscriber_data[1] as $index=>$value){

                    $index1 = ":cti" . $index;
                    $index2 = ":cgi" . $index;
                    $index3 = ":ci" . $index;
                    $index4 = ":ph" . $index;
                    $index5 = ":si" . $index;
                    $index6 = ":stt" . $index;
                    $index7 = ":sc" . $index;


                    $param_arr_temp[$index1] = $value[1][0];                    //content id
                    $param_arr_temp[$index2] = $value[0]["charging_id"];
                    $param_arr_temp[$index3] = $value[0]["category_id"];
                    $param_arr_temp[$index4] = $value[0]["phone_number"];
                    $param_arr_temp[$index5] = $index;
                    $param_arr_temp[$index6] = $value[1][1];                    //content
                    $param_arr_temp[$index7] = $value[0]["shortcode"];

                    $indexes_arr[] = "({$index1}, {$index2}, {$index3}, {$index4}, {$index5}, {$index6}, {$index7}, :nt, :stm)";
                }


                $add_params = array(":nt"=>"Рассылка", ":stm"=>date("Y-m-d H:i:s"));
                $param_arr = array_merge($param_arr_temp, $add_params);

                $values = implode(",", $indexes_arr);


                $sql = "INSERT INTO tbl_log_mt(content_id, charging_id, cat_id, phone_number, subscriber_id, sent_text, shortcode, note, sent_time) VALUES {$values}";
                $parameters = $param_arr;


                Yii::app()->db->createCommand($sql)->execute($parameters);

            }

            $fileTerminated = fopen('script_logs/script_center.log', 'a');
            $date_file_end = date("Y-m-d H:i:s").";" . Yii::app()->createAbsoluteUrl(Yii::app()->request->url) . ";";
            fwrite($fileTerminated, $date_file_end . $id . ";\tstopped;\n");
            fclose($fileTerminated);

        }
        else{

            die("There is no any appropriate abonent");

        }
    }

    public function actionDistributeSMSLIC( $is_rebill_enabled = false ){

        ////////////////////////////////////////LOG///////////////////////////////////////////////////

        $file = fopen('script_logs/script_center.log', 'a');
        $date_file = date("Y-m-d H:i:s").";" . Yii::app()->createAbsoluteUrl(Yii::app()->request->url) . ";";
        $id = mb_substr(md5($date_file), 0, 7,  "utf-8");
        $status = "\tstarted;\n";
        fwrite($file, $date_file . $id  .';'. $status);
        fclose($file);

        ////////////////////////////////////////LOG///////////////////////////////////////////////////



        $CAT_ID = 2;                                //Love is Carrots ID
        $URL_TEXT = "В течение суток Вы можете загрузить 1 ед. контента здесь: ";

        $non_dlvrd_numbers = array();

        if($is_rebill_enabled  == true){

            $non_dlvrd_numbers = DeliveryInfo::updateDlvSt($CAT_ID);                            //returns array of phone number and related status

        }

        if($subs_number = Subscribers::model()->count($condition = "is_subscribed=:is AND category_id=:ci AND (TIMESTAMPDIFF(DAY,date(subscribe_time),CURRENT_TIMESTAMP()))%2=:st AND date(subscribe_time)!=:stn", $params = array(":is"=>1, ":ci" => $CAT_ID, ":st" => 0, ":stn" => date("Y-m-d"))))
        {
            //$minDay = Profiles::model()->getMinDay($CAT_ID);
            /*$maxDay = Profiles::model()->getMaxDay($CAT_ID);


            if( $maxDay == false ){

                die("The max day is empty");

            }*/
            //die($subs_number);

            $content_container = Content::getBulkContentAdv(/*$maxDay, */$CAT_ID);
            $urls = Scripts::generateLinks($CAT_ID, $subs_number);

            if($subs_number > 1){

                $subscriber_data = array();
                $subscribers = Subscribers::model()->findAll($condition  . " ORDER BY id ASC", $params);

                foreach($subscribers as $value){

                    if (file_exists("script_logs/stop_" . $id . ".txt")) {
                        die();
                    }

                    if(Content::checkMNP($value->phone_number) == 02){

                        if($is_rebill_enabled  == true){

                            if(!in_array($value->phone_number, $non_dlvrd_numbers)){

                                $fileTerminated = fopen('script_logs/script_center.log', 'a');
                                $date_file_end = date("Y-m-d H:i:s").";" . Yii::app()->createAbsoluteUrl(Yii::app()->request->url) . ";";
                                fwrite($fileTerminated, $date_file_end . $id . ";\tstopped: non-delivered number not found\n");
                                fclose($fileTerminated);

                                die("non-delivered number not found");
                            }
                            else{

                                $subscriber_data[0][]=$value->id;
                                $subscriber_data[1][$value->id][] = array("phone_number"=>$value->phone_number,
                                    "shortcode"=>$value->shortcode,
                                    "charging_id"=>$value->charging_id,
                                    "category_id"=>$value->category_id,
                                    "subscribe_time"=>$value->subscribe_time);

                            }
                        }
                        else{

                            $subscriber_data[0][]=$value->id;
                            $subscriber_data[1][$value->id][] = array("phone_number"=>$value->phone_number,
                                "shortcode"=>$value->shortcode,
                                "charging_id"=>$value->charging_id,
                                "category_id"=>$value->category_id,
                                "subscribe_time"=>$value->subscribe_time);

                        }

                    }
                }

                if(!isset($subscriber_data[0])){
                    die("There is no any receiver");
                }

                $criteria = new CDbCriteria();
                $criteria->addInCondition("subscriber_id",$subscriber_data[0]);
                $criteria->select = "sex, age, relation, physics, day, subscriber_id";
                $criteria->order = "subscriber_id ASC";

                $profiles = Profiles::model()->findAll($criteria);

                //$nots = Scripts::getNotifications($profiles);

                foreach($profiles as $index => $value){

                    if (file_exists("script_logs/stop_" . $id . ".txt")) {
                        die();
                    }


                    if(isset($content_container[$value->sex][$value->age][$value->relation][$value->physics][$value->day])){

                        $SMSContent = $content_container[$value->sex][$value->age][$value->relation][$value->physics][$value->day];

                        if($index < count($urls->link)){

                            if(isset($urls->link[$index])){
                                $SMSContent[1] = $SMSContent[1] . "\n" . $URL_TEXT .  $urls->link[$index]->short;
                            }
                            else{
                                $SMSContent[1] = $SMSContent[1] . "\n" . $URL_TEXT .  $urls->link[(count($urls->link) - 1)]->short;
                            }

                        }

                        $subscriber_data[1][$value->subscriber_id][] = $SMSContent;
                    }

                    else{
                        $subscriber_data[1][$value->subscriber_id][] = array(null, Yii::app()->params["messages"]["common"]["SMS_default"]);

                        //Когда отсутствуте контент будет отправлено сандартное сообщение с нулевым чаржингом (10) и без указания категории, чтобы избежать повторной оптравки механизмом ребиллинга

                        $subscriber_data[1][$value->subscriber_id][0]["charging_id"] = "10";
                        $subscriber_data[1][$value->subscriber_id][0]["category_id"] = null;
                    }

                    $subsriber_id = $value->subscriber_id;


                    Scripts::sendSMS_MT($subscriber_data[1][$subsriber_id][1][1],
                        $subscriber_data[1][$subsriber_id][0]["phone_number"],
                        $subscriber_data[1][$subsriber_id][0]["shortcode"],
                        $subscriber_data[1][$subsriber_id][0]["charging_id"],
                        $subscriber_data[1][$subsriber_id][0]["category_id"]);

                }

                $profiles_number = count($profiles);

                //if($profiles_number < $subs_number){                              // IF there are subscribers without profile

                //    $url_iterator = $profiles_number;
                    $array_keys = array_keys($subscriber_data[1]);

                    foreach($subscriber_data[1] as $index => $value){

                        if(!isset($value[1])){

                            if (file_exists("script_logs/stop_" . $id . ".txt")) {
                                die();
                            }

                            $sub_date = strtotime(explode(" ", $value[0]["subscribe_time"])[0]);
                            $date = strtotime(date("Y-m-d"));

                            $content_day = ($date - $sub_date)/(60*60*24*2);

                            if(isset($content_container[0][0][0][0][$content_day])){

                                $SMSContent = $content_container[0][0][0][0][$content_day];

                                $url_index = array_search($index, $array_keys);

                                if($url_index <= count($urls->link)){

                                    if(isset($urls->link[$url_index])){
                                        $SMSContent[1] = $SMSContent[1] . "\n" . $URL_TEXT .  $urls->link[$url_index]->short;
                                    }
                                    else{
                                        $SMSContent[1] = $SMSContent[1] . "\n" . $URL_TEXT .  $urls->link[(count($urls->link) - 1)]->short;
                                    }

                                }

                                $subscriber_data[1][$index][1] = $SMSContent;

                            }

                            else{
                                $subscriber_data[1][$index][1] = array(null, Yii::app()->params["messages"]["common"]["SMS_default"]);

                                //Когда отсутствуте контент будет отправлено сандартное сообщение с нулевым чаржингом (10) и без указания категории, чтобы избежать повторной оптравки механизмом ребиллинга

                                $subscriber_data[1][$index][0]["charging_id"] = "10";
                                $subscriber_data[1][$index][0]["category_id"] = null;
                            }

                            Scripts::sendSMS_MT($subscriber_data[1][$index][1][1],
                                $subscriber_data[1][$index][0]["phone_number"],
                                $subscriber_data[1][$index][0]["shortcode"],
                                $subscriber_data[1][$index][0]["charging_id"],
                                $subscriber_data[1][$index][0]["category_id"]);

                        }
                    }

                //}

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

                    if(isset($value[1])){
                        $param_arr_temp[$index1] = $value[1][0];                    //content id
                        $param_arr_temp[$index6] = $value[1][1];                    //content
                    }
                    else{
                        $param_arr_temp[$index1] = 0;                    //content id
                        $param_arr_temp[$index6] = "контент";                    //content
                    }
                    $param_arr_temp[$index2] = $value[0]["charging_id"];
                    $param_arr_temp[$index3] = $value[0]["category_id"];
                    $param_arr_temp[$index4] = $value[0]["phone_number"];
                    $param_arr_temp[$index5] = $index;

                    $param_arr_temp[$index7] = $value[0]["shortcode"];

                    $indexes_arr[] = "({$index1}, {$index2}, {$index3}, {$index4}, {$index5}, {$index6}, {$index7}, :nt, :stm)";

                }

                $add_params = array(":nt"=>"Рассылка", ":stm"=>date("Y-m-d H:i:s"));

                $param_arr = array_merge($param_arr_temp, $add_params);
                $parameters = $param_arr;

                $values = implode(",", $indexes_arr);

                $sql = "INSERT INTO tbl_log_mt(content_id, charging_id, cat_id, phone_number, subscriber_id, sent_text, shortcode, note, sent_time) VALUES {$values}";

                Yii::app()->db->createCommand($sql)->execute($parameters);
            }

            else{

                $subscriber_data = array();

                $subscribers = Subscribers::model()->find($condition  . " ORDER BY id ASC", $params);

                if(Content::checkMNP($subscribers->phone_number) != 02){

                    die("There is no any KCELL provided phone number owner");

                }

                if($is_rebill_enabled  == true){

                    if(!in_array($subscribers->phone_number, $non_dlvrd_numbers)){

                        $fileTerminated = fopen('script_logs/script_center.log', 'a');
                        $date_file_end = date("Y-m-d H:i:s").";" . Yii::app()->createAbsoluteUrl(Yii::app()->request->url) . ";";
                        fwrite($fileTerminated, $date_file_end . $id . ";\tstopped: non-delivered number not found\n");
                        fclose($fileTerminated);

                        die("There is no any non delivered message1");
                    }
                    else{

                        $subscriber_data[0][]=$subscribers->id;
                        $subscriber_data[1][$subscribers->id][] = array("phone_number"=>$subscribers->phone_number,
                            "shortcode"=>$subscribers->shortcode,
                            "charging_id"=>$subscribers->charging_id,
                            "category_id"=>$subscribers->category_id,
                            "subscribe_time"=>$subscribers->subscribe_time);

                    }
                }
                else{

                    $subscriber_data[0][]=$subscribers->id;
                    $subscriber_data[1][$subscribers->id][] = array("phone_number"=>$subscribers->phone_number,
                        "shortcode"=>$subscribers->shortcode,
                        "charging_id"=>$subscribers->charging_id,
                        "category_id"=>$subscribers->category_id,
                        "subscribe_time"=>$subscribers->subscribe_time);

                }

                $criteria = new CDbCriteria();

                $criteria->addInCondition("subscriber_id", $subscriber_data[0]);
                $criteria->select = "sex, age, relation, physics, day, subscriber_id";
                $criteria->order = "subscriber_id ASC";


                if(Profiles::model()->exists($criteria)){

                    $profiles = Profiles::model()->find($criteria);

                    //$nots = Scripts::getNotifications($profiles);

                    if(isset($content_container[$profiles->sex][$profiles->age][$profiles->relation][$profiles->physics][$profiles->day])){

                        $SMSContent = $content_container[ $profiles->sex ][ $profiles->age ][ $profiles->relation ][ $profiles->physics ][ $profiles->day ];

                        if(count($urls->link) >= 1){

                            $SMSContent[1] = $SMSContent[1] . "\n" . $URL_TEXT .  $urls->link->short;
                        }

                        $subscriber_data[1][ $profiles->subscriber_id ][] = $SMSContent;
                    }

                    else{

                        $subscriber_data[1][$profiles->subscriber_id][] = array(null, Yii::app()->params["messages"]["common"]["SMS_default"]);

                        //Когда отсутствуте контент будет  отправлено сандартное сообщение с нулевым чаржингом (10) и без указания категории, чтобы избежать повторной оптравки механизмом ребиллинга

                        $subscriber_data[1][$profiles->subscriber_id][0]["charging_id"] = "10";
                        $subscriber_data[1][$profiles->subscriber_id][0]["category_id"] = null;

                    }

                    $subsriber_id = $profiles->subscriber_id;


                    Scripts::sendSMS_MT($subscriber_data[1][$subsriber_id][1][1],
                        $subscriber_data[1][$subsriber_id][0]["phone_number"],
                        $subscriber_data[1][$subsriber_id][0]["shortcode"],
                        $subscriber_data[1][$subsriber_id][0]["charging_id"],
                        $subscriber_data[1][$subsriber_id][0]["category_id"]);


                }
                else{

                    $subsriber_id = $subscribers->id;

                        if(!isset($subscriber_data[1][$subsriber_id][1])){

                            $sub_date = strtotime(explode(" ", $subscriber_data[1][$subsriber_id][0]["subscribe_time"])[0]);
                            //$sub_date = strtotime(explode(" ", $value[0]["subscribe_time"])[0]);
                            $date = strtotime(date("Y-m-d"));

                            $content_day = ($date - $sub_date) / (60*60*24*2);

                            if(isset($content_container[0][0][0][0][$content_day])){

                                $SMSContent = $content_container[0][0][0][0][$content_day];

                                $SMSContent[1] = $SMSContent[1] . "\n" . $URL_TEXT .  $urls->link[0]->short;

                                $subscriber_data[1][$subsriber_id][1] = $SMSContent;
                            }

                            else{
                                $subscriber_data[1][$subsriber_id][1] = array(null, Yii::app()->params["messages"]["common"]["SMS_default"]);

                                //Когда отсутствуте контент будет отправлено сандартное сообщение с нулевым чаржингом (10) и без указания категории, чтобы избежать повторной оптравки механизмом ребиллинга

                                $subscriber_data[1][$subsriber_id][0]["charging_id"] = "10";
                                $subscriber_data[1][$subsriber_id][0]["category_id"] = null;
                            }

                            Scripts::sendSMS_MT($subscriber_data[1][$subsriber_id][1][1],
                                $subscriber_data[1][$subsriber_id][0]["phone_number"],
                                $subscriber_data[1][$subsriber_id][0]["shortcode"],
                                $subscriber_data[1][$subsriber_id][0]["charging_id"],
                                $subscriber_data[1][$subsriber_id][0]["category_id"]);

                        }
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


                    $param_arr_temp[$index1] = $value[1][0];                    //content id
                    $param_arr_temp[$index2] = $value[0]["charging_id"];
                    $param_arr_temp[$index3] = $value[0]["category_id"];
                    $param_arr_temp[$index4] = $value[0]["phone_number"];
                    $param_arr_temp[$index5] = $index;
                    $param_arr_temp[$index6] = $value[1][1];                    //content
                    $param_arr_temp[$index7] = $value[0]["shortcode"];

                    $indexes_arr[] = "({$index1}, {$index2}, {$index3}, {$index4}, {$index5}, {$index6}, {$index7}, :nt, :stm)";
                }


                $add_params = array(":nt"=>"Рассылка", ":stm"=>date("Y-m-d H:i:s"));
                $param_arr = array_merge($param_arr_temp, $add_params);

                $values = implode(",", $indexes_arr);


                $sql = "INSERT INTO tbl_log_mt(content_id, charging_id, cat_id, phone_number, subscriber_id, sent_text, shortcode, note, sent_time) VALUES {$values}";
                $parameters = $param_arr;


                Yii::app()->db->createCommand($sql)->execute($parameters);

            }

            $fileTerminated = fopen('script_logs/script_center.log', 'a');
            $date_file_end = date("Y-m-d H:i:s").";" . Yii::app()->createAbsoluteUrl(Yii::app()->request->url) . ";";
            fwrite($fileTerminated, $date_file_end . $id . ";\tstopped;\n");
            fclose($fileTerminated);

        }
        else{

            die("There is no any appropriate abonent");

        }
    }

    public function actionDistributeSMSDiary($is_rebill_enabled = false){

        ////////////////////////////////////////LOG///////////////////////////////////////////////////

        $file = fopen('script_logs/script_center.log', 'a');
        $date_file = date("Y-m-d H:i:s").";" . Yii::app()->createAbsoluteUrl(Yii::app()->request->url) . ";";
        $id = mb_substr(md5($date_file), 0, 7,  "utf-8");
        $status = "\tstarted;\n";
        fwrite($file, $date_file . $id  .';'. $status);
        fclose($file);

        ////////////////////////////////////////LOG///////////////////////////////////////////////////


        $CAT_ID = 3;                                //Love is Carrots ID
        $URL_TEXT = "В течение суток Вы можете загрузить 1 ед. контента здесь:";

        $non_dlvrd_numbers = array();

        if($is_rebill_enabled  == true){

            $non_dlvrd_numbers = DeliveryInfo::updateDlvSt($CAT_ID);                            //returns array of phone number and related status

        }

        if($subs_number = Subscribers::model()->count($condition = "is_subscribed=:is AND category_id=:ci AND (TIMESTAMPDIFF(DAY,date(subscribe_time),CURRENT_TIMESTAMP()))%2=:st AND date(subscribe_time)!=:stn", $params = array(":is"=>1, ":ci" => $CAT_ID, ":st" => 0, ":stn" => date("Y-m-d")))){


            //$minDay = Profiles::model()->getMinDay($CAT_ID);
            /*$maxDay = Profiles::model()->getMaxDay($CAT_ID);


            if( $maxDay == false ){

                die("The max day is empty");

            }*/


            $content_container = Content::getBulkContentAdv(/*$maxDay, */$CAT_ID);

            $urls = Scripts::generateLinks($CAT_ID, $subs_number);

            if($subs_number > 1){

                $subscriber_data = array();
                $subscribers = Subscribers::model()->findAll($condition  . " ORDER BY id ASC", $params);

                foreach($subscribers as $value){

                    if (file_exists("script_logs/stop_" . $id . ".txt")) {
                        die();
                    }

                    if(Content::checkMNP($value->phone_number) == 02){

                        if($is_rebill_enabled  == true){

                            if(!in_array($value->phone_number, $non_dlvrd_numbers)){
                                //
                                $fileTerminated = fopen('script_logs/script_center.log', 'a');
                                $date_file_end = date("Y-m-d H:i:s").";" . Yii::app()->createAbsoluteUrl(Yii::app()->request->url) . ";";
                                fwrite($fileTerminated, $date_file_end . $id . ";\tstopped: non-delivered number not found\n");
                                fclose($fileTerminated);

                                die("non-delivered number not found");
                            }
                            else{

                                $subscriber_data[0][]=$value->id;
                                $subscriber_data[1][$value->id][] = array("phone_number"=>$value->phone_number,
                                    "shortcode"=>$value->shortcode,
                                    "charging_id"=>$value->charging_id,
                                    "category_id"=>$value->category_id,
                                    "subscribe_time"=>$value->subscribe_time);

                            }
                        }
                        else{

                            $subscriber_data[0][]=$value->id;
                            $subscriber_data[1][$value->id][] = array("phone_number"=>$value->phone_number,
                                "shortcode"=>$value->shortcode,
                                "charging_id"=>$value->charging_id,
                                "category_id"=>$value->category_id,
                                "subscribe_time"=>$value->subscribe_time);

                        }

                    }
                }

                if(!isset($subscriber_data[0])){
                    die("There is no any receiver");
                }

                $criteria = new CDbCriteria();
                $criteria->addInCondition("subscriber_id",$subscriber_data[0]);
                $criteria->select = "sex, age, relation, physics, day, subscriber_id";
                $criteria->order = "subscriber_id ASC";

                $profiles = Profiles::model()->findAll($criteria);

                //$nots = Scripts::getNotifications($profiles);

                foreach($profiles as $index => $value){

                    if (file_exists("script_logs/stop_" . $id . ".txt")) {
                        die();
                    }


                    if(isset($content_container[$value->sex][$value->age][$value->relation][$value->physics][$value->day])){

                        $SMSContent = $content_container[$value->sex][$value->age][$value->relation][$value->physics][$value->day];

                        if($index <= count($urls->link)){
                            $SMSContent[1] = $SMSContent[1] . "\n" . $URL_TEXT .  $urls->link[$index]->short;
                        }

                        $subscriber_data[1][$value->subscriber_id][] = $SMSContent;
                    }

                    else{
                        $subscriber_data[1][$value->subscriber_id][] = array(null, Yii::app()->params["messages"]["common"]["SMS_default"]);

                        //Когда отсутствуте контент будет отправлено сандартное сообщение с нулевым чаржингом (10) и без указания категории, чтобы избежать повторной оптравки механизмом ребиллинга

                        $subscriber_data[1][$value->subscriber_id][0]["charging_id"] = "10";
                        $subscriber_data[1][$value->subscriber_id][0]["category_id"] = null;
                    }

                    $subsriber_id = $value->subscriber_id;


                    Scripts::sendSMS_MT($subscriber_data[1][$subsriber_id][1][1],
                        $subscriber_data[1][$subsriber_id][0]["phone_number"],
                        $subscriber_data[1][$subsriber_id][0]["shortcode"],
                        $subscriber_data[1][$subsriber_id][0]["charging_id"],
                        $subscriber_data[1][$subsriber_id][0]["category_id"]);

                }

                $profiles_number = count($profiles);

                //if($profiles_number < $subs_number){                              // IF there are subscribers without profile

                $url_iterator = $profiles_number;

                foreach($subscriber_data[1] as $index => $value){

                    if(!isset($value[1])){

                        if (file_exists("script_logs/stop_" . $id . ".txt")) {
                            die();
                        }

                        $sub_date = strtotime(explode(" ", $value[0]["subscribe_time"])[0]);
                        $date = strtotime(date("Y-m-d"));

                        $content_day = ($date - $sub_date)/(60*60*24*2);

                        if(isset($content_container[0][0][0][0][$content_day])){

                            $SMSContent = $content_container[0][0][0][0][$content_day];

                            if($url_iterator <= count($urls->link)){
                                $SMSContent[1] = $SMSContent[1] . "\n" . $URL_TEXT .  $urls->link[$url_iterator]->short;
                                $url_iterator++;
                            }

                            $subscriber_data[1][$index][1] = $SMSContent;

                        }

                        else{
                            $subscriber_data[1][$index][1] = array(null, Yii::app()->params["messages"]["common"]["SMS_default"]);

                            //Когда отсутствуте контент будет отправлено сандартное сообщение с нулевым чаржингом (10) и без указания категории, чтобы избежать повторной оптравки механизмом ребиллинга

                            $subscriber_data[1][$index][0]["charging_id"] = "10";
                            $subscriber_data[1][$index][0]["category_id"] = null;
                        }

                        Scripts::sendSMS_MT($subscriber_data[1][$index][1][1],
                            $subscriber_data[1][$index][0]["phone_number"],
                            $subscriber_data[1][$index][0]["shortcode"],
                            $subscriber_data[1][$index][0]["charging_id"],
                            $subscriber_data[1][$index][0]["category_id"]);

                    }
                }

                //}

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

                    if(isset($value[1])){
                        $param_arr_temp[$index1] = $value[1][0];                    //content id
                        $param_arr_temp[$index6] = $value[1][1];                    //content
                    }
                    else{
                        $param_arr_temp[$index1] = 0;                    //content id
                        $param_arr_temp[$index6] = "контент";                    //content
                    }
                    $param_arr_temp[$index2] = $value[0]["charging_id"];
                    $param_arr_temp[$index3] = $value[0]["category_id"];
                    $param_arr_temp[$index4] = $value[0]["phone_number"];
                    $param_arr_temp[$index5] = $index;

                    $param_arr_temp[$index7] = $value[0]["shortcode"];

                    $indexes_arr[] = "({$index1}, {$index2}, {$index3}, {$index4}, {$index5}, {$index6}, {$index7}, :nt, :stm)";

                }

                $add_params = array(":nt"=>"Рассылка", ":stm"=>date("Y-m-d H:i:s"));

                $param_arr = array_merge($param_arr_temp, $add_params);
                $parameters = $param_arr;

                $values = implode(",", $indexes_arr);

                $sql = "INSERT INTO tbl_log_mt(content_id, charging_id, cat_id, phone_number, subscriber_id, sent_text, shortcode, note, sent_time) VALUES {$values}";

                Yii::app()->db->createCommand($sql)->execute($parameters);
            }

            else{

                $subscriber_data = array();

                $subscribers = Subscribers::model()->find($condition  . " ORDER BY id ASC", $params);

                if(Content::checkMNP($subscribers->phone_number) != 02){

                    die("There is no any KCELL provided phone number owner");

                }

                if($is_rebill_enabled  == true){

                    if(!in_array($subscribers->phone_number, $non_dlvrd_numbers)){
                        die("There is no any non delivered message1");
                    }
                    else{

                        $subscriber_data[0][]=$subscribers->id;
                        $subscriber_data[1][$subscribers->id][] = array("phone_number"=>$subscribers->phone_number,
                            "shortcode"=>$subscribers->shortcode,
                            "charging_id"=>$subscribers->charging_id,
                            "category_id"=>$subscribers->category_id,
                            "subscribe_time"=>$subscribers->subscribe_time);

                    }
                }
                else{

                    $subscriber_data[0][]=$subscribers->id;
                    $subscriber_data[1][$subscribers->id][] = array("phone_number"=>$subscribers->phone_number,
                        "shortcode"=>$subscribers->shortcode,
                        "charging_id"=>$subscribers->charging_id,
                        "category_id"=>$subscribers->category_id,
                        "subscribe_time"=>$subscribers->subscribe_time);

                }

                $criteria = new CDbCriteria();

                $criteria->addInCondition("subscriber_id", $subscriber_data[0]);
                $criteria->select = "sex, age, relation, physics, day, subscriber_id";
                $criteria->order = "subscriber_id ASC";


                if(Profiles::model()->exists($criteria)){

                    $profiles = Profiles::model()->find($criteria);

                    //$nots = Scripts::getNotifications($profiles);

                    if(isset($content_container[$profiles->sex][$profiles->age][$profiles->relation][$profiles->physics][$profiles->day])){

                        $SMSContent = $content_container[ $profiles->sex ][ $profiles->age ][ $profiles->relation ][ $profiles->physics ][ $profiles->day ];

                        if(count($urls->link) >= 1){

                            $SMSContent[1] = $SMSContent[1] . "\n" . $URL_TEXT .  $urls->link->short;
                        }

                        $subscriber_data[1][ $profiles->subscriber_id ][] = $SMSContent;
                    }

                    else{

                        $subscriber_data[1][$profiles->subscriber_id][] = array(null, Yii::app()->params["messages"]["common"]["SMS_default"]);

                        //Когда отсутствуте контент будет  отправлено сандартное сообщение с нулевым чаржингом (10) и без указания категории, чтобы избежать повторной оптравки механизмом ребиллинга

                        $subscriber_data[1][$profiles->subscriber_id][0]["charging_id"] = "10";
                        $subscriber_data[1][$profiles->subscriber_id][0]["category_id"] = null;

                    }

                    $subsriber_id = $profiles->subscriber_id;


                    Scripts::sendSMS_MT($subscriber_data[1][$subsriber_id][1][1],
                        $subscriber_data[1][$subsriber_id][0]["phone_number"],
                        $subscriber_data[1][$subsriber_id][0]["shortcode"],
                        $subscriber_data[1][$subsriber_id][0]["charging_id"],
                        $subscriber_data[1][$subsriber_id][0]["category_id"]);


                }
                else{

                    $subsriber_id = $subscribers->id;

                    if(!isset($subscriber_data[1][$subsriber_id][1])){

                        $sub_date = strtotime(explode(" ", $subscriber_data[1][$subsriber_id][0]["subscribe_time"])[0]);
                        //$sub_date = strtotime(explode(" ", $value[0]["subscribe_time"])[0]);
                        $date = strtotime(date("Y-m-d"));

                        $content_day = ($date - $sub_date) / (60*60*24*2);

                        if(isset($content_container[0][0][0][0][$content_day])){

                            $SMSContent = $content_container[0][0][0][0][$content_day];

                            $SMSContent[1] = $SMSContent[1] . "\n" . $URL_TEXT .  $urls->link[0]->short;

                            $subscriber_data[1][$subsriber_id][1] = $SMSContent;
                        }

                        else{
                            $subscriber_data[1][$subsriber_id][1] = array(null, Yii::app()->params["messages"]["common"]["SMS_default"]);

                            //Когда отсутствуте контент будет отправлено сандартное сообщение с нулевым чаржингом (10) и без указания категории, чтобы избежать повторной оптравки механизмом ребиллинга

                            $subscriber_data[1][$subsriber_id][0]["charging_id"] = "10";
                            $subscriber_data[1][$subsriber_id][0]["category_id"] = null;
                        }

                        Scripts::sendSMS_MT($subscriber_data[1][$subsriber_id][1][1],
                            $subscriber_data[1][$subsriber_id][0]["phone_number"],
                            $subscriber_data[1][$subsriber_id][0]["shortcode"],
                            $subscriber_data[1][$subsriber_id][0]["charging_id"],
                            $subscriber_data[1][$subsriber_id][0]["category_id"]);

                    }
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


                    $param_arr_temp[$index1] = $value[1][0];                    //content id
                    $param_arr_temp[$index2] = $value[0]["charging_id"];
                    $param_arr_temp[$index3] = $value[0]["category_id"];
                    $param_arr_temp[$index4] = $value[0]["phone_number"];
                    $param_arr_temp[$index5] = $index;
                    $param_arr_temp[$index6] = $value[1][1];                    //content
                    $param_arr_temp[$index7] = $value[0]["shortcode"];

                    $indexes_arr[] = "({$index1}, {$index2}, {$index3}, {$index4}, {$index5}, {$index6}, {$index7}, :nt, :stm)";
                }


                $add_params = array(":nt"=>"Рассылка", ":stm"=>date("Y-m-d H:i:s"));
                $param_arr = array_merge($param_arr_temp, $add_params);

                $values = implode(",", $indexes_arr);


                $sql = "INSERT INTO tbl_log_mt(content_id, charging_id, cat_id, phone_number, subscriber_id, sent_text, shortcode, note, sent_time) VALUES {$values}";
                $parameters = $param_arr;


                Yii::app()->db->createCommand($sql)->execute($parameters);

            }

            $fileTerminated = fopen('script_logs/script_center.log', 'a');
            $date_file_end = date("Y-m-d H:i:s").";" . Yii::app()->createAbsoluteUrl(Yii::app()->request->url) . ";";
            fwrite($fileTerminated, $date_file_end . $id . ";\tstopped;\n");
            fclose($fileTerminated);

        }
        else{

            die("There is no any appropriate abonent");

        }
    }



    //Old function

    public function actionNotifications(){

        $subscriber_data = array();

        $notification_mssgs = array(Yii::app()->params["messages"]["2"]["MissedSex"],
            Yii::app()->params["messages"]["2"]["MissedAge"],
            Yii::app()->params["messages"]["2"]["MissedRelation"]);


        if($subscriber_number = Subscribers::model()->count($sql = array("condition"=>"is_subscribed=:is", "params"=>array(":is" => 1)))){

            if($subscriber_number > 1){

                $subscribers = Subscribers::model()->findAll($sql);

                foreach($subscribers as $value){

                    if(Content::checkMNP($value->phone_number) == 02){                      //02  - Kcell mnc

                        $subscriber_data[0][]=$value->id;
                        $subscriber_data[1][$value->id][] = array("phone_number"=>$value->phone_number,
                            "shortcode"=>$value->shortcode,
                            "charging_id"=>$value->charging_id,
                            "category_id"=>$value->category_id);

                    }

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

                        //ОТПРАВКА Вопроса

                        $sid = $profiles->subscriber_id;

                        $resultSet = Results::model()->getAttrByParam(array("subscriber_id" => $sid), array("order" => "sent_date DESC", "LIMIT" => 1));

                        if($resultSet[0] == false){

                            $question = Questions::model()->getAttrByParam(array("status" => 1, "limit" => 1), array("order" => "id ASC"));

                            if($question[0] == false){

                                echo "Question NOT FOUND1";

                            }
                            else{
                                $result = new Results();
                                $result->subscriber_id = $sid;
                                $result->question_id = $question[1][0]->id;
                                $result->note = "Первый опрос";
                                $result->sent_date = date("Y-m-d H:i:s");
                                $result->save(false);
                            }
                        }
                        else{

                            $question_id = $resultSet[1][0]->question_id;

                            $question = Questions::model()->getAttrByParam(array("status" => 1), array("order" => "id ASC"));
                            $result_number = Results::model()->count(array("condition" => "subscriber_id=:sid", "params" => array(":sid" => $sid)));

                            if($question[0] == false){
                                echo "Question NOT FOUND2";
                            }
                            else if($resultSet[1][0]->character != NULL && count($question[1]) == $result_number){
                                // Предложение кандидатур для ЗНАКОМСТВ
                            }
                            else{

                                $result = new Results();
                                $result->subscriber_id = $sid;

                                $first_question = $question[1][0];

                                $result->question_id = $first_question->id;
                                $question_msg = $first_question->question . "\n1." . $first_question->variant1 . "\n2." . $first_question->variant2 . "\n3." .  $first_question->variant3;

                                foreach($question[1] as $value){

                                    if($value->id > $question_id){

                                        $question_msg = $value->question . "\n1." . $value->variant1 . "\n2." . $value->variant2 . "\n3." .  $value->variant3;
                                        $result->question_id = $value->id;

                                    }
                                }

                                $subscriber_data[1][$value->subscriber_id][] = array(null, $question_msg);

                                $resultSet2 = Results::model()->getAttrByParam(array("subscriber_id" => $sid, "question_id" => $result->question_id), array("limit" => 1));

                                if($resultSet2[0] == true){

                                    $resultSet2[1][0]->sent_date = date("Y-m-d H:i:s");
                                    $resultSet2[1][0]->save(false);

                                }
                                else{
                                    $result->sent_date = date("Y-m-d H:i:s");
                                    $result->note = "Опрос";
                                    $result->save(false);

                                }


                            }
                        }
                    }

                    if(!isset($subscriber_data[1][$value->subscriber_id][1])){
                        echo "Уведомление не найдено1";
                    }
                    else{
                        Scripts::sendSMS_MT($subscriber_data[1][$value->subscriber_id][1][1],
                            $subscriber_data[1][$value->subscriber_id][0]["phone_number"],
                            $subscriber_data[1][$value->subscriber_id][0]["shortcode"],
                            $subscriber_data[1][$value->subscriber_id][0]["charging_id"]);
                    }

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


                    $param_arr_temp[$index1] = $value[1][0];                    //content id
                    $param_arr_temp[$index2] = $value[0]["charging_id"];
                    $param_arr_temp[$index3] = $value[0]["category_id"];
                    $param_arr_temp[$index4] = $value[0]["phone_number"];
                    $param_arr_temp[$index5] = $index;
                    $param_arr_temp[$index6] = $value[1][1];                    //content
                    $param_arr_temp[$index7] = $value[0]["shortcode"];

                    $indexes_arr[] = "({$index1}, {$index2}, {$index3}, {$index4}, {$index5}, {$index6}, {$index7}, :nt, :stm)";
                }


                $add_params = array(":nt"=>"Опрос", ":stm"=>date("Y-m-d H:i:s"));
                $param_arr = array_merge($param_arr_temp, $add_params);

                $values = implode(",", $indexes_arr);


                $sql = "INSERT INTO tbl_log_mt(content_id, charging_id, cat_id, phone_number, subscriber_id, sent_text, shortcode, note, sent_time) VALUES {$values}";
                $parameters = $param_arr;


                Yii::app()->db->createCommand($sql)->execute($parameters);

            }
            else{

                $subscribers = Subscribers::model()->find($sql);


                if(Content::checkMNP($subscribers->phone_number) != 02){                      //02  - Kcell mnc

                    die("There is no Kcell abonents");

                }

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
                    //ОТПРАВКА Вопроса

                    $sid = $profiles->subscriber_id;

                    $resultSet = Results::model()->getAttrByParam(array("subscriber_id" => $sid), array("order" => "sent_date DESC", "limit" => 1));

                    if($resultSet[0] == false){

                        $question = Questions::model()->getAttrByParam(array("status" => 1), array("order" => "id ASC", "limit" => 1));

                        if($question[0] == false){

                            echo "Question NOT FOUND1";

                        }
                        else{

                            $first_question = $question[1][0];

                            $result = new Results();
                            $result->subscriber_id = $sid;
                            $result->question_id = $first_question->id;
                            $result->note = "Первый опрос";
                            $result->sent_date = date("Y-m-d H:i:s");
                            $result->save(false);

                            $question_msg = $first_question->question . "\n1." . $first_question->variant1 . "\n2." . $first_question->variant2 . "\n3." .  $first_question->variant3;
                            $subscriber_data[1][$profiles->subscriber_id][] = array(null, $question_msg);
                        }
                    }
                    else{

                        $question_id = $resultSet[1][0]->question_id;

                        $question = Questions::model()->getAttrByParam(array("status" => 1), array("order" => "id ASC"));
                        $result_number = Results::model()->count(array("condition" => "subscriber_id=:sid", "params" => array(":sid" => $sid)));

                        if($question[0] == false){
                            echo "Question NOT FOUND2";
                        }
                        else if($resultSet[1][0]->character != NULL && count($question[1]) == $result_number){

                            // Предложение кандидатур для ЗНАКОМСТВ
                        }
                        else{

                            $result = new Results();
                            $result->subscriber_id = $sid;

                            $first_question = $question[1][0];

                            $result->question_id = $first_question->id;
                            $question_msg = $first_question->question . "\n1." . $first_question->variant1 . "\n2." . $first_question->variant2 . "\n3." .  $first_question->variant3;

                            foreach($question[1] as $value){

                                if($value->id > $question_id){

                                    $question_msg = $value->question . "\n1." . $value->variant1 . "\n2." . $value->variant2 . "\n3." .  $value->variant3;
                                    $result->question_id = $value->id;

                                }
                            }

                            $subscriber_data[1][$profiles->subscriber_id][] = array(null, $question_msg);

                            $resultSet2 = Results::model()->getAttrByParam(array("subscriber_id" => $sid, "question_id" => $result->question_id), array("limit" => 1));

                            if($resultSet2[0] == true){

                                $resultSet2[1][0]->sent_date = date("Y-m-d H:i:s");
                                $resultSet2[1][0]->save(false);

                            }
                            else{
                                $result->sent_date = date("Y-m-d H:i:s");
                                $result->note = "Опрос";
                                $result->save(false);

                            }
                        }
                    }
                }

                if(!isset($subscriber_data[1][$profiles->subscriber_id][1])){
                    echo "Уведомление не найдено2";
                }
                else{
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


                        $param_arr_temp[$index1] = $value[1][0];                    //content id
                        $param_arr_temp[$index2] = $value[0]["charging_id"];
                        $param_arr_temp[$index3] = $value[0]["category_id"];
                        $param_arr_temp[$index4] = $value[0]["phone_number"];
                        $param_arr_temp[$index5] = $index;
                        $param_arr_temp[$index6] = $value[1][1];                    //content
                        $param_arr_temp[$index7] = $value[0]["shortcode"];

                        $indexes_arr[] = "({$index1}, {$index2}, {$index3}, {$index4}, {$index5}, {$index6}, {$index7}, :nt, :stm)";
                    }


                    $add_params = array(":nt"=>"Опрос", ":stm"=>date("Y-m-d H:i:s"));
                    $param_arr = array_merge($param_arr_temp, $add_params);

                    $values = implode(",", $indexes_arr);


                    $sql = "INSERT INTO tbl_log_mt(content_id, charging_id, cat_id, phone_number, subscriber_id, sent_text, shortcode, note, sent_time) VALUES {$values}";
                    $parameters = $param_arr;


                    Yii::app()->db->createCommand($sql)->execute($parameters);
                }

            }

        }

    }

    public function actionNotificationsDiary(){

        ////////////////////////////////////////LOG///////////////////////////////////////////////////

        $file = fopen('script_logs/script_center.log', 'a');
        $date_file = date("Y-m-d H:i:s").";" . Yii::app()->createAbsoluteUrl(Yii::app()->request->url) . ";";
        $id = mb_substr(md5($date_file), 0, 7,  "utf-8");
        $status = "\tstarted;\n";
        fwrite($file, $date_file . $id  .';'. $status);
        fclose($file);

        ////////////////////////////////////////LOG///////////////////////////////////////////////////

        $subscriber_data = array();

        $CAT_ID = 3;                                                    // 3 - Health Diary ID
        $free_charging = "10";

        $notification_mssgs = array(Yii::app()->params["messages"][$CAT_ID]["MissedSex"],
            Yii::app()->params["messages"][$CAT_ID]["MissedAge"],
            Yii::app()->params["messages"][$CAT_ID]["MissedPhysics"]);


        if($subscriber_number = Subscribers::model()->count($sql = array("condition"=>"is_subscribed=:is AND category_id=:ci AND (TIMESTAMPDIFF(DAY,date(subscribe_time),CURRENT_TIMESTAMP()))%6=:st", "params"=>array(":is" => 1, ":ci" => $CAT_ID, ":st" => 0)))){

            if($subscriber_number > 1){

                $subscribers = Subscribers::model()->findAll($sql);

                foreach($subscribers as $value){

                    if(Content::checkMNP($value->phone_number) == 02){                      //02  - Kcell mnc

                        $subscriber_data[0][]=$value->id;
                        $subscriber_data[1][$value->id][] = array("phone_number"=>$value->phone_number,
                            "shortcode"=>$value->shortcode,
                            "charging_id"=>$free_charging,
                            "category_id"=>$value->category_id);

                    }

                }

                $criteria = new CDbCriteria();
                $criteria->addInCondition("subscriber_id",$subscriber_data[0]);
                $criteria->select = "sex, age, physics, day, subscriber_id";
                $criteria->order = "subscriber_id ASC";

                $profiles = Profiles::model()->findAll($criteria);

                foreach($profiles as $value){

                    if($value->sex  == 0){

                        $subscriber_data[1][$value->subscriber_id][] = array(null, $notification_mssgs[0]);
                    }
                    else if($value->age  == 0){
                        $subscriber_data[1][$value->subscriber_id][] = array(null, $notification_mssgs[1]);
                    }
                    else if($value->physics  == 0){
                        $subscriber_data[1][$value->subscriber_id][] = array(null, $notification_mssgs[2]);
                    }
                    else{

                        //ОТПРАВКА Вопроса

                        $sid = $value->subscriber_id;

                        $resultSet = Results::model()->getAttrByParam(array("subscriber_id" => $sid), array("order" => "sent_date DESC"));

                        if($resultSet[0] == false){

                            $question = Questions::model()->getAttrByParam(array("status" => 1, "category_id" => $CAT_ID), array("order" => "id ASC"));

                            if($question[0] == false){

                                echo "Question NOT FOUND1";

                            }
                            else{

                                $first_question = $question[1][0];

                                $result = new Results();
                                $result->subscriber_id = $sid;
                                $result->question_id = $first_question->id;
                                $result->category_id = $first_question->category_id;
                                $result->note = "Первый опрос";
                                $result->sent_date = date("Y-m-d H:i:s");
                                $result->save(false);

                                $question_msg = $first_question->question . "\n1." . $first_question->variant1 . "\n2." . $first_question->variant2 . "\n3." .  $first_question->variant3;
                                $subscriber_data[1][$value->subscriber_id][] = array(null, $question_msg);
                            }
                        }
                        else{

                            $question_id = $resultSet[1][0]->question_id;

                            $question = Questions::model()->getAttrByParam(array("status" => 1, "category_id" => $CAT_ID), array("order" => "id ASC"));
                            $result_number = Results::model()->count(array("condition" => "subscriber_id=:sid", "params" => array(":sid" => $sid)));

                            if($question[0] == false){
                                echo "Question NOT FOUND2";
                            }
                            else if($resultSet[1][0]->character != NULL && count($question[1]) == $result_number){
                                // Предложение кандидатур для ЗНАКОМСТВ
                            }
                            else{

                                $result = new Results();
                                $result->subscriber_id = $sid;

                                $first_question = $question[1][0];

                                $result->question_id = $first_question->id;
                                $result->category_id = $first_question->category_id;
                                $question_msg = $first_question->question . "\n1." . $first_question->variant1 . "\n2." . $first_question->variant2 . "\n3." .  $first_question->variant3;

                                foreach($question[1] as $q_value){

                                    if($q_value->id > $question_id){

                                        $question_msg = $q_value->question . "\n1." . $q_value->variant1 . "\n2." . $q_value->variant2 . "\n3." .  $q_value->variant3;
                                        $result->question_id = $q_value->id;
                                        break;
                                    }
                                }

                                $subscriber_data[1][$value->subscriber_id][] = array(null, $question_msg);

                                $resultSet2 = Results::model()->getAttrByParam(array("subscriber_id" => $sid, "question_id" => $result->question_id));

                                if($resultSet2[0] == true){

                                    $resultSet2[1][0]->sent_date = date("Y-m-d H:i:s");
                                    $resultSet2[1][0]->save(false);

                                }
                                else{
                                    $result->sent_date = date("Y-m-d H:i:s");
                                    $result->note = "Опрос";
                                    $result->save(false);

                                }


                            }
                        }
                    }

                    if(!isset($subscriber_data[1][$value->subscriber_id][1])){
                        echo "Уведомление не найдено1";
                    }
                    else{

                        Scripts::sendSMS_MT($subscriber_data[1][$value->subscriber_id][1][1],
                            $subscriber_data[1][$value->subscriber_id][0]["phone_number"],
                            $subscriber_data[1][$value->subscriber_id][0]["shortcode"],
                            $free_charging);
                    }

                }

                //$profiles_number = count($profiles);

                //if($profiles_number < $subscriber_number){                              // IF there are subscribers without profile

                foreach($subscriber_data[1] as $s_index => $s_value){

                    if(!isset($s_value[1])){

                        $subscriber_data[1][$s_index][1] = array(null, $notification_mssgs[0]);

                        Scripts::sendSMS_MT($subscriber_data[1][$s_index][1][1],
                            $subscriber_data[1][$s_index][0]["phone_number"],
                            $subscriber_data[1][$s_index][0]["shortcode"],
                            $free_charging);

                    }
                }

                //}

                $param_arr_temp = $indexes_arr = array();

                foreach($subscriber_data[1] as $index=>$value){

                    $index1 = ":cti" . $index;
                    $index2 = ":cgi" . $index;
                    $index3 = ":ci" . $index;
                    $index4 = ":ph" . $index;
                    $index5 = ":si" . $index;
                    $index6 = ":stt" . $index;
                    $index7 = ":sc" . $index;


                    $param_arr_temp[$index1] = $value[1][0];                    //content id
                    $param_arr_temp[$index2] = $value[0]["charging_id"];
                    $param_arr_temp[$index3] = $value[0]["category_id"];
                    $param_arr_temp[$index4] = $value[0]["phone_number"];
                    $param_arr_temp[$index5] = $index;
                    $param_arr_temp[$index6] = $value[1][1];                    //content
                    $param_arr_temp[$index7] = $value[0]["shortcode"];

                    $indexes_arr[] = "({$index1}, {$index2}, {$index3}, {$index4}, {$index5}, {$index6}, {$index7}, :nt, :stm)";
                }


                $add_params = array(":nt"=>"Опрос", ":stm"=>date("Y-m-d H:i:s"));
                $param_arr = array_merge($param_arr_temp, $add_params);

                $values = implode(",", $indexes_arr);


                $sql = "INSERT INTO tbl_log_mt(content_id, charging_id, cat_id, phone_number, subscriber_id, sent_text, shortcode, note, sent_time) VALUES {$values}";
                $parameters = $param_arr;


                Yii::app()->db->createCommand($sql)->execute($parameters);

            }
            else{

                $subscribers = Subscribers::model()->find($sql);


                if(Content::checkMNP($subscribers->phone_number) != 02){                      //02  - Kcell mnc

                    die("There is no Kcell abonents");

                }

                $subscriber_data[0][] = $subscribers->id;
                $subscriber_data[1][$subscribers->id][] = array(
                    "phone_number"=>$subscribers->phone_number,
                    "shortcode"=>$subscribers->shortcode,
                    "charging_id"=>$free_charging,
                    "category_id"=>$subscribers->category_id);


                $criteria = new CDbCriteria();
                $criteria->addInCondition("subscriber_id",$subscriber_data[0]);
                $criteria->select = "sex, age, physics, day, subscriber_id";
                $criteria->order = "subscriber_id ASC";

                if(Profiles::model()->exists($criteria)){
                    $profiles = Profiles::model()->find($criteria);


                    if($profiles->sex  == 0){

                        $subscriber_data[1][$profiles->subscriber_id][] = array(null, $notification_mssgs[0]);
                    }
                    else if($profiles->age  == 0){
                        $subscriber_data[1][$profiles->subscriber_id][] = array(null, $notification_mssgs[1]);
                    }
                    else if($profiles->physics  == 0){
                        $subscriber_data[1][$profiles->subscriber_id][] = array(null, $notification_mssgs[2]);
                    }
                    else{
                        //ОТПРАВКА Вопроса

                        $sid = $profiles->subscriber_id;

                        $resultSet = Results::model()->getAttrByParam(array("subscriber_id" => $sid), array("order" => "sent_date DESC"));

                        if($resultSet[0] == false){

                            $question = Questions::model()->getAttrByParam(array("status" => 1, "category_id" => $CAT_ID), array("order" => "id ASC"));

                            if($question[0] == false){

                                echo "Question NOT FOUND1";

                            }
                            else{

                                $first_question = $question[1][0];

                                $result = new Results();
                                $result->subscriber_id = $sid;
                                $result->question_id = $first_question->id;
                                $result->category_id = $first_question->category_id;
                                $result->note = "Первый опрос";
                                $result->sent_date = date("Y-m-d H:i:s");
                                $result->save(false);

                                $question_msg = $first_question->question . "\n1." . $first_question->variant1 . "\n2." . $first_question->variant2 . "\n3." .  $first_question->variant3;
                                $subscriber_data[1][$profiles->subscriber_id][] = array(null, $question_msg);
                            }
                        }
                        else{

                            $question_id = $resultSet[1][0]->question_id;

                            $question = Questions::model()->getAttrByParam(array("status" => 1, "category_id" => $CAT_ID), array("order" => "id ASC"));
                            $result_number = Results::model()->count(array("condition" => "subscriber_id=:sid", "params" => array(":sid" => $sid)));

                            if($question[0] == false){
                                echo "Question NOT FOUND2";
                            }
                            else if($resultSet[1][0]->character != NULL && count($question[1]) == $result_number){

                                // Предложение кандидатур для ЗНАКОМСТВ
                            }
                            else{

                                $result = new Results();
                                $result->subscriber_id = $sid;

                                $first_question = $question[1][0];

                                $result->question_id = $first_question->id;
                                $result->category_id = $first_question->category_id;
                                $question_msg = $first_question->question . "\n1." . $first_question->variant1 . "\n2." . $first_question->variant2 . "\n3." .  $first_question->variant3;

                                foreach($question[1] as $value){

                                    if($value->id > $question_id){

                                        $question_msg = $value->question . "\n1." . $value->variant1 . "\n2." . $value->variant2 . "\n3." .  $value->variant3;
                                        $result->question_id = $value->id;
                                        break;

                                    }
                                }

                                $subscriber_data[1][$profiles->subscriber_id][] = array(null, $question_msg);

                                $resultSet2 = Results::model()->getAttrByParam(array("subscriber_id" => $sid, "question_id" => $result->question_id));

                                if($resultSet2[0] == true){

                                    $resultSet2[1][0]->sent_date = date("Y-m-d H:i:s");
                                    $resultSet2[1][0]->save(false);

                                }
                                else{
                                    $result->sent_date = date("Y-m-d H:i:s");
                                    $result->note = "Опрос";
                                    $result->save(false);

                                }
                            }
                        }
                    }

                    if(!isset($subscriber_data[1][$profiles->subscriber_id][1])){
                        echo "Уведомление не найдено2";
                    }
                    else{

                        Scripts::sendSMS_MT($subscriber_data[1][$profiles->subscriber_id][1][1],
                            $subscriber_data[1][$profiles->subscriber_id][0]["phone_number"],
                            $subscriber_data[1][$profiles->subscriber_id][0]["shortcode"],
                            $free_charging);

                    }
                }

                else{

                    $subsriber_id = $subscribers->id;

                    if(!isset($subscriber_data[1][$subsriber_id][1])){

                        $subscriber_data[1][$subsriber_id][1] = array(null, $notification_mssgs[0]);

                        Scripts::sendSMS_MT($subscriber_data[1][$subsriber_id][1][1],
                            $subscriber_data[1][$subsriber_id][0]["phone_number"],
                            $subscriber_data[1][$subsriber_id][0]["shortcode"],
                            $free_charging);

                    }
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


                        $param_arr_temp[$index1] = $value[1][0];                    //content id
                        $param_arr_temp[$index2] = $value[0]["charging_id"];
                        $param_arr_temp[$index3] = $value[0]["category_id"];
                        $param_arr_temp[$index4] = $value[0]["phone_number"];
                        $param_arr_temp[$index5] = $index;
                        $param_arr_temp[$index6] = $value[1][1];                    //content
                        $param_arr_temp[$index7] = $value[0]["shortcode"];

                        $indexes_arr[] = "({$index1}, {$index2}, {$index3}, {$index4}, {$index5}, {$index6}, {$index7}, :nt, :stm)";
                    }


                    $add_params = array(":nt"=>"Опрос", ":stm"=>date("Y-m-d H:i:s"));
                    $param_arr = array_merge($param_arr_temp, $add_params);

                    $values = implode(",", $indexes_arr);


                    $sql = "INSERT INTO tbl_log_mt(content_id, charging_id, cat_id, phone_number, subscriber_id, sent_text, shortcode, note, sent_time) VALUES {$values}";
                    $parameters = $param_arr;


                    Yii::app()->db->createCommand($sql)->execute($parameters);


            }

        }

        ////////////////////////////////LOG/////////////////////////////////////////////

        $fileTerminated = fopen('script_logs/script_center.log', 'a');
        $date_file_end = date("Y-m-d H:i:s").";" . Yii::app()->createAbsoluteUrl(Yii::app()->request->url) . ";";
        fwrite($fileTerminated, $date_file_end . $id . ";\tstopped;\n");
        fclose($fileTerminated);

        ////////////////////////////////LOG/////////////////////////////////////////////

    }

    /**
     * User Subscription Info Notification (every 30 days)
     * */

    public function actionNotificationsSubsInfo(){

        ////////////////////////////////////////LOG///////////////////////////////////////////////////

        $file = fopen('script_logs/script_center.log', 'a');
        $date_file = date("Y-m-d H:i:s").";" . Yii::app()->createAbsoluteUrl(Yii::app()->request->url) . ";";
        $id = mb_substr(md5($date_file), 0, 7,  "utf-8");
        $status = "\tstarted;\n";
        fwrite($file, $date_file . $id  .';'. $status);
        fclose($file);

        ////////////////////////////////////////LOG///////////////////////////////////////////////////

        $subscriber_data = array();

        $free_charging = "20";

        if($subscriber_number = Subscribers::model()->count($sql = array("condition"=>"is_subscribed=:is AND (TIMESTAMPDIFF(DAY,date(subscribe_time),CURRENT_TIMESTAMP()))%30=:st AND date(subscribe_time)!=:today", "params"=>array(":is" => 1, ":st" => 0, ":today" => date("Y-m-d"))))){

            if($subscriber_number > 1){

                $subscribers = Subscribers::model()->findAll($sql);

                foreach($subscribers as $value){

                    if(Content::checkMNP($value->phone_number) == 02){                      //02  - Kcell mnc

                        $subscriber_data[0][] = $value->id;

                        $subscriber_data[1][$value->id][] = array(
                            "phone_number"  =>  $value->phone_number,
                            "shortcode"     =>  $value->shortcode,
                            "charging_id"   =>  $free_charging,
                            "category_id"   =>  $value->category_id
                        );

                        $CAT_ID = $value->category_id;
                        $subscriber_data[1][$value->id][] = array(null, Yii::app()->params["messages"][$CAT_ID]["NotificationSubsInfo"]);

                        Scripts::sendSMS_MT($subscriber_data[1][$value->id][1][1],
                            $subscriber_data[1][$value->id][0]["phone_number"],
                            $subscriber_data[1][$value->id][0]["shortcode"],
                            $free_charging);

                    }
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


                    $param_arr_temp[$index1] = $value[1][0];                    //content id
                    $param_arr_temp[$index2] = $value[0]["charging_id"];
                    $param_arr_temp[$index3] = $value[0]["category_id"];
                    $param_arr_temp[$index4] = $value[0]["phone_number"];
                    $param_arr_temp[$index5] = $index;
                    $param_arr_temp[$index6] = $value[1][1];                    //content
                    $param_arr_temp[$index7] = $value[0]["shortcode"];

                    $indexes_arr[] = "({$index1}, {$index2}, {$index3}, {$index4}, {$index5}, {$index6}, {$index7}, :nt, :stm)";
                }


                $add_params = array(":nt"=>"Уведомление 30", ":stm"=>date("Y-m-d H:i:s"));
                $param_arr = array_merge($param_arr_temp, $add_params);

                $values = implode(",", $indexes_arr);


                $sql = "INSERT INTO tbl_log_mt(content_id, charging_id, cat_id, phone_number, subscriber_id, sent_text, shortcode, note, sent_time) VALUES {$values}";
                $parameters = $param_arr;


                Yii::app()->db->createCommand($sql)->execute($parameters);

            }
            else{

                $subscribers = Subscribers::model()->find($sql);


                if(Content::checkMNP($subscribers->phone_number) != 02){                      //02  - Kcell mnc

                    die("There is no Kcell abonents");

                }

                $subscriber_data[0][] = $subscribers->id;
                $subscriber_data[1][$subscribers->id][] = array(
                    "phone_number"  =>  $subscribers->phone_number,
                    "shortcode"     =>  $subscribers->shortcode,
                    "charging_id"   =>  $free_charging
                );

                $CAT_ID = $subscribers->category_id;
                $subscriber_data[1][$subscribers->id][] = array(null, Yii::app()->params["messages"][$CAT_ID]["NotificationSubsInfo"]);

                Scripts::sendSMS_MT($subscriber_data[1][$subscribers->id][1][1],
                    $subscriber_data[1][$subscribers->id][0]["phone_number"],
                    $subscriber_data[1][$subscribers->id][0]["shortcode"],
                    $free_charging
                );


                $param_arr_temp = $indexes_arr = array();

                foreach($subscriber_data[1] as $index=>$value){

                    $index1 = ":cti" . $index;
                    $index2 = ":cgi" . $index;
                    $index3 = ":ci" . $index;
                    $index4 = ":ph" . $index;
                    $index5 = ":si" . $index;
                    $index6 = ":stt" . $index;
                    $index7 = ":sc" . $index;


                    $param_arr_temp[$index1] = $value[1][0];                    //content id
                    $param_arr_temp[$index2] = $value[0]["charging_id"];
                    $param_arr_temp[$index3] = $value[0]["category_id"];
                    $param_arr_temp[$index4] = $value[0]["phone_number"];
                    $param_arr_temp[$index5] = $index;
                    $param_arr_temp[$index6] = $value[1][1];                    //content
                    $param_arr_temp[$index7] = $value[0]["shortcode"];

                    $indexes_arr[] = "({$index1}, {$index2}, {$index3}, {$index4}, {$index5}, {$index6}, {$index7}, :nt, :stm)";
                }


                $add_params = array(":nt"=>"Уведомление 30", ":stm"=>date("Y-m-d H:i:s"));
                $param_arr = array_merge($param_arr_temp, $add_params);

                $values = implode(",", $indexes_arr);


                $sql = "INSERT INTO tbl_log_mt(content_id, charging_id, cat_id, phone_number, subscriber_id, sent_text, shortcode, note, sent_time) VALUES {$values}";
                $parameters = $param_arr;


                Yii::app()->db->createCommand($sql)->execute($parameters);


            }

        }

        ////////////////////////////////LOG/////////////////////////////////////////////

        $fileTerminated = fopen('script_logs/script_center.log', 'a');
        $date_file_end = date("Y-m-d H:i:s").";" . Yii::app()->createAbsoluteUrl(Yii::app()->request->url) . ";";
        fwrite($fileTerminated, $date_file_end . $id . ";\tstopped;\n");
        fclose($fileTerminated);

        ////////////////////////////////LOG/////////////////////////////////////////////

    }

    public function actionImitateNotificationsLIC(){

        ////////////////////////////////////////LOG///////////////////////////////////////////////////

        $file = fopen('script_logs/script_center.log', 'a');
        $date_file = date("Y-m-d H:i:s").";" . Yii::app()->createAbsoluteUrl(Yii::app()->request->url) . ";";
        $id = mb_substr(md5($date_file), 0, 7,  "utf-8");
        $status = "\tstarted;\n";
        fwrite($file, $date_file . $id  .';'. $status);
        fclose($file);

        ////////////////////////////////////////LOG///////////////////////////////////////////////////


        $subscriber_data = array();

        $CAT_ID = 2;                                                    // 2 - Love IS Carrot ID
        $free_charging = "10";

        $notification_mssgs = array(Yii::app()->params["messages"][$CAT_ID]["MissedSex"],
            Yii::app()->params["messages"][$CAT_ID]["MissedAge"],
            Yii::app()->params["messages"][$CAT_ID]["MissedRelation"]);


        if($subscriber_number = Subscribers::model()->count($sql = array("condition"=>"is_subscribed=:is AND category_id=:ci AND (TIMESTAMPDIFF(DAY,date(subscribe_time),CURRENT_TIMESTAMP()))%6=:st AND date(subscribe_time)!=:stn", "params"=>array(":is" => 1, ":ci" => $CAT_ID, ":st" => 0, ":stn" => date("Y-m-d"))))){

            if($subscriber_number > 1){

                $subscribers = Subscribers::model()->findAll($sql);

                foreach($subscribers as $value){

                    if(Content::checkMNP($value->phone_number) == 02){                      //02  - Kcell mnc

                        $subscriber_data[0][]=$value->id;
                        $subscriber_data[1][$value->id][] = array("phone_number"=>$value->phone_number,
                            "shortcode"=>$value->shortcode,
                            "charging_id"=>$value->charging_id,
                            "category_id"=>$value->category_id);

                    }

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

                        //ОТПРАВКА Вопроса

                        $sid = $value->subscriber_id;

                        $resultSet = Results::model()->getAttrByParam(array("subscriber_id" => $sid), array("order" => "sent_date DESC"));

                        if($resultSet[0] == false){

                            $question = Questions::model()->getAttrByParam(array("status" => 1, "category_id" => $CAT_ID), array("order" => "id ASC"));

                            if($question[0] == false){

                                echo "Question NOT FOUND1";

                            }
                            else{

                                $first_question = $question[1][0];

                                $result = new Results();
                                $result->subscriber_id = $sid;
                                $result->question_id = $first_question->id;
                                $result->category_id = $first_question->category_id;
                                $result->note = "Первый опрос имитация";
                                $result->sent_date = date("Y-m-d H:i:s");
                                $result->save(false);

                                $question_msg = $first_question->question . "\n1." . $first_question->variant1 . "\n2." . $first_question->variant2 . "\n3." .  $first_question->variant3;
                                $subscriber_data[1][$value->subscriber_id][] = array(null, $question_msg);

                            }
                        }
                        else{

                            $question_id = $resultSet[1][0]->question_id;

                            $question = Questions::model()->getAttrByParam(array("status" => 1, "category_id" => $CAT_ID), array("order" => "id ASC"));
                            $result_number = Results::model()->count(array("condition" => "subscriber_id=:sid", "params" => array(":sid" => $sid)));

                            if($question[0] == false){
                                echo "Question NOT FOUND2";
                            }
                            else if($resultSet[1][0]->character != NULL && count($question[1]) == $result_number){
                                // Предложение кандидатур для ЗНАКОМСТВ
                            }
                            else{

                                $result = new Results();
                                $result->subscriber_id = $sid;

                                $first_question = $question[1][0];

                                $result->question_id = $first_question->id;
                                $result->category_id = $first_question->category_id;
                                $question_msg = $first_question->question . "\n1." . $first_question->variant1 . "\n2." . $first_question->variant2 . "\n3." .  $first_question->variant3;

                                foreach($question[1] as $value1){

                                    if($value1->id > $question_id){

                                        $question_msg = $value1->question . "\n1." . $value1->variant1 . "\n2." . $value1->variant2 . "\n3." .  $value1->variant3;
                                        $result->question_id = $value1->id;
                                        break;
                                    }
                                }

                                $subscriber_data[1][$value->subscriber_id][] = array(null, $question_msg);

                                $resultSet2 = Results::model()->getAttrByParam(array("subscriber_id" => $sid, "question_id" => $result->question_id));

                                if($resultSet2[0] == true){

                                    $resultSet2[1][0]->sent_date = date("Y-m-d H:i:s");
                                    $resultSet2[1][0]->save(false);

                                }
                                else{
                                    $result->sent_date = date("Y-m-d H:i:s");
                                    $result->note = "Опрос имитация";
                                    $result->save(false);

                                }


                            }
                        }
                    }

                    if(!isset($subscriber_data[1][$value->subscriber_id][1])){
                        echo "Уведомление не найдено1";
                    }
                    else{

                        //$subscriber_data[1][$value->subscriber_id][0]["phone_number"] == ""

                        /*Scripts::sendSMS_MT($subscriber_data[1][$value->subscriber_id][1][1],
                            $subscriber_data[1][$value->subscriber_id][0]["phone_number"],
                            $subscriber_data[1][$value->subscriber_id][0]["shortcode"],
                            $free_charging);*/
                    }

                }

                $profiles_number = count($profiles);

                //if($profiles_number < $subscriber_number){                              // IF there are subscribers without profile

                foreach($subscriber_data[1] as $index => $value){

                    if(!isset($value[1])){

                        $subscriber_data[1][$index][1] = array(null, $notification_mssgs[0]);

                        /*Scripts::sendSMS_MT($subscriber_data[1][$index][1][1],
                            $subscriber_data[1][$index][0]["phone_number"],
                            $subscriber_data[1][$index][0]["shortcode"],
                            $free_charging);*/

                    }
                }

                //}

                $param_arr_temp = $indexes_arr = array();

                foreach($subscriber_data[1] as $index=>$value){

                    $index1 = ":cti" . $index;
                    $index2 = ":cgi" . $index;
                    $index3 = ":ci" . $index;
                    $index4 = ":ph" . $index;
                    $index5 = ":si" . $index;
                    $index6 = ":stt" . $index;
                    $index7 = ":sc" . $index;


                    $param_arr_temp[$index1] = $value[1][0];                    //content id
                    $param_arr_temp[$index2] = $value[0]["charging_id"];
                    $param_arr_temp[$index3] = $value[0]["category_id"];
                    $param_arr_temp[$index4] = $value[0]["phone_number"];
                    $param_arr_temp[$index5] = $index;
                    $param_arr_temp[$index6] = $value[1][1];                    //content
                    $param_arr_temp[$index7] = $value[0]["shortcode"];

                    $indexes_arr[] = "({$index1}, {$index2}, {$index3}, {$index4}, {$index5}, {$index6}, {$index7}, :nt, :stm)";
                }


                $add_params = array(":nt"=>"Опрос имитация", ":stm"=>date("Y-m-d H:i:s"));
                $param_arr = array_merge($param_arr_temp, $add_params);

                $values = implode(",", $indexes_arr);


                $sql = "INSERT INTO tbl_log_mt(content_id, charging_id, cat_id, phone_number, subscriber_id, sent_text, shortcode, note, sent_time) VALUES {$values}";
                $parameters = $param_arr;


                Yii::app()->db->createCommand($sql)->execute($parameters);

            }
            else{

                $subscribers = Subscribers::model()->find($sql);


                if(Content::checkMNP($subscribers->phone_number) != 02){                      //02  - Kcell mnc

                    die("There is no Kcell abonents");

                }

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

                if(Profiles::model()->exists($criteria)) {

                    $profiles = Profiles::model()->find($criteria);


                    if ($profiles->sex == 0) {

                        $subscriber_data[1][$profiles->subscriber_id][] = array(null, $notification_mssgs[0]);
                    } else if ($profiles->age == 0) {
                        $subscriber_data[1][$profiles->subscriber_id][] = array(null, $notification_mssgs[1]);
                    } else if ($profiles->relation == 0) {
                        $subscriber_data[1][$profiles->subscriber_id][] = array(null, $notification_mssgs[2]);
                    } else {
                        //ОТПРАВКА Вопроса

                        $sid = $profiles->subscriber_id;

                        $resultSet = Results::model()->getAttrByParam(array("subscriber_id" => $sid), array("order" => "sent_date DESC", "limit" => 1));

                        if ($resultSet[0] == false) {

                            $question = Questions::model()->getAttrByParam(array("status" => 1, "category_id" => $CAT_ID), array("order" => "id ASC", "limit" => 1));

                            if ($question[0] == false) {

                                echo "Question NOT FOUND1";

                            } else {

                                $first_question = $question[1][0];

                                $result = new Results();
                                $result->subscriber_id = $sid;
                                $result->question_id = $first_question->id;
                                $result->category_id = $first_question->category_id;
                                $result->note = "Первый опрос имитация";
                                $result->sent_date = date("Y-m-d H:i:s");
                                $result->save(false);

                                $question_msg = $first_question->question . "\n1." . $first_question->variant1 . "\n2." . $first_question->variant2 . "\n3." . $first_question->variant3;
                                $subscriber_data[1][$profiles->subscriber_id][] = array(null, $question_msg);
                            }
                        } else {

                            $question_id = $resultSet[1][0]->question_id;

                            $question = Questions::model()->getAttrByParam(array("status" => 1, "category_id" => $CAT_ID), array("order" => "id ASC"));
                            $result_number = Results::model()->count(array("condition" => "subscriber_id=:sid", "params" => array(":sid" => $sid)));

                            if ($question[0] == false) {
                                echo "Question NOT FOUND2";
                            } else if ($resultSet[1][0]->character != NULL && count($question[1]) == $result_number) {

                                // Предложение кандидатур для ЗНАКОМСТВ
                            } else {

                                $result = new Results();
                                $result->subscriber_id = $sid;

                                $first_question = $question[1][0];

                                $result->question_id = $first_question->id;
                                $result->category_id = $first_question->category_id;
                                $question_msg = $first_question->question . "\n1." . $first_question->variant1 . "\n2." . $first_question->variant2 . "\n3." . $first_question->variant3;

                                foreach ($question[1] as $value) {

                                    if ($value->id > $question_id) {

                                        $question_msg = $value->question . "\n1." . $value->variant1 . "\n2." . $value->variant2 . "\n3." . $value->variant3;
                                        $result->question_id = $value->id;

                                    }
                                }

                                $subscriber_data[1][$profiles->subscriber_id][] = array(null, $question_msg);

                                $resultSet2 = Results::model()->getAttrByParam(array("subscriber_id" => $sid, "question_id" => $result->question_id), array("limit" => 1));

                                if ($resultSet2[0] == true) {

                                    $resultSet2[1][0]->sent_date = date("Y-m-d H:i:s");
                                    $resultSet2[1][0]->save(false);

                                } else {
                                    $result->sent_date = date("Y-m-d H:i:s");
                                    $result->note = "Опрос имитация";
                                    $result->save(false);

                                }
                            }
                        }
                    }

                    if (!isset($subscriber_data[1][$profiles->subscriber_id][1])) {
                        echo "Уведомление не найдено2";
                    } else {
                       /* Scripts::sendSMS_MT($subscriber_data[1][$profiles->subscriber_id][1][1],
                            $subscriber_data[1][$profiles->subscriber_id][0]["phone_number"],
                            $subscriber_data[1][$profiles->subscriber_id][0]["shortcode"],
                            $free_charging);*/


                    }
                }

                else{

                    $subsriber_id = $subscribers->id;

                    if(!isset($subscriber_data[1][$subsriber_id][1])){

                        $subscriber_data[1][$subsriber_id][1] = array(null, $notification_mssgs[0]);

                       /* Scripts::sendSMS_MT($subscriber_data[1][$subsriber_id][1][1],
                            $subscriber_data[1][$subsriber_id][0]["phone_number"],
                            $subscriber_data[1][$subsriber_id][0]["shortcode"],
                            $free_charging);*/

                    }
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


                    $param_arr_temp[$index1] = $value[1][0];                    //content id
                    $param_arr_temp[$index2] = $value[0]["charging_id"];
                    $param_arr_temp[$index3] = $value[0]["category_id"];
                    $param_arr_temp[$index4] = $value[0]["phone_number"];
                    $param_arr_temp[$index5] = $index;
                    $param_arr_temp[$index6] = $value[1][1];                    //content
                    $param_arr_temp[$index7] = $value[0]["shortcode"];

                    $indexes_arr[] = "({$index1}, {$index2}, {$index3}, {$index4}, {$index5}, {$index6}, {$index7}, :nt, :stm)";
                }


                $add_params = array(":nt"=>"Опрос имитация", ":stm"=>date("Y-m-d H:i:s"));
                $param_arr = array_merge($param_arr_temp, $add_params);

                $values = implode(",", $indexes_arr);


                $sql = "INSERT INTO tbl_log_mt(content_id, charging_id, cat_id, phone_number, subscriber_id, sent_text, shortcode, note, sent_time) VALUES {$values}";
                $parameters = $param_arr;


                //Yii::app()->db->createCommand($sql)->execute($parameters);


            }

        }

        ////////////////////////////////LOG/////////////////////////////////////////////

        $fileTerminated = fopen('script_logs/script_center.log', 'a');
        $date_file_end = date("Y-m-d H:i:s").";" . Yii::app()->createAbsoluteUrl(Yii::app()->request->url) . ";";
        fwrite($fileTerminated, $date_file_end . $id . ";\tstopped;\n");
        fclose($fileTerminated);

        ////////////////////////////////LOG/////////////////////////////////////////////

    }

    public function actionNotificationsLIC(){

        ////////////////////////////////////////LOG///////////////////////////////////////////////////

        $file = fopen('script_logs/script_center.log', 'a');
        $date_file = date("Y-m-d H:i:s").";" . Yii::app()->createAbsoluteUrl(Yii::app()->request->url) . ";";
        $id = mb_substr(md5($date_file), 0, 7,  "utf-8");
        $status = "\tstarted;\n";
        fwrite($file, $date_file . $id  .';'. $status);
        fclose($file);

        ////////////////////////////////////////LOG///////////////////////////////////////////////////


        $subscriber_data = array();

        $CAT_ID = 2;                                                    // 2 - Love IS Carrot ID
        $free_charging = "10";

        $notification_mssgs = array(Yii::app()->params["messages"][$CAT_ID]["MissedSex"],
            Yii::app()->params["messages"][$CAT_ID]["MissedAge"],
            Yii::app()->params["messages"][$CAT_ID]["MissedRelation"]);


        if($subscriber_number = Subscribers::model()->count($sql = array("condition"=>"is_subscribed=:is AND category_id=:ci AND (TIMESTAMPDIFF(DAY,date(subscribe_time),CURRENT_TIMESTAMP()))%6=:st AND date(subscribe_time)!=:stn", "params"=>array(":is" => 1, ":ci" => $CAT_ID, ":st" => 0, ":stn" => date("Y-m-d"))))){

            if($subscriber_number > 1){

                $subscribers = Subscribers::model()->findAll($sql);

                foreach($subscribers as $value){

                    if(Content::checkMNP($value->phone_number) == 02){                      //02  - Kcell mnc

                        $subscriber_data[0][]=$value->id;
                        $subscriber_data[1][$value->id][] = array("phone_number"=>$value->phone_number,
                            "shortcode"=>$value->shortcode,
                            "charging_id"=>$free_charging,
                            "category_id"=>$value->category_id);

                    }

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

                        //ОТПРАВКА Вопроса

                        $sid = $value->subscriber_id;

                        $resultSet = Results::model()->getAttrByParam(array("subscriber_id" => $sid), array("order" => "sent_date DESC"));

                        if($resultSet[0] == false){

                            $question = Questions::model()->getAttrByParam(array("status" => 1, "category_id" => $CAT_ID), array("order" => "id ASC"));

                            if($question[0] == false){

                                echo "Question NOT FOUND1";

                            }
                            else{

                                $first_question = $question[1][0];

                                $result = new Results();
                                $result->subscriber_id = $sid;
                                $result->question_id = $first_question->id;
                                $result->category_id = $first_question->category_id;
                                $result->note = "Первый опрос";
                                $result->sent_date = date("Y-m-d H:i:s");
                                $result->save(false);

                                $question_msg = $first_question->question . "\n1." . $first_question->variant1 . "\n2." . $first_question->variant2 . "\n3." .  $first_question->variant3;
                                $subscriber_data[1][$value->subscriber_id][] = array(null, $question_msg);

                            }
                        }
                        else{

                            $question_id = $resultSet[1][0]->question_id;

                            $question = Questions::model()->getAttrByParam(array("status" => 1, "category_id" => $CAT_ID), array("order" => "id ASC"));
                            $result_number = Results::model()->count(array("condition" => "subscriber_id=:sid", "params" => array(":sid" => $sid)));

                            if($question[0] == false){
                                echo "Question NOT FOUND2";
                            }
                            else if($resultSet[1][0]->character != NULL && count($question[1]) == $result_number){
                                // Предложение кандидатур для ЗНАКОМСТВ
                            }
                            else{

                                $result = new Results();
                                $result->subscriber_id = $sid;

                                $first_question = $question[1][0];

                                $result->question_id = $first_question->id;
                                $result->category_id = $first_question->category_id;
                                $question_msg = $first_question->question . "\n1." . $first_question->variant1 . "\n2." . $first_question->variant2 . "\n3." .  $first_question->variant3;

                                foreach($question[1] as $value1){

                                    if($value1->id > $question_id){

                                        $question_msg = $value1->question . "\n1." . $value1->variant1 . "\n2." . $value1->variant2 . "\n3." .  $value1->variant3;
                                        $result->question_id = $value1->id;
                                        break;

                                    }
                                }

                                $subscriber_data[1][$value->subscriber_id][] = array(null, $question_msg);

                                $resultSet2 = Results::model()->getAttrByParam(array("subscriber_id" => $sid, "question_id" => $result->question_id)/*, array("limit" => 1)*/);

                                if($resultSet2[0] == true){

                                    $resultSet2[1][0]->sent_date = date("Y-m-d H:i:s");
                                    $resultSet2[1][0]->save(false);

                                }
                                else{
                                    $result->sent_date = date("Y-m-d H:i:s");
                                    $result->note = "Опрос";
                                    $result->save(false);

                                }


                            }
                        }
                    }

                    if(!isset($subscriber_data[1][$value->subscriber_id][1])){
                        echo "Уведомление не найдено1";
                    }
                    else{
                        Scripts::sendSMS_MT($subscriber_data[1][$value->subscriber_id][1][1],
                            $subscriber_data[1][$value->subscriber_id][0]["phone_number"],
                            $subscriber_data[1][$value->subscriber_id][0]["shortcode"],
                            $free_charging);
                    }

                }

                $profiles_number = count($profiles);

                //if($profiles_number < $subscriber_number){                              // IF there are subscribers without profile

                    foreach($subscriber_data[1] as $index => $value){

                        if(!isset($value[1])){

                            $subscriber_data[1][$index][1] = array(null, $notification_mssgs[0]);

                            Scripts::sendSMS_MT($subscriber_data[1][$index][1][1],
                                $subscriber_data[1][$index][0]["phone_number"],
                                $subscriber_data[1][$index][0]["shortcode"],
                                $free_charging);

                        }
                    }

                //}

                $param_arr_temp = $indexes_arr = array();

                foreach($subscriber_data[1] as $index=>$value){

                    $index1 = ":cti" . $index;
                    $index2 = ":cgi" . $index;
                    $index3 = ":ci" . $index;
                    $index4 = ":ph" . $index;
                    $index5 = ":si" . $index;
                    $index6 = ":stt" . $index;
                    $index7 = ":sc" . $index;


                    $param_arr_temp[$index1] = $value[1][0];                    //content id
                    $param_arr_temp[$index2] = $value[0]["charging_id"];
                    $param_arr_temp[$index3] = $value[0]["category_id"];
                    $param_arr_temp[$index4] = $value[0]["phone_number"];
                    $param_arr_temp[$index5] = $index;
                    $param_arr_temp[$index6] = $value[1][1];                    //content
                    $param_arr_temp[$index7] = $value[0]["shortcode"];

                    $indexes_arr[] = "({$index1}, {$index2}, {$index3}, {$index4}, {$index5}, {$index6}, {$index7}, :nt, :stm)";
                }


                $add_params = array(":nt"=>"Опрос", ":stm"=>date("Y-m-d H:i:s"));
                $param_arr = array_merge($param_arr_temp, $add_params);

                $values = implode(",", $indexes_arr);


                $sql = "INSERT INTO tbl_log_mt(content_id, charging_id, cat_id, phone_number, subscriber_id, sent_text, shortcode, note, sent_time) VALUES {$values}";
                $parameters = $param_arr;


                Yii::app()->db->createCommand($sql)->execute($parameters);

            }
            else{

                $subscribers = Subscribers::model()->find($sql);


                if(Content::checkMNP($subscribers->phone_number) != 02){                      //02  - Kcell mnc

                    die("There is no Kcell abonents");

                }

                $subscriber_data[0][] = $subscribers->id;
                $subscriber_data[1][$subscribers->id][] = array(
                    "phone_number"=>$subscribers->phone_number,
                    "shortcode"=>$subscribers->shortcode,
                    "charging_id"=>$free_charging,
                    "category_id"=>$subscribers->category_id);


                $criteria = new CDbCriteria();
                $criteria->addInCondition("subscriber_id",$subscriber_data[0]);
                $criteria->select = "sex, age, relation, day, subscriber_id";
                $criteria->order = "subscriber_id ASC";

                if(Profiles::model()->exists($criteria)) {

                    $profiles = Profiles::model()->find($criteria);


                    if ($profiles->sex == 0) {

                        $subscriber_data[1][$profiles->subscriber_id][] = array(null, $notification_mssgs[0]);
                    } else if ($profiles->age == 0) {
                        $subscriber_data[1][$profiles->subscriber_id][] = array(null, $notification_mssgs[1]);
                    } else if ($profiles->relation == 0) {
                        $subscriber_data[1][$profiles->subscriber_id][] = array(null, $notification_mssgs[2]);
                    } else {
                        //ОТПРАВКА Вопроса

                        $sid = $profiles->subscriber_id;

                        $resultSet = Results::model()->getAttrByParam(array("subscriber_id" => $sid), array("order" => "sent_date DESC", "limit" => 1));

                        if ($resultSet[0] == false) {

                            $question = Questions::model()->getAttrByParam(array("status" => 1, "category_id" => $CAT_ID), array("order" => "id ASC", "limit" => 1));

                            if ($question[0] == false) {

                                echo "Question NOT FOUND1";

                            } else {

                                $first_question = $question[1][0];

                                $result = new Results();
                                $result->subscriber_id = $sid;
                                $result->question_id = $first_question->id;
                                $result->category_id = $first_question->category_id;
                                $result->note = "Первый опрос";
                                $result->sent_date = date("Y-m-d H:i:s");
                                $result->save(false);

                                $question_msg = $first_question->question . "\n1." . $first_question->variant1 . "\n2." . $first_question->variant2 . "\n3." . $first_question->variant3;
                                $subscriber_data[1][$profiles->subscriber_id][] = array(null, $question_msg);
                            }
                        } else {

                            $question_id = $resultSet[1][0]->question_id;

                            $question = Questions::model()->getAttrByParam(array("status" => 1, "category_id" => $CAT_ID), array("order" => "id ASC"));
                            $result_number = Results::model()->count(array("condition" => "subscriber_id=:sid", "params" => array(":sid" => $sid)));

                            if ($question[0] == false) {
                                echo "Question NOT FOUND2";
                            } else if ($resultSet[1][0]->character != NULL && count($question[1]) == $result_number) {

                                // Предложение кандидатур для ЗНАКОМСТВ
                            } else {

                                $result = new Results();
                                $result->subscriber_id = $sid;

                                $first_question = $question[1][0];

                                $result->question_id = $first_question->id;
                                $result->category_id = $first_question->category_id;
                                $question_msg = $first_question->question . "\n1." . $first_question->variant1 . "\n2." . $first_question->variant2 . "\n3." . $first_question->variant3;

                                foreach ($question[1] as $value) {

                                    if ($value->id > $question_id) {

                                        $question_msg = $value->question . "\n1." . $value->variant1 . "\n2." . $value->variant2 . "\n3." . $value->variant3;
                                        $result->question_id = $value->id;

                                    }
                                }

                                $subscriber_data[1][$profiles->subscriber_id][] = array(null, $question_msg);

                                $resultSet2 = Results::model()->getAttrByParam(array("subscriber_id" => $sid, "question_id" => $result->question_id), array("limit" => 1));

                                if ($resultSet2[0] == true) {

                                    $resultSet2[1][0]->sent_date = date("Y-m-d H:i:s");
                                    $resultSet2[1][0]->save(false);

                                } else {
                                    $result->sent_date = date("Y-m-d H:i:s");
                                    $result->note = "Опрос";
                                    $result->save(false);

                                }
                            }
                        }
                    }

                    if (!isset($subscriber_data[1][$profiles->subscriber_id][1])) {
                        echo "Уведомление не найдено2";
                    } else {
                        Scripts::sendSMS_MT($subscriber_data[1][$profiles->subscriber_id][1][1],
                            $subscriber_data[1][$profiles->subscriber_id][0]["phone_number"],
                            $subscriber_data[1][$profiles->subscriber_id][0]["shortcode"],
                            $free_charging);


                    }
                }

            else{

                    $subsriber_id = $subscribers->id;

                    if(!isset($subscriber_data[1][$subsriber_id][1])){

                        $subscriber_data[1][$subsriber_id][1] = array(null, $notification_mssgs[0]);

                        Scripts::sendSMS_MT($subscriber_data[1][$subsriber_id][1][1],
                            $subscriber_data[1][$subsriber_id][0]["phone_number"],
                            $subscriber_data[1][$subsriber_id][0]["shortcode"],
                            $free_charging);

                    }
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


                        $param_arr_temp[$index1] = $value[1][0];                    //content id
                        $param_arr_temp[$index2] = $value[0]["charging_id"];
                        $param_arr_temp[$index3] = $value[0]["category_id"];
                        $param_arr_temp[$index4] = $value[0]["phone_number"];
                        $param_arr_temp[$index5] = $index;
                        $param_arr_temp[$index6] = $value[1][1];                    //content
                        $param_arr_temp[$index7] = $value[0]["shortcode"];

                        $indexes_arr[] = "({$index1}, {$index2}, {$index3}, {$index4}, {$index5}, {$index6}, {$index7}, :nt, :stm)";
                    }


                    $add_params = array(":nt"=>"Опрос", ":stm"=>date("Y-m-d H:i:s"));
                    $param_arr = array_merge($param_arr_temp, $add_params);

                    $values = implode(",", $indexes_arr);


                    $sql = "INSERT INTO tbl_log_mt(content_id, charging_id, cat_id, phone_number, subscriber_id, sent_text, shortcode, note, sent_time) VALUES {$values}";
                    $parameters = $param_arr;


                    Yii::app()->db->createCommand($sql)->execute($parameters);


            }

        }

        ////////////////////////////////LOG/////////////////////////////////////////////

        $fileTerminated = fopen('script_logs/script_center.log', 'a');
        $date_file_end = date("Y-m-d H:i:s").";" . Yii::app()->createAbsoluteUrl(Yii::app()->request->url) . ";";
        fwrite($fileTerminated, $date_file_end . $id . ";\tstopped;\n");
        fclose($fileTerminated);

        ////////////////////////////////LOG/////////////////////////////////////////////

    }

    public function actionCheck(){

        $temp = Scripts::generateLinks(2, 2);

        //print_r($temp);

        //print_r($temp->link);

     //   print_r($temp->link);
        echo count($temp->link) . "<br>";

        echo count($temp->link[0]->short);


    }


}
