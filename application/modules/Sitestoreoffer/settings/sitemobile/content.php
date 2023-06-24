<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreoffer.isActivate', 0);
if ( empty($isActive) ) {
  return;
}

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => $view->translate('Store Profile Coupons'),
        'description' => $view->translate('This widget forms the Coupons tab on the Store Profile and displays the coupons of the Store. It should be placed in the Tabbed Blocks area of the Store Profile.'),
        'category' => $view->translate('Stores / Marketplace - Store Profile'),
        'type' => 'widget',
        'name' => 'sitestoreoffer.sitemobile-profile-sitestoreoffers',
        'defaultParams' => array(
            'title' => $view->translate('Coupons'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Store Coupons'),
        'description' => $view->translate('Displays the list of Coupons from Stores created on your community. This widget should be placed in the widgetized Store Coupons store. Results from the Search Store Coupons form are also shown here.'),
        'category' => $view->translate('Stores / Marketplace - Stores'),
        'type' => 'widget',
        'name' => 'sitestoreoffer.sitestore-coupon',
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
                        'description' => $view->translate('(number of coupons to show)'),
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
        'title' => 'Store’s Hot Coupons Slideshow',
        'description' => 'Displays hot coupons in an attractive slideshow. You can set the count of the number of coupons to show in this widget. If the total number of coupons selected as hot are more than that count, then the coupons to be displayed will be sequentially picked up.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestoreoffer.hot-coupons-slideshow',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Hot Coupons',
            'itemCountPerStore' => 10,
        ),
			'adminForm' => array(
					'elements' => array(
							array(
									'Select',
									'category_id',
									array(
											'label' => 'Category',
											'multiOptions' => $categories_prepared,
									)
							),
					),
			),
    ),
		array(
				'title' => $view->translate('Store Coupon View'),
				'description' => $view->translate("This widget should be placed on the Store Coupon View Store."),
				'category' => $view->translate('Stores / Marketplace - Stores'),
				'type' => 'widget',
				'name' => 'sitestoreoffer.coupon-content',
				'defaultParams' => array(
						'title' => '',
						'titleCount' => true,
				),
		),
	)

?>