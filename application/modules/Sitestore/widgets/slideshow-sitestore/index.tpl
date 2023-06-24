<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/_class.noobSlide.packed.js');
?>

<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0);?>
<?php
// Starting work for "Slide Show".
$image_var = '';
$image_text_var = '';
$pane_var = '';
$pagination_var = '';
$thumbnail_var = '';
$thumb_span_var = '';
$title_link_var = '';

$title_link_var = "new Element('h4').set('html',";
if ($this->show_link == 'true')
  $title_link_var .= "'<a href=" . '"' . "'+currentItem.link+'" . '"' . ">link</a>'";
if ($this->title == 'true')
  $title_link_var .= "+currentItem.title";
$title_link_var .= ").inject(im_info);";

$image_count = 1;
$priceEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.price.field', 0);
$locationEnable = Engine_Api::_()->sitestore()->enableLocation();
foreach ($this->show_slideshow_object as $type => $item) {
  $content_info = '';
  $itemPhoto = $this->htmlLink(Engine_Api::_()->sitestore()->getHref($item->store_id, $item->owner_id, $item->getSlug()), $this->itemPhoto($item, 'thumb.profile'));
  $content_info = $this->timestamp(strtotime($item->creation_date));
  if($postedBy):
  $content_info.= $this->translate(' - posted by ') . $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()); 
  endif;
  $content_info.='<p>';
  $content_info.=$this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) . ', ';

	if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
		$memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'storemember.member.title' , 1);
		if ($item->member_title && $memberTitle) : 
			if ($item->member_count == 1) :  
				$content_info.= $item->member_count . ' member' . ', ';  
			else: 
				$content_info.= $item->member_count . ' ' .  $item->member_title . ', '; 
			endif; 
		else : 
			$content_info.= $this->translate(array('%s member', '%s members', $item->member_count), $this->locale()->toNumber($item->member_count)) . ', ';
		endif;
	}

	$sitestorereviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview');
	if ($sitestorereviewEnabled) {
	 $content_info.=$this->translate(array('%s review', '%s reviews', $item->review_count), $this->locale()->toNumber($item->review_count)) . ', ';
	}

  $content_info.=$this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) . ', ';
  $content_info.=$this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count));

  $content_info.='</p>';

  $content_location = '';
  if ($locationEnable && $item->location) {
    $content_location = $this->translate("Location: ") . " " . $this->translate($item->location);
  }
  $content_price = '';
  if ($priceEnable && $item->price) {
    $content_price = $this->translate("Price: ") . " " . Engine_Api::_()->sitestore()->getPriceWithCurrency($item->price);
  }

  $description = $item->body;

  $content_link = $this->htmlLink(Engine_Api::_()->sitestore()->getHref($item->store_id, $item->owner_id, $item->getSlug()), $this->translate('View Store &raquo;'), array('class' => 'featured_slideshow_view_link'));

  $image_var .= '<span>' . $itemPhoto . '</span>';

  $pane_var .= "<span>Pane " . ($image_count + 1) . "</span>";
  $pagination_var .= "<span>" . ($image_count + 1) . "</span>";
  $thumbnail_var .= "<div>" . $this->itemPhoto($item, 'thumb.icon') . "</div>";
  $thumb_span_var .= "<span></span>";

  $image_text_var .= "<div class='featured_slidebox'>";
  $image_text_var .= "<div class='featured_slidshow_img'>" . $itemPhoto . "</div>";


  if (!empty($content_info)) {
    $image_text_var .= "<div class='featured_slidshow_content'>";
  }
  if (!empty($item->title)) {
    $tmpBody = strip_tags($item->title);
    $item->title = ( Engine_String::strlen($tmpBody) > 45 ? Engine_String::substr($tmpBody, 0, 45) . '..' : $tmpBody );

    $image_text_var .='<h5>' . $this->htmlLink(Engine_Api::_()->sitestore()->getHref($item->store_id, $item->owner_id, $item->getSlug()), $item->title) . '</h5>';
  }

  if (!empty($content_link)) {
    $image_text_var .= "<h3 style='display:none'><span>" . $image_count++ . '_caption_title:' . $item->title . '_caption_link:' . $content_link . '</span>' . "</h3>";
  }

  if (!empty($content_info)) {
    $image_text_var .= "<span class='featured_slidshow_info'>" . $content_info . "</span>";
  }
  $descriptionSize = 220;
  if (!empty($content_price)) {

    $descriptionSize-=30;
    $image_text_var .= "<span class='featured_slidshow_info'>" . $content_price . "</span>";
  }
  if (!empty($content_price)&& !empty($content_location)) {
     $image_text_var .="<span class='featured_slidshow_info'>, </span>";
  }
  if (!empty($content_location)) {
    $descriptionSize-=30;

    $image_text_var .= "<span class='featured_slidshow_info'>" . $content_location . "</span>";
  }

  if (!empty($description)) {
    $truncate_description = ( Engine_String::strlen($description) > $descriptionSize + 3 ? Engine_String::substr($description, 0, $descriptionSize) . '...' : $description );
    $image_text_var .= "<p>" . $truncate_description . " " . $this->htmlLink(Engine_Api::_()->sitestore()->getHref($item->store_id, $item->owner_id, $item->getSlug()), $this->translate('More &raquo;')) . "</p>";
  }

  $image_text_var .= "</div></div>";
}
if (!empty($this->sitestore_featured)) {
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
        box: $('sitestore_featured_te_advanced_box'),
        items: $$('#sitestore_featured_te_advanced_box h3'),
        size: (width-10),
        handles: $$('#handles8 span'),
        addButtons: {previous: $('sitestore_featured_prev8'), stop: $('sitestore_featured_stop8'), play: $('sitestore_featured_play8'), next: $('sitestore_featured_next8') },
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
            $('sitestore_featured_caption').innerHTML =   current_caption_link;
          }
          else {
            $('sitestore_featured_caption').innerHTML =  '';
          }


          $('sitestore_featured_current_numbering').innerHTML =  current_index + '/' + num_of_slidehsow ;
        }
      });
      
      //more handle buttons
      nS8.addHandleButtons(handles8_more);
      //walk to item 3 witouth fx
      nS8.walk(0,false,true);
    });
  </script>
<?php } ?>

<div class="featured_slideshow_wrapper">
  <div class="featured_slideshow_mask">
    <div id="sitestore_featured_te_advanced_box" class="featured_slideshow_advanced_box">
<?php echo $image_text_var ?>
    </div>
  </div>

  <div class="featured_slideshow_option_bar">
    <div>
      <p class="buttons">
        <span id="sitestore_featured_prev8" class="featured_slideshow_controllers-prev featured_slideshow_controllers prev" title="<?php echo $this->translate('Previous'); ?> "></span>
        <span id="sitestore_featured_stop8" class="featured_slideshow_controllers-stop featured_slideshow_controllers" title="<?php echo $this->translate('Stop'); ?> "></span>
        <span id="sitestore_featured_play8" class="featured_slideshow_controllers-play featured_slideshow_controllers" title="<?php echo $this->translate('Play'); ?>"></span>
        <span id="sitestore_featured_next8" class="featured_slideshow_controllers-next featured_slideshow_controllers" title="<?php echo $this->translate('Next'); ?> "></span>
      </p>
    </div>
    <span id="sitestore_featured_caption"></span>
    <span id="sitestore_featured_current_numbering" class="featured_slideshow_pagination"></span>
  </div>
</div>