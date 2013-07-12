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
	//Font-sizes
	$font_sizes = array();
	for ($i=5; $i < 16; $i++) { 
		$font_sizes[$i * 2] = ($i * 2).'px';
	}
?>
<div class="form">
<div>
	<a class="builder fancybox" href="#builder">Создать через конструктор</a>
</div>
<div id="builder">
	<div id="canvas-container" style="min-height: 100px;">
		<canvas id="canvas" width="640" height="960"></canvas>
	</div>
	<div id="settings">
		<form name="settings" method="GET" action="">
		<div class="block" style="display: none;">
			<div>
				<?php echo CHtml::label('Блок', 'block_id');?>
				<?php echo CHtml::dropDownList('block_id', $model->block_id, CHtml::listData($blocks, 'id', 'name')); ?>
			</div>
		</div>
		<?if($templates){?>
		<div class="block">
			<div>
				<?php
					$list =  CHtml::listData($templates, 'id', 'name');
					$delete = CHtml::listData($templates, 'id', 'name');
					$list[0] = 'Нет';
					ksort($list, SORT_NUMERIC);
				?>
				
				<?php echo CHtml::label('Загрузить из шаблона', 'template');?>
				<?php echo CHtml::dropDownList('template-check', 0, $list);?>
				<div style="margin: 5px 0;">
					<?php echo CHtml::dropDownList('template-del', 0, $delete);?>
					<a href="#delete-templte" id="del-template">Удалить шаблон</a>
				</div>
			</div>
		</div>
		<?}?>
		<div class="block">
			<div class="row">
				<?php echo CHtml::label('Ширина', 'c_width');?>
				<?php echo CHtml::textField('c_width', 640);?>
			</div>
			<div class="row">
				<?php echo CHtml::label('Высота', 'c_height');?>
				<?php echo CHtml::textField('c_height', 960);?>
			</div>
			<div class="clear"></div>
			<div>
				<span style="font-size:11px;">* при размерах 640 по ширине и 960 по высоте - изображение для устройств до 4S отображается на полный экран.</span>
			</div>
			<div class="clear"></div>			
		</div>
		<div class="block">
			<div class="row" style="width: 100px;">
				<?php echo CHtml::label('Цвет фона', 'color');?>
				<div id="bg-canvas"><div></div></div>
			</div>
			<div class="row" style="width: 100px;">
				<?php echo CHtml::label('Цвет текста', 'color');?>
				<div id="color-selector"><div></div></div>
			</div>
			<div class="row" style="width: 110px;">
				<?php echo CHtml::label('Шрифт', 'font');?>
				<?php echo CHtml::dropDownList('font','', $fonts);?>
			</div>
			<div class="clear"></div>
			<?php echo CHtml::textarea('text', 'text');?>
			<div class="row">
				<?php echo CHtml::label('Размер шрифта', 'font-size');?>
				<input id="text-font-size" type="range" min="1" step="1" max="130" value="22" />
			</div>
			<div class="row">
				<?php echo CHtml::button('Добавить текст', array('id' => 'add-text'));?>
				<?php echo CHtml::button('Подстроить', array('id' => 'refresh-text'));?>
			</div>
			<div class="clear"></div>
		</div>
		<div class="block">
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
	
	$( "#canvas-container" ).resizable({
		handles: "s",
		resize: function(event, ui){
			var c = $(this).find('canvas').data('canvas');
			c.setHeight($(this).height());
			$('#c_height').val($(this).height());
			c.renderAll();
		}
	});

	jQuery('.fancybox').fancybox({
		width: 1000,
		type: 'inline',
		afterShow: function(){
			jQuery('#c_width, #c_height').keyup();
			var fi = jQuery('.fancybox-inner');
			var s = fi.find('#settings');
			//hot fix -- !
			if($(window).height() >= 680){
				fi.scroll(function(){
					s.animate({top: fi.scrollTop()}, {duration: 500, queue:false});
				});
			}
			
		}
	});

	jQuery('#template').on('click', function(){
		if($(this).is(':checked'))
			$(this).parent().find('.template_name').fadeIn();
		else
			$(this).parent().find('.template_name').fadeOut();
	});

	jQuery('#del-template').on('click', function(){
		var template_id = $('#template-del').find('option:selected').val();
		var name = $('#template-del').find('option:selected').text();
		if(confirm("Вы действительно хотите удалить шаблон - "+name+"?")){
			$.post('<?=Yii::app()->createUrl('image/deleteTemplate')?>',{id: template_id}, function(data){
				if(data){
					if(data == 'no'){
						$('#template-del').parents('.block').hide();
						return false;
					}
					var select = "";
					jQuery.each(data, function(index, value){
						select += '<option value="'+index+'">'+value+'</option>';
					});
					console.log(select);
					$('#template-del').html(select);
					select = '<option value="0">Нет</option>' + select;
					$('#template-check').html(select);
					//var objects = jQuery.parseJSON('{"name":"John"}');
				}
			});
		}
	});

	jQuery('#template-check').change(function(){
		var template_id = $(this).find('option:selected').val();
		var c = $('#canvas').data('canvas');
		//console.log(template_id);
		if(template_id === 0){
			c.clear();
			c.renderAll();
			return false;
		}
		if(confirm("Все данные на холсте будут потеряны. Продолжить?")){
			$.post('<?=Yii::app()->createUrl('image/getTemplate')?>',{id: template_id}, function(data){
				if(data){
					c.clear();
					c.loadFromDatalessJSON(data);
					c.renderAll();
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
				//document.location.reload(true);
			});
			//console.log(JSON.stringify(c));
		}

		c.deactivateAll().renderAll();
		var image = c.toDataURL();
		var block_id = $('#block_id').val();
		
		$.post('<?=Yii::app()->createUrl('image/builder')?>',{Image:{block_id: block_id, filename: image}}, function(data){
			if(data == 'ok'){
				document.location = "<?=Yii::app()->createUrl('block')?>/" + block_id;
			}
			//document.location.reload(true);
		});
	});
</script>