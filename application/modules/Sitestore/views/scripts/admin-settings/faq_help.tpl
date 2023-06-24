<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  function faq_show(id) {
    if($(id).style.display == 'block') {
      $(id).style.display = 'none';
    } else {
      $(id).style.display = 'block';
    }
  }
</script>
<?php if(!empty($this->faq)) : ?>
	<p><?php echo $this->translate("Browse the different FAQ sections of this plugin by clicking on the corresponding tabs below.") ?><p>
	<br />
	<?php $action = 'faq' ?>
<?php else : ?>
	<?php $action = 'readme' ?>
<?php endif; ?>
<div class='seaocore_admin_tabs'>
		<ul class="navigation">
		  <li class="<?php if($this->faq_type == 'general') { echo "active"; } ?>">
		 	<?php echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitestore','controller' => 'settings','action' => $action, 'faq_type' => 'general'), $this->translate('General'), array())
		  ?>
			</li>
			<li class="<?php if($this->faq_type == 'package') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitestore','controller' => 'settings','action' => $action, 'faq_type' => 'package'), $this->translate('Packages'), array())
		  ?>
			</li>
			<li class="<?php if($this->faq_type == 'category') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitestore','controller' => 'settings','action' => $action, 'faq_type' => 'category'), $this->translate('Categories'), array())
		  ?>
			<li class="<?php if($this->faq_type == 'profile') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitestore','controller' => 'settings','action' => $action, 'faq_type' => 'profile'), $this->translate('Profile Fields'), array())
		  ?>
			</li>
<!--      <li class="<?php //if($this->faq_type == 'sitestoreproduct') { echo "active"; } ?>">
        <?php //echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitestore','controller' => 'settings','action' => $action, 'faq_type' => 'sitestoreproduct'), $this->translate('Products'), array())
      ?>
      </li>      -->
			<li class="<?php if($this->faq_type == 'sitestorealbum') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitestore','controller' => 'settings','action' => $action, 'faq_type' => 'sitestorealbum'), $this->translate('Photo Albums'), array())
		  ?>
			</li>
			<li class="<?php if($this->faq_type == 'sitestorevideo') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitestore','controller' => 'settings','action' => $action, 'faq_type' => 'sitestorevideo'), $this->translate('Videos'), array())
		  ?>
			</li>
			<li class="<?php if($this->faq_type == 'sitestorereview') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitestore','controller' => 'settings','action' => $action, 'faq_type' => 'sitestorereview'), $this->translate('Review & Ratings'), array())
		  ?>
			</li> 
			<li class="<?php if($this->faq_type == 'sitestoreurl') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitestore','controller' => 'settings','action' => $action, 'faq_type' => 'sitestoreurl'), $this->translate('Short Store URL'), array())
		  ?>
			</li>

			<li class="<?php if($this->faq_type == 'sitestoreform') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitestore','controller' => 'settings','action' => $action, 'faq_type' => 'sitestoreform'), $this->translate('Form'), array())
		  ?>
			</li>      
			<li class="<?php if($this->faq_type == 'sitestoreoffer') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitestore','controller' => 'settings','action' => $action, 'faq_type' => 'sitestoreoffer'), $this->translate('Coupons'), array())
		  ?>
			</li>

			<li class="<?php if($this->faq_type == 'sitestoreinviter') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitestore','controller' => 'settings','action' => $action, 'faq_type' => 'sitestoreinviter'), $this->translate('Inviter'), array())
		  ?>
			</li>      
			<li class="<?php if($this->faq_type == 'sitestoreadmincontact') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitestore','controller' => 'settings','action' => $action, 'faq_type' => 'sitestoreadmincontact'), $this->translate('Contact Store Owner'), array())
		  ?>
			</li>  
      <li class="<?php if($this->faq_type == 'editors') { echo "active"; } ?>">
        <?php echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitestore','controller' => 'settings','action' => $action, 'faq_type' => 'editors'), $this->translate('Editors'), array())
      ?>
      </li>         
			<li class="<?php if($this->faq_type == 'sitestorelikebox') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitestore','controller' => 'settings','action' => $action, 'faq_type' => 'sitestorelikebox'), $this->translate('Like Box'), array())
		  ?>
			</li> 
      <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument')): ?>
        <li class="<?php if($this->faq_type == 'sitestoredocument') { echo "active"; } ?>">
         <?php
          echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitestore','controller' => 'settings','action' => $action, 'faq_type' => 'sitestoredocument'), $this->translate('Documents'), array())
        ?>
        </li>
      <?php endif; ?>
      <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration')): ?>
        <li class="<?php if($this->faq_type == 'sitestoreintegration') { echo "active"; } ?>">
         <?php
          echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitestore','controller' => 'settings','action' => $action, 'faq_type' => 'sitestoreintegration'), $this->translate('Multiple Listings and Products Showcase'), array())
        ?>
        </li>
      <?php endif; ?>
			<li class="<?php if($this->faq_type == 'layout') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitestore','controller' => 'settings','action' => $action, 'faq_type' => 'layout'), $this->translate('Store Layout'), array())
		  ?>
			</li>
			<li class="<?php if($this->faq_type == 'claim') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitestore','controller' => 'settings','action' => $action, 'faq_type' => 'claim'), $this->translate('Claims'), array())
		  ?>
<!--			<li class="<?php //if($this->faq_type == 'insights') { echo "active"; } ?>">
		   <?php
		   // echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitestore','controller' => 'settings','action' => $action, 'faq_type' => 'insights'), $this->translate('Insights'), array())
		  ?>
			</li>-->
<!--			<li class="<?php //if($this->faq_type == 'import') { echo "active"; } ?>">
		   <?php
		    //echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitestore','controller' => 'settings','action' => $action, 'faq_type' => 'import'), $this->translate('Import'), array())
		  ?>
			</li>-->
			<li class="<?php if($this->faq_type == 'language') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitestore','controller' => 'settings','action' => $action, 'faq_type' => 'language'), $this->translate('Language'), array())
		  ?>
			</li>
        <?php //if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.isActivate',0)): ?>
<!--			<li class="<?php //if($this->faq_type == 'activity') { echo "active"; } ?>">
		   <?php
		    //echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitestore','controller' => 'settings','action' => 'activity-feed'), $this->translate('Activity Feed'), array())
		  ?>
			</li>-->
      <?php //endif; ?>
			<li class="<?php if($this->faq_type == 'customize') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitestore','controller' => 'settings','action' => $action, 'faq_type' => 'customize'), $this->translate('Customize'), array())
		  ?>
			</li>
			<li class="<?php if($this->faq_type == 'integration') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitestore','controller' => 'settings','action' => $action, 'faq_type' => 'integration'), $this->translate('Plugin Integration'), array())
		  ?>
			</li>
     
		</ul>
	</div>

<?php switch($this->faq_type) : ?>
<?php case 'sitestorelikebox': ?>
<div class="admin_sitestore_files_wrapper">
  <ul class="admin_sitestore_files sitestore_faq">
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("Where will the users see the option to generate code for Embeddable Store Badges / Like Boxes?"); ?></a>
      <div class='faq' style='display: none;' id='faq_1'>
        <?php echo $this->translate("Ans: Users will be able to configure and generate code for Embeddable Store Badges / Like Boxes from the “Marketing” section of their Store Dashboard."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("I want the embeddable badges / like box for Stores to match with the theme of my site. How can I do this?"); ?></a>
      <div class='faq' style='display: none;' id='faq_2'>
        <?php echo $this->translate("Ans. You can configure the 2 available color schemes (light and dark) for embeddable badges to match with your site’s theme. To do so, please go to the Color Scheme section and configure the color schemes using the style editor."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("The logo of my site is not being shown in “Powered By” on Embeddable Store Badges / Like Boxes. What would be the reason?"); ?></a>
      <div class='faq' style='display: none;' id='faq_3'>
        <?php echo $this->translate('Ans. Please use these 3 fields in the Like Box section to enable "Powered By" and upload the logo of your website: "Powered By", "Logo or Title", and "Upload Logo".'); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("How can I set the default height and width of the Embeddable Store Badges / Like Boxes?"); ?></a>
      <div class='faq' style='display: none;' id='faq_4'>
        <?php echo $this->translate('Ans. Use the "Badge Width" and "Badge Height" fields in Like Box section of this plugin. If you select "No" for "Badge Width", you will be able to specify the "Default Badge Width", and similarly for Badge Height.'); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("I want Embeddable Store Badges / Like Boxes to be available to only certain stores on my site. How can this be done?"); ?></a>
      <div class='faq' style='display: none;' id='faq_5'>
        <?php echo $this->translate("Ans. You can enable packages for stores on your site from Global Settings, and make “External Badge” App available to only certain packages. If you have not enabled packages, then from Member Level Settings, you can make External Embeddable Badge / Like Box to be available for stores of only certain member levels."); ?>
      </div>
    </li>


    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("I do not want all the Tabs / Store Apps to be shown in the Embeddable Store Badges / Like Boxes. I just want selected tabs to be available. What should I do?"); ?></a>
      <div class='faq' style='display: none;' id='faq_6'>
        <?php echo $this->translate("You can choose the tabs that should be available for display in embeddable store badges / like boxes from Like Box section of this plugin using the “Tabs from Apps” field and the fields above it."); ?>
      </div>
    </li>

    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("How are the sequence of tabs and tab names in Embeddable Store Badges / Like Boxes decided?"); ?></a>
      <div class='faq' style='display: none;' id='faq_7'>
        <?php echo $this->translate("Ans. The sequence of tabs and their names in Embeddable Store Badges / Like Boxes is the same as that on the main Store Profile."); ?>
      </div>
    </li>

<!--    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php //echo $this->translate("The CSS of this plugin is not coming on my site. What should I do?"); ?></a>
      <div class='faq' style='display: none;' id='faq_8'>
        <?php //echo $this->translate("Ans. Please enable the 'Development Mode' system mode for your site from the Admin homestore and then check the store which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'."); ?>
      </div>
    </li>-->

  </ul>
</div>  
<?php break;?>
<?php case 'sitestoredocument': ?>
<div class="admin_sitestore_files_wrapper">
  <ul class="admin_sitestore_files sitestore_faq">
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("I want Documents to be available to only certain directory items / stores on my site. How can this be done?"); ?></a>
      <div class='faq' style='display: none;' id='faq_1'>
        <?php echo $this->translate("Ans: You can enable packages for stores on your site, and make Documents available to only certain packages. If you have not enabled packages, then from Member Level Settings, you can make Documents to be available for stores of only certain member levels."); ?>
      </div>
    </li>	
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("How do I get my Scribd API details ?"); ?></a>
      <?php if ($this->show): ?>
        <div class='faq' style='display: block;' id='faq_2'>
      <?php else: ?>
        <div class='faq' style='display: none;' id='faq_2'>
      <?php endif; ?>
          <ul>
             <?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_getScribdKeys.tpl';?>           
          </ul>
        </div>
    </li>	

    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("How can I change the maximum limit for the document file size ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_3'>
        <?php echo $this->translate('Ans: Go to Global Settings and change the value of "Maximum file size" field.'); ?>
      </div>
    </li>	

    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("Can I create custom fields for Store Documents on my site?"); ?></a>
      <div class='faq' style='display: none;' id='faq_4'>
        <?php echo $this->translate('Ans: Yes, you can do so from the "Store Document Questions" section.'); ?></a>
      </div>
    </li>
		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo $this->translate("I had not selected to 'Enable Documents Module for Default Package' in 'Global Settings' section before activating this Plugin. But after activation, I am not able to view this option. Does this mean that this module is permanently disabled for Default Package?"); ?></a>
      <div class='faq' style='display: none;' id='faq_9'>
        <?php echo $this->translate("Ans: No, it does not mean so. You can still enable this module for Default Package from the 'Manage Packages' section of 'Directory/Stores Plugin' by editing the Default Package and selecting the 'Documents' checkbox in the 'Modules / Apps' field."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("I want to use this plugin for directory of car documents. How can I change the word: 'store documents' to 'car documents' in this plugin?"); ?></a>
      <div class='faq' style='display: none;' id='faq_5'>
        <?php echo $this->translate("Ans: You can easily use this plugin for creation of any type of directories. You can change the word 'store documents' to your directory type from the 'Layout' > 'Language Manager' section in the Admin Panel."); ?>
      </div>
    </li>
		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo $this->translate("I want to change the text 'storedocuments' to 'cardocuments' in the URLs of this plugin. How can I do so ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_10'>
        <?php echo $this->translate('Ans: To do so, please go to the Global Settings section of this plugin. Now, search for the field : Store Documents URL alternate text for "storedocuments"<br />Then, enter the text you want to display in place of \'storedocuments\' in the text box there.'); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_6'>
        <?php echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homestore and then check the store which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'."); ?></a>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("I am not able to find the Suggest to Friends feature for Store Documents. What can be the reason?"); ?></a>
      <div class='faq' style='display: none;' id='faq_7'>
        <?php echo $this->translate('Ans: The suggestions features are dependent on the %1$sSuggestions / Recommendations / People you may know & Inviter%2$s plugin and require that to be installed.', '<a href="http://www.socialengineaddons.com/socialengine-suggestions-recommendations-plugin" target="_blank">', '</a>'); ?></a>
      </div>
    </li>	

    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("I want to enhance the Stores on my site and provide more features to my users. How can I do it?"); ?></a>
      <div class='faq' style='display: none;' id='faq_8'>
        <?php echo $this->translate('Ans: There are various apps / extensions available for the "Directory / Stores Plugin" which can enhance the Stores on your site, by adding valuable functionalities to them. To view the list of available extensions, please visit: %s.', '<a href="http://www.socialengineaddons.com/catalog/directory-stores-extensions" target="_blank">http://www.socialengineaddons.com/catalog/directory-stores-extensions</a>'); ?></a>
      </div>
    </li>
	  <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_11');"><?php echo $this->translate("I am uploading documents on my website. Format conversion for most of them is being completed successfully. However, format for some documents are not getting converted. Why would this be happening?"); ?></a>
      <div class='faq' style='display: none;' id='faq_11'>
        <?php echo $this->translate('Ans: The documents which are not getting converted might be password protected or copyrighted. So, please check such documents by uploading them at scribd (https://www.scribd.com/) to see if they are getting converted there. Scribd does not allow password protected or copyrighted documents to get converted.'); ?></a>
      </div>
    </li>
    
	<li>
        <a href="javascript:void(0);" onClick="faq_show('faq_111');"><?php echo $this->translate('Some of my website users are facing "We\'re sorry!" or "Mysqli prepare error: MySQL server has gone away" error on "Document View" page. What might be the problem?'); ?></a>
        <div class='faq' style='display: none;' id='faq_111'>
            <?php echo $this->translate('It might be happening because the document users are uploading is larger in size. So we recommend you to increase the value of "max_allowed_packet" in your database configuration and check the issue. You can also ask to your service provider for increasing the value of "max_allowed_packet".'); ?></a>
        </div>
    </li>        
    
	  <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_12');"><?php echo $this->translate("My website is running under SSL (with https). Will this plugin work fine in this case?"); ?></a>
      <div class='faq' style='display: none;' id='faq_12'>
        <?php echo $this->translate('All stores of this plugin will display fine on your website running under SSL (with https). The main Document view store will give an SSL warning in the browser because of some components of the document viewer which are rendered over http and not https, but the store will display fine. The Scribd document viewer currently does not support https completely.'); ?></a>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_13');"><?php echo $this->translate("I have selected the HTML5 Reader as default viewer for the documents on my site, but some documents are still being shown in Flash Reader. Why is it so?"); ?></a>
      <div class='faq' style='display: none;' id='faq_13'>
        <?php echo $this->translate('Ans: This is happening because secure documents use access-management technology which is available in Flash only. Therefore, secure documents will always be viewed in Flash reader, even if you choose HTML5 reader as default.'); ?></a>
      </div>
    </li>
  </ul>
</div>
<?php break; ?>
<?php case 'sitestoreintegration': ?>
<div class="admin_sitestore_files_wrapper">
	<ul class="admin_sitestore_files sitestore_faq">
    <li>
			<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("I want Store Admins of certain Directory Items / Stores to be able to add Listings to their Stores. What should I do?");?></a>
			<div class='faq' style='display: none;' id='faq_1'>
				<?php echo $this->translate("To do so, you can choose to enable adding of listings in certain Packages / Member Levels. Store admins belonging to such Member Levels and of Stores associated with such Packages will be able to add listings to their Directory Items / Stores.");?>
			</div>
		</li>	
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("I had placed the ‘Store Profile Added Listings (selected content)’ widget on ‘Store Profile’ from Layout Editor, but there is no such widget placed on some Stores on my site. What might be the reason?");?></a>
			<div class='faq' style='display: none;' id='faq_2'>
				<?php echo $this->translate("Store Admins of such Stores might have changed the Layout of their Stores and might have removed the ‘Store Profile Added Listings (selected content)’ widget from the Edit Layout section of their Store Dashboard.");?>
			</div>
		</li>
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("I have enabled the settings to add listings to directory items / stores, but I am not shown any option to add listings in my Store Dashboard. Why is happening?");?></a>
			<div class='faq' style='display: none;' id='faq_3'>
				<?php echo $this->translate("You might have not enabled the settings for adding listings to directory items / stores in the Packages or Member Level Settings. To enable the settings, go to the ‘Manage Packages’ and ‘Member Level Settings’ sections in the admin panel of “Directory / Stores Plugin” and enable the desired settings.");?>
			</div>
		</li>
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate(" I want to enhance the Stores on my site and provide more features to my users. How can I do it?");?></a>
			<div class='faq' style='display: none;' id='faq_4'>
				<?php echo $this->translate("There are various apps / extensions available for the \"Directory / Stores Plugin\" which can enhance the Stores on your site, by adding valuable functionalities to them. To view the list of available extensions, please visit: <a href='http://www.socialengineaddons.com/catalog/directory-stores-extensions' target='_blank'>http://www.socialengineaddons.com/catalog/directory-stores-extensions</a>.");?>
			</div>
		</li>
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate(" The CSS of this plugin is not coming on my site. What should I do ?");?></a>
			<div class='faq' style='display: none;' id='faq_5'>
				<?php echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homestore and then check the store which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.");?>
			</div>
		</li>
		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate(" I have created various listing types on my site, but same icon is coming for all the listing types on my Store Dashboard. How can I change the icons for various listing types? "); ?></a>
      <div class='faq' style='display: none;' id='faq_6'>
        <?php echo "Ans:  To change the icons for each listing type, please follow the steps below:"; ?><br />
        <p>
          <b><?php echo $this->translate("Step 1:") ?></b>
        </p>
        <div class="code">
	        <?php echo $this->translate(nl2br("a) Make an icon for the listing type with the name: “icon_app_intg_listtype_LISTING_TYPE_ID”. To get the LISTING_TYPE_ID, go to the ‘Admin Panel’ >> ‘PLUGIN_NAME’ >> ‘Manage Listing Types’. For example: if the id of the listing type is 1, then the icon name will be: “icon_app_intg_listtype_1”. <br />b) Now, go to the path: \"/application/modules/Sitestoreintegration/externals/images\" and upload the icon.")) ?>
        </div><br />
        <p>
          <b><?php echo $this->translate("Step 2:") ?></b>
        </p>
        <div class="code">
					<?php echo $this->translate(nl2br("a) In the below lines of code, replace ICON_NAME with the name of the icon made in Step 1. <br /><br />.ICON_NAME{background-image:url(~/application/modules/Sitestoreintegration/externals/images/ICON_NAME.png);} <br /><br />Copy the lines of code after replacing the ICON_NAME.	<br />b) Now, Open the file:  \"/application/modules/Sitestoreintegration/externals/styles/main.css\" and paste the copied lines of code in the last of this file.<br />")) ?>
       </div>
      </div>
    </li>

    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("How should I enable adding of Listings, Stores and Multiple Listing Types to the Stores of my site?"); ?></a>
      <div class='faq' style='display: none;' id='faq_7'>
        <?php echo "Ans: To enable adding of Listings, Stores and Multiple Listing Types to the Stores of your site, please follow the steps below:"; ?><br /><br />
        <?php echo $this->translate("Step 1. Go to the Global Settings of this plugin.") ?>
        <br /><br />
        <?php echo $this->translate("Step 2. Choose ‘Yes’ option for the below mentioned fields:<br />") ?>
        <div class="">
					<?php echo $this->translate(nl2br("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;a) Adding Listings (This field will come only when “<a href='http://www.socialengineaddons.com/socialengine-listings-catalog-showcase-plugin' target='_blank'>Listings / Catalog Showcase Plugin</a>” is installed on your site.) <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;b) Adding Stores (This field will come only when “<a href='http://www.socialengineaddons.com/socialengine-directory-stores-plugin' target='_blank'>Directory / Stores Plugin</a>” is installed on your site.)<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;c) Adding Various Listing Types (This field will come only when “<a href='http://www.socialengineaddons.com/socialengine-multiple-listing-types-plugin-listings-blogs-products-classifieds-reviews-ratings-pinboard-wishlists' target='_blank'>Multiple Listing Types Plugin - Listings, Blogs, Products, Classifieds, Reviews & Ratings, Pinboard, Wishlists, etc All In One</a>” is installed on your site.)<br />")) ?>
        </div><br />
        <?php echo $this->translate("Step 3. Now, if you have enabled Packages for the Stores on your site, then go to the ‘Manage Packages’ section in the admin panel of “Directory / Stores Plugin” and edit the packages to select adding of Listings, Stores and Multiple Listing Types to be available to the Stores in them by using the “Modules / Apps” field.") ?>
      </div>
    </li>
	</ul>
</div>
<?php break; ?>
<?php //case 'sitestoreproduct': ?>
<!--  <div class="admin_seaocore_files_wrapper">
    <ul class="admin_seaocore_files seaocore_faq">	
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php //echo $this->translate("How should I start with creating products on my site?");?></a>
				<div class='faq' style='display: none;' id='faq_1'>
					<?php //echo $this->translate("After plugin installation, follow the steps below:");?>
					<ul>
						  <li>
							  <?php //echo $this->translate("Start by configuring the Global Settings for your plugin.");?>
							</li>
								<li>
									<?php //echo $this->translate("Now configure Member Level Settings.");?>
								</li>
								<li>
									<?php //echo $this->translate("Then create the categories, sub-categories and 3rd level categories and choose the product comparison level.");?>
								</li>
								<li>
									<?php //echo $this->translate("Then go to the Profile Fields section to create custom fields if required for products on your site and configure mapping between product categories and profile types such that custom profile fields can be based on categories, sub-categories and 3rd level categories.");?>
								</li>
								<li>
									<?php //echo $this->translate("Configure the reviews and ratings settings for your site from the Reviews & Ratings section.");?>
									<br />
									<?php //echo $this->translate('1) Go to the Review Settings sub-section and configure the settings here.');?>
									<br />
									<?php //echo $this->translate('2) Then go to the Review Profile Fields sub-section to create custom review profile fields if required and configure the mapping between product categories and review profile types such that custom profile fields can be based on categories, sub-categories and 3rd level categories..');?>
									<br />
									<?php //echo $this->translate('3) Now, go to the Rating Parameters sub-section and create rating parameters for different categories.');?>
									<br />
								</li>
                <li>
									<?php //echo $this->translate('Go to the Comparison Settings section to configure various comparison fields for products.');?>
                </li>
                 <li>
									<?php //echo $this->translate('Choose Editors and Super Editor from the Manage Editors section and add Editor badges to assign them to the Editors.');?>
                </li>
                <li>
									<?php //echo $this->translate('Then, go to the Video Settings section to configure various settings for videos.');?>
                </li>

                <li>
									<?php //echo $this->translate('Customize various widgetized pages from the Layout Editor section.');?>
                </li>
              <br />
              <?php //echo $this->translate("You can now start creating the products on your site.");?>
						</li>
					</ul>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php //echo $this->translate("The widths of the page columns are not coming fine on the Products Home page. What might be the reason?");?></a>
				<div class='faq' style='display: none;' id='faq_5'>
					<?php //echo $this->translate('This is happening because none or very few products have been created and viewed on your site, and thus the widgets on the Products Home page are currently empty. Once products are rated and liked on your site, and more activity happens, these widgets will get populated and the Products Home page will look good.<br /> If still the width of the pages are not coming fine, then edit the width of the page column by using the "Column Width" widget available in the SociaEngineAddOns-Core block in the Available Blocks section of the Layout Editor.');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php //echo $this->translate("How can I change the labels of Featured and Sponsored markers for the Products?");?></a>
				<div class='faq' style='display: none;' id='faq_7'>
					<?php //echo $this->translate('You can change the labels of Featured and New markers for the Products by replacing the "featured-label.png", "new-label.png" images respectively at the path "application/modules/Sitestoreproduct/externals/images/".<br /> To change the label for Sponsored marker, you can select a different color from "Global settings" section in the admin panel of this plugin.');?>
				</div>
			</li>

			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_34');"><?php //echo $this->translate('How can I mark products as Hot instead of New on my site?');?></a>
				<div class='faq' style='display: none;' id='faq_34'>
					<?php //echo $this->translate('To mark products as Hot instead of New, you can change the label of New marker for the Products by replacing the "new-label.png" image at the path "application/modules/Sitestoreproduct/externals/images/".');?>
				</div>
			</li>

			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_60');"><?php //echo $this->translate('There is a setting to choose between "Official SocialEngine Videos Plugin" and "Review - Inbuilt Videos". What is the difference between these two?');?></a>
				<div class='faq' style='display: none;' id='faq_60'>
					<?php //echo $this->translate('If you enable "Official SocialEngine Videos Plugin", then video settings will be inherited from the "Videos" plugin and videos uploaded in products will be displayed on "Video Browse Page" and "Products Profile" pages.<br /> If you enable "Stores / Marketplace - Inbuilt Videos", then Videos uploaded in the products will only be displayed on Product Profile pages and will have their own widgetized Stores / Marketplace - Video View page.');?>
				</div>
			</li>

			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_28');"><?php //echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?");?></a>
				<div class='faq' style='display: none;' id='faq_28'>
					<?php //echo $this->translate("Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.");?>
				</div>
			</li>
      
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_45');"><?php //echo $this->translate("In how many ways can I enable reviews on my site?");?></a>
        <div class='faq' style='display: none;' id='faq_45'>
          <?php //echo $this->translate("You can either disable reviews for products or choose to enable 'editor reviews', 'user reviews' or 'both editor and user reviews' by using the 'Allow Reviews' from 'Global Settings' section of this plugin.");?>
        </div>
      </li>      
      
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_290');"><?php //echo $this->translate("I want emails sent out from this plugin to be attractive. How can I do this?");?></a>
				<div class='faq' style='display: none;' id='faq_290'>
					<?php //echo $this->translate("You can send attractive emails to your users via rich, branded, professional and impact-ful emails by using our '<a href='http://www.socialengineaddons.com/socialengine-email-templates-plugin' target='_blank'>Email Templates Plugin</a>'. To see details, please <a href='http://www.socialengineaddons.com/socialengine-email-templates-plugin' target='_blank'>visit here.</a>");?>
				</div>
			</li>
		</ul>
	</div>-->
	<?php //break; ?>

	<?php case 'editors': ?>
  <div class="admin_seaocore_files_wrapper">
    <ul class="admin_seaocore_files seaocore_faq">	
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_13');"><?php echo $this->translate("What are Editor Reviews and how they can be useful for my site?");?></a>
					<div class='faq' style='display: none;' id='faq_13'>
						<?php echo $this->translate('Editor reviews are helpful in displaying accurate, trusted and unbiased reviews that will showcase products’ (for example: products of hotels, products, etc.) quality, features, and value. This will bring more user engagement to your site, as editor reviews provide reviews from expert people (editors) on the products of their interest.<br /> You can choose Editors from "Manage Editors" section of this plugin.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_14');"><?php echo $this->translate("I can choose various Editors for my site, but only one Super Editor. Why is this so? What is the difference between an Editor and a Super Editor?");?></a>
					<div class='faq' style='display: none;' id='faq_14'>
						<?php echo $this->translate('You can choose any number of Editors for your site who all will be allowed to write editor reviews for various products. Also, if any Editor deletes the respective member profile, then all the editor reviews written by that Editor will be automatically assigned to the Super Editor.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_15');"><?php echo $this->translate("I am removing a member as Editor from my site. What will happen to the editor reviews written by him?");?></a>
					<div class='faq' style='display: none;' id='faq_15'>
						<?php echo $this->translate('If you remove a member as Editor from your site, then you would be able to assign all Editor reviews written by that editor to any other editor on your site. <br/>You can remove an editor by using "Remove" option from Reviews & Ratings >> Products >> Editors section of this plugin.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_16');"><?php echo $this->translate("How can I select a Featured Editor on my site?");?></a>
					<div class='faq' style='display: none;' id='faq_16'>
						<?php echo $this->translate('You can select a Featured Editor on your site by using the "Use the auto-suggest field to select Featured Editor." field available in the edit settings of "Featured Editor" widget from the Layout editor by placing the widget on any widgetized page.<br /> You can place this widget multiple times on different pages with different featured editor chosen for each placement.');?>
					</div>
				</li>
			</ul>
		</div>
	<?php break; ?>  
<?php case 'sitestoreadmincontact': ?>
<div class="admin_sitestore_files_wrapper">
	<ul class="admin_sitestore_files sitestore_faq">
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("Why emails are queued for sending ?");?></a>
			<div class='faq' style='display: none;' id='faq_1'>
				<?php echo $this->translate("Ans: Mail queueing permits the emails to be sent out over time, preventing your mail server from being overloaded by outgoing emails. We utilize mail queueing for large email blasts to help prevent negative performance impacts on your site.")?>
			</div>
		</li>	
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("I am not receiving messages sent from 'Message Store Admins' section of this plugin even when I have created some stores. What is the reason ?");?></a>
			<div class='faq' style='display: none;' id='faq_2'>
				<?php echo $this->translate("Ans: You are not receiving messages sent from 'Message Store Admins' section of this plugin because messages can be sent to other users only, you can not send messages to yourself.");?>
			</div>
		</li>
<!--    <li>
			<a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php //echo $this->translate("I want the color settings of the email template sent using this plugin to be different from the email template sent from 'Insights'. What should I do ?");?></a>
			<div class='faq' style='display: none;' id='faq_8'>
				<?php //echo $this->translate("Ans: You can change the color settings of email template from 'Insights Email Settings' section of Stores / Marketplace Plugin for sending emails using this plugin. After sending email you can again change the color settings.");?>
			</div>
		</li>-->
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("From where should I edit the header and footer of the email body ?");?></a>
			<div class='faq' style='display: none;' id='faq_3'>
				<?php echo $this->translate("Ans: To edit the header and footer of the email body, go to Admin panel -> Main Navigation Menu bar -> Settings -> Mail Templates -> Choose message(Header ( Members ) and Footer ( Members )) -> Message Body.");?>
			</div>
		</li>		
<!--		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php //echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?");?></a>
			<div class='faq' style='display: none;' id='faq_4'>
				<?php //echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homestore and then check the store which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.");?>
			</div>
		</li>	-->
	</ul>
</div>  
<?php break; ?>
<?php case 'sitestoreinviter': ?>
<div class="admin_sitestore_files_wrapper">
  <ul class="admin_sitestore_files sitestore_faq">
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("I am getting the error : 'failed to open stream: HTTP request failed!' while trying to upload csv/text file content; what should I do ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_1'>
        <?php $link_phpinfo = "<a href='" . $this->baseUrl() . "/admin/system/php' 	style='color:#5BA1CD;'>http://" . $_SERVER['HTTP_HOST'] . $this->baseUrl() . "/admin/system/php</a>";
        echo $this->translate("Ans: You should ask your server administrator to check your server's php.ini PHP configuration file for 'allow_url_fopen' to be 'on' and 'user_agent' to have some values listed in it. It should be listed here: %s. This should resolve the issue.", $link_phpinfo); ?>
      </div>
    </li>	

    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("Windows Live contact importer is not working on my site, what should I do ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_2'>
        <?php $link_phpinfo = "<a href='" . $this->baseUrl() . "/admin/system/php' 	style='color:#5BA1CD;'>http://" . $_SERVER['HTTP_HOST'] . $this->baseUrl() . "/admin/system/php</a>";
        echo $this->translate("Ans: If, Windows Live contact importer is not working on your site then there may be chance that PHP Mhash package is not installed on your server. Please ask your server administrator to install this. After installing mhash, it should also be listed here: %s. This should resolve the issue.", $link_phpinfo); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate('Is "Invite & Promote" feature dependent on packages / member levels?'); ?></a>
      <div class='faq' style='display: none;' id='faq_3'>
        <?php echo $this->translate("Ans: Yes, if packages are enabled on site for stores, you (admin) can choose if this app  should be available to stores of a package. If packages are disabled, then access to this feature can be based on Member Level via the Member Level Settings."); ?></a>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate('Who can use the "Invite and Promote" feature to invite their contacts to a Store?'); ?></a>
      <div class='faq' style='display: none;' id='faq_4'>
        <?php echo $this->translate('Ans: Store Admins have access to the "Invite and Promote" feature from the "Marketing" and "Apps" sections of their Store Dashboard.'); ?></a>
      </div>
    </li>
		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("Would the invitees who are already members of this site also get store invitations from their friends if sent to them?"); ?></a>
      <div class='faq' style='display: none;' id='faq_5'>
        <?php echo $this->translate('Ans: Yes, the invitees who are already members of this site would get a suggestion for the Store. This feature is dependent on the %1$sSuggestions / Recommendations / People you may know & Inviter%2$s plugin and require that to be installed. In case, this plugin is not installed, a suggestion notification will be sent.<br />Additionally, in both cases an email will also be sent to the invitees who are not the members of the site. Users will also be able to preview these suggestions and email templates before sending the invitations to their friends.', '<a href="http://www.socialengineaddons.com/socialengine-suggestions-recommendations-plugin" target="_blank">', '</a>'); ?></a>
      </div>
    </li>
<!--		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php //echo $this->translate("I had not selected to 'Enable Inviter Module for Default Package' in 'Global Settings' section before activating this Plugin. But after activation, I am not able to view this option. Does this mean that this module is permanently disabled for Default Package?"); ?></a>
      <div class='faq' style='display: none;' id='faq_6'>
        <?php //echo $this->translate("Ans: No, it does not mean so. You can still enable this module for Default Package from the 'Manage Packages' section of 'Stores / Marketplace Plugin' by editing the Default Package and selecting the 'Invite & Promote' checkbox in the 'Modules / Apps' field."); ?>
      </div>
    </li>-->
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("I want to use this plugin for directory of car invitations. How can I change the word: 'store invitations' to 'car invitations' in this plugin?"); ?></a>
      <div class='faq' style='display: none;' id='faq_7'>
        <?php echo $this->translate("Ans: You can change the word 'store invitations' to your directory type from the 'Layout' > 'Language Manager' section in the Admin Panel."); ?><br />
        <?php echo $this->translate("If you also want to change the URLs, then follow the below steps:") ?><br /><br />
        <?php echo $this->translate('1) Open this file: "application/modules/Sitestoreinvite/settings/manifest.php".') ?><br /><br />
        <?php echo $this->translate("2) Change the word 'storeinvites' to your directory type in the route part of all the routes defined in this file. For example, if you want to change 'storeinvites' to 'carinvites', the change should be like:") ?><br /><br />
        <div class="code">
          <?php echo "'route' => 'storeinvites/invitefriends/:user_id/:sitestore_id/'" ?>
        </div><br />
        <?php echo $this->translate("The above code should be replaced with the below code.") ?><br /><br />
        <div class="code">
          <?php echo "'route' => 'carinvites/invitefriends/:user_id/:sitestore_id/'" ?>
        </div><br />
        <?php echo $this->translate("Please note that in the above example, the change is 'storeinvites' => 'carinvites'. But you can also change it to anything else according to your specifications. This type of change should be made in all the routes of this file.") ?>
      </div>
    </li>
<!--    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php //echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_8'>
        <?php //echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homestore and then check the store which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'."); ?></a>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php ///echo $this->translate("I want to enhance the Stores on my site and provide more features to my users. How can I do it?"); ?></a>
      <div class='faq' style='display: none;' id='faq_9'>
        <?php //echo $this->translate('Ans: There are various apps / extensions available for the "Stores / Marketplace Plugin" which can enhance the Stores on your site, by adding valuable functionalities to them. To view the list of available extensions, please visit: %s.', '<a href="http://www.socialengineaddons.com/catalog/directory-stores-extensions" target="_blank">http://www.socialengineaddons.com/catalog/directory-stores-extensions</a>'); ?></a>
      </div>
    </li>-->
  </ul>
</div>  
<?php break; ?>  
<?php case 'sitestoreoffer': ?>
<div class="admin_sitestore_files_wrapper">
  <ul class="admin_sitestore_files sitestore_faq" >
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("I want Coupons to be available to only certain stores on my site. How can this be done?"); ?></a>
      <div class='faq' style='display: none;' id='faq_1'>
        <?php echo $this->translate("Ans: You can enable packages for stores on your site, and make Coupons available to only certain packages. If you have not enabled packages, then from Member Level Settings, you can make Coupons to be available for stores of only certain member levels."); ?></a>
      </div>
    </li>	
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("What is the difference between Hot Coupons and Featured Coupons?"); ?></a>
      <div class='faq' style='display: none;' id='faq_2'>
        <?php echo $this->translate('Ans: Featured Coupons are confined to a stores. A store admin can mark one coupon from their coupons as featured. A featured coupon is highlighted and shown on top in the Coupons section of a stores. Hot Coupons correspond to all stores on a site. Coupons are marked by you(admin) as "Hot" from the "Coupons" >> "Manage Store Coupons" section. There is also a "Hot Store Coupons" widget showcasing the coupons marked as Hot.'); ?></a>
      </div>
    </li>
		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("I am not able to add Coupons to the store from Store profile. What should I do?"); ?></a>
      <div class='faq' style='display: none;' id='faq_7'>
        <?php echo $this->translate("Ans: There should be at least one coupon for a Store to show Coupons tab on its profile. If there are no Coupons currently for a Store then 'Coupons' tab is not shown at Store Profile. In that case, you can go to the Store Dashboard and add coupons from the 'Get Started' and 'Apps' sections there."); ?></a>
      </div>
    </li>

<!--		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php //echo $this->translate("I had not selected to 'Enable Coupons Module for Default Package' in 'Global Settings' section before activating this Plugin. But after activation, I am not able to view this option. Does this mean that this module is permanently disabled for Default Package?"); ?></a>
      <div class='faq' style='display: none;' id='faq_6'>
        <?php //echo $this->translate("Ans: No, it does not mean so. You can still enable this module for Default Package from the 'Manage Packages' section of 'stores Plugin' by editing the Default Package and selecting the 'Coupons' checkbox in the 'Modules / Apps' field."); ?>
      </div>
    </li>-->
<!--    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php //echo $this->translate("I want to use this plugin for directory of car coupons. How can I change the word: 'store coupons' to 'car coupons' in this plugin?"); ?></a>
      <div class='faq' style='display: none;' id='faq_3'>
        <?php //echo $this->translate("Ans: You can easily use this plugin for creation of any type of directories. You can change the word 'store coupons' to your directory type from the 'Layout' > 'Language Manager' section in the Admin Panel."); ?>
      </div>
    </li>-->
		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("I want to change the text 'storecoupon' to 'carcoupon' in the URLs of this plugin. How can I do so ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_8'>
        <?php echo $this->translate('Ans: To do so, please go to the Coupons section of this plugin. Now, search for the field : Store Coupons URL alternate text for "storecoupon"<br />Then, enter the text you want to display in place of \'storecoupon\' in the text box there.'); ?>
      </div>
    </li>
<!--    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php //echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_4'>
        <?php //echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homestore and then check the store which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'."); ?></a>
      </div>
    </li>	
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php //echo $this->translate("I want to enhance the Stores on my site and provide more features to my users. How can I do it?"); ?></a>
      <div class='faq' style='display: none;' id='faq_5'>
        <?php //echo $this->translate('Ans: There are various apps / extensions available for the "Stores / Marketplace Plugin" which can enhance the Stores on your site, by adding valuable functionalities to them. To view the list of available extensions, please visit: %s.', '<a href="http://www.socialengineaddons.com/catalog/directory-stores-extensions" target="_blank">http://www.socialengineaddons.com/catalog/directory-stores-extensions</a>'); ?></a>
      </div>
    </li>-->
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo $this->translate("What is the popularity criteria for the “Most Popular Coupons” filter in the Store Coupons search form?"); ?></a>
      <div class='faq' style='display: none;' id='faq_9'>
        <?php echo $this->translate("Ans: The number of times that an coupon has been claimed is used to determine the coupon’s popularity."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo $this->translate("I want to send Coupons with their details to users via attractive emails. How can I do this?"); ?></a>
      <div class='faq' style='display: none;' id='faq_10'>
        <?php echo $this->translate('Ans: You can send Coupons to your users via rich, branded, professional and impact-ful emails by using our %1s. To see details, please %2s','<a href="http://www.socialengineaddons.com/socialengine-email-templates-plugin" target="_blank">"Email Templates Plugin"</a>','<a href="http://www.socialengineaddons.com/socialengine-email-templates-plugin" target="_blank">visit here</a>'.'.'); ?>
      </div>
    </li>
  </ul>
</div>
<?php break; ?>
<?php case 'sitestoreform': ?>
<div class="admin_sitestore_files_wrapper">
  <ul class="admin_sitestore_files sitestore_faq">
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("I want Forms to be available to only certain stores on my site. How can this be done?"); ?></a>
      <div class='faq' style='display: none;' id='faq_1'>
        <?php echo $this->translate("Ans: You can enable packages for stores on your site, and make Forms available to only certain packages. If you have not enabled packages, then from Member Level Settings, you can make Forms to be available for stores of only certain member levels."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("Who creates forms for stores?"); ?></a>
      <div class='faq' style='display: none;' id='faq_2'>
        <?php echo $this->translate("Ans: The forms for stores are created by their admins from the Store Dashboard. There are many settings available there, and store admins can create questions of their choice in the forms. They can also choose for the Form tab to not be shown on the store profile till they have configured it, by deactivating the form."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("Can I disable form for a stores if I find it offensive?"); ?></a>
      <div class='faq' style='display: none;' id='faq_3'>
        <?php echo $this->translate("Ans: Yes, you can do so from the Form >> Manage Forms section."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("Who receives the responses for the forms filled by visitors to a store, and how?"); ?></a>
      <div class='faq' style='display: none;' id='faq_4'>
        <?php echo $this->translate("Ans: The responses for the forms filled by visitors to a store are emailed to the Admins of that store."); ?>
      </div>
    </li>
<!--		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php //echo $this->translate("I had not selected to 'Enable Form Module for Default Package' in 'Global Settings' section before activating this Plugin. But after activation, I am not able to view this option. Does this mean that this module is permanently disabled for Default Package?"); ?></a>
      <div class='faq' style='display: none;' id='faq_8'>
        <?php //echo $this->translate("Ans: No, it does not mean so. You can still enable this module for Default Package from the 'Manage Packages' section of 'Stores / Marketplace Plugin' by editing the Default Package and selecting the 'Form' checkbox in the 'Modules / Apps' field."); ?>
      </div>
    </li>-->
<!--    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php //echo $this->translate("I want to use this plugin for directory of car form. How can I change the word: 'store form' to 'car form' in this plugin?"); ?></a>
      <div class='faq' style='display: none;' id='faq_5'>
        <?php //echo $this->translate("Ans: You can easily use this plugin for creation of any type of directories. You can change the word 'store form' to your directory type from the 'Layout' > 'Language Manager' section in the Admin Panel."); ?>
      </div>
    </li>-->
		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo $this->translate("I want to change the text 'storeform' to 'carform' in the URLs of this plugin. How can I do so ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_9'>
        <?php echo $this->translate('Ans: To do so, please go to the Form section of this plugin. Now, search for the field : Store Form URL alternate text for "storeform"<br />Then, enter the text you want to display in place of \'storeform\' in the text box there.'); ?>
      </div>
    </li>
<!--    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php //echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_6'>
        <?php //echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homestore and then check the store which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'."); ?>
      </div>
    </li>-->
<!--    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php //echo $this->translate("I want to enhance the Stores on my site and provide more features to my users. How can I do it?"); ?></a>
      <div class='faq' style='display: none;' id='faq_7'>
        <?php //echo $this->translate('Ans: There are various apps / extensions available for the "Stores / Marketplace Plugin" which can enhance the Stores on your site, by adding valuable functionalities to them. To view the list of available extensions, please visit: %s.', '<a href="http://www.socialengineaddons.com/catalog/directory-stores-extensions" target="_blank">http://www.socialengineaddons.com/catalog/directory-stores-extensions</a>'); ?>
      </div>
    </li>	-->
  </ul>
</div>  
<?php break; ?>  
<?php case 'sitestorereview': ?>
  <div class="admin_sitestore_files_wrapper">
    <ul class="admin_sitestore_files sitestore_faq">
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("What are rating parameters?"); ?></a>
        <div class='faq' style='display: none;' id='faq_1'>
          <?php echo $this->translate('Ans: Stores and Products of different categories have different parameters for judging. Hence, rating parameters enable gathering of refined ratings, reviews and feedback for the Stores and Products in your community. You (admin) can configure rating parameters for different store categories on your site from the "Rating Parameters" section available in the "Reviews & Ratings" section for Stores and Products. Users will be able to give an overall rating to stores and products as well as rate on the parameters defined by you.'); ?>
        </div>
      </li>
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("Can I (admin) delete offensive reviews?"); ?></a>
        <div class='faq' style='display: none;' id='faq_2'>
          <?php echo $this->translate('Ans: Yes, you can do so from the Manage Ratings & Reviews section.'); ?>
        </div>
      </li>
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("Can a Store Admin write a review for his Store?"); ?></a>
        <div class='faq' style='display: none;' id='faq_3'>
          <?php echo $this->translate('Ans: No.'); ?>
        </div>
      </li>
<!--      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php //echo $this->translate("How can users mark a review that they find useful?"); ?></a>
        <div class='faq' style='display: none;' id='faq_4'>
          <?php //echo $this->translate('Ans: Users can "Like" reviews that they find useful and can also comment on them.'); ?>
        </div>
      </li>-->
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo $this->translate("I am not able to post reviews on the store while I can view them. Why is it so ?"); ?></a>
        <div class='faq' style='display: none;' id='faq_9'>
          <?php echo $this->translate('Ans: In that case you must be the Store Owner or one of the Store Admins for that Store. Store Owners/Admins are not supposed to rate or post reviews on their own Store. They can just view the reviews posted on their Stores by others.'); ?>
        </div>
      </li>
      <li>
				<a href="javascript:void(0);" onClick="faq_show('faq_22');"><?php echo $this->translate("In how many ways I can enable reviews for products on my site?");?></a>
				<div class='faq' style='display: none;' id='faq_22'>
					<?php echo $this->translate("Ans: You can either disable reviews for products or choose to enable 'editor reviews', 'user reviews' or 'both editor and user reviews' by using the 'Allow Reviews' from 'Global Settings' >> 'Products' section of this plugin.");?>
				</div>
			</li>
<!--      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php //echo $this->translate("I want to use this plugin for directory of car reviews. How can I change the word: 'store reviews' to 'car reviews' in this plugin?"); ?></a>
        <div class='faq' style='display: none;' id='faq_5'>
          <?php //echo $this->translate("Ans: You can easily use this plugin for creation of any type of directories. You can change the word 'store reviews' to your directory type from the 'Layout' > 'Language Manager' section in the Admin Panel."); ?>
        </div>
      </li>-->
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo $this->translate("I want to change the text 'storereviews' to 'carreviews' in the URLs of this plugin. How can I do so?"); ?></a>
        <div class='faq' style='display: none;' id='faq_10'>
          <?php echo $this->translate('Ans: To do so, please go to the "Reviews & Ratings" >> "Stores" section of this plugin. Now, search for the field : Store Reviews URL alternate text for "storereviews"<br />Then, enter the text you want to display in place of \'storereviews\' in the text box there.'); ?>
        </div>
      </li>
<!--      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php //echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?"); ?></a>
        <div class='faq' style='display: none;' id='faq_6'>
          <?php //echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homestore and then check the store which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'."); ?></a>
        </div>
      </li>	-->
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("I am not able to find the Suggest to Friends feature for Store Reviews. What can be the reason?"); ?></a>
        <div class='faq' style='display: none;' id='faq_7'>
          <?php echo $this->translate('Ans: The suggestions features are dependent on the %1$sSuggestions / Recommendations / People you may know & Inviter%2$s plugin and require that to be installed.', '<a href="http://www.socialengineaddons.com/socialengine-suggestions-recommendations-plugin" target="_blank">', '</a>'); ?></a>
        </div>
      </li>	
<!--      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php //echo $this->translate("I want to enhance the Stores on my site and provide more features to my users. How can I do it?"); ?></a>
        <div class='faq' style='display: none;' id='faq_8'>
          <?php //echo $this->translate('Ans: There are various apps / extensions available for the "Stores / Marketplace Plugin" which can enhance the Stores on your site, by adding valuable functionalities to them. To view the list of available extensions, please visit: %s.', '<a href="http://www.socialengineaddons.com/catalog/directory-stores-extensions" target="_blank">http://www.socialengineaddons.com/catalog/directory-stores-extensions</a>'); ?></a>
        </div>
      </li>	-->
    </ul>
  </div>  
<?php break; ?>    
<?php case 'sitestoreurl': ?>
  <div class="admin_sitestore_files_wrapper">
    <ul class="admin_sitestore_files sitestore_faq">
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("A Store on my website has been assigned a short URL. However the short URL is not coming for that Store. What could be the reason?");?></a>
        <div class='faq' style='display: none;' id='faq_1'>
          <?php echo $this->translate("Ans: The number of Likes on that Store would not have exceeded the Likes Limit for Active Short URL configured by you in Global Settings. Short URLs for Stores are activated when their Likes exceed the value set by you for Likes limit.")?>
        </div>
      </li>	
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("There is a Store on my website which has the number of Likes greater than the Likes Limit for Active Short URL configured in Global Settings. However, short URL is still not activated for this Store. What could be the reason?");?></a>
        <div class='faq' style='display: none;' id='faq_2'>
          <?php echo $this->translate("Ans: The short URL set for this Store might have been added to the Banned URLs, and would have thus been blocked. In this case, this Store would be appearing in the \"Short Store URL\" >> \"Stores with Banned URLs\" section. Edit the short URL for this Store from that section.");?>
        </div>
      </li>
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("There are already Stores created on my website. What will happen to their URLs?");?></a>
        <div class='faq' style='display: none;' id='faq_3'>
          <?php echo $this->translate('Ans: This plugin will automatically pick up the URLs of those Stores for their Short URLs. Check the "Short Store URL" >> "Stores with Banned URLs" section of the to see the Stores which will have conflicting URLs and edit their URLs.');?>
        </div>
      </li>		
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("What type of short URLs should I ban for Stores from the Banned URLs section?");?></a>
        <div class='faq' style='display: none;' id='faq_4'>
          <?php echo $this->translate("Ans: You should ban all standard URLs, i.e., URLs from other plugins, etc. If a non-banned URL from a plugin gets assigned to a Store, then that corresponding plugin’s webstore will not be accessible, rather, the Store will open at that URL. The list in the Banned URLs section comes pre-configured with some banned URLs. You can also ban URLs containing offensive terms.");?>
        </div>
      </li>	
<!--      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php //echo $this->translate("Q: Is there a way to simply let the Stores / Marketplace Short Store URL Extension work all the time and not be limited to a minimum of 5 likes?");?></a>
        <div class='faq' style='display: none;' id='faq_5'>
          <?php //echo $this->translate('Ans: The reason of making this a requirement to have minimum 5 likes for Stores / Marketplace Short Store URL Extension to work:<br />
//          Suppose your site has URL shorten working for 0 likes also, then in that case if there exists a store created by a user with URL:<br />
//          http://yoursitename.com/files<br />
//          and one of the plugins installed on your site is already using this URL.<br />
//
//          Then in this situation when there is an ajax based call for the URL of the plugin then also store URL will be opened as it will not be listed in the "Banned URLs".
//          So to avoid this situation we fixed the minimum likes requirement to 5 for URL to be shortened.
//          By doing this URL will be listed in the "Banned URLs" and will not be shortened until and unless you do not change it by clicking on "edit" in the "Admin Panel" of this plugin from the tab "Stores with Banned URLs".');?>
        </div>
      </li>	-->
<!--      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php //echo $this->translate("Q. The CSS of this plugin is not coming on my site. What should I do ?");?></a>
        <div class='faq' style='display: none;' id='faq_6'>
          <?php //echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homestore and then check the store which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.");?>
        </div>
      </li>	-->
    </ul>
  </div>  
<?php break; ?>
<?php case 'sitestorevideo': ?>
  <div class="admin_sitestore_files_wrapper">
	<ul class="admin_sitestore_files sitestore_faq">
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("I want Videos to be available to only certain stores on my site. How can this be done?");?></a>
			<div class='faq' style='display: none;' id='faq_1'>
				<?php echo $this->translate("Ans: You can enable packages for stores on your site, and make Videos available to only certain packages. If you have not enabled packages, then from Member Level Settings, you can make Videos to be available for stores of only certain member levels.");?></a>
			</div>
		</li>
    <li>
				<a href="javascript:void(0);" onClick="faq_show('faq_20');"><?php echo $this->translate("There is a setting to choose between “Official SocialEngine Videos Plugin” and “Inbuilt Videos” for products. What is the difference between these two?");?></a>
				<div class='faq' style='display: none;' id='faq_20'>
					<?php echo $this->translate('Ans: If you enable "Official SocialEngine Videos Plugin", then video settings will be inherited from the "Videos" plugin and videos uploaded in listings will be displayed on "Video Browse Page" and "Listings Profile" pages. <br /> If you enable "Inbuilt Videos", then Videos uploaded in the listings will only be displayed on Listing Profile pages and will have their own widgetized Stores - Video View page.');?>
				</div>
			</li>
<!--		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php //echo $this->translate("I had not selected to 'Enable Videos Module for Default Package' in 'Global Settings' section before activating this Plugin. But after activation, I am not able to view this option. Does this mean that this module is permanently disabled for Default Package?"); ?></a>
      <div class='faq' style='display: none;' id='faq_6'>
        <?php //echo $this->translate("Ans: No, it does not mean so. You can still enable this module for Default Package from the 'Manage Packages' section of 'Stores / Marketplace Plugin' by editing the Default Package and selecting the 'Videos' checkbox in the 'Modules / Apps' field."); ?>
      </div>
    </li>-->
<!--		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php //echo $this->translate("I want to use this plugin for directory of car videos. How can I change the word: 'store videos' to 'car videos' in this plugin?");?></a>
			<div class='faq' style='display: none;' id='faq_2'>
				<?php //echo $this->translate("Ans: You can easily use this plugin for creation of any type of directories. You can change the word 'store videos' to your directory type from the 'Layout' > 'Language Manager' section in the Admin Panel.");?>
			</div>
		</li>-->
		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo $this->translate("I want to change the text 'storevideos' to 'carvideos' in the URLs of this plugin. How can I do so ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_10'>
        <?php echo $this->translate('Ans: To do so, please go to the Videos section of this plugin. Now, search for the field : Store Videos URL alternate text for "storevideos"
Then, enter the text you want to display in place of \'store-videos\' in the text box there.'); ?>
      </div>
    </li>
<!--		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php //echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?");?></a>
			<div class='faq' style='display: none;' id='faq_3'>
				<?php //echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homestore and then check the store which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.");?></a>
			</div>
		</li>	-->
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("I am not able to find the Suggest to Friends feature for Store Videos. What can be the reason?");?></a>
			<div class='faq' style='display: none;' id='faq_4'>
				<?php echo $this->translate('Ans: The suggestions features are dependent on the %1$sSuggestions / Recommendations / People you may know & Inviter%2$s plugin and require that to be installed.', '<a href="http://www.socialengineaddons.com/socialengine-suggestions-recommendations-plugin" target="_blank">', '</a>');?></a>
			</div>
		</li>	
<!--		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php //echo $this->translate("I want to enhance the Stores on my site and provide more features to my users. How can I do it?");?></a>
			<div class='faq' style='display: none;' id='faq_5'>
				<?php //echo $this->translate('Ans: There are various apps / extensions available for the "Stores / Marketplace Plugin" which can enhance the Stores on your site, by adding valuable functionalities to them. To view the list of available extensions, please visit: %s.', '<a href="http://www.socialengineaddons.com/catalog/directory-stores-extensions" target="_blank">http://www.socialengineaddons.com/catalog/directory-stores-extensions</a>');?></a>
			</div>
		</li>-->
    <li>
			<a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate('How can I enable the "Upload from My Computer" feature in this plugin?');?></a>
			<div class='faq' style='display: none;' id='faq_7'>
				<?php echo $this->translate('Ans: Yes, you can enable the "Upload from My Computer" feature in this plugin and for that you will have to get FFMPEG installed at your site. You can contact your server hosting company for this and they will install it at your site.');?></a>
			</div>
		</li>
	</ul>
</div>
<?php break; ?>   
<?php case 'sitestorealbum': ?>
<div class="admin_sitestore_files_wrapper">
  <ul class="admin_sitestore_files sitestore_faq">
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("I want Albums to be available to only certain stores on my site. How can this be done?"); ?></a>
      <div class='faq' style='display: none;' id='faq_1'>
        <?php echo $this->translate("Ans: You can enable packages for stores on your site, and make Photos available to only certain packages. If you have not enabled packages, then from Member Level Settings, you can make Photos to be available for stores of only certain member levels."); ?>
      </div>
    </li>	
		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("Is it possible for a member (other than Store owner or Store admin) to upload photo to a Store album without being the owner of the Store?"); ?></a>
      <div class='faq' style='display: none;' id='faq_2'>
        <?php echo $this->translate('Ans: Yes, it is possible but such a member can upload photos only to the Default Store Album which was created during the Store creation. Also while creating a Store, at "Photo Creation Privacy" field, Store owner can decide that who can upload photos to the default store album. Store owner/admin can also edit this privacy setting by editing the Store details after its creation.<br />Default privacy is set to "All Registered Members" which means that all the registered members of the site can upload photos to the Store\'s default album.'); ?>
      </div>
    </li>	
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("What kinds of displays are available for Store Albums and Photos?"); ?></a>
      <div class='faq' style='display: none;' id='faq_3'>
        <?php echo $this->translate("Ans: There are 2 types of Album displays available. Both of them are AJAX based displays. The first is a simple AJAX based display, and the second one is an advanced lightbox display. The Advanced Lightbox Display for store album photos can be enabled from Photo Albums section of this plugin."); ?>
      </div>
    </li>	
    <li style='display: none;'>
      <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate('I have enabled the "Advanced Lightbox Display" from Global Settings. But the store album photos in the activity feed on the Store Profile are still not being displayed in Advanced Lightbox. What can be the solution ?'); ?></a>
      <div class='faq' style='display: none;' id='faq_4'>
      	<?php //echo $description;?>
        </div>
    </li>	
<!--		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php //echo $this->translate("I had not selected to 'Enable Albums Module for Default Package' in 'Global Settings' section before activating this Plugin. But after activation, I am not able to view this option. Does this mean that this module is permanently disabled for Default Package?"); ?></a>
      <div class='faq' style='display: none;' id='faq_5'>
        <?php //echo $this->translate("Ans: No, it does not mean so. You can still enable this module for Default Package from the 'Manage Packages' section of 'Stores / Marketplace Plugin' by editing the Default Package and selecting the 'Photos' checkbox in the 'Modules / Apps' field."); ?>
      </div>
    </li>	-->
<!--    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php //echo $this->translate("I want to use this plugin for directory of car albums. How can I change the word: 'store albums' to 'car albums' in this plugin?"); ?></a>
      <div class='faq' style='display: none;' id='faq_6'>
        <?php //echo $this->translate("Ans: You can easily use this plugin for creation of any type of directories. You can change the word 'store albums' to your directory type from the 'Layout' > 'Language Manager' section in the Admin Panel."); ?>
      </div>
    </li>-->
<!--    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php //echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_7'>
        <?php //echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homestore and then check the store which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'."); ?>
      </div>
    </li>	-->
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("I am not able to find the Suggest to Friends feature for Store Albums. What can be the reason?"); ?></a>
      <div class='faq' style='display: none;' id='faq_8'>
        <?php echo $this->translate('Ans: The suggestions features are dependent on the %1$sSuggestions / Recommendations / People you may know & Inviter%2$s plugin and require that to be installed.', '<a href="http://www.socialengineaddons.com/socialengine-suggestions-recommendations-plugin" target="_blank">', '</a>'); ?>
      </div>
    </li>	
<!--    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php //echo $this->translate("I want to enhance the Stores on my site and provide more features to my users. How can I do it?"); ?></a>
      <div class='faq' style='display: none;' id='faq_9'>
        <?php //echo $this->translate('Ans: There are various apps / extensions available for the "Stores / Marketplace Plugin" which can enhance the Stores on your site, by adding valuable functionalities to them. To view the list of available extensions, please visit: %s.', '<a href="http://www.socialengineaddons.com/catalog/directory-stores-extensions" target="_blank">http://www.socialengineaddons.com/catalog/directory-stores-extensions</a>'); ?>
      </div>
    </li>	-->
  </ul>
</div>  
<?php break; ?>  
  
<?php case 'general': ?>
	<div class="admin_sitestore_files_wrapper">
		<ul class="admin_sitestore_files sitestore_faq">
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("How do I go about setting up this plugin on my site? What are the steps?");?></a>
				<div class='faq' style='display: none;' id='faq_1'>
					<?php echo $this->translate("Ans: Below are the steps you should follow to configure the plugin on your site according to your requirements:");?>
					<ul>
						<li>
							<?php echo $this->translate("Start by configuring the Global Settings of Stores, Products, Inviter and Short URL on your site. Some important settings here are:");?>
							<ul>
								<li>
									<?php echo $this->translate("Should Packages be enabled for stores?");?>
								</li>
								<li>
									<?php echo $this->translate("Should stores be able to have multiple admins / owners?");?>
								</li>
								<li>
									<?php echo $this->translate("Should store admins / owners be able to alter the block positions(layout) / add new available blocks on the store profiles?");?>
								</li>
								<li>
									<?php echo $this->translate("Should Price and Location fields be enabled for stores?");?>
								</li>
                <li>
									<?php echo $this->translate("Should Cart Contents Dropdown come in the header of your site?");?>
								</li>
                <li>
									<?php echo $this->translate("Should Reviews be allowed on the products on your site?");?>
								</li>
                <li>
									<?php echo $this->translate("Should comparison of products be enabled?");?>
								</li>
								<li>
									<?php echo $this->translate("Should maps integration be enabled?");?>
								</li>
                <li>
									<?php echo $this->translate("Enter your account details in the “Send Cheque To” field on which users will send their cheques to make payments.");?>
								</li>
							</ul>
							<?php echo $this->translate("and many more.");?>
						</li>
						<li>
							<?php echo $this->translate('If you have enabled packages for stores on your site, then create packages from the "Manage Packages" section. Users will have to select a package before creating a store. You can choose settings for packages like free / paid, duration, product types available, auto-approve, featured, sponsored, features available, apps accessible, etc.');?>
						</li>
						<li>
							<?php echo $this->translate('If you have disabled packages, then settings for stores like product types available, auto-approve, featured, sponsored, features available, apps accessible, etc will depend on Member Level Settings. You may configure them from the "Member Level Settings" section.');?>
						</li>
						<li>
							<?php echo $this->translate('If you have configured paid store packages on your site, then configure payment related settings on your site from the "Billing" > "Settings" and "Billing" > "Gateways" sections.');?>
						</li>
						<li>
							<?php echo $this->translate('Configure the categories and sub-categories for stores on your site from the "Categories" section.');?>
						</li>
						<li>
							<?php echo $this->translate('Create and configure profile types and profile questions (custom fields) for stores and products on your site from the Profile Fields section.');?>
						</li>
						<li>
							<?php echo $this->translate('Configure the Store Categories to Store Profile mapping and Product Categories to Product Profile mapping from the "Category - Profile Fields Mapping" section. This feature enables you to have different profile information questions for stores and products based on their categories.');?>
						</li>
            <li>
							<?php echo $this->translate('Configure the reviews and ratings settings for store and products on your site from the Reviews & Ratings section.	');?>
              <ul>
                <li>
                  <?php echo $this->translate('Choose Editors and Super Editor from the Products >> Editors section  and add Editor badges to assign them to the Editors.');?>
                </li>
              </ul>
            </li>
            <li>
							<?php echo $this->translate('Go to the Comparison Settings section to configure various comparison fields.');?>
						</li>
            <li>
							<?php echo $this->translate('From Shipping Locations section, add and manage shipping locations.');?>
						</li>
            <li>
							<?php echo $this->translate('Add and manage taxes to be applied on orders made on your site from Taxes section.');?>
						</li>
            <li>
							<?php echo $this->translate('Now, go to Photo Albums section to manage photo albums for stores and products on your site.');?>
						</li>
            <li>
							<?php echo $this->translate('Then, go to the Video Settings section to configure various settings for videos.');?>
						</li>
            <li>
							<?php echo $this->translate('Now from the Coupons section, manage coupons ongoing on your site.');?>
						</li>
            <li>
							<?php echo $this->translate('Approve, cancel and manage payment requests from the store admins.');?>
						</li>
						<li>
							<?php echo $this->translate('Choose the type of layout for Stores on your site from the "Store Layout" section. This plugin provides 2 types of layouts: tabbed and non-tabbed.');?>
						</li>
						<li>
							<?php echo $this->translate('Configure other settings for stores on your site like: Member Level Settings, Widget Settings, Ad Settings, Graph Settings, etc.');?>
						</li>
						<li>
							<?php echo $this->translate('Place the various widgets available with this plugin at the desired locations from the "Layout" > "Layout Editor" section.');?>
						</li>
<!--						<li>
							<?php echo $this->translate('Install and set up the desired apps for this plugin.');?>
						</li>-->
					</ul>
          <?php echo $this->translate('You can now start creating stores and products on your site.');?>
				</div>
			</li>
         
         <li>
				<a href="javascript:void(0);" onClick="faq_show('faq_21');"><?php echo $this->translate("How can I change the Landing Page of Stores on my website?");?></a>
				<div class='faq' style='display: none;' id='faq_21'>
					<?php echo $this->translate('Ans: Changing the Landing Page is very easy. To do so, please follow the below steps:<br/>1. Go to the "Layout" > "Menu Editor" in the admin panel of your site.<br/>2. Select "Main Navigation Menu" in the Editing drop-down.<br/>3. Disable the existing "Stores" menu item.<br/>4. Create a new menu item by clicking on "Add Item" link, and then entering the desired Label Name and URL of the page that you want to set as the Stores Landing Page on your site.');?>
				</div>
			</li>
         
         <li>
				<a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("Is this plugin integrated with the SocialEngine's inbuilt payment system? What should I do, if I want a different payment gateway to be integrated with this plugin?");?></a>
				<div class='faq' style='display: none;' id='faq_2'>
					<?php echo $this->translate("Ans: Yes, this plugin is integrated with the SocialEngine's inbuilt payment system, and hence PayPal and 2Checkout are supported. If you want a different payment gateway to be integrated with this plugin, then please %s.", '<a href="http://www.socialengineaddons.com/contact" target="_blank">contact us</a>');?>
				</div>
			</li>
         
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("Can a stores have multiple administrators? How will this work?");?></a>
				<div class='faq' style='display: none;' id='faq_3'>
					<?php echo $this->translate('Ans: Yes, stores on your site can have multiple administrators having equal authority on the store. Multiple admins for stores can be enabled from Global Settings using the field: "Store Admins". If enabled, then every Store will be able to have multiple administrators who will be able to manage that Store. Store Admins will have the authority to add other users as administrators of their Store from the Store Dashboard.');?>
				</div>
			</li>
         
         
         <li>
				<a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("Can I disable Multiple User-driven Stores (Marketplace with multiple Sellers) on my site. If Yes, then what should I do?");?></a>
				<div class='faq' style='display: none;' id='faq_4'>
					<?php echo $this->translate('Ans: Yes, you can disable Multiple User-driven Stores on your site. To do so, go the Global Settings section of this plugin and choose "Admin Stores" value for the field "Setup Type: Admin Stores or Marketplace"');?>
				</div>
			</li>
         
         
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("Can the owners of stores select somes specific administrators of their stores and highlight them in some way to users?");?></a>
				<div class='faq' style='display: none;' id='faq_5'>
					<?php echo $this->translate('Ans. Yes, Store owners can do so by making some admins as "Featured" from the Store Dashboard. These Featured Store admins will then be displayed in a block at Store Profile.');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("What is Proximity Search?");?></a>
				<div class='faq' style='display: none;' id='faq_6'>
					<?php echo $this->translate("Ans: Proximity Search enables users to find stores within a certain distance from a location.");?>
				</div>
			</li>

			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate('How can I disbale the Price Field from the "Open a New Store" section?');?></a>
				<div class='faq' style='display: none;' id='faq_7'>
					<?php echo $this->translate('Ans: Go to the "Global Settings" >> "Stores" section of this plugin and set the "Price Field" field to "No" and save the settings. This will disable the Price field at the "Open a New Store" form.');?>
				</div>
			</li>

			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate('What is the difference between sponsored and featured stores?');?></a>
				<div class='faq' style='display: none;' id='faq_8'>
					<?php echo $this->translate('Ans: "Sponsored" and "Featured" are just some kind of special or can say preferred categories which are in some way highlighted through dedicated widgets on the site.<br />Now, if we talk about the difference between the two, then there is no difference as such on the level of functionality between the two. They just work like labels for a particular store so that user can view the Store with that label.<br /><br />For ex- there is a "Featured Stores Slideshow widget" and a "Sponsored Stores Carousel widget" in this plugin.<br />So, one difference in terms of display of Featured and Sponsored Stores is that Featured Stores are displayed in Slideshow and Sponsored Stores are displayed in Carousel in the respective widgets.<br /><br />But there is no such criteria only on which a store can be made sponsored or featured. Site admin can make any store sponsored or featured considering the value of that Store and also users can select packages 
accordingly while creating store to make their store sponsored or featured.');?>
				</div>
			</li>
      
      <li>
				<a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo $this->translate("How can I mark products as Hot instead of New on my site?");?></a>
				<div class='faq' style='display: none;' id='faq_9'>
					<?php echo $this->translate('Ans: To mark products as Hot instead of New, you can change the label of New marker for the Products by replacing the "new-label.png" image at the path "application/modules/Sitestoreproduct/externals/images/new-label.png".');?>
				</div>
			</li>
			
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo $this->translate("How can stores be made sponsored? Where are sponsored stores displayed? Can I have users to pay for Sponsored Stores?");?></a>
				<div class='faq' style='display: none;' id='faq_10'>
					<?php echo $this->translate("Ans: There are 3 ways in which stores can be made sponsored:");?>
							<br />
							<?php echo $this->translate('1) If you have packages enabled on your site, then you can choose for stores of a package to be automatically made sponsored.');?>
							<br />
							<?php echo $this->translate('2) If packages are disabled, then you can choose from Member Level Settings that stores created by certain member levels be automatically made sponsored.');?>
							<br />
							<?php echo $this->translate('3) From the Manage Stores section, you can make / remove stores as sponsored.');?>
							<br />
					<?php echo $this->translate('Sponsored Stores are shown in the "Sponsored Stores Carousel" widget. Also, at various places like in listing of stores on Stores Home and in Browse Stores, sponsored stores are specially marked. The setting: "Default ordering on Browse Stores" in Global Settings also enable you to show sponsored stores at the top in the listing on browse stores. Also, on the main profile of sponsored stores, you can configure a label to be shown above the profile picture. Users can be made to pay to get their stores sponsored by making auto-sponsored packages as paid packages.'); ?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_11');"><?php echo $this->translate("How can stores be made featured? Where are featured stores displayed? Can I have users to pay for Featured Stores?");?></a>
				<div class='faq' style='display: none;' id='faq_11'>
					<?php echo $this->translate("Ans: There are 3 ways in which stores can be made featured:");?>
							<br />
							<?php echo $this->translate('1) If you have packages enabled on your site, then you can choose for stores of a package to be automatically made featured.');?>
							<br />
							<?php echo $this->translate('2) If packages are disabled, then you can choose from Member Level Settings that stores created by certain member levels be automatically made featured.');?>
							<br />
							<?php echo $this->translate('3) From the Manage Stores section, you can make / remove stores as featured.');?>
							<br />
					<?php echo $this->translate('Featured Stores are shown in the "Featured Stores Slideshow" widget. Also, at various places like in listing of stores on Stores Home and in Browse Stores, featured stores are specially marked. The setting: "Default ordering on Browse Stores" in Global Settings also enable you to show featured stores at the top in the listing on browse stores. Also, on the main profile of featured stores, you can configure a label to be shown below the profile picture. Users can be made to pay to get their stores featured by making auto-featured packages as paid packages.'); ?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_12');"><?php echo $this->translate("I have multiple networks on my site and I want users to be shown only those stores belonging to their network. How can this be done?");?></a>
				<div class='faq' style='display: none;' id='faq_12'>
					<?php echo $this->translate('Ans: You can do this by saving "Yes" for the "Browse by Networks" field in Global Settings.');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_13');"><?php echo $this->translate("I have closed a Store. But still I am able to view this. How can I disable the display of closed stores to users ?");?></a>
				<div class='faq' style='display: none;' id='faq_13'>
					<?php echo $this->translate("Ans: To do so, please go to the 'Global Settings' >> ‘Stores’ section of this plugin and find the field 'Open / Closed status in Search'. Setting this field to 'No' will disable the display of Closed Stores to users on the Browse Stores and in the search options and results.");?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_14');"><?php echo $this->translate('Is there any need of Google Maps JavaScript API keys?');?></a>
				<div class='faq' style='display: none;' id='faq_14'>
					<?php echo $this->translate('Ans: No, there is no need of Google Maps JavaScript API keys. This is because we are using Google Maps Javascript API V3 in this plugin and this version no longer needs API keys. For more details, you can visit this link: %s', '<a href="http://code.google.com/apis/maps/documentation/javascript/basics.html" target="_blank">http://code.google.com/apis/maps/documentation/javascript/basics.html</a>');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_15');"><?php echo $this->translate('I am having almost all the Stores of my site belonging to the locations which comes in a particular area or city. Is there anyway so that users can clearly view the nearby areas and locations in the map without manually zooming it?');?></a>
				<div class='faq' style='display: none;' id='faq_15'>
					<?php echo $this->translate("Ans: Yes, you can set the centre location for the Stores in the field 'Centre Location for Map at Stores Home and Browse Stores' at the Global Settings section of this plugin and also below that you can enter the deafult zoom level for the map so that users do not have to to manually do that and you can specify it accordingly.");?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_16');"><?php echo $this->translate("How can I re-arrange the items in the search-box being shown on 'Stores Home' and 'Browse Stores' stores ?");?></a>
				<div class='faq' style='display: none;' id='faq_16'>
					<?php echo $this->translate("Ans: Yes, you can re-arrange the items in the search-box from the 'Search Box Settings' section at the admin side of this plugin. There, you can drag-and-drop the search fields vertically and save the sequence according to you.");?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_17');"><?php echo $this->translate("I do not want to show some fields in the search box to users. How can I disable them ?");?></a>
				<div class='faq' style='display: none;' id='faq_17'>
					<?php echo $this->translate("Ans: Go to the 'Search Box Settings' section at the admin side of this plugin and click on the Hide icon for such fields. Fields which have been made hidden here would not be shown in the search box. You can also display them later if you want.");?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_18');"><?php echo $this->translate("I want my users to be able to link their Stores to other related Stores. Can it be done ?");?></a>
				<div class='faq' style='display: none;' id='faq_18'>
					<?php echo $this->translate("Ans: Yes, you can link your Store to the other related stores with this plugin. To do so, please go the 'Global Settings' section of this plugin and select the option 'Yes' for the field 'Linking Stores' there. Doing so will make a 'Link to your Store' link appear on Stores and then users can select any of their Stores which they want to be linked to that Store.<br /><br />Once a Store is linked to their Store, that Store will appear in a widget titled 'Linked stores' on their Store's Profile.<br /> Please make sure that 'Store Profile Linked Stores' widget is placed on the Store Profile page.");?>
				</div>
			</li>
      <li>
				<a href="javascript:void(0);" onClick="faq_show('faq_19');"><?php echo $this->translate("I want emails sent out from this plugin to be attractive. How can I do this?");?></a>
				<div class='faq' style='display: none;' id='faq_19'>
					<?php echo $this->translate('Ans: You can send attractive emails to your users via rich, branded, professional and impact-ful emails by using our "%1$sEmail Templates Plugin%2$s". To see details, please %1$svisit here%2$s.', '<a href="http://www.socialengineaddons.com/socialengine-email-templates-plugin" target="_blank">', '</a>');?>
				</div>
			</li>
      <li>
				<a href="javascript:void(0);" onClick="faq_show('faq_20');"><?php echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?");?></a>
				<div class='faq' style='display: none;' id='faq_20'>
					<?php echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homestore and then check the store which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.");?>
				</div>
			</li>
		</ul>
	</div>
	<?php break; ?>

	<?php case 'package': ?>
	<div class="admin_sitestore_files_wrapper">
		<ul class="admin_sitestore_files sitestore_faq">
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("What are Store Packages? How are Packages and Member Level Settings related?");?></a>
				<div class='faq' style='display: none;' id='faq_2'>
					<?php echo $this->translate('Ans: Both packages and member level settings enable you to configure settings for stores belonging to them / created by members belonging to them, like product types available, auto-approve, featured, sponsored, features available, apps accessible, etc. Packages can be enabled from "Global Settings" >> "Stores" section and created from "Manage Packages" section. If packages are disabled, then settings configured in Member Level Settings apply. If packages are enabled, then before creating a store on your site, users will have to choose a package for it. Packages in this system are very flexible and create many settings as mentioned below, to suit your needs:');?>
					<ul>
						<li>
							<?php echo $this->translate('Paid / Free package. Paid packages enable you to monetize your site!');?>
						</li>
						<li>
							<?php echo $this->translate('Package cost');?>
						</li>
            
            
            <li>
							<?php echo $this->translate('Product Types availability');?>
						</li>
            <li>
							<?php echo $this->translate('Maximum files and file sizes to be uploaded in the downloadable products.');?>
						</li>
            <li>
							<?php echo $this->translate('Configure commission type and value');?>
						</li>
            <li>
							<?php echo $this->translate('Configure payment threshold amount.');?>
						</li>
           
            
            
						<li>
							<?php echo $this->translate('Lifetime Duration for stores of this package (forever, or fixed duration)');?>
						</li>
						<li>
							<?php echo $this->translate('Auto-Approve stores of this package');?>
						</li>
						<li>
							<?php echo $this->translate('Make stores of package sponsored');?>
						</li>
						<li>
							<?php echo $this->translate('Make stores of package featured');?>
						</li>
						<li>
							<?php echo $this->translate('Which of the following features should be available to stores of this package:');?>
							<ul>
								<li>
									<?php echo $this->translate('Tell a friend');?>
								</li>
								<li>
									<?php echo $this->translate('Print');?>
								</li>
								<li>
									<?php echo $this->translate('Rich Overview');?>
								</li>
								<li>
									<?php echo $this->translate('Location Map');?>
								</li>
								<li>
									<?php echo $this->translate('Contact Details');?>
								</li>
<!--								<li>
									<?php echo $this->translate('Save to Foursquare Button');?>
								</li>-->
                <?php //if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoretwitter')) :?>
<!--                  <li>
                    <?php //echo $this->translate('Display Twitter Updates');?>
                  </li>-->
                <?php //endif;?>
								<li>
									<?php echo $this->translate('Send an Update');?>
								</li>
							</ul>
							<?php echo $this->translate('and more'); ?>
						</li>
						<li>
							<?php echo $this->translate('Modules / Apps that should be available to stores of this package.'); ?>
						</li>
						<li>
							<?php echo $this->translate('Show all, none or restricted profile information for stores of this package. Restricted profile information will enable you to choose the profile fields that should be available to stores of this package.'); ?>
						</li>
					</ul>
				</div>
			</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("I have created a new package but no Apps are available for the stores of this package. What can be the reason?");?></a>
					<div class='faq' style='display: none;' id='faq_3'>
						<?php echo $this->translate("Ans. To enable the various Apps for the stores of a package, you would have to edit this package from 'Manage Packages' section and select the modules in the 'Modules / Apps' field which you want to be available for the stores of that package.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("Can the Store owner change the package of a Store after its creation?");?></a>
					<div class='faq' style='display: none;' id='faq_4'>
						<?php echo $this->translate("Ans: Yes, Store owners can change the package of their Stores from the 'Packages' section at Store Dashboard. All the available packages with their features are listed there. Once the package for a store is changed, all the settings of the store will be applied according to the new package, including apps available, features available, price, etc.<br />Please note that if you(admin) have not selected the 'Auto-Approve' field for the new package in 'Manage Packages' section, then the Store will get dis-approved. Also, if the new package is a paid package then the Store will get dis-approved even if it has been made auto-approved by you(admin) and store owner will have to first make the payment to make it approved again.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_59');"><?php echo $this->translate("Why am I not able to delete a Store Package?");?></a>
					<div class='faq' style='display: none;' id='faq_59'>
						<?php echo $this->translate("Ans: You can not delete a Store Package once you have created it. This is because the consistency of the already created stores in that package would get affected in that case. But if you wish, you can disable a store package from Manage Packages section and hence it would not be displayed in the list of packages during the initial step of the store creation and also while upgrading the package of a store at Store's Dashboard.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_60');"><?php echo $this->translate("Is it possible to let users create free trial stores on my site valid only for a limited period of time?");?></a>
					<div class='faq' style='display: none;' id='faq_60'>
						<?php echo $this->translate('Ans: Yes, it is possible to do so by doing the following things:<br />1) You can disable the existing free package from the "Manage Packages" section at the admin panel of this plugin.<br />2) Now, create a new free package from there and you can set the specifications including the "Duration" and the features you want to give users in that package and then do not select the checkbox for field \'Show in "Other available Packages" List\' so that users will not be able to upgrade their store\'s package and use this package for more than the no. of days you have set there.<br />3) Also, in future when you do not want users to create free Stores anymore at your site, you can disable all the FREE packages from "Manage Packages" section.');?>
					</div>
				</li>
			</ul>
		</div>
	<?php break; ?>

	<?php case 'layout': ?>
		<div class="admin_sitestore_files_wrapper">
			<ul class="admin_sitestore_files sitestore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_11');"><?php echo $this->translate("Can I enable the owners of stores on my site to customize the position of blocks / layout of their stores, and add new blocks to it?");?></a>
					<div class='faq' style='display: none;' id='faq_11'>
						<?php echo $this->translate('Ans: Yes, you can do so by enabling the "Edit Store Layout" field in Global Settings. If enabled, store admins will be able to alter the block positions / add new available blocks on the stores profile. Store admins will also be able to add HTML blocks on their stores profile.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_12');"><?php echo $this->translate("Can I change the layout types for profile of stores on my site? What options do I have?");?></a>
					<div class='faq' style='display: none;' id='faq_12'>
						<?php echo $this->translate('Ans: Yes, you can choose from amongst 2 attractive AJAX based layouts for Stores on your site, from the "Store Layout" section. The 2 layouts are: Tabbed Layout and Non-tabbed Layout. The 2 layouts differ primarily in the way the AJAX based widgets like Info, Updates, Overview, and modules based widgets like Photos, Videos, Reviews, etc are displayed. The tabbed layout has main widgets as ajax based tabs in a horizontal row and in the middle column of the store. The non-tabbed layout has main widgets as ajax based links in a vertical order and in the left column of the store, below the store profile picture.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_45');"><?php echo $this->translate("I do not want some tabs to be hidden in the 'More' at the Store Profile. I want users to view them directly on the Store profile. What should I do ?");?></a>
					<div class='faq' style='display: none;' id='faq_45'>
						<?php echo $this->translate("Ans: If you want some specific tabs to be shown directly and not in the 'More' tab, then you can do the following things:<br /><br />1) Go to the Global Settings section of this plugin, and set the value of 'Tabs / Links' field like that so as to occupy the whole space in the navigation bar for tabs at Store profile. This will display that much of tabs out of the 'More' tab on the Store Profile.<br /><br />2) Now, if user 'Edit Store Layout' is disabled from Global Settings section then, you can go to the 'Layout' > 'Layout Editor' section at the admin side and edit the Store profile layout and place those widgets a bit above vertically in the tab container there.<br /> Otherwise individual Store Admins will be able to set the ordering of tabs on the Store Profile from their Store Dashboard's 'Edit Layout' section.<br /><br />It will let those tabs appear in priority and out of the 'More' tab if there are too many tabs.");?>
					</div>
				</li>
				<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_43');"><?php echo $this->translate("The left column menu is not shown in various tabs on Store profile. But this is not the case with 'Info' and 'Updates' tabs. Why is it so ?");?></a>
				<div class='faq' style='display: none;' id='faq_43'>
					<?php echo $this->translate("Ans: In that case Community Ads would have been installed at your site and is being integrated with Stores / Marketplace Plugin.<br /><br />When Ads are shown in various tabs at Store profile, we have to hide the left side menu column. This is because of the lack of width in that case. So, to display the Ads in such a way that there is no UI issue, we are hiding the left side menu in case of tabs.<br /><br /> While at Info and Update tabs, there is sufficient width to display Ads at the same Store and we do not need to hide left side menu in that case.");?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_44');"><?php echo $this->translate("I have messed up with the layout of Store Profile and want to set it to the default layout again. What should I do ?");?></a>
				<div class='faq' style='display: none;' id='faq_44'>
					<?php echo $this->translate("Ans: Yes, you can reset the layout of your Stores to default layout from the 'Store Layout' > 'Store Profile Layout Type' section at the admin side of this plugin. You can just choose one out of the two layouts there and save it. It will set the layout of all the Stores at your site to the default layout.");?>
				</div>
			</li>
      <li>
				<a href="javascript:void(0);" onClick="faq_show('faq_80');"><?php echo $this->translate('How can I configure Stores Main Navigation Menu bar? Suppose my site is an Album based site and I want users to view "Albums" tab above other tabs in the Stores Main Navigation Menu. How can I do this?');?></a>
				<div class='faq' style='display: none;' id='faq_80'>
					<?php echo $this->translate('Ans: You can do so by following the steps mentioned below:<br /><br />
						1) Go to the Global Settings section of this plugin, and set the value of "Tabs in Stores navigation bar" as the number of tabs you want to be displayed before the "More" drop-down link including more link.<br /><br />
						2) Now, to configure the tabs, go to the "Layout" > "Menu Editor" section at the admin side. There on selecting "Store Main Navigation Menu" from "Editing" drop-down you can add item or edit the items in the navigation bar. To re-order the tabs drag and drop the tabs to their desired position.');?>
				</div>
			</li>
			</ul>
		</div>
	<?php break; ?>

	<?php case 'claim': ?>
		<div class="admin_sitestore_files_wrapper">
			<ul class="admin_sitestore_files sitestore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_13');"><?php echo $this->translate("Can users claim stores on my site, if they are the rightful owner? How does this work?");?></a>
					<div class='faq' style='display: none;' id='faq_13'>
						<?php echo $this->translate('Ans: The claiming of stores feature can be enabled from the "Claim a Store Listing" field in Global Settings. If enabled, users will be able to file claims for stores. You can customize the position of the "Claim a Store" link from Global Settings. Claims filed by users can be managed from the "Manage Claims" section. From "Member Level Settings", you can choose if users of a member level should be able to claim stores. Whenever someone makes a claim for a store, that claim comes to you(admin) for review and approval.<br />You(admin) can also assign certain users as "Claimable Store Creators" from the "Manage Claims" section. Though using the "Claim a Store" link a user can make a claim for any store, the stores created by "Claimable Store Creators" get the "Claim this Store" link on the store profile itself. This would be useful in cases like if you have certain members whose job is to create only those 
stores on your site which could later be easily claimed by their rightful owners.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_14');"><?php echo $this->translate("How can I modify the Terms of Service for claiming a store?");?></a>
					<div class='faq' style='display: none;' id='faq_14'>
						<?php echo $this->translate('Ans: You can modify them from the Language variables of the plugin using the Language Manager. Go to the Layout > Language Manager and search for the two variables "STORE_TERMS_CLAIM_1" and "STORE_TERMS_CLAIM_2" and edit them according to your specifications.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_15');"><?php echo $this->translate("If I approve a claim request for a Store, what changes take place?");?></a>
					<div class='faq' style='display: none;' id='faq_15'>
						<?php echo $this->translate("Ans: If you approve a claim request by a user for a Store, it means that you are assigning that Store to the claimer and the claimer will now be the new owner of that store. All ownership rights for this store will then be transferred to the new owner. In this case, both the new and old owners will be recieving an email concerning the change in the ownership of the Store.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_16');"><?php echo $this->translate("Can I change the owner of a Store and assign it to someone else even if I have not recieved a claim request for that Store?");?></a>
					<div class='faq' style='display: none;' id='faq_16'>
						<?php echo $this->translate("Ans. Yes, you can change the owner of a store from the 'change owner' option in the 'Mange Stores' section. Both the new and old owner of the store will be recieving an email concerning the change in the ownership of the store.");?>
					</div>
				</li>
			</ul>
		</div>
	<?php break; ?>

	<?php case 'category': ?>
		<div class="admin_sitestore_files_wrapper">
			<ul class="admin_sitestore_files sitestore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_23');"><?php echo $this->translate("The Categories and Sub-categories widget is not showing all the categories and sub-categories. What can be the reason?");?></a>
					<div class='faq' style='display: none;' id='faq_23'>
						<?php echo $this->translate("Ans: This widget for stores only shows those categories and sub-categories which have atleast one store in them and widget for products only shows those categories and sub-categories which have atleast one product in them. However, the Categories and Sub-categories sidebar widget shows all the categories and sub-categories irrespective of the number of stores or products in them respectively.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_24');"><?php echo $this->translate("There is no option to delete a category or sub-category. If I want to delete them, how can I do so ?");?></a>
					<div class='faq' style='display: none;' id='faq_24'>
						<?php echo $this->translate("Ans. Go to the 'Categories' >> 'Stores' section and click on the category or sub-category name which want to delete. The category name would then appear in a text box for editing. Now, you can either edit category name or delete the whole text to delete the category.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_40');"><?php echo $this->translate("How can I make my users to edit categories of their Stores ?");?></a>
					<div class='faq' style='display: none;' id='faq_40'>
						<?php echo $this->translate("Ans: Please go to the 'Global Settings' section of this plugin and select the value 'Yes' for the 'Edit Store Category' field there. This way store admins will be able to edit the category of their Stores even after the store creation in the 'Edit Info' section of the Store Dashboard.");?>
					</div>
				</li>

				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_46');"><?php echo $this->translate("I am not able to see sub-categories below some categories in the widget on the 'Stores Home' page. Why is it so ?");?></a>
					<div class='faq' style='display: none;' id='faq_46'>
						<?php echo $this->translate("Ans: This is because you would have not selected any subcategory for those Stores created in those categories. And as we have told you in the 1st FAQ of this section also that this widget only shows those categories and sub-categories which have atleast one store in them.");?>
					</div>
				</li>
			</ul>
		</div>
	<?php break; ?>

	<?php case 'profile': ?>
		<div class="admin_sitestore_files_wrapper">
			<ul class="admin_sitestore_files sitestore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_17');"><?php echo $this->translate("Can I add custom profile fields for stores? Can these fields be related to the categories of the stores?");?></a>
					<div class='faq' style='display: none;' id='faq_17'>
						<?php echo $this->translate('Ans: Yes, you can create custom profile fields for stores from the "Profile Fields" section. Profile Fields can be created by first creating profile types and then creating profile questions for them. You can also associate mappings between categories and store profile types from the "Category-Store Profile Mapping" section, such that profile fields are available to stores based on their categories. If you have not created such mappings between categories and profile types, then store owners will have to select a profile type before filling profile information.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_39');"><?php echo $this->translate("I have added some Profile fields for Stores, but these are not shown on the Store creation form. Why is it so ?");?></a>
					<div class='faq' style='display: none;' id='faq_39'>
						<?php echo $this->translate('Ans: The profile questions which are created by the site admin in different profile types for the Stores on the site does not appear at the time of the creation of the Store in the create form.<br /><br />These profile information related questions appear after the creation of the Store at the Store Dashboard in the "Profile Info" tab.<br /><br />The user can select the profile type there and then fill the profile info in various fields. In case the category of that store had already mapped with a profile type by you (site admin), then the user can directly fill the information in the corresponding fields of the profile type.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_18');"><?php echo $this->translate("I have created a new Profile Type for Stores and associated it with a category. But while editing the Profile Info of a Store in that category from the Store Dashboard, all the fields of that profile type did not appear. What can be the reason?");?></a>
					<div class='faq' style='display: none;' id='faq_18'>
						<?php echo $this->translate("Ans. Availability of Profile fields to stores also depends on their package; if packages are disabled, then it depends on the member level settings for the store owner. Now, you would have unchecked some profile fields in the 'Profile Information' field of the package your store belongs to. And if packages are disabled, then you would have unchecked some profile fields in the 'Profile Creation' field of 'Member Level Settings' for the member level of the owner of this Store. So, Please check these settings.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_19');"><?php echo $this->translate("How can I re-arrange the sequence of the custom profile fields for stores?");?></a>
					<div class='faq' style='display: none;' id='faq_19'>
						<?php echo $this->translate('Ans: You may drag-and-drop the fields in the "Profile Fields" section to their desired position in sequence.');?>
					</div>
				</li>
        
        <li>
					<a href="javascript:void(0);" onClick="faq_show('faq_109');"><?php echo $this->translate("I want 2 products belonging to different categories mapped with 2 different Profile Fields to be compared over a particular attribute. How do I configure the Profile Question for this attribute?");?></a>
					<div class='faq' style='display: none;' id='faq_109'>
						<?php echo $this->translate('Ans: In this case, you should first create the Profile Question for this attribute for first Profile Field and then duplicate this Question for the second Profile Field. Example: You may want to compare 2 products belonging to categories Phone and Tablet based on their Screen Size.');?>
					</div>
				</li>
			</ul>
		</div>
	<?php break; ?>

	<?php //case 'insights': ?>
<!--		<div class="admin_sitestore_files_wrapper">
			<ul class="admin_sitestore_files sitestore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_20');"><?php //echo $this->translate("Can stores / marketplace owners see the performance of their store listing?");?></a>
					<div class='faq' style='display: none;' id='faq_20'>
						<?php //echo $this->translate('Ans: Yes, store owners can see both graphical as well as statistical reports of their stores. Graphical statistics are shown in the "Insights" section of the Store Dashboard and tabular statistics are shown in the "Reports" section. Insights can also be made available to stores based on their package. Insights for stores show graphical statistics and other metrics such as views, likes, comments, active users, etc over different durations and time summaries. Using this, store admins will also be getting periodic, auto-generated emails containing Store insights. Settings for these can be configured from the "Insights Email Settings" and "Insights Graph Settings" sections in the admin panel.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_25');"><?php //echo $this->translate("For the Graphical Insights of the Stores on my site, the dates shown do not match with the actual dates of the activities. For example, it shows up yesterday's or tomorrow's date in place of today's date. How should this be corrected ?");?></a>
					<div class='faq' style='display: none;' id='faq_25'>
						<?php //echo $this->translate('Ans. This is happening because of the Locale Settings of your site. Please go to the "Settings" > "Locale Settings" section in the Admin Panel and set the \'Default Timezone\' according to your location.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_26');"><?php //echo $this->translate("I am not able to view comments for the Stores in the insights displayed at Store Profile, graph, reports etc. What can be the reason?");?></a>
					<div class='faq' style='display: none;' id='faq_26'>
						<?php //echo $this->translate("Ans: Comments are shown at all these places only if Info widget is rendered on Store Profile as comments can be posted from there. You might have removed the Info widget from Store Profile. Please check it from the 'Layout' > 'Layout Editor' section.");?>
					</div>
				</li>
			</ul>
		</div>-->
	<?php //break; ?>
	<?php //case 'import': ?>
<!--		<div class="admin_sitestore_files_wrapper">
			<ul class="admin_sitestore_files sitestore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_50');"><?php echo $this->translate("I want to import many Stores altogether from a CSV file. Is that possible with this plugin ?");?></a>
					<div class='faq' style='display: none;' id='faq_50'>
						<?php echo $this->translate("Ans: Yes, you can import many stores at the same time from a CSV file. To do so, please go to the 'Import' section at the admin side of this plugin and read all the instructions given there under the section 'Import Stores from a CSV file' and then click on 'Import a file' link.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_51');"><?php echo $this->translate("I want to convert all the Listings of my site into Stores. How can it be done ?");?></a>
					<div class='faq' style='display: none;' id='faq_51'>
						<?php echo $this->translate("Ans: Yes, you can convert your Listings into Stores but there are some required conditions for that. Please go to the 'Import' section at the admin side of this plugin and fulfil all the required conditions given under the section 'Import Listings into Stores'. Then, you can import your Listings into Stores there.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_52');"><?php echo $this->translate("In the import section at admin side, I can not see any button or link to import Listings at my site into Stores. Why is it so ?");?></a>
					<div class='faq' style='display: none;' id='faq_52'>
						<?php echo $this->translate("Ans: There are some required conditions which are required to be fulfilled before starting to import Listings into Stores. when the condition becomes true or is fulfilled, it appears in green otherwise red. So, once all the conditions are green or fulfilled, the button for 'Import listings' will automatically appear there.");?>
					</div>
				</li>
			</ul>
		</div>-->
	<?php //break; ?>

	<?php case 'language': ?>
		<div class="admin_sitestore_files_wrapper">
			<ul class="admin_sitestore_files sitestore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_21');"><?php echo $this->translate("I want to use this plugin for directory of cars. How can I change the word: 'stores' to 'cars' in this plugin?");?></a>
					<div class='faq' style='display: none;' id='faq_21'>
						<?php echo $this->translate("Ans: You can easily use this plugin for creation of any type of directories. You can change the word 'stores' to your directory type from the 'Layout' > 'Language Manager' section in the Admin Panel.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_42');"><?php echo $this->translate("I want to change the text 'storeitems' to 'caritems' in the URLs of this plugin. How can I do so ?");?></a>
					<div class='faq' style='display: none;' id='faq_42'>
						<?php echo $this->translate('Ans: To do so, please go to the Global Settings section of this plugin. Now, search for the fields:<br />1) "Stores URL alternate text for \'storeitems\'" <br /> 2) "Stores URL alternate text for \'storeitem\'" <br /><br />Now, enter the text you want to display in place of \'storeitems\' and \'storeitem\' in the respective text boxes there.');?>
						</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_29');"><?php echo $this->translate("There are multiple languages on my site. How should this plugin be used for non-English languages?");?></a>
					<div class='faq' style='display: none;' id='faq_29'>
						<?php echo $this->translate("Ans : This plugin only comes with English language by default. For other languages, you need to copy the 'sitestore.csv' language file from the directory: '/application/languages/en/' of your site, to the directory '/application/languages/LANGUAGE_PACK_DIRECTORY/'. Then, go to the section 'Layout' > 'Language Manager' in the Admin Panel and edit phrases for the desired language.");?>
						</div>
				</li>
			</ul>
		</div>
	<?php break; ?>
	<?php case 'customize': ?>
		<div class="admin_sitestore_files_wrapper">
			<ul class="admin_sitestore_files sitestore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_56');"><?php echo $this->translate("I want to remove some information from the 'Basic Information' section in the Info tab at the Store profile. How can I do so?");?></a>
					<div class='faq' style='display: none;' id='faq_56'>
						<?php echo $this->translate("Ans: You can do so by commenting the code corresponding to the sections you do not want to display in the following template file:<br />'application/modules/Sitestore/widgets/info-sitestore/index.tpl'");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_57');"><?php echo $this->translate("I want to customize the main menu for Stores. From where can I do this ?");?></a>
					<div class='faq' style='display: none;' id='faq_57'>
						<?php echo $this->translate("Ans: Yes, you can do so from the admin panel at: 'Layout' > 'Menu Editor' section. Open the 'Store Main Navigation Menu' to edit and then you can edit all the items of this menu there.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_41');"><?php echo $this->translate("I want to change the 'Stores' text in the mini-navigation bar for Stores. How can I do so ?");?></a>
					<div class='faq' style='display: none;' id='faq_41'>
						<?php echo $this->translate('Ans: To do this, follow the steps below:<br /><br />1) Please open the file with path: <br />"application/modules/Sitestore/views/scripts/payment_navigation_views.tpl"<br /><br />2) Now, change the word "Stores" to anything you want at line no. 15 (approx) in this file.<br /><br />It will change the text "Stores" to as desired in the mini navigation bar for Stores.');?>
						</div>
				</li>
				<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_33');"><?php echo $this->translate("How can I improve search engine optimization (SEO) for the main stores profile?");?></a>
				<div class='faq' style='display: none;' id='faq_33'>
					<?php echo $this->translate("Ans: A quick and easy way to improve SEO for store profile is to manually do the template level changes as detailed below (Please note that these changes will need to be re-done when you upgrade SocialEngine on your site.):");?>
					<br /><br />
					<?php echo $this->translate('1) 2 easy changes in the template file : "/application/modules/Core/widgets/container-tabs/index.tpl":'); ?>
					<br /><br />
					<?php echo $this->translate('i) Open the template file and find around line 50:'); ?>
					<br /><br />
					<div class="code">
						<?php echo '&lt;li class="&lt;?php echo $class ?&gt;"&gt;&lt;a href="javascript:void(0);" onclick="tabContainerSwitch($(this), \'&lt;?php echo $tab[\'containerClass\'] ?&gt;\');"&gt;&lt;?php echo $this->translate($tab[\'title\']) ?&gt;&lt;?php if( !empty($tab[\'childCount\']) ): ?&gt;&lt;span&gt;(&lt;?php echo $tab[\'childCount\'] ?&gt;)&lt;/span&gt;&lt;?php endif; ?&gt;&lt;/a&gt;&lt;/li&gt;'; ?>
					</div>
					<br />
					<?php echo $this->translate('Replace the above code with the below code:') ?>
					<br /><br />
					<div class="code">
						<?php echo '&lt;li class="&lt;?php echo $class ?&gt;"&gt;&lt;a href="&lt;?php echo  Zend_Controller_Front::getInstance()->getRequest()->getRequestUri()."/tab/".$tab[\'id\']?&gt;" onclick="tabContainerSwitch($(this), \'&lt;?php echo $tab[\'containerClass\'] ?&gt;\'); return false;"&gt;&lt;?php echo $this->translate($tab[\'title\']) ?&gt;&lt;?php if( !empty($tab[\'childCount\']) ): ?&gt;&lt;span&gt;(&lt;?php echo $tab[\'childCount\'] ?&gt;)&lt;/span&gt;&lt;?php endif; ?&gt;&lt;/a&gt;&lt;/li&gt;'; ?>
					</div>
					<br />
					<?php echo $this->translate('2) 1 easy addition in the CSS file : "application/modules/Core/externals/style/main.css":'); ?>
					<br /><br />
					<?php echo $this->translate('Open this CSS file and add the below code at the end of this file:'); ?>
					<div class="code">
					<?php echo $this->translate('.tab_pulldown_contents > ul > li >a, .tab_pulldown_contents > ul > li >a:hover{<br />
		color: $theme_tabs_font_color;<br />
		text-decoration:none;<br />
	}'); ?>
					</div>
					<br /><br />
					<?php echo $this->translate('NOTE: Whenever you will upgrade Socialengine Core at your site, these changes will be overwritten and you will have to do them again in the respective files as mentioned above.'); ?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_70');"><?php echo $this->translate("Can I change the Featured label for the Stores created on my site? If yes then how?");?></a>
				<div class='faq' style='display: none;' id='faq_70'>
					<?php echo $this->translate('Ans: Yes, you can change the look of the Featured label for the Stores on your site. You can customize Featured label for your site by replacing the image in the file at the path "application/modules/Sitestore/externals/images/featured-label.png".');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_71');"><?php echo $this->translate("Can I customize the icon for Featured Stores on my site? If yes then how?");?></a>
				<div class='faq' style='display: none;' id='faq_71'>
					<?php echo $this->translate('Ans: Yes, you can customize the icons for Featured Stores on your site by replacing the image in the file at the path "application/modules/Sitestore/externals/images/sitestore_goldmedal1.gif".');?>
				</div>
			</li>
			</ul>
		</div>
	<?php break; ?>

	<?php case 'integration': ?>
		<div class="admin_sitestore_files_wrapper">
			<ul class="admin_sitestore_files sitestore_faq">
<!--				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_53');"><?php //echo $this->translate("I have installed the Stores Discussions extension plugin on my site. But I can not find it in the 'Plugins' dropdown at the admin panel. Why should I do ?");?></a>
					<div class='faq' style='display: none;' id='faq_53'>
						<?php //echo $this->translate("Ans: Discussion for Stores / Marketplace is a FREE extension plugin. Hence, it does not have admin section of its own and gets automatically enabled once you install it.<br />Its settings are controlled from the admin section of Stores / Marketplace Plugin only.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_54');"><?php //echo $this->translate("I do not find disscussion section in my stores. Where can I enable it ?");?></a>
					<div class='faq' style='display: none;' id='faq_54'>
						<?php //echo $this->translate("Ans: To enable disscussions for Stores, please go to the 'Manage Packages' section of this plugin (if packages are enabled from Global Settings) and edit the packages one by one for which you want to enable Discussions for Stores and select the checkbox corresponding to the 'Discussions' in the 'Modules / Apps' field.<br /><br />If Packages are disabled from Global Settings section of this plugin, then 'Discussions' depend on the Comments privacy. If comments are enabled for that member level then discussions are also otherwise not.");?>
					</div>
				</li>-->
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_27');"><?php echo $this->translate("I have installed the Community Ads plugin and set the 'Advertise your Store Widget' and 'Sample Ad Widget' options as 'Yes' in the 'Ad Settings' section. But I am not able to view these widgets on Store Profile. What can be the reason?");?></a>
					<div class='faq' style='display: none;' id='faq_27'>
						<?php echo $this->translate('Ans: These widgets appear in the Info tab on Store Profile and will only be visible to Store admins. This widget is not shown to the Store admins if an Ad corresponding to that Store has been created. So, Please check this.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_55');"><?php echo $this->translate("I have installed the Community Ads plugin and then trying to create an Ad corresponding to a Store from the Store profile, then I am redirected to a Store saying that there are no Ad packages available although I am having packages in my Ads Plugin. What can be the reason ?");?></a>
					<div class='faq' style='display: none;' id='faq_55'>
						<?php echo $this->translate("Ans: This happens when you have not enabled 'Store' for any of the Ad Packages available on your site. You can do so by going to the 'Manage Packages' section of Community Ads Plugin and then editing the Package which you want to be available while creating the Ads corresponding to the Stores. Now, for the field 'Content Advertised in this Package', select the 'Store' option too in the multi-select box there.<br />In this way, that package would then be available when you will try to create an Ad corresponding to any Store from the Store Profile.");?>
					</div>
				</li>
				<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_30');"><?php echo $this->translate("I am not able to find the Suggest to Friends feature and the Stores Recommendations widget in this plugin. What can be the reason?");?></a>
				<div class='faq' style='display: none;' id='faq_30'>
					<?php echo $this->translate('These features are dependent on the %1$sSuggestions / Recommendations / People you may know & Inviter%2$s plugin and require that to be installed.', '<a href="http://www.socialengineaddons.com/socialengine-suggestions-recommendations-plugin" target="_blank">', '</a>');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_31');"><?php echo $this->translate("I want to improve the Likes functionality of this plugin. How can that be done?");?></a>
				<div class='faq' style='display: none;' id='faq_31'>
					<?php echo $this->translate('Ans: You may install the %1$sLikes Plugin and Widgets%2$s on your site to incorporate its integration with this plugin.', '<a href="http://www.socialengineaddons.com/socialengine-likes-plugin-and-widgets" target="_blank">', '</a>');?>
				</div>
			</li>
<!--			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_32');"><?php //echo $this->translate("I want to enhance the Stores on my site and provide more features to my users. How can I do it?");?></a>
				<div class='faq' style='display: none;' id='faq_32'>
					<?php //echo $this->translate('Ans: There are various apps / extensions available for the "Stores / Marketplace Plugin" which can enhance the Stores on your site, by adding valuable functionalities to them. To view the list of available extensions, please visit: %s.', '<a href="http://www.socialengineaddons.com/catalog/directory-stores-extensions" target="_blank">http://www.socialengineaddons.com/catalog/directory-stores-extensions</a>');?>
				</div>
			</li>-->
			</ul>
		</div>
	<?php break; ?>
<?php endswitch; ?>
