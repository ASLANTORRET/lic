<?php
/* @var $this ContentController */
/* @var $model Content */

$this->breadcrumbs=array(
	'Панель управления "Вопрос"'=>array('admin'),
	$model->id,
);

?>
<legend><h2><?php echo "Вопрос №" . $model->id; ?></h2></legend>

<?php
$this->widget(
    'bootstrap.widgets.TbDetailView',
    array(
        'data' => $model,
        'attributes'=>array(

            'id',
            'question',
            'status',
            array(
                'value'=>'&nbsp;',
                'type'=>'html'
            ),
            'variant1',
            'variant2',
            'variant3',
            array(
                'value'=>'&nbsp;',
                'type'=>'html'
            ),
            'response1',
            'response2',
            'response3',
            array(
                'value'=>'&nbsp;',
                'type'=>'html'
            ),
            'character1',
            'character2',
            'character3',
            array(
                'value'=>'&nbsp;',
                'type'=>'html'
            ),
            'create_time',
            'update_time'
        ),
    )
);

?>
<br>
<div class="row buttons" style="float:right;margin-right: 5px;">

    <?=CHtml::linkButton('Редактировать', array(
        'submit'=>array(
            'questions/update',
            'id'=>$model->id,
        ),
        'class' => 'btn btn-default btn-sm',

    ))?>

</div>