<?php
/* @var $this ImageController */
/* @var $model Image */

$this->menu=array(
	array('label'=>'Блоки', 'url'=>array('block/index')),
	array('label'=>'Загрузить', 'url'=>array('create'))
);
if(isset($_GET['block'])){
	$this->menu[] = array('label'=>'Вернуться в блок', 'url'=>array('block/'.$_GET['block']));
}
?>

<h1>Создание изображения</h1>

<?php echo $this->renderPartial('_form_builder', array('model'=>$model, 'blocks'=>$blocks)); ?>