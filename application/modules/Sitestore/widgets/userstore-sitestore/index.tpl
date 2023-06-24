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
<?php if (count($this->userStores)): ?>
  <h3> <?php echo $this->sitestore->getOwner()->toString() ?><?php echo $this->translate("'s Stores"); ?></h3>
  <ul class="sitestore_sidebar_list">
       <?php  $this->partialLoop()->setObjectKey('sitestore');
              echo $this->partialLoop('application/modules/Sitestore/views/scripts/partialloop_widget.tpl', $this->userStores);
			//echo $this->partialLoop('partialloop_widget.tpl', $this->userStores)
		?>
  </ul>
<?php endif; ?>