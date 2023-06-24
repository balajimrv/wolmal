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
//   if(in_array('viewRating', $this->statistics))
?>
 

<?php $statistics = Zend_Json_Encoder::encode($this->statistics);?>
<?php
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php
include APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/Adintegration.tpl';
?>
<?php if (empty($this->isajax)) : ?>
  <div id="id_<?php echo $this->content_id; ?>">
  <?php endif; ?>
  <?php $viewer_id = $this->viewer->getIdentity(); ?>
  <?php if (!empty($viewer_id)): ?>
    <?php $oldTz = date_default_timezone_get(); ?>
    <?php date_default_timezone_set($this->viewer->timezone); ?>
  <?php endif; ?>


  <script type="text/javascript" >
    function owner(thisobj) {
      var Obj_Url = thisobj.href;
      Smoothbox.open(Obj_Url);
    }

    function getPopup(popupPath) {
      Smoothbox.open(popupPath);
    }
  </script>

  <?php if (!empty($this->show_content)) : ?>
    <?php if ($this->showtoptitle == 1): ?>
      <div class="layout_simple_head" id="layout_offer" style="display:none;">	
        <?php echo $this->translate($this->sitestore->getTitle()); ?><?php echo $this->translate("'s Coupons"); ?>
      </div>
    <?php endif; ?>
    <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adofferwidget', 3) && $store_communityad_integration && Engine_Api::_()->sitestore()->showAdWithPackage($this->sitestore)): ?>
      <div class="layout_right" id="communityad_offer">
				<?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adofferwidget', 3),"loaded_by_ajax"=>1,'widgetId'=>"store_coupons"))?>
      </div>
      <div class="layout_middle">
      <?php endif; ?>


      <?php if ($this->can_create_offer): ?>
        <div class="seaocore_add">
          <?php
          echo $this->htmlLink(array('route' => 'sitestoreoffer_general', 'action' => 'create', 'store_id' => $this->sitestore->store_id, 'tab' => $this->identity_temp), $this->translate('Add Coupon'), array(
              'class' => 'buttonlink seaocore_icon_create',
          ))
          ?>
        </div>
      <?php endif; ?>

        <?php if (count($this->paginator) > 0): ?>
        <ul class="sitestore_profile_list">
          <?php foreach ($this->paginator as $item): ?>
              <?php if ($item->sticky == 1): ?>
              <li class="sitestoreoffer_show">
              <?php else: ?>
              <li class="sitestore_offer_block">
                <?php endif; ?>
              <div class="sitestore_offer_photo">
                <?php if (!empty($item->photo_id)): ?>
                  <?php echo $this->htmlLink(array('route' => 'sitestoreoffer_view', 'user_id' => $item->owner_id, 'offer_id' => $item->offer_id, 'tab' => $this->identity_temp, 'slug' => $item->getOfferSlug($item->title)), $this->itemPhoto($item, 'thumb.normal')) ?>
                <?php else: ?>
                  <?php echo $this->htmlLink(array('route' => 'sitestoreoffer_view', 'user_id' => $item->owner_id, 'offer_id' => $item->offer_id, 'tab' => $this->identity_temp, 'slug' => $item->getOfferSlug($item->title)), "<img class='thumb_normal' src='" . $this->layout()->staticBaseUrl . "application/modules/Sitestoreoffer/externals/images/nophoto_offer_thumb_normal.png' alt='' />") ?>
      <?php endif; ?>
              </div>  
              <div class='sitestore_profile_list_options'>
                <?php
                //echo $this->htmlLink(array('route' => 'sitestoreoffer_view', 'user_id' => $item->owner_id, 'offer_id' => $item->offer_id, 'tab' => $this->identity_temp, 'slug' => $item->getOfferSlug($item->title)), $this->translate('View Coupon'), array(
                   // 'class' => 'buttonlink item_icon_sitestoreoffer_offer'
                //))
                ?>
                <?php if ($this->can_create_offer): ?>
                  <?php
                  echo $this->htmlLink(array('route' => 'sitestoreoffer_general', 'action' => 'edit', 'store_id' => $this->sitestore->store_id, 'offer_id' => $item->offer_id, 'tab' => $this->identity_temp), $this->translate('Edit Coupon'), array(
                      'class' => 'buttonlink seaocore_icon_edit'
                  ))
                  ?>	
                  <?php
                  echo $this->htmlLink(array('route' => 'sitestoreoffer_general', 'action' => 'delete', 'store_id' => $this->sitestore->store_id, 'offer_id' => $item->offer_id, 'tab' => $this->identity_temp), $this->translate('Delete Coupon'), array(
                      'class' => 'buttonlink seaocore_icon_delete',
                  ))
                  ?>

                  <?php //if ($item->sticky == 1): ?>
                    <?php //echo $this->htmlLink(array('route' => 'sitestoreoffer_general', 'action' => 'sticky', 'offer_id' => $item->offer_id, 'store_id' => $item->store_id, 'tab' => $this->identity_temp), $this->translate('Remove as Featured'), array(
                        //'onclick' => 'owner(this);return false', ' class' => 'buttonlink seaocore_icon_unfeatured'))
                    ?>
                  <?php// else: ?>
                    <?php
                   // echo $this->htmlLink(array('route' => 'sitestoreoffer_general', 'action' => 'sticky', 'offer_id' => $item->offer_id, 'store_id' => $item->store_id, 'tab' => $this->identity_temp), $this->translate('Make Featured'), array(
                        //'onclick' => 'owner(this);return false', ' class' => 'buttonlink seaocore_icon_featured')
                   // )
                    ?>
                  <?php //endif; ?>
                <?php if (!empty($item->status)): ?>
                <a class='buttonlink seaocore_icon_disapproved' href='javascript:void(0)' onclick='Smoothbox.open("<?php echo $this->url(array('module' => 'sitestoreoffer', 'controller' => 'index','action' => 'enable-disable', 'offer_id' => $item->offer_id,'store_id' => $item->store_id, 'tab' => $this->identity_temp, 'status' => $item->status), 'default', true); ?>")'><?php echo $this->translate('Disable Coupon '); ?></a>		
                  <?php else: ?>
                <a class='buttonlink seaocore_icon_approved' href='javascript:void(0)' onclick='Smoothbox.open("<?php echo $this->url(array('module' => 'sitestoreoffer', 'controller' => 'index','action' => 'enable-disable', 'offer_id' => $item->offer_id,'store_id' => $item->store_id, 'tab' => $this->identity_temp, 'status' => $item->status), 'default', true); ?>")'><?php echo $this->translate('Enable Coupon '); ?></a>	
                  <?php endif; ?>
                <?php endif; ?>
                
                <?php echo $this->htmlLink(array('route' => 'sitestoreoffer_general', 'action' => 'print', 'offer_id' => $item->offer_id, 'store_id' => $item->store_id), $this->translate('Print Coupon'), array('target' => '_blank', ' class' => 'buttonlink icon_sitestores_print')) ?>
                

                <?php
               // if (!empty($this->is_moduleEnabled)) {
                 // Engine_Api::_()->getApi('suggestion', 'sitestore')->deleteSuggestion($this->viewer->getIdentity(), 'store_offer', $item->offer_id, 'store_offer_suggestion');
                //}
                //if (!empty($this->offerSuggLink)):
                  ?>		
                      <?php
                      //$link = $this->url(array('module' => 'suggestion', 'controller' => 'index', 'action' => 'popups', 'sugg_id' => $item->offer_id, 'sugg_type' => 'store_offer'), 'default', true);

                      //echo '<a href="javascript:void(0);" onclick = "getPopup(\'' . $link . '\');" class ="buttonlink icon_suggestion">' . $this->translate('Suggest to Friends') . '</a>';
                      ?>
                    <?php //endif; ?>
              </div>

              <div class='sitestore_profile_list_info'>
                <div class='sitestore_profile_list_title'>
                  <h3> 
                  <?php if (!empty($item->hotoffer)): ?>
                    <span class="fleft">
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/icons/hot-offer.png', '', array('class' => 'icon', 'title' => $this->translate('Hot Coupon'))) ?>&nbsp;
                    </span>
                  <?php endif; ?>
                  <?php echo $this->htmlLink(array('route' => 'sitestoreoffer_view', 'user_id' => $item->owner_id, 'offer_id' => $item->offer_id, 'tab' => $this->identity_temp, 'slug' => $item->getOfferSlug($item->title)), $item->title, array('title' => $item->title)) ?>
                  </h3>
                </div>

                <div class="sitestore_offer_date">
                  <?php if (in_array('startdate', $this->statistics)): ?>
                    <div class="sitestore_offer_date">
                      <span><?php echo $this->translate('Start date') . ":"; ?></span>
                      <span><?php echo $this->timestamp(strtotime($item->start_time)) ?></span>
                    </div>
                  <?php endif; ?>
                  
                  <?php if (in_array('enddate', $this->statistics)): ?>
                    <div class="sitestore_offer_date">
                      <span><?php echo $this->translate('End date') . ":"; ?></span>
                      <?php if ($item->end_settings == 1): ?><span><?php echo $this->timestamp(strtotime($item->end_time)) ?></span><?php else: ?><span><?php echo $this->translate('Never Expires'); ?></span><?php endif; ?>
                    </div>
                  <?php endif; ?>
                  
                  <?php if (in_array('minpurchase', $this->statistics) && !empty($item->minimum_purchase)): ?>
                    <div class="sitestore_offer_date">
                      <span><?php echo $this->translate('Minimum Purchase') . ":"; ?></span>
                      <span><?php echo $item->minimum_purchase; ?></span>
                    </div>
                  <?php endif; ?> 
                  
                  <?php if (in_array('couponurl', $this->statistics)): ?>
                    <?php if (!empty($this->enable_url) && !empty($item->url)): ?>
                    <div class="sitestore_offer_date">
                      <span><?php echo $this->translate('URL') . ':'; ?></span>
                      <span><a href="<?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $item->url; ?>" target="_blank" title="<?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $item->url ?>"><?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $item->url ?></a></span> 
                    </div> 
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
                
                <div class="sitestore_offer_stats ">
                    <?php if (in_array('couponcode', $this->statistics)): ?>
                      <span class="sitestore_offer_stat sitestorecoupon_code sitestorecoupon_tip_wrapper">
                        <span class="sitestorecoupon_tip">
                          <span><?php echo $this->translate('Select and Copy Code to use');?></span>
                          <i></i>
                        </span>
                        <input type="text" value="<?php echo $item->coupon_code; ?>" class="sitestorecoupon_code_num" onclick="this.select()" readonly>
                      </span>
                    <?php endif; ?>
                    
                    <span class="sitestore_offer_stat sitestore_offer_discount sitestorecoupon_tip_wrapper">
                      <?php if (in_array('discount', $this->statistics)): ?>
                      <span class="sitestorecoupon_tip">
                        <span><?php echo $this->translate('Coupon Discount Value');?></span>
                        <i></i>
                      </span>
                      <?php if(!empty($item->discount_type)):
                        $priceStr = Engine_Api::_()->sitestoreoffer()->getCurrencySymbolPrice($item->discount_amount);?>
                        <span class="discount_value"><?php echo $priceStr; ?></span>&nbsp;&nbsp;
                      <?php else:?>
                        <span class="discount_value"><?php echo $item->discount_amount . '%'; ?></span>&nbsp;&nbsp;
                      <?php endif;?>
                      <?php endif;?>
                    </span>
                    
                  <?php $today = date("Y-m-d H:i:s"); ?>
                  <?php if(in_array('expire', $this->statistics) && !empty($item->end_settings) && $item->end_time < $today):?>
                        <span class="sitestorecoupon_stat sitestorecoupon_left fright">
                          <b><?php echo $this->translate('Expired'); ?></b>
                        </span>
                     	<?php //endif; ?>
                    
                  <?php elseif(in_array('claim', $this->statistics)):?>
                   <?php //echo '<span class="sitestorecoupon_stat sitestorecoupon_left fright">' . $item->claimed . ' ' . $this->translate('Used') . '</span>'; ?>
                    <?php if ($item->claim_count != -1): ?>
                      <?php $item->claim_count  = $item->claim_count - $item->claimed ;?>
                      <span class="sitestorecoupon_stat sitestorecoupon_left fright">
                        <?php //echo $this->translate(array('%1$s Left', '%1$s Left', $item->claim_count), $this->locale()->toNumber($item->claim_count)) ?>
                        
                        <?php echo $this->translate(array('%1$s coupon left', '%1$s coupons left', $item->claim_count), $this->locale()->toNumber($item->claim_count)) ?>                        
                        
                      </span>
                    <?php else : ?>
                      <span class="sitestorecoupon_stat sitestorecoupon_left fright"><?php echo $this->translate('Unlimited Use') ?></span>
                    <?php endif;?>
            			<?php endif; ?>	
                  </div>
               	
                <?php //if ($item->end_settings == 1 && ($item->end_time < $today)): ?>
                  <!--<div class="tip" id='sitestorenoffer_search'>-->
                    <!--<span>-->
                      <?php //echo $this->translate('This coupon has expired.'); ?>
                    <!--</span>-->
                  <!--</div>--> 
                <?php //endif; ?>
               <div class="sitestore_offer_stats">
                  <?php echo nl2br($item->description);?>
               </div>
              </div>
            </li>
            <?php endforeach; ?>
        </ul>
            <?php if ($this->paginator->count() > 1): ?>
          <div>
            <?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
              <div id="user_sitestore_members_previous" class="paginator_previous">
            <?php
            echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                'onclick' => 'paginateSitestoreOffers(sitestoreOfferStore - 1)',
                'class' => 'buttonlink icon_previous'
            ));
            ?>
              </div>
              <?php endif; ?>
      <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
              <div id="user_sitestore_members_next" class="paginator_next">
            <?php
            echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
                'onclick' => 'paginateSitestoreOffers(sitestoreOfferStore + 1)',
                'class' => 'buttonlink_right icon_next'
            ));
            ?>
              </div>
        <?php endif; ?>
          </div>
      <?php endif; ?>
  <?php else: ?>
        <div class="tip" id='sitestoreoffer_search'>
          <span>
    <?php echo $this->translate('No coupons have been created in this Store yet.'); ?>
    <?php if ($this->can_create_offer): ?>
      <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'create', 'store_id' => $this->sitestore->store_id, 'tab' => $this->identity_temp), 'sitestoreoffer_general') . '">', '</a>'); ?>
    <?php endif; ?>
          </span>
        </div>

  <?php endif; ?>


  <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adofferwidget', 3) && $store_communityad_integration && Engine_Api::_()->sitestore()->showAdWithPackage($this->sitestore)): ?>
      </div>
  <?php endif; ?>
<?php endif; ?>

<?php if (empty($this->isajax)) : ?>
  </div>
<?php endif; ?>
<?php if (!empty($viewer_id)): ?>
  <?php date_default_timezone_set($oldTz); ?>
<?php endif; ?>
<script type="text/javascript">
  var adwithoutpackage = '<?php echo Engine_Api::_()->sitestore()->showAdWithPackage($this->sitestore) ?>';
  var offer_ads_display = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adofferwidget', 3); ?>';
  var is_ajax_divhide = '<?php echo $this->isajax; ?>';
  var execute_Request_Offer = '<?php echo $this->show_content; ?>';
  var sitestoreOfferStore = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
  var show_widgets = '<?php echo $this->widgets ?>';
  var store_communityad_integration = '<?php echo $store_communityad_integration; ?>';
  //window.addEvent('domready', function () {
  var OffertabId = '<?php echo $this->module_tabid; ?>';
  var OfferTabIdCurrent = '<?php echo $this->identity_temp; ?>';
  if (OfferTabIdCurrent == OffertabId) {
    if (store_showtitle != 0) {
      if ($('profile_status') && show_widgets == 1) {
        $('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitestore->getTitle()) ?><?php echo $this->translate(' &raquo; '); ?><?php echo $this->translate('Coupons'); ?></h2>";
      }
      if ($('layout_offer')) {
        $('layout_offer').style.display = 'block';
      }
    }
    hideWidgetsForModule('sitestoreoffer');
    prev_tab_id = '<?php echo $this->content_id; ?>';
    prev_tab_class = 'layout_sitestoreoffer_profile_sitestoreoffers';
    execute_Request_Offer = true;
    hideLeftContainer(offer_ads_display, store_communityad_integration, adwithoutpackage);
  }
  else if (is_ajax_divhide != 1) {
    if ($('global_content').getElement('.layout_sitestoreoffer_profile_sitestoreoffers')) {
      $('global_content').getElement('.layout_sitestoreoffer_profile_sitestoreoffers').style.display = 'none';
    }
  }
  //});

  $$('.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function() {
    $('global_content').getElement('.layout_sitestoreoffer_profile_sitestoreoffers').style.display = 'block';
    if (store_showtitle != 0) {
      if ($('profile_status') && show_widgets == 1) {
        $('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitestore->getTitle()) ?><?php echo $this->translate(' &raquo; '); ?><?php echo $this->translate('Coupons'); ?></h2>";
      }
    }
    hideWidgetsForModule('sitestoreoffer');
    $('id_' + <?php echo $this->content_id ?>).style.display = "block";
    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '<?php echo $this->content_id; ?>') {
      $$('.' + prev_tab_class).setStyle('display', 'none');
    }

    if (prev_tab_id != '<?php echo $this->content_id; ?>') {
      execute_Request_Offer = false;
      prev_tab_id = '<?php echo $this->content_id; ?>';
      prev_tab_class = 'layout_sitestoreoffer_profile_sitestoreoffers';
    }
    if (execute_Request_Offer == false) {
      ShowContent('<?php echo $this->content_id; ?>', execute_Request_Offer, '<?php echo $this->identity_temp ?>', 'coupon', 'sitestoreoffer', 'profile-sitestoreoffers', store_showtitle, 'null', offer_ads_display, store_communityad_integration, adwithoutpackage, null, null, null, null, null, null, null, <?php echo $statistics;?>);
      execute_Request_Offer = true;
    }

    if ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1); ?>' && offer_ads_display == 0)
    {
      setLeftLayoutForStore();
    }
  });
  var paginateSitestoreOffers = function(store) {

    var url = en4.core.baseUrl + 'widget/index/mod/sitestoreoffer/name/profile-sitestoreoffers';
    en4.core.request.send(new Request.HTML({
      'url': url,
      'data': {
        'format': 'html',
        'subject': en4.core.subject.guid,
        'store': store,
        'isajax': '1',
        'tab': '<?php echo $this->content_id ?>'
      }
    }), {
      'element': $('id_' + <?php echo $this->content_id ?>)
    });
  }
</script>