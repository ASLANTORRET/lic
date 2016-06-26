<html>
<head>
    <title></title>
</head>
<body>
<?php
/**
 * Created by PhpStorm.
 * User: Leo
 * Date: 10/29/14
 * Time: 6:07 PM
 */
$costInfo = false;
$dontShowCost = false;
$cats_counter = 0;

if(isset($message)){
    echo $message . "<br>";
}

foreach($model as $categoryID=>$category){
    foreach($category as $nestedID=>$nestedValue){
        $category_name = $nestedValue;
        $startElement = $nestedID;
    }

    if($categoryObj = Categories::model()->findByPk($categoryID)){
        if(!Categories::model()->exists('parent_category_id=:parent_category_id', array(':parent_category_id'=>$categoryObj->id)) && $costInfo == false && (strpos($categoryObj->script_url, 'subscribe') || strpos($categoryObj->script_url, 'Partner')) && $categoryObj->script_url!=null && $dontShowCost == false){
            $costInfo = true;
            $templ_cat_name = $sms_cost = null;
            if($templ_category = Categories::model()->findByPk($categoryObj->parent_category_id)){
                $templ_cat_name = $templ_category->category_name;
                $sms_cost = $templ_category->sms_cost;
                if($parent_category = Categories::model()->findByPk($templ_category->parent_category_id)){
                    if($parent_category->parent_category_id != 0){
                        $templ_cat_name = $parent_category->category_name . "/" . $templ_cat_name;
                    }
                }
            }

            $search_arr = array('category_name', 'sms_cost');
            $replace_arr = array($templ_cat_name, $sms_cost);
            $templateMessage = Yii::app()->params['USSD_USSD_SMScostInfo'];
            $costInfoText = str_replace($search_arr, $replace_arr, $templateMessage);
            echo $costInfoText . "<br>";
        }
        else{
            $dontShowCost = true;
        }
    }

    Yii::app()->params['ussd_shortcomb_' . $cats_counter++] = $categoryID . "_" . $startElement;

    foreach($category as $nestedID=>$nestedValue){
        $category_name = $nestedValue;
        $startElement = $nestedID;

        echo CHtml::link($category_name, array('categories/abonentpage', 'cat_id'=>$categoryID . '_' . $startElement));
        echo  "<br>";

        /*
        $category_name = $nestedValue;
        $startElement = $nestedID;
        echo CHtml::link($category_name, array('tree777and200/abonentpage', 'cat_id'=>$categoryID . '_' . $startElement));
        echo  "<br>";*/
    }
}
?>
</body>
</html>
