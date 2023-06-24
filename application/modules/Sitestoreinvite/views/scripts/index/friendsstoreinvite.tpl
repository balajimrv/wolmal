<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreinvite
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: friendsstoreinvite.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
  include APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/Adintegration.tpl';
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitestoreinvite/externals/styles/style_sitestoreinvite.css');
?>
<div class="sitestore_viewstores_head">
	<?php echo $this->htmlLink($this->sitestore->getHref(), $this->itemPhoto($this->sitestore, 'thumb.icon', '', array('align' => 'left'))) ?>
  <?php if(!empty($this->can_edit)):?>
		<div class="fright">
			<a href='<?php echo $this->url(array('store_id' => $this->sitestore->store_id), 'sitestore_edit', true) ?>' class='buttonlink icon_sitestores_dashboard'><?php echo $this->translate('Dashboard');?></a>
		</div>
	<?php endif;?>
	<h2>	
	  <?php echo $this->sitestore->__toString() ?>	
	  <?php echo $this->translate('&raquo; ');?>
		<b><?php echo $this->translate('Invite & Promote'); ?></b>
    <?php //echo $this->htmlLink(array( 'route' => 'sitestore_entry_view', 'store_url' => Engine_Api::_()->sitestore()->getStoreUrl($this->sitestore->store_id)), $this->translate('Promote')) ?>
  </h2>
</div>
<!--RIGHT AD START HERE-->
<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adinvite', 3) && $store_communityad_integration):?>
	<div class="layout_right" id="communityad_invite">
    <?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adinvite', 3),"loaded_by_ajax"=>0,'widgetId'=>"store_invite"))?>		
	</div>
<?php endif;?>
<!--RIGHT AD END HERE-->
<div class="layout_middle">
	<div class="storeinvites-friends">
		<div class="store-heading"><?php echo $this->translate("Tell your friends, fans and customers about your Store"); ?></div>
		<!--<div class="store-des">
			<?php //echo $this->translate("You can use any of the tools on this store to find and connect with more friends."); ?>
		</div>-->
	</div>
	<?php if ($this->viewer()->getIdentity()) { ?>
	<div id="id_show_networkcontacts" style="display:block"  class="storeinvites-friends">
		<div class="header">	
			<div class="title">	
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/images/webmail.png', '') ?>
				<?php echo $this->translate("Find Your Contacts"); ?>				
			</div>
			<div class="webmail-options">
				<a href="javascript:void(0);" onclick="showhide('id_show_networkcontacts', 'id_csvcontacts')"><?php echo $this->translate("Upload Contact File"); ?></a><br>
				<div class="icons">
					<a href="javascript:void(0);" onclick="showhide('id_show_networkcontacts', 'id_csvcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/images/addressbook.png', '',array('title' => $this->translate('Mac Address Book'))) ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_show_networkcontacts', 'id_csvcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/images/linkedin16.png', '',array('title' => $this->translate('LinkedIn Address Book'))) ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_show_networkcontacts', 'id_csvcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/images/thunderbird.png', '',array('title' => $this->translate('Thunderbird Address Book'))) ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_show_networkcontacts', 'id_csvcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/images/outlook.png', '',array('title' => $this->translate('Microsoft Outlook Address Book'))) ?></a>
					
					<a href="javascript:void(0);" onclick="showhide('id_show_networkcontacts', 'id_csvcontacts')"><?php echo $this->translate("more"); ?> <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/images/plus.png', '') ?></a>
				</div>
        	<div class="help-link">
        	
						<a href="<?php echo $this->baseUrl() . '/invite';?>" >
						<?php echo $this->htmlLink(array('route' => 'sitestoreinvite_invitefriends', 'sitestore_id' => $this->invitestore_id, 'user_id' => $this->invitestore_userid), $this->translate('or Add Email Addresses Manually'), array(
                'class' => 'smoothbox',
              )) ?>
							
						</a>
				</div>
			</div>
		</div>
			<div class="sub-title">
				<?php echo $this->translate("Search from your Web Accounts."); ?>                        
			</div>
			<div class="webacc-logos">
				 <!--FINDING FRIENDS FROM USER'S GOOGLE CONTACTS LIST.-->
			<?php if( is_array($this->webmail_show) && in_array("gmail", $this->webmail_show) ) { ?>
				<div class="webacc-logos-img">
					<a href='javascript:void(0)' onclick="show_contacts_google (1);" ><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/images/gmail.png', '') ?></a>
				</div>	
			<?php } ?>
			
			<!--FINDING FRIENDS FROM USER'S YAHOO CONTACTS LIST.-->
			<?php $yahoo_apikey = Engine_Api::_()->getApi('settings', 'core')->getSetting('yahoo.apikey');
						$yahoo_secret = Engine_Api::_()->getApi('settings', 'core')->getSetting('yahoo.secretkey');
			 if (!empty($yahoo_apikey) && !empty($yahoo_secret) && is_array($this->webmail_show) && in_array("yahoo", $this->webmail_show) ) {?>
			 	<div class="webacc-logos-img">
					<a href='javascript:void(0)' onclick="show_contacts_yahoo (1);" ><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/images/yahoomail.png', '') ?></a>
				</div>	
      <?php } ?>

			<!--FINDING FRIENDS FROM USER'S WINDOW LIVE CONTACTS LIST.-->
			<?php $windowlive_apikey = Engine_Api::_()->getApi('settings', 'core')->getSetting('windowlive.apikey');
						$windowlive_secret = Engine_Api::_()->getApi('settings', 'core')->getSetting('windowlive.secretkey');
			 if (!empty($windowlive_apikey) && !empty($windowlive_secret) && is_array($this->webmail_show) && in_array("window_mail", $this->webmail_show) ) {?>
			 <div class="webacc-logos-img">
					<a href='javascript:void(0)' onclick="show_contacts_windowlive (1);" ><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/images/windowslive.png', '') ?></a>
				</div>	
			<?php } ?>
			
			<!--FINDING FRIENDS FROM USER'S FACEBOOK CONTACTS LIST.-->
			<?php
		        $client_id = Engine_Api::_()->getApi('settings', 'core')->core_facebook_appid;
		        $client_secret = Engine_Api::_()->getApi('settings', 'core')->core_facebook_secret;

    			 if (!empty($client_id) && !empty($client_secret) && is_array($this->webmail_show) && in_array("facebook_mail", $this->webmail_show)) {
    	?>
    			  <div class="webacc-logos-img">  			
    					<a href='javascript:void(0)' onclick="show_contacts_Facebook(1);" ><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/images/facebook.png', '') ?></a>
    				</div>	
    				 
    			<?php } ?>
    			
    			<?php
	          $twittersettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.twitter');
		        if (!empty($twittersettings['key']) && !empty($twittersettings['secret']) && in_array("twitter_mail", $this->webmail_show) ) {
    			 ?>
    			  <div class="webacc-logos-img">  			
    					<a href='javascript:void(0)' onclick="show_contacts_Twitter(1);" ><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/images/twitter.png', '') ?></a>
    				</div>	
    				 
    			<?php } ?>
    			
    				<!--FINDING FRIENDS FROM USER'S LINKEDIN CONTACTS LIST.-->
				<?php $linkedin_apikey = Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.apikey');
							$linkedin_secret = Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.secretkey');
				 if (!empty($linkedin_apikey) && !empty($linkedin_secret) && in_array("linkedin_mail", $this->webmail_show) && false ) {?>
					<div class="webacc-logos-img">
				 		<a href='javascript:void(0)' onclick="show_contacts_linkedin (1);" ><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/images/linkedin.png', '', array('align' => 'left')) ?></a>
				 	</div>	
	      <?php } ?>
	      
	      
    			
    			
    			<!--FINDING FRIENDS FROM USER'S AOL CONTACTS LIST.-->
			<?php if( is_array($this->webmail_show) && in_array("aol", $this->webmail_show) ) { ?>
				<div class="webacc-logos-img">
					<a href='javascript:void(0)' onclick="show_contacts_aol (1);" ><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/images/aol.png', '') ?></a>
				</div>	
			<?php } ?>
			</div>        
			<div class="sub-txt">
			<?php echo $this->translate("Click on one of the above services to search from your Web Account."); ?>
			<br />
			<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/images/lock.gif', '') ?>
			<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title');?>
			<?php echo $this->translate("will not store your account information."); ?>			
		</div>
	</div>
	
	<div id="id_csvcontacts" style="display:none" class="storeinvites-friends">
		<div class="header">	
			<div class="title">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/images/webmail.png', '') ?>
				<?php echo $this->translate("Find Your Contacts"); ?>				
			</div>	
			<div class="webmail-options">
				<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->translate("Use your Web Accounts"); ?></a><br>
				<div class="icons">
					<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/images/gmail16.png', '') ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/images/yahoo16.png', '') ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/images/windowslive16.png', '') ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/images/facebook16.png', '') ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/images/twitter16.png', '') ?></a>
					<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/images/linkedin16.png', '') ?></a>
					
					<a href="javascript:void(0);" onclick="showhide('id_csvcontacts', 'id_show_networkcontacts')"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/images/aol16.png', '') ?></a>
				</div>
        <div class="help-link">
					<?php echo $this->htmlLink(array('route' => 'sitestoreinvite_invitefriends', 'sitestore_id' => $this->invitestore_id, 'user_id' => $this->invitestore_userid), $this->translate('or Add Email Addresses Manually'), array(
                'class' => 'smoothbox',
              )) ?>
				</div>
			</div>
		</div>
		<div class="sub-title">
			<?php echo $this->translate("Search your contacts in your contact file."); ?>			
		</div>
		<div class="upload-contact-file">
			<div class="op-cat"><?php echo $this->translate("Contact file :"); ?></div>
			<div class="op-field">
				<iframe id='ajaxframe' name='ajaxframe' style='display: none;' src='javascript:void(0);' onchange="myform();"></iframe>
				<form method="post" action="<?php echo $this->baseUrl() . '/seaocore/usercontacts/uploads'?>" name="csvimport" id="csvimport" enctype="multipart/form-data" target="ajaxframe"> 

					<input name="Filedata"  class="inputbox" type="file"  id="Filedata"  size="23" value="" onchange="savefilepath();"><br />
					<span><?php echo $this->translate("Contact file must be of .csv or .txt format"); ?></span><br />
					<button class="mtop10" id="csvmasssubmit" name="csvmasssubmit" onClick="getcsvcontacts();return false;"><?php echo $this->translate("Find Friends"); ?></button>
				</form>
			</div>	
		</div>
		<div class="help-link">
			<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/images/support.png', '') ?>
			<a href="javascript:void(0);" onclick="show_services();"><?php echo $this->translate("Supported Services"); ?></a><br>
			<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/images/help.png', '') ?>
			<a href="javascript:void(0);" onclick="show_createfile();"><?php echo $this->translate("How to create a contact file"); ?></a>
		</div>	
	</div>
<?php } ?>

	<div id="id_nonsite_success_mess" style="display:none;">
		<ul class="form-notices sitestoreinvite-success-message"><li><?php echo $this->translate("Your Store invitation(s) were sent successfully."); ?></li></ul>
	</div>		
    <br />
	<div class="storeinvites-friends" style="display:none;" id="network_friends">
		<div id="show_contacts"> </div>
	</div>		

	<div id="id_csvformate_error_mess" style="display:none;">
		<ul class="form-errors"><li><ul class="errors"><li><?php echo $this->translate("Invalid file format."); ?></li></ul></li></ul>
  </div>
   <br />
	<div class="storeinvites-friends" style="display:none;" id="network_friends">
		<div id="show_contacts"> </div>
	</div>		

	
	<div class="storeinvites-friends" style="display:none;" id="csv_friends">
		<div id="show_contacts_csv"> </div>
	</div>		
	
<div id="storeinvite_list_box">
<?php
// If no record found then show message.
if(isset($this->message))
{
	echo $this->message;
}
?>
</div>

<div style="display:none" id="myid">
	<div class="storeinvites-help-popup">
		<ul>
			<li>
				<a href='javascript: toggle("id_outlook")'><?php echo $this->translate('Microsoft Outlook'); ?></a>
				<ul style="display:none;" id="id_outlook">
					<li>	
						<?php echo $this->translate('To export a CSV file from Microsoft Outlook:'); ?>						
						<ol>
							<li><?php echo $this->translate('1. Open Outlook'); ?></li>
							<li><?php echo $this->translate("2. Go to File menu and select 'Import and Export'"); ?></li>
							<li><?php echo $this->translate("3. In the wizard window that appears, select 'Export to a file' and click 'Next'"); ?></li>
							<li><?php echo $this->translate("4. Select 'Comma separated values (Windows)' and click 'Next'"); ?></li>
							<li><?php echo $this->translate("5. Select where you want to save the exported CSV file, choose a name for your file (example : mycontacts.csv) and click 'Next'"); ?></li>
							<li><?php echo $this->translate("6. Ensure that the checkbox next to 'Export..' is checked and click 'Finish'."); ?></li>
						</ol>	
					</li>	
				</ul>
			</li>
			<li>
				<a href='javascript: toggle("id_microsoftoutlook")'><?php echo $this->translate('Microsoft Outlook Express'); ?></a>
				<ul style="display:none" id="id_microsoftoutlook">
					<li>
						<?php echo $this->translate('To export a CSV file from Microsoft Outlook Express:'); ?>
						
						<ol>
							<li><?php echo $this->translate('1. Open Outlook Express'); ?></li>
							<li><?php echo $this->translate("2. Go to File menu and select 'Export', and then click 'Address Book'"); ?></li>
							<li><?php echo $this->translate("3. Select 'Text File (Comma Separated Values)', and then click 'Export'"); ?></li>
							<li><?php echo $this->translate("4. Select where you want to save the exported CSV file, choose a name for your file (example : mycontacts.csv) and click 'Next'"); ?></li>
							<li><?php echo $this->translate("5. Select the check boxes for the fields that you want to export (be sure to select the email address field), and then click 'Finish'."); ?></li>
						</ol>	
					</li>	
				</ul>
			</li>
			<li>
		    <a href='javascript: toggle("id_mozila_thunder")'><?php echo $this->translate('Mozilla Thunderbird'); ?></a>
				<ul style="display:none" id="id_mozila_thunder">
					<li>
						<?php echo $this->translate('To export a CSV file from Mozilla Thunderbird:'); ?>
						
						<ol>
							<li><?php echo $this->translate('1. Open Mozilla Thunderbird'); ?></li>
							<li><?php echo $this->translate("2. Go to Tools menu and select 'Address Book'"); ?></li>
							<li><?php echo $this->translate("3. In the 'Address Book' window that opens, select 'Export...' from the Tools menu"); ?></li>
							<li><?php echo $this->translate("4. Select where you want to save the exported file, choose 'Comma Separated (*.CSV)' under the 'Save as type' dropdown list, choose a name for your file (example : mycontacts.csv) and click 'Save'."); ?></li>
						</ol>	
					</li>	
				</ul>
			</li>
			<li>
				<a href='javascript: toggle("id_linkedin")'><?php echo $this->translate('LinkedIn'); ?></a>
				<ul style="display:none" id="id_linkedin">
					<li>
						<?php echo $this->translate('To export a CSV file from LinkedIn:'); ?>
						
						<ol>
							<li><?php echo $this->translate('1. Sign into your LinkedIn account'); ?></li>
							<li><?php echo $this->translate('2. Visit the'); ?> <a href='http://www.linkedin.com/addressBookExport' target="_blank"><?php echo $this->translate('Address Book Export'); ?></a><?php echo $this->translate(' store'); ?></li>
							<li><?php echo $this->translate("3. Select 'Microsoft Outlook (.CSV file)' under the 'Export to' dropdown list and click 'Export'"); ?></li>
							<li><?php echo $this->translate('4. Select where you want to save the exported CSV file, choose a name for your file (example : mycontacts.csv).'); ?></li>
						</ol>
					</li>	
				</ul>
			</li>
			<li>
				<a href='javascript: toggle("id_windowabook")'><?php echo $this->translate('Windows Address Book'); ?></a>
				<ul style="display:none" id="id_windowabook">
					<li>
						<?php echo $this->translate('To export a CSV file from Windows Address Book:'); ?>
					<ol>
							<li><?php echo $this->translate('1. Open Windows Address Book'); ?></li>
							<li><?php echo $this->translate("2. Go to the File menu, select 'Export', and then select 'Other Address Book...'"); ?></li>
							<li><?php echo $this->translate("3. In the 'Address Book Export Tool' dialog that opens, select 'Text File (Comma Separated Values)' and click 'Export'"); ?></li>
							<li><?php echo $this->translate("4. Select where you want to save the exported CSV file, choose a name for your file (example : mycontacts.csv) and click 'Next'"); ?></li>
							<li><?php echo $this->translate("5. Select the check boxes for the fields that you want to export (be sure to select the email address field), and then click 'Finish'."); ?></li>
							<li><?php echo $this->translate("6. Click 'OK' and then click 'Close'"); ?></li>
						</ol>
					</li>	
				</ul>
			</li>	
			<li>
				<a href='javascript: toggle("id_macos")'><?php echo $this->translate('Mac OS X Address Book'); ?></a>
				<ul style="display:none" id="id_macos">
					<li>
					<?php echo $this->translate('To export a CSV file from Mac OS X Address Book:'); ?>
					
						<ol>
							<li><?php echo $this->translate('1. Download the free Mac Address Book exporter from'); ?> <a href='http://www.apple.com/downloads/macosx/productivity_tools/exportaddressbook.html' target="_blank">here</a>.</li>
							<li><?php echo $this->translate('2. Choose to export your Address Book in CSV format.'); ?></li>
							<li><?php echo $this->translate('3. Save your exported address book in CSV format.'); ?></li>
						</ol>	
					</li>	
				</ul>
			</li>	
			<li>
				<a href='javascript: toggle("id_palmdesktop")'><?php echo $this->translate('Palm Desktop'); ?></a>
				<ul style="display:none" id="id_palmdesktop">
					<li>
						<?php echo $this->translate('To export a CSV file from Palm Desktop:'); ?>
						
						<ol>
							<li><?php echo $this->translate('1. Open Palm Desktop'); ?></li>
							<li><?php echo $this->translate("2. Click on the 'Addresses' icon on the lefthand side of the screen to display your contact list"); ?></li>
							<li><?php echo $this->translate("3. Go to the File menu, select 'Export'"); ?></li>
							<li><?php echo $this->translate('4. In the dialog box that opens, do the following:'); ?></li>
							<li><?php echo $this->translate("5. Enter a name for the file you are creating in the 'File name:' field"); ?></li>
							<li><?php echo $this->translate("6. Select 'Comma Separated' in the 'Export Type' pulldown menu"); ?></li>
							<li><?php echo $this->translate("7. Be sure to select the 'All' radio button from the two 'Range:' radio buttons"); ?></li>
							<li><?php echo $this->translate("8. In the second dialog box: 'Specify Export Fields' that opens, leave all of the checkboxes checked, and click 'OK'."); ?></li>
						</ol>
					</li>
				</ul>
			</li>
			<li>
				<a href='javascript: toggle("id_windowmail")'><?php echo $this->translate('Windows Mail'); ?></a>
				<ul style="display:none" id="id_windowmail">
					<li>
						<?php echo $this->translate('To export a CSV file from Windows Mail:'); ?>
						
						<ol>
							<li><?php echo $this->translate('1. Open Windows Mail'); ?>
							<li><?php echo $this->translate('2. Select: Tools | Windows Contacts... from the menu in Windows Mail'); ?>
							<li><?php echo $this->translate("3. Click 'Export' in the toolbar"); ?>
							<li><?php echo $this->translate("4. Make sure CSV (Comma Separated Values) is highlighted, then click 'Export'"); ?>
							<li><?php echo $this->translate("5. Select where you want to save the exported CSV file, choose a name for your file (example : mycontacts.csv) and click 'Next'"); ?>
							<li><?php echo $this->translate("6. Click 'Save' then click 'Next'"); ?>
							<li><?php echo $this->translate('7. Make sure all address book fields you want included are checked'); ?>
							<li><?php echo $this->translate("8. Click 'Finish'"); ?></li>
							<li><?php echo $this->translate("9. Click 'OK' then click 'Close'"); ?></li>
						</ol>	
					</li>	
				</ul>
			</li>	
			<li>
				<a href='javascript: toggle("id_othermail")'><?php echo $this->translate('For Other'); ?></a>
				<ul style="display:none" id="id_othermail">
					<li>
							<?php echo $this->translate('Many email services, email applications, address book management applications allow contacts to be imported to a file. We support .CSV and .TXT types of contact files'); ?>
						
					</li>	
				</ul>
			</li>	
			<script type="text/javascript">
			function toggle(divid){ 
			  var MyidHTML =  $('myid').innerHTML;
			  $('myid').innerHTML = '';
				var div1 = $(divid);
				if (div1.style.display == 'none') {
					div1.style.display = 'block'
				} else {
					div1.style.display = 'none'
				}
				 $('myid').innerHTML = MyidHTML;
			}
			</script>
		</ul>
	</div>	
	<button onclick="parent.Smoothbox.close();"><?php echo $this->translate('Close'); ?></button>
</div>
    <?php
    if(!empty($this->paginator)) {
			foreach( $this->paginator as $search_result ): 
				echo $this->htmlLink($search_result->getHref(), $this->itemPhoto($search_result, 'thumb.icon'), array('class' => 'popularmembers_thumb'));
				echo $this->htmlLink($search_result->getHref(), $search_result->getTitle());
			endforeach; 
			echo $this->paginationControl($this->paginator);
    }
   

if ($this->user_id) { 
  ?>
</div>
<form action="" id="id_myform_temp" name="id_myform_temp">

</form>
<?php } ?>
<?php
$session = new Zend_Session_Namespace();
$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/usercontacts.js');
  
$googleredirect = $session->googleredirect;
$yahooredirect = $session->yahooredirect;
$aolredirect = $session->aolredirect;
$windowlivemsnredirect = $session->windowlivemsnredirect;
$facebookredirect =$session->facebookredirect;
$linkedinredirect =$session->linkedinredirect;
$twitterredirect = $session->twitterredirect;
?>


<script type="text/javascript">
var semoduletype_id = '<?php echo $this->invitestore_id ;?>';
var invitestore_userid = '<?php echo $this->invitestore_userid ;?>';
semoduletype = 'sitestore';
var semoduletype_type = 'sitestore_store';
var invite_callbackURl = "<?php echo ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('sitestore_id' => $this->invitestore_id, 'user_id' => $this->invitestore_userid), 'sitestoreinvite_invite', true) ;?>";

//RETRIVING THE VALUE FROM SESSION AND CALL THE CORROSPONDING ACTION FOR WHICH SERVICE IS BEING CURRENTLY EXECUTING.
var googleredirect = '<?php echo $googleredirect;?>';
var yahooredirect = '<?php echo $yahooredirect;?>';
var aolredirect = '<?php echo $aolredirect;?>';
var windowliveredirect = '<?php echo $windowlivemsnredirect;?>';
var facebookredirect = '<?php echo $facebookredirect;?>';
var linkedinredirect = '<?php echo $linkedinredirect;?>';
var twitterredirect = '<?php echo $twitterredirect;?>';
if (googleredirect == 1 && window.opener!= null) { 
  show_contacts_google (0);
}
else if (yahooredirect == 1 && window.opener!= null) {
	show_contacts_yahoo (0);
}
else if (aolredirect == 1 && window.opener!= null) {
	show_contacts_aol (0);
}
else if (windowliveredirect == 1 && window.opener!= null) {
  show_contacts_windowlive (0);
}
else if (facebookredirect == 1 && window.opener!= null) {
  show_contacts_Facebook (0);
}
else if (linkedinredirect == 1 && window.opener!= null) { 
  show_contacts_linkedin (0);
}
else if (twitterredirect == 1 && window.opener!= null) { 
  show_contacts_Twitter (0);
}

if  (window.opener == null && facebookredirect == 1) {  
  <?php if (isset($_GET['redirect_fbinvite'])) : ?>
  facebookredirect = 0;
  window.addEvent('load', function() { 
	  
	     setTimeout('get_contacts_Facebook();', '1000');
	  
	  });
   <?php endif;?>  
  
}

function show_services() {
	 var supported_services = '<div class="storeinvites-mail-supported"> <h2><?php echo $this->string()->escapeJavascript($this->translate("Supported Services")) ?></h2><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreinvite/externals/images/outlook.png" alt="" /> <?php echo $this->string()->escapeJavascript($this->translate("Microsoft Outlook")) ?> <br /><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreinvite/externals/images/outlookexpress.gif" alt="" /><?php echo $this->string()->escapeJavascript($this->translate("Microsoft Outlook Express")) ?><br /><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreinvite/externals/images/thunderbird.png" alt="" /><?php echo $this->string()->escapeJavascript($this->translate("Mozilla Thunderbird")) ?> <br / ><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreinvite/externals/images/linkedin16.png" alt="" /> <?php echo $this->string()->escapeJavascript($this->translate("LinkedIn")) ?> <br /><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreinvite/externals/images/windowslive16.png" alt="" /> <?php echo $this->string()->escapeJavascript($this->translate("Windows Address Book")) ?> <br /><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreinvite/externals/images/addressbook.png" alt="" /> <?php echo $this->string()->escapeJavascript($this->translate("Mac OS X Address Book")) ?><br /><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreinvite/externals/images/palm.gif" width="16" alt="" /><?php echo $this->string()->escapeJavascript($this->translate("Palm Desktop")) ?><br /><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreinvite/externals/images/windowslive16.png" alt="" /> <?php echo $this->string()->escapeJavascript($this->translate("Windows Mail")) ?> <br /><img src="<?php echo $this->layout(
)->staticBaseUrl ?>application/modules/Sitestoreinvite/externals/images/plus.png" alt="" /> <?php echo $this->string()->escapeJavascript($this->translate("Other")) ?><br /><br /><button onclick="javascript:close_popup();"><?php echo $this->string()->escapeJavascript($this->translate("Close")) ?></button></div>';
	Smoothbox.open( supported_services);
 }

 function show_createfile() {
  if ($('myid').innerHTML != '') {
		var howToCreateFile = $('myid').innerHTML;
	}
	Smoothbox.open(howToCreateFile);
	
 }

function close_popup () {
 Smoothbox.close();

}
var fbinvitemessage = "<?php echo $this->translate('FACEBOOK_APP_INVITE_MESSAGE'); ?>";
window.addEvent('domready', function () { 
	 <?php if (!empty($client_id)) : ?>
	  fbappid= '<?php echo $client_id;?>';
	    
	  <?php endif;?>
	  
	   if (typeof FB == 'undefined' && typeof fbappid != 'undefined')  { 
    en4.seaocore.facebook.runFacebookSdk ();
  }
  
  
	
});

<?php if ($this->success_fbinvite): ?>

   if ($('id_nonsite_success_mess'))
      $('id_nonsite_success_mess').style.display = 'block';
   
<?php endif;?>
 
</script>
