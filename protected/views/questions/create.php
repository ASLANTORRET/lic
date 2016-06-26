<?php
/* @var $this ContentController */
/* @var $model Content */

$this->breadcrumbs=array(
    'Панель управления'=>array('admin'),
    'Создать новый вопрос',
);


?>

    <legend><h1>Создать новый вопрос</h1></legend>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>