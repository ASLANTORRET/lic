<?php
/* @var $this ProfileController */
/* @var $model Profile */

$this->breadcrumbs=array(
    'Панель управления',
);
?>

<h1>Панель управления</h1>

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
                'name' => 'subscriber_id',
                'value' => 'Subscribers::getPhoneByPk($data->subscriber_id)'
            ),
            array(
                'name'=>'sex',
                'value'=>'Yii::app()->params["interface"]["sex"][$data->sex]',
                'filter'=> CHtml::dropDownList('Profiles[sex]', $model->sex, Yii::app()->params['interface']['sex'], array('class'=>'form-control')),

            ),
            array(
                'name'=>'age',
                'value'=>'Yii::app()->params["interface"]["age"][$data->category_id][$data->age]',
                //'filter'=> CHtml::dropDownList('Profiles[age]', $model->age, Yii::app()->params['interface']['age']['Profiles::getDataByPk($data->id, "category_id")'], array('class'=>'form-control')),
            ),
            array(
                'name'=>'relation',
                'value'=>'Yii::app()->params["interface"]["relation"][$data->relation]',
                'filter'=> CHtml::dropDownList('Profiles[relation]', $model->relation, Yii::app()->params['interface']['relation'], array('class'=>'form-control')),
            ),
            array(
                'name'=>'physics',
                'value'=>'Yii::app()->params["interface"]["physics"][$data->physics]',
                'filter'=> CHtml::dropDownList('Profiles[physics]', $model->physics, Yii::app()->params['interface']['physics'], array('class'=>'form-control')),
            ),

            'day',

            array(
                'name'=>'create_time',
                'header'=>'Время создания',
                'htmlOptions' => array('style' => 'width: 120px'),
                'filter' => $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'model'=>$model,
                        'attribute'=>'create_time',
                        'language'=>'ru',
                        'htmlOptions' => array(
                            'id' => 'datepicker_for_create_time',
                            'size' => '10',
                            'class'=>'form-control'
                        ),
                        'defaultOptions' => array(
                            'showOn' => 'focus',
                            'dateFormat' => 'yy-mm-dd',
                            'showOtherMonths' => true,
                            'selectOtherMonths' => true,
                            'changeMonth' => true,
                            'changeYear' => true,
                            'showButtonPanel' => true
                        ),
                        'options' => array(
                            'dateFormat' => 'yy-mm-dd'
                        )
                    ),
                    'true'),
            ),

            array(
                'name'=>'update_time',
                'header'=>'Время посл. обновления',
                'htmlOptions' => array('style' => 'width: 120px'),
                'filter' => $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'model'=>$model,
                        'attribute'=>'update_time',
                        'language'=>'ru',
                        'htmlOptions' => array(
                            'id' => 'datepicker_for_update_time',
                            'size' => '10',
                            'class'=>'form-control'
                        ),
                        'defaultOptions' => array(
                            'showOn' => 'focus',
                            'dateFormat' => 'yy-mm-dd',
                            'showOtherMonths' => true,
                            'selectOtherMonths' => true,
                            'changeMonth' => true,
                            'changeYear' => true,
                            'showButtonPanel' => true
                        ),
                        'options' => array(
                            'dateFormat' => 'yy-mm-dd'
                        )
                    ),
                    'true'),
            ),
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

        'afterAjaxUpdate'=>"function() {
        jQuery('#datepicker_for_create_time').datepicker(jQuery.extend(jQuery.datepicker.regional['ru'],{'dateFormat':'yy-mm-dd','changeMonth':'true','showButtonPanel':'true','changeYear':'true'}));
        jQuery('#datepicker_for_update_time').datepicker(jQuery.extend(jQuery.datepicker.regional['ru'],{'dateFormat':'yy-mm-dd','changeMonth':'true','showButtonPanel':'true','changeYear':'true'}));
        }",

        'summaryText'=>'Найдено всего: <b>{count}</b>',
    ));
?>


