<?php

/**
 * This is the model class for table "image".
 *
 * The followings are the available columns in table 'image':
 * @property integer $id
 * @property string $filename
 * @property integer $block_id
 */
class Image extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'image';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('filename, block_id', 'required'),
			array('block_id, sort', 'numerical', 'integerOnly'=>true),
			array('filename', 'length', 'max'=>255),
			//array('filename', 'file', 'safe' => true, 'types'=>'jpg, jpeg, gif, png'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, filename, block_id', 'safe', 'on'=>'search'),
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
			'block'=>array(self::BELONGS_TO, 'Block', 'block_id'),
		);
	}

	public function behaviors()
    {
        return CMap::mergeArray(parent::behaviors(), array(
			
            'imgBehaviorPreview' => array(
                'class' => 'application.behaviors.UploadableImageBehavior',
                'attributeName' => 'filename',
                'versions' => array(
                    // 'icon' => array(
                    //     'centeredpreview' => array(90, 90),
                    // ),
                    //    'small' => array(
                    //         'adaptiveresize' => array(300, 220),
                    // ),
                    'retina' => array(
                            'adaptiveresize' => array(640, false),
                    ),
                    'iphone6' => array(
                            'adaptiveresize' => array(750, false),
                    ),
                    'iphone6plus' => array(
                            'adaptiveresize' => array(1080, false),
                    ),
                    'original' => array(
                            'adaptiveresize' => array(320, false),
                    ),
                    'thumbs' => array(
                            'adaptiveresize' => array(100, 100),
                    ),
                    
                     
                ),
            ),

        ));
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'filename' => 'Изображение',
			'block_id' => 'Блок',
			'sort' => 'Вес'
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
		$criteria->compare('filename',$this->filename,true);
		$criteria->compare('block_id',$this->block_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Image the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	protected function afterDelete(){
		@unlink(YiiBase::getPathOfAlias('webroot')."/uploads/{$this->block_id}/".$this->filename);
		@unlink(YiiBase::getPathOfAlias('webroot')."/uploads/{$this->block_id}/retina/".$this->filename);
		@unlink(YiiBase::getPathOfAlias('webroot')."/uploads/{$this->block_id}/thumbs/".$this->filename);

		return parent::afterDelete();
	}
}
