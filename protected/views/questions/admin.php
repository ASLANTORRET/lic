<?php
/* @var $this QuestionsController */
/* @var $model Questions */

$this->breadcrumbs=array(
    'Панель управления "Вопрос"',
);
?>

<h1>Панель управления «Вопрос»</h1>

<?php

$this->widget(
    'booster.widgets.TbGridView',
    array(
        'type' => 'striped',
        'dataProvider' => $model->search(),
        'filter' =>$model,
        'columns' => array(
            array(
                'name' => 'id',
                'htmlOptions' => array('style' => 'width: 10px')
            ),
            array(
                'name' => 'question',
                'value' => 'Questions::getShortVersionQ($data->id)',
            ),
            array(
                'class' => 'booster.widgets.TbToggleColumn',
                'toggleAction' => 'questions/toggle',
                'name' => 'status',
                'header' => 'Статус',
                'htmlOptions' => array('style' => 'width: 48px')
            ),

            array(
                'name' => 'variant1',
                'value' => 'Questions::getShortVersion($data->variant1)',

            ),

            array(
                'name' => 'variant2',
                'value' => 'Questions::getShortVersion($data->variant2)',

            ),

            array(
                'name' => 'variant3',
                'value' => 'Questions::getShortVersion($data->variant3)',

            ),

            array(
                'name' => 'response1',
                'value' => 'Questions::getShortVersion($data->response1)',

            ),

            array(
                'name' => 'response2',
                'value' => 'Questions::getShortVersion($data->response2)',

            ),

            array(
                'name' => 'response3',
                'value' => 'Questions::getShortVersion($data->response3)',

            ),

            array(
                'name' => 'character1',
                'value' => 'Questions::getShortVersion($data->character1)',

            ),

            array(
                'name' => 'character2',
                'value' => 'Questions::getShortVersion($data->character2)',


            ),

            array(
                'name' => 'character3',
                'value' => 'Questions::getShortVersion($data->character3)',

            ),

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


