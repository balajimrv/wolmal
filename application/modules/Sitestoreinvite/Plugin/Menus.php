<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreinvite
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreinvite_Plugin_Menus {

  public function onMenuInitialize_SitestoreinviteGutterStoreinvite($row) {
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if (empty($viewer_id)) {
      return false;
    }

    $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');

    if (!empty($sitestore) && !empty($viewer_id)) {
      //START MANAGE-ADMIN CHECK
      $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'invite');
      if (empty($isManageAdmin)) {
        $can_invite = 0;
      } else {
        $can_invite = 1;
      }
      //END MANAGE-ADMIN CHECK

      $manageadminTable = Engine_Api::_()->getDbtable('manageadmins', 'sitestore');
      $manageadminTableName = $manageadminTable->info('name');
      $select = $manageadminTable->select()
              ->from($manageadminTableName, 'manageadmin_id')
              ->where('user_id = ?', $viewer_id)
              ->where('store_id = ?', $sitestore->store_id)
              ->limit(1);
      $rowData = $manageadminTable->fetchAll($select)->toArray();
      if (!empty($rowData[0]['manageadmin_id'])) {
        $ismanageadmin = 1;
      } else {
        $ismanageadmin = 0;
      }

      if (($can_invite != 1) || (Engine_Api::_()->user()->getViewer()->level_id != 1  && $viewer_id != $sitestore->owner_id && $ismanageadmin != 1)) {
        return false;
      }
    }

    // Modify params
    $params = $row->params;
    $params['params']['sitestore_id'] = $sitestore->getIdentity();
    $params['params']['user_id'] = $sitestore->owner_id;
    return $params;
  }

}

?>