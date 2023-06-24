<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Installer extends Engine_Package_Installer_Module {

     //SITEMOBILE CODE TO CALL MY.SQL ON POST INSTALL
//    public function onPostInstall() {
//        $moduleName = 'sitestoreproduct';
//        $db = $this->getDb();
//        $select = new Zend_Db_Select($db);
//        $select
//                ->from('engine4_core_modules')
//                ->where('name = ?', 'sitemobile')
//                ->where('enabled = ?', 1);
//        $is_sitemobile_object = $select->query()->fetchObject();
//        if (!empty($is_sitemobile_object)) {
//            $db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`) VALUES
//('$moduleName','1')");
//            $select = new Zend_Db_Select($db);
//            $select
//                    ->from('engine4_sitemobile_modules')
//                    ->where('name = ?', $moduleName)
//                    ->where('integrated = ?', 0);
//            $is_sitemobile_object = $select->query()->fetchObject();
//            if ($is_sitemobile_object) {
//                $actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
//                $controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
//                if ($controllerName == 'manage' && $actionName == 'install') {
//                    $view = new Zend_View();
//                    $baseUrl = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
//                    $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
//                    $redirector->gotoUrl($baseUrl . 'admin/sitemobile/module/enable-mobile/enable_mobile/1/name/' . $moduleName . '/integrated/0/redirect/install');
//                }
//            }
//        }
//    }
  
}