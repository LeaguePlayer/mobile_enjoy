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
	
	jQuery('#add-text').click(function() {
		var text = jQuery("textarea#text").val();
		var color = jQuery("textarea#text").css('color');
		var font = jQuery("#font").val();
		var align = jQuery("#align").val();
		console.log(font);
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

 	jQuery('#set-size').click(function() {
	    var w = jQuery('#c_width').val();
	    var h = jQuery('#c_height').val();

	    if(w > 640) w = 640;
	    if(w < 100) w = 100;

	    if(h < 100) h = 100;

	    console.log(w,h);
	    canvas.setWidth(w);
	    canvas.setHeight(h);

	    jQuery('#c_width').val(w);
	    jQuery('#c_height').val(h);

		canvas.renderAll();
 	});

 	jQuery('#builder-form').submit(function(){
 		canvas.deactivateAll().renderAll();
 		jQuery(this).find('.file').val(canvas.toDataURL());
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
		jQuery("#text").css('font-family', jQuery(this).val());
	});

	jQuery('#canvas').data('canvas', canvas);
})(this);