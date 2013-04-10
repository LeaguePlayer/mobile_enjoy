<?php
/* @var $this ImageController */
/* @var $model Image */

$this->menu=array(
	array('label'=>'Блоки', 'url'=>array('block/index')),
	array('label'=>'Загрузить', 'url'=>array('create'))
);
?>

<h1>Создание изображения</h1>

<?php echo $this->renderPartial('_form_builder', array('model'=>$model, 'blocks'=>$blocks)); ?>