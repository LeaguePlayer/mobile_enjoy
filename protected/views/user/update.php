<?php
/* @var $this UserController */
/* @var $model User */
?>

<h1>Обновить информацию <?php echo $model->name; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>