
<?php

$model->category_id = 3;

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
                'value'=>'Yii::app()->params["interface"]["agediary"][$data->age]',
                'filter' => CHtml::dropDownList('Content[age]', $model->age, Yii::app()->params['interface']['agediary'], array('class'=>'form-control')),
            ),

            array(
                'name'=>'physics',
                'value'=>'Yii::app()->params["interface"]["physics"][$data->physics]',
                'filter'=> CHtml::dropDownList('Content[physics]', $model->physics, Yii::app()->params['interface']['physics'], array('class'=>'form-control')),
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
                            'id' => 'datepicker_for_create_time1',
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
                            'id' => 'datepicker_for_update_time1',
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
            jQuery('#datepicker_for_create_time1').datepicker(jQuery.extend(jQuery.datepicker.regional['ru'],{'dateFormat':'yy-mm-dd','changeMonth':'true','showButtonPanel':'true','changeYear':'true'}));
            jQuery('#datepicker_for_update_time1').datepicker(jQuery.extend(jQuery.datepicker.regional['ru'],{'dateFormat':'yy-mm-dd','changeMonth':'true','showButtonPanel':'true','changeYear':'true'}));
        }",

        'summaryText'=>'Найдено всего: <b>{count}</b>',
    ));
?>


