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
				'actions'=>array('delete', 'view', 'PreviewImage', 'create', 'setsort', 'builder', 'getImage', 'createTemplate', 'getTemplate', 'deleteTemplate'),
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

	public function actionDeleteTemplate(){
		header('Content-type: application/json');
		if(!empty($_POST['id']) && $_POST['id'] > 0){
			$template = Template::model()->findByPk($_POST['id']);
			if($template && $template->delete()){
				$result = array();
				$templates = Template::model()->findAll();
				if($templates){
					foreach (Template::model()->findAll() as $value) {
						$result[$value->id] = $value->name;
					}
					echo CJavaScript::jsonEncode($result);
				}else{
					echo CJavaScript::jsonEncode('no');
				}
			}
		}
		Yii::app()->end();
	}

	public function actionGetTemplate(){
		header('Content-type: application/json');
		if(is_numeric($_POST['id']) && $_POST['id'] > 0){
			$template = Template::model()->findByPk($_POST['id']);
			echo $template->json;
		}
		Yii::app()->end();
	}

	public function actionCreateTemplate(){
		$template = new Template;

		if(isset($_POST['Template'])){
			$template->attributes = $_POST['Template'];
			
			if($template->save()){
				$copy_images = array();
				$id = $template->id;

				$uploadsDir =  YiiBase::getPathOfAlias('webroot').$this->uploadsDirName;
				if(!is_dir($uploadsDir)) @mkdir($uploadsDir);

				$templateDir = $uploadsDir.'template/';
				if(!is_dir($templateDir)) @mkdir($templateDir);

				$idDir = $templateDir.$id.'/';
				if(!is_dir($idDir)) @mkdir($idDir);

				$objects = CJSON::decode($template->json);
				$objects = $objects['objects'];
				foreach ($objects as $key => $obj) {
					if($obj['type'] == 'image'){
						$copy_images[] = YiiBase::getPathOfAlias('webroot').str_replace(Yii::app()->request->getBaseUrl(true), '', $obj['src']);
					}
				}
				foreach ($copy_images as $image) {
					$to_file = str_replace('tmp', 'template/'.$id, $image);
					copy($image, $to_file);
				}
				echo 'ok';
			}
		}
		Yii::app()->end();
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

			$model->sort = 1000;
			if($model->save()){
				echo 'ok';
			}
				
		}

		Yii::app()->end();
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate($block)
	{
		$model=new Image;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		$cs = Yii::app()->clientScript;
		$am = Yii::app()->assetManager;

		Yii::app()->getClientScript()->registerCoreScript('jquery');
		Yii::app()->getClientScript()->registerCoreScript( 'jquery.ui' );
		$cs->registerCssFile($cs->getCoreScriptUrl().'/jui/css/base/jquery-ui.css', 'screen');
		//Include fancybox.js for Canvas
		$cs->registerScriptFile($am->publish(Yii::getPathOfAlias('webroot').'/js/fancybox/lib/jquery.mousewheel-3.0.6.pack.js'), CClientScript::POS_HEAD);
		$cs->registerScriptFile($am->publish(Yii::getPathOfAlias('webroot').'/js/fancybox/source/jquery.fancybox.js'), CClientScript::POS_HEAD);
		//Include Fabric.js for Canvas
		$cs->registerScriptFile($am->publish(Yii::getPathOfAlias('webroot').'/js/fabricjs/all.js'), CClientScript::POS_HEAD);
		//Include my js file
		$cs->registerScriptFile($am->publish(Yii::getPathOfAlias('webroot').'/js/builder.js'), CClientScript::POS_END);
		//Include color-picker
		$cs->registerCssFile('/js/colorpicker/css/colorpicker.css', 'screen');
		$cs->registerScriptFile($am->publish(Yii::getPathOfAlias('webroot').'/js/colorpicker/js/colorpicker.js'), CClientScript::POS_HEAD);

		$cs->registerCssFile('/js/fancybox/source/jquery.fancybox.css', 'screen');

		if(isset($_POST['Image']))
		{
			// $model->attributes=$_POST['Image'];
			// $file = CUploadedFile::getInstance($model,'filename');
			// $model->filename = $this->createImage($file->tempName, $file->extensionName, $model->block_id);
			$model->filename = "1";
			$model->block_id = $block;
			
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
			'blocks'=>$blocks,
			'templates' => Template::model()->findAll()
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

	public function actionPreviewImage()
	{

		$uploadsDir =  YiiBase::getPathOfAlias('webroot').$this->uploadsDirName.'previews';
		if(!is_dir($uploadsDir)) @mkdir($uploadsDir);

		$base64img = $_POST['image_base64'];
		$device = $_POST['device'];
		

		$base64img = str_replace("data:image/png;base64,", '', $base64img);
		$data = base64_decode($base64img);

		
		
        
       
       

        $file = $uploadsDir . '/preview.png';//. $format[1];

        file_put_contents($file, $data);

        $thumb = Yii::app()->phpThumb->create($file);

        $size = getimagesize($file);

			// SiteHelper::mpr($size);die();

			$iPhone6PlusWidth = $size[0];
			$iPhone6PlusHeight = $size[1];

			switch ($device) {
				case 'iphone4s':
						$height = round($iPhone6PlusHeight/4/1.32596685);
						$width = round(320/1.33334);
					break;

				case 'iphone5s':
						$height = round($iPhone6PlusHeight/2/2.36453202);
						$width = round(640/2.60162602);
						
					break;

				case 'iphone6':
						$height = round($iPhone6PlusHeight/1.43928036/2.875);
						$width = round(750/2.87356322);
					break;

				case 'iphone6plus':
						$height = round($iPhone6PlusHeight/3.54898336);
						$width = 307;
					break;
				
				default:
						die();
					break;
			}


			
		// echo $width;die();
        $thumb->adaptiveResize($width, $height)->save($file);
        
		
		// $data = file_get_contents($file);

		$path = $file;
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$data = file_get_contents($path);
		echo 'data:image/' . $type . ';base64,' . base64_encode($data);

		// $im = imagecreatefromstring($data);
		// if ($im !== false) {
		//     header('Content-Type: image/png');
		//     imagepng($im);
		//     imagedestroy($im);
		// }
		
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

			$iphone6Dir = $blockDir.'iphone6/';
			if(!is_dir($iphone6Dir)) @mkdir($iphone6Dir);

			$iphone5sDir = $blockDir.'iphone5s/';
			if(!is_dir($iphone5sDir)) @mkdir($iphone5sDir);

			$iphone6plusDir = $blockDir.'iphone6plus/';
			if(!is_dir($iphone6plusDir)) @mkdir($iphone6plusDir);

			$thumbDir = $blockDir.'thumbs/';
			if(!is_dir($thumbDir)) @mkdir($thumbDir);

			$thumb = Yii::app()->phpThumb->create($file);
			$thumbDef = Yii::app()->phpThumb->create($file);
			$thumbRetina = Yii::app()->phpThumb->create($file);
			$thumbiPhone5s = Yii::app()->phpThumb->create($file);
			$thumbiPhone6 = Yii::app()->phpThumb->create($file);
			$thumbiPhone6plus = Yii::app()->phpThumb->create($file);

			$fileName = md5(mktime()).".".$extension;

			$size = getimagesize($file);

			// SiteHelper::mpr($size);die();

			$iPhone6PlusWidth = $size[0];
			$iPhone6PlusHeight = $size[1];

			// $defW = floor($size[0]/2);
			$defH = floor($iPhone6PlusHeight/4);
			$retinaH = floor($iPhone6PlusHeight/2);
			$iphone6H = round($iPhone6PlusHeight/1.43928036);
			$iphone5H = round($iPhone6PlusHeight/1.69014085);

			$thumb->adaptiveResize(100, 100)->save($thumbDir.$fileName);
			$thumbDef->adaptiveResize(320, $defH)->save($blockDir.$fileName);
			$thumbiPhone5s->adaptiveResize(640, $iphone5H)->save($iphone5sDir.$fileName);
			$thumbiPhone6->adaptiveResize(750, $iphone6H)->save($iphone6Dir.$fileName);
			$thumbRetina->adaptiveResize(640, $retinaH)->save($retinaDir.$fileName);

			$thumbiPhone6plus->resize($iPhone6PlusWidth, $iPhone6PlusHeight)->save($iphone6plusDir.$fileName);

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
