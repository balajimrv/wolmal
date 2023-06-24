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

<?php if(empty($this->offer_store)):?>
	<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>
	<div class="sitestore_viewstores_head">
		 <?php echo $this->htmlLink($this->sitestore->getHref(), $this->itemPhoto($this->sitestore, 'thumb.icon', '', array('align' => 'left'))) ?>
		<h2>	
			<?php echo $this->sitestore->__toString() ?>	
			<?php echo $this->translate('&raquo; '); ?>
       <?php echo $this->htmlLink($this->sitestore->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Coupons')) ?>
		</h2>
	</div>
<?php endif;?>

<?php if(!empty($this->offer_store)):?>
	<form method="post" class="global_form_popup">
		<div>
			<h3><?php echo $this->translate('Delete Store Coupon ?'); ?></h3>
			<p>
				<?php echo $this->translate('Are you sure that you want to delete this coupon? It will not be recoverable after being deleted.'); ?>
			</p>
			<br />
			<p>
				<input type="hidden" name="confirm" value="<?php echo $this->offer_id ?>"/>
				<button type='submit'><?php echo $this->translate('Delete'); ?></button>
				or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
			</p>
		</div>
	</form>
<?php else:?>
	<form method="post" class="global_form">
		<div>
			<div>
				<h3><?php echo $this->translate('Delete Store Coupon ?'); ?></h3>
				<p>
					<?php echo $this->translate('Are you sure that you want to delete this coupon? It will not be recoverable after being deleted.'); ?>
				</p>
				<br />
				<p>
					<input type="hidden" name="confirm" value="<?php echo $this->offer_id ?>"/>
					<button type='submit'><?php echo $this->translate('Delete'); ?></button>
					<?php echo $this->translate('or'); ?> <?php echo $this->htmlLink($this->sitestore->getHref(array('tab'=>$this->tab_selected_id)),$this->translate('cancel')) ?>
				</p>
			</div>
		</div>
	</form>
<?php endif;?>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>