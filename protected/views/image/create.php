<?php
/* @var $this ImageController */
/* @var $model Image */

$block = isset($_GET['block']) ? '?block='.$_GET['block'] : '';

$this->menu=array(
	array('label'=>'Блоки', 'url'=>array('block/index')),
	//array('label'=>'Конструктор', 'url'=>'#builder', 'itemOptions' => array('class' => 'fancybox')),
);
?>
<div class="bg"></div>
<div id="preview_iphone">
	<div class="switch_device">
		<ul>
			<li><a data-device='iphone4s' href="#">iPhone 4 / 4S</a></li>
			<li><a data-device='iphone5s' href="#">iPhone 5S</a></li>
			<li><a data-device='iphone6' href="#">iPhone 6</a></li>
			<li><a class="active" data-device='iphone6plus' href="#">iPhone 6 plus</a></li>
			<li><a data-device='close' href="#">закрыть окно</a></li>
		</ul>
	</div>
	<div class="device iphone6plus">
		<div class="screen">
			<div class="scroll"><img src=""></div>
		</div>
	</div>
	
</div>

<h1>Добавить изображение</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'blocks'=>$blocks, 'templates' => $templates)); ?>