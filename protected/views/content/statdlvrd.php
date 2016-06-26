<?php
/* @var $this ContentController */
/* @var $model PromoLogs */

$this->breadcrumbs=array(
    'Статистика',
);

?>

    <legend><h1>Статистика по доставленным сообщениям</h1></legend>

    <div class="span-1">
        &nbsp;
    </div>

    <div class="form span-10">
        <?php $form = $this->beginWidget(
            'booster.widgets.TbActiveForm',
            array(
                'id' => 'statsdlvrd-inline',
                'type' => 'inline',
                'method' => 'get',
                'htmlOptions' => array('class' => 'well'), // for inset effect
                'enableAjaxValidation'=>false,

            )
        );
        ?>

        <div class="row">
            <?php echo $form->datePickerGroup(
                $model,
                'start_date',
                array(
                    'widgetOptions' => array(
                        'options' => array(
                            'language' => 'ru',
                            'autoclose' => true,
                            'calendarWeeks' => false,
                            'forceParse' => true,
                            'todayHighlight' => true,
                            'format' => 'yyyy-mm-dd',
                        ),

                    ),

                    'append' => '<i class="glyphicon glyphicon-calendar"></i>'
                )
            ); ?>
        </div>

        <div class="row">
            <?php echo $form->datePickerGroup(
                $model,
                'end_date',
                array(
                    'widgetOptions' => array(
                        'options' => array(
                            'language' => 'ru',
                            'autoclose' => true,
                            'calendarWeeks' => false,
                            'forceParse' => true,
                            'todayHighlight' => true,
                            'format' => 'yyyy-mm-dd',
                        ),
                    ),
                    'append' => '<i class="glyphicon glyphicon-calendar"></i>'
                )
            ); ?>
        </div>


        <div class="row buttons" style="margin-top: 18px;margin-left: 266px;">

            <?php $this->widget('booster.widgets.TbButton', array(
                'buttonType'=>'submit',
                'label'=>'Показать',
            )); ?>

        </div>

        <?php $this->endWidget(); ?>
    </div>


    <span class="label label-info">Сумма выделенных ячеек</span>: <span id="hours">0</span>
<?php

$this->widget(
    'booster.widgets.TbExtendedGridView',
    array(
        'selectableCells'=>true,
        'selectableCellsFilter'=>'td.tobeselected',
        'afterSelectableCells' => 'js:function(selected){
        var sum = 0;
        $.each(selected, function(){
            sum += parseInt($(this).text());
        });
            $("#hours").html(sum);
        }',
        'type' => 'bordered',
        'dataProvider' => $dp,
        'columns' => array(
            array(
                'name' => 'id',
                'htmlOptions' => array('style' => 'width: 20px')
            ),
            array(
                'name'=>'shortcode_smscost',
                'footer'=>'<h5><b>Всего</b></h5>',
            ),

            array(
                'name' => 'in_quantity',
                'class'=>'booster.widgets.TbTotalSumColumn',
                'htmlOptions'=>array('class'=>'tobeselected'),
            ),
            array(
                'name' => 'in_money',
                'class'=>'booster.widgets.TbTotalSumColumn',
                'htmlOptions'=>array('class'=>'tobeselected'),
            ),
            array(
                'name'=>'date',
                'htmlOptions' => array('style' => 'width: 130px')
            )
        ),

        'template'=>'<div>{summary}</div>{items}<br>{extendedSummary}<br>{pager}',
        'summaryText'=>'Найдено всего: <b>{count}</b>'

    ));




