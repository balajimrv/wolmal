<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/Adintegration.tpl';?>
<h2 class="fleft"><?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin'); ?></h2>
<?php if (count($this->navigationStore)): ?>
  <div class='seaocore_admin_tabs clr'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigationStore)->render()
  ?>
  </div>
<?php endif; ?>
<div class="headline">
  <h2> <?php $this->translate('Stores'); ?> </h2>
  <div class="tabs">
    <?php
    // Render the menu
    echo $this->navigation()
            ->menu()
            ->setContainer($this->navigation)
            ->render();
    ?>
  </div>
</div>
<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adofferlist', 3) && $store_communityad_integration): ?>
  <div class="layout_right" id="communityad_offerlist">
		<?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adofferlist', 3),"loaded_by_ajax"=>0,'widgetId'=>"store_offerlist"))?>
  </div>
<?php endif; ?>

<?php if($this->paginator->getTotalItemCount()):?>
	<div class='layout_middle'>
		<h3 class="sitestore_mystore_head"><?php echo $this->translate('Coupons');?></h3>
		<ul class="seaocore_browse_list">
			<?php foreach ($this->paginator as $sitestore): ?>
				<li>
					<div class="seaocore_browse_list_photo"> 
              <?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $sitestore->store_id);?>
              <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
                    $tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreoffer.profile-sitestoreoffers', $sitestore->store_id, $layout);?>
							<?php if(!empty($sitestore->photo_id)):?>
								<?php echo $this->htmlLink($sitestore_object->getHref(array('tab'=> $tab_id)), $this->itemPhoto($sitestore, 'thumb.normal', $sitestore->getTitle()), array('title' => $sitestore->getTitle())) ?>   
							<?php else:?>
                <?php echo $this->htmlLink($sitestore_object->getHref(array('tab'=> $tab_id)), "<img src='". $this->layout()->staticBaseUrl . "application/modules/Sitestoreoffer/externals/images/offer_thumb.png' alt='' />", array('title' => $sitestore->getTitle())) ?>   
							<?php endif;?>
						</div>
					<div class='seaocore_browse_list_info'>
						<div class='seaocore_browse_list_info_title'>
							<h3>   <?php echo $item_title = $this->htmlLink($sitestore_object->getHref(array('tab'=> $tab_id)), $sitestore->title, array('title' => $sitestore->title)); ?></h3>
						</div>
						<div class="seaocore_browse_list_info_date">
							<?php $item = Engine_Api::_()->getItem('sitestore_store', $sitestore->store_id); ?>
							<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id, $item->getSlug()),  $sitestore->sitestore_title) ?>
						</div>
						<div class="seaocore_browse_list_info_date">
							<span><?php echo $this->translate('End date:');?></span>
							<?php if($sitestore->end_settings == 1):?>
								<span><?php echo $this->translate( gmdate('M d, Y', strtotime($sitestore->end_time))) ?></span>
							<?php else:?>
								<span><?php echo $this->translate('Never Expires');?></span>
							<?php endif;?>
							<?php if(!empty($sitestore->url)):?><?php echo '| ' .$this->translate('URL:');?>
								<a href = "<?php echo "http://".$sitestore->url ?>" target="_blank" title="<?php echo "http://".$sitestore->url ?>"><?php echo "http://".$sitestore->truncate20Url(); ?></a>
							<?php endif;?>
						</div> 
						<?php if(!empty($sitestore->coupon_code)):?>
							<div class="seaocore_browse_list_info_date">
								<?php echo $this->translate('Coupon Code:');?>
								<?php echo $sitestore->coupon_code;?>
							</div>
			      <?php endif;?>
						<div class='seaocore_browse_list_info_blurb'>
							<?php echo $this->viewMore($sitestore->description); ?><br />
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php echo $this->paginationControl($this->paginator); ?>
	</div>
<?php else: ?>
	<div class="tip">
		<span>
			<?php echo $this->translate('No coupons available.');?>
		</span>
	</div>
<?php endif;?>
