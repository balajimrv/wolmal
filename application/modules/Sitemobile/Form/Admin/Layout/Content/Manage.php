<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Manage.php 6590 2013-05-07 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Form_Admin_Layout_Content_Manage extends Engine_Form {

  public function init() {

    $this
            ->setTitle('Manage Widgets')
            ->setDescription('These settings will affect complete layout settings of your community. This is the easiest way to manage all widgets. ');

    $collapsible = 'Tab Collapsible view ' . '<a class="buttonlink sm_icon_view mleft5" href="https://lh3.googleusercontent.com/-oFptDwG-5kg/UbWyOKoVLfI/AAAAAAAAAVM/JrKf-PoDc_c/s512/Mobile-Tabs-Collapsible-view.jpg" target="_blank" title="View Screen Shot"></a>';
    $horizontal = 'Horizontal view ' . '<a class="buttonlink sm_icon_view mleft5" href="https://lh4.googleusercontent.com/-MSAv6_J30lM/UbWyPK_yD4I/AAAAAAAAAVg/x7axXCGe3bI/s512/Mobile-Tabs-Horizontal-view.jpg" target="_blank" title="View Screen Shot"></a>';
    $horizontal_icon = 'Horizontal icon view ' . '<a class="buttonlink sm_icon_view mleft5" href="https://lh6.googleusercontent.com/-eC2Xb0GbH00/UbWyO1rpJ1I/AAAAAAAAAVY/9NcfM7mwg08/s512/Mobile-Tabs-Horizontal-icon-view.jpg" target="_blank" title="View Screen Shot"></a>';
    $panel = 'Panel view ' . '<a class="buttonlink sm_icon_view mleft5" href="https://lh3.googleusercontent.com/-1hEvBbj3hE0/UbWyPw6KGOI/AAAAAAAAAVs/2uzDWQYxB1A/s512/Mobile-Tabs-Panel-view.jpg" target="_blank" title="View Screen Shot"></a>';

    $this->addElement('radio', 'layoutContainer', array(
        'label' => 'Tab Container',
        'description' => "Select the view for tab container.",
        'multiOptions' => array(
            'tab' => $collapsible,
            'horizontal' => $horizontal,
            'horizontal_icon' => $horizontal_icon,
            'panel' => $panel,
        ),
        'escape' => false,
        'value' => '',
    ));

    if (false) {
      $header = "Header " . '<a class="buttonlink sm_icon_view mleft5" href="http://devaddons.socialengineaddons.com/mobile_plugin_screen_shot/panel_list.png" target="_blank" title="View Screen Shot"></a>';
      $panel_dashboard = "Panel / Dashboard " . '<a class="buttonlink sm_icon_view mleft5" href="http://devaddons.socialengineaddons.com/mobile_plugin_screen_shot/panel_icon.png" target="_blank" title="View Screen Shot"></a>';
      $footer = "Footer " . '<a class="buttonlink sm_icon_view mleft5" href="http://devaddons.socialengineaddons.com/mobile_plugin_screen_shot/panel_icon.png" target="_blank" title="View Screen Shot"></a>';
      $this->addElement('radio', 'sitemobile_notification_position', array(
          'label' => 'Notification Position Settings',
          'description' => 'Please select the position of notification.',
          'multiOptions' => array(
              'header' => $header,
              'dashboard_panel' => $panel_dashboard,
              'footer' => $footer,
          ),
          'escape' => false,
          'value' => ''
      ));

      $header = "Header " . '<a class="buttonlink sm_icon_view mleft5" href="http://devaddons.socialengineaddons.com/mobile_plugin_screen_shot/panel_list.png" target="_blank" title="View Screen Shot"></a>';
      $content = "Content " . '<a class="buttonlink sm_icon_view mleft5" href="http://devaddons.socialengineaddons.com/mobile_plugin_screen_shot/panel_icon.png" target="_blank" title="View Screen Shot"></a>';
      $footer = "Footer " . '<a class="buttonlink sm_icon_view mleft5" href="http://devaddons.socialengineaddons.com/mobile_plugin_screen_shot/panel_icon.png" target="_blank" title="View Screen Shot"></a>';
      $this->addElement('radio', 'sitemobile_navigation_position', array(
          'label' => 'Navigation Menu Settings',
          'description' => 'Please select the position of navigation menu.',
          'multiOptions' => array(
              'header' => $header,
              'content' => $content,
              'footer' => $footer,
          ),
          'escape' => false,
          'value' => ''
      ));
    }

    $search = "Only Search Text field " . '<a class="buttonlink sm_icon_view mleft5" href="https://lh3.googleusercontent.com/-1jmmGUhQTmk/UbWyNiSzKEI/AAAAAAAAAVE/jmxHbt1HHsE/s512/Mobile-Search-Only-Search-Text-field.jpg" target="_blank" title="View Screen Shot"></a>';
    $fullsearch = "Expanded Advanced Search " . '<a class="buttonlink sm_icon_view mleft5" href="https://lh4.googleusercontent.com/-xfLOTHgy6Oc/UbWyM4hz0gI/AAAAAAAAAU4/q6GGBcBFt3A/s512/Mobile-Search-Expanded%2520Advanced%2520Search.jpg" target="_blank" title="View Screen Shot"></a>';
    $advsearch = "Search Text field with expandable Advanced Search options " . '<a class="buttonlink sm_icon_view mleft5" href="https://lh6.googleusercontent.com/-SUC6Mq6CuCY/UbWyOHBMGjI/AAAAAAAAAVU/giek-JXwi34/s512/Mobile-Search-Search-Text-field-with-expandable-Advanced-Search-options.jpg" target="_blank" title="View Screen Shot"></a>';
    $this->addElement('radio', 'sitemobile_search_type', array(
        'label' => 'Search',
        'description' => 'Select the display type for Search.',
        'multiOptions' => array(
            '1' => $search,
            '3' => $fullsearch,
            '2' => $advsearch,
        ),
        'escape' => false,
        'value' => ''
    ));

    if (false) {
      $panel_dashboard = "Panel / Dashboard " . '<a class="buttonlink sm_icon_view" href="http://devaddons.socialengineaddons.com/mobile_plugin_screen_shot/panel_list.png" target="_blank" title="View Screen Shot"></a>';
      $footer = "Footer " . '<a class="buttonlink sm_icon_view mleft5" href="http://devaddons.socialengineaddons.com/mobile_plugin_screen_shot/panel_icon.png" target="_blank" title="View Screen Shot"></a>';
      $this->addElement('radio', 'sitemobile_footer_widget', array(
          'label' => 'Footer Widget Settings',
          'description' => 'Please select the position of footer widget.',
          'multiOptions' => array(
              'dashboard_panel' => $panel_dashboard,
              'footer' => $footer,
          ),
          'escape' => false,
          'value' => ''
      ));
    }
    // Element: submit
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
        'prependText' => ' or ',
        'link' => true,
        'label' => 'cancel',
        'onclick' => 'history.go(-1); return false;',
        'decorators' => array(
            'ViewHelper'
        )
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }

}