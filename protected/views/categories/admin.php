<?php
/* @var $this CategoriesController */
/* @var $model Categories */

$this->breadcrumbs=array(
    'Панель управления "Услуги и категории"',
);
?>

<h1>Панель управления «Услуги и категории»</h1>

<?php

$this->widget(
    'booster.widgets.TbGridView',
    array(
        'type' => 'striped',
        'dataProvider' => $model->search(),
        'filter' =>$model,
        'columns' => array(
            'id',
            'name',
            'charging_id',
            'service_name',
            array(
                'name'=>'parent_category_id',
                'value'=>'Categories::getDataByPk($data->parent_category_id, "name")',
            ),
            'create_time',
            'update_time',
            'is_visible',
            array(
                'htmlOptions' => array('nowrap'=>'nowrap'),
                'class'=>'booster.widgets.TbButtonColumn',
            )
        ),
        'summaryText'=>'Найдено всего: <b>{count}</b>',
    ));
?>


