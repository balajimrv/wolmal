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
<div class="sitestore_view_all_store txt_center bold">
  <a  href="<?php echo $this->url(array('action' => 'account', 'menuType' => 'my-stores'), 'sitestoreproduct_general', true); ?>">
      <?php echo $this->translate(array('View My Store (%s)', 'View My Stores (%s)', $this->storeCount), $this->locale()->toNumber($this->storeCount)); ?>
  </a>
</div>
