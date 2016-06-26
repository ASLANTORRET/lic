<?php
/* @var $this ContentController */
/* @var $model Content */

$this->breadcrumbs=array(
    'Панель управления "Контент"',
);
?>

    <h1>Панель управления «Контент»</h1>

<?php
$this->widget('bootstrap.widgets.TbTabs', array(
    'tabs'=>array(
        array(
            'id'=>'tab1',
            'active'=>true,
            'label'=>'Любовь - Морковь',
            'content'=>$this->renderPartial("_panellic", array('model' => $model),true),
        ),
        array(
            'id'=>'tab2',
            'active'=>false,
            'label'=>'Дневник здоровья',
            'content'=>$this->renderPartial("_paneldiary", array('model' => $model),true),
        ),

    ),
));

/*$this->renderPartial('_form', array('model'=>$model));*/ ?>