<?php
/* @var $this ImageController */
/* @var $model Image */

$block = isset($_GET['block']) ? '?block='.$_GET['block'] : '';

$this->menu=array(
	array('label'=>'Блоки', 'url'=>array('block/index')),
	//array('label'=>'Конструктор', 'url'=>'#builder', 'itemOptions' => array('class' => 'fancybox')),
);
?>

<h1>Добавить изображение</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'blocks'=>$blocks, 'templates' => $templates)); ?>