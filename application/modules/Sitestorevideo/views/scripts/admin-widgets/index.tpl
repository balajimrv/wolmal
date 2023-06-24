<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
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

<div class='tabs'>
  <ul class="navigation">
    <li class="active">
    <?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestorevideo','controller'=>'settings','action'=>'index'), $this->translate('Store Settings'), array())
    ?>
    </li>
    <li>
    <?php
      echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestoreproduct','controller'=>'video','action'=>'show-video'), $this->translate('Product Settings'), array())
    ?>
    </li>			
  </ul>
</div>

<div class='tabs'>
  <?php
  echo $this->navigation()
          ->menu()
          ->setContainer($this->navigation)
          ->render();
  ?>
</div>
<?php if( count($this->subNavigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->subNavigation)->render()
    ?>
  </div>
<?php endif; ?>
<br />

<div class="tip">
	<span>
		<?php echo $this->translate('We have moved these "Widget Settings" to "Layout Editor". You can change the desired settings of the respective widgets from "Layout Editor" by clicking on the "edit" link.');?>
	</span>
</div>