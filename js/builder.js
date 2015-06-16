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
			// console.log('ds');
			if (activeObject && activeObject.type === 'text') {
				console.log(activeObject.top);
				var old_heieght = activeObject.height;
				activeObject.setFontSize(parseInt(this.value, 10));
				var new_height = activeObject.height;

				activeObject.top += parseInt((new_height - old_heieght)/2);
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

	// $('canvas').click(function(){
	// 	console.log('ds');
	// });

	
	jQuery('#add-text').click(function() {
		// jQuery("textarea#text").val('');
		var text = jQuery("textarea#text").val();

		if(!text)
		{
			console.log('empty!');
			$('#text').focus();
			$('#text').animate({borderColor:'#f00'},200, function(){
				$(this).animate({borderColor:'#aaa'},200);
			});
			return false;
		}

		var color = $('#color-selector').data('color');
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

	    	if($('#c_height').val() < 960)
				  			$('.line.bottom').hide();
				  		else
				  			$('.line.bottom').show();
	    }

	    // console.log(canvas.getHeight());

	    if(canvas.getHeight()<$('.fancybox-inner').scrollTop()+15)
	    	textSample.top = canvas.getHeight()-textSample.height;
	    else
	    	textSample.top = $('.fancybox-inner').scrollTop()+textSample.height/2;

	    canvas.add(textSample);
	    canvas.renderAll();
	    jQuery('#c_width').keyup();
	    canvas.setActiveObject(textSample);
	    //updateComplexity();
 	});

	jQuery('#to-center').click(function() {
		var width_canvas = jQuery('#c_width').val();
		var activeObject = canvas.getActiveObject();
		
		activeObject.set('left', width_canvas/2  );
			canvas.renderAll();
	});

	jQuery('#vertical-to-center').click(function() {
		var height_canvas = canvas.height;
		var activeObject = canvas.getActiveObject();
		
		activeObject.set('top', (height_canvas/2)  );
			canvas.renderAll();
	});

	jQuery('#vertical-to-top').click(function() {
		var height_canvas = canvas.height;
		var activeObject = canvas.getActiveObject();
		
		activeObject.set('top', 0+(activeObject.height/2)  );
			canvas.renderAll();
	});

	jQuery('#vertical-to-bottom').click(function() {
		var height_canvas = canvas.height;
		var activeObject = canvas.getActiveObject();
		
		activeObject.set('top', height_canvas-(activeObject.height/2)  );
			canvas.renderAll();
	});

	jQuery('#to-right').click(function() {
		var width_canvas = jQuery('#c_width').val();
		var activeObject = canvas.getActiveObject();
		var K = 2;

		if($('#image_id').val())
		{
			if($('#json_canvas').val())
				K = 2;
			else
				K = 4;
			if (activeObject && activeObject.type === 'text') {
				K = 2;
			}
		}
		
		activeObject.set('left', width_canvas-(activeObject.width/K)  );
			canvas.renderAll();
	});
	jQuery('#to-left').click(function() {
		// var width_canvas = jQuery('#c_width').val();
		var activeObject = canvas.getActiveObject();
		var K = 2;

		if($('#image_id').val())
		{
			if($('#json_canvas').val())
				K = 2;
			else
				K = 4;

			if (activeObject && activeObject.type === 'text') {
				K = 2;
			}
		}
		
		
		activeObject.set('left', 0 + (activeObject.width/K) );
			canvas.renderAll();
	});



	jQuery('#refresh-text').click(function() {
		var width_canvas = jQuery('#c_width').val();
		var activeObject = canvas.getActiveObject();
		var font_s = jQuery('#text-font-size').val();
		var old_top = activeObject.top;
		var old_height = activeObject.height;
		console.log("oldtop : "+old_top);
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
		    	
		    	jQuery('#c_height').val(Math.ceil(tmp.height));

		    	if($('#c_height').val() < 960)
				  			$('.line.bottom').hide();
				  		else
				  			$('.line.bottom').show();
		    }
		    tmp.top = old_top;
			canvas.add(tmp);


			// console.log(activeObject.text);
			// console.log(activeObject.getText());
			canvas.renderAll();
			canvas.setActiveObject(tmp);
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

 		if($('#c_height').val() < 960)
  			$('.line.bottom').hide();
  		else
  			$('.line.bottom').show();


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
			jQuery('#color-selector').data('color', '#' + hex);
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
	canvas.on('selection:cleared', onObjectDeselected);

	function onObjectDeselected(e){
		var selectedObject = e.target;
		var color = "#000000";
		$('#color-selector').data('color', color);
	      $('#color-selector div').css('background-color', color);
	      $('#text').val('');
	      
	      	$('output').fadeOut(250);
	      
	}

	function onObjectSelected(e){
		var selectedObject = e.target;

		if (selectedObject.type === 'text') {
			// $('.font-control').show();
	      $('textarea#text').val(selectedObject.getText());
	      textFontSizeField.val(selectedObject.get('fontSize'));
	      // console.log(selectedObject.fill);
	      $('#color-selector').data('color', selectedObject.fill);
	      $('#color-selector div').css('background-color', selectedObject.fill);

	      // $('#c_height').focus();
	      setTimeout(function() { $('#text').focus() }, 200);
	    }
	}

	$('#text').focus(function(){
		console.log('focused!');
		$('output').fadeIn(250);
	});
	
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
// DOM Ready
$(function() {
 var el, newPoint, newPlace, offset;
 
 // Select all range inputs, watch for change
 $("input[type='range']").change(function() {
 
   // Cache this for efficiency
   el = $(this);
   
   // Measure width of range input
   width = el.width();
   
   // Figure out placement percentage between left and right of input
   newPoint = (el.val() - el.attr("min")) / (el.attr("max") - el.attr("min"));
   
   // Janky value to get pointer to line up better
   offset = -1.3;
   
   // Prevent bubble from going beyond left or right (unsupported browsers)
   if (newPoint < 0) { newPlace = 0; }
   else if (newPoint > 1) { newPlace = width; }
   else { newPlace = width * newPoint + offset; offset -= newPoint; }
   
   // Move bubble
   el
     .next("output")
     .css({
       left: newPlace,
       marginLeft: offset + "%"
     })
     .text(el.val());
 })
 // Fake a change to position bubble at page load
 .trigger('change');
});
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