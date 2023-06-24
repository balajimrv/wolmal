<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
	$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/sitestore-tooltip.css');
        
	$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestorereview/externals/styles/style_sitestorereview.css');

include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php $photo_review = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.photo', 1);?>
<ul class="sitestore_sidebar_list">
	<li>
    <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
		$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestorereview.profile-sitestorereviews', $this->reviewOfDay->store_id, $layout);?>
    <?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $this->reviewOfDay->store_id);?>
		<?php $user = Engine_Api::_()->getItem('user', $this->reviewOfDay->owner_id); ?>
		<?php if(!empty($photo_review)):?>
      <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('title' => $user->getTitle())) ?>
    <?php else:?>
			<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore_object->store_id, $sitestore_object->owner_id, $sitestore_object->getSlug()), $this->itemPhoto($sitestore_object, 'thumb.icon'), array('title' => $sitestore_object->getTitle()));?>
    <?php endif;?>
		<div class="sitestore_sidebar_list_info">
			<div class="sitestore_sidebar_list_title">
				<?php
					$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.truncation.limit', 13);
					$review_title = Engine_Api::_()->sitestorereview()->truncateText($this->reviewOfDay->title, $truncation_limit);
				?>
				<?php echo $this->htmlLink($this->reviewOfDay->getHref(), $review_title, array('title' => $this->reviewOfDay->title)) ?>
	
			</div>
      <?php $store = Engine_Api::_()->sitestorereview()->getLinkedStore($this->reviewOfDay->review_id); ?>  
      <div class="sitestore_sidebar_list_details">
				<?php echo $this->translate("on ").$this->htmlLink(Engine_Api::_()->sitestore()->getHref($store->store_id, $store->owner_id), Engine_Api::_()->sitestore()->truncation($store->title), array('title' => $store->title, 'class' => 'bold')); ?>
			</div>
      <div class='sitestore_sidebar_list_details'>
				<span title="<?php echo $store->rating . $this->translate(' rating'); ?>">
					<?php if (($store->rating > 0)): ?>
						<?php for ($x = 1; $x <= $store->rating; $x++): ?>
							<span class="rating_star_generic rating_star"></span>
						<?php endfor; ?>
						<?php if ((round($store->rating) - $store->rating) > 0): ?>
							<span class="rating_star_generic rating_star_half"></span>
						<?php endif; ?>
					<?php endif; ?>
				</span>
			</div>  
		</div>
		<div class="clr sitestore_review_code">
			<b class="c-l fleft"></b>
			<?php echo Engine_Api::_()->sitestorereview()->truncateText($this->reviewOfDay->body, 100) ?>
			<b class="c-r fright"></b>
		</div>
	</li>
  <li class="sitestore_sidebar_list_seeall">
    <?php echo $this->htmlLink($this->reviewOfDay->getHref(array('tab' => $tab_id)), $this->translate('More &raquo;'));?>
  </li>
</ul>