<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');
?>

<?php 
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl."application/modules/Sitestoreproduct/externals/scripts/_class.noobSlide.packed.js");
?>
<?php
$ratingValue = $this->ratingType;
$ratingShow = 'small-star';
  if ($this->ratingType == 'rating_editor') {$ratingType = 'editor';} elseif ($this->ratingType == 'rating_avg') {$ratingType = 'overall';} else { $ratingType = 'user';}
?>

<script type="text/javascript">
  window.addEvent('domready',function() {
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

    var width=$("featured_slideshow_wrapper<?php echo $this->identity ?>").clientWidth;
    $("featured_slideshow_mask<?php echo $this->identity ?>").style.width= (width-10)+"px";
    var divElements=$("featured_slideshow_mask<?php echo $this->identity ?>").getElements('.featured_slidebox');   
    for(var i=0;i < divElements.length;i++)
      divElements[i].style.width= (width-10)+"px";

    var handles8_more = $$('.handles8_more span');
    var num_of_slidehsow = "<?php echo $this->num_of_slideshow; ?>";
    var nS8 = new noobSlide({
      box: $('sitestoreproduct_featured_<?php echo $this->identity ?>_im_te_advanced_box'),
      items: $$('#sitestoreproduct_featured_<?php echo $this->identity ?>_im_te_advanced_box h3'),
      size: (width-10),
      handles: $$('#handles8 span'),
      addButtons: {previous: $('sitestoreproduct_featured_<?php echo $this->identity ?>_prev8'), stop: $('sitestoreproduct_featured_<?php echo $this->identity ?>_stop8'), play: $('sitestoreproduct_featured_<?php echo $this->identity ?>_play8'), next: $('sitestoreproduct_featured_<?php echo $this->identity ?>_next8') },
      interval: 5000,
      fxOptions: {
        duration: 500,
        transition: '',
        wait: false
      },
      autoPlay: true,
      mode: 'horizontal',
      onWalk: function(currentItem,currentHandle){

        // Finding the current number of index.
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
          $('sitestoreproduct_featured_<?php echo $this->identity ?>_caption').innerHTML =   current_caption_link;
        }
        else {
          $('sitestoreproduct_featured_<?php echo $this->identity ?>_caption').innerHTML =  '';
        }
        $('sitestoreproduct_featured_<?php echo $this->identity ?>_current_numbering').innerHTML =  current_index + '/' + "<?php echo $this->num_of_slideshow; ?>" ;
      }
    });

    //more handle buttons
    nS8.addHandleButtons(handles8_more);
    //walk to item 3 witouth fx
    nS8.walk(0,false,true);
  });
</script>

<div class="featured_slideshow_wrapper" id="featured_slideshow_wrapper<?php echo $this->identity ?>">
  <div class="featured_slideshow_mask" id="featured_slideshow_mask<?php echo $this->identity ?>" style="height:220px;">
    <div id="sitestoreproduct_featured_<?php echo $this->identity ?>_im_te_advanced_box" class="featured_slideshow_advanced_box">

      <?php $image_count = 1; ?>
      <?php foreach ($this->show_slideshow_object as $type => $item): ?>
      <div class='featured_slidebox sitestoreproduct_q_v_wrap' style="height:220px;">
          <div class='featured_slidshow_img'> 
            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)):?>             
              <?php if (!empty($item->featured) && !empty($this->featuredIcon)): ?> 
                <span class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"><?php echo $this->translate('Featured'); ?></span>
              <?php endif; ?>
              <?php if (!empty($item->newlabel) && !empty($this->newIcon)): ?> 
                <i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
              <?php endif; ?>
            <?php endif; ?>
            
            <?php $product_id = $item->product_id; ?>
            <?php $quickViewButton = true; ?>
            <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_quickView.tpl'; ?>
              
          	<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.profile')); ?>  
          	
            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)):?>
              <?php if (  !empty($item->sponsored) && !empty($this->sponsoredIcon)): ?>
                <div class="sr_sitestoreproduct_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.sponsoredcolor', '#FC0505'); ?>">
                  <?php echo $this->translate('SPONSORED'); ?>                 
                </div>
              <?php endif; ?>
            <?php endif; ?>
          
          </div>
          <div class='featured_slidshow_content'>
            <?php $tmpBody = strip_tags($item->getTitle());
            $title = ( Engine_String::strlen($tmpBody) > $this->title_truncation ? Engine_String::substr($tmpBody, 0, $this->title_truncation) . '..' : $tmpBody ); ?>
            <?php if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)) :?>
            	<span class="fright mtop5">              
                <?php if (!empty($item->sponsored) && !empty($this->sponsoredIcon)): ?>
                  <i title="<?php echo $this->translate('Sponsored');?>" class="sr_sitestoreproduct_icon seaocore_icon_sponsored"></i>
                <?php endif; ?>
                <?php if ( !empty($item->featured) && !empty($this->featuredIcon)): ?> 
                  <i title="<?php echo $this->translate('Featured');?>" class="sr_sitestoreproduct_icon seaocore_icon_featured"></i>
                <?php endif; ?>
	          	</span>    
            <?php endif; ?> 
            <h5 class="o_hidden"> <?php echo $this->htmlLink($item->getHref(), $title, array('title' => $item->getTitle())) ?></h5>
            <h3 style='display:none'><span><?php echo $image_count++ . '_caption_title:' . $item->getTitle() . '_caption_link:' . $this->htmlLink($item->getHref(), $this->translate("View Product &raquo;"), array('class' => 'featured_slideshow_view_link','title' => $item->getTitle())) . '</span>' ?></h3>
            
            <span class='featured_slidshow_info'>
	             <?php if (empty($this->category_id)): ?>
                <p>&nbsp;
                <a href="<?php echo $this->url(array('category_id' => $item->category_id, 'categoryname' => $item->getCategory()->getCategorySlug()), "". $this->categoryRouteName .""); ?>"> 
                    <?php 
                      $getCategoryName = $item->getCategory()->getTitle(true);  
                      echo $this->translate($getCategoryName);
                    ?>
                  </a>
                </p>
              <?php endif; ?>

            <?php 
            // CALLING HELPER FOR GETTING PRODUCT INFORMATIONS
            echo $this->getProductInfo($item, $this->identity, 'list_view', $this->showAddToCart, $this->showinStock, true); ?>
                
            <?php if(!empty($this->statistics)): ?>  
                <p class="mtop5">

                  <?php 

                    $statistics = '';

                    if(in_array('commentCount', $this->statistics)) {
                      $statistics .= $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)).', ';
                    }

                    if(in_array('reviewCount', $this->statistics) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2)) {
                      $statistics .= $this->partial(
                      '_showReview.tpl', 'sitestoreproduct', array('sitestoreproduct'=>$item)).', ';
                    }

                    if(in_array('viewCount', $this->statistics)) {
                      $statistics .= $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)).', ';
                    }

                    if(in_array('likeCount', $this->statistics)) {
                      $statistics .= $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)).', ';
                    }                 

                    $statistics = trim($statistics);
                    $statistics = rtrim($statistics, ',');

                  ?>

                  <?php echo $statistics; ?>
                </p>
              <?php endif; ?>
                
              <?php if ($ratingValue == "rating_both") : ?>
                <span class="clr mtop5 dblock o_hidden">
                  <?php echo $this->showRatingStarSitestoreproduct($item->rating_editor, 'editor', $ratingShow); ?>
                  <?php echo $this->showRatingStarSitestoreproduct($item->rating_users, 'user', $ratingShow); ?>
                </span>       
                <?php
              else:
                echo '<span class="clr mtop5 dblock">'. $this->showRatingStarSitestoreproduct($item->$ratingValue, $ratingType, $ratingShow) .'</span>';
              endif;
              ?>
            </span>
            <?php if (!empty($item->body)) : ?>
              <p class="clr"> <?php echo ( Engine_String::strlen($item->body) > 55 ? Engine_String::substr($item->body, 0, 55) . '...' : $item->body ) . ' ' . $this->htmlLink($item->getHref(), $this->translate('More &raquo;')) ?></p>
                
            <?php endif; ?>
            <div class="sr_sitestoreproduct_browse_list_info_footer o_hidden mtop5">
              <div>
                <?php echo $this->compareButtonSitestoreproduct($item, $this->identity); ?>
              </div>
              <div>
                <?php echo $this->addToWishlistSitestoreproduct($item, array('classIcon' => 'sr_sitestoreproduct_wishlist_href_link', 'classLink' => '', 'text' => 'Add to Wishlist'));?>
              </div> 
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="featured_slideshow_option_bar">
    <div>
      <p class="buttons" style="<?php if($image_count<=2): echo "display:none;"; endif;?>">
        <span id="sitestoreproduct_featured_<?php echo $this->identity ?>_prev8" class="featured_slideshow_controllers-prev featured_slideshow_controllers prev" title="Previous" ></span>
        <span id="sitestoreproduct_featured_<?php echo $this->identity ?>_stop8" class="featured_slideshow_controllers-stop featured_slideshow_controllers" title="Stop"></span>
        <span id="sitestoreproduct_featured_<?php echo $this->identity ?>_play8" class="featured_slideshow_controllers-play featured_slideshow_controllers" title="Play"></span>
        <span id="sitestoreproduct_featured_<?php echo $this->identity ?>_next8" class="featured_slideshow_controllers-next featured_slideshow_controllers" title="Next" ></span>
      </p>
   	</div>
    <span id="sitestoreproduct_featured_<?php echo $this->identity ?>_caption"></span>
    <span id="sitestoreproduct_featured_<?php echo $this->identity ?>_current_numbering" class="featured_slideshow_pagination" style="<?php if($image_count<=2): echo "display:none;"; endif;?>"></span>
  </div>
</div>