<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: storeMenuSettings.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$check_table = Engine_Api::_()->getDbtable('menuItems', 'core');
$check_name = $check_table->info('name');
$adminTabOrdering = 2;

// --------------- SITESTORE PLUGIN MENUS -------------------

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_package');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_package';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Manage Packages';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestore","controller":"package","action":"index"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_level');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_level';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Member Level Settings';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestore","controller":"level"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_sitestorecategories');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_sitestorecategories';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Categories';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestore","controller":"settings","action":"sitestorecategories"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_fields');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_fields';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Profile Fields';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestore","controller":"fields"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_profilemaps');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_profilemaps';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Category-Profile Fields Mapping';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestore","controller":"profilemaps", "action":"manage"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_sitestorereview');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_sitestorereview';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Reviews & Ratings';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestorereview","controller":"settings"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_viewsitestore');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_viewsitestore';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Manage Stores';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestore","controller":"viewsitestore"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_productmanage');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_productmanage';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Manage Products';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestoreproduct","controller":"manage"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_compare');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_compare';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Comparison Settings';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestoreproduct","controller":"settings","action":"compare"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_transactions');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_transactions';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Transactions';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestore","controller":"payment"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_shippinglocation');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_shippinglocation';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Shipping Locations';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestoreproduct","controller":"location","action" : "index"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_producttax');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_producttax';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Taxes';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestoreproduct","controller":"tax"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_sitestorealbum');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_sitestorealbum';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Photo Albums';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestorealbum","controller":"settings"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_sitestorevideo');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_sitestorevideo';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Videos';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestorevideo","controller":"settings"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_sitestoreoffer');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_sitestoreoffer';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Offers';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestoreoffer","controller":"settings"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_sitestoreform');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_sitestoreform';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Forms';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestoreform","controller":"settings"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_manage_manage');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_manage_manage';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Manage Orders';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestoreproduct","controller":"manage", "action":"manage-orders"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_commission');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_commission';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Commissions';
    $menu_item->plugin = 'Sitestoreproduct_Plugin_Menus::showAdminCommissionTab';
    $menu_item->params = '{"route":"admin_default","module":"sitestoreproduct","controller":"manage", "action":"commission"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_payment');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_payment';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Payment Requests';
    $menu_item->plugin = 'Sitestoreproduct_Plugin_Menus::showAdminPaymentRequestTab';
    $menu_item->params = '{"route":"admin_default","module":"sitestoreproduct","controller":"payment"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_wishlist');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_wishlist';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Wishlists';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestoreproduct","controller":"wishlist","action":"manage"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_productgraph');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_productgraph';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Graph Settings';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestoreproduct","controller":"settings","action" : "graph"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_sitestoreadmincontact');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_sitestoreadmincontact';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Contact Store Owners';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestoreadmincontact","controller":"mails"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_claim');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_claim';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Manage Claims';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestore","controller":"claim"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

//        $select = $check_table->select()
//                ->from($check_name, array('id'))
//                ->where('name = ?', 'sitestore_admin_main_email');
//        $queary_info = $select->query()->fetchAll();
//        if (empty($queary_info)) {
//          $menu_item = $check_table->createRow();
//          $menu_item->name = 'sitestore_admin_main_email';
//          $menu_item->module = 'sitestore';
//          $menu_item->label = 'Insights Email Settings';
//          $menu_item->plugin = '';
//          $menu_item->params = '{"route":"admin_default","module":"sitestore","controller":"settings","action":"email"}';
//          $menu_item->menu = 'sitestore_admin_main';
//          $menu_item->submenu = '';
//          $menu_item->order = $adminTabOrdering++;
//          $menu_item->save();
//        }
//        $select = $check_table->select()
//                ->from($check_name, array('id'))
//                ->where('name = ?', 'sitestore_admin_main_graph');
//        $queary_info = $select->query()->fetchAll();
//        if (empty($queary_info)) {
//          $menu_item = $check_table->createRow();
//          $menu_item->name = 'sitestore_admin_main_graph';
//          $menu_item->module = 'sitestore';
//          $menu_item->label = 'Insights Graph Settings';
//          $menu_item->plugin = '';
//          $menu_item->params = '{"route":"admin_default","module":"sitestore","controller":"settings","action":"graph"}';
//          $menu_item->menu = 'sitestore_admin_main';
//          $menu_item->submenu = '';
//          $menu_item->order = $adminTabOrdering++;
//          $menu_item->save();
//        }

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_form_search');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_form_search';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Search Form Settings';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestore","controller":"settings","action":"form-search"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->enabled = 1;
    $menu_item->custom = 0;
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_manage_startup');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_manage_startup';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Store Startup Pages';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestoreproduct","controller":"startup"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_items');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_items';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Store of the day';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestore","controller":"items","action":"day"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_report');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_report';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Sales Report';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestoreproduct","controller":"report","action" : "index"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_layoutdefault');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_layoutdefault';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Store Layout';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestore","controller":"defaultlayout"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_global_url');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_global_url';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Short Store URL';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestoreurl","controller":"settings","action":"index"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();

    $select = $check_table->select()
            ->from($check_name, array('id'))
            ->where('name = ?', 'sitestoreurl_admin_blockurl');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
        $menu_item = $check_table->createRow();
        $menu_item->name = 'sitestoreurl_admin_global_url';
        $menu_item->module = 'sitestoreurl';
        $menu_item->label = 'General Settings';
        $menu_item->plugin = '';
        $menu_item->params = '{"route":"admin_default","module":"sitestoreurl","controller":"settings","action":"index"}';
        $menu_item->menu = 'sitestoreurl_admin_main';
        $menu_item->submenu = '';
        $menu_item->order = 1;
        $menu_item->save();
    }

    $select = $check_table->select()
            ->from($check_name, array('id'))
            ->where('name = ?', 'sitestoreurl_admin_blockurl');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
        $menu_item = $check_table->createRow();
        $menu_item->name = 'sitestoreurl_admin_blockurl';
        $menu_item->module = 'sitestoreurl';
        $menu_item->label = 'Banned URLs';
        $menu_item->plugin = '';
        $menu_item->params = '{"route":"admin_default","module":"sitestoreurl","controller":"settings","action":"banningurl"}';
        $menu_item->menu = 'sitestoreurl_admin_main';
        $menu_item->submenu = '';
        $menu_item->order = 2;
        $menu_item->save();
    }

    $select = $check_table->select()
            ->from($check_name, array('id'))
            ->where('name = ?', 'sitestoreurl_admin_main_url');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
        $menu_item = $check_table->createRow();
        $menu_item->name = 'sitestoreurl_admin_main_url';
        $menu_item->module = 'sitestoreurl';
        $menu_item->label = 'Stores with Banned URLs';
        $menu_item->plugin = '';
        $menu_item->params = '{"route":"admin_default","module":"sitestoreurl","controller":"settings","action":"storeurl"}';
        $menu_item->menu = 'sitestoreurl_admin_main';
        $menu_item->submenu = '';
        $menu_item->order = 3;
        $menu_item->save();
    }
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_sitestorelikebox');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_sitestorelikebox';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Like Box';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestorelikebox","controller":"settings"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}


//$select = $check_table->select()
//        ->from($check_name, array('id'))
//        ->where('name = ?', 'sitestore_document_admin_main_settings');
//$queary_info = $select->query()->fetchAll();
//if (empty($queary_info)) {
//    $menu_item = $check_table->createRow();
//    $menu_item->name = 'sitestore_document_admin_main_settings';
//    $menu_item->module = 'sitestore';
//    $menu_item->label = 'Documents';
//    $menu_item->plugin = '';
//    $menu_item->params = '{"route":"admin_default","module":"sitestoredocument","controller":"settings"}';
//    $menu_item->menu = 'sitestore_admin_main';
//    $menu_item->submenu = '';
//    $menu_item->order = $adminTabOrdering++;
//    $menu_item->save();
//}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_statistic');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_statistic';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Statistics';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestore","controller":"settings","action":"statistic"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

//$select = $check_table->select()
//        ->from($check_name, array('id'))
//        ->where('name = ?', 'sitestore_admin_main_import');
//$queary_info = $select->query()->fetchAll();
//if (empty($queary_info)) {
//  $menu_item = $check_table->createRow();
//  $menu_item->name = 'sitestore_admin_main_import';
//  $menu_item->module = 'sitestore';
//  $menu_item->label = 'Import';
//  $menu_item->plugin = '';
//  $menu_item->params = '{"route":"admin_default","module":"sitestore","controller":"importlisting"}';
//  $menu_item->menu = 'sitestore_admin_main';
//  $menu_item->submenu = '';
//  $menu_item->order = $adminTabOrdering++;
//  $menu_item->save();
//}
//        $select = $check_table->select()
//                ->from($check_name, array('id'))
//                ->where('name = ?', 'sitestore_admin_extension');
//        $queary_info = $select->query()->fetchAll();
//        if (empty($queary_info)) {
//          $menu_item = $check_table->createRow();
//          $menu_item->name = 'sitestore_admin_extension';
//          $menu_item->module = 'sitestore';
//          $menu_item->label = 'Extensions';
//          $menu_item->plugin = '';
//          $menu_item->params = '{"route":"admin_default","module":"sitestore","controller":"extension","action":"upgrade"}';
//          $menu_item->menu = 'sitestore_admin_main';
//          $menu_item->submenu = '';
//          $menu_item->order = $adminTabOrdering++;
//          $menu_item->save();
//        }


$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_adsettings');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_adsettings';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Ad Settings';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestore","controller":"settings","action":"adsettings"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestorep_admin_main_integrations');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestorep_admin_main_integrations';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Plugin Integrations';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestoreproduct","controller":"settings","action":"integrations"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestorep_admin_main_integrations');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestorep_admin_main_integrations';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Plugin Integrations';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestoreproduct","controller":"settings","action":"integrations"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

//        $select = $check_table->select()
//                ->from($check_name, array('id'))
//                ->where('name = ?', 'core_main_sitestore');
//        $queary_info = $select->query()->fetchAll();
//        if (empty($queary_info)) {
//          $menu_item = $check_table->createRow();
//          $menu_item->name = 'core_main_sitestore';
//          $menu_item->module = 'sitestore';
//          $menu_item->label = 'Stores';
//          $menu_item->plugin = '';
//          $menu_item->params = '{"route":"sitestore_general","action":"home"}';
//          $menu_item->menu = 'core_main';
//          $menu_item->submenu = '';
//          $menu_item->order = $adminTabOrdering++;
//          $menu_item->save();
//        }

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'core_sitemap_sitestore');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'core_sitemap_sitestore';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Stores';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"sitestore_general","action":"home"}';
    $menu_item->menu = 'core_sitemap';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'authorization_admin_level_sitestore');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'authorization_admin_level_sitestore';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Stores';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestore","controller":"level","action":"index"}';
    $menu_item->menu = 'authorization_admin_level';
    $menu_item->submenu = '';
    $menu_item->order = 999;
    $menu_item->save();
}




// ------------------- SITESTORE-ALBUM PLUGIN MENUS ------------------

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestorealbum_admin_main_manage');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestorealbum_admin_main_manage';
    $menu_item->module = 'sitestorealbum';
    $menu_item->label = 'Manage Store Albums';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestorealbum","controller":"manage","action":"index"}';
    $menu_item->menu = 'sitestorealbum_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 2;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestorealbum_admin_main_level');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestorealbum_admin_main_level';
    $menu_item->module = 'sitestorealbum';
    $menu_item->label = 'Member Level Settings';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestorealbum","controller":"level"}';
    $menu_item->menu = 'sitestorealbum_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 3;
    $menu_item->save();
}




// ------------- SITESTORE-FORM PLUGIN MENUS --------------------
$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestoreform_admin_main_manage');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestoreform_admin_main_manage';
    $menu_item->module = 'sitestoreform';
    $menu_item->label = 'Manage Forms';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestoreform","controller":"manage","action":"index"}';
    $menu_item->menu = 'sitestoreform_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 2;
    $menu_item->save();
}




// ------------- SITESTORE-OFFER PLUGIN MENUS --------------------
$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestoreoffer_admin_main_manage');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestoreoffer_admin_main_manage';
    $menu_item->module = 'sitestoreoffer';
    $menu_item->label = 'Manage Store Offers';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestoreoffer","controller":"manage"}';
    $menu_item->menu = 'sitestoreoffer_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 2;
    $menu_item->save();
}




// ------------- SITESTORE-REVIEW PLUGIN MENUS --------------------
$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestorereview_admin_main_params');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestorereview_admin_main_params';
    $menu_item->module = 'sitestorereview';
    $menu_item->label = 'Rating Parameters';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestorereview","controller":"ratingparameter", "action":"manage"}';
    $menu_item->menu = 'sitestorereview_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 3;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestorereview_admin_main_manage');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestorereview_admin_main_manage';
    $menu_item->module = 'sitestorereview';
    $menu_item->label = 'Manage Ratings & Reviews';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestorereview","controller":"manage", "action":"manage"}';
    $menu_item->menu = 'sitestorereview_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 5;
    $menu_item->save();
}




// ------------- SITESTORE-REVIEW PLUGIN MENUS --------------------
$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestorevideo_admin_main_utility');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestorevideo_admin_main_utility';
    $menu_item->module = 'sitestorevideo';
    $menu_item->label = 'Store Video Utilities';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestorevideo","controller":"settings","action":"utility"}';
    $menu_item->menu = 'sitestorevideo_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 4;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestorevideo_admin_main_manage');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestorevideo_admin_main_manage';
    $menu_item->module = 'sitestorevideo';
    $menu_item->label = 'Manage Store Videos';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestorevideo","controller":"manage","action":"index"}';
    $menu_item->menu = 'sitestorevideo_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 2;
    $menu_item->save();
}





// ------------- SITESTORE-LIKEBOX PLUGIN MENUS --------------------
$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestorelikebox_admin_main_css');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestorelikebox_admin_main_css';
    $menu_item->module = 'sitestorelikebox';
    $menu_item->label = 'Color Schemes';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestorelikebox","controller":"css","action":"index"}';
    $menu_item->menu = 'sitestorelikebox_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = 2;
    $menu_item->save();
}

// ---------------------- SITESTORE-DOCUMENT EXTENSIONS ------------------
//
//$select = $check_table->select()
//        ->from($check_name, array('id'))
//        ->where('name = ?', 'sitestoredocument_admin_main_manage');
//$queary_info = $select->query()->fetchAll();
//if (empty($queary_info)) {
//    $menu_item = $check_table->createRow();
//    $menu_item->name = 'sitestoredocument_admin_main_manage';
//    $menu_item->module = 'sitestoredocument';
//    $menu_item->label = 'Manage Store Documents';
//    $menu_item->plugin = '';
//    $menu_item->params = '{"route":"admin_default","module":"sitestoredocument","controller":"manage"}';
//    $menu_item->menu = 'sitestoredocument_admin_main';
//    $menu_item->submenu = '';
//    $menu_item->order = 2;
//    $menu_item->save();
//}
//
//$select = $check_table->select()
//        ->from($check_name, array('id'))
//        ->where('name = ?', 'sitestoredocument_admin_main_fields');
//$queary_info = $select->query()->fetchAll();
//if (empty($queary_info)) {
//    $menu_item = $check_table->createRow();
//    $menu_item->name = 'sitestoredocument_admin_main_fields';
//    $menu_item->module = 'sitestoredocument';
//    $menu_item->label = 'Store Document Questions';
//    $menu_item->plugin = '';
//    $menu_item->params = '{"route":"admin_default","module":"sitestoredocument","controller":"fields"}';
//    $menu_item->menu = 'sitestoredocument_admin_main';
//    $menu_item->submenu = '';
//    $menu_item->order = 4;
//    $menu_item->save();
//}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitestore_admin_main_package');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitestore_admin_main_package';
    $menu_item->module = 'sitestore';
    $menu_item->label = 'Manage Packages';
    $menu_item->plugin = '';
    $menu_item->params = '{"route":"admin_default","module":"sitestore","controller":"package","action":"index"}';
    $menu_item->menu = 'sitestore_admin_main';
    $menu_item->submenu = '';
    $menu_item->order = $adminTabOrdering++;
    $menu_item->save();
}
?>