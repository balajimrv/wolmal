<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';

	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitestoreoffer/externals/styles/style_sitestoreoffer.css')
?>
<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/_class.noobSlide.packed.js');
?>

<?php
// Starting work for "Slide Show".
$image_text_var = '';
$title_link_var = '';

$title_link_var = "new Element('h4').set('html',";
if ($this->show_link == 'true')
  $title_link_var .= "'<a href=" . '"' . "'+currentItem.link+'" . '"' . ">link</a>'";
if ($this->title == 'true')
  $title_link_var .= "+currentItem.title";
$title_link_var .= ").inject(im_info);";

$image_count = 1;
$viewer_id = $this->viewer->getIdentity();
if(!empty($viewer_id)) {
	$oldTz = date_default_timezone_get();
	date_default_timezone_set($this->viewer->timezone);
}

foreach ($this->show_slideshow_object as $type => $coupon) {
  $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $coupon->store_id);
  $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
  $tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreoffer.profile-sitestoreoffers', $coupon->store_id, $layout);
  if($coupon->photo_id == 0) {
		$offerPhoto = $this->htmlLink(array('route' => 'sitestoreoffer_view', 'user_id' => $coupon->owner_id, 'offer_id' =>  $coupon->offer_id,'tab' => $tab_id,'slug' => $coupon->getOfferSlug($coupon->title)), "<img class='thumb_normal' src='". $this->layout()->staticBaseUrl . "application/modules/Sitestoreoffer/externals/images/nophoto_offer_thumb_normal.png' alt='' />",array('title' => $coupon->description));
  }
  else {
    $offerPhoto = $this->htmlLink($coupon->getHref(),$this->itemPhoto($coupon, 'thumb.normal', $coupon->description));
  }

  $today = date("Y-m-d H:i:s");
	$claim_value = Engine_Api::_()->getDbTable('claims','sitestoreoffer')->getClaimValue($this->viewer_id,$coupon->offer_id,$coupon->store_id);
	if($coupon->claim_count == -1 && ($coupon->end_time > $today || $coupon->end_settings == 0)) {
		$show_offer_claim = 1;
	}
	elseif($coupon->claim_count > 0 && ($coupon->end_time > $today || $coupon->end_settings == 0)) {
		$show_offer_claim = 1;
	}
	else {
		$show_offer_claim = 0;
	}
					

  $content_info = null;
  $content_info .= '<div class="sitestore_offer_date">';
  if(in_array('startdate', $this->statistics)){
      $content_info .= '<div class="sitestore_offer_date">';
      $content_info .= '<span>';
      $content_info .= $this->translate('Start date:');
      $content_info .= '</span>';
      $content_info .= '<span>';
      $content_info .= $this->timestamp(strtotime($coupon->start_time));
      $content_info .= '</span>';
      $content_info .= "</div>";
  }
  if(in_array('enddate', $this->statistics)){
      $content_info .= '<div class="sitestore_offer_date">';
      $content_info .= '<span>';
      $content_info .= $this->translate('End date:');
      $content_info .= '</span>';
      $content_info .= '<span>';
      if($coupon->end_settings == 1)
        $content_info .= $this->timestamp(strtotime($coupon->end_time));
      else
        $content_info .= $this->translate('Never Expires');
      $content_info .= '</span>';
      $content_info .= "</div>";
  }
//  if(in_array('minpurchase', $this->statistics) && !empty($coupon->minimum_purchase)){
//      $content_info .= '<div class="sitestore_offer_date">';
//      $content_info .= '<span>';
//      $content_info .= $this->translate('Minimum Purchase:');
//      $content_info .= '</span>';
//      $content_info .= '<span>';
//      $content_info .= $coupon->minimum_purchase;
//      $content_info .= '</span>';
//      $content_info .= "</div>";
//  }
  if(in_array('couponcode', $this->statistics)){
    $content_info .= '<div class="sitestore_offer_date">';
    $content_info .= '<span>';
    $content_info .= $this->translate('Select and Copy Code to use : ');
    $content_info .= '</span>';
    $content_info .= $coupon->coupon_code;
    $content_info .= "</div>";
  }
  if(in_array('discount', $this->statistics)){
    $content_info .= '<div class="sitestore_offer_date">';
    $content_info .= '<span>';
    $content_info .= $this->translate('Coupon Discount Value : ');
    $content_info .= '</span>';
    if(!empty($coupon->discount_type)){
      $priceStr = Engine_Api::_()->sitestoreoffer()->getCurrencySymbolPrice($coupon->discount_amount);
      $content_info .= '<span class ="discount_value">';
      $content_info.= $coupon->discount_amount . '%';
      $content_info .= '</span>';
    }
    else{
      $content_info .= '<span class ="discount_value">';
      $content_info.= $priceStr;
      $content_info .= '</span>';
    }
    $content_info .= "</div>";
  }

  if(!empty($show_offer_claim) && empty($claim_value)) {
    $request = Zend_Controller_Front::getInstance()->getRequest();
		$urlO = $request->getRequestUri();
		$request_url = explode('/',$urlO);
		$param = 1;
		if(empty($request_url['2'])) {
			$param = 0;
		}
		$return_url = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://":"http://";
		$currentUrl = urlencode($urlO);
	}
		elseif(!empty($claim_value) && !empty($show_offer_claim) || ($coupon->claim_count == 0 && $coupon->end_time > $today && !empty($claim_value))) {
//			$content_info .= '<span><img src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestoreoffer/externals/images/invite.png" alt="" class="get_offer_icon" />';
//			$content_info .= $this->htmlLink(array('route' => 'sitestoreoffer_general', 'action' => 'resendoffer', 'id' => $coupon->offer_id),Zend_Registry::get('Zend_Translate')->_('Resend Coupon'),array('onclick' => 'owner(this);return false')) . '</span>';
		}
		else {
//			$content_info .= "<span><b>";
//      $content_info .= $this->translate('Expired');
//      $content_info .= "</b></span>";
		}
//	  $content_info .= '<span><b>&middot;</b></span><span>' .$coupon->claimed.' '.$this->translate('claimed') . '</span>';
//		if($coupon->claim_count != -1) {
//		$content_info .= "<span><b>&middot;</b></span>";
//		$content_info .= '<span>' . $coupon->claim_count.' '.$this->translate('claims left') . '</span>';
//	}
  $content_info .= '</div>';

  $description = strip_tags($coupon->description);

  $content_link = $this->htmlLink($coupon->getHref(array('tab' => $tab_id)), $this->translate('View Coupon &raquo;'), array('class' => 'featured_slideshow_view_link'));

  $image_text_var .= "<div class='featured_slidebox'>";
  $image_text_var .= "<div class='featured_slidshow_img'>" . $offerPhoto . "</div>";


  if (!empty($content_info)) {
    $image_text_var .= "<div class='featured_slidshow_content'>";
  }
  if (!empty($coupon->title)) {

    $title = $this->htmlLink($coupon->getHref(array('tab' => $tab_id)), $this->string()->chunk($coupon->getTitle()),array('title' => $coupon->description ));

    $image_text_var .='<h5>' . $this->htmlLink($coupon->getHref(array('tab' => $tab_id)), $coupon->title) . '</h5>';
		$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.title.truncation', 18);
		$tmpBody = strip_tags($sitestore_object->title);
		$store_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
    $image_text_var .= "<div class='featured_slidshow_info'>";
    $image_text_var .= $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitestore()->getHref($coupon->store_id, $coupon->owner_id, $coupon->getSlug()),  $store_title,array('title' => $sitestore_object->title)); 
    $image_text_var .= "</div>";

  }

  if (!empty($content_link)) {
    $image_text_var .= "<h3 style='display:none'><span>" . $image_count++ . '_caption_title:' . $title . '_caption_link:' . $content_link . '</span>' . "</h3>";
  }

  if (!empty($content_info)) {
    $image_text_var .= "<span class='featured_slidshow_info'>" . $content_info . "</span>";
  }

  if (!empty($description)) {
    $truncate_description = ( Engine_String::strlen($description) > 253 ? Engine_String::substr($description, 0, 250) . '...' : $description );
    $image_text_var .= "<p>" . $truncate_description . " " . $this->htmlLink($coupon->getHref(array('tab' => $tab_id)), $this->translate('More &raquo;')) . "</p>";
  }

  $image_text_var .= "</div></div>";
}
if (!empty($this->num_of_slideshow)) {
?>
  <script type="text/javascript">
    window.addEvent('domready',function(){
      
      if (document.getElementsByClassName == undefined) {
        document.getElementsByClassName = function(className)
        {
          var hasClassName = new RegExp("(?:^|\\s)" + className + "(?:$|\\s)");
          var allElements = document.getElementsByTagName("*");
          var results = [];

          var element;
          for (var i = 0; (element = allElements[i]) != null; i++) {
            var elementClass = element.className;
            if (elementClass && elementClass.indexOf(className) != -1 && hasClassName.test(elementClass))
              results.push(element);
          }

          return results;
        }
      }

      var width=$('global_content').getElement(".featured_slideshow_wrapper").clientWidth;
      $('global_content').getElement(".featured_slideshow_mask").style.width= (width-10)+"px";
      var divElements=document.getElementsByClassName('featured_slidebox');   
     for(var i=0;i < divElements.length;i++)
      divElements[i].style.width= (width-10)+"px";
  
      var handles8_more = $$('#handles8_more span');
      var num_of_slidehsow = "<?php echo $this->num_of_slideshow; ?>";
      var nS8 = new noobSlide({
        box: $('sitestoreoffer_featured_offer_im_te_advanced_box'),
        items: $$('#sitestoreoffer_featured_offer_im_te_advanced_box h3'),
        size: (width-10),
        handles: $$('#handles8 span'),
        addButtons: {previous: $('sitestoreoffer_featured_offer_prev8'), stop: $('sitestoreoffer_featured_offer_stop8'), play: $('sitestoreoffer_featured_offer_play8'), next: $('sitestoreoffer_featured_offer_next8') },
        interval: 5000,
        fxOptions: {
          duration: 500,
          transition: '',
          wait: false
        },
        autoPlay: true,
        mode: 'horizontal',
        onWalk: function(currentItem,currentHandle){

          //		// Finding the current number of index.
          var current_index = this.items[this.currentIndex].innerHTML;
          var current_start_title_index = current_index.indexOf(">");
          var current_last_title_index = current_index.indexOf("</span>");
          // This variable containe "Index number" and "Title" and we are finding index.
          var current_title = current_index.slice(current_start_title_index + 1, current_last_title_index);
          // Find out the current index id.
          var current_index = current_title.indexOf("_");
          // "current_index" is the current index.
          current_index = current_title.substr(0, current_index);

          // Find out the caption title.
          var current_caption_title = current_title.indexOf("_caption_title:") + 15;
          var current_caption_link = current_title.indexOf("_caption_link:");
          // "current_caption_title" is the caption title.
          current_caption_title = current_title.slice(current_caption_title, current_caption_link);
          var caption_title = current_caption_title;
          // "current_caption_link" is the caption title.
          current_caption_link = current_title.slice(current_caption_link + 14);


          var caption_title_lenght = current_caption_title.length;
          if( caption_title_lenght > 30 )
          {
            current_caption_title = current_caption_title.substr(0, 30) + '..';
          }

          if( current_caption_title != null && current_caption_link!= null )
          {
            $('sitestoreoffer_featured_offer_caption').innerHTML =   current_caption_link;
          }
          else {
            $('sitestoreoffer_featured_offer_caption').innerHTML =  '';
          }


          $('sitestoreoffer_featured_offer_current_numbering').innerHTML =  current_index + '/' + num_of_slidehsow ;
        }
      });

      //more handle buttons
      nS8.addHandleButtons(handles8_more);
      //walk to item 3 witouth fx
      nS8.walk(0,false,true);
    });
  </script>
<?php } ?>

<script type="text/javascript" >
	function owner(thisobj) {
		var Obj_Url = thisobj.href ;
		Smoothbox.open(Obj_Url);
	}
</script>

<div class="featured_slideshow_wrapper">
  <div class="featured_slideshow_mask">
    <div id="sitestoreoffer_featured_offer_im_te_advanced_box" class="featured_slideshow_advanced_box">
      <?php echo $image_text_var ?>
    </div>
  </div>

  <div class="featured_slideshow_option_bar">
    <div>
      <p class="buttons">
        <span id="sitestoreoffer_featured_offer_prev8" class="featured_slideshow_controllers-prev featured_slideshow_controllers prev" title=<?php echo $this->translate("Previous") ?> ></span>
        <span id="sitestoreoffer_featured_offer_stop8" class="featured_slideshow_controllers-stop featured_slideshow_controllers" title=<?php echo $this->translate("Stop") ?> ></span>
        <span id="sitestoreoffer_featured_offer_play8" class="featured_slideshow_controllers-play featured_slideshow_controllers" title=<?php echo $this->translate("Play") ?> ></span>
        <span id="sitestoreoffer_featured_offer_next8" class="featured_slideshow_controllers-next featured_slideshow_controllers" title=<?php echo $this->translate("Next") ?> ></span>
      </p>
    </div>
    <span id="sitestoreoffer_featured_offer_caption"></span>
    <span id="sitestoreoffer_featured_offer_current_numbering" class="featured_slideshow_pagination"></span>
  </div>
</div>  
<?php if(!empty($viewer_id)):?>
	<?php date_default_timezone_set($oldTz);?>
<?php endif;?>