<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

?>
<div style="margin:0 auto;margin-top:15%; min-width: 300px;max-width: 320px;">


    <div class="form">
        <div class="span-10">
            <?php
            $form = $this->beginWidget(
                'booster.widgets.TbActiveForm',
                array(

                    'id' => 'verticalForm',
                    'htmlOptions' => array('class' => 'well'), // for inset effect
                    'type' => 'vertical',
                )
            );
            ?>
            <legend><h2>Авторизация</h2></legend>

            <p class="note">Поля, обязательные к заполнению <span class="required">*</span> .</p>
            <?php
            echo $form->textFieldGroup($model, 'username');
            echo $form->passwordFieldGroup($model, 'password');
            echo $form->checkboxGroup($model, 'rememberMe');
            $this->widget(
                'booster.widgets.TbButton',
                array('buttonType' => 'submit', 'context'=>'primary', 'label' => 'Войти')
            );

            $this->endWidget();
            unset($form);
            ?>

        </div><!-- form -->
    </div><!-- form -->
</div>
