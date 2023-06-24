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
    <?php if (empty($this->sticky)): ?>
      <h3><?php echo $this->translate('Featured Coupon'); ?></h3>
    <?php else: ?>
      <h3><?php echo $this->translate('Remove as Featured Coupon'); ?></h3>
    <?php endif; ?>
    <?php if (empty($this->sticky)): ?>
      <p>
        <?php echo $this->translate('Are you sure you want to make this coupon as featured? Only one coupon can be made featured, and this coupon will then be shown alongside your Store\'s entry in the listing of all Stores of this community. It will also be shown on top of all your coupons on your Store profile.'); ?>
      </p>
    <?php else: ?>
      <p>
        <?php echo $this->translate('Are you sure you want to remove this coupon as featured?'); ?>
      </p>
    <?php endif; ?>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->offer_id ?>"/>
      <?php if (empty($this->sticky)): ?>
        <button type='submit' data-theme="b"><?php echo $this->translate('Make Featured'); ?></button>
      <?php else: ?>
        <button type='submit' data-theme="b"><?php echo $this->translate('Remove as Featured'); ?></button>
      <?php endif; ?>
    <div style="text-align: center"><?php echo $this->translate('or'); ?> </div>
    <a href="#" data-rel="back" data-role="button">
      <?php echo $this->translate('Cancel') ?>
    </a>
    </p>
  </div>
</form>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>