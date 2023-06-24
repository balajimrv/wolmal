<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sticky.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<form method="post" class="global_form_popup">
  <div>
    <?php if(empty($this->status)):?>
			<h3><?php echo $this->translate('Enable Coupon'); ?></h3>
    <?php else:?>
      <h3><?php echo $this->translate('Disable Coupon'); ?></h3>
    <?php endif;?>
    <?php if(empty($this->status)):?>
			<p>
				<?php echo $this->translate('Are you sure you want to Enable this Coupon?'); ?>
			</p>
		<?php else:?>
			<p>
				<?php echo $this->translate('Are you sure you want to Disable this Coupon?'); ?>
			</p>
    <?php endif;?>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->offer_id ?>"/>
      <?php if(empty($this->status)):?>
				<button type='submit'><?php echo $this->translate('Enable Coupon'); ?></button>
			<?php else:?>
				<button type='submit'><?php echo $this->translate('Disable Coupon'); ?></button>
      <?php endif;?>
      or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
    </p>
  </div>
</form>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>