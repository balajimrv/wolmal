<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<div class="tip">
  <span> <?php echo $this->translate('No Stores have been created yet.'); ?>
    <?php if ($this->can_create): ?>
      <?php
      if (Engine_Api::_()->sitestore()->hasPackageEnable()):
        $createUrl = $this->url(array("action" => "startup"), "sitestoreproduct_general", true); //$this->url(array('action' => 'index'), 'sitestore_packages');
      else:
        $createUrl = $this->url(array("action" => "startup"), "sitestoreproduct_general", true); // $this->url(array('action' => 'create'), 'sitestore_general');
      endif;
      ?>
  <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $createUrl . '">', '</a>'); ?>
<?php endif; ?>
  </span>
</div>