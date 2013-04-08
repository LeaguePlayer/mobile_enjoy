<?php

class ImageController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	private $uploadsDirName = '/uploads/';
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update', 'setsort'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Image;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Image']))
		{
			$model->attributes=$_POST['Image'];
			$model->filename = $this->createImage(CUploadedFile::getInstance($model,'filename'), $model->block_id);
			
			if($model->save()){
				if(!empty($_POST['back']))
					$this->redirect(array('block/view','id'=>$_GET['block']));
				$this->redirect(array('view','id'=>$model->id));
			}
				
		}

		$blocks = Block::model()->findAll();
		if(!empty($_GET['block']))
			$model->block_id = $_GET['block'];

		$this->render('create',array(
			'model'=>$model,
			'blocks'=>$blocks
		));
	}

	//Save image
	private function createImage($uploadFile, $block){
		if($uploadFile){
			$uploadsDir =  YiiBase::getPathOfAlias('webroot').$this->uploadsDirName;
			if(!is_dir($uploadsDir)) @mkdir($uploadsDir);

			$blockDir = $uploadsDir.$block.'/';
			if(!is_dir($blockDir)) @mkdir($blockDir);

			$retinaDir = $blockDir.'retina/';
			if(!is_dir($retinaDir)) @mkdir($retinaDir);

			$thumbDir = $blockDir.'thumbs/';
			if(!is_dir($thumbDir)) @mkdir($thumbDir);

			$thumb = Yii::app()->phpThumb->create($uploadFile->tempName);
			$thumbDef = Yii::app()->phpThumb->create($uploadFile->tempName);
			$thumbRetina = Yii::app()->phpThumb->create($uploadFile->tempName);

			$fileName = md5(mktime()).".".$uploadFile->extensionName;

			$thumb->adaptiveResize(100, 100)->save($thumbDir.$fileName);
			$thumbDef->resize(320)->save($blockDir.$fileName);
			$thumbRetina->resize(640)->save($retinaDir.$fileName);

			return $fileName;
		}
		return '';
	}

	//Delete image
	private function deleteImage($model){
		@unlink(YiiBase::getPathOfAlias('webroot')."/uploads/{$model->block_id}/".$model->filename);
		@unlink(YiiBase::getPathOfAlias('webroot')."/uploads/{$model->block_id}/retina/".$model->filename);
		@unlink(YiiBase::getPathOfAlias('webroot')."/uploads/{$model->block_id}/thumbs/".$model->filename);
	}

	//Update ajax Fields sort
	public function actionSetSort(){
        if(count($_POST['items']) > 0){
            foreach ($_POST['items'] as $item) {
                $row = Image::model()->find('id = :id', array(':id' => $item['id']));
                $row->setAttribute('sort', $item['sort']);
                $row->update();
            }
        }
        Yii::app()->end();
    }

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Image']))
		{
			$model->attributes=$_POST['Image'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$model = $this->loadModel($id);
		$this->deleteImage($model);
		$model->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Image');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Image('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Image']))
			$model->attributes=$_GET['Image'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Image the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Image::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Image $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='image-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
