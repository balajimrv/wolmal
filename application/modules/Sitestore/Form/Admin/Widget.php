<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Widget.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class sitestore_Form_Admin_Widget extends Engine_Form {

  public function init() {
    $this
            ->setTitle('General Settings')
            ->setDescription('Configure the general settings for various widgets available with this plugin.');

    // VALUE FOR FEATURE store IN SLIDESHOW
    $this->addElement('Text', 'sitestore_feature_widgets', array(
        'label' => 'Featured Stores Slideshow
 Widget',
        'maxlength' => '3',
        'description' => 'How many stores will be shown in the featured stores slideshow widget? Note that out of all the featured stores, these many stores will be picked up randomly to be shown in the slideshow (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.feature.widgets', 10),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR SPONSORED store IN Carousel
    $this->addElement('Text', 'sitestore_sponserdsitestore_widgets', array(
        'label' => 'Sponsored Stores Carousel Widget',
        'maxlength' => '3',
        'description' => 'How many stores will be shown in one view of the sponsored stores carousel widget? Note that this carousel is AJAX based and users will be able to browse through all the sponsored stores (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponserdsitestore.widgets', 4),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));
    // VALUE FOR Sponsored Interval
    $this->addElement('Text', 'sitestore_sponsored_interval', array(
        'label' => 'Sponsored Carousel Speed',
        'allowEmpty' => false,
        'required' => true,
        'maxlength' => '3',
        'description' => 'What maximum Carousel Speed should be applied to the sponsored widget?',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.interval', 300),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR TRUNCATION
    $this->addElement('Text', 'sitestore_title_truncationsponsored', array(
        'label' => 'Title Truncation Limit For Sponsored Items Widget',
        'allowEmpty' => false,
        'maxlength' => '3',
        'required' => true,
        'description' => 'What maximum limit should be applied to the number of characters in the titles of items in the Sponsored widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.)',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.title.truncationsponsored', 18),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR MOSTCOMMENT
    $this->addElement('Text', 'sitestore_comment_widgets', array(
        'label' => 'Most Commented Stores Widget',
        'maxlength' => '3',
        'description' => 'How many stores will be shown in the most commented stores widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.comment.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR MOSTLIKE
    $this->addElement('Text', 'sitestore_likes_widgets', array(
        'label' => 'Most Liked Stores Widget',
        'maxlength' => '3',
        'description' => 'How many stores will be shown in the most liked stores widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.likes.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR USER store STORE
    $this->addElement('Text', 'sitestore_usersitestore_widgets', array(
        'label' => 'Store Profile Owner Stores Widget',
        'maxlength' => '3',
        'description' => 'How many stores will be shown in the store profile owner stores widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.usersitestore.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR AJAX LAYOUT
    $this->addElement('MultiCheckbox', 'sitestore_ajax_widgets_layout', array(
        'description' => 'Choose the view types that you want to be available for stores on the stores home and browse stores.',
        'label' => 'Views on Stores Home and Browse Stores',
        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View"),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.ajax.widgets.layout', array("0" => "1", "1" => "2", "2" => "3")),
    ));

    // VALUE FOR AJAX LAYOUT ORDER
    $this->addElement('Radio', 'sitestore_ajax_layouts_oder', array(
        'description' => 'Select a default view type for stores on the Stores Home Widget and Browse Stores.',
        'label' => 'Default View on Stores Home Widget and Browse Stores',
        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View"),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.ajax.layouts.oder', 1),
    ));

    // VALUE FOR LIST SHOW IN AJAX WIDGETS
    $this->addElement('MultiCheckbox', 'sitestore_ajax_widgets_list', array(
        'description' => 'Choose the ajax tabs that you want to be there in the Main Stores Home Widget.',
        'label' => 'Ajax Tabs of Main Stores Home Widget',
        // 'required' => true,
        'multiOptions' => array("1" => "Recent", "2" => "Most Popular", "3" => "Random", "4" => "Featured", "5" => "Sponsored"),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.ajax.widgets.list', array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => '5')),
    ));


    // VALUE FOR POPULAR IN SITESTORE VIEW
    $this->addElement('Text', 'sitestore_popular_widgets', array(
        'label' => 'Popular Stores Widget',
        'maxlength' => '3',
        'description' => 'How many stores will be shown in the popular stores widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.popular.widgets', 10),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR POPULAR IN GRID VIEW
    $this->addElement('Text', 'sitestore_popular_thumbs', array(
        'label' => 'Popular Stores Widget Grid View',
        'maxlength' => '3',
        'description' => 'How many stores will be shown in the popular stores widget in image view (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.popular.thumbs', 15),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR RECENT IN SITESTORE VIEW
    $this->addElement('Text', 'sitestore_recent_widgets', array(
        'label' => 'Recent Stores Widget',
        'maxlength' => '3',
        'description' => 'How many stores will be shown in the recent stores widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.recent.widgets', 10),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR RECENT IN GRID VIEW
    $this->addElement('Text', 'sitestore_recent_thumbs', array(
        'label' => 'Recent Stores Widget Grid View',
        'maxlength' => '3',
        'description' => 'How many stores will be shown in the recent stores widget in image view (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.recent.thumbs', 15),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR RANDOM IN SITESTORE VIEW
    $this->addElement('Text', 'sitestore_random_widgets', array(
        'label' => 'Random Stores Widget',
        'maxlength' => '3',
        'description' => 'How many stores will be shown in the random stores widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.random.widgets', 10),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR RANDOM IN GRID VIEW
    $this->addElement('Text', 'sitestore_random_thumbs', array(
        'label' => 'Random Stores Widget Grid View',
        'maxlength' => '3',
        'description' => 'How many stores will be shown in the random stores widget in image view (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.random.thumbs', 15),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));


    // VALUE FOR FETURED IN SITESTORE VIEW
    $this->addElement('Text', 'sitestore_featured_list', array(
        'label' => 'Featured Stores Widget',
        'maxlength' => '3',
        'description' => 'How many stores will be shown in the featured stores widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.featured.list', 10),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR FETURED IN GRID VIEW
    $this->addElement('Text', 'sitestore_featured_thumbs', array(
        'label' => 'Featured Stores  Widget Grid View',
        'maxlength' => '3',
        'description' => 'How many stores will be shown in the featured stores widget in image view (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.featured.thumbs', 15),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR SPONSORED IN SITESTORE VIEW
    $this->addElement('Text', 'sitestore_sponsored_list', array(
        'label' => 'Sponsored Stores Widget',
        'maxlength' => '3',
        'description' => 'How many stores will be shown in the sponsored stores widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.list', 10),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR SPONSORED IN GRID VIEW
    $this->addElement('Text', 'sitestore_sponosred_thumbs', array(
        'label' => 'Sponsored Stores Widget Grid View',
        'maxlength' => '3',
        'description' => 'How many stores will be shown in the sponsored stores widget in image view (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponosred.thumbs', 15),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));
    $this->addElement('Text', 'sitestore_favourite_stores', array(
        'label' => 'Favourites stores Widget',
        'maxlength' => '3',
        'description' => 'How many stores will be shown in the Favourites Stores widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.favourite.stores', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));


    // VALUE FOR RANDOM IN GRID VIEW
    $this->addElement('Text', 'sitestore_suggest_sitestores', array(
        'label' => 'You May Also Like Widget',
        'maxlength' => '3',
        'description' => 'How many stores will be shown in the you may also like widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.suggest.sitestores', 5),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR RANDOM IN GRID VIEW
    $this->addElement('Text', 'sitestore_recently_view', array(
        'label' => 'Recently Viewed Stores Widget',
        'maxlength' => '3',
        'description' => 'How many stores will be shown in the recently viewed stores widget in image view (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.recently.view', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR RANDOM IN GRID VIEW
    $this->addElement('Text', 'sitestore_recentlyfriend_view', array(
        'label' => 'Recently Viewed By Friends Widget',
        'maxlength' => '3',
        'description' => 'How many stores will be shown in the recently viewed by friends widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.recentlyfriend_view', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));
    // VALUE FOR RANDOM IN GRID VIEW
    $this->addElement('Text', 'sitestore_storelike_view', array(
        'label' => 'Store Profile Likes Widget',
        'maxlength' => '3',
        'description' => 'How many users will be shown in the store profile likes widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.storelike.view', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR Discusion IN SITESTORE VIEW
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion')) {
      $this->addElement('Text', 'sitestore_mostdiscussed_widgets', array(
          'label' => 'Most Discussed Stores Widget',
          'maxlength' => '3',
          'description' => 'How many stores will be shown in the most discussed Stores widget (value can not be empty or zero) ?',
          'required' => true,
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.mostdiscussed.widgets', 3),
          'validators' => array(
              array('Int', true),
              array('GreaterThan', true, array(0)),
          ),
      ));
    }

    $this->addElement('Text', 'sitestore_popular_locations', array(
        'label' => 'Popular Locations Widget',
        'maxlength' => '3',
        'description' => 'How many locations will be shown in the popular locations widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.popular.locations', 10),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));


    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}

?>