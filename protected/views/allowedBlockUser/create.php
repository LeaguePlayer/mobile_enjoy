<?php
$this->menu=array(
	// array('label'=>'List AllowedBlockUser', 'url'=>array('index')),
	array('label'=>'Управление доступами блока', 'url'=>array('admin')),
);
?>

<h1>Добавление пользователя к управлению блоком</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'info'=>$info)); ?>