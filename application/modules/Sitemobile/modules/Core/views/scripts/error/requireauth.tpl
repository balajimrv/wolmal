<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: requireauth.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<div  class="ui-collapsible ui-collapsible-inset ui-corner-all ui-collapsible-themed-content">
  <h3 class="ui-collapsible-heading">
    <a href="#" class="ui-collapsible-heading-toggle ui-btn ui-fullsize ui-btn-icon-left ui-btn-up-e">
      <span class="ui-btn-inner">
        <span class="ui-btn-text">
          <?php echo $this->translate('Private Page') ?>
        </span>
        <span class="ui-icon ui-icon-shadow ui-icon-alert">&nbsp;</span>
      </span></a></h3>
  <div class="ui-collapsible-content ui-body-e" aria-hidden="false">
    <p>
      <?php echo $this->translate('You do not have permission to view this private page.') ?>
    </p>
    <a  <?php echo $this->dataHtmlAttribs("go_back_button", array('data-role' => "button", 'data-rel' => "back", 'data-corners' => "true", 'data-shadow' => "true", 'data-iconshadow' => "true", 'data-theme' => "b", "data-icon" => "chevron-left")); ?> > <?php echo $this->translate('Go Back') ?></a>
  </div>
</div>
