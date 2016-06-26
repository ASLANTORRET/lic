<?php
/* @var $this ContentController */
/* @var $model Content */

$this->breadcrumbs=array(
    'Панель управления "Вопрос"'=>array('admin'),
    $model->id=>array('view','id'=>$model->id),
    'Редактирование',
);

?>

    <legend><h1>Редактирование вопроса №"<?=$model->id?>"</h1></legend>
    <br>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>