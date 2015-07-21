<?php
/* @var $this AllowedBlockUserController */
/* @var $model AllowedBlockUser */

$this->breadcrumbs=array(
	'Allowed Block Users'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List AllowedBlockUser', 'url'=>array('index')),
	array('label'=>'Create AllowedBlockUser', 'url'=>array('create')),
	array('label'=>'View AllowedBlockUser', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage AllowedBlockUser', 'url'=>array('admin')),
);
?>

<h1>Update AllowedBlockUser <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>