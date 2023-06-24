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
<?php 
$breadcrumb = array(
    array("href"=>$this->sitestore->getHref(),"title"=>$this->sitestore->getTitle(),"icon"=>"arrow-r"),
    array("href"=>$this->sitestore->getHref(array('tab' => $this->tab_selected_id)),"title"=>"Coupons","icon"=>"arrow-d")
   );

echo $this->breadcrumb($breadcrumb);
?>
<?php if (!empty($this->offer_store)): ?>
  <form method="post" class="global_form_popup">
    <div>
      <h3><?php echo $this->translate('Delete Store Coupon ?'); ?></h3>
      <p>
        <?php echo $this->translate('Are you sure that you want to delete this coupon? It will not be recoverable after being deleted.'); ?>
      </p>
      <br />
      <p>
        <input type="hidden" name="confirm" value="<?php echo $this->offer_id ?>"/>
        <button type='submit' data-theme="b"><?php echo $this->translate('Delete'); ?></button>
          <div style="text-align: center"><?php echo $this->translate('or'); ?> </div>
          <a href="#" data-rel="back" data-role="button">
            <?php echo $this->translate('Cancel') ?>
          </a>
      </p>
    </div>
  </form>
<?php else: ?>
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
          <button type='submit' data-theme="b"><?php echo $this->translate('Delete'); ?></button>
           <div style="text-align: center"><?php echo $this->translate('or'); ?> </div>
          <a href="#" data-rel="back" data-role="button">
            <?php echo $this->translate('Cancel') ?>
          </a>
        </p>
      </div>
    </div>
  </form>
<?php endif; ?>

