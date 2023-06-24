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
<?php
	$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/sitestore-tooltip.css');

include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<ul class="sitestorelike_users_block">
  <li>
    <?php foreach ($this->featuredowners as $item): ?>
      <div class="likes_member_sitestore sitestore_show_owner_tooltip_wrapper">
        <?php echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon')) ?>	
        <div class='sitestore_show_owner_tooltip'>
          <img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/tooltip_arrow.png" alt="" />
          <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
        </div>
      </div>
    <?php endforeach; ?>
  </li>	
</ul>