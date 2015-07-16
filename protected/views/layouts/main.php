<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css?v=2.04" />

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div class="container" id="page">

	<div id="header">
		<div id="logo"><?php echo CHtml::encode(Yii::app()->name); ?></div>
	</div><!-- header -->

	<div id="mainmenu">
		<?
			$items=array(
				array('label'=>'Описание', 'url'=>array('/page/1')),
				array('label'=>'Блоки', 'url'=>array('/block/')),
				array('label'=>'Руководство', 'url'=>array('/page/2')),
				array('label'=>'Пользователи', 'url'=>array('/user')),
				array('label'=>'Права доступа', 'url'=>array('/auth')),
				
			);
			
			$urls=array(
				array('url'=>'page.view'),
				array('url'=>'block.index'),
				array('url'=>'page.view'),
				array('url'=>'user.index'),
				array('url'=>'auth.index'),
			);

			$menu=array();
			foreach ($urls as $key => $data) {
				if (Yii::app()->user->checkAccess($data['url']))
					$menu[]=$items[$key];
			}
			if (Yii::app()->user->isGuest)
				$menu[]=array('label'=>'Войти', 'url'=>array('/user/login'));
			$menu[]=array('label'=>'Выйти ('.Yii::app()->user->name.')', 'url'=>array('/user/logout'));
		?>
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>$menu
		)); ?>

	</div><!-- mainmenu -->
	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>

	<?php echo $content; ?>

	<div class="clear"></div>

	<div id="footer">

	</div><!-- footer -->

</div><!-- page -->

</body>
</html>
