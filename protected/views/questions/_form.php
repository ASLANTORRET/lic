<?php
/* @var $this QuestionsController */
/* @var $model Questions */
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
            'category_id',
            array(
                'wrapperHtmlOptions' => array(
                    'class' => 'col-md-7',
                ),
                'widgetOptions' => array(
                    'data' => Categories::model()->catsForDropDownList(true),
                )
            )
        );

        ?>
    </div>

    <div class="row">
        <?php echo $form->textAreaGroup($model,'question',array('widgetOptions'=> array('htmlOptions'=>array('rows'=>3)))); ?>
    </div>

    <div class="row">
        <div class="span-8 ">
            <?php echo $form->switchGroup($model, 'status',
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

    <legend>Варианты ответов</legend>
    <div class="row">
        <?php echo $form->textFieldGroup($model,'variant1'); ?>
    </div>
    <div class="row">
        <?php echo $form->textFieldGroup($model,'variant2'); ?>
    </div>
    <div class="row">
        <?php echo $form->textFieldGroup($model,'variant3'); ?>
    </div>


    <legend>Ответы</legend>
    <div class="row">
        <?php echo $form->textFieldGroup($model,'response1'); ?>
    </div>
    <div class="row">
        <?php echo $form->textFieldGroup($model,'response2'); ?>
    </div>
    <div class="row">
        <?php echo $form->textFieldGroup($model,'response3'); ?>
    </div>

    <legend>Характеры</legend>
    <div class="row">
        <?php echo $form->textFieldGroup($model,'character1'); ?>
    </div>
    <div class="row">
        <?php echo $form->textFieldGroup($model,'character2'); ?>
    </div>
    <div class="row">
        <?php echo $form->textFieldGroup($model,'character3'); ?>
    </div>



    <div class="row buttons">

        <?
        echo CHtml::linkButton('Отменить', array(
            'submit'=>array(
                'questions/admin',
                'id'=>$model->id
            ),
            'class' => 'btn btn-default',
        ));

        ?>

        <?php $this->widget('booster.widgets.TbButton', array(
            'buttonType'=>'submit',
            'label'=>$model->isNewRecord ? 'Добавить' : 'Сохранить',
        ));

        if($this->action->id=="create"){$this->widget('booster.widgets.TbButton', array(

            'buttonType'=>'submit',
            'context'=>'primary',
            'label'=>'Добавить и заполнить еще'
        ));
        }
        ?>

    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->