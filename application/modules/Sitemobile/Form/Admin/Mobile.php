<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Mobile.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Form_Admin_Mobile extends Engine_Form {

  public function init() {

    $this
            ->setTitle('Mobile Settings')
            ->setDescription('A good mobile user experience requires a different design than the design of a full site on Desktop. Below, you can manage and configure various settings for the design of your site in mobile.');

    $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');

    $board_list = "Board List View (The board comes with a fade-out effect in list view.)
" . '<a href="https://lh3.googleusercontent.com/-wR0DyozGuJs/UbWyLjTDm8I/AAAAAAAAAUc/jQMALI0kh14/s512/Mobile-Board-List-View.jpg" title="View Screenshot" class="buttonlink sm_icon_view mleft5" target="_blank"></a>';

    $board_grid = "Board Grid View (The board comes with a fade-out effect in grid view.)
" . '<a href="https://lh3.googleusercontent.com/-CULlfhtegzU/UbWyL-pupqI/AAAAAAAAAUg/SET8Do62ufM/s512/Mobile-Board-View.jpg" title="View Screenshot" class="buttonlink sm_icon_view mleft5" target="_blank"></a>';

    $panel_overlay_list = "Panel Overlay List View (The panel comes in the view with a sliding effect, over the page content in list view.) " . '<a href="https://lh3.googleusercontent.com/-QGFxD0lCNUU/UbWyMvmDfTI/AAAAAAAAAUs/kEUHVmZgf1o/s512/Mobile-Panel-Overlay-List-View.jpg" target="_blank" title="View Screenshot" class="buttonlink sm_icon_view mleft5"></a>';

    $panel_overlay_icon = "Panel Overlay Icon View (The panel comes in the view with a sliding effect, over the page content in icon view.) " . '<a href="https://lh6.googleusercontent.com/-oSwujWvZELQ/UbWyLmnmE0I/AAAAAAAAAUY/rlncteRdeV8/s512/Mobile-Panel-Overlay-Icon.jpg" target="_blank" title="View Screenshot" class="buttonlink sm_icon_view mleft5"></a>';

    $panel_reveal_list = "Panel Reveal List View (The panel comes in the view with a sliding effect, and the page content slides out in list view.) " . '<a href="https://lh4.googleusercontent.com/-MqJ2zCyQSIw/UbWyM8Yf7uI/AAAAAAAAAU0/L-UlSJ2Rr00/s512/Mobile-Panel-Reveal-List-View.jpg" target="_blank" title="View Screenshot" class="buttonlink sm_icon_view mleft5"></a>';


    $panel_reveal_icon = "Panel Reveal Icon View (The panel comes in the view with a sliding effect, and the page content slides out in icon view.) " . '<a href="https://lh6.googleusercontent.com/-oSwujWvZELQ/UbWyLmnmE0I/AAAAAAAAAUY/rlncteRdeV8/s512/Mobile-Panel-Overlay-Icon.jpg" target="_blank" title="View Screenshot" class="buttonlink sm_icon_view mleft5"></a>';


    $this->addElement('radio', 'sitemobile_dashboard_contentType_mobile', array(
        'label' => 'Dashboard View Type',
        'description' => "Please select the type of view for the Mobile Dashboard (Main Menu) of your site.",
        'multiOptions' => array(
            'dashboard_list' => $board_list,
            'dashboard_grid' => $board_grid,
            'panel_overlay_list' => $panel_overlay_list,
            'panel_overlay_icon' => $panel_overlay_icon,
            'panel_reveal_list' => $panel_reveal_list,
            'panel_reveal_icon' => $panel_reveal_icon,
        ),
        'escape' => false,
        'value' => $coreSettingsApi->getSetting('sitemobile.dashboard.contentType.mobile', 'panel_reveal_list'),
    ));

    $this->addElement('radio', 'sitemobile_header_position_mobile', array(
        'label' => 'Top Floating Header',
        'description' => "Do you want your mobile site's Header to be top-floating?",
        'multiOptions' => array(
            "fixed" => "Yes",
            "false" => "No",
        ),
        'value' => $coreSettingsApi->getSetting('sitemobile.header.position.mobile', 'false'),
    ));

    $this->addElement('radio', 'sitemobile_footer_position_mobile', array(
        'label' => 'Bottom Floating Footer',
        'description' => 'Do you want your mobile site\'s Footer to be bottom-floating?',
        'multiOptions' => array(
            "fixed" => "Yes",
            "false" => "No",
        ),
        'value' => $coreSettingsApi->getSetting('sitemobile.footer.position.mobile', 'false'),
    ));

//    $this->addElement('radio', 'sitemobile_popup_view_mobile', array(
//        'label' => 'Drop-down Select-boxes View',
//        'description' => "Select the mobile view type for the drop-down select-boxes of your site.",
//        'multiOptions' => array(
//            'custom' => 'Attractive Customised view from this plugin ',
//            'mobile' => "Users' Mobiles' Default View",
//        ),
//        'escape' => false,
//        'value' => $coreSettingsApi->getSetting('sitemobile.popup.view.mobile', 'mobile')
//    ));

    $this->addElement('MultiCheckbox', 'sitemobile_lightbox_options_mobile', array(
        'label' => 'Photo Lightbox Viewer Options',
        'description' => "Choose the options that you want to be displayed in the Photo Lightbox Viewer below the photos.",
        'multiOptions' => array(
            'fullView' => 'Photo View Page',
            'comments' => 'Comments and Like',
            'tags' => 'Tags',
            'slideshow' => 'Slideshow'
        ),
        'value' => $coreSettingsApi->getSetting('sitemobile.lightbox.options.mobile', array('fullView', 'comments', 'tags', 'slideshow'))
    ));

    // Element: submit
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}