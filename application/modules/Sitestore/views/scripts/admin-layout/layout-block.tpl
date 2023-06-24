<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: layout-block.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2 class="fleft"><?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin'); ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>

<div class='tabs'>
	<ul class="navigation">
		<li >
			<?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestore','controller'=>'defaultlayout','action'=>'index'), $this->translate('Store Profile Layout Type'), array())
			?>
		</li>

		<li>
			<?php
			echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestore','controller'=>'layout','action'=>'layout', 'store' => $this->store_id), $this->translate('Store Profile Layout Editor'), array())
		  ?>
		</li>

    <li class="active">
			<?php
			echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestore','controller'=>'layout','action'=>'layout-block'), $this->translate('Store Profile Layout Settings'), array())
		  ?>
		</li>
	</ul>
</div>
<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0)):?>
  <div class='clear sitestore_settings_form'>
    <div class='settings'>
      <?php echo $this->form->render($this); ?>
    </div>
  </div>
<?php else :?>

		<div class="tip">
	  	<span><?php echo $this->translate('You have disabled Store Profile Layout editing by their owners from the "Edit Store Layout" field in Global Settings. If you enable it, then from here you will be able to choose which blocks / widgets of "Core" and "SocialEngineAddOns" modules should be available to users on their Store Profile. Currently, you can configure Store Profile Layout from the "Layout" > "Layout Editor" section by selecting "Store Profile" from the "Editing" dropdown.'); ?></span>
	  </div>

<?php endif;?>
