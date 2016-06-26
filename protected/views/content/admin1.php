<?php
/* @var $this ContentController */
/* @var $model Content */

$this->breadcrumbs=array(
    'Панель управления "Контент"',
);
?>

<h1>Панель управления «Контент»</h1>

<?php

$this->widget(
    'booster.widgets.TbGridView',
    array(
        'type' => 'striped',
        'dataProvider' => $model->search(),
        'filter' =>$model,
        'columns' => array(
            'id',

            array(
              'name' => 'content',
              'value' => 'Content::getShortVersion($data->id)'
            ),

            array(
                'name'=>'category_id',
                'value'=>'Categories::getDataByPk($data->category_id, "name")'
            ),

            array(
                'class' => 'booster.widgets.TbToggleColumn',
                'toggleAction' => 'content/toggle',
                'name' => 'status',
                'header' => 'Статус',
                'htmlOptions' => array('style' => 'width: 48px')
            ),

            'day',

            array(
                'name'=>'sex',
                'value'=>'Yii::app()->params["interface"]["sex"][$data->sex]',
                'filter'=> CHtml::dropDownList('Content[sex]', $model->sex, Yii::app()->params['interface']['sex'], array('class'=>'form-control')),

            ),

            array(
                'name'=>'age',
                'value'=>'Yii::app()->params["interface"]["age"][$data->category_id][$data->age]',
                'filter' => ''
            ),

            array(
                'name'=>'relation',
                'value'=>'Yii::app()->params["interface"]["relation"][$data->relation]',
                'filter'=> CHtml::dropDownList('Content[relation]', $model->relation, Yii::app()->params['interface']['relation'], array('class'=>'form-control')),
            ),
            array(
                'name'=>'physics',
                'value'=>'Yii::app()->params["interface"]["physics"][$data->physics]',
                'filter'=> CHtml::dropDownList('Content[physics]', $model->physics, Yii::app()->params['interface']['physics'], array('class'=>'form-control')),
            ),
            'create_time',
            'update_time',
            array(
                'htmlOptions' => array('nowrap'=>'nowrap'),
                'class'=>'booster.widgets.TbButtonColumn',
                'buttons' => array(
                    'delete' => array(
                        'url' => '',
                        'visible'=>'false'
                    ),
                ),
            )
        ),
        'summaryText'=>'Найдено всего: <b>{count}</b>',
    ));
?>


