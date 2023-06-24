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

<?php include APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/Adintegration.tpl'; ?>

<?php
$sitestoreOfferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer');
if ( $sitestoreOfferEnabled ) {
  $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreoffer/externals/styles/style_sitestoreoffer.css');
}
$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/sitestore-tooltip.css');
$viewer = Engine_Api::_()->user()->getViewer()->getIdentity();
$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
$MODULE_NAME = 'sitestore';
$RESOURCE_TYPE = 'sitestore_store';
$enableBouce = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.map.sponsored', 1);
 ?>
<?php // include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>

<?php if ( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.admylikes', 3) && $store_communityad_integration ): ?>
  <div class="layout_right" id="communityad_mylikelist">
    <?php echo $this->content()->renderWidget("communityad.ads", array("itemCount" => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebusiness.admylikes', 3), "loaded_by_ajax" => 1, 'widgetId' => 'store_likelist')) ?>
  </div>
<?php endif; ?>

<script type="text/javascript">
  var sitestores_likes = function(resource_id, resource_type) {
    var content_type = 'sitestore';
    // SENDING REQUEST TO AJAX
    var request = createLikestore(resource_id, resource_type, content_type);
    // RESPONCE FROM AJAX
    request.addEvent('complete', function(responseJSON) {
      if (responseJSON.error_mess == 0) {
        $(resource_id).style.display = 'block';
        if (responseJSON.like_id)
        {
          $('backgroundcolor_' + resource_id).className = "sitestore_browse_thumb sitestore_browse_liked";
          $('sitestore_like_' + resource_id).value = responseJSON.like_id;
          if ($('sitestore_most_likes_' + resource_id))
            $('sitestore_most_likes_' + resource_id).style.display = 'none';

          if ($('sitestore_unlikes_' + resource_id))
            $('sitestore_unlikes_' + resource_id).style.display = 'block';
          $('show_like_button_child_' + resource_id).style.display = 'none';
        }
        else
        {
          $('backgroundcolor_' + resource_id).className = "sitestore_browse_thumb";
          $('sitestore_like_' + resource_id).value = 0;
          if ($('sitestore_most_likes_' + resource_id))
            $('sitestore_most_likes_' + resource_id).style.display = 'block';

          if ($('sitestore_unlikes_' + resource_id))
            $('sitestore_unlikes_' + resource_id).style.display = 'none';
          $('show_like_button_child_' + resource_id).style.display = 'none';
        }
      }
      else {
        en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
        return;
      }
    });
  }
  // FUNCTION FOR CREATING A FEEDBACK
  var createLikestore = function(resource_id, resource_type, content_type)
  {
    if ($('sitestore_most_likes_' + resource_id) && $('sitestore_most_likes_' + resource_id).style.display == 'block')
      $('sitestore_most_likes_' + resource_id).style.display = 'none';
    if ($('sitestore_unlikes_' + resource_id) && $('sitestore_unlikes_' + resource_id).style.display == 'block')
      $('sitestore_unlikes_' + resource_id).style.display = 'none';
    
    if($(resource_id))
    $(resource_id).style.display = 'none';
  
  if($('show_like_button_child_' + resource_id))
    $('show_like_button_child_' + resource_id).style.display = 'block';
    if (content_type == 'sitestore') {
      var like_id = $(content_type + '_like_' + resource_id).value
    }
    var url = '<?php echo $this->url(array('action' => 'global-likes'), 'sitestore_like', true); ?>';
    var request = new Request.JSON({
      url: url,
      data: {
        format: 'json',
        'resource_id': resource_id,
        'resource_type': resource_type,
        'like_id': like_id
      }
    });
    request.send();
    return request;
  }
</script>
<div class="layout_middle">
  <?php if ( $this->paginator->count() > 0 ): ?>
    <?php $latitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.map.latitude', 0); ?>
    <?php $longitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.map.longitude', 0); ?>
    <?php $defaultZoom = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.map.zoom', 1); ?>
    <?php //if (($this->list_view && $this->grid_view) || ($this->map_view && $this->grid_view) || ($this->list_view && $this->map_view)):  ?>
    <!--	<div class="sitestore_view_select">
        <h3 class="fleft"><?php // echo $this->translate('Stores I Like');   ?></h3>
    <?php //if( $this->enableLocation  && $this->map_view): ?>
          <span class="seaocore_tab_select_wrapper fright">
            <div class="seaocore_tab_select_view_tooltip"><?php //echo $this->translate("Map View");   ?></div>
             <span class="seaocore_tab_icon tab_icon_map_view" onclick="switchview(2)"></span>
          </span>
    <?php //endif;?>
    <?php //if( $this->grid_view): ?>
        <span class="seaocore_tab_select_wrapper fright">
          <div class="seaocore_tab_select_view_tooltip"><?php //echo $this->translate("Grid View");   ?></div>
          <span class="seaocore_tab_icon tab_icon_grid_view" onclick="switchview(1)"></span>
        </span>
    <?php //endif;?>
    <?php //if( $this->list_view): ?>
        <span class="seaocore_tab_select_wrapper fright">
          <div class="seaocore_tab_select_view_tooltip"><?php //echo $this->translate("List View");   ?></div>
          <span class="seaocore_tab_icon tab_icon_list_view" onclick="switchview(0)"></span>
        </span>
    <?php //endif; ?>
      </div>-->
    <?php //endif; ?>
    <?php if ( $this->list_view ): ?>
      <div id="grid_view" style="display: none;">
        <ul class="seaocore_browse_list">
          <?php foreach ( $this->paginator as $sitestore ): ?>
            <li>
              <div class='seaocore_browse_list_photo'>
                <?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id, $sitestore->getSlug()), $this->itemPhoto($sitestore, 'thumb.normal')) ?>
              </div>
              <div class='seaocore_browse_list_info'>
                <div class='seaocore_browse_list_info_title'>
                  <span>
                    <?php if ( $sitestore->closed ): ?>
                      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/close.png', '', array('class' => 'icon', 'title' => $this->translate('Closed'))) ?>

                    <?php endif; ?>
                    <?php if ( $sitestore->sponsored == 1 ): ?>
                      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
                    <?php endif; ?>
                    <?php if ( $sitestore->featured == 1 ): ?>
                      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
                    <?php endif; ?>
                  </span>
                  <?php if ( $this->ratngShow ): ?>
                    <?php if ( ($sitestore->rating > 0 ) ): ?>

                      <?php
                      $currentRatingValue = $sitestore->rating;
                      $difference = $currentRatingValue - ( int ) $currentRatingValue;
                      if ( $difference < .5 ) {
                        $finalRatingValue = ( int ) $currentRatingValue;
                      } else {
                        $finalRatingValue = ( int ) $currentRatingValue + .5;
                      }
                      ?>

                      <span class="list_rating_star" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
                        <?php for ( $x = 1; $x <= $sitestore->rating; $x++ ): ?>
                          <span class="rating_star_generic rating_star" ></span>
                        <?php endfor; ?>
                        <?php if ( (round($sitestore->rating) - $sitestore->rating) > 0 ): ?>
                          <span class="rating_star_generic rating_star_half" ></span>
                        <?php endif; ?>
                      <?php endif; ?>
                    </span>
                  <?php endif; ?>
                  <h3><?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id, $sitestore->getSlug()), $sitestore->getTitle()); ?></h3>
                </div>
                <div class='seaocore_browse_list_info_date'>
                  <?php echo $this->timestamp(strtotime($sitestore->creation_date)) ?> - <?php echo $this->translate('posted by'); ?>
                  <?php echo $this->htmlLink($sitestore->getOwner()->getHref(), $sitestore->getOwner()->getTitle()) ?>,
                  <?php echo $this->translate(array('%s like', '%s likes', $sitestore->like_count), $this->locale()->toNumber($sitestore->like_count)) ?>,
                  <?php if ( $this->ratngShow ): ?>
                    <?php echo $this->translate(array('%s review', '%s reviews', $sitestore->review_count), $this->locale()->toNumber($sitestore->review_count)) ?>,
                  <?php endif; ?>
                  <?php echo $this->translate(array('%s comment', '%s comments', $sitestore->comment_count), $this->locale()->toNumber($sitestore->comment_count)) ?>,
                  <?php echo $this->translate(array('%s view', '%s views', $sitestore->view_count), $this->locale()->toNumber($sitestore->view_count)) ?>

                </div>
                <?php
                $user = Engine_Api::_()->user()->getUser($sitestore->owner_id);
                $view_options = ( array ) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestore_store', $user, 'contact_detail');
                $availableLabels = array('phone' => 'Phone', 'website' => 'Website', 'email' => 'Email');
                $options_create = array_intersect_key($availableLabels, array_flip($view_options));
                ?>
                <?php
                $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'contact');
                if ( !empty($isManageAdmin) ):
                  ?>
                  <div class="seaocore_browse_list_info_date">
                    <?php if ( isset($options_create['phone']) && $options_create['phone'] == 'Phone' ): ?><?php if ( $sitestore->phone ): ?>
                      <?php echo $this->translate('Phone: '); ?><?php echo $sitestore->phone ?><?php endif; ?><?php endif; ?><?php if ( isset($options_create['email']) && $options_create['email'] == 'Email' ): ?><?php if ( $sitestore->email ): ?><?php if ( !empty($sitestore->phone) && in_array("Phone", $options_create) ): ?>, <?php endif; ?><?php echo $this->translate('Email: '); ?><a href='mailto:<?php echo $sitestore->email ?>'><?php echo $sitestore->email ?></a><?php endif; ?><?php endif; ?><?php if ( isset($options_create['website']) && $options_create['website'] == 'Website' ): ?><?php if ( $sitestore->website ): ?><?php if ( ($sitestore->email && in_array("Email", $options_create)) || !empty($sitestore->phone) && in_array("Phone", $options_create) ): ?>,&nbsp;<?php endif; ?><?php echo $this->translate('Website: '); ?><?php if ( strstr($sitestore->website, 'http://') || strstr($sitestore->website, 'https://') ): ?><a href='<?php echo $sitestore->website ?>' target="_blank"><?php echo $sitestore->website ?></a><?php else: ?><a href='http://<?php echo $sitestore->website ?>' target="_blank"><?php echo $sitestore->website ?></a><?php endif; ?><?php endif; ?><?php endif; ?>
                  </div>
                <?php endif; ?>
                  <?php if ( (!empty($sitestore->location) && $this->enableLocation) || (!empty($sitestore->price) && $this->enablePrice) ): ?>
                  <div class="seaocore_browse_list_info_date"><?php if ( !empty($sitestore->price) && $this->enablePrice ): ?><?php echo $this->translate("Price: ");
            echo Engine_Api::_()->sitestore()->getPriceWithCurrency($sitestore->price);
                      ?><?php endif; ?><?php if ( (!empty($sitestore->location) && $this->enableLocation) && (!empty($sitestore->price) && $this->enablePrice) ): ?><?php echo $this->translate(", "); ?>
                    <?php endif; ?>
                    <?php if ( !empty($sitestore->location) && $this->enableLocation ): ?>
                        <?php echo $this->translate("Location: ");
                        echo $this->translate($sitestore->location);
                        ?>&nbsp;-
                      <b><?php
              $locationId = Engine_Api::_()->getDbTable('locations', 'sitestore')->getLocationId($sitestore->store_id, $sitestore->location);
              echo $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $sitestore->store_id, 'resouce_type' => 'sitestore_store', 'location_id' => $locationId, 'flag' => 'map'), $this->translate("Get Directions"), array('class' => 'smoothbox'));
              ?></b>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
                <div class='seaocore_browse_list_info_blurb'>
                <?php echo $this->viewMore(nl2br($sitestore->body), 200, 5000) ?>
                </div>

            <?php if ( !empty($this->sitestoreOfferEnabled) && !empty($sitestore->offer) ): ?>
              <?php echo $sitestore->getOffer(); ?>
      <?php endif; ?>
              </div>
            </li>
      <?php endforeach; ?>
        </ul>
      </div>
        <?php endif; ?>
        <?php if ( $this->grid_view ): ?>
      <div id="image_view" style="display: none;">
        <div class="sitestore_img_view">
          <?php $counter = 1;
          foreach ( $this->paginator as $sitestore ):
            ?>
            <?php
            $likeStore = false;
            if ( !empty($viewer_id) ):
              $likeStore = Engine_Api::_()->sitestore()->hasStoreLike($sitestore->store_id, $viewer_id);
            endif;
            ?>
            <div class="sitestore_browse_thumb <?php if ( $likeStore ): ?> sitestore_browse_liked <?php endif; ?>" id = "backgroundcolor_<?php echo $sitestore->store_id; ?>" >
              <div class="sitestore_browse_thumb_list" <?php if ( !empty($viewer_id) ) : ?> onmouseOver="$('like_<?php echo $sitestore->getIdentity(); ?>').style.display = 'block';
            if ($('<?php echo $sitestore->getIdentity(); ?>').style.display == 'none')
              $('<?php echo $sitestore->getIdentity(); ?>').style.display = 'block';"  onmouseout="$('like_<?php echo $sitestore->getIdentity(); ?>').style.display = 'none';
            $('<?php echo $sitestore->getIdentity(); ?>').style.display = 'none';" <?php endif; ?> >
                <a href="javascript:void(0);">
      <?php
      $url = $this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/nophoto_store_thumb_profile.png';
      $temp_url = $sitestore->getPhotoUrl('thumb.profile');
      if ( !empty($temp_url) ): $url = $sitestore->getPhotoUrl('thumb.profile');
      endif;
      ?>
                  <span style="background-image: url(<?php echo $url; ?>);"> </span>
                </a>

                    <?php if ( !empty($viewer_id) ) : ?>
                  <div id="like_<?php echo $sitestore->getIdentity() ?>" style="display:none;">
                      <?php $RESOURCE_ID = $sitestore->getIdentity(); ?>
                    <div class="" style="display:none;" id="<?php echo $RESOURCE_ID; ?>">
                      <?php
                      // Check that for this 'resurce type' & 'resource id' user liked or not.
                      $check_availability = Engine_Api::_()->$MODULE_NAME()->checkAvailability($RESOURCE_TYPE, $RESOURCE_ID);
                      if ( !empty($check_availability) ) {
                        $label = 'Unlike this';
                        $unlike_show = "display:block;";
                        $like_show = "display:none;";
                        $like_id = $check_availability[0]['like_id'];
                      } else {
                        $label = 'Like this';
                        $unlike_show = "display:none;";
                        $like_show = "display:block;";
                        $like_id = 0;
                      }
                      //}
                      ?>
                      <div class="sitestore_browse_thumb_hover_color"></div>

                      <div class="seaocore_like_button sitestore_browse_thumb_hover_unlike_button" id="sitestore_unlikes_<?php echo $RESOURCE_ID; ?>" style ='<?php echo $unlike_show; ?>' >
                        <a href = "javascript:void(0);" onclick = "sitestores_likes('<?php echo $RESOURCE_ID; ?>', 'sitestore_store');">
                          <i class="seaocore_like_thumbdown_icon"></i>
                          <span><?php echo $this->translate('Unlike') ?></span>
                        </a>
                      </div>

                      <div class="seaocore_like_button sitestore_browse_thumb_hover_like_button" id="sitestore_most_likes_<?php echo $RESOURCE_ID; ?>" style ='<?php echo $like_show; ?>'>
                        <a href = "javascript:void(0);" onclick = "sitestores_likes('<?php echo $RESOURCE_ID; ?>', 'sitestore_store');">
                          <i class="seaocore_like_thumbup_icon"></i>
                          <span><?php echo $this->translate('Like') ?></span>
                        </a>
                      </div>

                      <input type ="hidden" id = "sitestore_like_<?php echo $RESOURCE_ID; ?>" value = '<?php echo $like_id; ?>' />

                    </div>
                  </div>
                  <div id = "show_like_button_child_<?php echo $RESOURCE_ID; ?>" style="display:none;" >
                    <div class="sitestore_browse_thumb_hover_color"></div>
                    <div class="sitestore_browse_thumb_hover_loader">
                      <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/loader.gif" class="mtop5" />
                    </div>
                  </div>
              <?php endif; ?>
              <?php if ( $sitestore->featured == 1 ): ?>
                  <span class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured') ?>"><?php echo $this->translate('Featured')?></span>
                <?php endif; ?>
                <div class="sitestore_browse_title">
              <?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id, $sitestore->getSlug()), Engine_Api::_()->sitestore()->truncation($sitestore->getTitle(), 15)); ?>
                </div>	
              </div>
                <?php if ( $sitestore->sponsored == 1 ): ?>
                <div class="seaocore_list_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.color', '#fc0505'); ?>;'>
                  <?php echo $this->translate('SPONSORED'); ?>                 
                </div>
                  <?php endif; ?>

              <div class='sitestore_browse_thumb_info'>
                  <?php if ( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember') ) : ?>
                    <?php
                    $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('storemember.member.title', 1);
                    if ( $sitestore->member_title && $memberTitle ) :
                      ?>
                    <div class="member_count">
                      <?php if ( $sitestore->member_count == 1 ) : ?>
                      <?php echo $sitestore->member_count . ' ' . ucfirst('member'); ?> 
                    <?php else: ?>
                      <?php echo $sitestore->member_count . ' ' . ucfirst($sitestore->member_title); ?>
          <?php endif; ?>
                    </div>
                    <?php else : ?>
                    <div class="member_count">
                      <?php echo $this->translate(array('%s ' . ucfirst('member'), '%s ' . ucfirst('members'), $sitestore->member_count), $this->locale()->toNumber($sitestore->member_count)) ?>
                    </div> 
        <?php endif; ?>
                  <?php endif; ?>


                <div class='sitestore_browse_thumb_stats seaocore_txt_light'>
                  <?php echo $this->timestamp(strtotime($sitestore->creation_date)) ?> - <?php echo $this->translate('posted by'); ?>
                  <?php echo $this->htmlLink($sitestore->getOwner()->getHref(), $sitestore->getOwner()->getTitle()) ?>
                </div>

                <div class='sitestore_browse_thumb_stats seaocore_txt_light'>
                <?php echo $this->translate(array('%s like', '%s likes', $sitestore->like_count), $this->locale()->toNumber($sitestore->like_count)) ?>,
                  <?php if ( $this->ratngShow ): ?>
                    <?php echo $this->translate(array('%s review', '%s reviews', $sitestore->review_count), $this->locale()->toNumber($sitestore->review_count)) ?>,
                  <?php endif; ?>
                <?php echo $this->translate(array('%s comment', '%s comments', $sitestore->comment_count), $this->locale()->toNumber($sitestore->comment_count)) ?>,
                <?php echo $this->translate(array('%s view', '%s views', $sitestore->view_count), $this->locale()->toNumber($sitestore->view_count)) ?>

                </div>
            <?php if ( !empty($sitestore->price) && $this->enablePrice ): ?>
                  <div class='sitestore_browse_thumb_stats seaocore_txt_light'>
          <?php echo $this->translate("Price: ");
          echo Engine_Api::_()->sitestore()->getPriceWithCurrency($sitestore->price);
          ?>
                  </div>
          <?php endif; ?>
              </div>
            </div>
    <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
    <div id="map_canvas_view" style="display: none;">
      <div id="map_canvas"> </div>
  <?php if ( $this->enableLocation && $this->flageSponsored && $this->map_view && $enableBouce ): ?>
        <a href="javascript:void(0);" onclick="toggleBounce()" class="stop_bounce_link"> <?php echo $this->translate('Stop Bounce'); ?></a>
        <?php endif; ?>
    </div>
    <div class="clr"></div>
        <?php echo $this->paginationControl($this->paginator); ?>
<?php else: ?>
    <div class="sitestore_view_select">
      <h3 class="fleft"><?php echo $this->translate('Stores I Like'); ?></h3>
    </div>
    <div class="tip">
      <span>
  <?php
  $translatestore = "<a href=" . $this->url(array('action' => 'index'), 'sitestore_general', true) . ">" . $this->translate("Explore stores") . "</a>";
  echo $this->translate("You have not liked any stores yet. %s and Like the stores you like.", $translatestore);
  ?>
      </span>
    </div>
<?php endif; ?>
</div>
<style type="text/css">
  #map_canvas {
    width: 100% !important;
    height: 400px;
    float: left;
  }
  #map_canvas > div{
    height: 500px;
  }
  #infoPanel {
    float: left;
    margin-left: 10px;
  }
  #infoPanel div {
    margin-bottom: 5px;
  }
</style>

<script type="text/javascript" >
  function switchview(flage) {
    if (flage == 2) {
      if ($('map_canvas_view')) {
        $('map_canvas_view').style.display = 'block';
<?php if ( $this->enableLocation && $this->map_view && $this->paginator->count() > 0 ): ?>
          google.maps.event.trigger(map, 'resize');
          map.setZoom(<?php echo $defaultZoom; ?>);
          map.setCenter(new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude; ?>));
<?php endif; ?>
        if ($('grid_view'))
          $('grid_view').style.display = 'none';
        if ($('image_view'))
          $('image_view').style.display = 'none';
      }
    } else if (flage == 1) {
      if ($('image_view')) {
        if ($('map_canvas_view'))
          $('map_canvas_view').style.display = 'none';
        if ($('grid_view'))
          $('grid_view').style.display = 'none';
        $('image_view').style.display = 'block';
      }
    } else {
      if ($('grid_view')) {
        if ($('map_canvas_view'))
          $('map_canvas_view').style.display = 'none';
        $('grid_view').style.display = 'block';
        if ($('image_view'))
          $('image_view').style.display = 'none';
      }
    }
  }

  /* moo style */
  en4.core.runonce.add(function() {
    //opacity / display fix
    $$('.sitestore_tooltip').setStyles({
      opacity: 0,
      display: 'block'
    });
    //put the effect in place
    $$('.jq-sitestore_tooltip li').each(function(el, i) {
      el.addEvents({
        'mouseenter': function() {
          el.getElement('div').fade('in');
        },
        'mouseleave': function() {
          el.getElement('div').fade('out');
        }
      });
    });
<?php if ( $this->paginator->count() > 0 ): ?>
  <?php if ( $this->enableLocation && $this->map_view ): ?>
        initialize();
  <?php endif; ?>
      //  $('grid_view').style.display='none';
      switchview(<?php echo $this->defaultView ?>);
<?php endif; ?>
  });
</script>

<?php if ( $this->enableLocation && $this->map_view && $this->paginator->count() > 0 ): ?>
<?php
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>

  <script type="text/javascript">
    //<![CDATA[
    // this variable will collect the html which will eventually be placed in the side_bar
    var side_bar_html = "";

    // arrays to hold copies of the markers and html used by the side_bar
    // because the function closure trick doesnt work there
    var gmarkers = [];

    // global "map" variable
    var map = null;
    // A function to create the marker and set up the event window function
    function createMarker(latlng, name, html, title_store) {
      var contentString = html;
      if (name == 0) {
        var marker = new google.maps.Marker({
          position: latlng,
          map: map,
          title: title_store,
          animation: google.maps.Animation.DROP,
          zIndex: Math.round(latlng.lat() * -100000) << 5
        });
      }
      else {
        var marker = new google.maps.Marker({
          position: latlng,
          map: map,
          title: title_store,
          draggable: false,
          animation: google.maps.Animation.BOUNCE
        });
      }
      gmarkers.push(marker);
      google.maps.event.addListener(marker, 'click', function() {
        infowindow.setContent(contentString);
        google.maps.event.trigger(map, 'resize');

        infowindow.open(map, marker);

      });
    }

    function initialize() {

      // create the map
      var myOptions = {
        zoom: <?php echo $defaultZoom; ?>,
        center: new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>),
        //  mapTypeControl: true,
        // mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
        navigationControl: true,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      }
      map = new google.maps.Map(document.getElementById("map_canvas"),
              myOptions);

      google.maps.event.addListener(map, 'click', function() {

        infowindow.close();
        google.maps.event.trigger(map, 'resize');
      });

  <?php foreach ( $this->locations as $location ) : ?>
        // obtain the attribues of each marker
        var lat = <?php echo $location->latitude ?>;
        var lng =<?php echo $location->longitude ?>;
        var point = new google.maps.LatLng(lat, lng);
    <?php if ( !empty($enableBouce) ): ?>
          var sponsored = <?php echo $this->sitestore[$location->store_id]->sponsored ?>
    <?php else: ?>
          var sponsored = 0;
    <?php endif; ?>
        // create the marker

    <?php $store_id = $this->sitestore[$location->store_id]->store_id; ?>
        var contentString = '<div id="content">' +
                '<div id="siteNotice">' +
                '</div>' + '  <ul class="sitestores_locationdetails"><li>' +
                '<div class="sitestores_locationdetails_info_title">' +
                '<a href="<?php echo $this->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($store_id)), 'sitestore_entry_view', true) ?>">' + "<?php echo $this->string()->escapeJavascript($this->sitestore[$location->store_id]->getTitle()); ?>" + '</a>' +
                '<div class="fright">' +
                '<span >' +
    <?php if ( $this->sitestore[$location->store_id]->featured == 1 ): ?>
          '<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->string()->escapeJavascript($this->translate('Featured')))) ?>' + <?php endif; ?>
        '</span>' +
                '<span>' +
    <?php if ( $this->sitestore[$location->store_id]->sponsored == 1 ): ?>
          '<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->string()->escapeJavascript($this->translate('Sponsored')))) ?>' +
    <?php endif; ?>
        '</span>' +
                '</div>' +
                '<div class="clr"></div>' +
                '</div>' +
                '<div class="sitestores_locationdetails_photo" >' +
                '<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->sitestore[$location->store_id]->store_id, $this->sitestore[$location->store_id]->owner_id, $this->sitestore[$location->store_id]->getSlug()), $this->itemPhoto($this->sitestore[$location->store_id], 'thumb.normal')) ?>' +
                '</div>' +
                '<div class="sitestores_locationdetails_info">' +
    <?php if ( $this->ratngShow ): ?>
      <?php if ( ($this->sitestore[$location->store_id]->rating > 0 ) ): ?>
            '<span class="clr">' +
        <?php for ( $x = 1; $x <= $this->sitestore[$location->store_id]->rating; $x++ ): ?>
              '<span class="rating_star_generic rating_star"></span>' +
        <?php endfor; ?>
        <?php if ( (round($this->sitestore[$location->store_id]->rating) - $this->sitestore[$location->store_id]->rating) > 0 ): ?>
              '<span class="rating_star_generic rating_star_half"></span>' +
        <?php endif; ?>
            '</span>' +
      <?php endif; ?>
    <?php endif; ?>

        '<div class="sitestores_locationdetails_info_date">' +
                '<?php echo $this->timestamp(strtotime($this->sitestore[$location->store_id]->creation_date)) ?> - <?php echo $this->string()->escapeJavascript($this->translate('posted by123')); ?> ' +
                '<?php echo $this->htmlLink($this->sitestore[$location->store_id]->getOwner()->getHref(), $this->string()->escapeJavascript($this->sitestore[$location->store_id]->getOwner()->getTitle())) ?>' +
                '</div>' +
                '<div class="sitestores_locationdetails_info_date">' +
                '<?php echo $this->string()->escapeJavascript($this->translate(array('%s like', '%s likes', $this->sitestore[$location->store_id]->like_count), $this->locale()->toNumber($this->sitestore[$location->store_id]->like_count))) ?>,&nbsp;' +
    <?php if ( $this->ratngShow ): ?>
          '<?php echo $this->string()->escapeJavascript($this->translate(array('%s review', '%s reviews', $this->sitestore[$location->store_id]->review_count), $this->locale()->toNumber($this->sitestore[$location->store_id]->review_count))) ?>,&nbsp;' +
    <?php endif; ?>
        '<?php echo $this->string()->escapeJavascript($this->translate(array('%s comment', '%s comments', $this->sitestore[$location->store_id]->comment_count), $this->locale()->toNumber($this->sitestore[$location->store_id]->comment_count))) ?>,&nbsp;' +
                '<?php echo $this->string()->escapeJavascript($this->translate(array('%s view', '%s views', $this->sitestore[$location->store_id]->view_count), $this->locale()->toNumber($this->sitestore[$location->store_id]->view_count))) ?>' +
                '</div>' +
                '<div class="sitestores_locationdetails_info_date">' +
    <?php if ( !empty($this->sitestore[$location->store_id]->phone) ): ?>
          "<?php echo $this->string()->escapeJavascript($this->translate("Phone: ")) . $this->sitestore[$location->store_id]->phone ?><br />" +
    <?php endif; ?>
    <?php if ( !empty($this->sitestore[$location->store_id]->email) ): ?>
          "<?php echo $this->string()->escapeJavascript($this->translate("Email: ")) . $this->sitestore[$location->store_id]->email ?><br />" +
    <?php endif; ?>
    <?php if ( !empty($this->sitestore[$location->store_id]->website) ): ?>
          "<?php echo $this->string()->escapeJavascript($this->translate("Website: ")) . $this->sitestore[$location->store_id]->website ?>" +
    <?php endif; ?>
        '</div>' +
    <?php if ( $this->sitestore[$location->store_id]->price && $this->enablePrice ): ?>
          '<div class="sitestores_locationdetails_info_date">' +
                  "<?php echo $this->string()->escapeJavascript($this->translate("Price: "));
      echo Engine_Api::_()->sitestore()->getPriceWithCurrency($this->sitestore[$location->store_id]->price)
      ?>" +
                  '</div>' +
    <?php endif; ?>
        '<div class="sitestores_locationdetails_info_date">' +
                "<?php $this->translate("Location: ");
    echo $this->string()->escapeJavascript($location->location);
    ?>" +
                '</div>' +
                '</div>' +
                '<div class="clr"></div>' +
                ' </li></ul>' +
                '</div>';

        var marker = createMarker(point, sponsored, contentString, "<?php echo str_replace('"', ' ', $this->sitestore[$location->store_id]->getTitle()); ?>");
  <?php endforeach; ?>

    }

    var infowindow = new google.maps.InfoWindow(
            {
              size: new google.maps.Size(250, 50)
            });

    function toggleBounce() {
      for (var i = 0; i < gmarkers.length; i++) {
        if (gmarkers[i].getAnimation() != null) {
          gmarkers[i].setAnimation(null);
        }
      }
    }
  </script>
<?php endif; ?>
