<?php

class SubscribersController extends Controller
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

    public function actions(){

        return
            array(
            'toggle' => array(
                'class'=>'booster.actions.TbToggleAction',
                'modelName' => 'Subscribers',
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
            array('deny', // allow admin user to perform 'admin' and 'delete' actions
                'actions'=>array('toggle'),
                'users'=>array('partnermg5'),
            ),

            array('allow',  // deny all users
                'users'=>array('@'),
            ),

			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Subscribers('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Subscribers']))
			$model->attributes=$_GET['Subscribers'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Subscribers the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Subscribers::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Subscribers $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='subscribers-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
