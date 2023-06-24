<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Adsettings.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Admin_Widgets_AjaxBasedRecentlyPosted extends Core_Form_Admin_Widget_Standard {

  public function init() {

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;    
    $url = $view->url(array(), "sitestore_general", true);

    $layouts_tabs_options = array("1" => "Recent", "2" => "Most Popular", "3" => "Random", "4" => "Featured", "5" => "Sponsored");
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
      $layouts_tabs_options["6"] = "Most Joined Stores";
    }
    
    $categories = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategories();
    if (count($categories) != 0) {
      $categories_prepared[0] = "";
      foreach ($categories as $category) {
        $categories_prepared[$category->category_id] = $category->category_name;
      }
    }

    $this->addElement('Text', 'titleLink', array(
            'label' => $view->translate('Enter Title Link'),
            'description' => 'If you do not want to show title link, then simply leave this field empty.',
            'value' => '<a href="'.$url.'">Explore Stores »</a>',
        )
    );
    
    $this->addElement('Radio', 'titleLinkPosition', array(
            'label' => 'Enter Title Link Position',
            'description' => 'Please select the position of the title link. Setting will work only if above setting "Enter Title Link" is not empty.',
            'multiOptions' => array("top" => "Top", "bottom" => "Bottom"),
            'value' => "bottom"
        )
    );
    
    $this->addElement('Text', 'photoHeight', array(
            'label' => 'Enter the height of image.'
        )
    );
    
    $this->addElement('Text', 'photoWidth', array(
            'label' => 'Enter the width of image.'
        )
    );

    $this->addElement('MultiCheckbox', 'layouts_views', array(
            'label' => 'Choose the view types that you want to be available for stores on the stores home and browse stores.',
            'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View"),
        )
    );
    
    $this->addElement('Radio', 'layouts_oder', array(
            'label' => 'Select a default view type for Stores.',
            'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View"),
        )
    );
    
    $this->addElement('Text', 'list_limit', array(
            'label' => 'List View (Limit)',
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        )
    );
    
    $this->addElement('Text', 'grid_limit', array(
            'label' => 'Grid View (Limit)',
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        )
    );
    
    $this->addElement('Text', 'columnWidth', array(
            'label' => 'Column Width For Grid View.',
            'value' => '188',
        )
    );
    
    $this->addElement('Text', 'columnHeight', array(
            'label' => 'Column Height For Grid View.',
            'value' => '350',
        )
    );
    
    $this->addElement('Text', 'turncation', array(
            'label' => 'Title Truncation Limit For Grid View.',
            'value' => '40',
        )
    );
    
    $this->addElement('Radio', 'showlikebutton', array(
            'label' => 'Do you want to show “Like Button” when users mouse over on Stores in grid view?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1,
        )
    );
    
    $this->addElement('Radio', 'showfeaturedLable', array(
            'label' => 'Do you want “Featured Label” for the Stores to be displayed in grid view?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1,
        )
    );
    
    $this->addElement('Radio', 'titlePosition', array(
            'label' => 'Do you want "Store Title" to be displayed in grid view?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1,
        )
    );
    
    $this->addElement('Radio', 'showsponsoredLable', array(
            'label' => 'Do you want "Sponsored Label"  for the Stores to be displayed in grid view?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1,
        )
    );
    
    $this->addElement('Radio', 'showlocation', array(
            'label' => 'Do you want “Location” of the Stores to be displayed in grid view?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1,
        )
    );
    
    $this->addElement('Radio', 'showprice', array(
            'label' => 'Do you want “Price” of the Stores to be displayed in grid view?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1,
        )
    );
    
    $this->addElement('Radio', 'showpostedBy', array(
            'label' => 'Do you want "Posted By" of the Stores to be displayed in grid view?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1,
        )
    );
    
    $this->addElement('Radio', 'showdate', array(
            'label' => 'Do you want "Creation Date" of the Stores to be displayed in grid view?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1,
        )
    );
    
    $this->addElement('MultiCheckbox', 'layouts_tabs', array(
            'label' => 'Choose the ajax tabs that you want to be there in the Main Stores Home Widget.',
            'multiOptions' => $layouts_tabs_options,
        )
    );
    
    $this->addElement('Select', 'category_id', array(
            'label' => 'Category',
            'multiOptions' => $categories_prepared,
        )
    );
    
    $this->addElement('MultiCheckbox', 'statistics', array(
            'label' => 'Choose the statistics that you want to be displayed for the Stores in this block.',
            'multiOptions' => array("likeCount" => "Likes", "followCount" => "Followers", "viewCount" => "Views", "commentCount" => "Comments"),
        )
    );
    
    $this->addElement('Text', 'recent_order', array(
            'label' => 'Recent Tab (order)',
        )
    );
    
    $this->addElement('Text', 'popular_order', array(
            'label' => 'Most Popular Tab (order)',
        )
    );
    
    $this->addElement('Text', 'random_order', array(
            'label' => 'Random Tab (order)',
        )
    );
    
    $this->addElement('Text', 'featured_order', array(
            'label' => 'Featured Tab (order)',
        )
    );
    
    $this->addElement('Text', 'sponosred_order', array(
            'label' => 'Sponosred Tab (order)',
        )
    );
    
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
      $this->addElement('Text', 'joined_order', array(
              'label' => 'Most Joined Stores Tab (order)',
          )
      );
    }
    
    $this->addElement(
     'Select',
     'detactLocation',
     array(
         'label' => 'Do you want to display stores based on user’s current location? (Note:- For this you must be enabled the auto-loading.)',
         'multiOptions' => array(
             1 => 'Yes',
             0 => 'No'
         ),
         'value' => '0'
     )
    );
    
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.proximity.search.kilometer', 0)) {
        $locationDescription = "Choose the kilometers within which pages will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
        $locationLableS = "Kilometer";
        $locationLable = "Kilometers";
    } else {
        $locationDescription = "Choose the miles within which stores will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
        $locationLableS = "Mile";
        $locationLable = "Miles";
    }
    
    $this->addElement(
        'Select',
        'defaultLocationDistance',
        array(
            'label' => $locationDescription,
            'multiOptions' => array(
                '0' => '',
                '1' => '1 ' . $locationLableS,
                '2' => '2 ' . $locationLable,
                '5' => '5 ' . $locationLable,
                '10' => '10 ' . $locationLable,
                '20' => '20 ' . $locationLable,
                '50' => '50 ' . $locationLable,
                '100' => '100 ' . $locationLable,
                '250' => '250 ' . $locationLable,
                '500' => '500 ' . $locationLable,
                '750' => '750 ' . $locationLable,
                '1000' => '1000 ' . $locationLable,
            ),
            'value' => '1000'
        )
    );    
    
//    $this->addElement(
//        'Radio',
//        'loaded_by_ajax',
//        array(
//            'label' => 'Widget Content Loading',
//            'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
//            'multiOptions' => array(
//                1 => 'Yes',
//                0 => 'No'
//            ),
//            'value' => 0,
//        )
//    );
  }
}