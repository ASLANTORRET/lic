<?php
/* @var $this CategoriesController */
/* @var $model Categories */

$this->breadcrumbs=array(
    'Логи "Исходящие сообщения"',
);
?>

<h1>Логи «Исходящие сообщения»</h1>

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
                'name' => 'cat_id',
                'value' => 'Categories::model()->getDataByPk($data->cat_id, "name")',
                'filter'=> Categories::model()->catsForDropDownList(true, true),
                'htmlOptions' => array('style' => 'width: 140px')
            ),
            'phone_number',
            'sent_text',

            array(
                'header' => 'Стоимость(тнг.)',
                'value'=>'Yii::app()->params["interface"]["charging_smscost"][$data->charging_id]',
                'filter' => CHtml::dropDownList('MessagesLog[charging_id]', $model->charging_id, Yii::app()->params['interface']['charging_smscost'], array('class'=>'form-control')),
            ),

            'note',
            'sent_time'
        ),
        'summaryText'=>'Найдено всего: <b>{count}</b>',
    ));
?>


