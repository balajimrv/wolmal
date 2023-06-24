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
try{
	?>
	<?php
	$baseUrl = $this->layout()->staticBaseUrl;
	// $this->headLink()->prependStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
	?>
	<div class='layout_middle'>
		<?php
		$item_approved = Zend_Registry::isRegistered('sitestore_approved') ? Zend_Registry::get('sitestore_approved') : null;
		$renew_date= date('Y-m-d', mktime(0, 0, 0, date("m"), date('d', time()) + (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.renew.email', 2))));?>
		<?php if ($this->current_count >= $this->quota && !empty($this->quota)): ?>
			<div class="tip">
				<span><?php echo $this->translate('You have already created the maximum number of stores allowed. If you would like to open a new store, please delete an old one first.'); ?></span>
			</div>
			<br/>
		<?php endif; ?>

		<?php if ($this->paginator->getTotalItemCount() > 0): ?>
			<ul class="seaocore_browse_list">
				<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
				<?php foreach ($this->paginator as $item): ?>
					<li <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.fs.markers', 1)):?><?php if($item->featured):?>class="lists_highlight"<?php endif;?><?php endif;?>>
						<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.fs.markers', 1)):?>
							<?php if($item->featured):?>
								<span title="<?php echo $this->translate('Featured')?>" class="seaocore_list_featured_label"><?php echo $this->translate('Featured')?></span>
							<?php endif;?>
						<?php endif;?>
						<div class='seaocore_browse_list_photo'>
							<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($item->store_id, $item->owner_id), $this->itemPhoto($item, 'thumb.normal', '', array('align'=>'left'))) ?>
							<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.fs.markers', 1)):?>
								<?php if (!empty($item->sponsored)): ?>
									<?php $sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.image', 1);
									if (!empty($sponsored)) { ?>
										<div class="seaocore_list_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.color', '#fc0505'); ?>;'>
											<?php echo $this->translate('SPONSORED'); ?>
										</div>
									<?php } ?>
								<?php endif; ?>
							<?php endif; ?>
						</div>

						<div class='seaocore_browse_list_options'>
							<?php if ($this->can_edit): ?>
								<?php if(empty ($item->declined)): ?>
									<a href='javascript:void(0)' onclick="window.location.href='<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'product', 'action' => 'manage', 'store_id' => $item->store_id), 'default', true); ?>';return false;" class='buttonlink item_icon_sitestoreproduct'><?php echo $this->translate('My Products'); ?></a>
									<a href='javascript:void(0)' onclick="window.location.href='<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'index', 'action' => 'manage-order', 'store_id' => $item->store_id), 'default', true); ?>';return false;" class='buttonlink item_icon_sitestoreproduct'><?php echo $this->translate('My Orders'); ?></a>
								<?php endif; ?>
							<?php endif; ?>
						</div>

						<?php echo $this->partial('partial_views.tpl', 'sitestore', array("sitestore" => $item)); ?>
						<?php echo $this->viewMore($item->body,200,5000) ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php elseif ($this->search): ?>
			<div class="tip"> <span> <?php if(!empty($item_approved)){ echo $this->translate('You do not have any store which matches your search criteria.'); }else { echo $this->translate($this->store_manage_msg); } ?> </span> </div>
		<?php else: ?>
			<div class="tip">
				<span> <?php if(!empty($item_approved)){ echo $this->translate('You do not have any stores yet.'); }else { echo $this->translate($this->store_manage_msg); } ?>
					<?php if ($this->can_create): ?>
						<?php  if (Engine_Api::_()->sitestore()->hasPackageEnable()):
							$createUrl=$this->url(array('action'=>'index'), 'sitestore_packages');
						else:
							$createUrl=$this->url(array('action'=>'create'), 'sitestore_general');
						endif; ?>
						<?php echo $this->translate('Get started by %1$screating%2$s a new store.', '<a href=\''. $createUrl. '\'>', '</a>'); ?>
					<?php endif; ?>
				</span>
			</div>
		<?php endif; ?>
		<?php echo $this->paginationControl($this->paginator, null); ?>
		<form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), 'sitestore_session_payment', true) ?>">
			<input type="hidden" name="store_id_session" id="store_id_session" />
		</form>
	</div>
	<?php
} catch(Exception $e){
	// var_dump($e);die;
	throw $e;
}
?>
