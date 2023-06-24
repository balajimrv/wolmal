<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

$final_array = array(
	array(
		'title' => $view->translate('Manage Products'),
		'description' => $view->translate('This widget forms the Products tab on the Store Profile and displays the products of the Store. It should be placed in the Tabbed Blocks area of the Store Profile.'),
		'category' => $view->translate('Stores / Marketplace - Store Profile'),
		'type' => 'widget',
		'autoEdit' => true,
		'name' => 'sitestoreproduct.store-profile-products',
		'defaultParams' => array(
				'title' => 'Products',
		),
	),
);

return $final_array;