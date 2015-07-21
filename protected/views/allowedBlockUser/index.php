<?php
/* @var $this AllowedBlockUserController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Allowed Block Users',
);

$this->menu=array(
	array('label'=>'Create AllowedBlockUser', 'url'=>array('create')),
	array('label'=>'Manage AllowedBlockUser', 'url'=>array('admin')),
);
?>

<h1>Allowed Block Users</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
