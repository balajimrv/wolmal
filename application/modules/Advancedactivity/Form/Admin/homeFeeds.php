<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedactivity
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: homeFeeds.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedactivity_Form_Admin_homeFeeds extends Engine_Form {

    public function init() {
        $this->loadDefaultDecorators();
        $this->addElement('Text', 'title', array(
            'label' => 'Title',
            'value' => 'What\'s New',
        ));

        $faqlink = "http://www.socialengineaddons.com/page/facebook-application-submission";

        $this->addElement('MultiCheckbox', 'advancedactivity_tabs', array(
            'description' => 'Select the tabs that you want to be available in this block. <br> [Note 1: Facebook has restricted the feature of displaying Facebook Feeds on other social sites. Please <a href="' . $faqlink . '" target="_blank">click here</a> to read more details.]<br> [Note 2: LinkedIn has restricted the feature of displaying LinkedIn Feeds on other social sites.]<br>[Note 3: The Welcome, Twitter tabs will only work for Member Home Page. Twitter tabs will show the logged-in user\'s  Twitter and Linkedin feeds. It is recommended to place this widget where SocialEngine\'s Core Activity Feed widget is placed.]',
            'multiOptions' => array(
                "welcome" => "Welcome",
                "aaffeed" => "Site Activity Feeds",
                "twitter" => "Twitter Feeds",
//                "instagram" => "Instagram Feeds"
            ),
        ));
        $this->addElement('Radio', 'showPosts', array(
            'label' => 'Show Post',
            'description' => "Do you want to display “post something, module filters and other options?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1,
        ));
        
        if(Engine_Api::_()->hasModuleBootstrap('sitealbum') || Engine_Api::_()->hasModuleBootstrap('sitevideo')) {
            $this->addElement('Radio', 'showTabs', array(
                'label' => 'Show Tabs',
                'description' => "Do you want to show various tabs like Add Photo, Create Photo Album and Add Videos in this widget? [Note: This setting only work if you have placed this widget on the Member Home Page and Member Profile Page.]",
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => 0,
            ));
        }
        $this->advancedactivity_tabs->getDecorator('description')->setOptions(array('escape' => false, 'placement' => 'prepend'));
        $this->addElement('Radio', 'loadByAjax', array(
            'label' => 'AJAX Based Feed Loading On This Pages',
            'description' => "Do you want the feeds on this pages of members and various content to be loaded via AJAX after page load (this will be good for the overall web page loading speed)?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 0,
        ));

        
        
        $this->addElement('Radio', 'showScrollTopButton', array(
            'label' => 'Scroll to Top Button',
            'description' => 'Do you want the "Scroll to Top" button to be displayed for this Activity Feeds block? (As a user scrolls down to see more Activity Feeds from this widget, the "Scroll to Top" button will be shown in the bottom-right side of the screen, enabling user to easily move to the top.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 0,
        ));

        $this->addElement('Text', 'videowidth', array(
            'label' => 'Enter the width (in pixels) of video attachment block:',
            'value' => 0,
        ));
        $this->addElement('Text', 'widthphotoattachment', array(
            'label' => 'Enter the width (in pixels) of photo attachment block:',
            'value' => 440,
        ));

        $this->addElement('Text', 'width1', array(
            'label' => 'Enter the width (in pixels) of photo you want to display if 1 photo will be shown ' . '<a href="http://demo.socialengineaddons.com/public/admin/Adv_Activity_1.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>',
            'value' => 410,
        ));
        $this->width1->getDecorator('Label')->setOptions(array('escape' => false));

        $this->addElement('Text', 'width2', array(
            'label' => 'Enter the width (in pixels) of photo you want to display if 2 photos will be shown ' . '<a href="http://demo.socialengineaddons.com/public/admin/Adv_Activity_2.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>',
            'value' => 410,
        ));
        $this->width2->getDecorator('Label')->setOptions(array('escape' => false));

        $this->addElement('Text', 'height2', array(
            'label' => 'Enter the height (in pixels) of photo you want to display if 2 photos will be shown ' . '<a href="http://demo.socialengineaddons.com/public/admin/Adv_Activity_2.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>',
            'value' => 250,
        ));
        $this->height2->getDecorator('Label')->setOptions(array('escape' => false));

        $this->addElement('Text', 'width3big', array(
            'label' => 'Enter the width (in pixels) of big photo you want to display if 3 photos will be shown ' . '<a href="http://demo.socialengineaddons.com/public/admin/Adv_Activity_3.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>',
            'value' => 410,
        ));
        $this->width3big->getDecorator('Label')->setOptions(array('escape' => false));

        $this->addElement('Text', 'height3big', array(
            'label' => 'Enter the height (in pixels) of big photo you want to display if 3 photos will be shown ' . '<a href="http://demo.socialengineaddons.com/public/admin/Adv_Activity_2.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>',
            'value' => 250,
        ));
        $this->height3big->getDecorator('Label')->setOptions(array('escape' => false));

        $this->addElement('Text', 'width3small', array(
            'label' => 'Enter the width (in pixels) of small photos you want to display if 3 photos will be shown ' . '<a href="http://demo.socialengineaddons.com/public/admin/Adv_Activity_3.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>',
            'value' => 200,
        ));
        $this->width3small->getDecorator('Label')->setOptions(array('escape' => false));

        $this->addElement('Text', 'height3small', array(
            'label' => 'Enter the height (in pixels) of small photos you want to display if 3 photos will be shown ' . '<a href="http://demo.socialengineaddons.com/public/admin/Adv_Activity_3.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>',
            'value' => 150,
        ));
        $this->height3small->getDecorator('Label')->setOptions(array('escape' => false));

        $this->addElement('Text', 'width4big', array(
            'label' => 'Enter the width (in pixels) of big photo you want to display if 4 photos will be shown ' . '<a href="http://demo.socialengineaddons.com/public/admin/Adv_Activity_4.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>',
            'value' => 410,
        ));
        $this->width4big->getDecorator('Label')->setOptions(array('escape' => false));

        $this->addElement('Text', 'height4big', array(
            'label' => 'Enter the height (in pixels) of big photo you want to display if 4 photos will be shown ' . '<a href="http://demo.socialengineaddons.com/public/admin/Adv_Activity_4.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>',
            'value' => 250,
        ));
        $this->height4big->getDecorator('Label')->setOptions(array('escape' => false));

        $this->addElement('Text', 'width4small', array(
            'label' => 'Enter the width (in pixels) of small photos you want to display if 4 photos will be shown ' . '<a href="http://demo.socialengineaddons.com/public/admin/Adv_Activity_4.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>',
            'value' => 130,
        ));
        $this->width4small->getDecorator('Label')->setOptions(array('escape' => false));

        $this->addElement('Text', 'height4small', array(
            'label' => 'Enter the height (in pixels) of small photos you want to display if 4 photos will be shown ' . '<a href="http://demo.socialengineaddons.com/public/admin/Adv_Activity_4.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>',
            'value' => 100,
        ));
        $this->height4small->getDecorator('Label')->setOptions(array('escape' => false));

        $this->addElement('Text', 'width5big', array(
            'label' => 'Enter the width (in pixels) of big photo you want to display if 5 photos will be shown ' . '<a href="http://demo.socialengineaddons.com/public/admin/Adv_Activity_5.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>',
            'value' => 200,
        ));
        $this->width5big->getDecorator('Label')->setOptions(array('escape' => false));

        $this->addElement('Text', 'height5big', array(
            'label' => 'Enter the height (in pixels) of big photo you want to display if 5 photos will be shown ' . '<a href="http://demo.socialengineaddons.com/public/admin/Adv_Activity_5.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>',
            'value' => 150,
        ));
        $this->height5big->getDecorator('Label')->setOptions(array('escape' => false));

        $this->addElement('Text', 'width5small', array(
            'label' => 'Enter the width (in pixels) of small photos you want to display if 5 photos will be shown ' . '<a href="http://demo.socialengineaddons.com/public/admin/Adv_Activity_5.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>',
            'value' => 130,
        ));
        $this->width5small->getDecorator('Label')->setOptions(array('escape' => false));

        $this->addElement('Text', 'height5small', array(
            'label' => 'Enter the height (in pixels) of small photos you want to display if 5 photos will be shown ' . '<a href="http://demo.socialengineaddons.com/public/admin/Adv_Activity_5.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>',
            'value' => 100,
        ));
        $this->height5small->getDecorator('Label')->setOptions(array('escape' => false));
        
        //WE HAVE COMMENTED THESE SETTING WE ARE USING THE +PHOTO COUNT 
//        $this->addElement('Text', 'width6', array(
//            'label' => "Enter the width (in pixels) of photo you want to display if 6 photos will be shown " . '<a href="http://demo.socialengineaddons.com/public/admin/Adv_Activity_6.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>',
//            'value' => 200,
//        ));
//        $this->width6->getDecorator('Label')->setOptions(array('escape' => false));
//
//        $this->addElement('Text', 'height6', array(
//            'label' => "Enter the height (in pixels) of photo you want to display if 6 photos will be shown " . '<a href="http://demo.socialengineaddons.com/public/admin/Adv_Activity_6.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>',
//            'value' => 150,
//        ));
//        $this->height6->getDecorator('Label')->setOptions(array('escape' => false));
//
//        $this->addElement('Text', 'width78', array(
//            'label' => "Enter the width (in pixels) of photo you want to display if 7 or 8 photos will be shown " . '<a href="http://demo.socialengineaddons.com/public/admin/Adv_Activity_7.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>',
//            'value' => 100,
//        ));
//        $this->width78->getDecorator('Label')->setOptions(array('escape' => false));
//
//        $this->addElement('Text', 'height78', array(
//            'label' => "Enter the height (in pixels) of photo you want to display if 7 or 8 photos will be shown " . '<a href="http://demo.socialengineaddons.com/public/admin/Adv_Activity_7.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>',
//            'value' => 90,
//        ));
//        $this->height78->getDecorator('Label')->setOptions(array('escape' => false));
    }

}
