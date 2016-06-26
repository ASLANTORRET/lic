<?php
/* @var $this ContentController */
/* @var $model Content */

$this->breadcrumbs=array(
	'Панель управления "Контент"'=>array('admin'),
	$model->id,
);

?>
<legend><h2><?php echo "Редактирование контента №" . $model->id; ?></h2></legend>

<?php
$this->widget(
    'bootstrap.widgets.TbDetailView',
    array(
        'data' => $model,
        'attributes'=>array(
            'id',
            array(
                'name'=>'category_id',
                'value'=>Categories::getDataByPk($model->category_id, "name"),
            ),
            'content',
            'day',
            'create_time',
            'update_time'            
        ),
    )
);

?>
<br>
<div class="row buttons" style="float:right;margin-right: 5px;">

    <?=CHtml::linkButton('Удалить', array(
        'submit'=>array(
            'content/delete',
            'id'=>$model->id
        ),
        'confirm'=>"Вы уверены, что хотите удалить данный элемент?",
        'class' => 'btn btn-default btn-sm',
    ))?>

    <?=CHtml::linkButton('Редактировать', array(
        'submit'=>array(
            'content/update',
            'id'=>$model->id,
        ),
        'class' => 'btn btn-default btn-sm',

    ))?>

</div>