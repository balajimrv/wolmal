<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: enable-product.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<div class='global_form_popup'>
  <form method="POST" action="<?php echo $this->url() ?>">
    <div>
      <h3><?php echo $this->translate('Enable Product?'); ?></h3>
      <p>
        <?php echo $this->translate('Are you sure that you want to make this product enable?'); ?>
      </p>
      <p>&nbsp;
      </p>
      <p>
        <input type="hidden" name="product_id" value="<?php echo $this->product_id ?>"/>
        <button type='submit'><?php echo $this->translate('Enable'); ?></button>
        <?php echo $this->translate(' or ') ?> <a href="javascript:void(0);" onclick="parent.Smoothbox.close();"><?php echo $this->translate('cancel') ?></a>
      </p>
    </div>
  </form>
</div>