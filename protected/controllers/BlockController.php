<?php

class BlockController extends FrontController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */

	private $uploadsDirName = '/uploads/';
	//private $uploadsDir = '/uploads/';

	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	
	public function filters(){
		return array(
			array('application.modules.auth.filters.AuthFilter'),
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */

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

		$arraySizes = $this->getBlockSize($id);
		
		$this->render('view',array(
			'model'=>$this->loadModel($id),
			'arraySizes'=>$arraySizes
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
			$model->preview = CUploadedFile::getInstance($model,'preview');

			$model->sort = 1000;

			if($model->validate()){
				// $model->preview = $this->createImage($model->preview);
				$model->save(false);
				$this->redirect(array('view','id'=>$model->id));
			}

			// if($model->save())
			// 	$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	//Save image
	private function createImage($uploadFile){
		if($uploadFile){
			$uploadsDir =  YiiBase::getPathOfAlias('webroot').$this->uploadsDirName;
			if(!is_dir($uploadsDir)) @mkdir($uploadsDir);

			$retinaDir = $uploadsDir.'retina/';
			if(!is_dir($retinaDir)) @mkdir($retinaDir);

			$filename = md5(mktime()).".".$uploadFile->extensionName;

			$thumb = Yii::app()->phpThumb->create($uploadFile->tempName);
			$thumbRetina = Yii::app()->phpThumb->create($uploadFile->tempName);

			$thumb->adaptiveResize(129, 127)->save($uploadsDir.$filename);
			$thumbRetina->adaptiveResize(253, 249)->save($retinaDir.$filename);

			$filename = md5(mktime()).".".$uploadFile->extensionName;

			return $filename;
		}
		return '';
	}

	//Delete image
	private function deleteImage($filename){
		@unlink(YiiBase::getPathOfAlias('webroot').$this->uploadsDirName.$filename);
		@unlink(YiiBase::getPathOfAlias('webroot').$this->uploadsDirName.'retina/'.$filename);
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
		$model->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		//include jQuery UI
		Yii::app()->getClientScript()->registerCoreScript( 'jquery.ui' );
		Yii::app()->clientScript->registerCssFile(
			Yii::app()->clientScript->getCoreScriptUrl().
			'/jui/css/base/jquery-ui.css'
		);

		$dataProvider=new CActiveDataProvider('Block', array(
			'pagination' => false,
			'criteria' => array(
				'order'=>'sort ASC'
			)
		));

		$arraySizes = $this->getBlockSize();

		$this->render('index',array(
			'dataProvider'=>$dataProvider,
			'arraySizes' => $arraySizes
		));
	}

	private function getBlockSize($id = 0){
		$arraySizes = array();
		$pathToUploads = YiiBase::getPathOfAlias('webroot').$this->uploadsDirName;

		if(is_numeric($id)){
			$blocks = array();
			if($id == 0){ //Посчитать размер всех блоков
				$blocks = Block::model()->findAll();
			}elseif($id > 0){
				$blocks = Block::model()->findAll(array('condition' => 'id = :id', 'params'=>array(':id' => $id)));
			}

			if(!empty($blocks)){
				foreach ($blocks as $item) {
					$id = $item->id;
					$arraySizes[$id] = 0;
					if(is_file($pathToUploads.$item->preview))
						$arraySizes[$id] += filesize($pathToUploads.$item->preview);
					if(is_file($pathToUploads.'retina/'.$item->preview))
						$arraySizes[$id] += filesize($pathToUploads.'retina/'.$item->preview);
					foreach ($item->images as $image) {
						if(is_file($pathToUploads."{$image->block_id}/retina/".$image->filename))
							$arraySizes[$id] += filesize($pathToUploads."{$image->block_id}/retina/".$image->filename);
						if(is_file($pathToUploads."{$image->block_id}/thumbs/".$image->filename))
							$arraySizes[$id] += filesize($pathToUploads."{$image->block_id}/thumbs/".$image->filename);
						
						
					}
				}
			}
		}
		return $arraySizes;
	}

	public function getImageSize($id = 0){
		$size = 0;
		$pathToUploads = YiiBase::getPathOfAlias('webroot').$this->uploadsDirName;
		if(is_numeric($id) && $id > 0){
			$image = Image::model()->findByPk($id);
			if(is_file($pathToUploads."{$image->block_id}/retina/".$image->filename))
				$size += filesize($pathToUploads."{$image->block_id}/retina/".$image->filename);
			if(is_file($pathToUploads."{$image->block_id}/thumbs/".$image->filename))
				$size += filesize($pathToUploads."{$image->block_id}/thumbs/".$image->filename);
		}
		return round($size / (1024*1024), 3)." Мб";
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

	//Update ajax Fields sort
	public function actionSetSort(){
        if(count($_POST['items']) > 0){
            foreach ($_POST['items'] as $item) {
                $row = Block::model()->find('id = :id', array(':id' => $item['id']));
                $row->setAttribute('sort', $item['sort']);
                $row->update();
            }
        }
        Yii::app()->end();
    }
}
