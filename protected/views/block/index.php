<?php
/* @var $this BlockController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Блоки',
);

$this->menu=array(
	array('label'=>'Создать блок', 'url'=>array('create'))
);
?>

<h1>Блоки</h1>
<style>
	#blocks ul{
		list-style: none;
	}
	#blocks li{
		cursor: move;
		height: 60px;
		margin-bottom: 10px;
		border: 1px dashed #aaa;
	}
	#blocks li img{
		padding: 5px;
		background-color: #ccc;
	}
</style>
<div id="blocks">
	<ul>
	<?foreach ($dataProvider->getData() as $data) {?>
		<li data-id="<?=$data->id?>">
			<?
				$img = CHtml::image(CHtml::encode('/uploads/'.$data->preview), '', array('height' => 50, 'align' => 'left', 'style'=>'margin-right: 5px;'));
				echo CHtml::link($img, array('view', 'id'=>$data->id));
			?>
			<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
			<?php echo CHtml::link(CHtml::encode($data->name), array('view', 'id'=>$data->id)); ?>
			<br />

			<b><?php echo CHtml::encode($data->getAttributeLabel('price')); ?>:</b>
			<?php echo CHtml::encode($data->price). " $"; ?>
			<br />

			<b><?php echo CHtml::encode($data->getAttributeLabel('public')); ?>:</b>
			<?php echo ($data->public == 1 ? 'Да' : 'Нет'); ?>
			<br />
		</li>
	<?}?>
	</ul>
</div>
<script type="text/javascript">
$(function() {
    $("#blocks ul").sortable({
    	items: "li",
        stop: function(event, ui){
            //console.log($(ui.item).parent().children());
            var sort = 0;
            var change_items = [];
            // var cat_id = $(ui.item).parent().prev().data('id');
            $(ui.item).parent().children('li').each(function(i){
                $(this).data('sort', ++sort);
                var tmp = new Object();
                tmp.id = $(this).data('id');
                tmp.sort = $(this).data('sort');
                change_items[i] = tmp;
            });
            $.post('<?=$this->createUrl("block/setsort")?>', {
                items: change_items
            });
        }
    });
    $("#blocks ul").disableSelection();
});
</script>
<?php 
// $this->widget('zii.widgets.CListView', array(
// 	'dataProvider'=>$dataProvider,
// 	'itemView'=>'_view',
// )); ?>
