<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: homesponsored.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0);?>
<?php if ($this->direction == 1) { ?>
  <?php foreach ($this->sitestoresitestore as $sitestore): ?>
    <div class="SlideItMoo_element seaocore_sponsored_carousel_items">
      <div class="seaocore_sponsored_carousel_items_thumb">
        <?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id, $sitestore->getSlug()), $this->itemPhoto($sitestore, 'thumb.icon', $sitestore->getTitle()), array('rel' => 'lightbox[galerie]', 'class' => "thumb_icon")) ?>
      </div>
      <div class="seaocore_sponsored_carousel_items_info">
        <div class="seaocore_sponsored_carousel_items_title">
          <?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id, $sitestore->getSlug()), Engine_Api::_()->sitestore()->truncation($sitestore->getTitle(), $this->titletruncation), array('title' => $sitestore->getTitle())) ?>
        </div>
        <?php if($postedBy):?>
          <div class="seaocore_sponsored_carousel_items_stat seaocore_txt_light">
            <?php echo $this->translate('posted by'); ?>
            <?php echo $this->htmlLink($sitestore->getOwner()->getHref(), Engine_Api::_()->sitestore()->truncation($sitestore->getOwner()->getTitle(), 10), array('title' => $sitestore->getOwner()->getTitle())) ?>
          </div>
        <?php endif;?>
      </div>
    </div>	 
  <?php endforeach; ?>
<?php } else { ?>
  <?php $count = $this->totalstores; ?>
  <?php for ($i = $count; $i < $this->count; $i++): ?>
    <div class="SlideItMoo_element seaocore_sponsored_carousel_items">
      <div class="seaocore_sponsored_carousel_items_thumb">
        <?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->sitestoresitestore[$i]->store_id, $this->sitestoresitestore[$i]->owner_id, $this->sitestoresitestore[$i]->getSlug()), $this->itemPhoto($this->sitestoresitestore[$i], 'thumb.icon', $this->sitestoresitestore[$i]->getTitle()), array('rel' => 'lightbox[galerie]', 'class' => "thumb_icon")) ?>
      </div>
      <div class="seaocore_sponsored_carousel_items_info">
        <div class="seaocore_sponsored_carousel_items_title">
          <?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->sitestoresitestore[$i]->store_id, $this->sitestoresitestore[$i]->owner_id, $this->sitestoresitestore[$i]->getSlug()), Engine_Api::_()->sitestore()->truncation($this->sitestoresitestore[$i]->getTitle(), $this->titletruncation), array('title' => $this->sitestoresitestore[$i]->getTitle())) ?>
        </div>      
        <?php if($postedBy):?>
          <div class="seaocore_sponsored_carousel_items_stat seaocore_txt_light">
            <?php echo $this->translate('posted by'); ?>
            <?php echo $this->htmlLink($this->sitestoresitestore[$i]->getOwner()->getHref(), Engine_Api::_()->sitestore()->truncation($this->sitestoresitestore[$i]->getOwner()->getTitle(), 10), array('title' => $this->sitestoresitestore[$i]->getOwner()->getTitle())) ?>
          </div>
        <?php endif;?>
      </div>
    </div>	 
  <?php endfor; ?>
  <?php for ($i = 0; $i < $count; $i++): ?>
    <div class="SlideItMoo_element seaocore_sponsored_carousel_items">
      <div class="seaocore_sponsored_carousel_items_thumb">
        <?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->sitestoresitestore[$i]->store_id, $this->sitestoresitestore[$i]->owner_id, $this->sitestoresitestore[$i]->getSlug()), $this->itemPhoto($this->sitestoresitestore[$i], 'thumb.icon', $this->sitestoresitestore[$i]->getTitle()), array('rel' => 'lightbox[galerie]', 'class' => "thumb_icon")) ?>
      </div>
      <div class="seaocore_sponsored_carousel_items_info">
        <div class="seaocore_sponsored_carousel_items_title">
          <?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->sitestoresitestore[$i]->store_id, $this->sitestoresitestore[$i]->owner_id, $this->sitestoresitestore[$i]->getSlug()), Engine_Api::_()->sitestore()->truncation($this->sitestoresitestore[$i]->getTitle(), $this->titletruncation), array('title' => $this->sitestoresitestore[$i]->getTitle())) ?>
        </div> 
        <?php if($postedBy):?>
          <div class="seaocore_sponsored_carousel_items_stat seaocore_txt_light">
            <?php echo $this->translate('posted by'); ?>
            <?php echo $this->htmlLink($this->sitestoresitestore[$i]->getOwner()->getHref(), Engine_Api::_()->sitestore()->truncation($this->sitestoresitestore[$i]->getOwner()->getTitle(), 10), array('title' => $this->sitestoresitestore[$i]->getOwner()->getTitle())) ?>
          </div>
        <?php endif;?>
      </div>
    </div>
  <?php endfor; ?>
<?php } ?>