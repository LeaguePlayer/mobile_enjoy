(function(global){

	"use strict";

	function pad(str, length) {
		while (str.length < length) {
		  	str = '0' + str;
		}
		return str;
	};

	var getRandomInt = fabric.util.getRandomInt;
	function getRandomColor() {
		return (
			pad(getRandomInt(0, 255).toString(16), 2) +
			pad(getRandomInt(0, 255).toString(16), 2) +
			pad(getRandomInt(0, 255).toString(16), 2)
		);
	}

	function updateComplexity() {
	    setTimeout(function(){
	      	document.getElementById('complexity').childNodes[1].innerHTML = ' ' + canvas.complexity();
	    }, 100);
	  }

	var canvas = global.canvas = new fabric.Canvas('canvas');
	canvas.setBackgroundColor('#ffffff');

	//Обрабатываем событие клика на текстоый объект
	canvas.on('object:selected', onObjectSelected);

	function onObjectSelected(e){
		var selectedObject = e.target;

		if (selectedObject.type === 'text') {
	      $('textarea#text').val(selectedObject.getText());
	    }
	}

	var textEl = $('textarea#text');
	if (textEl) {
		textEl.focus(function(e){
			var activeObject = canvas.getActiveObject();
			if (activeObject && activeObject.type === 'text') {
				$(this).val(activeObject.text);
			}
		});
		textEl.keyup(function(e){
			var activeObject = canvas.getActiveObject();
			if (activeObject) {
				if (!$(this).val()) {
					canvas.discardActiveObject();
				}
				else {
					activeObject.setText($(this).val());
				}
				canvas.renderAll();
			}
		});
	}
	
	jQuery('#add-text').click(function() {
		var text = jQuery("textarea#text").val();
		var color = jQuery("textarea#text").css('color');
		var font = jQuery("#font").val();
		var align = jQuery("#align").val();

	    var textSample = new fabric.Text(text, {
			left: 30,
			top: 30,
			fontFamily: font,
			angle: 0,
			fill: color,
			scaleX: 1,
			scaleY: 1,
			fontWeight: '',
			originX: 'left',
			hasRotatingPoint: true
	    });
	    canvas.add(textSample);
	    canvas.renderAll();
	    jQuery('#set-size').click();
	    //updateComplexity();
 	});

 	jQuery('#c_width, #c_height').on('keyup', function(){
 		var self = jQuery(this);
 		var v = parseInt(self.val(), 10);

 		switch(self.attr('id')){
 			case 'c_width':{
 				if(v > 640) v = 640;
 				if(v < 100) v = 100;
 				canvas.setWidth(v);
 				break;
 			}
 			case 'c_height':{
 				if(v < 100) v = 100;
 				canvas.setHeight(v);
 				break;
 			}
 		}
	    canvas.renderAll();
 	});

 	jQuery('#builder-form').submit(function(){
 		canvas.deactivateAll().renderAll();
 		jQuery(this).find('.file').val(canvas.toDataURL());
 	});

 	jQuery('#clear-canvas').click(function() {
 		canvas.clear();
 		canvas.renderAll();
 	});

 	jQuery('#delete').click(function() {
		var activeObject = canvas.getActiveObject(),
        activeGroup = canvas.getActiveGroup();
	    if (activeObject) {
	      	canvas.remove(activeObject);
	    }
	    else if (activeGroup) {
			var objectsInGroup = activeGroup.getObjects();
			canvas.discardActiveGroup();
			objectsInGroup.forEach(function(object) {
				canvas.remove(object);
			});
	    }
	    canvas.renderAll();
 	});

 	//color text
	jQuery('#color-selector').ColorPicker({
		color: '#000000',
		onShow: function (colpkr) {
			jQuery(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			jQuery(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			jQuery('#color-selector div').css('backgroundColor', '#' + hex);
			jQuery('#text').css('color', '#' + hex);
			var activeObject = canvas.getActiveObject();
			if (activeObject && activeObject.type === 'text') {
				activeObject.setColor('#' + hex);
				canvas.renderAll();
			}
		}
	});
	//background canvas
	jQuery('#bg-canvas').ColorPicker({
		color: '#ffffff',
		onShow: function (colpkr) {
			jQuery(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			jQuery(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			jQuery('#bg-canvas div').css('backgroundColor', '#' + hex);
			canvas.setBackgroundColor('#' + hex);
			canvas.renderAll();
		}
	});

	jQuery("#font").change(function(){
		//jQuery("#text").css('font-family', jQuery(this).val());
		var activeObject = canvas.getActiveObject();
		if (activeObject && activeObject.type === 'text') {
			activeObject.fontFamily = jQuery(this).val();
			canvas.renderAll();
		}
	});

	jQuery('#canvas').data('canvas', canvas);
})(this);