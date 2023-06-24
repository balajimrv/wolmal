<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$module = null;
$controller = null;
$action = null;
$request = Zend_Controller_Front::getInstance()->getRequest();
$routes = array();
if (!empty($request)) {
    $module = $request->getModuleName();
    $action = $request->getActionName();
    $controller = $request->getControllerName();
}

if (empty($request) || !($module == "default" && $controller == "sdk" && $action == "build")) {
    $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
    $slug_plural = $coreSettingsApi->getSetting('sitestoreproduct.slugplural', 'products');
    $slug_singular = $coreSettingsApi->getSetting('sitestoreproduct.slugsingular', 'product');
    $store_slug_plural = $coreSettingsApi->getSetting('sitestore.manifestUrlP', "stores");
    $store_slug_singular = $coreSettingsApi->getSetting('sitestore.manifestUrlS', "seller");
}

if (empty($request) || !($module == "default" && $controller == "sdk" && $action == "build")) {

    $routes = array(
        'sitestoreproduct_manage' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/:action/:product_id/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'product',
                'action' => 'highlighted'
            ),
            'reqs' => array(
                'action' => '(highlighted|featured|enable-product|sponsored|highlighted-mobile|featured-mobile|sponsored-mobile)',
                'product_id' => '\d+'
            )
        ),
        'sitestoreproduct_product_general' => array(
            'route' => $store_slug_plural . '/' . $slug_singular . '/:action/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'product',
                'action' => 'view',
            ),
            'reqs' => array(
                'action' => '(manage|view|browse|edit|delete|my-products|cart|addto-cart|payment-to-me|payment-info|view-transaction-detail|view-payment-request|my-order|quick-view|upload-product|download-products|download|download-sample|notify-to-seller|bundle-product-attributes|tips-on-buying|transaction|your-bill|store-transaction|store-dashboard|set-minimum-shipping-cost|get-product-selling-price|show-product-specifications)',
            ),
        ),
        'sitestoreproduct_extended' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/:controller/:action/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'index',
                'action' => 'home',
            ),
            'reqs' => array(
                'controller' => '\D+',
                'action' => '\D+',
            )
        ),
        'sitestoreproduct_wishlist_general' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/wishlists/:action/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'wishlist',
                'action' => 'browse',
            ),
            'reqs' => array(
                'action' => '(browse|create|edit|add|cover-photo|delete|remove|print|tell-a-friend|message-owner|my-wishlists)',
            ),
        ),
        'sitestoreproduct_wishlist_view' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/wishlist/:wishlist_id/:slug/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'wishlist',
                'action' => 'profile',
                'slug' => '',
            ),
            'reqs' => array(
                'wishlist_id' => '\d+'
            )
        ),
        'sitestoreproduct_general' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/:action/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'index',
                'action' => 'home',
            ),
            'reqs' => array(
              'action' => '(home|delete|categories|list|index|create|create-mobile|manage|view|account|add-shipping-method|shipping-methods|edit-shipping-method|delete-shipping-method|order-view|order-ship|changestate|deletecountry|shipping-methods|manage-order|ajaxhomesitestoreproduct|tagscloud|get-search-products|sub-category|subsub-category|map|upload-photo|checkout|success|print-packing-slip|print-invoice|my-orders|process|table-rate-enable|manage-address|show-tooltip-info|edit-shipment|delete-shipment|order-products|product-code-validation|success|payment|pinboard|startup|product-type-details|get-started|basic|stories|tools|sections|terms-and-conditions|copy-product|add-minimum-shipping-cost|show-radius-tip|delete-mobile|copy-product-mobile)',
            ),
        ),
        'sitestoreproduct_import_general' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/import/:action/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'import',
                'action' => 'index',
            ),
        ),
        'sitestoreproduct_export_general' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/export/:action/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'export',
                'action' => 'index',
            ),
        ),
        'sitestoreproduct_tax_general' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/tax/:action/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'tax',
                'action' => 'home',
            ),
            'reqs' => array(
                'action' => '(index)',
            ),
        ),
        'sitestoreproduct_statistics_general' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/statistics/:action/:store_id/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'statistics',
                'action' => 'index',
                'store_id' => '0',
            )
        ),
        'sitestoreproduct_report_general' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/report/:action/:store_id/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'report',
                'action' => 'index',
                'store_id' => '0',
            ),
            'reqs' => array(
                'action' => '(index|export-webpage|export-excel)',
            ),
        ),
        'sitestoreproduct_editor_general' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/editors/:action/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'editor',
                'action' => 'home',
            ),
            'reqs' => array(
                'action' => '(home|similar-items|add-items|categories|editor-mail)',
            ),
        ),
        'sitestoreproduct_specific' => array(
            'route' => $store_slug_plural . '/' . $slug_singular . '/:action/:product_id/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'index',
                'action' => 'view',
            ),
            'reqs' => array(
                'action' => '(messageowner|tellafriend|ask-opinion|print|delete|publish|close|edit|overview|editstyle|editaddress)',
                'product_id' => '\d+',
            )
        ),
        'sitestoreproduct_dashboard' => array(
            'route' => $store_slug_plural . '/' . $slug_singular . '/:action/:product_id/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'dashboard',
            ),
            'reqs' => array(
                'action' => '(contact|change-photo|remove-photo|meta-detail|product-history|product-document|create-document|edit-document|download-document|editlocation|editaddress)',
                'product_id' => '\d+',
            )
        ),
        'sitestoreproduct_tag' => array(
            'route' => $store_slug_plural . '/' . $slug_singular . '/:action/:product_id/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'printing-tag',
            ),
            'reqs' => array(
                'action' => '(print-tag|manage|show-products)',
                'product_id' => '\d+',
            )
        ),
        'sitestoreproduct_files' => array(
            'route' => $store_slug_plural . '/' . $slug_singular . '/:action/:product_id/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'files'
            ),
            'reqs' => array(
                'action' => '(index|sample|upload-file|multi-delete|edit-file|delete-file|download)',
                'product_id' => '\d+',
            )
        ),
        'sitestoreproduct_entry_view' => array(
            'route' => $store_slug_plural . '/' . $slug_singular . '/:product_id/:slug/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'index',
                'action' => 'view',
                'slug' => ''
            ),
            'reqs' => array(
                'product_id' => '\d+'
            )
        ),
        'sitestoreproduct_image_specific' => array(
            'route' => $store_slug_plural . '/' . $slug_singular . '/photo/view/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'photo',
                'action' => 'view',
            ),
            'reqs' => array(
                'action' => '(view|remove)',
            ),
        ),
        'sitestoreproduct_photo_extended' => array(
            'route' => $store_slug_plural . '/' . $slug_singular . '/photo/:action/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'photo',
                'action' => 'edit',
            ),
            'reqs' => array(
                'action' => '\D+',
            )
        ),
        'sitestoreproduct_photoalbumupload' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/photo/:product_id/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'photo',
                'action' => 'upload',
                'product_id' => '0',
            )
        ),
        'sitestoreproduct_albumspecific' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/album/:action/:product_id/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'album',
                'action' => 'editphotos',
            ),
            'reqs' => array(
                'action' => '(compose-upload|delete|edit|editphotos|upload|view)',
            ),
        ),
        'sitestoreproduct_videospecific' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/videos/:action/:product_id/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'videoedit',
                'action' => 'edit',
            ),
            'reqs' => array(
                'action' => '(compose-upload|delete|edit|editphotos|upload|view)',
            ),
        ),
        'sitestoreproduct_video_upload' => array(
            'route' => $store_slug_plural . '/' . $slug_singular . '/video/:action/:product_id/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'video',
                'action' => 'index',
                'product_id' => '0',
            ),
        ),
        'sitestoreproduct_general_category' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/browse-category/:categoryname/:category_id',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'category_id' => '\d+',
            ),
        ),
        'sitestoreproduct_category_home' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/category/:categoryname/:category_id',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'index',
                'action' => 'category-home',
            ),
            'reqs' => array(
                'category_id' => '\d+',
            ),
        ),
        'sitestoreproduct_general_subcategory' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/category/:categoryname/:category_id/:subcategoryname/:subcategory_id',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'category_id' => '\d+',
                'subcategory_id' => '\d+',
            ),
        ),
        'sitestoreproduct_general_subsubcategory' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/category/:categoryname/:category_id/:subcategoryname/:subcategory_id/:subsubcategoryname/:subsubcategory_id',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'category_id' => '\d+',
                'subcategory_id' => '\d+',
                'subsubcategory_id' => '\d+',
            ),
        ),
        'sitestoreproduct_review_general' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/review/:action/product_id/:product_id/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'review',
            ),
            'reqs' => array(
                'product_id' => '\d+',
                'action' => '(create|edit|update|reply|helpful|email|delete)'
            ),
        ),
        'sitestoreproduct_view_review' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/review/:action/:review_id/:product_id/:slug/:tab/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'review',
                'action' => 'view',
                'slug' => '',
                'tab' => ''
            ),
            'reqs' => array(
                'review_id' => '\d+',
                'product_id' => '\d+'
            ),
        ),
        'sitestoreproduct_video_general' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/video/:action/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'video',
                'action' => 'view',
            ),
            'reqs' => array(
                'action' => '(index|create)',
            )
        ),
        'sitestoreproduct_video_view' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/video/:product_id/:user_id/:video_id/:slug/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'video',
                'action' => 'view',
                'slug' => '',
            ),
            'reqs' => array(
                'user_id' => '\d+'
            )
        ),
        'sitestoreproduct_video_create' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/video/create/:product_id/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'video',
                'action' => 'create',
            ),
            'reqs' => array(
                'product_id' => '\d+'
            )
        ),
        'sitestoreproduct_video_edit' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/video/edit/:product_id/:video_id/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'video',
                'action' => 'edit',
            )
        ),
        'sitestoreproduct_video_embed' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/videos/embed/:id/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'video',
                'action' => 'embed',
            )
        ),
        'sitestoreproduct_video_delete' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/video/delete/:product_id/:video_id/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'video',
                'action' => 'delete',
            ),
            'reqs' => array(
                'video_id' => '\d+',
                'product_id' => '\d+'
            )
        ),
        'sitestoreproduct_video_general' => array(
            'route' => 'product-videos/:action/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'video',
                'action' => 'browse',
            ),
            'reqs' => array(
                'action' => '(index|browse)',
            )
        ),
        'sitestoreproduct_downloads' => array(
            'route' => $slug_singular . '/download/:product_id/:downloadablefile_id/:download_id/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'product',
                'action' => 'download',
            )
        ),
        'sitestoreproduct_review_browse' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/reviews/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'review',
                'action' => 'browse'
            ),
        ),
        'sitestoreproduct_review_categories' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/categories/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'index',
                'action' => 'categories'
            ),
        ),
        'sitestoreproduct_compare' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/compare/*',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'compare',
                'action' => 'compare'
            ),
            'reqs' => array(
                'id' => '\d+',
            ),
        ),
        'sitestoreproduct_review_editor_profile' => array(
            'route' => $store_slug_plural . '/' . $slug_plural . '/editor/profile/:username/:user_id',
            'defaults' => array(
                'module' => 'sitestoreproduct',
                'controller' => 'editor',
                'action' => 'profile',
            ),
            'reqs' => array(
                'user_id' => '\d+'
            )
        ),

    
          );
}

return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sitestoreproduct',
        'version' => '-',
        'path' => 'application/modules/Sitestoreproduct',
        'title' => '<i><span style="color:#999999">Stores / Marketplace - Ecommerce Products Extension</span></i>',
        'description' => '<i><span style="color:#999999">Stores / Marketplace - Ecommerce Products Extension</span></i>',
        'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Sitestoreproduct/settings/install.php',
            'class' => 'Sitestoreproduct_Installer',
        ),
        'directories' => array(
            'application/modules/Sitestoreproduct',
        ),
        'files' => array(
            'application/languages/en/sitestoreproduct.csv',
        ),
    ),
    //'sitemobile_compatible' =>true,
    //Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onStatistics',
            'resource' => 'Sitestoreproduct_Plugin_Core'
        ),
        array(
            'event' => 'onRenderLayoutDefault',
            'resource' => 'Sitestoreproduct_Plugin_Core'
        ),
//         array(
//            'event' => 'onRenderLayoutMobileSMDefault',
//            'resource' => 'Sitestoreproduct_Plugin_Sitemobile'
//        ),
        array(
            'event' => 'onUserDeleteBefore',
            'resource' => 'Sitestoreproduct_Plugin_Core',
        ),
        array(
            'event' => 'onItemDeleteBefore',
            'resource' => 'Sitestoreproduct_Plugin_Core',
        ),
    ),
    //Items ---------------------------------------------------------------------
    'items' => array(
        'sitestoreproduct_clasfvideo',
        'sitestoreproduct_product',
        'sitestoreproduct_album',
        'sitestoreproduct_photo',
        'sitestoreproduct_review',
        'sitestoreproduct_topic',
        'sitestoreproduct_post',
        'sitestoreproduct_import',
        'sitestoreproduct_importfile',
        'sitestoreproduct_category',
        'sitestoreproduct_profilemap',
        'sitestoreproduct_ratingparam',
        'sitestoreproduct_wishlist',
        'sitestoreproduct_badge',
        'sitestoreproduct_editor',
        'sitestoreproduct_video',
        'sitestoreproduct_order',
        'sitestoreproduct_shippingmethod',
        'sitestoreproduct_shippingregion',
        'sitestoreproduct_region',
        'sitestoreproduct_taxes',
        'sitestoreproduct_taxrate',
        'sitestoreproduct_addresse',
        'sitestoreproduct_gateway',
        'sitestoreproduct_usergateway',
        'sitestoreproduct_storegateway',
        'sitestoreproduct_paymentrequest',
        'sitestoreproduct_paymentreq',
        'sitestoreproduct_storepaypalbill',
        'sitestoreproduct_storebill',
        'sitestoreproduct_transaction',
        'sitestoreproduct_orderaddress',
        'sitestoreproduct_shippingtracking',
        'sitestoreproduct_cartproduct',
        'sitestoreproduct_cart',
        'sitestoreproduct_downloadablefile',
        'sitestoreproduct_orderdownload',
        'sitestoreproduct_startuppage',
        'sitestoreproduct_printingtag',
        'sitestoreproduct_tagmapping',
        'sitestoreproduct_document',
        'sitestoreproduct_orderdownpayment'
    ),
    //Route--------------------------------------------------------------------
    'routes' => $routes,
);
