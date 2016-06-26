<?php
/* @var $this TreeDSTKController */
/* @var $model Content */

?>
<div class="confer">

    <div class="container">

        <? if($model == null) {
            ?>
            <div class='alert alert-danger' style="margin-top: 35px;" role="alert"> К сожалению бронь с указанным номером не найден. Повторите попытку.</div>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
        <?
        }
        else{
            ?>
            <div class='alert alert-success' style="margin-top: 35px;" role="alert"> Рады вас видеть снова,<b> <? echo $model->name . " " . $model->surname; ?></b></span></div>
            <h4>Вот данные вашей брони: </h4>
            <?php
            $this->widget(
                'bootstrap.widgets.TbDetailView',
                array(
                    'data' => $model,
                    'attributes' => array(
                        'room_type',
                        'number_of_rooms',
                        'id_card',
                        'total_price',
                        'booking_start',
                        'booking_end',
                        'name',
                        'surname',
                        'email',
                        'wishes',
                    ),
                )
            );
        }
        ?>

        <div class="row buttons" style="float:right;margin-right: 5px;">

            <?=CHtml::linkButton('На главную', array(
                'submit'=>array(
                    '/',
                ),
                'class' => 'btn btn-default btn-sm',

            ))?>

        </div>

    </div>
</div>