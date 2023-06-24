<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: publish.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class='global_form_popup'>
  <?php if ($this->success): ?>
    <div class="global_form_popup_message">
    <?php echo $this->translate('Your store has been published.'); ?>
  </div>
  <?php else: ?>
      <form method="POST" action="<?php echo $this->url() ?>">
        <div>
          <h3><?php echo $this->translate('Publish Store?'); ?></h3>
          <p>
        <?php echo $this->translate('Are you sure that you want to publish this Store?'); ?>
      </p>
      <p>&nbsp;
      </p>
      <p>
        <input type="hidden" name="store_id" value="<?php echo $this->store_id ?>"/>  
        <input type="hidden" value="" name="search"><input type="checkbox" checked="checked" value="1" id="search" name="search">
        <?php echo $this->translate("Show this store in search results."); ?>
        <br />
        <br />
      <button type='submit' data-theme="b" ><?php echo $this->translate('Publish'); ?></button>
              <div style="text-align: center"><?php echo $this->translate('or'); ?> </div>
          <a href="#" data-rel="back" data-role="button">
            <?php echo $this->translate('Cancel') ?>
          </a>
        </p>
      </div>
    </form>
  <?php endif; ?>
      </div>
