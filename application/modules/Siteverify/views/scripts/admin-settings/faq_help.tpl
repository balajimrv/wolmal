<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteverify
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq.tpl 2014-09-11 00:00:00 SocialEngineAddOns $
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
      <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("Who is a Verified Member?"); ?></a>
      <div class='faq' style='display: none;' id='faq_1'>
<?php echo $this->translate("Ans: Verified members are the authorized members of the site who are verified by the site members or by the site admin."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("Is Verification a requirement?"); ?></a>
      <div class='faq' style='display: none;' id='faq_2'>
<?php echo $this->translate('Ans: No, but it will enhance your reputation as a site member and helps you to appear as authenticated user.'); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate('How can a member become a verified member?'); ?></a>
      <div class='faq' style='display: none;' id='faq_3'>
<?php echo $this->translate("Ans: Member can become a verified member by below methods:<br />
    a. Member Driven Verification: Site members will be verified by the other site members using “Verify Button” placed on their “Member Profile Page”.<br />
b. Admin Driven Verification:<br />&nbsp;&nbsp;i.Site members need to send verification request to admin by verifying the other site member using “Verify Button” placed on their 'Member Profile Page'.<br />&nbsp;&nbsp;ii. A mail and a notification will be sent to the admin whenever a site member will request for verification.<br />&nbsp;&nbsp;iii. Now, admin needs to approve verification request from the “Manage Verifications” tab in the admin panel to approve site member’s verification. [Site member will become verified member only if he has achieved “Verify Limit”.]"); ?>

      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("I want only super admin to verify the default members. How can I do this?"); ?></a>
      <div class='faq' style='display: none;' id='faq_4'>
<?php echo $this->translate('Ans: Please go to the Member level Settings of this plugin. Now select the "Default Level" from the "Member Level" dropdown. Enable the "Allow Verification of Members?" setting and choose the member level you want to allow for verification of this member level from the given check boxes.'); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("I want verify icon wherever verified site members get display on my site. Is it possible with this plugin?"); ?></a>
      <div class='faq' style='display: none;' id='faq_5'>
<?php echo $this->translate("Ans: Yes, Integration with our <a href='http://www.socialengineaddons.com/socialengine-advanced-members-plugin-better-browse-search-user-reviews-ratings-location' target='_blank'>Advanced Members Plugin - Better Browse & Search, User Reviews, Ratings & Location</a> enhances the usability of this plugin. Members can be displayed as verified user and can be showcased with verify icon in various member driven widgets. So If you want your verified users to be displayed in various member widgets then you can purchase our <a href='http://www.socialengineaddons.com/socialengine-advanced-members-plugin-better-browse-search-user-reviews-ratings-location' target='_blank'>Advanced Members Plugin - Better Browse & Search, User Reviews, Ratings & Location</a>."); ?>
      </div>
    </li>

        
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("I am unable to see verify icon in info tooltip. What might be the reason?"); ?></a>
      <div class='faq' style='display: none;' id='faq_6'>
<?php echo $this->translate("Ans: It seems that you have not enabled verify icon from info tooltip settings. To do so please go to the “SocialEngineAddons Core Plugin” >> “Info Tooltip Settings” from the admin panel. Now enable '<b>Verify Icon</b>' from the given check boxes.<br> You will now be able to see verify icon in the info tooltip."); ?>
      </div>
    </li>
    
    
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("I have verified a member, but after 2 days I realized that I have verified a wrong person. Can I cancel my verification for that member now?"); ?></a>
      <div class='faq' style='display: none;' id='faq_7'>
<?php echo $this->translate("Ans: Yes, to do so please go to the profile page of that member, use “Cancel Verification” option to cancel your verification for that user."); ?>
      </div>
    </li>

    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_8'>
<?php echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'."); ?>
      </div>
    </li>

  </ul>
</div>