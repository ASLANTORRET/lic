<?php

/**
 * This is the model class for table "tbl_content".
 *
 * The followings are the available columns in table 'tbl_content':
 * @property integer $id
 * @property integer $category_id
 * @property string $create_time
 * @property string $update_time
 * @property string $content
 * @property string $sex
 * @property string $age
 * @property string $relation
 * @property string $status
 * @property string $physics
 * @property integer $day
 */
class Content extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */

	public function tableName()
	{
		return 'tbl_content';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('category_id, day, content', 'required'),
			array('category_id, day', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, category_id, create_time, update_time, day, content, sex, age, relation, status, physics', 'safe', 'on'=>'search'),
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
			'category_id' => 'Услуга',
			'create_time' => 'Время создания',
            'content' => 'Контент',
            'sex' => 'Пол',
            'age' => 'Возраст',
            'relation' => 'Статус отношений',
            'physics' => 'Уровень подготовки',
			'update_time' => 'Время посл. обновления',
			'day' => 'День',
            'status'=>'Статус'
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
        $criteria->compare('content',$this->content, true);
		$criteria->compare('category_id',$this->category_id);
        $criteria->compare('sex',$this->sex);
        $criteria->compare('age',$this->age);
        $criteria->compare('status',$this->status);
        $criteria->compare('relation',$this->relation);
        $criteria->compare('physics',$this->physics);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('update_time',$this->update_time,true);
		$criteria->compare('day',$this->day);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize' => 25
            )
		));
	}

    protected function beforeSave(){
        if(parent::beforeSave()){

            $today = new DateTime(date("Y-m-d H:i:s"));
            $today->setTimezone(new DateTimeZone('Asia/Almaty'));

            if($this->isNewRecord){

                $this->create_time = $this->update_time = $today->format("Y-m-d H:i:s");
            }
            else{
                $this->update_time = $today->format("Y-m-d H:i:s");
            }

            return true;
        }
        else{
            return false;
        }
    }


    public static function checkMNP($phone){
        $mnp = new KZMnp();
        $mnp->phone = $phone;
        $mnp->is_local = false;
        $mnp->query();
        return $mnp->mnc;
    }

    public static function getContent($subscriber){

        $category_id = $subscriber->category_id;

        if(Profiles::model()->exists($sql = array("condition"=>"subscriber_id=:si", "params"=>array(":si"=>$subscriber->id)))){

            $profile = Profiles::model()->find($sql);

            if($content = Content::model()->find(array("condition"=>"category_id=:ci AND sex=:sex AND age=:age AND relation=:r AND physics=:ph AND status=:st AND day=:day", "order"=>"id DESC", "limit"=>'1',
                                                        "params"=> array(":ci"=>$category_id, ":sex"=>$profile->sex, ":age"=>$profile->age, ":r"=>$profile->relation, ":ph"=> $profile->physics, ":st"=>1, ":day"=>$profile->day))
                                           )){
                $SMSContent = $content->content;
                $contentID = $content->id;

                $profile->day = $profile->day + 1;
                $profile->save(false);

                return array(true, array($contentID, $SMSContent));
            }
            else{
                return array(false);
            }
        }
        else{
            if($content = Content::model()->find(array("condition"=>"category_id=:ci AND sex=:sex AND age=:age AND relation=:r  AND physics=:ph AND status=:st AND day=:day", "order"=>"id DESC", "limit"=>'1',
                                                        "params"=> array(":ci"=>$category_id, ":sex"=>0, ":age"=>0, ":r"=>0, ":ph"=>0, ":st"=>1, ":day"=>1))
            )){
                $SMSContent = $content->content;
                $contentID = $content->id;
                return array(true, array($contentID, $SMSContent));
            }else{
                return array(false);
            }
        }

    }

    public static function getShortVersion($pk){
        $length = 225;
        if(Content::model()->findByPk($pk)){
            $content_length = strlen($content = Content::model()->findByPk($pk)->content);
            echo mb_substr($content, 0 , (int)$length, "utf-8") . (($content_length>=$length)?("<a href='". $pk ."' title='" . $content ."'>...></a>"):"");
        }
    }

    public static function getBulkContent($day, $category_id = null){

        $content_container = array();

        if(count($day) == 2){

            if($content_number = Content::model()->count($condition = "day <= :maxday AND day >= :minday AND status=:s AND category_id=:ci", $params = array(":maxday"=>$day[1], ":minday"=>$day[0], ":s"=>1, ":ci" => $category_id))){


                if($content_number > 1){
                     $contents = Content::model()->findAll($condition . " ORDER BY id DESC", $params);

                    foreach($contents as $value){
                        $content_container[$value->sex][$value->age][$value->relation][$value->physics][$value->day] = array($value->id, $value->content);
                    }
                }
                else{
                    $content = Content::model()->find($condition . " ORDER BY id DESC", $params);
                    $content_container[$content->sex][$content->age][$content->relation][$content->physics][$content->day] = array($content->id, $content->content);
                }
            }
        }

        return $content_container;
    }


    public static function getBulkContentAdv(/*$maxday = null,*/ $category_id = null){

        $content_container = array();

       // if( $maxday != null){

            if($content_number = Content::model()->count($condition = "status=:s AND category_id=:ci", $params = array(":s"=>1, ":ci" => $category_id))){

                if($content_number > 1){
                    $contents = Content::model()->findAll($condition . " ORDER BY id DESC", $params);

                    foreach($contents as $value){
                        $content_container[$value->sex][$value->age][$value->relation][$value->physics][$value->day] = array($value->id, $value->content);
                    }
                }
                else{
                    $content = Content::model()->find($condition . " ORDER BY id DESC", $params);
                    $content_container[$content->sex][$content->age][$content->relation][$content->physics][$content->day] = array($content->id, $content->content);
                }
            }
        //}

        return $content_container;
    }


    public static function dropdownFilterAge($pk){

        $content = Content::model()->findByPk($pk);

        return CHtml::dropDownList('Content[age]', $content->age, Yii::app()->params['interface']['age'][$content->category_id], array('class'=>'form-control'));
    }
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Content the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
