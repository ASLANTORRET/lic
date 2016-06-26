<?php
/* @var $this CategoriesController */
/* @var $model Categories */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php $form = $this->beginWidget(
        'booster.widgets.TbActiveForm',
        array(
            'id' => 'roomTypes-form',
            'htmlOptions' => array(
                'class'=>'well',
                'enctype' => 'multipart/form-data',
                'multiple'=>'multiple'
            ),
            'enableAjaxValidation'=>false
        )
    );
    ?>

    <p class="note">Поля, обязательные к заполнению. <span class="required">*</span></p>

    <?php echo $form->errorSummary($model); ?>


    <div class="row">
        <?php
        echo $form->dropDownListGroup(
            $model,
            'parent_category_id',
            array(
                'wrapperHtmlOptions' => array(
                    'class' => 'col-md-7',
                ),
                'widgetOptions' => array(
                    'data' => Categories::model()->catsForDropDownList(),
                )
            )
        );

        ?>
    </div>

    <div class="row">
        <?php

        echo $form->textFieldGroup(
            $model,
            'name'
        );
        ?>
    </div>

    <div class="row">
        <?php

        echo $form->textFieldGroup(
            $model,
            'service_name'
        );
        ?>
    </div>


    <div class="row">
        <div class="span-8 ">
            <?php echo $form->switchGroup($model, 'is_visible',
                array(
                    'widgetOptions' => array(
                        'options'=>array(
                            'onText'=>"ДА",
                            'offText'=>"НЕТ",
                        ),
                    )
                )
            ); ?>

        </div>
    </div>

    <div class="row">
        <?php echo $form->textFieldGroup($model,'charging_id',array('widgetOptions'=> array('htmlOptions'=>array('rows'=>6)))); ?>
    </div>


    <div class="row buttons">

        <?
        echo CHtml::linkButton('Отменить', array(
            'submit'=>array(
                'categories/admin',
                'id'=>$model->id
            ),
            'class' => 'btn btn-default',
        ));

        ?>

        <?php $this->widget('booster.widgets.TbButton', array(
            'buttonType'=>'submit',
            'context'=>'primary',
            'label'=>$model->isNewRecord ? 'Добавить' : 'Сохранить',
        )); ?>

    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->