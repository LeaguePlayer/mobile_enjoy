<?php
/* @var $this BlockController */
/* @var $data Block */
?>

<div class="view" style="overflow: hidden;">
	<?php echo CHtml::image(CHtml::encode($data->preview), '', array('width' => 50, 'align' => 'left'));?>
	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->name), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('price')); ?>:</b>
	<?php echo CHtml::encode($data->price). " $"; ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('public')); ?>:</b>
	<?php echo ($data->public == 1 ? 'Да' : 'Нет'); ?>
	<br />


</div>