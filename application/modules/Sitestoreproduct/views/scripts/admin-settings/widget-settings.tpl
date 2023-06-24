<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: widget-settings.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>


<h2 class="fleft">
  <?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin');?>
</h2>

<?php if (count($this->navigationStore)): ?>
	<div class='seaocore_admin_tabs'>
		<?php echo $this->navigation()->menu()->setContainer($this->navigationStore)->render() ?>
	</div>
<?php endif; ?>

<?php if( count($this->navigationStoreWidget) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigationStoreWidget)->render()
    ?>
  </div>
<?php endif; ?>

<?php if( !empty($this->form) && @count($this->form->getElements()) > 1  ) : ?>
<div class='seaocore_settings_form'>
	<div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>
<?php else: ?>
<div class='tip'>
	<span>
    <?php echo "It seems that you have removed all the default widgets placed during this plugin's installation. Thus, all the associated settings have also been removed from here."; ?>
  </span>
</div>

<?php endif; ?>
