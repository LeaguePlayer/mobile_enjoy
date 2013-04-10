<?php
/* @var $this BlockController */
/* @var $model Block */

$this->breadcrumbs=array(
	'Блоки'=>array('index'),
	'Создать блок',
);

$this->menu=array(
	array('label'=>'Все блоки', 'url'=>array('index')),
);
?>

<h1>Создать блок</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>