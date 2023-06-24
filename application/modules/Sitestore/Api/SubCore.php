<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Api_SubCore extends Core_Api_Abstract {

    /**
     * Get feeds for store profile store
     *
     * @$user User_Model_User
     * @param array $params
     */
    public function getEveryoneStoreProfileFeeds(Core_Model_Item_Abstract $about, array $params = array()) {
        $ids = array();
        if (!(bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.feed.everyone', 0))
            return $ids;
        //Proc args
        extract($params); //action_id, limit, min_id, max_id

        $actionDbTable = Engine_Api::_()->getDbtable('actions', 'activity');
        $select = $actionDbTable->select();
        if ($about->getType() == 'sitestore_store') {
            $select->where("(subject_type ='sitestore_store'  and subject_id = ? ) OR ( (type <> 'sitestore_new' AND type <> 'like_sitestore_store') and object_type ='sitestore_store'  and object_id = ?) ", $about->getIdentity());
        } elseif ($about->getType() == 'sitestoreevent_event') {
            $select->where("(subject_type ='sitestoreevent_event'  and subject_id = ? ) OR ( (type <> 'like_sitestoreevent_event') and object_type ='sitestoreevent_event'  and object_id = ?) ", $about->getIdentity());
        }
        $select->order('action_id DESC')
                ->limit($limit);

        // Add action_id/max_id/min_id
        if (null !== $action_id) {
            $select->where('action_id = ?', $action_id);
        } else {
            if (null !== $min_id) {
                $select->where('action_id >= ?', $min_id);
            } else if (null !== $max_id) {
                $select->where('action_id <= ?', $max_id);
            }
        }
        $results = $actionDbTable->fetchAll($select);
        foreach ($results as $actionData)
            $ids[] = $actionData->action_id;
        return $ids;
    }

    /**
     * Delete Create Activity Feed Of Item Before Delete Item
     *
     * $item
     * @$actionsType array $actionsType
     */
    public function deleteCreateActivityOfExtensionsItem($item, $actionsType = array()) {

        $attachmentsTable = Engine_Api::_()->getDbtable('attachments', 'activity');
        $attachmentsTableName = $attachmentsTable->info('name');
        $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');
        $actionsTableName = $actionsTable->info('name');
        $select = $attachmentsTable->select()
                ->setIntegrityCheck(false)
                ->from($attachmentsTableName, array($attachmentsTableName . '.action_id'))
                ->join($actionsTableName, "`{$attachmentsTableName}`.action_id = `{$actionsTableName}`.action_id  ", null)
                ->where($attachmentsTableName . '.id = ?', $item->getIdentity())
                ->where($attachmentsTableName . '.type = ?', $item->getType())
                ->where($actionsTableName . '.type in(?)', new Zend_Db_Expr("'" . join("', '", $actionsType) . "'"));

        $row = $attachmentsTable->fetchRow($select);
        if (!empty($row)) {
            try {
                $action = $actionsTable->fetchRow(array('action_id =?' => $row->action_id));
                if (!empty($action)) {
                    $action->deleteItem();
                    $action->delete();
                }
            } catch (Exception $ex) {
                
            }
        }
    }

    /**
     * Store base network enable
     *
     * @return bool
     */
    public function storeBaseNetworkEnable() {
        return (bool) ( Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.networks.type', 0) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.network', 0) || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.default.show', 0)));
    }

    /**
     * Content in File or not
     *
     * @return bool
     */
    public function isContentInFile($path, $string) {

        $isContentInFile = 0;
        if (is_file($path)) {
            @chmod($path, 0777);
            $fileData = file($path);
            foreach ($fileData as $key => $value) {
                $pos = strpos($value, $string);
                if ($pos !== false) {
                    $isContentInFile = 1;
                    break;
                }
            }
        }
        return $isContentInFile;
    }

    /**
     * Activity Feed Widget
     *
     * @return bool
     */
    public function isCoreActivtyFeedWidget($storeName, $widgetName, $params = array()) {
        $isCoreActivtyFeedWidget = false;

        $storesTable = Engine_Api::_()->getDbtable('pages', 'core');
        $storesTableName = $storesTable->info('name');
        $contentsTable = Engine_Api::_()->getDbtable('content', 'core');
        $contentsTableName = $contentsTable->info('name');

        $select = $contentsTable->select()
                ->setIntegrityCheck(false)
                ->from($contentsTableName, array($contentsTableName . '.name'))
                ->join($storesTableName, "`{$storesTableName}`.page_id = `{$contentsTableName}`.page_id  ", null)
                ->where($storesTableName . '.name = ?', $storeName)
                ->where($contentsTableName . '.name = ?', $widgetName);
        $row = $contentsTable->fetchRow($select);
        if (!empty($row))
            $isCoreActivtyFeedWidget = true;
        return $isCoreActivtyFeedWidget;
    }

    /**
     * Activity Feed Widget
     *
     * @return bool
     */
    public function isStoreCoreActivtyFeedWidget($widgetName, $params = array()) {
        $isCoreActivtyFeedWidget = false;
        $contentsTable = Engine_Api::_()->getDbtable('admincontent', 'sitestore');
        $select = $contentsTable->select()
                ->where('name = ?', $widgetName);
        $row = $contentsTable->fetchRow($select);
        if (!empty($row))
            $isCoreActivtyFeedWidget = true;
        return $isCoreActivtyFeedWidget;
    }

    /**
     * GET TRUE OR FALSE FOR SAMPLE AD WIDGET
     *
     * @return bool
     */
    public function getSampleAdWidgetEnabled($sitestore) {

        //CHECK STORE OWNER IS THERE OR NOT
        $isManageAdmin = Engine_Api::_()->sitestore()->isStoreOwner($sitestore);
        if (!$isManageAdmin) {
            return false;
        }

        //CHECK WHETHER THE SITESTORE MODULE IN THE COMMUNITYAD TABLE OR NOT
        $ismoduleads_enabled = Engine_Api::_()->getDbtable('modules', 'communityad')->ismoduleads_enabled("sitestore");
        if (!$ismoduleads_enabled) {
            return false;
        }

        //CHECK WHETHER THE AD BELONG TO THE SITESTORE MODULE OR NOT
        $useradsTable = Engine_Api::_()->getDbtable('userads', 'communityad');
        $select = $useradsTable->select();
        $select
                ->from($useradsTable->info('name'), array('userad_id'))
                ->where('resource_type = ?', "sitestore")
                ->where('resource_id = ?', $sitestore->store_id)
                ->limit(1);
        $ad_exist = $useradsTable->fetchRow($select);
        if (!empty($ad_exist)) {
            return false;
        }

        //CHECK THE CREATE LINK OR ADPREVIEW LINK YES OR NOT FROM THE ADMIN
        if ((Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adcreatelink', 1) || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adpreview', 1))) {
            return true;
        }
    }

    /**
     * GET STORES ON WHICH HE HAS ACTIVTYF FEED
     * 
     * @$member User_Model_User
     * @return bool
     */
    public function getMemberFeedsForStoreOfIds($member) {
        $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
        return $streamTable->select()
                        ->from($streamTable->info('name'), "target_id")
                        ->where('subject_id = ?', $member->getIdentity())
                        ->where('subject_type = ?', $member->getType())
                        ->where('target_type = ?', 'sitestore_store')
                        ->query()
                        ->fetchAll(Zend_Db::FETCH_COLUMN);
    }

    /**
     * DELETE ACTIVITY FEED STREAM PRIVACY
     * 
     * @$member User_Model_User
     * @return bool
     */
    public function deleteFeedStream($action) {
        $settingsCoreApi = Engine_Api::_()->getApi('settings', 'core');
        if (empty($action) || empty($settingsCoreApi->sitestore_feed_type) || empty($settingsCoreApi->sitestore_feed_onlyliked))
            return;
        $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
        $streamTable->delete(array(
            'action_id = ?' => $action->getIdentity(),
            'target_type <> ?' => 'sitestore_store'
        ));
    }

    /**
     * DELETE ACTIVITY FEED STREAM PRIVACY WHICH ARE NOT NEED
     * 
     * @$member User_Model_User
     * @return bool
     */
    public function getStoreFeedActionIds() {

        $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
        $actionIds = $streamTable->select()
                ->from($streamTable->info('name'), "action_id")
                ->where('target_type = ?', 'sitestore_store')
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);

        if (!empty($actionIds)) {
            $streamTable->delete(array(
                'action_id  In(?)' => $actionIds,
                'target_type <> ?' => 'sitestore_store'
            ));
        }
    }

}

?>