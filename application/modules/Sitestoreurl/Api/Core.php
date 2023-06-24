<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreurl
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreurl_Api_Core extends Core_Api_Abstract {

    /**
     * Get Sitestore banned url
     * @param array $params : contain desirable Sitestorebanned info
     * @return  object of Sitestoreurl
     */
    public function getBlockUrl($values = array()) {

        $storeTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
        $storeTableName = $storeTable->info('name');
        $bannedStoreurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
        $bannedStoreurlsTableName = $bannedStoreurlsTable->info('name');
        $select = $bannedStoreurlsTable->select();
        $select = $select
                ->from($bannedStoreurlsTableName)
                ->setIntegrityCheck(false)
                ->joinInner($storeTableName, "$storeTableName.store_url = $bannedStoreurlsTableName.word", array('store_id', 'store_url', 'title'))
                ->order((!empty($values['order']) ? $values['order'] : 'bannedpageurl_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

        return Zend_Paginator::factory($select);
    }

    public function setBandURL() {
				$includeModules = array("sitepage" => "sitepage","sitepagedocument" => 'Documents', "sitepageoffer" => 'Offers', "sitepageform" => "Form", "sitepagediscussion" => "Discussions", "sitepagenote" => "Notes", "sitepagealbum" => "Photos", "sitepagevideo" => "Videos", "sitepageevent" => "Events", "sitepagepoll" => "Polls", "sitepageinvite" => "Invite & Promote", "sitepagebadge" => "Badges", "sitepagelikebox" => "External Badge", "sitepagemusic" => "Music","sitegroup" => "sitegroup","sitegroupdocument" => 'Documents', "sitegroupoffer" => 'Offers', "sitegroupform" => "Form", "sitegroupdiscussion" => "Discussions", "sitegroupnote" => "Notes", "sitegroupalbum" => "Photos", "sitegroupvideo" => "Videos", "sitegroupevent" => "Events", "sitegrouppoll" => "Polls", "sitegroupinvite" => "Invite & Promote", "sitegroupbadge" => "Badges", "sitegrouplikebox" => "External Badge", "sitegroupmusic" => "Music","sitestore" => "sitestore","sitestoredocument" => 'Documents', "sitestoreoffer" => 'Offers', "
sitestoreform" => "Form", "sitestorediscussion" => "Discussions","sitestorenote" => "Notes", "sitestorealbum" => "Photos", "sitestorevideo" => "Videos", "sitestoreevent" => "Events", "sitestorepoll" => "Polls", "sitestoreinvite" => "Invite & Promote", "sitestorebadge" => "Badges", "sitestorelikebox" => "External Badge", "sitestoremusic" => "Music","sitebusiness" => "sitebusiness","sitebusinessdocument" => 'Documents', "sitebusinessoffer" => 'Offers', "sitebusinessform" => "Form","sitebusinessdiscussion" => "Discussions", "sitebusinessnote" => "Notes", "sitebusinessalbum" => "Photos", "sitebusinessvideo" => "Videos", "sitebusinessevent" => "Events", "sitebusinesspoll" => "Polls", "sitebusinessinvite" => "Invite & Promote", "sitebusinessbadge" => "Badges", "sitebusinesslikebox" => "External Badge", "sitebusinessmusic" => "Music","list"=>"list");
        $moduleTable = Engine_Api::_()->getDbtable('modules', 'core');
        $tempDb = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = $moduleTable->select()->where('enabled = ?', 1);
        $enableAllModules = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        $enableModules = array_intersect(array_keys($includeModules), $enableAllModules);
        foreach ($enableAllModules as $moduleName) {
            if (!in_array($moduleName, $enableModules)) {
                $file_path = APPLICATION_PATH . "/application/modules/" . ucfirst($moduleName) . "/settings/manifest.php";
                if (@file_exists($file_path)) {
                    $ret = include $file_path;
                    $is_exist = array();
                    if (isset($ret['routes'])) {
                        foreach ($ret['routes'] as $item) {
                            $route = $item['route'];
                            $route_array = explode('/', $route);
                            $route_url = strtolower($route_array[0]);
                            if (!empty($route_url) && !in_array($route_url, $is_exist)) {
                                $tempDb->query("INSERT IGNORE INTO `engine4_seaocore_bannedpageurls` (`bannedpageurl_id`, `word`) VALUES ('','" . $route_url . "')");
                            }
                            $is_exist[] = $route_url;
                        }
                    }
                }
            } else {
                if($moduleName == 'sitepage' || $moduleName == 'sitebusiness' || $moduleName == 'sitegroup' || $moduleName == 'sitestore') {
                    $name = $moduleName . '.manifestUrlS';
                } else {
                    $name = $moduleName . '.manifestUrl';
                }
                $select = new Zend_Db_Select($tempDb);
                $value = $select
                        ->from('engine4_core_settings', 'value')
                        ->where('name = ?', $name)
                        ->query()
                        ->fetchColumn();
                $route_url = strtolower($value);
                if (!empty($route_url)) {
                    $tempDb->query("INSERT IGNORE INTO `engine4_seaocore_bannedpageurls` (`bannedpageurl_id`, `word`) VALUES ('','" . $route_url . "')");
                }
            }
        }
    }

}

?>