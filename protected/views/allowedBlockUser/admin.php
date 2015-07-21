<?php
$this->menu=array(
	// array('label'=>'List AllowedBlockUser', 'url'=>array('index')),
	array('label'=>'Добавить пользователя к управлению', 'url'=>array('create', 'id_block'=>$model->id_block)),
	array('label'=>'Вернутся в блок', 'url'=>array('/block/'.$model->id_block)),
);
?>

<h1>Управление доступом к блоку</h1>



<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'allowed-block-user-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		// 'id',
		// 'id_block',
		array(
				'value'=>'$data->user->username',
			),
		array(
			'class'=>'CButtonColumn',
			'template'=>"{update} {delete}"
		),
	),
)); ?>
