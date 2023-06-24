<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$routeStartP = "storeitems";
$routeStartS = "storeitem";
$module=null;$controller=null;$action=null;
$request = Zend_Controller_Front::getInstance()->getRequest();
if (!empty($request)) {
  $module = $request->getModuleName(); // Return the current module name.
  $action = $request->getActionName();
  $controller = $request->getControllerName();
}
if (empty($request) || !($module == "default" && $controller == "sdk" && $action == "build")) {
  $routeStartP = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manifestUrlP', "stores");
  $routeStartS = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manifestUrlS', "store");
}
return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sitestore',
        'version' => '4.9.1p1',
        'path' => 'application/modules/Sitestore',
        'title' => 'Stores / Marketplace - Ecommerce',
        'description' => 'Stores / Marketplace - Ecommerce',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'date' => 'Thursday, 05 May 2011 18:33:08 +0000',
        'copyright' => 'Copyright 2012-2013 BigStep Technologies Pvt. Ltd.',
        'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Sitestore/settings/install.php',
            'class' => 'Sitestore_Installer',
        ),
        'directories' => array(
            'application/modules/Sitestore',
            'application/modules/Sitestoreproduct',            
            'application/modules/Sitestoreadmincontact',            
            'application/modules/Sitestorealbum',
            'application/modules/Sitestoreform',
            'application/modules/Sitestoreinvite',
            'application/modules/Sitestorelikebox',
            'application/modules/Sitestoreoffer',
            'application/modules/Sitestorereview',
            'application/modules/Sitestoreurl',
            'application/modules/Sitestorevideo'    
        ),
        'files' => array(
            'application/languages/en/sitestore.csv',
            'application/languages/en/sitestoreproduct.csv',
            'application/languages/en/sitestoreadmincontact.csv',
            'application/languages/en/sitestorealbum.csv',
            'application/languages/en/sitestoreform.csv',
            'application/languages/en/sitestoreinvite.csv',
            'application/languages/en/sitestorelikebox.csv',
            'application/languages/en/sitestoreoffer.csv',
            'application/languages/en/sitestorereview.csv',
            'application/languages/en/sitestoreurl.csv',
            'application/languages/en/sitestorevideo.csv',
        ),
    ),
     'sitemobile_compatible' => true,
// Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            // 'event' => 'addActivity',
            'event' => 'onRenderLayoutDefault',
            'resource' => 'Sitestore_Plugin_Core'
        ),
        array(
            'event' => 'onStatistics',
            'resource' => 'Sitestore_Plugin_Core'
        ),
        array(
            'event' => 'onUserDeleteBefore',
            'resource' => 'Sitestore_Plugin_Core',
        ),
        array(
            'event' => 'getActivity',
            'resource' => 'Sitestore_Plugin_Core',
        ),
        array(
            'event' => 'addActivity',
            'resource' => 'Sitestore_Plugin_Core',
        ),
        array(
					'event' => 'onActivityActionCreateAfter',
					'resource' => 'Sitestore_Plugin_Core',
			  ),
 			  array(
 					'event' => 'onUserSignupAfter',
 					'resource' => 'Sitestore_Plugin_Core',
 			  ),
    ),
// Items ---------------------------------------------------------------------
    'items' => array(
        'sitestore_store',
        'sitestore_album',
        'sitestore_photo','sitestore_category',
        'sitestore_topic',
        'sitestore_post',
        'sitestore_profilemap',
        'sitestore_planmap',
        'sitestore_claim',
        'sitestore_package',
        'sitestore_gateway', 
        'sitestore_location',
        'sitestore_listmemberclaims',
        'sitestore_gateway',
        'sitestore_itemofthedays',
        'sitestore_transaction',
				'sitestore_import',
				'sitestore_membership',
				'sitestore_announcements','sitestore_announcements','sitestore_list',
			  'sitestore_membership',
				'sitestore_importfile'
    ),
// Route--------------------------------------------------------------------
    'routes' => array(
        'sitestore_extended' => array(
            'route' => $routeStartP.'/:controller/:action/*',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'index',
                'action' => 'home',
            ),
            'reqs' => array(
                'controller' => '\D+',
                'action' => '\D+',
            )
        ),
        'sitestore_general' => array(
            'route' => $routeStartP.'/:action/*',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'index',
                'action' => 'home',
            ),
            'reqs' => array(
                'action' => '(home|index|manage|create|subcategory|storeurlvalidation|get-search-stores|subsubcategory|map|pinboard-browse|toggle-store-products-status)',
            ),
        ),

				'sitestore_claimstores' => array(
				            'route' => $routeStartP.'/claim/:action/*',
				            'defaults' => array(
				                'module' => 'sitestore',
				                'controller' => 'claim',
				                'action' => 'index',
				            ),
				            'reqs' => array(
				                'action' => '(claim-store|get-stores|terms|my-stores|delete)',

				            ),
				        ),
			'sitestore_store_dashboard' => array(
			            'route' => $routeStartP.'/dashboard/:action/:store_id/:type/:menuId/:method/*',
			            'defaults' => array(
			                'module' => 'sitestore',
			                'controller' => 'dashboard',
			                'action' => 'store',
			            ),
			            'reqs' => array(
			                'action' => '(store)',
                      'store_id' => '\d+',
                      'menuId' => '\d+',
			            ),
			        ), 
			'sitestore_dashboard' => array(
			            'route' => $routeStartP.'/dashboard/:action/*',
			            'defaults' => array(
			                'module' => 'sitestore',
			                'controller' => 'dashboard',
			                //'action' => 'index',
			            ),
			            'reqs' => array(
			                'action' => '(get-started|edit-style|edit-location|overview|edit-address|profile-type|marketing|foursquare-help|contact|featured-owners|profile-picture|remove-photo|unhide-photo|app|foursquare|favourite|favourite-delete|upload-photo|wishlist|twitter|announcements|notification-settings|add-location|delete-location|all-location|manage-member-category|delete-member-category|reset-position-cover-photo|upload-cover-photo|get-albums-photos|remove-cover-photo)',
			            ),
			        ),
			'sitestore_profilestore' => array(
			            'route' => $routeStartP.'/profilestore/:action/*',
			            'defaults' => array(
			                'module' => 'sitestore',
			                'controller' => 'profile',
			                //'action' => 'index',
			            ),
			            'reqs' => array(
			                'action' => '(message-owner|tell-a-friend|print|contact-detail|get-cover-photo|email-me)',	
			            ),
			        ),
			'sitestore_like' => array(
          'route' => $routeStartP.'/like/:action/*',
          'defaults' => array(
              'module' => 'sitestore',
              'controller' => 'like',
              //'action' => 'index',
          ),
          'reqs' => array(
              'action' => '(global-likes|like-stores|send-update|mylikes|my-joined)',
          ),
      ),

			'sitestore_packages' => array(
          'route' => $routeStartP.'/package/:action/*',
          'defaults' => array(
              'module' => 'sitestore',
              'controller' => 'package',
              'action' => 'index',
          ),
          'reqs' => array(
              'action' => '(detail|update-package|update-confirmation|cancel)',
          ),
      ),

			'sitestore_manageadmins' => array(
          'route' => $routeStartP.'/manageadmin/:action/*',
          'defaults' => array(
              'module' => 'sitestore',
              'controller' => 'manageadmin',
              'action' => 'index',
          ),
          'reqs' => array(
              'action' => '(my-stores|manage-auto-suggest|list|delete)',
          ),
      ),


        // Public
        'sitestore_entry_view' => array(
            'route' => $routeStartS.'/:store_url/*',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'index',
                'action' => 'view',
            ),
        ),
        // User

        'sitestore_delete' => array(
            'route' => $routeStartP.'/delete/:store_id/*',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'index',
                'action' => 'delete'
            ),
            'reqs' => array(
                'store_id' => '\d+',
            )
        ),
        'sitestore_publish' => array(
            'route' => $routeStartP.'/publish/:store_id/*',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'index',
                'action' => 'publish'
            ),
            'reqs' => array(
                'store_id' => '\d+'
            )
        ),
        'sitestore_close' => array(
            'route' => $routeStartP.'/close/:store_id/:closed/*',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'index',
                'action' => 'close'
            )
        ),
        'sitestore_edit' => array(
            'route' => $routeStartP.'/edit/:store_id/*',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'index',
                'action' => 'edit',
                'store_id' => '0',
            )
        ),
        'sitestore_session_payment' => array(
            'route' => $routeStartP.'/payment/sessionpayment/',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'package',
                'action' => 'payment',
            ),
        ),
        'sitestore_payment' => array(
            'route' => $routeStartP.'/payment/',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'payment',
                'action' => 'index',
            ),
        ),
        'sitestore_process_payment' => array(
            'route' => $routeStartP.'/payment/process',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'payment',
                'action' => 'process',
            ),
        ),
        'sitestore_insights' => array(
            'route' => $routeStartP.'/insights/:action/:store_id/*',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'insights',
                'action' => 'index',
                'store_id' => '0',
            )
        ),
        'sitestore_tags' => array(
            'route' => $routeStartP.'/tagscloud/:store/*',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'index',
                'action' => 'tags-cloud',
                'store' => 1
            )
        ),
        'sitestore_photoalbumupload' => array(
            'route' => $routeStartP.'/photo/:store_id/*',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'photo',
                'action' => 'upload-album',
                'store_id' => '1'
            )
        ),
        'sitestore_imagephoto_specific' => array(
            'route' => $routeStartP.'/photo/:action/*',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'photo',
                'action' => 'view'
            ),
            'reqs' => array(
                'action' => '(view|photo-edit|remove|make-store-profile-photo)',
            ),
        ),
        'sitestore_albumphoto_general' => array(
            'route' => $routeStartP.'/album/:action/*',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'album',
                'action' => 'view'
            ),
            'reqs' => array(
                'action' => '(edit|delete|edit-photos|view-album|view|album-order)',
            ),
        ),
        'sitestore_general_category' => array(
            'route' => $routeStartP.'/:category_id/:categoryname/*',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'category_id' => '\d+'
            ),
        ),
        'sitestore_general_subcategory' => array(
            'route' => $routeStartP.'/:category_id/:categoryname/:subcategory_id/:subcategoryname/*',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'category_id' => '\d+',
                'subcategory_id' => '\d+'

            ),
        ),

        'sitestore_general_subsubcategory' => array(
            'route' => $routeStartP.'/:category_id/:categoryname/:subcategory_id/:subcategoryname/:subsubcategory_id/:subsubcategoryname/*',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'category_id' => '\d+',
                'subcategory_id' => '\d+'

            ),
         ),

        'sitestore_layout' => array(
            'route' => $routeStartP.'/layout/:store_id/',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'layout',
                'action' => 'layout',
            ),
            'reqs' => array(
                'store_id' => '\d+',
            )
        ),
        'sitestore_ajaxhomelist' => array(
            'route' => $routeStartP.'/ajaxhomelist/',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'index',
                'action' => 'ajax-home-list'
            )
        ),
        'sitestore_reports' => array(
            'route' => $routeStartP.'/insights/:action/:store_id/*',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'insights',
                'action' => 'export-report',
                'store_id' => '0',
            )
        ),
        'sitestore_webstorereport' => array(
            'route' => $routeStartP.'/insights/:action/:store_id/*',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'insights',
                'action' => 'export-webstore',
                'store_id' => '0',
            )
        ),
        'sitestore_homesponsored' => array(
            'route' => $routeStartP.'/homesponsored',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'index',
                'action' => 'home-sponsored'
            )
        ),
        // User
        'sitestore_widget' => array(
            'route' => 'admin/storeitem/widgets/*',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'admin-widgets',
                'action' => 'index',
            )
        ),
        // User
        'sitestore_itemofday' => array(
            'route' => 'admin/storeitem/items/day/:store/*',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'admin-items',
                'action' => 'day',
                'store' => 1
            )
        ),
				'sitestore_profilestoremobile' => array(
            'route' => $routeStartS . '/profile/:action/*',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'profile',
            ),
            'reqs' => array(
                'action' => '(upload-cover-photo|get-albums-photos|remove-cover-photo)'
            ),
        ),
        'sitestore_viewmap' => array(
            'route' => $routeStartS.'/index/view-map/:id',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'index',
                'action' => 'view-map'
            )
        ),
    ),
);
?>
