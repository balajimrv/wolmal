<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.php 2012-31-12 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
    function faq_show(id) {
        if ($(id)) {
            if ($(id).style.display == 'block') {
                $(id).style.display = 'none';
            } else {
                $(id).style.display = 'block';
            }
        }
    }
<?php if ($this->faq_id): ?>
        window.addEvent('domready', function() {
            faq_show('<?php echo $this->faq_id; ?>');
        });
<?php endif; ?>
</script>
<?php $i = 1; ?>
<div class="admin_seaocore_files_wrapper">
    <ul class="admin_seaocore_files seaocore_faq">
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate("What is the role of Mobile / Tablet Menu Editor?"); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate('You can manage the various navigation menus that appear in the mobile / tablet view of your community. When you select the menu you wish to edit, a list of the menu items it contains will be shown. You can drag these items up and down to change their order. You can also add a separator to visually categorize the "Dashboard / Panel Navigation Menu" items.'); ?>
                </div>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate("I already have some other Mobile Plugin on my website, but now I want to use the “Mobile / Tablet Plugin”. What should I do?"); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate('If you already have any other Mobile Plugin (like the official SE Mobile Plugin) and want to use our "Mobile / Tablet Plugin", then you can easily do this by disabling that plugin and installing our "Mobile / Tablet Plugin".'); ?>
                </div>
            </div>
        </li>	
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate("I want to add menu items and separators in my Dashboard. How can I do this?"); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate('To add a new menu item, please follow the steps below:'); ?>
                </div>
                <p>
                    <b><?php echo $this->translate("Step 1:") ?></b>
                </p>
                <div class="code">
                    <?php echo $this->translate('Go to the "Mobile / Tablet Menu Editor" section of this plugin.'); ?>
                </div><br />
                <p>
                    <b><?php echo $this->translate("Step 2:") ?></b>
                </p>
                <div class="code">
                    <?php echo $this->translate('Select the  "Dashboard / Panel Navigation Menu" option from the drop-down select box.'); ?>
                </div><br />
                <p>
                    <b><?php echo $this->translate("Step 3:") ?></b>
                </p>
                <div class="code">
                    <?php echo $this->translate('Click on "Add Item" and fill the details in the popup to add a new menu item.'); ?>
                </div><br />
                <p>
                    <b><?php echo $this->translate("Step 4:") ?></b>
                </p>
                <div class="code">
                    <?php echo $this->translate('You can add an icon for the newly created item.'); ?>
                </div><br />
                <div class="code"><?php echo $this->translate('To add a new separator, please follow the steps below:'); ?></div>
                <p>
                    <b><?php echo $this->translate("Step 1:") ?></b>
                </p>
                <div class="code">
                    <?php echo $this->translate('Go to the “Mobile / Tablet Menu Editor” section of this plugin.'); ?>
                </div><br />
                <p>
                    <b><?php echo $this->translate("Step 2:") ?></b>
                </p>
                <div class="code">
                    <?php echo $this->translate('Select the  "Dashboard / Panel Navigation Menu" option from the drop-down select box.'); ?>
                </div><br />
                <p>
                    <b><?php echo $this->translate("Step 3:") ?></b>
                </p>
                <div class="code">
                    <?php echo $this->translate('Click on "Add Separator" and fill the details in the popup to add a new separator.'); ?>
                </div><br />
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate("How many display types can I use for the Tab Containers in my mobile site?"); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate('This plugin provides 4 attractive display types for the tab containers in your mobile site:'); ?><br />
                    <?php echo $this->translate('1) Tab Collapsible View'); ?><br />
                    <?php echo $this->translate('2) Horizontal Tab View'); ?><br />
                    <?php echo $this->translate('3) Horizontal Tab with Icon View'); ?><br />
                    <?php echo $this->translate('4) Tab Panel View'); ?>
                </div>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate("How many Dashboard View Types can I set in my mobile site and how can I change the dashboard view of my mobile site?"); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate("There are 6 views for the Dashboard of your mobile site:"); ?><br />
                    <?php echo $this->translate('1) Board List View (The board comes with a fade-out effect in list view.)'); ?><br />
                    <?php echo $this->translate('2) Board Grid View (The board comes with a fade-out effect in grid view.)'); ?><br />
                    <?php echo $this->translate('3) Panel Overlay List View (The panel comes in the view with a sliding effect, over the page content in list view.)'); ?><br />
                    <?php echo $this->translate('4) Panel Overlay Icon View (The panel comes in the view with a sliding effect, over the page content in icon view.)'); ?><br />
                    <?php echo $this->translate('5) Panel Reveal List View (The panel comes in the view with a sliding effect, and the page content slides out in list view.)'); ?><br />
                    <?php echo $this->translate('6) Panel Reveal Icon View (The panel comes in the view with a sliding effect, and the page content slides out in icon view.)'); ?><br /><br />
                    <?php echo $this->translate('You can change the dashboard view of your site by using the “Dashboard View Type” setting by visiting the Global Settings >> Mobile Settings section of this plugin.'); ?>
                </div>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate("I want to create a new theme for my mobile site. How can I do this?"); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate('To create a new theme for your mobile site, you can refer to the "Guidelines for creating a new theme" mentioned in the "Mobile / Tablet Theme Editor" section of this plugin.'); ?><br/>
                    <?php echo $this->translate('Note: We recommend you to create new theme to configure the look and feel of your mobile site because if you do changes in the default themes provided with this plugin, then the changes made by you will be overwritten after the plugin upgrade.'); ?>
                </div>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate("I want to customize the theme of my mobile site. Can you help me?"); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate('Yes, please %1$s with your customization requirements by selecting the "Mobile / Tablet Theme Customization" option from the "Category" field in the contact form. Our Theme development team will get back to you for the same.', "<a href='http://www.socialengineaddons.com/contact-us' target='_blank'>contact us</a>"); ?>
                </div>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate("How can I set the startup image for my mobile site?"); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate('To do so: please follow the steps below:'); ?><br/>
                </div>
                <p>
                    <b><?php echo $this->translate("Step 1:") ?></b>
                </p>
                <div class="code">
                    <?php echo $this->translate('Add a Startup Image from the "Layout" >> "File & Media Manager".'); ?>
                </div><br />

                <p>
                    <b><?php echo $this->translate("Step 2:") ?></b>
                </p>
                <div class="code">
                    <?php echo $this->translate('Go to the Mobile / Tablet Layout Editor.'); ?>
                </div><br />

                <p>
                    <b><?php echo $this->translate("Step 3:") ?></b>
                </p>
                <div class="code">
                    <?php echo $this->translate('Select the Startup Page from the “Editing” section.'); ?>         


                </div><br />

                <p>
                    <b><?php echo $this->translate("Step 4:") ?></b>
                </p>
                <div class="code">
                    <?php echo $this->translate('Edit the Startup Image widget and select and startup image from the drop-down select-box.'); ?>
                    <p style="margin-left:5em" >    
                </div><br />

                <p>
                    <b><?php echo $this->translate("Step 5:") ?></b>
                </p>
                <div class="code">
                    <?php echo $this->translate('Click on “Save Changes” button to save the changes in the widget.'); ?>
                </div><br />
                <p>
                    <b><?php echo $this->translate("Step 6:") ?></b>
                </p>
                <div class="code">
                    <?php echo $this->translate('Click on “Save Changes” link to save the changes in the page.'); ?>
                </div><br />

                <div class="code"><?php echo $this->translate('This startup image will appear during start-up, before loading of your site.'); ?></div>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate("I want my users to check-in via their mobiles into various contents on my site and other places. How can I allow them to do so?"); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate('You can enable your users to check-in into various contents on your site and other places by using our "%1$s". To see details, please %2$s.', "<a href='http://www.socialengineaddons.com/socialengine-geo-location-geo-tagging-checkins-proximity-search-plugin' target='_blank'>Geo-Location, Geo-Tagging, Check-Ins & Proximity Search Plugin</a>", "<a href='http://www.socialengineaddons.com/socialengine-geo-location-geo-tagging-checkins-proximity-search-plugin' target='_blank'>visit here</a>"); ?>
                </div>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('The CSS of this plugin is not coming on my site. What should I do?'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate("Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'."); ?>
                </div>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('I have done some changes from the Mobile / Tablet Layout Editor section of this widget, but my changes are not reflecting in when I view my site in Tablet. What might be the reason?'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate("This might be happening because you may have done changes only in the Mobile Layout Editor. You need to do same changes in the Tablet Layout Editor also to reflect those chages when you view your site in tablet."); ?>
                </div>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('What is Home Screen Icon?'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate("Home screen icon is a glossy, round cornered, square shaped icon, same as standard games and apps icons in mobiles / tablets. User can add this icon to their smartphone's home screens. Using this icon users need just one click to open your site from their mobiles.") ?><br />
                    <?php echo $this->translate("Admin can manage home screen icon from 'Device Based Settings'."); ?><br />
                    <?php echo $this->translate("Note: In This plugin, Home screen icon is supported only for iphones and ipad devices.
"); ?>
                </div>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('What is Splash / Startup  Screen? '); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate("Splash screen is an image that appears while program is loading. Splash screens cover the entire screen or simply a rectangle near the center of the screen.") ?><br />
                    <?php echo $this->translate("Admin can manage splash screen from 'Device Based Settings'."); ?><br/>
                    <?php echo $this->translate("Note: In This plugin, Splash screen is supported only for iphones and ipad devices. Also Splash screen image size should be less than 30kb that easily loaded."); ?>
                </div>
            </div>
        </li>
    </ul>
</div>