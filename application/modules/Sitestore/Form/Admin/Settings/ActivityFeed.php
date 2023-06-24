<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ActivityFeed.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Admin_Settings_ActivityFeed extends Engine_Form {

  public function init() {
    $this
            ->setAttribs(array(
                'id' => 'activity_feed_form',
            ))
            ->setTitle('Activity Feed Settings')
            ->setDescription('Below are the options to customize the Activity Feeds related to Stores. Once you configure all the settings below, click on "Save Changes" to save them.');
    $subCoreStoreApi = Engine_Api::_()->getApi('subCore', 'sitestore');
    $socialengineaddonFeedIsAdded = $subCoreStoreApi->isCoreActivtyFeedWidget('user_index_home', 'activity.feed');
    $advancedactivityHomeFeedsIsAdded = $subCoreStoreApi->isCoreActivtyFeedWidget('user_index_home', 'advancedactivity.home-feeds');
    $translate = Zend_Registry::get('Zend_Translate');
    $description = "Select the type of activity feeds that should be published for stores. By default, the photo and name of Store Admin is shown in activity feeds of stores. Using this setting, you can instead choose to show the Store's Photo and Title.";
    if ($socialengineaddonFeedIsAdded || $advancedactivityHomeFeedsIsAdded) {
      $description .= "If you choose the 2nd option to show the Store's Photo and Title, then users will also receive on their homestore the updates from Stores that they have Liked.";
    }
    $options1 = 'Store\'s Photo and Title';
    if ($socialengineaddonFeedIsAdded) {
      $options1.=" (Choosing this will send to users on their homestore, the updates from Stores that they Like. If you have the \"Advanced Activity Feeds / Wall Plugin\" (http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin) installed on your site, then Store Photo and Title will also come for comments made by Store Admins on activity feeds of their Store and comments on the content of their Store (photo, video, document, etc.))";
    } elseif ($advancedactivityHomeFeedsIsAdded) {
      $options1.=" (Choosing this will send to users on their homestore, the updates from Stores that they Like. Store Photo and Title will also come for comments made by Store Admins on activity feeds of their Store and comments on the content of their Store (photo, video, document, etc.))";
    } else {
      $options1.=" (If you have the \"Advanced Activity Feeds / Wall Plugin\" (http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin) installed on your site, then Store Photo and Title will also come for comments made by Store Admins on activity feeds of their Store and comments on the content of their Store (photo, video, document, etc.))";
    }
    $this->getView()->escape($options1);
    $this->addElement('Radio', 'sitestore_feed_type', array(
        'label' => 'Stores / Marketplace Activity Feed Type',
        'description' => $description,
        'multiOptions' => array(
            '0' => 'Store Admin\'s Photo and Name',
            '1' => $options1,
        ),
        'onclick' => 'showEditingOptions("sitestorefeed_likestore_dummy-wrapper",this.value)',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.feed.type', 0),
    ));

    if (!$socialengineaddonFeedIsAdded && !$advancedactivityHomeFeedsIsAdded) {
      $this->addElement('Dummy', 'sitestorefeed_likestore_dummy', array(
          'decorators' => array(array('ViewScript', array(
                      'viewScript' => 'admin-settings/activity-feed/_feedLikestore.tpl',
                      'class' => 'form element'
              )))
      ));
    }
      $this->addElement('Radio', 'sitestore_feed_onlyliked', array(
        'label' => 'Show Stores / Marketplace Activity Feeds On Member Home Page',
        'description' => 'Select the type of Activity Feeds corresponding to Stores which you want to display to users on Member Home Page.',
        'multiOptions' => array(
            '0' => 'Show Feeds of all Stores (This will be dependent on the Content Privacy of user.)',
            '1' => 'Show Feeds only of Liked Stores',
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.feed.onlyliked', 1),
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}

?>