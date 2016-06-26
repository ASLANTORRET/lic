<?php
/* @var $this CategoriesController */
/* @var $model Categories */

$this->breadcrumbs=array(
    'Панель управления "Услуги и категории"'=>array('admin'),
    $model->name=>array('view','id'=>$model->id),
    'Редактирование',
);

?>

    <legend><h1>Редактирование категории "<?=$model->name?>"</h1></legend>
    <br>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>