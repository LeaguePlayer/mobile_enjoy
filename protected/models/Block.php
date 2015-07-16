<?php

/**
 * This is the model class for table "block".
 *
 * The followings are the available columns in table 'block':
 * @property integer $id
 * @property string $name
 * @property string $price
 * @property string $preview
 * @property integer $public
 */
class Block extends CActiveRecord
{

	const PREVIEW_126X124 = 1;
	const PREVIEW_252X248 = 2;

	private $uploadsDirName = '/uploads/';

	// public previewArray = array(
	// 	Block::PREVIEW_126X124 => '126x124',
	// 	Block::PREVIEW_252X248 => '252x248'
	// );

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'block';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, price, public, preview', 'required'),
			array('public, sort, owner', 'numerical', 'integerOnly'=>true),
			array('name, preview, desc', 'length', 'max'=>255),
			array('preview', 'file', 'safe' => true, 'types'=>'jpeg, jpg, gif, png', 'on'=>'insert'),
			array('price', 'length', 'max'=>9),
			array('price', 'numerical', 'min'=>0, 'max'=>9.99),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, price, preview, public, sort, desc', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */

	public function defaultScope(){
		if (Yii::app()->user->checkAccess('block.all') || Yii::app()->user->isAdmin)
			return array(
	            'alias' =>'t',
	        );
		else 
			return array(
				'alias'=>'t',
				'condition'=>'owner=:owner',
				'params'=>array(':owner'=>Yii::app()->user->id)
			);
	}

	public function beforeSave(){
		if ($this->scenario=='insert')
			$this->owner=Yii::app()->user->id;
		return parent::beforeSave();
	}

	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'images'=>array(self::HAS_MANY, 'Image', 'block_id', 'order'=>'sort'),
		);
	}

	public function getPreviewArray(){
		return array(
			Block::PREVIEW_126X124 => '126x124',
			Block::PREVIEW_252X248 => '252x248'
		);
	}

	public function behaviors()
    {
        return CMap::mergeArray(parent::behaviors(), array(
			
            'imgBehaviorPreview' => array(
                'class' => 'application.behaviors.UploadableImageBehavior',
                'attributeName' => 'preview',
                'versions' => array(
                    // 'icon' => array(
                    //     'centeredpreview' => array(90, 90),
                    // ),
                    //    'small' => array(
                    //         'adaptiveresize' => array(300, 220),
                    // ),
                    'retina' => array(
                            'adaptiveresize' => array(253, 249),
                    ),
                    'original' => array(
                            'adaptiveresize' => array(129, 127),
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
			'name' => 'Название блока',
			'desc' => 'Краткое описание',
			'price' => 'Цена',
			'preview' => 'Превью',
			'public' => 'Опубликовать',
			'sort' => 'Сортировка'
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
		$criteria->compare('price',$this->price,true);
		$criteria->compare('preview',$this->preview,true);
		$criteria->compare('public',$this->public);
		$criteria->compare('sort',$this->sort);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Block the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	protected function afterDelete(){
		@unlink(YiiBase::getPathOfAlias('webroot').$this->uploadsDirName.$this->preview);
		@unlink(YiiBase::getPathOfAlias('webroot').$this->uploadsDirName.'retina/'.$this->preview);

		if($this->images){
			foreach ($this->images as $item) {
				$item->delete();
			}
		}

		return parent::afterDelete();
	}
}
