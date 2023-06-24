/* $Id: sitestoreproduct_zoom.js 2013-09-02 00:00:00Z SocialEngineAddOns Copyright 2012-2013 BigStep Technologies Pvt. Ltd. $ */

var SitestoreproductZoom = new Class({
	Implements: [Options],
	options: {
		'classes': {
			sitestoreproductMagnifyRoll : 'sitestoreproduct_magnify_search',
      sitestoreproductMagnifyRollImage : 'sitestoreproduct_magnify_image',
			sitestoreproductMagnifiedImage : 'sitestoreproduct_magnify'
		},
    'notShowImageInLightBox' : true
	},

	initialize:  function(options) {
		this.setOptions(options);		
		var thisObj = this;

    var parentImage = $('sitestoreproduct_product_image_zoom');
    var originalImage = parentImage;
  	var originalImageSize = $(originalImage).getSize();
      
    var largeImageHref = parentImage.get('href');
    var newPos = parentImage.getPosition( originalImage );

    parentImage.setStyles({'position':'relative','display':'block'});
    
    if( thisObj.options.notShowImageInLightBox ) {
      parentImage.addEvent('click', function(e){
        e.stop();
      });
      $('product_profile_picture').addEvent('click', function(e){
        e.stop();
      });
    }
			
    // MAKING THE OUTPUT MAGNIFIED IMAGE
    var largeImage = thisObj.makeImage( largeImageHref, { 'onload' : function(){
				
    var imageCoordinates = $('profile_photo').getCoordinates();

      // IMAGE PORTION INSIDE THE ROLL
      var rollImage = new Element('div', {'class':thisObj.options.classes.sitestoreproductMagnifyRollImage, 'styles':{ 
          'width' : originalImageSize.x, 
          'height': originalImageSize.y, 
          'top'   : newPos.x,
          'left'  : newPos.y
      }});
    
      rollImage.inject( parentImage );

      var searchMagnify = new Element('div', {'class':thisObj.options.classes.sitestoreproductMagnifyRoll} ).inject( rollImage );
      searchMagnifySize = searchMagnify.getSize();
      searchMagnify.fade('hide');
      
      var sitestoreproductMagnifyImageHeight = 380;
      var sitestoreproductMagnifyImageWidth = 480;

      var sitestoreproductMagnifyImage = new Element('div', {'class':thisObj.options.classes.sitestoreproductMagnifiedImage, 'styles':{ 
          'background-image': 'url(' + (largeImageHref) + ')',
          'width' : sitestoreproductMagnifyImageWidth ,
          'height' : sitestoreproductMagnifyImageHeight,
          'top' : imageCoordinates.top,
          'left' : imageCoordinates.right
      }});

      sitestoreproductMagnifyImage.inject( document.body );

      var sitestoreproductMagnifyImageSize  = sitestoreproductMagnifyImage.getSize(),
        searchMagnifySize  = searchMagnify.getSize(),
        rollImagePos = rollImage.getPosition();

      rollImage.addEvent('mouseover', function(){

        $("product_profile_magnify_message").style.display = 'none';
        var windowSize = window.getSize();
        rollImagePos = rollImage.getPosition();
        if( Math.abs(originalImageSize.x - ( windowSize.x - rollImagePos.x )) > searchMagnifySize.x ){
        }else{
          sitestoreproductMagnifyImage.setStyle('left',  0 - sitestoreproductMagnifyImageSize.x );
        }

        searchMagnify.fade('in');
        sitestoreproductMagnifyImage.fade('in');
      });

      rollImage.addEvent('mousemove', function(ev){
        var posX = (ev.page.x - searchMagnifySize.x / 2) - rollImagePos.x;
        var posY = (ev.page.y - searchMagnifySize.y / 2) - rollImagePos.y;
        searchMagnify.setStyle('top', posY.limit(0, originalImageSize.y - searchMagnifySize.y ) );
        searchMagnify.setStyle('left', posX.limit(0, originalImageSize.x - searchMagnifySize.x ) );
        sitestoreproductMagnifyImage.setStyle('background-position', ((posX / (originalImageSize.x - searchMagnifySize.x)) * 100).limit(0,100) + '% ' + ((posY / (originalImageSize.y - searchMagnifySize.y)) * 100).limit(0,100) + '%' );
      });
      
      rollImage.removeEvent('mouseout').addEvent('mouseout', function(){
        searchMagnify.fade('out');
        sitestoreproductMagnifyImage.fade('out');
        $("product_profile_magnify_message").style.display = 'block';
      });

      rollImage.fireEvent('mouseout');
    }});
	},

	makeImage: function(source, properties){
		properties = $merge({
			onload: $empty,
			onabort: $empty,
			onerror: $empty
		}, properties);
		var image = new Image();
		var element = document.id(image) || new Element('img');
		['load', 'abort', 'error'].each(function(name){
			var type = 'on' + name;
			var cap = name.capitalize();
			if (properties['on' + cap]) properties[type] = properties['on' + cap];
			var event = properties[type];
			delete properties[type];
			image[type] = function(){
				if (!image) return;
				if (!element.parentNode){
					element.width = image.width;
					element.height = image.height;
				}
				image = image.onload = image.onabort = image.onerror = null;
				event.delay(1, element, element);
				element.fireEvent(name, element, 1);
			};
		});
		image.src = element.src = source;
		if (image && image.complete) image.onload.delay(1);
		return element.set(properties);
	}
});