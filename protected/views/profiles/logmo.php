<?php
/* @var $this CategoriesController */
/* @var $model Categories */

$this->breadcrumbs=array(
    'Логи "Входящие сообщения"',
);
?>

<h1>Логи «Входящие сообщения»</h1>

<?php

$this->widget(
    'booster.widgets.TbGridView',
    array(
        'type' => 'striped',
        'dataProvider' => $model->search(),
        'filter' =>$model,
        'columns' => array(
            'id',
            'phone_number',
            'subscriber_id',
            'message',
            'shortcode',
            'note',
            'sent_time'
        ),
        'summaryText'=>'Найдено всего: <b>{count}</b>',
    ));
?>


