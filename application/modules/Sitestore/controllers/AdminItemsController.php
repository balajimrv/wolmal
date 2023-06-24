<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminItemsController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_AdminItemsController extends Core_Controller_Action_Admin {

    //ACTION FOR STORE OF THE DAY
    public function dayAction() {

        //TAB CREATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_items');

        //FORM GENERATION
        $this->view->formFilter = $formFilter = new Sitestore_Form_Admin_Filter();
        $store = $this->_getParam('page', 1);

        $values = array();
        if ($formFilter->isValid($this->_getAllParams())) {
            $values = $formFilter->getValues();
        }
        foreach ($values as $key => $value) {
            if (null == $value) {
                unset($values[$key]);
            }
        }
        $values = array_merge(array(
            'order' => 'start_date',
            'order_direction' => 'DESC',
                ), $values);

        $this->view->assign($values);

        //FETCH DATA
        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('itemofthedays', 'sitestore')->getItemOfDayList($values, 'store_id', 'sitestore_store');
        $this->view->paginator->setItemCountPerPage(50);
        $this->view->paginator = $paginator->setCurrentPageNumber($store);
    }

    //ACTION FOR ADDING STORE OF THE DAY
    public function addItemAction() {

        //SET LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        //FORM GENERATION
        $form = $this->view->form = new Sitestore_Form_Admin_Item();
        $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

        //CHECK POST
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            //GET FORM VALUES
            $values = $form->getValues();

            //BEGIN TRANSACTION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                $table = Engine_Api::_()->getDbtable('itemofthedays', 'sitestore');
                $select = $table->select()->where('resource_id = ?', $values["resource_id"])->where('resource_type = ?', 'sitestore_store');
                $row = $table->fetchRow($select);

                if (empty($row)) {
                    $row = $table->createRow();
                    $row->resource_id = $values["resource_id"];
                }

                $viewer = Engine_Api::_()->user()->getViewer();
                $oldTz = date_default_timezone_get();
                date_default_timezone_set($viewer->timezone);
                $start = strtotime($values['starttime']);
                $end = strtotime($values['endtime']);
                date_default_timezone_set($oldTz);
                $values['starttime'] = date('Y-m-d H:i:s', $start);
                $values['endtime'] = date('Y-m-d H:i:s', $end);

                $row->start_date = $values["starttime"];
                $row->end_date = $values["endtime"];
                $row->resource_type = 'sitestore_store';
                $row->save();

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('The Store of the Day has been added successfully.'))
            ));
        }
    }

    //ACTION FOR STORE SUGGESTION DROP-DOWN
    public function getitemAction() {

        $search_text = $this->_getParam('text', null);
        $limit = $this->_getParam('limit', 40);

        $data = array();

        $moduleContents = Engine_Api::_()->getItemTable('sitestore_store')->getDayItems($search_text, $limit = 10);

        foreach ($moduleContents as $moduleContent) {

            $content_photo = $this->view->itemPhoto($moduleContent, 'thumb.icon');

            $data[] = array(
                'id' => $moduleContent->store_id,
                'label' => $moduleContent->title,
                'photo' => $content_photo
            );
        }
        return $this->_helper->json($data);
    }

    //ACTION FOR STORE DELETE ENTRY
    public function deleteItemAction() {

        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $itemofthedaysTable = Engine_Api::_()->getDbtable('itemofthedays', 'sitestore')->delete(array('itemoftheday_id =?' => $this->_getParam('id')));
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
            ));
        }
        $this->renderScript('admin-items/delete.tpl');
    }

    //ACTION FOR MULTI DELETE STORE ENTRIES
    public function multiDeleteAction() {

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    $sitestoreitemofthedays = Engine_Api::_()->getItem('sitestore_itemofthedays', (int) $value);
                    if (!empty($sitestoreitemofthedays)) {
                        $sitestoreitemofthedays->delete();
                    }
                }
            }
        }
        return $this->_helper->redirector->gotoRoute(array('action' => 'day'));
    }

}

?>