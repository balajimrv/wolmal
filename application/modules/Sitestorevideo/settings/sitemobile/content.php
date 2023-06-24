<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.isActivate', 0);
if (empty($isActive)) {
  return;
}

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => $view->translate('Store Profile Videos'),
        'description' => $view->translate('This widget forms the Videos tab on the Store Profile and displays the videos of the Store. It should be placed in the Tabbed Blocks area of the Store Profile.'),
        'category' => $view->translate('Stores / Marketplace - Store Profile'),
        'type' => 'widget',
        'name' => 'sitestorevideo.sitemobile-profile-sitestorevideos',
        'defaultParams' => array(
            'title' => $view->translate('Videos'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Store Videos'),
        'description' => $view->translate('Displays the list of Videos from Stores created on your community. This widget should be placed in the widgetized Store Videos store. Results from the Search Store Videos form are also shown here.'),
        'category' => $view->translate('Stores / Marketplace - Stores'),
        'type' => 'widget',
        'name' => 'sitestorevideo.sitestore-video',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of videos to show)'),
                        'value' => 10,
												'validators' => array(
													array('Int', true),
													array('GreaterThan', true, array(0)),
												),
                    ),
                ),
            ),
        ),
    ),
		array(
				'title' => $view->translate('Store Video View'),
				'description' => $view->translate("This widget should be placed on the Store Video View Store."),
				'category' => $view->translate('Stores / Marketplace - Stores'),
				'type' => 'widget',
				'name' => 'sitestorevideo.video-content',
				'defaultParams' => array(
						'title' => '',
						'titleCount' => true,
				),
		),
)
?>