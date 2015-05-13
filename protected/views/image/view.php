<?php
/* @var $this ImageController */
/* @var $model Image */

$this->menu=array(
	array('label'=>'Создать', 'url'=>array('create?block='.$model->block_id)),
	array('label'=>'Редактировать', 'url'=>array('update?id='.$model->id)),
	array('label'=>'Назад', 'url'=>$_SERVER['HTTP_REFERER']),
	array('label'=>'Удалить', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Вы уверены?'))
);
?>

<h1>Изображение #<?php echo $model->id; ?></h1>

<div id="images">
	<?php echo CHtml::image("/uploads/{$model->block_id}/".CHtml::encode($model->filename));?><br><br>
	<?php echo CHtml::image("/uploads/{$model->block_id}/retina/".CHtml::encode($model->filename));?>
</div>
