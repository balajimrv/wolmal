<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _copyImages.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/scripts/core.js'); ?>
<?php 
$product_id = $this->product_id; 
$sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
$album = $sitestoreproduct->getSingletonAlbum();
    $photo_paginator = $album->getCollectiblesPaginator();

    $temp_total_images = $photo_paginator->getTotalItemCount();
    $total_images = $temp_total_images+5;
    $isQuickView = null;
    $itemCount = 5;
?>

<div class="form-wrapper" id="oldimages-wrapper">
  <div class="form-label" id="oldimages-label">
    <label class="optional" for="photoo"><?php echo $this->translate("Images") ?></label>
  </div>
  <div class="form-element" id="oldimages-element">
<div class="sr_sitestoreproduct_prpfile_photos_strip o_hidden">

  <a id="sr_sitestoreproduct_crousal_photoPrev_id" class="photoPrev sr_sitestoreproduct_option_button photoLeft" style="visibility: hidden; <?php if(!($itemCount < $total_images)):?>display:none; <?php endif;?>"></a>

  <div class="sr_sitestoreproduct_photo_scroll" id="sr_sitestoreproduct_ul_photo_scroll_id" style="width:<?php echo ($itemCount * 74) ?>px">
    <ul id="ul_images"class="">
      <?php foreach ($photo_paginator as $photo): ?>
     
        <li class="liPhoto" >
           <input type="checkbox" value="<?php echo $photo->file_id;?>" id="temp_image_file_id-<?php echo $photo->file_id; ?>" name="temp_image_file_id[]" checked="checked" onClick ="getId(this);" style="display: block" />
          <div id="temp_image_file_id-<?php echo $photo->file_id."image"; ?>"class='photoThumb'>
     
<?php if( empty($isQuickView) ) : ?>
            
<!--              <a class="txt_center" href="<?php echo $photo->getPhotoUrl(); ?>"  <?php if (SEA_LIST_LIGHTBOX) : ?> onclick='openSeaocoreLightBox("<?php echo $photo->getHref(); ?>");return false;' <?php endif; ?>>-->
                <?php echo $this->itemPhoto($photo, 'thumb.normal', '', array('align' => 'center', 'width' => 80, 'height' => 80)); ?>
<!--              </a>-->
    
            <?php endif; ?>

          </div>          
        </li>
      <?php endforeach; ?>
        <?php for($tempImage=1;$tempImage<6;$tempImage++): ?>
  <li class="liPhoto" id="<?php echo "uploaded_images_".$tempImage; ?>">
    <div class='photoThumb'>
    <center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreproduct/externals/images/nophoto_product_thumb_profile.png" /></center>
      
    </div>
    </li>
  <?php endfor; ?>
        
    </ul>
  </div>
  <a id="sr_sitestoreproduct_crousal_photoNext_id" class="photoNext sr_sitestoreproduct_option_button photoRight" style="visibility: hidden; <?php if(!($itemCount < $total_images)):?>display:none; <?php endif;?> "></a>
</div>
</div></div>
<script type="text/javascript">
  en4.core.runonce.add(function(){
    new Fx.Scroll.Carousel('sr_sitestoreproduct_ul_photo_scroll_id',{
      mode: 'horizontal',
      childSelector:'.liPhoto',
      noOfItemPerPage:<?php echo $itemCount?>,
      noOfItemScroll:<?php echo $itemCount?>,
      navs:{
        frwd:'sr_sitestoreproduct_crousal_photoNext_id',
        prev:'sr_sitestoreproduct_crousal_photoPrev_id'
      }
    });
  });
  
  function getId(temp){
    var element = temp.id+"image";
    if($(temp.id).checked)
      $(element).style.opacity = "1";
    else
      $(element).style.opacity = "0.3";
 }
</script>