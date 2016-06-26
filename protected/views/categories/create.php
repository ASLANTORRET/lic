<?php
/* @var $this CategoriesController */
/* @var $model Categories */

$this->breadcrumbs=array(
    'Панель управления'=>array('admin'),
    'Создать новую категорию',
);


?>

    <legend><h1>Создать новую категорию</h1></legend>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>