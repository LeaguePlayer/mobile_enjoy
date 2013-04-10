<?php
/* @var $this BlockController */
/* @var $model Block */
/* @var $form CActiveForm */
$prices = array('0.00' => 0.00);
for($i = 0; $i <= 9; $i++){
	$p = ((0.99) + $i);
	$prices[$p.""] = $p."$";
}
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'block-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

	<p class="note">Обязательные поля <span class="required">*</span>.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'price'); ?>
		<?php echo $form->dropDownList($model,'price', $prices); ?>
		<?php echo $form->error($model,'price'); ?>
	</div>

	<div class="row">

		<?php if($model->preview) {?>
		<div class="image">
			<?=CHtml::image($model->preview);?>
			<?php echo CHtml::label('Удалить изображение', 'deleteImage'); ?>
			<?php echo CHtml::checkBox('deleteImage'); ?>
		</div>
		<?}?>
		<?php echo $form->labelEx($model,'preview'); ?>
		<?php echo CHtml::dropDownList('preview_size', 1, $model->getPreviewArray());?>
		<?php echo $form->fileField($model,'preview'); ?>
		<?php echo $form->error($model,'preview'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'public'); ?>
		<?php echo $form->checkBox($model,'public', array('checked' => 'checked')); ?>
		<?php echo $form->error($model,'public'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->