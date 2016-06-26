<?php
/* @var $this ContentController */
/* @var $model Content */

$this->breadcrumbs=array(
    'Панель управления "Профиль"'=>array('admin'),
    $model->id,
);

?>
<legend><h2><?php echo "Профиль №" . $model->id; ?></h2></legend>

<?php

if($model->category_id == 2){
    $this->widget(
        'bootstrap.widgets.TbDetailView',
        array(
            'data' => $model,
            'attributes'=>array(
                'id',
                'subscriber_id',

                array(
                  'header' => '',
                   'value' => Categories::model()->getDataByPk($model->category_id, "name")
                ),

                array(
                    'header' => 'phone_number',
                    'value' => Subscribers::getPhoneByPk($model->subscriber_id)
                ),

                array(
                    'name' => 'sex',
                    'value' => Yii::app()->params["interface"]["sex"][$model->sex]
                ),

                array(
                    'name' => 'age',
                    'value' => Yii::app()->params["interface"]["agelic"][$model->age]
                ),

                array(
                    'name' => 'relation',
                    'value' => Yii::app()->params["interface"]["relation"][$model->relation]
                ),

                'day',
                'create_time',
                'update_time'
            )
        )
    );
}
else{
    $this->widget(
        'bootstrap.widgets.TbDetailView',
        array(
            'data' => $model,
            'attributes'=>array(
                'id',
                'subscriber_id',

                array(
                    'header' => '',
                    'value' => Categories::model()->getDataByPk($model->category_id, "name")
                ),

                array(
                    'header' => 'phone_number',
                    'value' => Subscribers::getPhoneByPk($model->subscriber_id)
                ),

                array(
                    'name' => 'sex',
                    'value' => Yii::app()->params["interface"]["sex"][$model->sex]
                ),

                array(
                    'name' => 'age',
                    'value' => Yii::app()->params["interface"]["agediary"][$model->age]
                ),

                array(
                    'name' => 'physics',
                    'value' => Yii::app()->params["interface"]["physics"][$model->physics]
                ),
                'day',
                'create_time',
                'update_time'
            )
        )
    );
}


?>
<br>
<div class="row buttons" style="float:right;margin-right: 5px;">

    <?=CHtml::linkButton('Редактировать', array(
        'submit'=>array(
            'profile/update',
            'id'=>$model->id,
        ),
        'class' => 'btn btn-default btn-sm',

    ))?>

</div>