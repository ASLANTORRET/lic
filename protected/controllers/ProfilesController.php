<?php

class ProfilesController extends Controller
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

            array('deny', // allow admin user to perform 'admin' and 'delete' actions
                'actions'=>array('update'),
                'users'=>array('partnermg5'),
            ),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
//				'actions'=>array('create','update'),
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
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Profiles('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Profiles']))
			$model->attributes=$_GET['Profiles'];

		$this->render('admin2',array(
			'model'=>$model,
		));
	}

    public function actionMssgsLogs(){
        $model = new MessagesLog('search');
        $model->unsetAttributes();

        if(isset($_GET['MessagesLog'])){
            $model->attributes = $_GET['MessagesLog'];
        }
        $this->render("mssgslog", array("model"=>$model));
    }


    public function actionlogMO(){
        $model = new LogMO('search');
        $model->unsetAttributes();

        if(isset($_GET['LogMO'])){
            $model->attributes = $_GET['LogMO'];
        }
        $this->render("logmo", array("model"=>$model));
    }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Profiles the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Profiles::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Profiles $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='profiles-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
