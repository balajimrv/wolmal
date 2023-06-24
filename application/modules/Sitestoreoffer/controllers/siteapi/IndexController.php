<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreoffer_IndexController extends Siteapi_Controller_Action_Standard {
    
    
    public function init()
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        $store_id = $this->_getParam('store_id');

        if (!empty($store_id)) { 
            $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
            if (!empty($sitestore))
                Engine_Api::_()->core()->setSubject($sitestore);
        }


        // Authorization check
        if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', $viewer, "view")->isValid())
            $this->respondWithError('unauthorized');
    }

    public function indexAction()
    {

        $this->validateRequestMethod();
        
        $response = $tempResponse = array();
        
        $viewer = Engine_Api::_()->user()->getViewer();

        $limit = $this->_getParam('limit' , 20);
        $page = $this->_getParam('page' , 1);
        $type = $this->_getParam('orderby' , 'offer_id');

        $offersTable = Engine_Api::_()->getDbTable('offers' , 'sitestoreoffer');

        $select = $offersTable->select();

        if($type == 'offer_id')
            $select->order('offer_id asc');
        else
            $select->order($type.' desc');

        $store_offers = Zend_Paginator::factory($select);
        $store_offers->setCurrentPageNumber($page);
        $store_offers->setItemCountPerPage($limit);

        $response = array();

        $response['totalItemCount'] = $store_offers->getTotalItemCount();
        if($store_offers)
        {
            foreach($store_offers as $value)
            {
                $data = $value->toArray();
                $data = array_merge($data, Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($value));
                $data = array_merge($data, Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($value, true));
                
                if($value->getOwner()->getIdentity() == $viewer->getIdentity())
                $data['gutterMenu'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitestoreoffer')->guttermenu($value);
                
                $tempResponse[] = $data;
                
            }
            $response['offers'] = $tempResponse;
        }
        $this->respondWithSuccess($response , true);
    }
    
    /* 
     * Browse offers 
     * 
     */
    public function browseAction()
    {

        $this->validateRequestMethod();
        
        $response = $tempResponse = array();
        
        $viewer = Engine_Api::_()->user()->getViewer();
        if (Engine_Api::_()->core()->hasSubject('sitestore_store'))
            $sitestore = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
        else
            $this->respondWithError('no_record');

        $store_offers = Engine_Api::_()->getDbTable('offers','sitestoreoffer')->getsitestoreoffersPaginator($subject->getIdentity(),null,null , 1) ;

        $response = array();
        $response['totalItemCount'] = $store_offers->getTotalItemCount();
        if($store_offers)
        {
            foreach($store_offers as $value)
            {
                $data = $value->toArray();
                $data = array_merge($data, Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($value));
                $data = array_merge($data, Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($value, true));
                
                if($value->getOwner()->getIdentity() == $viewer->getIdentity())
                $data['gutterMenu'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitestoreoffer')->guttermenu($value);
                
                $tempResponse[] = $data;
                
            }
            $response['offers'] = $tempResponse;
        }
        $this->respondWithSuccess($response , true);
    }
    
    /**
     * Enable or disable an offer
     */
    public function enableAction()
    {
        $this->validateRequestMethod("POST");
        
        $viewer = Engine_Api::_()->user()->getViewer();
        $offer_id = $this->_getParam('offer_id');
        
        $offer = Engine_Api::_()->getItem('sitestoreoffer_offer',$offer_id);
        if(!$offer)
            $this->respondWithError('no_record');
        
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $offer->status = !$offer->status;
            $offer->save();
            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
        
    }

    public function viewAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $offer_id = $this->_getParam('offer_id');
        
        $offer = Engine_Api::_()->getItem('sitestoreoffer_offer',$offer_id);
        if(!$offer)
            $this->respondWithError('no_record');

        $response = $offer->toArray();
        $response = array_merge($response , Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($offer, false));
        $response = array_merge($response , Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($offer, true));
        $response['owner_title'] = $offer->getOwner()->getTitle();
        $this->respondWithSuccess($response,true);

    }
    
    
    public function deleteAction()
    {
        $this->validateRequestMethod("DELETE");
        
        $viewer = Engine_Api::_()->user()->getViewer();
        $offer_id = $this->_getParam('offer_id');
        
        $offer = Engine_Api::_()->getItem('sitestoreoffer_offer',$offer_id);
        if(!$offer)
            $this->respondWithError('no_record');
        
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $offer->delete();
            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }
    
}



























