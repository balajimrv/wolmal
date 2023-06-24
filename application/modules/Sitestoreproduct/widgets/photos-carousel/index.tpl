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

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/scripts/core.js'); ?>
<div class="sr_sitestoreproduct_prpfile_photos_strip o_hidden">
  <a id="sr_sitestoreproduct_crousal_photoPrev_<?php echo $this->includeInWidget ? $this->includeInWidget : $this->identity ?>" class="photoPrev sr_sitestoreproduct_option_button photoLeft" style="visibility: hidden; <?php if(!($this->itemCount < $this->total_images)):?>display:none; <?php endif;?>"></a>
  <div class="sr_sitestoreproduct_photo_scroll" id="sr_sitestoreproduct_ul_photo_scroll_<?php echo $this->includeInWidget ? $this->includeInWidget : $this->identity ?>" style="width:<?php echo ($this->itemCount * 74) ?>px">
    <ul class="">
      <?php foreach ($this->photo_paginator as $photo): ?>
        <li class="liPhoto">
          <div class='photoThumb'>
            <?php if( empty($this->isQuickView) ) : ?>
              <a class="txt_center" href="<?php echo $photo->getPhotoUrl(); ?>" onmouseover="changeProfilePicture(this, '<?php echo $photo->getPhotoUrl(); ?>', '<?php echo $photo->photo_id ?>', true)" <?php if (SEA_LIST_LIGHTBOX) : ?> onclick='openSeaocoreLightBox("<?php echo $photo->getHref(); ?>");return false;' <?php endif; ?>>
                <?php echo $this->itemPhoto($photo, 'thumb.normal', '', array('align' => 'center')); ?>
              </a>
            <?php else: ?>
              <a id="sitestoreproduct_product_profile_crousal_<?php echo $photo->photo_id ?>" class="txt_center" href="<?php echo $photo->getPhotoUrl(); ?>" onmouseover="changeProfilePicture(this, '<?php echo $photo->getPhotoUrl(); ?>', '<?php echo $photo->photo_id ?>', false)" >
                <?php echo $this->itemPhoto($photo, 'thumb.normal', '', array('align' => 'center')); ?>
              </a>
            <?php endif; ?>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <a id="sr_sitestoreproduct_crousal_photoNext_<?php echo $this->includeInWidget ? $this->includeInWidget : $this->identity ?>" class="photoNext sr_sitestoreproduct_option_button photoRight" style="visibility: hidden; <?php if(!($this->itemCount < $this->total_images)):?>display:none; <?php endif;?> "></a>
</div>

<script type="text/javascript">
  en4.core.runonce.add(function(){
    new Fx.Scroll.Carousel('sr_sitestoreproduct_ul_photo_scroll_<?php echo $this->includeInWidget ? $this->includeInWidget :  $this->identity ?>',{
      mode: 'horizontal',
      childSelector:'.liPhoto',
      noOfItemPerPage:<?php echo $this->itemCount?>,
      noOfItemScroll:<?php echo $this->itemCount?>,
      navs:{
        frwd:'sr_sitestoreproduct_crousal_photoNext_<?php echo $this->includeInWidget ? $this->includeInWidget :  $this->identity ?>',
        prev:'sr_sitestoreproduct_crousal_photoPrev_<?php echo $this->includeInWidget ? $this->includeInWidget :  $this->identity ?>'
      }
    });
  });
</script>
