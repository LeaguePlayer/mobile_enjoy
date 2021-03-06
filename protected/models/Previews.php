<?php

/**
 * This is the model class for table "previews".
 *
 * The followings are the available columns in table 'previews':
 * @property integer $id
 * @property string $id_unique
 * @property string $data_image
 * @property string $create_date
 */
class Previews extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'previews';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_unique', 'required'),
			array('id_unique', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_unique, data_image, create_date', 'safe', 'on'=>'search'),
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
			'id_unique' => 'Id Unique',
			'data_image' => 'Data Image',
			'create_date' => 'Create Date',
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
		$criteria->compare('id_unique',$this->id_unique,true);
		$criteria->compare('data_image',$this->data_image,true);
		$criteria->compare('create_date',$this->create_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Previews the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getFullImage()
	{
		$ar = array();

		$uploadsDirFile =  YiiBase::getPathOfAlias('webroot').'/preview_files/';
			if(!is_dir($uploadsDirFile)) @mkdir($uploadsDirFile);

			$fileName = "{$this->id_unique}.txt";

			$full_path_to_file = $uploadsDirFile.$fileName;

			if(is_file($full_path_to_file))
			{
				$myfile = fopen($full_path_to_file, "r");
				$exist_string = fgets($myfile);
				fclose($myfile);
				
				$ar = unserialize($exist_string);
				
			


				$result = "";
				
				// $ar = unserialize($this->data_image);
				ksort($ar);
				foreach($ar as $a)
					$result .= $a;


			}

		return $result;

	}
}
