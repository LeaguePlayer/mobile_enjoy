<?php
/* @var $this AllowedBlockUserController */
/* @var $model AllowedBlockUser */

$this->breadcrumbs=array(
	'Allowed Block Users'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List AllowedBlockUser', 'url'=>array('index')),
	array('label'=>'Create AllowedBlockUser', 'url'=>array('create')),
	array('label'=>'Update AllowedBlockUser', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete AllowedBlockUser', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage AllowedBlockUser', 'url'=>array('admin')),
);
?>

<h1>View AllowedBlockUser #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'id_block',
		'id_user',
	),
)); ?>
