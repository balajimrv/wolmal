<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: claimstore.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/style_sitestore_profile.css');
?>
<?php if (!$this->status && $this->userclaim && $this->claimoption): ?>
  <div class="sitestore_tellafriend_popup">
    <?php echo $this->form->render($this); ?>
  </div>
<?php endif; ?>