<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestore_Widget_SitemobileCustomManagestoresController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        try {
            //MANAGE ADMIN IS ALLOWED OR NOT BY ADMIN
            $manageAdminEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manageadmin', 1);
            if (empty($manageAdminEnabled)) {
                return $this->setNoRender();
            }

            //GETTING THE VIEWER AND VIEWER ID AND PASS VALUE .TPL FILE.
            $viewer = Engine_Api::_()->user()->getViewer();
            $this->view->owner_id = $viewer_id = $viewer->getIdentity();

            //USER VALIDATION
            if (!$viewer_id) {
                return $this->setNoRender();
            }

            //CHEKC FOR MEMBER LEVEL SETTINGS FOR EDIT AND DELETE AND CREATE.
            $this->view->can_create = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestore_store', 'create');
            $this->view->can_edit = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestore_store', 'edit');
            $this->view->can_delete = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestore_store', 'delete');

            $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.price.field', 0);
            $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitestore()->enableLocation();

            //GET NAVIGATION
            $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_main');

            //GET QUICK NAVIGATION
            $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sitestore_quick');

            $this->view->form = $form = new Sitestore_Form_Myadminstores();

            //PROCESS FORM
            $values = array();
            if ($form->isValid($this->_getAllParams())) {
                $values = $form->getValues();
            }

            //RATING ENABLE / DISABLE
            $this->view->ratngShow = $ratingShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview');

            //GET STORES
            $adminstores = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->getManageAdminStores($viewer_id);

            //GET STUFF
            $ids = array();
            foreach ($adminstores as $adminstore) {
                $ids[] = $adminstore->store_id;
            }
            $values['adminstores'] = $ids;
            $values['orderby'] = 'creation_date';

            // $values['notIncludeSelfStores'] = $viewer_id;
            //GET PAGINATOR.
            $this->view->paginator = $paginator = Engine_Api::_()->sitestore()->getSitestoresPaginator($values, null);
            $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.store', 10);

            $paginator->setItemCountPerPage($items_count);
            $this->view->paginator = $paginator->setCurrentPageNumber($values['store']);

            $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

            //MAXIMUN ALLOWED STORES.
            $this->view->quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestore_store', 'max');
            $this->view->current_count = $paginator->getTotalItemCount();
        } catch (Exception $e) {
            // var_dump($e);die;
            throw $e;
        }
    }

    public function _getParam($key, $default) {
        $param = parent::_getParam($key);
        if (empty($param)) {
            $param = Zend_Controller_Front::getInstance()->getRequest()->getParam($key, $default);
        }

        return $param;
    }
}
