<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreinvite
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: getyahoocontacts.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/styles/style_sitestoreinvite.css');

if (!$this->errormessage) {
 
	if (empty($this->addtofriend)) { ?>
	<div id='show_nonsitefriends' style="display:block;">
	<?php
	} else { ?>
	<div id='show_nonsitefriends' style="display:none;">
	<?php
	}
	$total = count($this->addtononfriend);
	if ($total > 0) { ?>
		<div class="header">	
			<div class="title">	
				<?php echo $this->translate("Found %s Yahoo contacts you can promote this Store to.", $total);?>
			</div>
			<div>
				<br /><?php echo $this->translate("Select the contacts to invite to your Store from the list below.");?>
			</div>	
		</div>
	<?php  
		
	}
	if (!empty($this->addtononfriend)) { ?>
		<div class="member-friend-list">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="5%">
						<input type="checkbox" name="nonsiteselect_all"  id="nonsiteselect_all" onclick="nonsitecheckedAll();" checked="checked">
					</td>
					<td colspan="2">
						<b><?php echo $this->translate("Select all");?></b>
					</td>
				</tr>
				<?php
				$total_contacts = 0;
				foreach($this->addtononfriend as $values) {
					$total_contacts++;?>
				<tr>
					<td width="5%">
						<input type="checkbox" name="nonsitecontactname_"<?php echo $total_contacts;?>  id="nonsitecontact_<?php echo $total_contacts;?>" checked="checked" value='<?php echo $values['contactMail'];?>'>
					</td>
					<td>
						<b><?php echo $values['contactName'];?></b>
					</td>
					<td>
						<?php echo $values['contactMail']; ?>
					</td>
				</tr>	
					
				<?php
					
				} ?>

				<input type="hidden" name="nonsitetotal_contacts"  id="nonsitetotal_contacts" value="<?php echo $total_contacts;?>"  onclick="nonsiteInviteFriends();">
			</table>
		</div>
		<?php  
			$getYahooPer = Zend_Registry::isRegistered('sitestoreinvite_getYahooPer') ? Zend_Registry::get('sitestoreinvite_getYahooPer') : null;
			if( !empty($getYahooPer) ):
		?>
		<div class="buttons">
			<button name="invitefriends"  id="invitefriends" onclick="invitePreview();" class="sitestoreinvite-send-button"><?php echo $this->translate("Send");?></button>
			<form action="" method="post" >	
				<?php echo $this->translate("or");  ?> <button class="disabled" name="skip_invite"  id="skip_invite"  type="submit"><?php echo $this->translate("Skip");?></button>
			</form>
		</div>
		<?php endif; ?>
	<?php
	} ?>
	</div>
<?php
}
else {
	echo "<div>" . $this->translate("All your imported contacts are already members of") .  ' ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title') . ".</div>";
}
?>