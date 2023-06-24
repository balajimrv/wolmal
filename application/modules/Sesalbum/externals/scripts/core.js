/* $Id: core.js  2015-6-16 00:00:000 SocialEngineSolutions $ */
(function() { // START NAMESPACE
var $ = 'id' in document ? document.id : window.$;
en4.album = {
  composer : false,
  getComposer : function(){
    if( !this.composer ){
      this.composer = new en4.album.acompose();
    }
    return this.composer;
  },
  rotate : function(photo_id, angle) {
    request = new Request.JSON({
      url : en4.core.baseUrl + 'sesalbum/photo/rotate',
      data : {
        format : 'json',
        photo_id : photo_id,
        angle : angle
      },
      onComplete: function(response) {
        // Check status
        if( $type(response) == 'object' &&
            $type(response.status) &&
            response.status == false ) {
          en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onClick="Smoothbox.close()">Close</button>');
          return;
        } else if( $type(response) != 'object' ||
          !$type(response.status) ) {
          en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onClick="Smoothbox.close()">Close</button>');
          return;
        }

        // Ok, let's refresh the page I guess
        window.location.reload(true);
      }
    });
    request.send();
    return request;
  },

  flip : function(photo_id, direction) {
    request = new Request.JSON({
      url : en4.core.baseUrl + 'sesalbum/photo/flip',
      data : {
        format : 'json',
        photo_id : photo_id,
        direction : direction
      },
      onComplete: function(response) {
        // Check status
        if( $type(response) == 'object' &&
            $type(response.status) &&
            response.status == false ) {
          en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onClick="Smoothbox.close()">Close</button>');
          return;
        } else if( $type(response) != 'object' ||
          !$type(response.status) ) {
          en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onClick="Smoothbox.close()">Close</button>');
          return;
        }

        // Ok, let's refresh the page I guess
        window.location.reload(true);
      }
    });
    request.send();
    return request;
  },

  crop : function(photo_id, x, y, w, h) {
    if( $type(x) == 'object' ) {
      h = x.h;
      w = x.w;
      y = x.y;
      x = x.x;
    }
    request = new Request.JSON({
      url : en4.core.baseUrl + 'sesalbum/photo/crop',
      data : {
        format : 'json',
        photo_id : photo_id,
        x : x,
        y : y,
        w : w,
        h : h
      },
      onComplete: function(response) {
        // Check status
        if( $type(response) == 'object' &&
            $type(response.status) &&
            response.status == false ) {
          en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onClick="Smoothbox.close()">Close</button>');
          return;
        } else if( $type(response) != 'object' ||
          !$type(response.status) ) {
          en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onClick="Smoothbox.close()">Close</button>');
          return;
        }

        // Ok, let's refresh the page I guess
        window.location.reload(true);
      }
    });
    request.send();
    return request;
  }

};

en4.album.acompose = new Class({

  Extends : en4.activity.compose.icompose,

  name : 'photo',

  active : false,

  options : {},

  frame : false,

  photo_id : false,

  initialize : function(element, options){
    if( !element ) element = $('activity-compose-photo');
    this.parent(element, options);
  },
  
  activate : function(){
    this.parent();
    this.element.style.display = '';
    $('activity-compose-photo-input').style.display = '';
    $('activity-compose-photo-loading').style.display = 'none';
    $('activity-compose-photo-preview').style.display = 'none';
    $('activity-form').addEvent('beforesubmit', this.checkSubmit.bind(this));
    this.active = true;

    // @todo this is a hack
    $('activity-post-submit').style.display = 'none';
  },

  deactivate : function(){
    if( !this.active ) return;
    this.active = false
    this.photo_id = false;
    if( this.frame ) this.frame.destroy();
    this.frame = false;
    $('activity-compose-photo-preview').empty();
    $('activity-compose-photo-input').style.display = '';
    this.element.style.display = 'none';
    $('activity-form').removeEvent('submit', this.checkSubmit.bind(this));;

    // @todo this is a hack
    $('activity-post-submit').style.display = 'block';
    $('activity-compose-photo-activate').style.display = '';
    $('activity-compose-link-activate').style.display = '';
  },

  process : function(){
    if( this.photo_id ) return;
    
    if( !this.frame ){
      this.frame = new IFrame({
        src : 'about:blank',
        name : 'albumComposeFrame',
        styles : {
          display : 'none'
        }
      });
      this.frame.inject(this.element);
    }
    $('activity-compose-photo-input').style.display = 'none';
    $('activity-compose-photo-loading').style.display = '';
    $('activity-compose-photo-form').target = 'albumComposeFrame';
    $('activity-compose-photo-form').submit();
  },

  processResponse : function(responseObject){
		console.log(responseObject);
    if( this.photo_id ) return;
    
    (new Element('img', {
      src : responseObject.src,
      styles : {
        //'max-width' : '100px'
      }
    })).inject($('activity-compose-photo-preview'));
    $('activity-compose-photo-loading').style.display = 'none';
    $('activity-compose-photo-preview').style.display = '';
    this.photo_id = responseObject.photo_id;

    // @todo this is a hack
    $('activity-post-submit').style.display = 'block';
    $('activity-compose-photo-activate').style.display = 'none';
    $('activity-compose-link-activate').style.display = 'none';
  },

  checkSubmit : function(event)
  {
    if( this.active && this.photo_id )
    {
      //event.stop();
      $('activity-form').attachment_type.value = 'album_photo';
      $('activity-form').attachment_id.value = this.photo_id;
    }
  }
});
})(); // END NAMESPACE
//MAP CODE 
//initialize default values
var map;
var infowindow;
var marker;
var mapLoad = true;
function initializeSesAlbumMap() {
  var mapOptions = {
    center: new google.maps.LatLng(-33.8688, 151.2195),
    zoom: 17
  };
   map = new google.maps.Map(document.getElementById('map-canvas'),
    mapOptions);

  var input =document.getElementById('locationSes');

  var autocomplete = new google.maps.places.Autocomplete(input);
  autocomplete.bindTo('bounds', map);

   infowindow = new google.maps.InfoWindow();
   marker = new google.maps.Marker({
    map: map,
    anchorPoint: new google.maps.Point(0, -29)
  });

  google.maps.event.addListener(autocomplete, 'place_changed', function() {
    infowindow.close();
    marker.setVisible(false);
    var place = autocomplete.getPlace();
    if (!place.geometry) {
      return;
    }

    // If the place has a geometry, then present it on a map.
    if (place.geometry.viewport) {
      map.fitBounds(place.geometry.viewport);
    } else {
      map.setCenter(place.geometry.location);
      map.setZoom(17);  // Why 17? Because it looks good.
    }
    marker.setIcon(/** @type {google.maps.Icon} */({
      url: place.icon,
      size: new google.maps.Size(71, 71),
      origin: new google.maps.Point(0, 0),
      anchor: new google.maps.Point(17, 34),
      scaledSize: new google.maps.Size(35, 35)
    }));
		document.getElementById('lngSes').value = place.geometry.location.lng();
		document.getElementById('latSes').value = place.geometry.location.lat();
    marker.setPosition(place.geometry.location);
    marker.setVisible(true);

    var address = '';
    if (place.address_components) {
      address = [
        (place.address_components[0] && place.address_components[0].short_name || ''),
        (place.address_components[1] && place.address_components[1].short_name || ''),
        (place.address_components[2] && place.address_components[2].short_name || '')
      ].join(' ');
    }
    infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
    infowindow.open(map, marker);
		return false;
  }); 
	google.maps.event.addDomListener(window, 'load', initializeSesAlbumMap);
}
function editSetMarkerOnMap(){
	geocoder = new google.maps.Geocoder();
	var address = trim(document.getElementById('ses_location_data').innerHTML);
	var lat = document.getElementById('lngSes').value;
	var lng = document.getElementById('latSes').value;
  var latlng = new google.maps.LatLng(lat, lng);
    geocoder.geocode({'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
          map.setZoom(17);
          marker = new google.maps.Marker({
              position: latlng,
              map: map
          });
          infowindow.setContent(results[0].formatted_address);
          infowindow.open(map, marker);
      } else {
        //console.log("Map failed to show location due to: " + status);
      }
    });

}
//list page map 
function initializeSesAlbumMapList() {
if(mapLoad){
  var mapOptions = {
    center: new google.maps.LatLng(-33.8688, 151.2195),
    zoom: 17
  };
   map = new google.maps.Map(document.getElementById('map-canvas-list'),
    mapOptions);
}
if(sesJqueryObject('#locationSes').length)
	var input = document.getElementById('locationSes');
else
  var input =document.getElementById('locationSesList');

  var autocomplete = new google.maps.places.Autocomplete(input);
if(mapLoad)
  autocomplete.bindTo('bounds', map);

if(mapLoad){
   infowindow = new google.maps.InfoWindow();
   marker = new google.maps.Marker({
    map: map,
    anchorPoint: new google.maps.Point(0, -29)
  });
}
  google.maps.event.addListener(autocomplete, 'place_changed', function() {
	
	if(mapLoad){
    infowindow.close();
    marker.setVisible(false);
	}
    var place = autocomplete.getPlace();
    if (!place.geometry) {
      return;
    }
	if(mapLoad){
    // If the place has a geometry, then present it on a map.
    if (place.geometry.viewport) {
      map.fitBounds(place.geometry.viewport);
    } else {
      map.setCenter(place.geometry.location);
      map.setZoom(17);  // Why 17? Because it looks good.
    }
    marker.setIcon(/** @type {google.maps.Icon} */({
      url: place.icon,
      size: new google.maps.Size(71, 71),
      origin: new google.maps.Point(0, 0),
      anchor: new google.maps.Point(17, 34),
      scaledSize: new google.maps.Size(35, 35)
    }));
	}
	if(sesJqueryObject('#locationSes').length){
		document.getElementById('lngSes').value = place.geometry.location.lng();
		document.getElementById('latSes').value = place.geometry.location.lat();
	}else{
		document.getElementById('lngSesList').value = place.geometry.location.lng();
		document.getElementById('latSesList').value = place.geometry.location.lat();
	}
if(mapLoad){
    marker.setPosition(place.geometry.location);
    marker.setVisible(true);
}
    var address = '';
    if (place.address_components) {
      address = [
        (place.address_components[0] && place.address_components[0].short_name || ''),
        (place.address_components[1] && place.address_components[1].short_name || ''),
        (place.address_components[2] && place.address_components[2].short_name || '')
      ].join(' ');
    }
  if(mapLoad){
	  infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
    infowindow.open(map, marker);
		return false;
	}
	}); 
	if(mapLoad){
		google.maps.event.addDomListener(window, 'load', initializeSesAlbumMapList);
	}
}

function editSetMarkerOnMapList(){
	geocoder = new google.maps.Geocoder();
if(mapLoad){
	if(document.getElementById('ses_location_data_list'))
		var address = trim(document.getElementById('ses_location_data_list').innerHTML);
}else{
	if(document.getElementById('locationSesList'))
		var address = trim(document.getElementById('locationSesList').innerHTML);	
}
	var lat = document.getElementById('lngSesList').value;
	var lng = document.getElementById('latSesList').value;
  var latlng = new google.maps.LatLng(lat, lng);
    geocoder.geocode({'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
          map.setCenter(results[0].geometry.location);
          marker = new google.maps.Marker({
              position: results[0].geometry.location,
              map: map
          });
          infowindow.setContent(results[0].formatted_address);
          infowindow.open(map, marker);
      } else {
        //console.log("Map failed to show location due to: " + status);
      }
    });
}
sesJqueryObject(document).on('click','.smoothboxOpen',function(){
	var url = sesJqueryObject(this).attr('href');
	openURLinSmoothBox(url);
	return false;
});
function openURLinSmoothBox(openURLsmoothbox){
	Smoothbox.open(openURLsmoothbox);
	parent.Smoothbox.close;
	return false;
}
// ALBUM LIKE ON ALBUM LISTINGS
sesJqueryObject(document).on('click','.sesalbum_albumlike',function(){
		var data = sesJqueryObject(this).attr('data-src');
		var objectDocument = this;
		 (new Request.JSON({
			url : en4.core.baseUrl + 'sesalbum/album/like/album_id/'+data,
			data : {
				format : 'json',
				type : 'album',
				id : data,
			},
		 onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
			 var data = JSON.parse(responseElements);
			 if(data.status == 'false'){
					 if(data.error == 'Login')
							alert(en4.core.language.translate('Please login'));
					 else
							alert(en4.core.language.translate('Invalid argument supplied.'));
			 }else{
					 if(data.condition == 'increment'){
              sesJqueryObject(objectDocument).addClass('button_active');
							sesJqueryObject(objectDocument).find('span').html(data.like_count);
							showTooltip(10,10,'<i class="fa fa-thumbs-up"></i><span>'+(en4.core.language.translate("Album liked successfully"))+'</span>', 'sesbasic_liked_notification');
					 }else{
              sesJqueryObject(objectDocument).removeClass('button_active');
							sesJqueryObject(objectDocument).find('span').html(data.like_count);
							showTooltip(10,10,'<i class="fa fa-thumbs-up"></i><span>'+en4.core.language.translate("Album Unliked successfully")+'</span>');
					 }						 
				}
			var ObjectIncrem = sesJqueryObject(objectDocument).parent().parent().find('.sesalbum_list_grid_info').find('.sesalbum_list_grid_stats');
			var ObjectLength = sesJqueryObject(ObjectIncrem).children();
			if(ObjectLength.length >0){
					for(i=0;i<ObjectLength.length;i++){
						if(ObjectLength[i].hasClass('sesalbum_list_grid_likes')){
							var title = sesJqueryObject(ObjectLength[i]).attr('title').replace(/[^0-9]/g, '');	
							sesJqueryObject(ObjectLength[i]).attr('title',sesJqueryObject(ObjectLength[i]).attr('title').replace(title,data.like_count));
							var innerContent = sesJqueryObject(ObjectLength[i]).html().replace(/[^0-9]/g, '');
							sesJqueryObject(ObjectLength[i]).html(sesJqueryObject(ObjectLength[i]).html().replace(innerContent,data.like_count));
						}	
					}
			}
		}
		})).send();
		return false;
});
function sesRotate(photo_id,rotateAngle){
	var className;
	sesJqueryObject('#ses-rotate-'+rotateAngle).attr('class','icon_loading');
	if(rotateAngle == 90 || rotateAngle == 270){
		if(rotateAngle == 90)
			className = 'sesalbum_icon_photos_rotate_ccw';
		else
			className = 'sesalbum_icon_photos_rotate_cw';		
		rotateSes(photo_id,rotateAngle,className);
	}else{
		if(rotateAngle == 'horizontal')
			className = 'sesalbum_icon_photos_flip_horizontal';
		else
			className = 'sesalbum_icon_photos_flip_vertical';
		flipSes(photo_id,rotateAngle,className);
	}
		
	return false;
}
function flipSes(photo_id,rotateAngle,className){
	request = new Request.JSON({
      url : en4.core.baseUrl + 'albums/photo/flip',
      data : {
        format : 'json',
        photo_id : photo_id,
        direction : rotateAngle
      },
      onComplete: function(response) {
        // Check status
        if( $type(response) == 'object' &&
            $type(response.status) &&
            response.status == false ) {
						alert(en4.core.language.translate('An error has occurred processing the request. The target may no longer exist.'));
						return;
        } else if( $type(response) != 'object' ||
          !$type(response.status) ) {
         	 alert(en4.core.language.translate('An error has occurred processing the request. The target may no longer exist.'));
           return;
        }
					if(response.status){
							sesJqueryObject('#ses-rotate-'+rotateAngle).attr('class',className);
						if(sesJqueryObject('#media_photo').length>0 && !sesJqueryObject('#ses_media_lightbox_container').length)
							sesJqueryObject('#media_photo').attr('src',response.href);
						else
							sesJqueryObject('#gallery-img').attr('src',response.href);
						return;
					}
      }
    });
    request.send();
		return false;
}
function rotateSes(photo_id,rotateAngle,className){
	request = new Request.JSON({
      url : en4.core.baseUrl + 'albums/photo/rotate',
      data : {
        format : 'json',
        photo_id : photo_id,
        angle : rotateAngle
      },
      onComplete: function(response) {
        // Check status
        if( $type(response) == 'object' &&
            $type(response.status) &&
            response.status == false ) {
 					  alert(en4.core.language.translate('An error has occurred processing the request. The target may no longer exist.'));
					  return;
        } else if( $type(response) != 'object' ||
          !$type(response.status) ) {
           alert(en4.core.language.translate('An error has occurred processing the request. The target may no longer exist.'));
           return;
        }
			 if(response.status){
							sesJqueryObject('#ses-rotate-'+rotateAngle).attr('class',className);
							if(sesJqueryObject('#media_photo').length>0 && (sesJqueryObject('#ses_media_lightbox_container').css('display') == 'none' ||  sesJqueryObject('#ses_media_lightbox_container').length == 0))
								sesJqueryObject('#media_photo').attr('src',response.href);
							else
								sesJqueryObject('#gallery-img').attr('src',response.href);
								return;
					}
      }
    });
    request.send();
		return;	
}
//FAV LIKE ON ALBUM LISTING
sesJqueryObject(document).on('click','.sesalbum_albumFav',function(){
		var data = sesJqueryObject(this).attr('data-src');
		var objectDocument = this;
		 (new Request.JSON({
			url : en4.core.baseUrl + 'sesalbum/album/fav/album_id/'+data,
			data : {
				format : 'json',
				type : 'album',
				id : data,
			},
		 onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
			 var data = JSON.parse(responseElements);
			 if(data.status == 'false'){
					 if(data.error == 'Login')
							alert(en4.core.language.translate('Please login'));
					 else
							alert(en4.core.language.translate('Invalid argument supplied.'));
			 }else{
					 if(data.condition == 'increment'){
              sesJqueryObject(objectDocument).addClass('button_active');
							sesJqueryObject(objectDocument).find('span').html(data.favourite_count);
							showTooltip(10,10,'<i class="fa fa-heart"></i><span>'+en4.core.language.translate("Album added as Favourite successfully")+'</span>', 'sesbasic_favourites_notification');
					 }else{
              sesJqueryObject(objectDocument).removeClass('button_active');
							sesJqueryObject(objectDocument).find('span').html(data.favourite_count);
							showTooltip(10,10,'<i class="fa fa-heart"></i><span>'+en4.core.language.translate("Album Unfavourited successfully")+'</span>');
					 }						 
				}
			var ObjectIncrem = sesJqueryObject(objectDocument).parent().parent().find('.sesalbum_list_grid_info').find('.sesalbum_list_grid_stats');
			var ObjectLength = sesJqueryObject(ObjectIncrem).children();
			if(ObjectLength.length >0){
					for(i=0;i<ObjectLength.length;i++){
						if(ObjectLength[i].hasClass('sesalbum_list_grid_fav')){
							var title = sesJqueryObject(ObjectLength[i]).attr('title').replace(/[^0-9]/g, '');	
							sesJqueryObject(ObjectLength[i]).attr('title',sesJqueryObject(ObjectLength[i]).attr('title').replace(title,data.favourite_count));
							var innerContent = sesJqueryObject(ObjectLength[i]).html().replace(/[^0-9]/g, '');
							sesJqueryObject(ObjectLength[i]).html(sesJqueryObject(ObjectLength[i]).html().replace(innerContent,data.favourite_count));
						}	
					}
			}
		}
		})).send();
		return false;
});
// ALBUM FAV ON ALBUM LISTINGS
sesJqueryObject(document).on('click','.sesalbum_photoFav ,#sesalbum_favourite',function(){
		var data = sesJqueryObject(this).attr('data-src');
		var objectDocument = this;
		 (new Request.JSON({
			url : en4.core.baseUrl + 'sesalbum/photo/fav/photo_id/'+data,
			data : {
				format : 'json',
				type : 'photo',
				id : data,
			},
		 onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
			 var data = JSON.parse(responseElements);
			 if(data.status == 'false'){
					 if(data.error == 'Login')
							alert(en4.core.language.translate('Please login'));
					 else
							alert(en4.core.language.translate('Invalid argument supplied.'));
			 }else{
					 if(data.condition == 'increment'){
              sesJqueryObject(objectDocument).addClass('button_active');
							sesJqueryObject(objectDocument).find('span').html(data.favourite_count);
							showTooltip(10,10,'<i class="fa fa-heart"></i><span>'+en4.core.language.translate("Photo added as Favourite successfully")+'</span>', 'sesbasic_favourites_notification');
					 }else{
              sesJqueryObject(objectDocument).removeClass('button_active');
							sesJqueryObject(objectDocument).find('span').html(data.favourite_count);
							showTooltip(10,10,'<i class="fa fa-heart"></i><span>'+en4.core.language.translate("Photo Unfavourited successfully")+'</span>');
					 }						 
				}
			var ObjectIncrem = sesJqueryObject(objectDocument).parent().parent().find('.sesalbum_list_grid_info').find('.sesalbum_list_grid_stats');
			var ObjectLength = sesJqueryObject(ObjectIncrem).children();
			if(ObjectLength.length >0){
					for(i=0;i<ObjectLength.length;i++){
						if(ObjectLength[i].hasClass('sesalbum_list_grid_fav')){
							var title = sesJqueryObject(ObjectLength[i]).attr('title').replace(/[^0-9]/g, '');	
							sesJqueryObject(ObjectLength[i]).attr('title',sesJqueryObject(ObjectLength[i]).attr('title').replace(title,data.favourite_count));
							var innerContent = sesJqueryObject(ObjectLength[i]).html().replace(/[^0-9]/g, '');
							sesJqueryObject(ObjectLength[i]).html(sesJqueryObject(ObjectLength[i]).html().replace(innerContent,data.favourite_count));
						}	
					}
			}
		}
		})).send();
		return false;
});
//Admin featured photo/album
sesJqueryObject(document).on('click','.sesalbum_admin_sponsored',function(event){
		event.preventDefault();
		var data = sesJqueryObject(this).attr('href');
		sesJqueryObject(this).css('pointer-events','none');
		var objectDocument = this;
		 (new Request.JSON({
			url : data,
			data : {
				format : 'json',
			},
		 onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
			 sesJqueryObject(objectDocument).css('pointer-events','auto');
				 if(responseElements == 1)
				 		sesJqueryObject(objectDocument).html(en4.core.language.translate('Unmark as Sponsored'));
				 else if(responseElements == 0)
				 		sesJqueryObject(objectDocument).html(en4.core.language.translate('Mark Sponsored'));
		 }
		})).send();
		return false;
});
//Admin sponsored photo/album
sesJqueryObject(document).on('click','.sesalbum_admin_featured',function(event){
		event.preventDefault();
		var data = sesJqueryObject(this).attr('href');
		sesJqueryObject(this).css('pointer-events','none');
		var objectDocument = this;
		 (new Request.JSON({
			url : data,
			data : {
				format : 'json',
			},
		 onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
			 sesJqueryObject(objectDocument).css('pointer-events','auto');
				 if(responseElements == 1)
				 		sesJqueryObject(objectDocument).html(en4.core.language.translate('Unmark as Featured'));
				 else if(responseElements == 0)
				 		sesJqueryObject(objectDocument).html(en4.core.language.translate('Mark Featured'));
				 
		 }
		})).send();
		return false;
});
// PHOTO LIKE ON ALBUM LISTINGS
sesJqueryObject(document).on('click','.sesalbum_photolike',function(){
		var data = sesJqueryObject(this).attr('data-src');
		var objectDocument = this;
		 (new Request.JSON({
			url : en4.core.baseUrl + 'sesalbum/photo/like/photo_id/'+data,
			data : {
				format : 'json',
				type : 'photo',
				id : data,
			},
		 onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
			 var data = JSON.parse(responseElements);
			 if(data.status == 'false'){
					 if(data.error == 'Login')
							alert(en4.core.language.translate('Please login'));
					 else
							alert(en4.core.language.translate('Invalid argument supplied.'));
			 }else{
					 if(data.condition == 'increment'){
              sesJqueryObject(objectDocument).addClass('button_active');
							sesJqueryObject(objectDocument).find('span').html(data.like_count);
							showTooltip(10,10,'<i class="fa fa-thumbs-up"></i><span>'+en4.core.language.translate("Photo Liked successfully")+'</span>', 'sesbasic_liked_notification');
					 }else{
              sesJqueryObject(objectDocument).removeClass('button_active');
							sesJqueryObject(objectDocument).find('span').html(data.like_count);
							showTooltip(10,10,'<i class="fa fa-thumbs-up"></i><span>'+en4.core.language.translate("Photo Unliked successfully")+'</span>');
					 }
					 
				}
			var ObjectIncrem = sesJqueryObject(objectDocument).parent().parent().find('.sesalbum_list_grid_info').find('.sesalbum_list_grid_stats');
			var ObjectLength = sesJqueryObject(ObjectIncrem).children();
			if(ObjectLength.length >0){
					for(i=0;i<ObjectLength.length;i++){
						if(ObjectLength[i].hasClass('sesalbum_list_grid_likes')){
							var title = sesJqueryObject(ObjectLength[i]).attr('title').replace(/[^0-9]/g, '');	
							sesJqueryObject(ObjectLength[i]).attr('title',sesJqueryObject(ObjectLength[i]).attr('title').replace(title,data.like_count));
							var innerContent = sesJqueryObject(ObjectLength[i]).html().replace(/[^0-9]/g, '');
							sesJqueryObject(ObjectLength[i]).html(sesJqueryObject(ObjectLength[i]).html().replace(innerContent,data.like_count));
						}	
					}
			}
		}
		})).send();
		return false;
});
// ALBUM LISTING SLIDING IMAGES
var interval;
sesJqueryObject(document).on({
	 mouseenter: function(){
			var imageIndex = 0;
			var imageContainerObject = this;
			var totalImageCount = sesJqueryObject(this).find('.sesalbum_list_grid_img').find('.ses_image_container').children('.child_image_container').length;
			var changeTime = 2000;
			interval = setInterval(function(){ 
				var imageURL = sesJqueryObject(imageContainerObject).find('.sesalbum_list_grid_img').find('.ses_image_container').children().eq(imageIndex).html();
				sesJqueryObject(imageContainerObject).find('.sesalbum_list_grid_img').find('.main_image_container').css('background-image', 'url(' + imageURL + ')');
				if(imageIndex == (totalImageCount-1)){
					imageIndex=0;
				}else
					imageIndex++;					
			}, changeTime);
	 },
	 mouseleave: function(){
		 var imageContainerObject = this;
			if(typeof interval != 'undefined')
			clearInterval(interval);
			var totalImageCount = sesJqueryObject(this).find('.sesalbum_list_grid_img').find('.ses_image_container').children('.child_image_container').length;
			var imageURL = sesJqueryObject(imageContainerObject).find('.sesalbum_list_grid_img').find('.ses_image_container').children().eq(totalImageCount-1).html();
			sesJqueryObject(imageContainerObject).find('.sesalbum_list_grid_img').find('.main_image_container').css('background-image', 'url(' + imageURL + ')');
	}
}, '.sesalbum_list_grid');

sesJqueryObject(document).on('click','#sesLikeUnlikeButton',function(){
		sesJqueryObject('#comments .comments_options').find("a:eq(1)").trigger('click');						 
		return false;
});
sesJqueryObject(document).on('click','#sesLightboxLikeUnlikeButton',function(){
		sesJqueryObject('#comments .comments_options').find("a:eq(1)").trigger('click');		
		return false;
});
sesJqueryObject(document).on('click','#sescomment_button',function(){
$('comment-form').style.display = '';$('comment-form').body.focus();
 sesJqueryObject("html, body").animate({scrollTop: sesJqueryObject("#comments").offset().top}, 1000); 
});
sesJqueryObject(document).on('click','#sesImageViewerLikeUnlike',function(){
		sesJqueryObject('#comments .comments_options').find("a:eq(1)").trigger('click');						 
		return false;
});
function trim(str, chr) {
  var rgxtrim = (!chr) ? new RegExp('^\\s+|\\s+$', 'g') : new RegExp('^'+chr+'+|'+chr+'+$', 'g');
  return str.replace(rgxtrim, '');
}
// profile tab resize
sesJqueryObject(document).on('click','.tab_layout_sesalbum_profile_albums',function (event) {
    sesJqueryObject(window).trigger('resize');
});
sesJqueryObject(document).on('click','ul li.tab_layout_sesalbum_profile_albums',function(event){
		sesJqueryObject('.layout_sesalbum_profile_albums').show();
		sesJqueryObject(window).trigger('resize');
});
// profile update for masonry tab resize
sesJqueryObject(document).on('click','ul#main_tabs li.tab_layout_activity_feed',function (event) {
	 sesJqueryObject(window).trigger('resize');
});
// chanel photo for masonry tab resize
sesJqueryObject(document).on('click','ul#main_tabs li.tab_layout_sesvideo_chanel_photos',function (event) {
	 sesJqueryObject(window).trigger('resize');
});
sesJqueryObject(document).on('click','ul#main_tabs li.tab_layout_sesalbum_profile_albums',function(event){
		sesJqueryObject(window).trigger('resize');
});
function showTooltip(x, y, contents, className) {
	if(sesJqueryObject('.sesbasic_notification').length > 0)
		sesJqueryObject('.sesbasic_notification').hide();
	sesJqueryObject('<div class="sesbasic_notification '+className+'">' + contents + '</div>').css( {
		display: 'block',
	}).appendTo("body").fadeOut(5000,'',function(){
		sesJqueryObject(this).remove();	
	});
}
function browsePhotoURL(){
	window.location.href = en4.core.baseUrl + 'albums/browse-photo';
	return false;
}
var sesalbum_cookie_set_value = [];
function setCookieSesalbum(cvalue) {
    var d = new Date();
    d.setTime(d.getTime() + (1*24*60*60*1000));
    var expires = "expires="+d.toGMTString();
		var length = sesalbum_cookie_set_value.length;
		var exists = false;
		if(length > 0){
			for (var i = 0; i < length; i++) {
				if (sesalbum_cookie_set_value[i] === cvalue) 
				{
					exists = true;   
					break;
				}
			}
		}
		if(!exists)
			sesalbum_cookie_set_value.push(cvalue);
    document.cookie = 'sesalbum_lightbox_value' + "=" + sesalbum_cookie_set_value + "; " + expires+"; path=/"; 
  }