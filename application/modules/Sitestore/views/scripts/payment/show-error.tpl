<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: shoe-error.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>
<ul class="form-errors">
  <li>
    <?php if (!empty($this->show)): ?>
      <?php echo $this->translate("There are currently no enabled payment gateways. Please contact the site admin to get this issue resolved."); ?>
    <?php else: ?>
      <?php echo $this->translate("There are currently no paid packages available of the site. Please upgrade your package."); ?>
    <?php endif; ?>
  </li>
</ul>