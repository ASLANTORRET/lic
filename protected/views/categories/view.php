<?php
/* @var $this CategoriesController */
/* @var $model Categories */

$this->breadcrumbs=array(
	'Панель управления "Услуги и категории"'=>array('admin'),
	$model->name,
);

?>
<legend><h2><?php echo $model->name; ?></h2></legend>

<?php
$this->widget(
    'bootstrap.widgets.TbDetailView',
    array(
        'data' => $model,
        'attributes'=>array(
            'id',
            'name',
            'charging_id',
            'service_name',
            array(
                'name'=>'parent_category_id',
                'value'=>Categories::getDataByPk($model->parent_category_id, 'name')
            ),
            'create_time',
            'update_time',
            array(
                'name'=>'is_visible',
                'value'=>Yii::app()->params['interface']['is_visible'][$model->is_visible]
            )                    ),
    )
);

?>
<br>
<div class="row buttons" style="float:right;margin-right: 5px;">

    <?=CHtml::linkButton('Удалить', array(
        'submit'=>array(
            'categories/delete',
            'id'=>$model->id
        ),
        'confirm'=>"Вы уверены, что хотите удалить данный элемент?",
        'class' => 'btn btn-default btn-sm',
    ))?>

    <?=CHtml::linkButton('Редактировать', array(
        'submit'=>array(
            'categories/update',
            'id'=>$model->id,
        ),
        'class' => 'btn btn-default btn-sm',

    ))?>

</div>