<?php
/* @var $this AllowedBlockUserController */
/* @var $model AllowedBlockUser */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'allowed-block-user-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Поля под <span class="required">*</span> обязательны для заполнения.</p>

	<?php echo $form->errorSummary($model); ?>


		<?php echo $form->hiddenField($model,'id_block'); ?>
	

	<div class="row">
		<?php echo $form->labelEx($model,'id_user'); ?>
		<?php echo $form->dropDownList($model,'id_user', $info['users']); ?>
		<?php echo $form->error($model,'id_user'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->