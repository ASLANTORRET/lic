<?php
/* @var $this ContentController */
/* @var $model Content */

$this->breadcrumbs=array(
    'Панель управления "Контент"'=>array('admin'),
    $model->id=>array('view','id'=>$model->id),
    'Редактирование',
);

?>

    <legend><h1>Редактирование контента №"<?=$model->id?>"</h1></legend>
    <br>

<?php

$views = array(
    '2' => '_formlic',
    '3' => '_formdiary'
);

$this->renderPartial($views[$service_type], array('model'=>$model)); ?>