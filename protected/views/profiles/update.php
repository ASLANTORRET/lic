<?php
/* @var $this ProfileController */
/* @var $model Profile */

$this->breadcrumbs=array(
    'Панель управления'=>array('admin'),
    $model->name=>array('view','id'=>$model->id),
    'Редактирование',
);

?>

    <legend><h1>Редактирование профиля "<?=$model->name?>"</h1></legend>
    <br>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>