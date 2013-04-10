<?php
/* @var $this BlockController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Блоки',
);

$this->menu=array(
	array('label'=>'Создать блок', 'url'=>array('create'))
);
?>

<h1>Блоки</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
