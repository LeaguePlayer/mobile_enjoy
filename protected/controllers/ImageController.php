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
				'actions'=>array('delete', 'view', 'create', 'setsort', 'builder', 'getImage'),
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

	public function actionGetImage(){
		Yii::import("ext.EAjaxUpload.qqFileUploader");

        $folder =  YiiBase::getPathOfAlias('webroot').$this->uploadsDirName.'tmp/';// folder for uploaded files
		if(!is_dir($folder)) @mkdir($folder);

        $allowedExtensions = array("jpg","jpeg","gif","png");//array("jpg","jpeg","gif","exe","mov" and etc...
        $sizeLimit = 4 * 1024 * 1024;// maximum file size in bytes
        $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
        $result = $uploader->handleUpload($folder);
        $result=htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        echo $result;// it's array
		Yii::app()->end();
	}

	public function actionBuilder(){
		$model=new Image;

		$cs = Yii::app()->clientScript;
		$am = Yii::app()->assetManager;

		Yii::app()->getClientScript()->registerCoreScript('jquery');
		$cs->registerCssFile($cs->getCoreScriptUrl().'/jui/css/base/jquery-ui.css', CClientScript::POS_HEAD);
		//Include Fabric.js for Canvas
		$cs->registerScriptFile($am->publish(Yii::getPathOfAlias('webroot').'/js/fabricjs/all.js'), CClientScript::POS_HEAD);
		//Include my js file
		$cs->registerScriptFile($am->publish(Yii::getPathOfAlias('webroot').'/js/builder.js'), CClientScript::POS_END);
		//Include jquery.form
		//$cs->registerScriptFile($am->publish(Yii::getPathOfAlias('webroot').'/js/form/jquery.form.js'), CClientScript::POS_HEAD);
		// //Include farbtastic
		$cs->registerCssFile('/js/colorpicker/css/colorpicker.css', CClientScript::POS_HEAD);
		$cs->registerScriptFile($am->publish(Yii::getPathOfAlias('webroot').'/js/colorpicker/js/colorpicker.js'), CClientScript::POS_HEAD);
		// $cs->registerScriptFile($am->publish(Yii::getPathOfAlias('webroot').'/js/colorpicker/js/eye.js'), CClientScript::POS_HEAD);
		// $cs->registerScriptFile($am->publish(Yii::getPathOfAlias('webroot').'/js/colorpicker/js/utils.js'), CClientScript::POS_HEAD);

		if(isset($_POST['Image']))
		{
			$model->attributes=$_POST['Image'];

			if(!empty($_POST['Image']['filename'])){
				$img = $_POST['Image']['filename'];
				$img = str_replace('data:image/png;base64,', '', $img);
				$img = str_replace(' ', '+', $img);
				$data = base64_decode($img);

				$folder = YiiBase::getPathOfAlias('webroot').$this->uploadsDirName.'tmp/';
				if(!is_dir($folder)) @mkdir($folder);

				$file = $folder.'create'.'.png';
				
				if(file_put_contents($file, $data)){
					$model->filename = $this->createBuildImage($file, 'png', $model->block_id);
				}
				//delete tmp files
				$files = glob($folder.'*'); // get all file names
				foreach($files as $file){ // iterate files
				  if(is_file($file))
				    unlink($file); // delete file
				}
			}

			if($model->save()){
				if(!empty($_POST['back']))
					$this->redirect(array('block/view','id'=>$_GET['block']));
				$this->redirect(array('view','id'=>$model->id));
			}
				
		}

		$blocks = Block::model()->findAll();
		if(!empty($_GET['block']))
			$model->block_id = $_GET['block'];

		$this->render('builder',array(
			'model'=>$model,
			'blocks'=>$blocks
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
			$file = CUploadedFile::getInstance($model,'filename');
			$model->filename = $this->createImage($file->tempName, $file->extensionName, $model->block_id);
			
			$model->sort = 1000;
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
	private function createImage($file, $extension, $block){
		if($file){
			$uploadsDir =  YiiBase::getPathOfAlias('webroot').$this->uploadsDirName;
			if(!is_dir($uploadsDir)) @mkdir($uploadsDir);

			$blockDir = $uploadsDir.$block.'/';
			if(!is_dir($blockDir)) @mkdir($blockDir);

			$retinaDir = $blockDir.'retina/';
			if(!is_dir($retinaDir)) @mkdir($retinaDir);

			$thumbDir = $blockDir.'thumbs/';
			if(!is_dir($thumbDir)) @mkdir($thumbDir);

			$thumb = Yii::app()->phpThumb->create($file);
			$thumbDef = Yii::app()->phpThumb->create($file);
			$thumbRetina = Yii::app()->phpThumb->create($file);

			$fileName = md5(mktime()).".".$extension;

			$thumb->adaptiveResize(100, 100)->save($thumbDir.$fileName);
			$thumbDef->resize(320)->save($blockDir.$fileName);
			$thumbRetina->resize(640)->save($retinaDir.$fileName);

			return $fileName;
		}
		return '';
	}

	//Save image
	private function createBuildImage($file, $extension, $block){
		if($file){
			$uploadsDir =  YiiBase::getPathOfAlias('webroot').$this->uploadsDirName;
			if(!is_dir($uploadsDir)) @mkdir($uploadsDir);

			$blockDir = $uploadsDir.$block.'/';
			if(!is_dir($blockDir)) @mkdir($blockDir);

			$retinaDir = $blockDir.'retina/';
			if(!is_dir($retinaDir)) @mkdir($retinaDir);

			$thumbDir = $blockDir.'thumbs/';
			if(!is_dir($thumbDir)) @mkdir($thumbDir);

			$thumb = Yii::app()->phpThumb->create($file);
			$thumbDef = Yii::app()->phpThumb->create($file);
			$thumbRetina = Yii::app()->phpThumb->create($file);

			$fileName = md5(mktime()).".".$extension;

			$size = getimagesize($file);

			$retinaW = $size[0];
			$retinaH = $size[1];

			$defW = floor($size[0]/2);
			$defH = floor($size[1]/2);

			$thumb->adaptiveResize(100, 100)->save($thumbDir.$fileName);
			$thumbDef->resize($defW, $defH)->save($blockDir.$fileName);
			$thumbRetina->resize($retinaW, $retinaH)->save($retinaDir.$fileName);

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
		$block_id = $model->block_id;

		$model->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect('/block/'.$block_id);
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
