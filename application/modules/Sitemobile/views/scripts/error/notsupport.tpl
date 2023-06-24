<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: requiresubject.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if (!Engine_API::_()->sitemobile()->isApp()): ?>
  <?php if ($this->viewer()->getIdentity() && $this->viewer()->level_id < 3): ?>
    <div  class="ui-collapsible ui-collapsible-inset ui-corner-all ui-collapsible-themed-content">
      <h3 class="ui-collapsible-heading">
        <a href="#" class="ui-collapsible-heading-toggle ui-btn ui-fullsize ui-btn-icon-left ui-btn-up-e">
          <span class="ui-btn-inner">
            <span class="ui-btn-text">
              <?php echo $this->translate('SITEMOBILE_MODULE_NOT_SUPPORT') ?>
            </span>
            <span class="ui-icon ui-icon-shadow ui-icon-alert">&nbsp;</span>
          </span></a></h3>
      <div class="ui-collapsible-content ui-body-e" aria-hidden="false">
        <?php if (empty($this->notSupportedForPage)): ?>
          <p>
            <?php echo $this->translate('SITEMOBILE_MODULE_NOT_SUPPORT_DESC') ?>
          </p>
        <?php else: ?>
          <p>
            <?php echo $this->translate('SITEMOBILE_MODULE_NOT_SUPPORT_DESC_FOR_SOMEPAGES') ?>
          </p>
        <?php endif; ?>
        <a data-role="button" data-theme ='b' class ='no-dloader' data-ajax ="false" data-ajax ="false" data-icon="chevron-right" data-iconpos="right" href="<?php echo Zend_Controller_Front::getInstance()->getRouter()->assemble(array()) ?>?switch-mode=standard"  > <?php echo $this->translate('View Full Site') ?></a>
      </div>
    </div>
  <?php else: ?>
    <div  class="ui-collapsible ui-collapsible-inset ui-corner-all ui-collapsible-themed-content">
      <h3 class="ui-collapsible-heading">
        <a href="#" class="ui-collapsible-heading-toggle ui-btn ui-fullsize ui-btn-icon-left ui-btn-up-e">
          <span class="ui-btn-inner">
            <span class="ui-btn-text">
              <?php echo $this->translate('Page Not Mobile Friendly') ?>
            </span>
            <span class="ui-icon ui-icon-shadow ui-icon-alert">&nbsp;</span>
          </span></a></h3>
      <div class="ui-collapsible-content ui-body-e" aria-hidden="false">
        <p>
          <?php echo $this->translate("The mobile friendly view of this page is not available. We're working on this and will have it up soon. Meanwhile, you can visit this on the full site by clicking below.") ?>
        </p>
        <a data-role="button" data-theme ='b' class ='no-dloader' data-ajax ="false" data-icon="chevron-right" data-iconpos="right" href="<?php echo Zend_Controller_Front::getInstance()->getRouter()->assemble(array()) ?>?switch-mode=standard"  > <?php echo $this->translate('View Full Site') ?></a>
      </div>
    </div>
  <?php endif; ?>
<?php else: ?>
  <div  class="ui-collapsible ui-collapsible-inset ui-corner-all ui-collapsible-themed-content">
    <h3 class="ui-collapsible-heading">
      <a href="#" class="ui-collapsible-heading-toggle ui-btn ui-fullsize ui-btn-icon-left ui-btn-up-e">
        <span class="ui-btn-inner">
          <span class="ui-btn-text">
            <?php echo $this->translate('Page Not Available In App') ?>
          </span>
          <span class="ui-icon ui-icon-shadow ui-icon-alert">&nbsp;</span>
        </span></a></h3>
    <div class="ui-collapsible-content ui-body-e" aria-hidden="false">

      <p> <?php if ($this->viewer()->getIdentity() && $this->viewer()->level_id < 3): ?>
                 <?php echo $this->translate("Woops! Sorry, this content is not available in our app right now. You can enable the plugin for this content in the Mobile App from the Admin Panel. Before doing so, please first check if this plugin is compatible with the Mobile App.") ?>
        <?php else: ?>
         <?php echo $this->translate("Woops! Sorry, this content is not available in our app right now.") ?>
        <?php endif; ?>
      </p>

      <a data-role="button" data-theme ='b' data-rel='back' data-icon='arrow-l'  > <?php echo $this->translate('Go back') ?></a>
    </div>
  </div>
<?php endif; ?>

