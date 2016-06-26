<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<body class="">

<div class="main">
    <div id="content" style="-webkit-transform: translate3d(0px, 0px, 0px); transform: translate3d(0px, 0px, 0px);">
        <header class="header">
            <div class="header-top">
                <a href="" class="logo">Онлайн андройд&nbsp;портал</a>
            </div>
        </header>

        <div class="line"></div>
        <div class="content">


            <input type="hidden" id="one_content_url" value="/category/640/one/default">
            <input type="hidden" id="category_id" value="331">
            <input type="hidden" id="first_item_count" value="5">
            <input type="hidden" id="page_block_id" value="75">
            <div style="margin: 18px 0px;color: rgb(45, 45, 140);">
                Для получения доступа к онлайн андройд порталу, отправьте SMS с текстом GAME на номер 900.
                В ответ Вы получите прямую ссылку для скачивания игр.

                Стоимость SMS составляет 50 тенге с НДС 12%
            </div>
            <div class="unit">
                <!-- products-section -->
                <section class="products-section content_append_wrapper">
                    <?

                    /*$value = Yii::app()->cache->get('1');
                    if($value===false)
                    {
                        $value = "ok";
                        Yii::app()->cache->set('1', $value);
                    }

                    echo $value;*/

                    foreach($model as $index=>$game){
                        if($index != 0){
                            echo "<div class='sep-line-got'></div>";
                        }
                        ?>
                        <div class="prev-g-item">
                            <div class="prev-g-item_photo">
                                <a style="text-decoration: underline;" href="site/appinfo?app_id=<?=$game->id?>"> <img src="<?= Games::getAppPath($game->id) . "/" . $game->image?>" alt="">
                                </a>    </div>

                            <div class="prev-g-item_column">
                                <div class="prev-g-item_name">
                                    <a style="text-decoration: underline;" href="site/appinfo?app_id=<?=$game->id?>"><?=$game->name?> </a>        </div>

                                <div class="prev-g-item_descr">

                                </div>

                                <div class="prev-g-item_buttons">

                                </div>
                            </div>
                        </div>
                    <?
                    }
                    ?>


                    <div id="popup_wrapper"></div>
            </div><!-- content -->




