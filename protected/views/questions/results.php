<?php
/* @var $this QuestionsController */
/* @var $model Results */

$this->breadcrumbs=array(
    'Панель управления "Опросы"',
);
?>

<h1>Панель управления «Опросы»</h1>

<?php

$this->widget(
    'booster.widgets.TbGridView',
    array(
        'type' => 'striped',
        'dataProvider' => $model->search(),
        'filter' =>$model,
        'columns' => array(
            'id',
            'subscriber_id',

            array(
                'name'=>'category_id',
                'value'=>'Yii::app()->params["interface"]["categories"][$data->category_id]',
                'filter'=> CHtml::dropDownList('Results[category_id]', $model->category_id, Yii::app()->params['interface']['categories'], array('class'=>'form-control')),

            ),

            'question_id',

            array(
                'name' => 'question_id',
                'filter' => '',
                'value' => 'Questions::getShortVersionQ($data->question_id)'
            ),

            'character',

            array(
                'name' => 'character',
                'filter' => '',
                'value' => 'Questions::getCharacter($data->question_id, $data->character)'
            ),

            'note',
            array(
                'name'=>'sent_date',
                'header'=>'Время создания',
                'htmlOptions' => array('style' => 'width: 120px'),
                'filter' => $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'model'=>$model,
                        'attribute'=>'sent_date',
                        'language'=>'ru',
                        'htmlOptions' => array(
                            'id' => 'datepicker_for_sent_date',
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
                'name'=>'answer_date',
                'header'=>'Время посл. обновления',
                'htmlOptions' => array('style' => 'width: 120px'),
                'filter' => $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'model'=>$model,
                        'attribute'=>'answer_date',
                        'language'=>'ru',
                        'htmlOptions' => array(
                            'id' => 'datepicker_for_answer_date',
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
                    'view' => array(
                        'url' => '',
                        'visible'=>'false'
                    ),
                    'update' => array(
                        'url' => '',
                        'visible'=>'false'
                    ),
                )
            )
        ),

        'afterAjaxUpdate'=>"function() {
            jQuery('#datepicker_for_sent_date').datepicker(jQuery.extend(jQuery.datepicker.regional['ru'],{'dateFormat':'yy-mm-dd','changeMonth':'true','showButtonPanel':'true','changeYear':'true'}));
            jQuery('#datepicker_for_answer_date').datepicker(jQuery.extend(jQuery.datepicker.regional['ru'],{'dateFormat':'yy-mm-dd','changeMonth':'true','showButtonPanel':'true','changeYear':'true'}));
        }",

        'summaryText'=>'Найдено всего: <b>{count}</b>',
    ));
?>


