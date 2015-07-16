<?php

class SiteController extends FrontController
{
	/**
	 * Declares class-based actions.
	 */
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */

	public function init(){
		parent::init();
		$this->layout='//layouts/column2';
		return true;
	}

	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$model = new LoginForm;

		if(Yii::app()->user->isGuest){
			$this->redirect('/user/login');
		}else{
			$this->redirect('/block/index');
		}
	}

	public function actionTest()
	{
		// echo YiiBase::getPathOfAlias('webroot');die();
		// $uploadsDirFile =  YiiBase::getPathOfAlias('webroot').'/preview_files/';
		// 	if(!is_dir($uploadsDirFile)) @mkdir($uploadsDirFile);

		// 	$fileName = "testfile.txt";

		// 	$full_path_to_file = $uploadsDirFile.$fileName;

		// 	if(is_file($full_path_to_file))
		// 	{
		// 		$myfile = fopen($full_path_to_file, "r");
		// 		$exist_string = fgets($myfile);
		// 		fclose($myfile);
		// 		// echo 'exist';
		// 	}

		// 		$myfile = fopen($full_path_to_file, "w");
		// 		$exist_string .= "John123 Doe ";
		// 		fwrite($myfile, $exist_string);
		// 		fclose($myfile);
				// echo 'new';
			
// echo 'd';
// 			ini_set("display_errors", "on");
// 			error_reporting(E_ALL);

// 			echo "<pre>Before: ", ini_get("memory_limit"), "\n";
// 			ini_set("memory_limit", "1G");
// 			echo "After: ", ini_get("memory_limit"), "\n";

// 			$str = null;
// 			while (true)
// 			{
// 			    $str .= str_repeat("1234567890"[mt_rand(0, 9)], 1024*1024 * 512);
// 			}
	}
	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$this->redirect('/user/login');
		// $model = new LoginForm;

		// // if it is ajax validation request
		// if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		// {
		// 	echo CActiveForm::validate($model);
		// 	Yii::app()->end();
		// }

		// // collect user input data
		// if(isset($_POST['LoginForm']))
		// {
		// 	$model->attributes=$_POST['LoginForm'];
		// 	// validate user input and redirect to the previous page if valid
		// 	if($model->validate() && $model->login())
		// 		$this->redirect('/block/index');
		// }
		// // display the login form
		// $this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		$this->redirect('/user/logout');
		// Yii::app()->user->logout();
		// $this->redirect(Yii::app()->homeUrl);
	}
}