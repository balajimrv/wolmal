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
<div class="iscroll_carousal content-slideshow" data-width="" data-height="">
  <div class='iscroll_carousal_wrapper prelative sm-widget-block' data-itemcount="<?php echo $this->num_of_slideshow ?>">
    <div class="iscroll_carousal_scroller" style="width: <?php echo $this->num_of_slideshow * 300 ?>px">
      <?php $i = 0; ?>
      <ul class="">
        <?php foreach ($this->hotOffers as $coupon): ?>
          <li class="liPhoto">            
            <a href="<?php echo $coupon->getHref() ?>" class="ui-link-inherit">
              <span class="slideshow-img prelative">
                <?php echo $this->itemPhoto($coupon, 'thumb.normal', '', array('align' => 'center')); ?>
              </span>
              <div class="o_hidden slideshow-content">
                <div class="slideshow-title bold">
                  <b><?php echo $coupon->title?></b>
                </div>
                <?php $truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.title.truncation', 18);
                $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $coupon->store_id);
                $tmpBody = strip_tags($sitestore_object->title);
                $store_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );             ?>
                <div class="f_small">  
                  <?php echo $this->translate("in ") ?>
                  <b><?php echo  $store_title; ?></b>
                </div>
              </div>
            </a>
          </li>
          <?php $i++; ?>
        <?php endforeach; ?>
      </ul>
    </div>       
  </div>
  <div class="iscroll_carousal_nav clr">
    <?php if($i>1): ?>
    <ul class="iscroll_carousal_indicator" id="indicator">
      <?php for ($j = 1; $j <= $i; $j++): ?>
        <li class="<?php echo $j == 1 ? 'active' : '' ?>"><?php echo $j; ?></li>
      <?php endfor; ?>
    </ul>
    <?php endif; ?>
  </div> 
</div>