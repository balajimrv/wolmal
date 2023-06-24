<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _shippingMethodLink.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
          $isVatAllow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0);
          if (!empty($isVatAllow)):
            $productPriceArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($this->sitestoreproduct);
            if (!empty($productPriceArray) && empty($productPriceArray['show_msg'])):
              ?>
              <?php if (empty($this->isQuickView)) : ?>
<span class="btnlink" style="margin-top: <?php echo !empty($this->isVirtual)? '8px' : '-3px' ?>; display: inline-block;">
                  <?php echo $this->htmlLink(array('route' => 'sitestoreproduct_general', 'action' => 'shipping-methods', 'store_id' => $this->sitestoreproduct->store_id, 'product_id' => $this->sitestoreproduct->product_id, 'isViewerSide' => true), $this->translate("Shipping methods"), array('class' => 'smoothbox buttonlink sitestoreproduct_shipping_link')); ?>
                </span>
              <?php else : ?>
                <span class="btnlink" style="margin-top: <?php echo !empty($this->isVirtual)? '8px' : '-3px' ?>; display: inline-block;">
                  <?php echo $this->htmlLink(array('route' => 'sitestoreproduct_general', 'action' => 'shipping-methods', 'store_id' => $this->sitestoreproduct->store_id, 'product_id' => $this->sitestoreproduct->product_id, 'isViewerSide' => true), $this->translate("Shipping methods"), array('class' => 'buttonlink sitestoreproduct_shipping_link', 'target' => '_blank')); ?>
                </span>
              <?php endif; ?>
            <?php endif; ?>
          <?php elseif (empty($isVatAllow)): ?>
            <?php if (empty($this->isQuickView)) : ?>
              <span class="btnlink" style="margin-top: <?php echo !empty($this->isVirtual)? '8px' : '-3px' ?>; display: inline-block;">
                <?php echo $this->htmlLink(array('route' => 'sitestoreproduct_general', 'action' => 'shipping-methods', 'store_id' => $this->sitestoreproduct->store_id, 'product_id' => $this->sitestoreproduct->product_id, 'isViewerSide' => true), $this->translate("Shipping methods"), array('class' => 'smoothbox buttonlink sitestoreproduct_shipping_link')); ?>
              </span>
            <?php else: ?>
              <span class="btnlink" style="margin-top: <?php echo !empty($this->isVirtual)? '8px' : '-3px' ?>; display: inline-block;">
                <?php echo $this->htmlLink(array('route' => 'sitestoreproduct_general', 'action' => 'shipping-methods', 'store_id' => $this->sitestoreproduct->store_id, 'product_id' => $this->sitestoreproduct->product_id, 'isViewerSide' => true), $this->translate("Shipping methods"), array('class' => 'buttonlink sitestoreproduct_shipping_link', 'target' => '_blank')); ?>
              </span>
            <?php endif; ?>
          <?php endif; ?>