<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: partialWidget.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
	$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/sitestore-tooltip.css');

include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php $photo_review = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.photo', 1);?>
<?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $this->review->store_id);?>
<?php $user = Engine_Api::_()->getItem('user', $this->review->owner_id); ?>
    <li class="sitestorereview_show_tooltip_wrapper">
			<div class="sitestorereview_show_tooltip">
				<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/tooltip_arrow.png" alt="" class="arrow" />
				<?php echo Engine_Api::_()->sitestorereview()->truncateText($this->review->body, 100) ?>
			</div>
      <?php if(!empty($photo_review)):?>
      <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('title' => $user->getTitle())) ?>
      <?php else:?>
        <?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore_object->store_id, $sitestore_object->owner_id, $sitestore_object->getSlug()), $this->itemPhoto($sitestore_object, 'thumb.icon'), array('title' => $sitestore_object->getTitle()));?>
      <?php endif;?>
      
      <div class='sitestore_sidebar_list_info'>
        <div class='sitestore_sidebar_list_title'>
          <?php
          $truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.truncation.limit', 13);
          $this->review_title = Engine_Api::_()->sitestorereview()->truncateText($this->review->title, $truncation_limit);

          $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
          $tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestorereview.profile-sitestorereviews', $this->review->store_id, $layout);
          ?>
          <?php echo $this->htmlLink($this->review->getHref(), $this->review_title, array('title' => $this->review->title)) ?>
        </div>

        <div class='sitestore_sidebar_list_details'>