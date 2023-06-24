<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _lightboxImageAlbum.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.Crop.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')
     ->appendFile($this->layout()->staticBaseUrl . 'externals/tagger/tagger.js');
  $this->headTranslate(array(
    'Save', 'Cancel', 'delete'
  ));
?>
<style type="text/css">
.sitestore_photo_tag{background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/icons/tagged.png);}
.sitestore_lightbox_photos_delete{background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/photo/album_delete.png);}
.sitestore_lightbox_photos_download{background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/icons/download.png);}
.sitestore_lightbox_like{background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/icons/thumb_up.png);}
.sitestore_lightbox_unlike{background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/icons/thumb_down.png);}
.sitestore_lightbox_comment{background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/icons/lightbox_comment.png);}
.store_image_content {background:<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorealbum.photolightbox.bgcolor', '#000000'); ?>;}
.store_lightbox_options a.close{background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/closebox.png);}
.store_lightbox_options a.nxt{background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/icons/store-photo-nxt.png);}
.store_lightbox_options a.pre{background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/icons/store-photo-prev.png);}
.store_lightbox_user_options{background:<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorealbum.photolightbox.bgcolor', '#000000'); ?>;}
.store_lightbox_user_right_options{background:<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorealbum.photolightbox.bgcolor', '#000000'); ?>;}
.lightbox_photo_detail{background:<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorealbum.photolightbox.bgcolor', '#000000'); ?>;}
.store_photo_lightbox_content .lightbox_photo_description_edit_icon a{background:url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/write_edit_icon.png);}
.store_photo_lightbox_content .lightbox_photo_description_edit_icon a:hover{
	background:url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/write_edit_icon.png) 0 18px;
}
.store_lightbox_user_options a,
.lightbox_photo_detail,
.lightbox_photo_detail a{color:<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorealbum.photolightbox.fontcolor','#FFFFFF') ?>;}
</style>
<div class="store_lightbox" id="light_album" style="display: none;">
  <input type="hidden" id="canReload" value="0" />
  <div class="store_black_overlay"  ></div>
  <div class="sitestore_lightbox_white_content_wrapper" onclick = "closeLightBoxSitestoreAlbum()">
    <div class="sitestore_lightbox_white_content"  id="white_content_default_album"  >
      <div class="store_image_content album_viewmedia_container" id="media_image_div_album">
      </div>
      <div id="album_lightbox">
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  var  baseY = '0';
       
  function openLightBoxSitestoreAlbum(imagepath, url){
    document.getElementById('light_album').style.display='block';    
    document.getElementById('media_image_div_album').style.display="block";
    document.getElementById('media_image_div_album').innerHTML= "&nbsp;<img class='lightbox_photo' src="+imagepath+"  />";
    setHtmlScroll("hidden");
    photopaginationDefaultSitestoreAlbum(url);
  }

 	window.addEvent('domready', function() {
     $('white_content_default_album').addEvent('click', function(event) {
      event.stopPropagation();
    });
  });
  
  var closeLightBoxSitestoreAlbum = function()
  {
    document.getElementById('light_album').style.display='none';
    setHtmlScroll("auto");
    $('album_lightbox').innerHTML ="";
     if(document.getElementById('store_lightbox_text')){
       document.getElementById('store_lightbox_text').innerHTML="";
    document.getElementById('store_lightbox_text').style.display="none";
     }
    if(document.getElementById('store_lightbox_user_options'))
    document.getElementById('store_lightbox_user_options').style.display="none";
    if(document.getElementById('store_photo_scroll'))
    document.getElementById('store_photo_scroll').style.display="none";
   if(document.getElementById('store_lightbox_user_right_options'))
    document.getElementById('store_lightbox_user_right_options').style.display="none";

    if(document.getElementById('canReload').value==1){
      window.location.reload(true);
    }
		if(document.getElementById('album_lightbox'))
		 $('album_lightbox').innerHTML="";
  };

  var photopaginationDefaultSitestoreAlbum = function(url)
  {
    if(document.getElementById('lightbox_photo_detail'))
         document.getElementById('lightbox_photo_detail').style.display="none";
    en4.core.request.send(new Request.HTML({
      url : url,
      data : {
        format : 'html',
        isajax : 0
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $('album_lightbox').innerHTML = responseHTML;
         document.getElementById('media_image_div_album').innerHTML="";
         document.getElementById('media_image_div_album').style.display="none";
          if($('ads') && $('ads_hidden')){
            $('ads').innerHTML =  $('ads_hidden').innerHTML;
             $('ads_hidden').innerHTML='';
          }
      }
    }));
  };

  function setHtmlScroll(cssCode) {
    $$('html').setStyle('overflow',cssCode);
    
  }
  function setImageScroll(cssCode) {
    $$('.sitestore_lightbox_white_content_wrapper').setStyle('overflow',cssCode);

  }
</script>