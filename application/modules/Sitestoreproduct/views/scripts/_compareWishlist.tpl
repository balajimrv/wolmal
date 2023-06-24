<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _compareWishlist.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if (!Engine_Api::_()->seaocore()->isSiteMobileModeEnabled()) : ?>
<div class="seao_pflinks_block">
<!--WORK FOR SHIPPING METHOD LINK-->
<?php
  $isVatAllow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0);
  if (!empty($isVatAllow)):
    $productPriceArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($this->sitestoreproduct);
  if(!empty($productPriceArray) && empty($productPriceArray['show_msg'])):
    ?>
    <?php if (empty($this->isQuickView)) : ?>
        <span class="btnlink">
      <?php echo $this->htmlLink(array('route' => 'sitestoreproduct_general', 'action' => 'shipping-methods', 'store_id' => $this->sitestoreproduct->store_id, 'product_id' => $this->sitestoreproduct->product_id, 'isViewerSide' => true), $this->translate("Shipping methods"), array('class' => 'smoothbox buttonlink sitestoreproduct_shipping_link')); ?>
        </span>
    <?php else : ?>
      <span class="btnlink">
      <?php echo $this->htmlLink(array('route' => 'sitestoreproduct_general', 'action' => 'shipping-methods', 'store_id' => $this->sitestoreproduct->store_id, 'product_id' => $this->sitestoreproduct->product_id, 'isViewerSide' => true), $this->translate("Shipping methods"), array('class' => 'buttonlink sitestoreproduct_shipping_link', 'target' => '_blank')); ?>
        </span>
    <?php endif; ?>
  <?php endif;?>
<?php else:?>
<?php if (empty($this->isQuickView)) : ?>
        <span class="btnlink">
      <?php echo $this->htmlLink(array('route' => 'sitestoreproduct_general', 'action' => 'shipping-methods', 'store_id' => $this->sitestoreproduct->store_id, 'product_id' => $this->sitestoreproduct->product_id, 'isViewerSide' => true), $this->translate("Shipping methods"), array('class' => 'smoothbox buttonlink sitestoreproduct_shipping_link')); ?>
        </span>
    <?php else : ?>
      <span class="btnlink">
      <?php echo $this->htmlLink(array('route' => 'sitestoreproduct_general', 'action' => 'shipping-methods', 'store_id' => $this->sitestoreproduct->store_id, 'product_id' => $this->sitestoreproduct->product_id, 'isViewerSide' => true), $this->translate("Shipping methods"), array('class' => 'buttonlink sitestoreproduct_shipping_link', 'target' => '_blank')); ?>
        </span>
<?php endif; ?>
<?php endif;?>

  <?php $compare = $this->compareButtonSitestoreproduct($this->sitestoreproduct, $this->identity); ?>
  <?php if (!empty($compare) || !empty($this->create_review)): ?>
    <?php if (!empty($compare)): ?>
      <span class="btnlink"> 
        <?php echo $compare ?>
      </span>
    <?php endif; ?>

    <?php echo $this->addToWishlistSitestoreproduct($this->sitestoreproduct, array('classIcon' => 'sr_sitestoreproduct_wishlist_href_link', 'classLink' => '')); ?>

    <?php if (!empty($this->create_review)): ?>
      <span class="btnlink">
        <?php echo $this->content()->renderWidget("sitestoreproduct.review-button", array('product_guid' => $this->sitestoreproduct->getGuid(), 'product_profile_page' => 1, 'identity' => $this->identity, 'isProductProfile' => 1, 'isQuickView' => $this->isQuickView)) ?>
      </span>
    <?php endif; ?>
  <?php endif; ?>
</div>
<?php else: ?>

  <?php echo $this->addToWishlistSitestoreproduct($this->sitestoreproduct, array('classIcon' => 'sr_sitestoreproduct_wishlist_href_link', 'classLink' => '')); ?>

  <?php if (!empty($this->create_review)): ?>
    <span class="btnlink">
      <?php echo $this->content()->renderWidget("sitestoreproduct.review-button", array('product_guid' => $this->sitestoreproduct->getGuid(), 'product_profile_page' => 1, 'identity' => $this->identity, 'isProductProfile' => 1, 'isQuickView' => $this->isQuickView)) ?>
    </span>
  <?php endif; ?>

<?php endif; ?>