<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreoffer_AdminController extends Core_Controller_Action_Admin {

  //ACTION FOR MAKE OFFER HOT AND REMOVE HOT OFFER 
  public function hotofferAction() {

    //GET OFFER ID
    $offerId = $this->_getParam('id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $sitestoreoffer = Engine_Api::_()->getItem('sitestoreoffer_offer', $offerId);
      if ($sitestoreoffer->hotoffer == 0) {
        $sitestoreoffer->hotoffer = 1;
      } else {
        $sitestoreoffer->hotoffer = 0;
      }
      $sitestoreoffer->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitestoreoffer/manage');
  }

  //VIEW OFFER DETAILS
  public function detailAction() {

    //GET OFFER ID
    $offerId = $this->_getParam('id');

    //FETCH THE BADGE DETAIL
    $this->view->sitestoreofferDetail = Engine_Api::_()->getDbtable('offers', 'sitestoreoffer')->getOfferDetail($offerId);
  }

  //ACTION FOR DELETE THE OFFERS
  public function deleteAction() {

    //RENDER DEFAULT LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET OFFER ID
    $this->view->offer_id = $offer_id = $this->_getParam('id');

    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        Engine_Api::_()->sitestoreoffer()->deleteContent($offer_id);

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('')
      ));
    }
    $this->renderScript('admin/delete.tpl');
  }

  public function approvedAction() {
    
    $offerId = $this->_getParam('id');
    if(empty($offerId))
      $this->_redirect('admin/sitestoreoffer/manage');
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $sitestoreoffer = Engine_Api::_()->getItem('sitestoreoffer_offer', $offerId);
      if ($sitestoreoffer->approved == 0) {
        $sitestoreoffer->approved = 1;
      } else {
        $sitestoreoffer->approved = 0;
      }
      $sitestoreoffer->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitestoreoffer/manage');
  }
 
}
?>