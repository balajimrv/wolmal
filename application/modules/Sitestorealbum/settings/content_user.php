<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content_user.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorealbum.isActivate', 0);
if (empty($isActive)) {
  return;
}
$showContent_cover = array("mainPhoto" => "Store Profile Photo", "title" => "Store Title", "followButton" => "Follow Button", "likeButton" => "Like Button", "likeCount" => "Total Likes","followCount" => "Total Followers");
$showContent_option = array("mainPhoto", "title", "followButton", "likeButton", "followCount", "likeCount");
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
	$showContent_cover['memberCount'] = 'Total Members';
	$showContent_cover['addButton'] = 'Add People Button';
	$showContent_cover['joinButton'] = 'Join Page Button';
	$showContent_option[] = 'addButton';
	$showContent_option[] = 'joinButton';
	$showContent_option[] = 'memberCount';
}
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => $view->translate('Store Profile Albums'),
        'description' => $view->translate('Forms the Albums tab of your Store. It displays the photos added by you and by the Store visitors. It should be placed in the Tabbed Blocks area of the Store Profile.'),
        'category' => $view->translate('Stores / Marketplace - Store Profile'),
        'type' => 'widget',
        'name' => 'sitestore.photos-sitestore',
        'defaultParams' => array(
            'title' => 'Photos',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Store Profile Random Albums'),
        'description' => $view->translate('Displays random albums and photos of the Store.'),
        'category' => $view->translate('Stores / Marketplace - Store Profile'),
        'type' => 'widget',
        'name' => 'sitestore.albums-sitestore',
        'defaultParams' => array(
            'title' => 'Albums',
            'titleCount' => '',
        ),
    ),
    array(
        'title' => $view->translate('Store Profile Most Commented Photos'),
        'description' => $view->translate('Displays the most commented photos of the Store.'),
        'category' => $view->translate('Stores / Marketplace - Store Profile'),
        'type' => 'widget',
        'name' => 'sitestore.photocomment-sitestore',
        'defaultParams' => array(
            'title' => $view->translate('Most Commented Photos'),
            'titleCount' => '',
        ),
    ),
    array(
        'title' => $view->translate('Store Profile Most Liked Photos'),
        'description' => $view->translate('Displays the most liked photos of the Store.'),
        'category' => $view->translate('Stores / Marketplace - Store Profile'),
        'type' => 'widget',
        'name' => 'sitestore.photolike-sitestore',
        'defaultParams' => array(
            'title' => $view->translate('Most Liked Photos'),
            'titleCount' => '',
        ),
    ),
    array(
        'title' => $view->translate('Store Profile Photos Strip'),
        'description' => $view->translate("Displays some photos out of all the albums of a Store in a strip. Store Admin (you) can choose which photos to be shown in the strip by hiding the ones that should not be displayed. Hidden photos are replaced by new photos and so on."),
        'category' => $view->translate('Stores / Marketplace - Store Profile'),
        'type' => 'widget',
        'name' => 'sitestore.photorecent-sitestore',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
    ),
    array(
        'title' => 'Store Profile Cover Photo and Information',
        'description' => 'Displays the store cover photo with store profile photo, title and various action links that can be performed on the store from their Profile store (Like, Follow, etc.). You can choose various options from the Edit Settings of this widget. This widget should be placed on the Store Profile store.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestore.store-cover-information-sitestore',
        'defaultParams' => array(
            'title' => 'Information',
            'titleCount' => true,
            'showContent' => $showContent_option
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'showContent',
                    array(
                        'label' => 'Select the information options that you want to be available in this block.',
                        'multiOptions' => $showContent_cover,
                    ),
                ), 
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Enter the cover photo height (in px). (Minimum 150 px required.)',
                        'value' => '300',
                    )
                ),             
            ),
        ),
    ),
)
?>