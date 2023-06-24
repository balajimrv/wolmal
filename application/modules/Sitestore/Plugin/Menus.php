<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Plugin_Menus {

    public function showSitestore($row) {
        $params = $row->params;
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $subject = Engine_Api::_()->core()->getSubject();
        if (!empty($viewer_id) && !empty($subject)) {
            return array(
                'class' => $params['class'],
                'route' => $params['route'],
                'action' => 'edit',
                'params' => array(
                    'product_id' => $subject->getIdentity(),
                    'modName' => 'sitestore',
                    'modContentId' => $subject->getIdentity(),
                    'modError' => 1
                ),
            );
        }
    }

    public function showSitestoreproduct($row) {
        $params = $row->params;
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $subject = Engine_Api::_()->core()->getSubject();
        if (!empty($viewer_id) && !empty($subject)) {
            return array(
                'class' => $params['class'],
                'route' => $params['route'],
                'action' => 'edit',
                'params' => array(
                    'product_id' => $subject->getIdentity(),
                    'modName' => 'sitestoreproduct',
                    'modContentId' => $subject->getIdentity(),
                    'modError' => 1
                ),
            );
        }
    }

    public function canCreateSitestores() {

        // Must be logged in
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer || !$viewer->getIdentity()) {
            return false;
        }

        // Must be able to view Sitestores
        if (!Engine_Api::_()->authorization()->isAllowed('sitestore_store', $viewer, 'view')) {
            return false;
        }

        // Must be able to create Sitestores
        if (!Engine_Api::_()->authorization()->isAllowed('sitestore_store', $viewer, 'create')) {
            return false;
        }
        return true;
    }

//  public function onMenuInitialize_SitestoreSubstoreGutterCreate($row) {
//
//    // Must be logged in
//    $viewer = Engine_Api::_()->user()->getViewer();
//    if (!$viewer || !$viewer->getIdentity()) {
//      return false;
//    }
//    $subject = Engine_Api::_()->core()->getSubject();
//    if ($subject->getType() !== 'sitestore_store') {
//      return false;
//    }
//    
//    if (!empty($subject->substore)) {
//			return false;
//    }
//    
//    // Must be able to view Sitestores
//    if (!Engine_Api::_()->authorization()->isAllowed('sitestore_store', $viewer, 'view')) {
//      return false;
//    }
//
//    $substoreCreate = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'sspcreate');
//    if (empty($substoreCreate) ){
//			return false;
//    }
//    
//		$isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($subject, 'sspcreate');
//		if (empty($isStoreOwnerAllow)) {
//			return false;
//		}
//		
//		if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
//			return array(
//        'label' => $row->label,
//        'route' => 'sitestore_packages',
//        'action' => 'index',
//        'class' => 'buttonlink item_icon_sitestore',
//        'params' => array(
//          'parent_id' =>  $subject->getIdentity()
//        ),
//      );
//		} else {
//			return array(
//        'label' => $row->label,
//        'route' => 'sitestore_general',
//        'action' => 'create',
//        'class' => 'buttonlink item_icon_sitestore',
//        'params' => array(
//          'parent_id' =>  $subject->getIdentity()
//        ),
//      );
//		}
//  }

    public function canViewSitestores($row) {

        $viewer = Engine_Api::_()->user()->getViewer();

        // Must be able to view Sitestores
        if (!Engine_Api::_()->authorization()->isAllowed('sitestore_store', $viewer, 'view')) {
            return false;
        }

//     $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.locationfield', 1);
//     if (empty($enableLocation)) { 
// 			return false;
// 	  }
        //Store location work for navigation show.
//     if ($row->params['route'] == 'sitestore_general' && $row->params['action'] == 'map') {
// 			$results = Engine_Api::_()->getDbtable('stores', 'sitestore')->getLocationCount();
// 			if (empty($results)) {
// 				return false;
// 			}
// 	  }
        //End Store location work.

        return true;
    }

    // SHOWING LINK ON "USER HOME STORE".
    public function onMenuInitialize_CoreMainSitestore($row) {

        $viewer = Engine_Api::_()->user()->getViewer()->getIdentity();
        if (!empty($viewer)) {
            return array(
                'label' => $row->label,
                'icon' => Zend_Registry::get('Zend_View')->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore.png',
                'route' => 'sitestore_general',
            );
        }
        return false;
    }

    public function onMenuInitialize_SitestoreGutterShare() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        if ($subject->getType() !== 'sitestore_store') {
            return false;
        }

        // Check share is enable/disable
        $can_share = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.share', 1);

        if (!$viewer->getIdentity() || empty($can_share)) {
            return false;
        }

        return array(
            'class' => 'smoothbox icon_sitestores_share buttonlink',
            'route' => 'default',
            'params' => array(
                'module' => 'activity',
                'controller' => 'index',
                'action' => 'share',
                'type' => $subject->getType(),
                'id' => $subject->getIdentity(),
                'format' => 'smoothbox',
            ),
        );
    }

    public function onMenuInitialize_SitestoreGutterDelete() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        if ($subject->getType() !== 'sitestore_store') {
            return false;
        }

        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'delete');
        if (empty($isManageAdmin)) {
            $can_delete = 0;
        } else {
            $can_delete = 1;
        }

        if (!$viewer->getIdentity() || empty($can_delete)) {
            return false;
        }

        return array(
            'class' => 'buttonlink icon_sitestores_delete',
            'route' => 'sitestore_delete',
            'params' => array(
                'store_id' => $subject->getIdentity(),
            ),
        );
    }

    public function onMenuInitialize_SitestoreGutterPublish() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        if ($subject->getType() !== 'sitestore_store') {
            return false;
        }

        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'edit');
        if (empty($isManageAdmin)) {
            $can_edit = 0;
        } else {
            $can_edit = 1;
        }

        if (!$viewer->getIdentity() || empty($can_edit) || $subject->draft == 1) {
            return false;
        }

        return array(
            'class' => 'buttonlink smoothbox icon_sitestore_publish',
            'route' => 'sitestore_publish',
            'params' => array(
                'store_id' => $subject->getIdentity(),
            ),
        );
    }

    public function onMenuInitialize_SitestoreGutterPrint() {
        $subject = Engine_Api::_()->core()->getSubject();
        if ($subject->getType() !== 'sitestore_store') {
            return false;
        }

        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'print');
        if (empty($isManageAdmin)) {
            $can_print = 0;
        } else {
            $can_print = 1;
        }

        if (empty($can_print)) {
            return false;
        }

        return array(
            'class' => 'buttonlink icon_sitestores_print',
            'target' => '_blank',
            'route' => 'sitestore_profilestore',
            'params' => array(
                'action' => 'print',
                'id' => $subject->getIdentity(),
            ),
        );
    }

    public function onMenuInitialize_SitestoreGutterTfriend() {
        $subject = Engine_Api::_()->core()->getSubject();
        if ($subject->getType() !== 'sitestore_store') {
            return false;
        }

        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'tfriend');
        if (empty($isManageAdmin)) {
            $can_tellfriend = 0;
        } else {
            $can_tellfriend = 1;
        }

        if (empty($can_tellfriend)) {
            return false;
        }
        $class = 'smoothbox buttonlink icon_sitestores_tellafriend';
        $sitemobile = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemobile');
        if ($sitemobile && !Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode'))
            $class = 'buttonlink icon_sitestores_tellafriend';

        return array(
            'class' => $class,
            'route' => 'sitestore_profilestore',
            'params' => array(
                'action' => 'tell-a-friend',
                'id' => $subject->getIdentity(),
            ),
        );
    }

    public function onMenuInitialize_SitestoreGutterClaim() {
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!Engine_Api::_()->authorization()->isAllowed('sitestore_store', $viewer, 'view')) {
            return false;
        }
        $viewer_id = $viewer->getIdentity();

        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $authorizationTable = Engine_Api::_()->getItemTable('authorization_level');
            $authorization = $authorizationTable->fetchRow(array('type = ?' => 'public', 'flag = ?' => 'public'));
            if (!empty($authorization))
                $level_id = $authorization->level_id;
        }

        $allow_claim = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestore_store', 'claim');
        if (empty($allow_claim)) {
            return false;
        }
        $subject = Engine_Api::_()->core()->getSubject();
        if ($subject->getType() !== 'sitestore_store') {
            return false;
        }

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.claimlink', 1)) {
            return false;
        }

        $listmemberclaimsTable = Engine_Api::_()->getDbtable('listmemberclaims', 'sitestore');
        $listmemberclaimsTablename = $listmemberclaimsTable->info('name');
        $select = $listmemberclaimsTable->select()->from($listmemberclaimsTablename, array('count(*) as total_count'))
                ->where('user_id = ?', $subject->owner_id);
        $row = $listmemberclaimsTable->fetchAll($select);

        if (!empty($row[0]['total_count'])) {
            $total_count = 1;
        }

        if (empty($total_count) || $subject->owner_id == $viewer_id || empty($subject->userclaim) || empty($allow_claim)) {
            return false;
        }

        return array(
            'class' => 'smoothbox buttonlink icon_sitestores_claim',
            'route' => 'sitestore_claimstores',
            'params' => array(
                'action' => 'claim-store',
                'store_id' => $subject->getIdentity(),
            ),
        );
    }

    public function onMenuInitialize_SitestoreGutterMessageowner() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $subject = Engine_Api::_()->core()->getSubject();
        if ($subject->getType() !== 'sitestore_store') {
            return false;
        }

        if ($subject->owner_id == $viewer_id || empty($viewer_id)) {
            return false;
        }

        $showMessageOwner = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
        if ($showMessageOwner == 'none') {
            return false;
        }

        return array(
            'class' => 'buttonlink smoothbox icon_sitestores_invite',
            'route' => 'sitestore_profilestore',
            'params' => array(
                'action' => 'message-owner',
                'store_id' => $subject->getIdentity(),
            ),
        );
    }

    public function onMenuInitialize_SitestoreGutterOpen() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        if ($subject->getType() !== 'sitestore_store') {
            return false;
        }

        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'edit');
        if (empty($isManageAdmin)) {
            $can_edit = 0;
        } else {
            $can_edit = 1;
        }

        if (!$viewer->getIdentity() || $subject->closed != 1 || empty($can_edit)) {
            return false;
        }

        return array(
            'class' => 'smoothbox buttonlink icon_sitestores_open',
            'route' => 'sitestore_general',
            'action' => 'toggle-store-products-status',
            'params' => array(
                'store_id' => $subject->getIdentity(),
                'closed' => 0,
            ),
        );
    }

    public function onMenuInitialize_SitestoreGutterClose() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        if ($subject->getType() !== 'sitestore_store') {
            return false;
        }

        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'edit');
        if (empty($isManageAdmin)) {
            $can_edit = 0;
        } else {
            $can_edit = 1;
        }

        if (!$viewer->getIdentity() || $subject->closed != 0 || empty($can_edit)) {
            return false;
        }

        return array(
            'class' => 'smoothbox buttonlink icon_sitestores_close',
            'route' => 'sitestore_general',
            'action' => 'toggle-store-products-status',
            'params' => array(
                'store_id' => $subject->getIdentity(),
                'closed' => 1,
            ),
        );
    }

    public function onMenuInitialize_SitestoreGutterReport() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        if ($subject->getType() !== 'sitestore_store') {
            return false;
        }

        $report = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.report', 1);

        if (!$viewer->getIdentity() || empty($report)) {
            return false;
        }

        return array(
            'class' => 'smoothbox icon_sitestores_report buttonlink',
            'route' => 'default',
            'params' => array(
                'module' => 'core',
                'controller' => 'report',
                'action' => 'create',
                'subject' => $subject->getGuid(),
                'format' => 'smoothbox',
            ),
        );
    }

    public function onMenuInitialize_SitestoreGutterEditdetail($row) {
        if (!Engine_Api::_()->core()->hasSubject()) {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
        if (empty($isManageAdmin)) {
            return false;
        }
        //END MANAGE-ADMIN CHECK
        // Modify params
        $params = $row->params;
        $params['params']['store_id'] = $sitestore->getIdentity();
        return $params;
    }

    public function onMenuInitialize_SitestoreGutterEditoverview($row) {
        if (!Engine_Api::_()->core()->hasSubject()) {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
        if (empty($isManageAdmin)) {
            return false;
        }

        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'overview');
        if (empty($isManageAdmin)) {
            return false;
        }
        //END MANAGE-ADMIN CHECK
        // Modify params
        $params = $row->params;
        $params['params']['store_id'] = $sitestore->getIdentity();
        return $params;
    }

    public function onMenuInitialize_SitestoreGutterEditstyle($row) {

        if (!Engine_Api::_()->core()->hasSubject()) {
            return false;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
        if (empty($isManageAdmin)) {
            return false;
        }
        //END MANAGE-ADMIN CHECK
        // Modify params
        $params = $row->params;
        $params['params']['store_id'] = $sitestore->getIdentity();
        return $params;
    }

    public function onMenuInitialize_SitestoreGutterEditlayout($row) {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer || !$viewer->getIdentity()) {
            return false;
        }
        if (!Engine_Api::_()->core()->hasSubject()) {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
        if (empty($isManageAdmin)) {
            return false;
        }
        //END MANAGE-ADMIN CHECK

        $check = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);

        // Modify params
        $params = $row->params;
        $params['params']['store_id'] = $sitestore->getIdentity();

        if (!empty($check)) {
            return $params;
        }
    }

    public function onMenuInitialize_SitestoreMainClaim($row) {
        $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
        // Modify params
        $params = $row->params;
        return $params;
    }

    public function canViewClaims() {
        $viewer = Engine_Api::_()->user()->getViewer();
        //Must be able to view Sitestores
        if (!Engine_Api::_()->authorization()->isAllowed('sitestore_store', $viewer, 'view')) {
            return false;
        }


        $viewer_id = $viewer->getIdentity();
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $authorizationTable = Engine_Api::_()->getItemTable('authorization_level');
            $authorization = $authorizationTable->fetchRow(array('type = ?' => 'public', 'flag = ?' => 'public'));
            if (!empty($authorization))
                $level_id = $authorization->level_id;
        }

        $allow_claim = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestore_store', 'claim');

        if (!Engine_Api::_()->getApi('settings', 'core')->sitestore_claimlink || empty($allow_claim)) {
            return false;
        }

        $table = Engine_Api::_()->getDbtable('stores', 'sitestore');
        $tablename = $table->info('name');
        $select = $table->select()->from($tablename, array('count(*) as count'))->where($tablename . '.closed = ?', '0')
                ->where($tablename . '.approved = ?', '1')
                ->where($tablename . '.declined = ?', '0')
                ->where($tablename . '.draft = ?', '1');
        if (Engine_Api::_()->sitestore()->hasPackageEnable())
            $select->where($tablename . '.expiration_date  > ?', date("Y-m-d H:i:s"));
        $results = $table->fetchAll($select);
        if (!$results[0]['count']) {
            return false;
        }
        return true;
    }

    // START FOR PROMOTE WITH AN AD LINK
    public function onMenuInitialize_SitestoreGutterPromotead($row) {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!Engine_Api::_()->core()->hasSubject()) {
            return false;
        }

        // check if Communityad Plugin is enabled
        $sitestorecommunityadEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
        if (!$sitestorecommunityadEnabled) {
            return false;
        }

        // check if it is upgraded version
        $communityadmodulemodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('communityad');
        $adversion = $communityadmodulemodule->version;
        if ($adversion < '4.1.5') {
            return;
        }

        $sitestore = Engine_Api::_()->core()->getSubject();
        $ismoduleads_enabled = Engine_Api::_()->getDbtable('modules', 'communityad')->ismoduleads_enabled('sitestore');
        if (!$ismoduleads_enabled) {
            return false;
        }

        $useradsTable = Engine_Api::_()->getDbtable('userads', 'communityad');
        $useradsName = $useradsTable->info('name');

        $select = $useradsTable->select();
        $select
                ->from($useradsName, array('userad_id'))
                ->where('resource_type = ?', 'sitestore')
                ->where('resource_id = ?', $sitestore->store_id)
                ->limit(1);
        $ad_exist = $useradsTable->fetchRow($select);
        if (!empty($ad_exist)) {
            return false;
        }

        //START OWNER CHECK
        $isOwner = Engine_Api::_()->sitestore()->isStoreOwner($sitestore);
        if (!$isOwner) {
            return false;
        }
        //END OWNER CHECK
        // Modify params
        $params = $row->params;
        $params['params']['type'] = 'sitestore';
        $params['params']['type_id'] = $sitestore->getIdentity();
        return $params;
    }

    // START FOR ADD fAVOURITE
    public function onMenuInitialize_SitestoreGutterFavourite($row) {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!$viewer || !$viewer->getIdentity()) {
            return false;
        }

        //FOR SHOW ADD FAVOURITE LINK ON THE STORE PROFILE STORE
        $show_link = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.addfavourite.show', 0);
        if (empty($show_link)) {
            return false;
        }

        if (!Engine_Api::_()->core()->hasSubject()) {
            return false;
        }

        $sitestore = Engine_Api::_()->core()->getSubject();
        $this->view->sitestore = $sitestore;
        $owner_id = $sitestore->owner_id;

        //START MANAGE-ADMIN CHECK
//     $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
//     if (empty($isManageAdmin)) {
//       return false;
//     }
        //END MANAGE-ADMIN CHECK

        $store_id = $sitestore->store_id;
        $table = Engine_Api::_()->getDbtable('stores', 'sitestore');
        $tablename = $table->info('name');

        $select = $table->select()->from($tablename, array('count(*) as count'))
                ->where('owner_id = ?', $viewer_id)
                ->where($tablename . '.store_id <> ?', $store_id)
                //->where($tablename . '.owner_id  <> ?', $viewer_id)
                ->where($tablename . '.approved = 1')
                ->where($tablename . '.draft = ?', '1')
                //->store($tablename . '.owner_id')
                ->where($tablename . '.closed = ?', '0');
        $results = $table->fetchRow($select);
        $count = $results->count;

        if ($count < 1) {
            return false;
        }

        $check = Engine_Api::_()->getDbtable('favourites', 'sitestore')->isShow($sitestore->getIdentity());
        $table_favourites = Engine_Api::_()->getDbtable('favourites', 'sitestore');
        $tablename = $table->info('name');

        $select_content = $table_favourites->select()->where('owner_id = ?', $viewer_id);
        $content = $select_content->query()->fetchAll();

        if (!empty($content)) {
            //Started the select query
            $select = $table->select()
                    ->from($tablename, 'store_id')
                    ->where($tablename . '.store_id <> ?', $store_id)
                    ->where($tablename . '.owner_id  =?', $viewer_id)
                    ->where($tablename . '.approved = 1')
                    ->where($tablename . '.draft = ?', '1')
                    ->where($tablename . '.closed = ?', '0')
                    ->where('NOT EXISTS (SELECT `store_id` FROM `engine4_sitestore_favourites` WHERE `store_id_for`=' . $store_id . ' AND `store_id` = ' . $tablename . '.`store_id`) ');
            $content_result = $select->query()->fetchAll();
            $count_result1 = count($content_result);

            if (($count_result1 == 0)) {
                return false;
            }
        }

        // Modify params
        $params = $row->params;
        $params['params']['store_id'] = $sitestore->getIdentity();

        if (!empty($check)) {
            return $params;
        } else {
            return $params;
        }
    }

    // END FOR ADD fAVOURITE
    // START FOR DELETE fAVOURITE
    public function onMenuInitialize_SitestoreGutterFavouritedelete($row) {

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer || !$viewer->getIdentity()) {
            return false;
        }

        if (!Engine_Api::_()->core()->hasSubject()) {
            return false;
        }
        $viewer_id = $viewer->getIdentity();


        $viewer = Engine_Api::_()->user()->getViewer();
        $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');

        $check = Engine_Api::_()->getDbtable('favourites', 'sitestore')->isnotShow($sitestore->getIdentity());

        // Modify params
        $params = $row->params;
        $params['params']['store_id'] = $sitestore->getIdentity();

        if (!empty($check)) {
            return $params;
        }
    }

    // END FOR DELETE fAVOURITE
    //ADD TO WISHLIST LINK
    public function onMenuInitialize_SitestoreGutterWishlist($row) {

        //GET VIEWER DETAIL
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id)) {
            return false;
        }

        $canView = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestorewishlist_wishlist', 'view');
        if (empty($canView)) {
            return false;
        }

        //RETURN FALSE IF SUBJECT IS NOT SET
        $subject = Engine_Api::_()->core()->getSubject();
        if ($subject->getType() !== 'sitestore_store') {
            return false;
        }

        //SHOW ADD TO WISHLIST LINK IF SITEPAGWISHLIST MODULES IS ENABLED
        $sitestoreWishlistEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorewishlist');
        if (!$viewer->getIdentity() || empty($sitestoreWishlistEnabled)) {
            return false;
        }

        return array(
            'class' => 'icon_sitestorewishlist_add buttonlink',
            'route' => 'default',
            'params' => array(
                'module' => 'sitestorewishlist',
                'controller' => 'index',
                'action' => 'add',
                'store_id' => $subject->getIdentity(),
            ),
        );
    }

    public function onMenuInitialize_SitestoreSitegroupGutterCreate($row) {

        if (!Engine_Api::_()->core()->hasSubject()) {
            return false;
        }

        $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration');
        $sitegroupmoduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup');
        if (empty($moduleEnabled) || (empty($sitegroupmoduleEnabled))) {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');

        $item_enabled = Engine_Api::_()->getDbtable('mixsettings', 'sitestoreintegration')->getItemsEnabled('sitegroup_group', 0);
        if (empty($item_enabled)) {
            return false;
        }

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
        if (empty($isManageAdmin)) {
            return false;
        }

        if (!Engine_Api::_()->authorization()->isAllowed('sitegroup_group', $viewer, 'create')) {
            return false;
        }

        if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
            return array(
                'label' => $row->label,
                'route' => 'sitegroup_packages',
                'action' => 'index',
                'class' => 'buttonlink item_icon_sitegroup',
                'params' => array(
                    'store_id' => $sitestore->store_id
                ),
            );
        } else {
            return array(
                'label' => $row->label,
                'route' => 'sitegroup_general',
                'action' => 'create',
                'class' => 'buttonlink item_icon_sitegroup',
                'params' => array(
                    'store_id' => $sitestore->store_id
                ),
            );
        }
    }

    public function onMenuInitialize_SitestoreDocumentGutterCreate($row) {

        if (!Engine_Api::_()->core()->hasSubject()) {
            return false;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
        $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration');
        $listmoduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document');
        if (empty($moduleEnabled) || (empty($listmoduleEnabled))) {
            return false;
        }

        $item_enabled = Engine_Api::_()->getDbtable('mixsettings', 'sitestoreintegration')->getItemsEnabled('document', 0);
        if (empty($item_enabled)) {
            return false;
        }

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
        if (empty($isManageAdmin)) {
            return false;
        }

        if (!Engine_Api::_()->authorization()->isAllowed('document', $viewer, 'create')) {
            return false;
        }

        // Modify params
        $params = $row->params;
        $params['params']['store_id'] = $sitestore->getIdentity();
        return $params;
    }

    public function onMenuInitialize_SitestoreSitepageGutterCreate($row) {

        if (!Engine_Api::_()->core()->hasSubject()) {
            return false;
        }

        $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration');
        if (empty($moduleEnabled)) {
            return false;
        }

        $sitepagemoduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage');
        if (empty($sitepagemoduleEnabled)) {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');

        $item_enabled = Engine_Api::_()->getDbtable('mixsettings', 'sitestoreintegration')->getItemsEnabled('sitepage_page', 0);
        if (empty($item_enabled)) {
            return false;
        }
        if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
            if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitepage_page")) {
                return false;
            }
        } else {
            $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'sitepage_page');
            if (empty($isStoreOwnerAllow)) {
                return false;
            }
        }
        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
        if (empty($isManageAdmin)) {
            return false;
        }

        if (!Engine_Api::_()->authorization()->isAllowed('sitepage_page', $viewer, 'create')) {
            return false;
        }

        if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
            return array(
                'label' => $row->label,
                'route' => 'sitepage_packages',
                'action' => 'index',
                'class' => 'buttonlink item_icon_sitepage',
                'params' => array(
                    'store_id' => $sitestore->store_id
                ),
            );
        } else {
            return array(
                'label' => $row->label,
                'route' => 'sitepage_general',
                'action' => 'create',
                'class' => 'buttonlink item_icon_sitepage',
                'params' => array(
                    'store_id' => $sitestore->store_id
                ),
            );
        }
    }

    public function onMenuInitialize_SitestoreListGutterCreate($row) {

        if (!Engine_Api::_()->core()->hasSubject()) {
            return false;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
        $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration');
        $listmoduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('list');
        if (empty($moduleEnabled) || (empty($listmoduleEnabled))) {
            return false;
        }

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
        if (empty($isManageAdmin)) {
            return false;
        }
        if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
            if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "list_listing")) {
                return false;
            }
        } else {
            $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'list_listing');
            if (empty($isStoreOwnerAllow)) {
                return false;
            }
        }
        if (!Engine_Api::_()->authorization()->isAllowed('list_listing', $viewer, 'create')) {
            return false;
        }

        // Modify params
        $params = $row->params;
        $params['params']['store_id'] = $sitestore->getIdentity();
        return $params;
    }

    public function sitestoresitereviewGutterCreate($row) {

        if (!Engine_Api::_()->core()->hasSubject()) {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
        $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration');
        $sitereviewmoduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereview');
        if (empty($moduleEnabled) || empty($sitereviewmoduleEnabled)) {
            return false;
        }

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
        if (empty($isManageAdmin)) {
            return false;
        }

        //GET LISTING TYPE ID
        $listingtype_id = $row->params['listing_id'];
        $listingType = Engine_Api::_()->getItem('sitereview_listingtype', $listingtype_id);
        $titleSinUc = ucfirst($listingType->title_singular);
        if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
            if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitereview_listing_$listingtype_id")) {
                return false;
            }
        } else {
            $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, "sitereview_listing_$listingtype_id");
            if (empty($isStoreOwnerAllow)) {
                return false;
            }
        }

        $item_enabled = Engine_Api::_()->getDbtable('mixsettings', 'sitestoreintegration')->getItemsEnabled('sitereview_listing', $listingtype_id);
        if (empty($item_enabled)) {
            return false;
        }

        //MUST BE ABLE TO VIEW LISTINGS
        if (!Engine_Api::_()->authorization()->isAllowed('sitereview_listing', $viewer, "view_listtype_$listingtype_id")) {
            return false;
        }

        //MUST BE ABLE TO CRETE LISTINGS
        if (!Engine_Api::_()->authorization()->isAllowed('sitereview_listing', $viewer, "create_listtype_$listingtype_id")) {
            return false;
        }

        $sitereviewpaidlistingEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereviewpaidlisting');
        if ($sitereviewpaidlistingEnabled) {
            $total_packages = Engine_Api::_()->getDbtable('packages', 'sitereviewpaidlisting')->getTotalPackage($listingtype_id);
        }

        if ($sitereviewpaidlistingEnabled && $total_packages == 1) {
            $package = Engine_Api::_()->getDbTable('packages', 'sitereviewpaidlisting')->getEnabledPackage($listingtype_id);
            return array(
                'label' => "Post New $titleSinUc Listings",
                'route' => "sitereview_general_listtype_$listingtype_id",
                'action' => 'create',
                'class' => $row->params['class'],
                'params' => array(
                    'id' => $package->package_id,
                    'store_id' => $sitestore->store_id,
                ),
            );
        } elseif ($sitereviewpaidlistingEnabled && $total_packages > 1) {
            return array(
                'label' => "Post New $titleSinUc Listings",
                'route' => "sitereview_all_package_listtype_$listingtype_id",
                'action' => 'index',
                'class' => $row->params['class'],
                'params' => array(
                    'store_id' => $sitestore->store_id,
                ),
            );
        } else {
            return array(
                'label' => "Post New $titleSinUc Listings",
                'route' => "sitereview_general_listtype_$listingtype_id",
                'action' => 'create',
                'class' => $row->params['class'],
                'params' => array(
                    'store_id' => $sitestore->store_id,
                ),
            );
        }
    }

    // sitestore_manage_mobile_main_orders
    public function onMenuInitialize_SitestoreManageMobileMainOrders($row) {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer || $viewer->getIdentity() <= 0) {
            return false;
        }

        if (!Engine_Api::_()->core()->hasSubject()) {
            return false;
        };

        $subject = Engine_Api::_()->core()->getSubject();
        if (!Engine_Api::_()->sitestore()->isManageAdmin($subject, 'view')) {
            return false;
        }

        $redirectURL = Zend_Registry::get('Zend_View')->url(array('module' => 'sitestoreproduct', 'controller' => 'index', 'action' => 'manage-order', 'store_id' => $subject->getIdentity()), 'default');
        return array(
            'label' => $row->label,
            'route' => 'default',
            'controller' => 'index',
            'action' => 'manage-order',
            'module' => 'sitestoreproduct',
            'onclick' => 'window.location.href = "' . $redirectURL . '";return false;',
            'params' => array(
                'store_id' => $subject->getIdentity(),
            ),
        );
    }

    // sitestore_manage_mobile_main_products
    public function onMenuInitialize_SitestoreManageMobileMainProducts($row) {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer || $viewer->getIdentity() <= 0) {
            return false;
        }

        if (!Engine_Api::_()->core()->hasSubject()) {
            return false;
        };

        $subject = Engine_Api::_()->core()->getSubject();
        if (!Engine_Api::_()->sitestore()->isManageAdmin($subject, 'view')) {
            return false;
        }

        $redirectURL = Zend_Registry::get('Zend_View')->url(array('controller' => 'product', 'action' => 'manage', 'store_id' => $subject->getIdentity()), 'sitestoreproduct_extended');
        return array(
            'label' => $row->label,
            'route' => 'sitestoreproduct_extended',
            'controller' => 'product',
            'action' => 'manage',
            'onclick' => 'window.location.href = "' . $redirectURL . '";return false;',
            'params' => array(
                'store_id' => $subject->getIdentity(),
            ),
        );
    }

    // sitestore_manage_mobile_main_shops
    public function onMenuInitialize_SitestoreManageMobileMainShops($row) {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer || $viewer->getIdentity() <= 0) {
            return false;
        }

        $redirectURL = Zend_Registry::get('Zend_View')->url(array('action' => 'manage'), 'sitestore_general');
        return array(
            'label' => $row->label,
            'route' => 'sitestore_general',
            'action' => 'manage',
            'onclick' => 'window.location.href = "' . $redirectURL . '";return false;',
        );
    }

}
