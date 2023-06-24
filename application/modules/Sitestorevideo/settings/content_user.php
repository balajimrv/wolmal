<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content_user.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => $view->translate('Store Profile Videos'),
        'description' => $view->translate('Forms the Videos tab of your Store. It should be placed in the Tabbed Blocks area of the Store Profile.'),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestorevideo.profile-sitestorevideos',
        'defaultParams' => array(
            'title' => $view->translate('Videos'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Store Profile Most Commented Videos'),
        'description' => $view->translate("Displays list of your Store's most commented videos."),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestorevideo.comment-sitestorevideos',
        'defaultParams' => array(
            'title' => $view->translate('Most Commented Videos'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Store Profile Most Recent Videos'),
        'description' => $view->translate("Displays list of your Store's most recent videos."),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestorevideo.recent-sitestorevideos',
        'defaultParams' => array(
            'title' => $view->translate('Most Recent Videos'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Store Profile Top Rated Videos'),
        'description' => $view->translate("Displays list of your Store's top rated videos."),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestorevideo.rate-sitestorevideos',
        'defaultParams' => array(
            'title' => $view->translate('Top Rated Videos'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Store Profile Most Liked Videos'),
        'description' => $view->translate("Displays list of your Store's most liked videos."),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestorevideo.like-sitestorevideos',
        'defaultParams' => array(
            'title' => $view->translate('Most Liked Videos'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Store Profile Featured Videos'),
        'description' => $view->translate("Displays list of your Store's featured videos."),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestorevideo.featurelist-sitestorevideos',
        'defaultParams' => array(
            'title' => $view->translate('Featured Store Videos'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Store Profile Highlighted Videos'),
        'description' => $view->translate("Displays list of your Store's highlighted videos."),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
       'name' => 'sitestorevideo.highlightelist-sitestorevideos',
        'defaultParams' => array(
            'title' => $view->translate('Highlighted Store Videos'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Store Profile Most Viewed Videos'),
        'description' => $view->translate("Displays list of your Store's most viewed videos."),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestorevideo.view-sitestorevideos',
        'defaultParams' => array(
            'title' => $view->translate('Most Viewed Videos'),
            'titleCount' => true,
        ),
    ),
)
?>