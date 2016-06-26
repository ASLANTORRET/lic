<?php

/**
 * This is the model class for table "tbl_categories".
 *
 * The followings are the available columns in table 'tbl_categories':
 * @property integer $id
 * @property string $name
 * @property string $service_name
 * @property integer $charging_id
 * @property integer $parent_category_id
 * @property integer $is_visible
 * @property string $update_time
 * @property string $create_time
 * @property string $is_final
 */
class Categories extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_categories';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, is_visible', 'required'),
			array('charging_id, is_visible', 'numerical', 'integerOnly'=>true),
			array('name, service_name', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, service_name, charging_id, is_visible, update_time, create_time, parent_category_id, subscribe_combination, unsubscribe_combination, is_final', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Название',
			'service_name' => 'Назв. услуги',
			'charging_id' => 'сhargingID',
            'is_final' => 'Конечная?',
			'is_visible' => 'Показать?',
			'update_time' => 'Время посл. редактирования',
			'create_time' => 'Время создания',
            'parent_category_id' => 'Родитель',
            'subscribe_combination'=> 'Команда подписки',
            'unsubscribe_combination'=> 'Команда отписки'
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('service_name',$this->service_name,true);
		$criteria->compare('charging_id',$this->charging_id);
        $criteria->compare('parent_category_id',$this->parent_category_id);
		$criteria->compare('is_visible',$this->is_visible);
        $criteria->compare('is_final',$this->is_final);
        $criteria->compare('subscribe_combination',$this->subscribe_combination, true);
        $criteria->compare('unsubscribe_combination',$this->unsubscribe_combination, true);
		$criteria->compare('update_time',$this->update_time,true);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    protected function beforeSave(){
        if(parent::beforeSave()){

            $today = new DateTime(date("Y-m-d H:i:s"));
            $today->setTimezone(new DateTimeZone('Asia/Almaty'));

            if($this->isNewRecord){

                $this->create_time = $this->update_time = $today->format("Y-m-d H:i:s");
                if(count($this->service_name)<=1 ){
                    $this->service_name = null;
                }
            }
            else{
                $this->update_time = $today->format("Y-m-d H:i:s");
                if(count($this->service_name)<=1 ){
                    $this->service_name = null;
                }
            }

            return true;
        }
        else{
            return false;
        }
    }

    public function getAttrByParam($condition = null, $options = null){

        $condition_params = $condition;

        if($condition_params == NULL){
            die( "Condition is empty");
        }


        $keys = array_keys($condition_params);

        foreach($keys as $index => $value){
            $keys[$index] = $value . "=:" . $value;
        }

        $condition = implode(" AND ", $keys);

        $sql = array("condition" => $condition,
            "params" => $condition_params);

        if($options != null){
            $sql = array_merge($sql, $options);
        }


        if($number = $this::model()->count($sql)){


            if($number > 1){

                $result_arr = array();

                $resultSet = $this::model()->findAll($sql);

                foreach($resultSet as $value){
                    $result_arr[]  = $value;
                }

                return array($withRecord = true,$result_arr);
            }

            else{
                $resultSet = $this::model()->findAll($sql);

                return array($withRecord = true, array($resultSet));
            }
        }
        else{
            return array($withRecord = false);
        }

    }

    public static function getDataByPk($pk, $columnn){

        if($category = Categories::model()->findByPk($pk)){
            return $category->$columnn;
        }
        else{
            return "";
        }
    }

    public static function constructAbonentPage($isStartPage=false, $catID=null, $showBack = true, $hiddencats = null){


        $search_arr = array("222" => "2", "333" => "3", "202" => "7", "303" => "40");

        $limit = 9;
        $startElement = $daleeIndex = 0;
        $categories_list = array();

        $indexPage = false;

        if($catID != null){

            if($isStartPage!=false){

                $parent_category_id = $catID;

                if(array_key_exists($parent_category_id, $search_arr)){

                    $parent_category_id = $search_arr[$parent_category_id];
                    $startElement = 9;
                }

                else{
                    $parent_category_id = (int)$parent_category_id;

                    $parent_category_id = $parent_category_id + 1;
                    $startElement = 0;
                }
            }
            else{
                $short_comb_value = $catID;
                $short_comb_value_arr = explode('_', $short_comb_value);
                $parent_category_id = $short_comb_value_arr[0];
                $startElement = (int)$short_comb_value_arr[1];

            }

            if($need_category = Categories::model()->findByPk($parent_category_id)){

                if(($need_category->parent_category_id == 0 && $startElement == 0) ||
                    ($need_category->parent_category_id == 0)){

                    $indexPage = true;
                }
            }


        }
        else{
            $parent_category_id = Categories::model()->find(array('order'=>'id ASC'))->id;

            $indexPage = true;
        }

        $sql = 'parent_category_id=:parent_category_id AND id>:id AND is_visible=:is_visible';
        $params = array(':parent_category_id'=>$parent_category_id, ':id'=>$startElement, ':is_visible'=>1);

        if($hiddencats != null){

            $hiddencats_data = array();

            foreach($hiddencats as $value){

                $hiddencats_data[":" . $value] = $value;

            }

            $sql = $sql . " AND  id NOT IN (" . implode(",", array_keys($hiddencats_data)) . ")";
            $params = array_merge($params, $hiddencats_data);

        }


        if($cats_number = Categories::model()->count( $sql , $params)){
            if($cats_number>1){
                if($indexPage==true){
                    $limit = $limit - 1;
                }
                else{
                    $limit = $limit -2;
                }

                $model = Categories::model()->findAll(array('select'=>'name, id', 'limit'=>$limit, 'condition'=>$sql, 'params'=>$params));

                foreach($model as $categories){
                    $categories_list[$categories->id][0] = $categories->name;
                    $daleeIndex = $categories->id;
                }

                if($cats_number > $limit){
                    $categories_list[$parent_category_id][$daleeIndex] = "Dalee";
                }
                if($indexPage!=true){
                    $first_element_id = Categories::model()->find(array('select'=>'id', 'condition'=>$sql, 'order'=>'id ASC', 'params'=>$params))->id;
                    if(($startElement ==  $first_element_id || $startElement == 0) && $showBack == true){
                        $prev_parent_cat_id = Categories::model()->findByPk($parent_category_id)->parent_category_id;
                        $categories_list[$prev_parent_cat_id][0] = "Nazad";
                    }
                    else if($showBack == true){
                        $nazadValue = (int)Yii::app()->params['nazad_start_element'];
                        $categories_list[$parent_category_id][$nazadValue] = "Nazad";
                    }
                }

            }
            else if($cats_number == 1){

                $model = Categories::model()->find(array('select'=>'name, id', 'limit'=>$limit, 'condition'=>$sql, 'params'=>$params));

                if($indexPage ==true){
                    $categories_list[$model->id][0] = $model->name;
                }
                else{
                    $categories_list[$model->id][0] = $model->name;
                    $first_element_id = Categories::model()->find(array('select'=>'id', 'condition'=>'parent_category_id=:parent_category_id', 'order'=>'id ASC', 'params'=>array(':parent_category_id'=>$parent_category_id)))->id;
                    if($showBack == true){
                        if($startElement ==  $first_element_id || $startElement == 0){
                            $prev_parent_cat_id = Categories::model()->findByPk($parent_category_id)->parent_category_id;
                            $categories_list[$prev_parent_cat_id][0] = "Nazad";
                        }
                        else{
                            $categories_list[$parent_category_id][0] = "Nazad";
                        }
                    }

                }
            }

        }

        Yii::app()->params['nazad_start_element'] = $startElement;

        return $categories_list;

    }

    public function catsForDropDownList($showServices = false, $isAdmin=false){

        $condition = "";

        if($showServices == true){
            $condition = " WHERE service_name IS NOT NULL";
        }

        if($cats_number = Categories::model()->countBySql($sql = "SELECT * FROM tbl_categories {$condition} ORDER BY name ASC")){


            if($cats_number > 1){
                $categories = Categories::model()->findAllBySql($sql);

                $list_array = array(null=>'Выберите категорию');
                if($isAdmin == true){
                    $list_array = array(null=>'--все--');
                }
                foreach($categories as $item){
                    $path = "";
                    $current_parent_id = $item->parent_category_id;
                    $str_temp = null;

                    if(Categories::model()->findByPk($current_parent_id, array('select'=>"parent_category_id"))){

                        while(Categories::model()->findByPk($current_parent_id, array('select'=>"parent_category_id"))->parent_category_id!=0){
                            $parent_category = Categories::model()->findByPk($current_parent_id);

                            $plusContentForParent = null;

                            $path = $parent_category->name .">" . $path;

                            $current_parent_id = $parent_category->parent_category_id;
                        }
                    }
                    $list_array[$item->getPrimaryKey() . ""] =  $path . $item->name;

                }
            }
            else{
                $item = Categories::model()->findBySql($sql);

                $list_array = array(null=>'Выберите категорию');

                    $path = "";
                    $current_parent_id = $item->parent_category_id;
                    $str_temp = null;

                    if(Categories::model()->findByPk($current_parent_id, array('select'=>"parent_category_id"))){
                        while(Categories::model()->findByPk($current_parent_id, array('select'=>"parent_category_id"))->parent_category_id!=0){
                            $parent_category = Categories::model()->findByPk($current_parent_id);

                            $plusContentForParent = null;

                            $path = $parent_category->name .">" . $path;

                            $current_parent_id = $parent_category->parent_category_id;
                        }
                    }
                    $list_array[$item->getPrimaryKey() . ""] =  $path . $item->name;

                }

        }

        return $list_array;
    }

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Categories the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
