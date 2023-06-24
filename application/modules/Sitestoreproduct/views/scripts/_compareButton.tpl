<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _compareButton.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<?php 
  $this->headTranslate(array('Compare All', 'Remove All', 'Compare', 'Show Compare Bar', 'Please select more than one product for the comparison.', 'Hide Compare Bar'));

	$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');
	$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/scripts/core.js');
?>

<?php if($this->buttonType=='pinboard-button'):?>
  <a><span class="compareProduct sr_sitestoreproduct_compare_button">
    <input type="checkbox" class="checkProduct compareButtonProduct<?php echo $this->item->getIdentity() ?>" name="<?php echo $this->escape($this->item->getTitle()) ?>" id="product_<?php echo $this->item->getIdentity() ?>" value="<?php echo $this->item->getIdentity() ?>"  />&nbsp;
    <label class="srlbCompare" for="product_<?php echo $this->item->getIdentity() ?>"><?php echo $this->translate('Compare') ?></label>
    <span id="productID<?php echo $this->item->getIdentity() ?>" class="productType<?php echo $this->category_id ?>" style="display:none;"><?php echo $this->translate($this->category_title) ?></span>
    <span id="productUrl<?php echo $this->item->getIdentity() ?>" style="display:none;"><?php echo $this->item->getHref() ?></span>
    <span id="productImgSrc<?php echo $this->item->getIdentity() ?>" style="display:none;"><?php echo $this->item->getPhotoUrl('thumb.icon') ? $this->item->getPhotoUrl('thumb.icon'): $this->layout()->staticBaseUrl.'application/modules/Sitestoreproduct/externals/images/nophoto_product_thumb_icon.png'; ?></span>
  </span></a>
<?php else:?>
  <span class="compareProduct sr_sitestoreproduct_compare_button">
    <input type="checkbox" class="checkProduct compareButtonProduct<?php echo $this->item->getIdentity() ?>" name="<?php echo $this->escape($this->item->getTitle()) ?>" id="product_<?php echo $this->item->getIdentity() ?>" value="<?php echo $this->item->getIdentity() ?>"  />&nbsp;
    <label class="srlbCompare" for="product_<?php echo $this->item->getIdentity() ?>"><?php echo $this->translate('Compare') ?></label>
    <span id="productID<?php echo $this->item->getIdentity() ?>"   class="productType<?php echo $this->category_id ?>" style="display:none;"><?php echo $this->translate($this->category_title) ?></span>
    <span id="productUrl<?php echo $this->item->getIdentity() ?>" style="display:none;"><?php echo $this->item->getHref() ?></span>
    <span id="productImgSrc<?php echo $this->item->getIdentity() ?>" style="display:none;"><?php echo $this->item->getPhotoUrl() ? $this->item->getPhotoUrl(): $this->layout()->staticBaseUrl.'application/modules/Sitestoreproduct/externals/images/nophoto_product_thumb_icon.png'; ?></span>
  </span>
<?php endif; ?>

<script type="text/javascript">
  if(!compareSitestoreproductDefault.enabel){
    compareSitestoreproductDefault={
      enabel:true,
      compareUrl:'<?php echo $this->url(array(), 'sitestoreproduct_compare', true) ?>'
    };
    
    en4.core.runonce.add(function() {
      if(typeof compareSitestoreproductContent =='undefined'){
      compareSitestoreproductContent  = new compareSitestoreproduct();
      compareSitestoreproductContent.compareUrl=compareSitestoreproductDefault.compareUrl;
      }
    });
  }


  en4.core.runonce.add(function() {
    $$('.compareButtonProduct<?php echo $this->item->getIdentity()  ?>').removeEvents('click', compareSitestoreproductContent.compareButtonEvent.bind(compareSitestoreproductContent));
    $$('.compareButtonProduct<?php echo $this->item->getIdentity()  ?>').addEvent('click', compareSitestoreproductContent.compareButtonEvent.bind(compareSitestoreproductContent));
    compareSitestoreproductContent.updateCompareButtons();
  });
</script>