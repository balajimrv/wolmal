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
class Sitestore_Widget_SuggestedstoreSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    // Get subject and check auth
    if (!Engine_Api::_()->core()->hasSubject('sitestore_store')) {
      return $this->setNoRender();
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');

    //FINDING THE LOCATION OF THIS SITESTORE.
//    $location_table = Engine_Api::_()->getDbtable('locations', 'sitestore');
//    $location_rName = $location_table->info('name');
//    $select_location = $location_table->select();
//    $select_location
//        ->setIntegrityCheck(false)
//        ->from($location_rName, array('city'))
//        ->where($location_rName . '.store_id =?', $sitestore->store_id);
//    $location_row = $location_table->fetchRow($select_location);
//    if (!empty($location_row)) {
//      $location_row = $location_row->toarray();
//    }

    $this->view->view_store_id = $sitestore->store_id;
    //get Tag for sitestore
//    $this->view->sitestoreTags = $sitestoreTags = $sitestore->tags()->getTagMaps();
//    $tagString = '';
//    foreach ($sitestoreTags as $value) {
//      $tagString .= "'" . $value->tag_id . "',";
//    }
//    $tagString = trim($tagString, ",");


    $featured = $this->_getParam('featured', 0);
    $sponsored = $this->_getParam('sponsored', 0);

    //GETTING THE TAG ID OF THIS SITESTORE ID.
    $items_count = $this->_getParam('itemCount', 5);
    $values['category'] = $sitestore->category_id;
    $table = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $rName = $table->info('name');
//    $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
//    $tmName = $tmTable->info('name');
    $select = $table->select();
    $select
        ->setIntegrityCheck(false)
				->from($rName)
				->order('RAND() DESC ')
				->where($rName . '.owner_id <> ?', $viewer_id)
				->where($rName . '.store_id <> ?', $sitestore->store_id)
				->where($rName . '.closed = ?', '0')
				->where($rName . '.draft = ?', '1')
				->where($rName . '.approved = ?', '1')
				->where($rName . ".search = ?", 1)
				->group($rName . '.store_id')
				->limit($items_count);

    if ( $featured == '1' ) {
      $select = $select->where($rName . '.	featured =?', '0');
    }
    elseif ( $featured == '2' ) {
      $select = $select->where($rName . '.	featured =?', '1');
    }

    if ( $sponsored == '1' ) {
      $select = $select->where($rName . '.	sponsored =?', '0');
    }
    elseif ( $sponsored == '2' ) {
      $select = $select->where($rName . '.	sponsored =?', '1');
    }

    $sqlStr = '';
//    if (!empty($tagString)) {
//      $select
//          ->setIntegrityCheck(false)
//          ->joinLeft($tmName, "$tmName.resource_id = $rName.store_id")
//          ->where($tmName . '.resource_type = ?', 'sitestore_store');
//      $sqlStr = $tmName . '.tag_id IN(' . $tagString . ')';
//    }
    if (!empty($sitestore->category_id)) {
      if (empty($sqlStr)) {
        $sqlStr = $rName . '.category_id = ' . "'" . $sitestore->category_id . "'";
      } else {
        $sqlStr.= ' OR ' . $rName . '.category_id = ' . "'" . $sitestore->category_id . "'";
      }
    }
    
    if (!empty($sitestore->price)) {
      $price = $sitestore->price;
      $price_min = $price - (int) abs(($price * 10) / 100);
      $price_max = $price + (int) abs(($price * 10) / 100);
      if (!empty($sqlStr)) {
        $sqlStr.= ' OR ' . $rName . ".price  BETWEEN " . $price_min . " AND " . $price_max . "";
      } else {
        $sqlStr.= $rName . ".price  BETWEEN " . $price_min . " AND " . $price_max . "";
      }
    }
//
//    if (isset($location_row['city']) && !empty($location_row['city'])) {
//      $select->joinLeft($location_rName, "$location_rName.store_id = $rName.store_id", null);
//
//      if (!empty($sqlStr)) {
//        $sqlStr.= ' OR ' . $location_rName . '.city =' . "'" . $location_row['city'] . "'";
//      } else {
//        $sqlStr.= $location_rName . '.city =' . "'" . $location_row['city'] . "'";
//      }
//    }

    if (!empty($sqlStr)) {
      $select->where($sqlStr);
    }
    $results = $table->fetchAll($select);
    $this->view->suggestedsitestore = $results;
    // NOT RENDER IF SITESTORE COUNT ZERO
    if (!(count($this->view->suggestedsitestore) > 0)) {
      return $this->setNoRender();
    }

  }

}
?>