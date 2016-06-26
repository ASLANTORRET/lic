<?php
/* @var $this SubscribersController */
/* @var $model Subscribers */

$this->breadcrumbs=array(
    'Подписчики',
);


Yii::app()->clientScript->registerScript('re-install-date-picker_st', "
    $('#datepicker_for_subscribe_time').click(function(){
        $('#datepicker_for_subscribe_time_from').val('');
        $('#datepicker_for_subscribe_time_to').val('');
        }
    );

    $('#datepicker_for_subscribe_time_from').click(function(){
        $('#datepicker_for_subscribe_time').val('');
        }
    );

    $('#datepicker_for_subscribe_time_to').click(function(){
        $('#datepicker_for_subscribe_time').val('');
        }
    );


    $('#datepicker_for_unsubscribe_time').click(function(){
        $('#datepicker_for_unsubscribe_time_from').val('');
        $('#datepicker_for_unsubscribe_time_to').val('');
        }
    );

    $('#datepicker_for_unsubscribe_time_from').click(function(){
        $('#datepicker_for_unsubscribe_time').val('');
        }
    );

    $('#datepicker_for_unsubscribe_time_to').click(function(){
        $('#datepicker_for_unsubscribe_time').val('');
        }
    );

");

?>

<h1>Список подписчиков</h1>


<?php

$this->widget(
    'booster.widgets.TbGridView',
    array(
        'type' => 'bordered condensed',
        'dataProvider' => $model->search(),
        'ajaxType' => 'get',
        'filter' =>$model,
        'columns' => array(
            array(
                'name'=>'id',
                'htmlOptions' => array('style' => 'width: 70px')
            ),
            array(
                'name'=>'phone_number',
                'htmlOptions' => array('style' => 'width: 110px')
            ),

            array(
                'header' => 'Цена(тнг.)',
                'value'=>'Yii::app()->params["interface"]["charging_smscost"][$data->charging_id]',
                'filter' => CHtml::dropDownList('Subscribers[charging_id]', $model->charging_id, Yii::app()->params['interface']['charging_smscost'], array('class'=>'form-control')),
                'htmlOptions' => array('style' => 'width: 48px')
            ),
            array(
                'name' => 'category_id',
                'value' => 'Categories::model()->getDataByPk($data->category_id, "name")',
                'filter'=> Categories::model()->catsForDropDownList(true, true),
                'htmlOptions' => array('style' => 'width: 140px')
            ),

            array(
                'class' => 'booster.widgets.TbToggleColumn',
                'toggleAction' => 'subscribers/toggle',
                'name' => 'is_subscribed',
                'header' => 'Статус',
                'htmlOptions' => array('style' => 'width: 48px')
            ),
            array(
                'name'=>'shortcode',
                'htmlOptions' => array('style' => 'width: 48px')
            ),

            array(
                'name' => 'has_profile',
                'filter' => '',
                'value' => 'Subscribers::hasProfile($data->id)'
            ),

            array(
                'name' => 'has_result',
                'filter' => '',
                'value' => 'Subscribers::hasResult($data->id)'
            ),

            array(
                'name'=>'subscribe_time',
                'header'=>'Время подписки',
                'htmlOptions' => array('style' => 'width: 120px'),
                'filter' => $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                            'model'=>$model,
                            'attribute'=>'subscribe_time',
                            'language'=>'ru',
                            'htmlOptions' => array(
                                'id' => 'datepicker_for_subscribe_time',
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
                'name'=>'subscribe_time_from',
                'header'=>'Подписка от...',
                'htmlOptions' => array('style' => 'width: 120px'),
                'filter' => $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                            'model'=>$model,
                            'attribute'=>'subscribe_time_from',
                            'language'=>'ru',
                            'htmlOptions' => array(
                                'id' => 'datepicker_for_subscribe_time_from',
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
                'name'=>'subscribe_time_to',
                'header'=>'Подписка до ...',
                'htmlOptions' => array('style' => 'width: 120px'),
                'filter' => $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                            'model'=>$model,
                            'attribute'=>'subscribe_time_to',
                            'language'=>'ru',
                            'htmlOptions' => array(
                                'id' => 'datepicker_for_subscribe_time_to',
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
                                'showButtonPanel' => true,
                            ),
                            'options' => array(
                                'dateFormat' => 'yy-mm-dd'
                            )
                        ),
                        'true'),
            ),
            array(
                'name'=>'unsubscribe_time',
                'header'=>'Время отписки',
                'htmlOptions' => array('style' => 'width: 120px'),
                'filter' => $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                            'model'=>$model,
                            'attribute'=>'unsubscribe_time',
                            'language'=>'ru',
                            'htmlOptions' => array(
                                'id' => 'datepicker_for_unsubscribe_time',
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
                                'showButtonPanel' => true,
                            ),
                            'options' => array(
                                'dateFormat' => 'yy-mm-dd'
                            )
                        ),
                        'true'),
            ),

            array(
                'name'=>'unsubscribe_time_from',
                'header'=>'Отписка от ...',
                'htmlOptions' => array('style' => 'width: 120px'),
                'filter' => $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                            'model'=>$model,
                            'attribute'=>'unsubscribe_time_from',
                            'language'=>'ru',
                            'htmlOptions' => array(
                                'id' => 'datepicker_for_unsubscribe_time_from',
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
                                'showButtonPanel' => true,
                            ),
                            'options' => array(
                                'dateFormat' => 'yy-mm-dd'
                            )
                        ),
                        'true'),
            ),
            array(
                'name'=>'unsubscribe_time_to',
                'header'=>'Отписка до ...',
                'htmlOptions' => array('style' => 'width: 120px'),
                'filter' => $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                            'model'=>$model,
                            'attribute'=>'unsubscribe_time_to',
                            'language'=>'ru',
                            'htmlOptions' => array(
                                'id' => 'datepicker_for_unsubscribe_time_to',
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
                                'showButtonPanel' => true,
                            ),
                            'options' => array(
                                'dateFormat' => 'yy-mm-dd'
                            )
                        ),
                        'true'),
            )
        ),
        'afterAjaxUpdate'=>"function() {
        jQuery('#datepicker_for_subscribe_time').datepicker(jQuery.extend(jQuery.datepicker.regional['ru'],{'dateFormat':'yy-mm-dd','changeMonth':'true','showButtonPanel':'true','changeYear':'true'}));
        jQuery('#datepicker_for_subscribe_time_from').datepicker(jQuery.extend(jQuery.datepicker.regional['ru'],{'dateFormat':'yy-mm-dd','changeMonth':'true','showButtonPanel':'true','changeYear':'true'}));
        jQuery('#datepicker_for_subscribe_time_to').datepicker(jQuery.extend(jQuery.datepicker.regional['ru'],{'dateFormat':'yy-mm-dd','changeMonth':'true','showButtonPanel':'true','changeYear':'true'}));

        jQuery('#datepicker_for_unsubscribe_time').datepicker(jQuery.extend(jQuery.datepicker.regional['ru'],{'dateFormat':'yy-mm-dd','changeMonth':'true','showButtonPanel':'true','changeYear':'true'}));
        jQuery('#datepicker_for_unsubscribe_time_from').datepicker(jQuery.extend(jQuery.datepicker.regional['ru'],{'dateFormat':'yy-mm-dd','changeMonth':'true','showButtonPanel':'true','changeYear':'true'}));
        jQuery('#datepicker_for_unsubscribe_time_to').datepicker(jQuery.extend(jQuery.datepicker.regional['ru'],{'dateFormat':'yy-mm-dd','changeMonth':'true','showButtonPanel':'true','changeYear':'true'}));
    }",
        'template'=>'<div>{pager}{summary}</div>{items}<br>{pager}',
        'summaryText'=>'Найдено всего: <b>{count}</b>',
        'pager'=>array(
            'maxButtonCount' => 25,
            'prevPageLabel' => ' &laquo; ',
            'nextPageLabel' => ' &raquo; ',
            'header'=>'',
        ),

    ));
?>


