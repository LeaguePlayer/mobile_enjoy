<?php
/* @var $this BlockController */
/* @var $model Block */

$this->breadcrumbs=array(
	'Блоки'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'Все блоки', 'url'=>array('index')),
	array('label'=>'Создать блок', 'url'=>array('create')),
	array('label'=>'Обновить блок', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Удалить блок', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?'))
);
?>

<h1>Блок - <?php echo $model->name; ?></h1>

<div id="block">
	<div>
		<?php echo CHtml::image('/uploads/'.$model->preview, "", array());?>
		<?php echo CHtml::image('/uploads/retina/'.$model->preview, "", array());?>
		<span><?php echo $model->getAttributeLabel('price').' : '.$model->price.' $';?></span>
		<div style="clear: both;"></div>
	</div>
	<div>
		<h2>Изображения в блоке</h2>
		<div class="images">
			<?php if($model->images){
				foreach ($model->images as $value) {
					echo "<div class='image' data-id='{$value->id}'>".CHtml::link(CHtml::image("/uploads/{$model->id}/thumbs/{$value->filename}"), array('image/view', 'id'=>$value->id ))."</div>";
				}
			}?>
			<div class="add"><a href="<?=$this->createUrl('image/create', array('block' => $model->id))?>">+</a></div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(function() {
    $( ".images" ).sortable({
    	items: ".image",
        stop: function(event, ui){
            //console.log($(ui.item).parent().children());
            var sort = 0;
            var change_items = [];
            // var cat_id = $(ui.item).parent().prev().data('id');
            $(ui.item).parent().children('.image').each(function(i){
                $(this).data('sort', ++sort);
                var tmp = new Object();
                tmp.id = $(this).data('id');
                tmp.sort = $(this).data('sort');
                change_items[i] = tmp;
            });
            $.post('<?=$this->createUrl("image/setsort")?>', {
                items: change_items
            });
        }
    });
    $( ".images" ).disableSelection();
});
</script>