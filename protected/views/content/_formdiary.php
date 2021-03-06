<?php
/* @var $this ContentController */
/* @var $model Content */
/* @var $form CActiveForm */

if($model->isNewRecord){
    Yii::app()->clientScript->registerScript('re-install-date-picker_di', "
        $('#opt-sexdiary').change(function(){

            if($('#opt-sexdiary').val()!='0'){
                $('#opt-agediary').removeAttr('disabled');
            }else{
                $('#opt-agediary').attr('disabled','true');
                $('#opt-physics').attr('disabled', 'true');
            }

            }
        );

        $('#opt-agediary').change(function(){
            if($('#opt-agediary').val()!='0'){
                $('#opt-physics').removeAttr('disabled');
            }else{
                $('#opt-physics').attr('disabled', 'true');
            }
            }
        );

    ");
}
else{
    Yii::app()->clientScript->registerScript('re-install-date-picker_di', "

        $('#opt-agediary').removeAttr('disabled');
        $('#opt-sexdiary').removeAttr('disabled');
        $('#opt-physics').removeAttr('disabled');
");
}

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

    $model->category_id = 3;            //assings cats ID

    ?>

    <p class="note">Поля, обязательные к заполнению. <span class="required">*</span></p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row" style="display: none;">
        <?php
        echo $form->dropDownListGroup(
            $model,
            'category_id',
            array(
                'wrapperHtmlOptions' => array(
                    'class' => 'col-md-7',
                ),
                'widgetOptions' => array(

                    'data' => Categories::model()->catsForDropDownList(),

                    'htmlOptions'=>array(
                        "style" => "display:none;",
                        'options'=>array("3" => array("selected" => "selected"))
                    )
                )
            )
        );

        ?>
    </div>

    <div class="row">
        <?php echo $form->textAreaGroup($model,'content',array('widgetOptions'=> array('htmlOptions'=>array('rows'=>3)))); ?>
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

    <div class="row">
        <?php echo $form->textFieldGroup($model,'day'); ?>
    </div>

    <div class="row">
        <?php
        echo $form->dropDownListGroup(
            $model,
            'sex',
            array(
                'wrapperHtmlOptions' => array(
                    'class' => 'col-md-7',
                ),
                'widgetOptions' => array(
                    'data' => Yii::app()->params["form"]["sex"],
                    'htmlOptions'=>array('id'=>'opt-sexdiary')
                )
            )
        );

        ?>
    </div>
    <div class="row">
        <?php
        echo $form->dropDownListGroup(
            $model,
            'age',
            array(
                'wrapperHtmlOptions' => array(
                    'class' => 'col-md-7',
                ),
                'widgetOptions' => array(
                    'data' => Yii::app()->params["form"]["agediary"],
                    'htmlOptions'=>array('disabled'=>true, 'id'=>'opt-agediary')
                )
            )
        );

        ?>
    </div>

    <div class="row">
        <?php
        echo $form->dropDownListGroup(
            $model,
            'physics',
            array(
                'wrapperHtmlOptions' => array(
                    'class' => 'col-md-7'
                ),
                'widgetOptions' => array(
                    'data' => Yii::app()->params["form"]["physics"],
                    'htmlOptions'=>array('disabled'=>true , 'id'=>'opt-physics')
                ),

            )
        );

        ?>
    </div>

    <div class="row buttons">

        <?
        echo CHtml::linkButton('Отменить', array(
            'submit'=>array(
                'content/admin',
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