<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<ul class="sitestore_sidebar_list">
  <?php foreach ($this->paginator as $coupon):?>
        <li>
					<?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $coupon['store_id']);?>
					<?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
					$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreoffer.profile-sitestoreoffers', $coupon['store_id'], $layout);?>
					<a href="<?php echo Engine_Api::_()->sitestore()->getHref($sitestore_object->store_id, $sitestore_object->owner_id, $sitestore_object->getSlug()); ?>">
						<?php echo $this->itemPhoto($sitestore_object, 'thumb.icon', $sitestore_object->getTitle()) ?>
					</a>
					<div class="sitestore_sidebar_list_info">
						<div class="sitestore_sidebar_list_title">
              <?php echo $this->htmlLink($sitestore_object->getHref(), Engine_Api::_()->sitestore()->truncation($sitestore_object->getTitle()), array('title' => $sitestore_object->getTitle())); ?> 
						</div>
           <?php $category = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($sitestore_object->category_id);?>
           <?php $subCategory = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($sitestore_object->subcategory_id);?>
				   <?php if ($category): ?>
						<div class="sitestore_sidebar_list_details">
							<?php echo $category->category_name;?>
							<?php if ($subCategory): ?> &raquo;
							<?php echo $subCategory->category_name; ?>
							<?php endif; ?>
						</div>
					 <?php endif; ?>
					<div class="sitestore_sidebar_list_details">
						<?php echo $this->htmlLink($sitestore_object->getHref(array('tab'=> $tab_id)),$coupon['item_count'].' coupons'); ?>
					</div>	
        </div>
      </li>
	<?php endforeach; ?>
</ul>