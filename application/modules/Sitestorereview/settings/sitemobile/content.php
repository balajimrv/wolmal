<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.isActivate', 0);
if(empty($isActive)){ return; }

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => $view->translate('Store Profile Reviews'),
        'description' => $view->translate('This widget forms the Reviews tab on the Store Profile and displays the reviews of the Store. It should be placed in the Tabbed Blocks area of the Store Profile.'),
        'category' => $view->translate('Stores / Marketplace - Store Profile'),
        'type' => 'widget',
        'name' => 'sitestorereview.sitemobile-profile-sitestorereviews',
        'defaultParams' => array(
            'title' => $view->translate('Reviews'),
        ),
    ),
    array(
			'title' => $view->translate('Store Review View'),
			'description' => $view->translate("This widget should be placed on the Store Review View Store."),
      'category' => $view->translate('Stores / Marketplace - Stores'),
			'type' => 'widget',
			'name' => 'sitestorereview.review-content',
			'defaultParams' => array(
					'title' => '',
					'titleCount' => true,
			),
	  ),
    array(
        'title' => $view->translate('Store Reviews'),
        'description' => $view->translate('Displays the list of Reviews from Stores created on your community. This widget should be placed in the widgetized Store Reviews store. Results from the Search Store Reviews form are also shown here.'),
        'category' => $view->translate('Stores / Marketplace - Stores'),
        'type' => 'widget',
        'name' => 'sitestorereview.sitestore-review',
        'defaultParams' => array(
            'title' => $view->translate('Reviews'),
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of reviews to show)'),
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
)
?>