<?php
/* @var $this BlockController */
/* @var $model Block */

$this->breadcrumbs=array(
	'Блоки'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Обновить блок',
);

$this->menu=array(
	array('label'=>'Все блоки', 'url'=>array('index')),
	array('label'=>'Создать блок', 'url'=>array('create')),
	array('label'=>'Просмотреть блок', 'url'=>array('view', 'id'=>$model->id)),
);
?>

<h1>Обновить блок - <?php echo $model->name; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>