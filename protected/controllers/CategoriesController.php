<?php

class CategoriesController extends Controller
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

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
            array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions'=>array('abonentpage'),
                'users'=>array('?'),
            ),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				//'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			/*array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'users'=>array('admin'),
			),*/
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
		$model=new Categories;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Categories']))
		{

            foreach($_POST['Categories'] as $index=>$value){
                $model->$index=$value;
            }

			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
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

		if(isset($_POST['Categories']))
		{
            foreach($_POST['Categories'] as $index=>$value){
                $model->$index=$value;
            }
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
		$dataProvider=new CActiveDataProvider('Categories');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

    public function actionAbonentPage(){

        //$categories_for_combs = Categories::model()->findAll(array('select'=>'id, subscribe_combination, unsubscribe_combination', 'condition'=>'subscribe_combination IS NOT NULL'));

        $sender_phone_number = "777";


        $phone_number =  "77012273248";      //launch SCRIPT for getting TEST number
        $isStartPage = false;
        $catID = false;

        if(isset($_SERVER['HTTP_MSISDN'])){
            $phone_number =  $_SERVER['HTTP_MSISDN'];                //uncomment to get REAL phone number
        }

        /*foreach($categories_for_combs as $cats_value){
            if($cats_value->subscribe_combination != null){
                $subs_combs = explode('|', $cats_value->subscribe_combination);
                $search_arr[$subs_combs[0] . ""] = $cats_value->id;
            }
            if($cats_value->unsubscribe_combination != null){

                $unsubs_combs = explode('|', $cats_value->unsubscribe_combination);

                foreach($unsubs_combs as $unsubs_comb){
                    $search_arr[$unsubs_comb . ""] = $cats_value->id;
                }
            }
        }*/

        if(isset($_GET['cat_id'])){
            $catID = $_GET['cat_id'];
            if(isset($_GET['isstartpage'])){
                $isStartPage = false;
                $current_category_id = $catID;

                $search_arr = array("222" => "2", "333" => "3", "202" => "7", "303" => "40");

                if($catID < 5){
                    $current_category_id = (int)$catID + 1;
                }

                if(array_key_exists($catID, $search_arr)){

                    $current_category_id = $search_arr[$catID];

                    $file = fopen('script_logs/provider_prom.log', 'a');
                    fwrite($file, date("Y-m-d H:i:s") ." ". $phone_number . ' *603# '. "\tProm_cat_ID:" .$catID . "  Orig_cat_ID:" . $current_category_id . "\n");
                    fclose($file);

                }

                $catID = $current_category_id . "_0";
            }
            else{
                $short_comb_value = $_GET['cat_id'];
                $short_comb_value_arr = explode('_', $short_comb_value);
                $current_category_id = $short_comb_value_arr[0];
            }
        }
        else{
            $current_category_id = Categories::model()->find(array('order'=>'id ASC'))->id;
        }

        if(Categories::model()->findByPk($current_category_id)->script_url != null){

            $model = Categories::model()->findByPk($current_category_id);

            $function = $model->script_url;
            $function[0] = strtoupper($function[0]);
            $answer_arr = Scripts::$function($phone_number,$model);
            if($model->is_final){

                $this->renderPartial('subscription', array('query_result'=>$answer_arr[1][0], 'parentID'=>$answer_arr[0]));
            }
            else{
                if($answer_arr[2] == false){                //Show category list ?

                    $this->renderPartial('subscription', array('query_result'=>$answer_arr[1][0], 'parentID'=>$answer_arr[0]));
                }
                else{

                    if(count($answer_arr[1]) == 2){
                        $catID = $answer_arr[1][1];
                    }

                    $hiddencats = null;

                    if($answer_arr[4][0] == true){
                        $hiddencats = $answer_arr[4][1];
                    }

                    $categories_list = Categories::constructAbonentPage($isStartPage, $catID, $answer_arr[3], $hiddencats);
                    $param = array('model'=>$categories_list);
                    if($answer_arr[1][0] != null){
                        $param['message'] = $answer_arr[1][0];
                    }

                    $this->renderPartial('abonentpage', $param);
                }
            }
        }
        else{

            $categories_list = Categories::constructAbonentPage(false, $catID);
            $this->renderPartial('abonentpage', array('model'=>$categories_list));
        }
    }


	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Categories('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Categories']))
			$model->attributes=$_GET['Categories'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Categories the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Categories::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Categories $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='categories-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
