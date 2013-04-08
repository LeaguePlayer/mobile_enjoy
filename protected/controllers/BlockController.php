<?php

class BlockController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */

	private $uploadsDirName = 'uploads';
	//private $uploadsDir = '/uploads/';

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
				'actions'=>array('create','update'),
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
		//include jQuery UI
		Yii::app()->getClientScript()->registerCoreScript( 'jquery.ui' );
		Yii::app()->clientScript->registerCssFile(
			Yii::app()->clientScript->getCoreScriptUrl().
			'/jui/css/base/jquery-ui.css'
		);

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
		$model=new Block;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Block']))
		{
			$model->attributes = $_POST['Block'];
			$model->preview = $this->createImage(CUploadedFile::getInstance($model,'preview'));

			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	//Save image
	private function createImage($uploadFile){
		if($uploadFile){
			$uploadsDir =  YiiBase::getPathOfAlias('webroot').DIRECTORY_SEPARATOR.$this->uploadsDirName.DIRECTORY_SEPARATOR;
			if(!is_dir($uploadsDir)){
				@mkdir($uploadsDir);
			}

			$thumb = Yii::app()->phpThumb->create($uploadFile->tempName);

			switch ($_POST['preview_size']) {
				case Block::PREVIEW_126X124:
					$thumb->resize(126, 124);
					break;
				case Block::PREVIEW_252X248:
					$thumb->resize(252, 248);
					break;
			}

			$filename = md5(mktime()).".".$uploadFile->extensionName;
			$thumb->save($uploadsDir.$filename);
			return DIRECTORY_SEPARATOR.$this->uploadsDirName.DIRECTORY_SEPARATOR.$filename;
		}
		return '';
	}

	//Delete image
	private function deleteImage($imagePath){
		@unlink(YiiBase::getPathOfAlias('webroot').DIRECTORY_SEPARATOR.$imagePath);
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

		$current_image = $model->preview;

		if(!empty($_POST['deleteImage']))
			$this->deleteImage($model->preview);

		if(isset($_POST['Block']))
		{
			$model->attributes=$_POST['Block'];
			$model->preview = $this->createImage(CUploadedFile::getInstance($model,'preview'));

			if($model->preview == '')
				$model->preview = $current_image;

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
		$this->deleteImage($model->preview);
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
		$dataProvider=new CActiveDataProvider('Block');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Block('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Block']))
			$model->attributes=$_GET['Block'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Block the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Block::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Block $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='block-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
