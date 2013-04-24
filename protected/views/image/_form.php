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
<div class="form">
<div>
	<a class="builder fancybox" href="#builder">Создать через конструктор</a>
</div>
<div id="builder">
	<div id="canvas-container">
		<canvas id="canvas" width="640" height="740"></canvas>
	</div>
	<div id="settings">
		<form name="settings" method="GET" action="">
		<div class="block">
			<div>
				<?php echo CHtml::label('Блок', 'block_id');?>
				<?php echo CHtml::dropDownList('block_id', $model->block_id, CHtml::listData($blocks, 'id', 'name')); ?>
			</div>
		</div>
		<div class="block">
			<div>
				<?php
					$list =  CHtml::listData($templates, 'id', 'name');
					$list[0] = 'Нет';
					ksort($list, SORT_NUMERIC);
				?>
				<?if($templates){?>
				<?php echo CHtml::label('Загрузить из шаблона', 'template');?>
				<?php echo CHtml::dropDownList('template-check', 0, $list);?>
				<?}?>
				
			</div>
		</div>
		<div class="block">
			<div class="row">
				<?php echo CHtml::label('Ширина', 'c_width');?>
				<?php echo CHtml::textField('c_width', 640);?>
			</div>
			<div class="row">
				<?php echo CHtml::label('Высота', 'c_height');?>
				<?php echo CHtml::textField('c_height', 740);?>
			</div>
			<div class="row">
				<?php echo CHtml::label('Цвет фона', 'color');?>
				<div id="bg-canvas"><div></div></div>
			</div>
			<div class="clear"></div>
		</div>
		<div class="block">
			<?php echo CHtml::textarea('text', 'text');?>
			<div class="row">
				<?php echo CHtml::label('Шрифт', 'font');?>
				<?php echo CHtml::dropDownList('font','', $fonts);?>
			</div>
			<div class="row">
				<?php echo CHtml::label('Цвет текста', 'color');?>
				<div id="color-selector"><div></div></div>
			</div>
			<div class="clear"></div>
			<?php echo CHtml::button('Добавить текст', array('id' => 'add-text'));?>
		</div>
		<div class="block">
			<?php echo CHtml::label('Изображение', 'upload');?>
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
		<div class="block">
			<div>
				<?php echo CHtml::checkbox('template', false, array('id' => 'template'));?>
				<?php echo CHtml::label('Сохранить как шаблон', 'template');?>
				<div class="template_name" style="display: none;">
					<?php echo CHtml::textField('template_name', 'Название');?>
				</div>
			</div>
		</div>
		<div class="block">
			<div class="row">
				<?php echo CHtml::button('Удалить элемент', array('id' => 'delete'));?>
			</div>
			<div class="row">
				<?php echo CHtml::button('Очистить', array('id' => 'clear-canvas'));?>
			</div>
			<div class="clear"></div>
		</div>
		<div class="save-block">
			<div class="row">
				<?php echo CHtml::button('Сохранить', array('id' => 'save-builder'));?>
			</div>
			<div class="clear"></div>
		</div>
		</form>
	</div>
</div>

<h2 style="margin-top: 20px;">Загрузить обычное изображение</h2>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'image-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

	<p class="note">Обязательные поля <span class="required">*</span></p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo CHtml::hiddenField('back', (!empty($_GET['block']) ? true : false));?>

	<div class="row">
		<?php echo $form->labelEx($model,'filename'); ?>
		<?php echo $form->fileField($model,'filename'); ?>
		<?php echo $form->error($model,'filename'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'block_id'); ?>
		<?php echo $form->dropDownList($model,'block_id', CHtml::listData($blocks, 'id', 'name')); ?>
		<?php echo $form->error($model,'block_id'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
	jQuery('.fancybox').fancybox({
		width: 1000,
		type: 'inline',
		afterShow: function(){
			jQuery('#c_width, #c_height').keyup();
		}
	});

	jQuery('#template').on('click', function(){
		if($(this).is(':checked'))
			$(this).parent().find('.template_name').fadeIn();
		else
			$(this).parent().find('.template_name').fadeOut();
	});

	jQuery('#template-check').click(function(){
		var template_id = $(this).find('option:selected').val();
		if(confirm("Все данные на холсте будут потеряны. Продолжить?")){
			$.post('<?=Yii::app()->createUrl('image/getTemplate')?>',{id: template_id}, function(data){
				if(data){
					var c = $('#canvas').data('canvas');
					c.clear();
					c.loadFromJSON(data);
				}

			});
		}
	});

	jQuery('#save-builder').on('click', function(){
		//Save as template
		var c = $('#canvas').data('canvas');
		var parent = $(this).closest('#builder');
		if(parent.find('#template').is(':checked')){
			var name = parent.find('#template_name').val();
			if(name.length == 0){
				parent.find('#template_name').css({backgroundColor: 'red', color: '#fff'});
				return false;
			}
			parent.find('#template_name').css({background: 'none', color: '#000'});
			var data = {template_name: name, template_json: JSON.stringify(c)};
			$.post('<?=Yii::app()->createUrl('image/createTemplate')?>',{Template:{name: name, json: JSON.stringify(c)}}, function(data){
				document.location.reload(true);
			});
			//console.log(JSON.stringify(c));
		}else{
			c.deactivateAll().renderAll();
 			var image = c.toDataURL();
			var block_id = $('#block_id').val();
			
			$.post('<?=Yii::app()->createUrl('image/builder')?>',{Image:{block_id: block_id, filename: image}}, function(data){
				if(data == 'ok'){
					document.location = "<?=Yii::app()->createUrl('block')?>/" + block_id;
				}
				//document.location.reload(true);
			});
			
		}
	});
</script>