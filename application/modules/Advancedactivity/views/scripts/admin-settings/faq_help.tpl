<?php
 /**
* SocialEngine
*
* @category   Application_Extensions
* @package    Advancedactivity
* @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: faq_help.tpl 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
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
<div class="admin_seaocore_files_wrapper">
	<ul class="admin_seaocore_files seaocore_faq">	
   
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("Q: I have placed Advanced Activity Feeds widget on Content Profile / View Pages and enabled Welcome, Facebook and Twitter tabs there, but only the site activity feeds are getting displayed and no tabs are coming. What could be the reason ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_1'>
        <?php echo $this->translate("Ans: The Welcome, Facebook and Twitter tabs will not be shown on the content profile / view pages even if they are enabled for the widget location. On these pages, only the feeds for respective content profile will be shows."); ?> 
        </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("Q: I have placed all the widgets in the Welcome tab page but Welcome tab is not being shown in the Advanced Activity Feeds widget. What might be the reason ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_2'>
        <?php echo $this->translate('Ans: The Welcome tab will not be shown in the Advanced Activity Feeds widget for a user if none of the conditions configured by you for the blocks in it are being satisfied. You may edit these conditions from the "Welcome Settings" section of this plugin.'); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("Q: The CSS of this plugin is not coming on my site. What should I do ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_3'>
        <?php echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("Q: I am performing lots of actions on my site, but the activity feeds of those actions are not shown in the 'all updates' section of the Advanced Activity Feeds. What might be the reason?"); ?></a>
      <div class='faq' style='display: none;' id='faq_4'>
        <?php echo $this->translate("Ans: To show activity feeds of all the actions performed by you, please go to the 'Activity Feeds Settings' section of this plugin and find the field 'Item Limit Per User'. Now, enter the value for number of feeds per user you want to be displayed in the 'all updates' section. (Note : To have a nice mix of feeds from various users on your site, it is recommended to put a value less than 10.)"); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("Q: While updating status on my site, I selected the option to publish the status updates on twitter also, but my updates are not shown in my twitter timeline. Why is it happening?"); ?></a>
      <div class='faq' style='display: none;' id='faq_5'>
        <?php echo $this->translate("Ans: This is happening because you might have not given the 'Read and Write' permission while creating your application on twitter. To give the permission now, please go to <a href='https://dev.twitter.com/apps' target='_blank'> 'https://dev.twitter.com/apps/' </a> and select your application. Now, search for the field 'Application Type' in the settings section of your application. Selecting 'Read and write' value for this field will enable you to publish your status updates on twitter from your site."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("Q: I have selected the 'feed content' privacy on my site to 'friends only' but some of my feeds are still shown to the members who are not my friends when they visit my profile page. What might be the reason?"); ?></a>
      <div class='faq' style='display: none;' id='faq_6'>
        <?php echo $this->translate("Ans: This is happening because while updating the status you might have chosen the privacy to 'Everyone' because of which feeds posted with privacy 'Everyone' will be visible to all the users when they visit your profile."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate('Q: I want to enable / disable the "Scroll to Top" button for the Advanced Activity Feeds widget. What should I do?'); ?></a>
      <div class='faq' style='display: none;' id='faq_7'>
        <?php echo $this->translate("Ans: To do so, please go to the Layout Editor and click on the 'edit' link of the 'Advanced Activity Feeds' widget for the location where you want to enable / disable the 'Scroll to Top' button. Now, from the settings form popup of this widget, enable / disable the 'Scroll to Top Button' setting as per your requirement."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate('Q: I have enabled "Welcome" tab in the "Advanced Activity Feeds" widget placed on the Member Home Page on my site, but this widget is not displayed when I view my site in mobile. Why is this happening?'); ?></a>
      <div class='faq' style='display: none;' id='faq_8'>
        <?php echo $this->translate("Ans: This is happening so because welcome tab will not be displayed when your site is viewed in mobile."); ?>
      </div>
    </li>
    <li>
      <a onClick="faq_show('faq_9');">
       Q: I have created some custom lists for filtering activity feeds on my Wall, but I can not edit them. How can edit them?
      </a>
      <div class='faq' style='display: none;' id='faq_9'>
        You can edit custom lists by clicking on 'Pencil' icon placed in front of listing. But if your custom lists in not coming in the "More" tab, then the option to edit the custom list will not be shown.
        <br>
To place the custom list in the More tab, you can set number of default items to be shown in the wall by using the "Default Visible Items" setting available in the "Manage Lists" >> "General" section of this plugin such that custom lists created on your site come in the "More" tab.
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo $this->translate("Q. Facebook Feeds are not getting display on my website in Facebook Tab. What might be the reason?");?></a>
			<div class='faq' style='display: none;' id='faq_10'>
				<?php echo $this->translate("It is happening so because, Facebook has restricted the feature of displaying Facebook Feeds on other social sites. Please <a href='http://www.socialengineaddons.com/page/facebook-application-submission' target='_blank' >click here</a> to read more details.");?>
			</div>
    </li>
<!--    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_11');"><?php echo $this->translate("Q. I am getting \"Redirect URI does not match registered redirect URI\" error while logging into my instagram account?");?></a>
			<div class='faq' style='display: none;' id='faq_11'>
				<?php echo $this->translate("Ans: You might have configured wrong \"REDIRECT URI\" for your Instagram App. Please follow the below steps: </br> 1) Please go to the below URL and edit the Instagram App created by you:</br> <a href='https://instagram.com/developer/clients/manage/' target='_blank' >https://instagram.com/developer/clients/manage/</a> </br> 2) You need to re-enter the \"REDIRECT URI\" details here.</br> 3) Now, please go to \"SocialEngineAddOns Core Plugin\" >> \"Invite Services\" >> \"Instagram Help\", available in the admin panel. </br> 4) Now open 'Step 3' and copy the available \"OAuth redirect_uri\" from here and paste it in the Instagram App created by you (opened by you in step 1 and 2).</br> You can now login to you instagram account.");?>
			</div>
    </li>-->
   
	</ul>
</div>