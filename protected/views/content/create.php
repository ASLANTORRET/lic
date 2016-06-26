<?php
/* @var $this ContentController */
/* @var $model Content */

$this->breadcrumbs=array(
    'Панель управления'=>array('admin'),
    'Создать новый контент',
);


?>

    <legend><h1>Создать новый контент</h1></legend>

<?php
$this->widget('bootstrap.widgets.TbTabs', array(
    'tabs'=>array(
        array(
            'id'=>'tab1',
            'active'=>true,
            'label'=>'Любовь - Морковь',
            'content'=>$this->renderPartial("_formlic", array('model' => $model),true),
        ),
        array(
            'id'=>'tab2',
            'active'=>false,
            'label'=>'Дневник здоровья',
            'content'=>$this->renderPartial("_formdiary", array('model' => $model),true),
        ),

    ),
));

/*$this->renderPartial('_form', array('model'=>$model));*/ ?>