<?php

/**
 * This is the model class for table "tbl_script_work".
 *
 * The followings are the available columns in table 'tbl_script_work':
 * @property integer $id
 * @property string $start_date
 * @property integer $type
 * @property integer $category_id
 * @property integer $is_rebill_enabled
 */
class ScriptWork extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */

	public function tableName()
	{
		return 'tbl_script_work';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('id, start_date, category_id, is_rebill_enabled, type', 'safe'),
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

    public static function assignAttributes($attrubutes){
        $model = new ScriptWork();
        foreach($attrubutes as $index=>$value){
            $model->$index = $value;
        }
        $model->save(false);
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


        if($number = $this::model()->count($sql)){

            if($options != null){
                $sql = array_merge($sql, $options);
            }

            if($number > 1){

                $result_arr = array();

                $resultSet = $this::model()->findAll($sql);

                foreach($resultSet as $value){
                    $result_arr[]  = $value;
                }

                return array($withRecord = true,$result_arr);
            }

            else{
                $resultSet = $this::model()->find($sql);

                return array($withRecord = true, array($resultSet));
            }
        }
        else{
            return array($withRecord = false);
        }

    }

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

}
