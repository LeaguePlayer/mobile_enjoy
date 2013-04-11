<?php
/* @var $this ImageController */
/* @var $model Image */
/* @var $form CActiveForm */
?>
<?php
	//Fonts
	$fonts = array(
		'Arial'=>'Arial',
		'Verdana'=>'Verdana',
		'Georgia'=>'Georgia',
		'Myriad Pro'=>'Myriad Pro',
		'Courier'=>'Courier',
		'Plaster'=>'Plaster'
	);
	//Align text
	$align = array(
		'left'=>'Left',
		'center'=>'Center',
		'right'=>'Right',
		'justify'=>'Justify'
	);
?>

<div id="settings">
	<fieldset>
		<legend>Размеры</legend>
		<?php echo CHtml::label('Ширина', 'c_width');?>
		<?php echo CHtml::textField('c_width', 640);?>
		<?php echo CHtml::label('Высота', 'c_height');?>
		<?php echo CHtml::textField('c_height', 480);?>
		<div>
			<?php echo CHtml::button('Изменить', array('id' => 'set-size'));?>
		</div>
	</fieldset>
	<fieldset>
		<legend>Текст</legend>
		<?php echo CHtml::textarea('text', 'text');?>
		<div>
			<?php echo CHtml::label('Шрифт', 'font');?>
			<?php echo CHtml::dropDownList('font','', $fonts);?>
		</div>
		<div>
			<?php //echo CHtml::label('Выравнивание', 'align');?>
			<?php //echo CHtml::dropDownList('align','', $align);?>
		</div>

		<div>
			<?php echo CHtml::label('Цвет', 'color');?>
			<div id="color-selector"><div></div></div>
		</div>

		<?php echo CHtml::button('Добавить', array('id' => 'add-text'));?>
	</fieldset>
	<fieldset>
		<div>
			<?php echo CHtml::label('Цвет фона', 'color');?>
			<div id="bg-canvas"><div></div></div>

			<?php echo CHtml::label('Загрузить изображение', 'upload');?>
			<?$this->widget('ext.EAjaxUpload.EAjaxUpload',
			array(
		        'id'=>'uploadFile',
		        'config'=>array(
		               'action'=>Yii::app()->createUrl('image/getimage'),
		               'allowedExtensions'=>array("jpg","jpeg","gif","png"),//array("jpg","jpeg","gif","exe","mov" and etc...
		               'sizeLimit'=>4*1024*1024,// maximum file size in bytes
		               'onComplete'=>"js:function(id, fileName, responseJSON){
		               		var c = jQuery('#canvas').data('canvas');
		               		fabric.Image.fromURL('/uploads/tmp/' + fileName, function(img) {
								img.set('left', 100).set('top', 100);
								c.add(img);
								jQuery('#set-size').click();
							});
		               		console.log(c,fileName); 
		               	}"
		              )
			)); ?>
		</div>
	</fieldset>
</div>
<div>
	<?php echo CHtml::button('Удалить элемент', array('id' => 'delete'));?>
</div>
<div id="canvas-container">
	<canvas id="canvas" width="640" height="480"></canvas>
</div>
<?/* <div class="row">
	<?php echo $form->labelEx($model,'block_id'); ?>
	<?php echo $form->dropDownList($model,'block_id', CHtml::listData($blocks, 'id', 'name')); ?>
	<?php echo $form->error($model,'block_id'); ?>
</div> */?>
<div>
	<?php //echo CHtml::button('Сохранить', array('id' => 'save-image'));?>
</div>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'builder-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<?php echo CHtml::hiddenField('back', (!empty($_GET['block']) ? true : false));?>
	<?php echo $form->hiddenField($model,'filename', array('class' => 'file' )); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'block_id'); ?>
		<?php echo $form->dropDownList($model,'block_id', CHtml::listData($blocks, 'id', 'name')); ?>
		<?php echo $form->error($model,'block_id'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Сохранить' : 'Сохранить'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->