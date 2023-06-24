<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _addToWishlist.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>


<a title="<?php echo $this->translate('Add to Wishlist'); ?>" class="<?php echo $this->classIcon . ' ' . $this->classLink ?>" data-role="button" data-rel="dialog" href=" <?php echo $this->url(array('action' => 'add', 'product_id' => $this->item->product_id), 'sitestoreproduct_wishlist_general', true); ?>">
  <?php echo $this->translate($this->text); ?>
</a>
