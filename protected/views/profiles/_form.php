<?php
/* @var $this ProfileController */
/* @var $model Profile */
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
            'service_id',
            array(
                'wrapperHtmlOptions' => array(
                    'class' => 'col-md-7',

                ),
                'widgetOptions' => array(
                    'data' => array(null=>'Выберите язык','kaz'=> 'казахский', 'ru'=>'русский', 'en'=>'английский'),
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
        <?php echo $form->textFieldGroup($model,'charging_id',array('widgetOptions'=> array('htmlOptions'=>array('rows'=>6)))); ?>
    </div>

    <div class="row">
        <div class="span-8 ">
            <?php echo $form->switchGroup($model, 'is_visible',
                array(
                    'widgetOptions' => array(
                        'options'=>array(
                            'onText'=>"ВКЛ.",
                            'offText'=>"ВЫКЛ.",
                        ),
                    )
                )
            ); ?>

        </div>
    </div>

    <div class="row buttons">

        <?php $this->widget('booster.widgets.TbButton', array(
            'buttonType'=>'submit',
            'context'=>'primary',
            'label'=>$model->isNewRecord ? 'Добавить' : 'Сохранить',
        )); ?>

    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->