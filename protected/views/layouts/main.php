<?php /* @var $this Controller*/ ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="en" />

    <!-- blueprint CSS framework -->
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
    <!--[if lt IE 8]>
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
    <![endif]-->

    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />

    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <?php Yii::app()->bootstrap->register(); ?>
</head>

<body>

<div class="container" id="content">

    <div>
        <?php

        if(Yii::app()->user->name == "partnermg5"){
            $this->widget(
                'booster.widgets.TbNavbar',
                array(
                    'brand' => 'USSD Services',
                    'fixed' => true,
                    'fluid' => true,
                    'items' => array(
                        array(
                            'class' => 'booster.widgets.TbMenu',
                            'type' => 'navbar',
                            'items' => array(

                                array('label' => 'Подписчики',
                                    'url' => array('subscribers/admin')
                                ),


                                array('label' => 'Польз. даннные',
                                    'items' => array(
                                        array('label'=>'Профили', 'url' => array('/profiles/admin')),
                                        array('label'=> 'Опросы', 'url' => array('/questions/results')),
                                    )),

                               /* array('label' => 'Логи',

                                    'items' => array(
                                        array('label'=>'«Входящие сообщения»', 'url' => array('/profiles/logMO')),
                                        array('label'=> '«Исходящие сообщения»', 'url' => array('/profiles/mssgslogs')),
                                        '--',
                                        array('label'=> 'Логи продвижения', 'url' => array('/script_logs/provider_prom.log'), 'linkOptions' => array(
                                            'target' => '_blank'
                                        )),
                                        array('label'=> 'Работа скриптов', 'url' => array('/script_logs/script_center.log'), 'linkOptions' => array(
                                            'target' => '_blank'
                                        )),
                                    )),*/

                                array('label' => 'Авторизоваться', 'url' => array('/user/index'),'visible'=>Yii::app()->user->isGuest),
                                array('label' => 'Выйти ('.Yii::app()->user->name.')', 'url' => array('/site/logout'),'visible'=>!Yii::app()->user->isGuest)

                            )
                        )
                    )
                )
            );
        }
        else{
            $this->widget(
                'booster.widgets.TbNavbar',
                array(
                    'brand' => 'USSD Services',
                    'fixed' => true,
                    'fluid' => true,
                    'items' => array(
                        array(
                            'class' => 'booster.widgets.TbMenu',
                            'type' => 'navbar',
                            'items' => array(
                                array('label' => 'Услуги и категории',
                                    'items' => array(
                                        array('label'=>'Создать категорию', 'url' => array('/categories/create')),
                                        array('label'=> 'Панель управления', 'url' => array('/categories/admin')),
                                        '---',
                                        array('label'=> 'Страница абонента', 'url' => array('/categories/abonentPage'), 'linkOptions' => array(
                                            'target' => '_blank'
                                        )),
                                    )),

                                array('label' => 'Контент',
                                    'items' => array(
                                        array('label'=>'Создать контент', 'url' => array('/content/create')),
                                        array('label'=> 'Панель управления «Контент»', 'url' => array('/content/admin')),
                                        '--',
                                        array('label'=>'Создать вопрос', 'url' => array('/questions/create')),
                                        array('label'=> 'Панель управления «Вопрос»', 'url' => array('/questions/admin')),
                                    )),

                                array('label' => 'Подписчики',
                                    'url' => array('subscribers/admin')
                                ),


                                array('label' => 'Польз. даннные',
                                    'items' => array(
                                        array('label'=>'Профили', 'url' => array('/profiles/admin')),
                                        array('label'=> 'Опросы', 'url' => array('/questions/results')),
                                    )),

                                array('label' => 'Статистика',
                                    'items' => array(
                                        array('label'=>'Доставленные сообщения(день)', 'url' => array('/content/statsDlvrd'))
                                    )),

                                array('label' => 'Логи',

                                    'items' => array(
                                        array('label'=>'«Входящие сообщения»', 'url' => array('/profiles/logMO')),
                                        array('label'=> '«Исходящие сообщения»', 'url' => array('/profiles/mssgslogs')),
                                        '--',
                                        array('label'=> 'Логи продвижения', 'url' => array('/script_logs/provider_prom.log'), 'linkOptions' => array(
                                            'target' => '_blank'
                                        )),
                                        array('label'=> 'Работа скриптов', 'url' => array('/script_logs/script_center.log'), 'linkOptions' => array(
                                            'target' => '_blank'
                                        )),
                                    )),

                                array('label' => 'Авторизоваться', 'url' => array('/user/index'),'visible'=>Yii::app()->user->isGuest),
                                array('label' => 'Выйти ('.Yii::app()->user->name.')', 'url' => array('/site/logout'),'visible'=>!Yii::app()->user->isGuest)

                            )
                        )
                    )
                )
            );
        }

        ?>
    </div>

    <?php if(isset($this->breadcrumbs)):?>
        <?php $this->widget('zii.widgets.CBreadcrumbs', array(
            'links'=>$this->breadcrumbs,
        )); ?><!-- breadcrumbs -->
    <?php endif?>
    <?php echo $content; ?>
    <?php
    ?>
    <div class="clear"></div>

    <div id="footer">
         <br> &copy; <?php echo date('Y'); ?> USSD Services.<br/>

    </div><!-- footer -->

</div><!-- page -->

</body>
</html>
