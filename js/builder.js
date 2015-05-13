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
	// canvas.width = 200;
	// console.log(canvas);
	// console.log(canvas.scaleX);
	// console.log(canvas.scaleY);
	canvas.setBackgroundColor('#ffffff');

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

	//Change font-size
	var textFontSizeField = jQuery('#text-font-size');
	if (textFontSizeField) {
		textFontSizeField.change(function() {
			var activeObject = canvas.getActiveObject();
			if (activeObject && activeObject.type === 'text') {
				activeObject.setFontSize(parseInt(this.value, 10));
				canvas.renderAll();
			}
		});
	}

	$('#test').click(function(){
		var json = JSON.stringify( canvas.toJSON() );
		console.log(json);

		canvas.clear();
 		canvas.renderAll();

		// parse the data into the canvas
  canvas.loadFromJSON(json);

  // re-render the canvas
  canvas.renderAll();

  // optional
  // canvas.calculateOffset();
	});



	
	jQuery('#add-text').click(function() {
		var text = jQuery("textarea#text").val();
		var color = jQuery("textarea#text").css('color');
		var font = jQuery("#font").val();
		var font_s = jQuery('#text-font-size').val();
		var align = jQuery("#align").val();
		var width_canvas = jQuery('#c_width').val();

	    var addText = new fabric.Text(text, {
			left: (width_canvas / 2),
			top: 100,
			fontFamily: font,
			fontSize: parseInt(font_s, 10),
			angle: 0,
			fill: color,
			scaleX: 1,
			scaleY: 1,
			fontWeight: '',
			originX: 'left',
			hasRotatingPoint: true
	    });
	    var textSample = wrapCanvasText(addText, canvas, width_canvas, 32766);

	    /*if(textSample.height > 32766){ //fucking stop
			textSample = wrapCanvasText(addText, canvas, width_canvas, 32766);
		}*/

	    textSample.top = Math.ceil(textSample.height / 2);
	    //Check size Height text and canvas
	    if(canvas.getHeight() < textSample.height){
	    	canvas.setHeight(textSample.height);
	    	textSample.top = Math.ceil(textSample.height / 2);
	    	jQuery('#c_height').val(Math.ceil(textSample.top));
	    }

	    console.log(canvas.getHeight());

	    canvas.add(textSample);
	    canvas.renderAll();
	    jQuery('#c_width').keyup();
	    //updateComplexity();
 	});

	jQuery('#to-center').click(function() {
		var width_canvas = jQuery('#c_width').val();
		var activeObject = canvas.getActiveObject();
		
		activeObject.set('left', width_canvas/2  );
		
			
			// console.log(activeObject.text);
			// console.log(activeObject.getText());
			canvas.renderAll();
		
	});

	jQuery('#refresh-text').click(function() {
		var width_canvas = jQuery('#c_width').val();
		var activeObject = canvas.getActiveObject();
		var font_s = jQuery('#text-font-size').val();

		if (activeObject && activeObject.type === 'text') {
			activeObject.setText(activeObject.text.replace(/(\r\n|\n|\r)/gm, ""));	
			var tmp = wrapCanvasText(activeObject, canvas, width_canvas);
			

			if(tmp.height > 32766){ //fucking stop
				tmp = wrapCanvasText(activeObject, canvas, width_canvas, 32766);
			}
			//console.log(activeObject.text);
			//someText = someText.replace(/(\r\n|\n|\r)/gm," ");
			//newText.setFontSize(font_s);
			canvas.remove(activeObject);

			//Check size Height text and canvas
		    if(canvas.getHeight() < tmp.height){
		   		canvas.setHeight(tmp.height);
		    	tmp.top = Math.ceil(tmp.height / 2);
		    	jQuery('#c_height').val(Math.ceil(tmp.height));
		    }

			canvas.add(tmp);
			// console.log(activeObject.text);
			// console.log(activeObject.getText());
			canvas.renderAll();
		}
	});

 	jQuery('#c_width, #c_height').on('keyup', function(){
 		var self = jQuery(this);
 		var v = parseInt(self.val(), 10);

 		switch(self.attr('id')){
 			case 'c_width':{
 				if(v > 1080) v = 1080;
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

 	canvas.on('object:scaling', function(e) {
		var activeObject = e.target;
		if (activeObject.type === 'text') {
			
			// var textSample = wrapCanvasText(activeObject, canvas, activeObject.scaleX * activeObject.width);
			// canvas.add(textSample);
			// canvas.renderAll();
		}
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
			//jQuery('#text').css('color', '#' + hex);
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
		jQuery("#text").css('font-family', jQuery(this).val());
		var activeObject = canvas.getActiveObject();
		if (activeObject && activeObject.type === 'text') {
			activeObject.fontFamily = jQuery(this).val();
			canvas.renderAll();
		}
	});

	jQuery("#align").change(function(){
		//jQuery("#text").css('font-family', jQuery(this).val());
		var activeObject = canvas.getActiveObject();
		if (activeObject && activeObject.type === 'text') {
			var value = $(this).val();
        	activeObject.textAlign = value;
	        canvas._adjustPosition && canvas._adjustPosition(activeObject, value === 'justify' ? 'left' : value);
	        canvas.renderAll();
		}
	});

	//Обрабатываем событие клика на текстоый объект
	canvas.on('object:selected', onObjectSelected);

	function onObjectSelected(e){
		var selectedObject = e.target;

		if (selectedObject.type === 'text') {
	      $('textarea#text').val(selectedObject.getText());
	      textFontSizeField.val(selectedObject.get('fontSize'));
	    }
	}

	/// Подгоняем текст под ширину и высоту
	/// t:fabric.IText, canvas:HTMLCanvas, maxW:number, maxH:number
	///
	function wrapCanvasText(t, canvas, maxW, maxH) {

	    if (typeof maxH === "undefined") { maxH = 0; }
	    var words = t.text.split(" ");
	    var formatted = '';

	    // clear newlines
	    var sansBreaks = t.text.replace(/(\r\n|\n|\r)/gm, "");  
	    // calc line height
	    var lineHeight = new fabric.Text(sansBreaks, {         
	        fontFamily: t.fontFamily,
	        fontSize: t.fontSize
	    }).height;

	    // adjust for vertical offset
	    var maxHAdjusted = maxH > 0 ? maxH - lineHeight : 0;                  
	    var context = canvas.getContext("2d");


	    context.font = t.fontSize + "px " + t.fontFamily;
	    var currentLine = "";
	    var breakLineCount = 0;

	    for(var n = 0; n < words.length; n++) {

	        var isNewLine = currentLine == "";
	        var testOverlap = currentLine + ' ' + words[n];

	        // are we over width?
	        var w = context.measureText(testOverlap).width;     

	        if(w < maxW) {  // if not, keep adding words
	            currentLine += words[n] + ' ';
	            formatted += words[n] + ' ';
	        } else {

	            // if this hits, we got a word that need to be hypenated
	            if(isNewLine) { 
	                var wordOverlap = "";

	                // test word length until its over maxW
	                for(var i = 0; i < words[n].length; ++i) {

	                    wordOverlap += words[n].charAt(i);
	                    var withHypeh = wordOverlap + "-";

	                    if(context.measureText(withHypeh).width >= maxW) {
	                        // add hyphen when splitting a word
	                        withHypeh = wordOverlap.substr(0, wordOverlap.length - 2) + "-";
	                        // update current word with remainder
	                        words[n] = words[n].substr(wordOverlap.length - 1, words[n].length);
	                        formatted += withHypeh; // add hypenated word
	                        break;
	                    }
	                }
	            }
	            n--; // restart cycle
	            formatted += '\n';
	            breakLineCount++;
	            currentLine = "";
	        }
	        if(maxHAdjusted > 0 && (breakLineCount * lineHeight) > maxHAdjusted) {
	            // add ... at the end indicating text was cutoff
	            formatted = formatted.substr(0, formatted.length - 3) + "...\n"; 
	            break;
	        }
	    }
	    // get rid of empy newline at the end
	    formatted = formatted.substr(0, formatted.length - 1); 

	    var ret = new fabric.Text(formatted, { // return new text-wrapped text obj
	        left: t.left,
	        top: t.top,
	        fill: t.fill, 
	        fontFamily: t.fontFamily,
	        fontSize: t.fontSize
	    });
	    console.log(ret);
	    return ret;
	}
	
	jQuery('#canvas').data('canvas', canvas);
})(this);

/*align

var textAlignSwitch = document.getElementById('text-align');
  if (textAlignSwitch) {
    activeObjectButtons.push(textAlignSwitch);
    textAlignSwitch.disabled = true;
    textAlignSwitch.onchange = function() {
      var activeObject = canvas.getActiveObject();
      if (activeObject && activeObject.type === 'text') {
        var value = this.value.toLowerCase();
        activeObject.textAlign = value;
        canvas._adjustPosition && canvas._adjustPosition(activeObject, value === 'justify' ? 'left' : value);
        canvas.renderAll();
      }
    };
  }

*/