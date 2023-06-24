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
class Sitestore_Widget_BrowselocationSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    // Make form
    $this->view->form = $form = new Sitestore_Form_Locationsearch(array('type' => 'sitestore_store'));
    
		if(!empty($_POST)) {
			$this->view->is_ajax = $_POST['is_ajax'];
		}
		
	  if(empty($_POST['sitestore_location'])) {
			$this->view->locationVariable = '1';
		}
		
    if (empty($_POST['is_ajax'])) {
			$p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
			$form->isValid($p);
			$values = $form->getValues();
			$customFieldValues = array_intersect_key($values, $form->getFieldElements());
			$this->view->is_ajax = $this->_getParam( 'is_ajax', 0 );
    } else {
			$values = $_POST;
			$customFieldValues = array_intersect_key($values, $form->getFieldElements());
    }

    unset($values['or']);
    $this->view->assign($values);
    $viewer = Engine_Api::_()->user()->getViewer();
    if (@$values['show'] == 2) {

      //GET AN ARRAY OF FRIEND IDS
      $friends = $viewer->membership()->getMembers();

      $ids = array();
      foreach ($friends as $friend) {
        $ids[] = $friend->user_id;
      }

      $values['users'] = $ids;
    }
    $values['type'] = 'browse';
    $values['type_location'] = 'browseLocation';
    if (isset($values['show'])) {
			if ($form->show->getValue() == 3) {
				@$values['show'] = 3;
			}
    }
    
    //$viewerId = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->current_store = $store = $this->_getParam( 'store' , 1 ) ;
    $this->view->current_totalstores = $store * 15 ;
    $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitestore()->enableLocation();
    $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.price.field', 0);

    //check for miles or street.
		if (isset($values['locationmiles']) && !empty($values['locationmiles'])) {
			if (isset($values['sitestore_street']) && !empty($values['sitestore_street'])) {
				$values['sitestore_location'] = $values['sitestore_street'] . ',';
				unset($values['sitestore_street']);
			}
			
			if (isset($values['sitestore_city']) && !empty($values['sitestore_city'])) {
				$values['sitestore_location'].= $values['sitestore_city'] . ',';
				unset($values['sitestore_city']);
			}	
			
			if (isset($values['sitestore_state']) && !empty($values['sitestore_state'])) {
				$values['sitestore_location'].= $values['sitestore_state'] . ',';
				unset($values['sitestore_state']);
			} 
			
			if (isset($values['sitestore_country']) && !empty($values['sitestore_country'])) {
				$values['sitestore_location'].= $values['sitestore_country'];
				unset($values['sitestore_country']);
			}
		}
    
    //GET STORE SETTING
    $result = Engine_Api::_()->sitestore()->getSitestoresSelect($values, $customFieldValues);
   	$this->view->paginator = $paginator = Zend_Paginator::factory($result); 
    $paginator->setItemCountPerPage(15);
    $this->view->paginator = $paginator->setCurrentPageNumber($store);
    $this->view->mobile = Engine_Api::_()->seaocore()->isMobile();
		if(!empty($_POST['is_ajax'])) {
			//For show location marker.
			if (!empty($checkLocation) && $paginator->getTotalItemCount() > 0) {
				$ids = array();
				$sponsored = array();
				foreach ($paginator as $sitestore) {
					$id = $sitestore->getIdentity();
					$ids[] = $id;
					$sitestore_temp[$id] = $sitestore;
				}
				$values['store_ids'] = $ids;
				$this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'sitestore')->getLocation($values);
				$sitestore_lat = array();
				$sitestore_long = array();
				foreach ($locations as $location) {
					$sitestore_lat[] = $location->latitude;
					$sitestore_long[] = $location->longitude;
				}

				foreach ($locations as $location) {
					if ($sitestore_temp[$location->store_id]->sponsored) {
						break;
					}
				}
				$this->view->sitestore = $sitestore_temp;
			}
    }
  }
}