<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content_user.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => $view->translate('Store Profile Popular Reviews'),
        'description' => $view->translate('Displays your Store\'s popular reviews.'),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestorereview.popular-sitestorereviews',
        'defaultParams' => array(
            'title' => $view->translate('Most Popular Reviews'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Store Profile Most Commented Reviews'),
        'description' => $view->translate('Displays your Store\'s most commented reviews.'),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestorereview.comment-sitestorereviews',
        'defaultParams' => array(
            'title' => $view->translate('Most Commented Reviews'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Store Profile Most Liked Reviews'),
        'description' => $view->translate('Displays your Store\'s most liked reviews.'),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestorereview.like-sitestorereviews',
        'defaultParams' => array(
            'title' => $view->translate('Most Liked Reviews'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Store Profile Reviews'),
        'description' => $view->translate('Forms the Reviews tab of your Store and shows reviews of your Store. It should be placed in the Tabbed Blocks area of the Store Profile.'),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestorereview.profile-sitestorereviews',
        'defaultParams' => array(
            'title' => $view->translate('Reviews'),
        ),
    ),
    array(
        'title' => $view->translate('Store Profile Review Rating'),
        'description' => $view->translate('Displays your Store\'s review ratings.'),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestorereview.ratings-sitestorereviews',
        'defaultParams' => array(
            'title' => $view->translate('Ratings'),
        ),
    ),
)
?>