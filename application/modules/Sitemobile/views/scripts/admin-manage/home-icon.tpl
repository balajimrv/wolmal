<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: home-icon.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<!--ADD NAVIGATION-->
<?php include APPLICATION_PATH . '/application/modules/Sitemobile/views/scripts/adminNav.tpl'; ?>

	<h3 class="sm-page-head"><?php echo $this->translate('Home Screen Icon Settings'); ?>
    <span><a href="<?php echo $this->url(array('module' => 'sitemobile', 'controller' => 'settings', 'action' => 'faq', 'faq_id' => 'faq_12'), 'admin_default', true) ?>/#faq_12" class="buttonlink icon_help" target="_blank"></a></span>
    <span><a href="https://lh3.googleusercontent.com/-mqkCpEHG2wg/UbXABIKJcWI/AAAAAAAAAXw/Mz6_ctRPOoQ/s512/Home-Screen-icon.jpg" title="View Screenshot" class="buttonlink sm_icon_view" target="_blank" ></a></span>
 </h3>   
	
	<p><?php echo $this->translate('Use this area to manage the mobile home icon for your website. You can upload home screen icon of your choice here. Click "Edit" to browse another icon for a particular size.'); ?> </p>
  
	<br />
<!--  If Home screen icon exist then display the icon with its all compatible sizes otherwise display the tip.-->
	<?php if ($this->photoUrl): ?>
		<div class="admin_menus_options">
			<?php echo $this->htmlLink(array('reset' => false, 'action' => 'add-icon'), $this->translate('Update Icon'), array('class' => 'buttonlink smoothbox seaocore_icon_edit')) ?>
			<?php echo $this->htmlLink(array('reset' => false, 'action' => 'remove-icon'), $this->translate('Delete Icon'), array('class' => 'buttonlink seaocore_icon_delete smoothbox')) ?> 
		</div>
		<br />
		<table class='admin_table' width="1000">
			<thead>
				<tr>
					<th><?php echo $this->translate('Preview '); ?></th>
					<th><?php echo $this->translate('Size'); ?></th>
					<th><?php echo $this->translate('Options'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->photoUrl as $key => $photo): ?>
				<tr>
					<td><img src="<?php echo $photo ?>" /></td>
					<td><?php echo $this->translate($key); ?></td>
					<td>
         - <?php echo $this->htmlLink(array('reset' => false, 'action' => 'crop-icon', 'key' => $key), $this->translate('Recreate from orginal image')) ?>
           <br/> 
         - <?php echo $this->htmlLink(array('reset' => false, 'action' => 'edit-icon', 'key' => $key), $this->translate('Edit'), array('class' => 'smoothbox')) ?> </td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php else: ?>
		<div class="tip"> 
			<span> 
				<?php echo $this->translate('You have not uploaded any mobile home screen icon yet. Please '); ?> <?php echo $this->htmlLink(array('reset' => false, 'action' => 'add-icon'), $this->translate('click here '), array('class' => 'smoothbox')) ?><?php echo $this->translate('to add a mobile home screen icon. ')?> 
			</span> 
		</div>
	<?php endif; ?>
