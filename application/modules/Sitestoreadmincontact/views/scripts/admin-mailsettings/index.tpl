<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreadmincontact
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2 class="fleft"><?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin'); ?></h2>
<?php if (count($this->navigationStore)): ?>
  <div class='seaocore_admin_tabs clr'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigationStore)->render()
  ?>
  </div>
<?php endif; ?>


<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>
<?php $sitemailtemplates = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');?>
<div class='clear sitestore_settings_form'>
  <div class='settings'>
    <div class="tip">
	  	<span>
        <?php if($sitemailtemplates):?>
					<?php echo $this->translate('To configure the email template, please click %s.',$this->htmlLink(array('route'=>'admin_default','module'=>'sitemailtemplates','controller'=>'settings'), $this->translate('here'), array('target' => '_blank'))); ?>
        <?php else:?>
          <?php echo $this->translate('To configure the email template, please click %s.',$this->htmlLink(array('route'=>'admin_default','module'=>'sitestore','controller'=>'settings','action'=>'email'), $this->translate('here'), array('target' => '_blank'))); ?>
        <?php endif;?>
      </span> 
	  </div>
  </div>
</div>
