<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Shippingmethods.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Shippingmethods extends Engine_Db_Table {

  protected $_name = 'sitestoreproduct_shipping_methods';
  protected $_rowClass = 'Sitestoreproduct_Model_Shippingmethod';

  /**
   * Return shipping methods object
   *
   * @param array $params
   * @return object
   */
  public function getShippingMethodsPaginator($params = array()) {
    $paginator = Zend_Paginator::factory($this->getShippingMethodsSelect($params));
    if (!empty($params['page']))
      $paginator->setCurrentPageNumber($params['page']);

    if (empty($params['limit']))
      $paginator->setItemCountPerPage(8);
    else
      $paginator->setItemCountPerPage($params['limit']);

    return $paginator;
  }
  
  public function getShippingMethodsSelect($params) {
    $shippingMethodTableName = $this->info('name');
    $regionsTable = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct');
    $regionsTableName = $regionsTable->info('name');

    $enableCountries = $regionsTable->select()
                    ->from($regionsTableName, array('country'))
                    ->where('country_status = 1')
                    ->group('country')
                    ->query()->fetchAll();

    $regionArray = array();
    $regionArray[0] = '\'\'';
    foreach ($enableCountries as $region) {
      $regionArray[] = '\'' . $region['country'] . '\'';
    }
    $enableCountryString = implode(',', $regionArray);


    $select = $this->select()->setIntegrityCheck(false);
    $select->from($shippingMethodTableName)
            ->joinLeft($regionsTableName, '(' . $shippingMethodTableName . '.region = ' . $regionsTableName . '.region_id)', array("$regionsTableName.region as region_name"))
            ->where($shippingMethodTableName . '.store_id = ?', $params['store_id'])
            ->where("(($shippingMethodTableName.country LIKE 'ALL') OR ($shippingMethodTableName.region = 0 AND $shippingMethodTableName.country IN ($enableCountryString)) OR ($shippingMethodTableName.region != 0 AND $regionsTableName.country_status = 1 AND $regionsTableName.status = 1))")
            ->order($shippingMethodTableName . '.creation_date DESC')
            ->order("$shippingMethodTableName.shippingmethod_id DESC");

    return $select;
  }

  /**
   * Return checkout shipping methods
   *
   * @param array $info
   * @return array
   */
  public function getCheckoutShippingMethods($info = array()) {

    $index = 0;
    $shippingMethodTableName = $this->info('name');
    $shippingCountry = $info['shipping_country'];
    $shippingRegion = $info['shipping_region_id'];

    $shippingMethodsArray = array();
    $select = $this->select()
            ->where('store_id = ? AND status = 1', $info['store_id'])
            ->where("(country LIKE 'ALL' OR (country LIKE '$shippingCountry' AND (region = 0 OR region = $shippingRegion)))")
            ->order('creation_date ASC')
            ->query()
            ->fetchAll();

    foreach ($select as $key => $values) {

      if ($values['dependency'] == 1) {
        if ($info['total_weight'] >= $values['ship_start_limit'] && (empty($values['ship_end_limit']) || $info['total_weight'] <= $values['ship_end_limit'])) {
          if ($values['ship_type'] == 0) {
            if ($values['handling_type'] == 0) {
              $shippingMethodsArray[$index]['name'] = $values['title'];
              $shippingMethodsArray[$index]['delivery_time'] = $values['delivery_time'];
              $shippingMethodsArray[$index]['charge'] = @round($values['handling_fee'], 2);
              $index++;
            } else {
              $shippingMethodsArray[$index]['name'] = $values['title'];
              $shippingMethodsArray[$index]['delivery_time'] = $values['delivery_time'];
              $shippingMethodsArray[$index]['charge'] = @round(($values['handling_fee'] / 100) * $info['total_price'], 2);
              $index++;
            }
          } else {
            $shippingMethodsArray[$index]['name'] = $values['title'];
            $shippingMethodsArray[$index]['delivery_time'] = $values['delivery_time'];
            $shippingMethodsArray[$index]['charge'] = @round(ceil($info['total_weight']) * $values['handling_fee'], 2);
            $index++;
          }
        }
      } else {
        if ($info['total_weight'] >= $values['allow_weight_from'] && (empty($values['allow_weight_to']) || $info['total_weight'] <= $values['allow_weight_to'])) {
          if ($values['dependency'] == 0) {
            if ($info['total_price'] >= $values['ship_start_limit'] && (empty($values['ship_end_limit']) || $info['total_price'] <= $values['ship_end_limit'])) {
              if ($values['handling_type'] == 0) {
                $shippingMethodsArray[$index]['name'] = $values['title'];
                $shippingMethodsArray[$index]['delivery_time'] = $values['delivery_time'];
                $shippingMethodsArray[$index]['charge'] = @round($values['handling_fee'], 2);
                $index++;
              } else {
                $shippingMethodsArray[$index]['name'] = $values['title'];
                $shippingMethodsArray[$index]['delivery_time'] = $values['delivery_time'];
                $shippingMethodsArray[$index]['charge'] = @round(($values['handling_fee'] / 100) * $info['total_price'], 2);
                $index++;
              }
            }
          } else {
            if ($info['total_quantity'] >= $values['ship_start_limit'] && (empty($values['ship_end_limit']) || $info['total_quantity'] <= $values['ship_end_limit'])) {
              if ($values['ship_type'] == 0) {
                $shippingMethodsArray[$index]['name'] = $values['title'];
                $shippingMethodsArray[$index]['delivery_time'] = $values['delivery_time'];
                $shippingMethodsArray[$index]['charge'] = @round($values['handling_fee'], 2);
                $index++;
              } else {
                if ($values['handling_type'] == 0) {
                  $shippingMethodsArray[$index]['name'] = $values['title'];
                  $shippingMethodsArray[$index]['delivery_time'] = $values['delivery_time'];
                  $shippingMethodsArray[$index]['charge'] = @round($values['handling_fee'] * $info['total_quantity'], 2);
                  $index++;
                } else {
                  $shippingMethodsArray[$index]['name'] = $values['title'];
                  $shippingMethodsArray[$index]['delivery_time'] = $values['delivery_time'];
                  $shippingMethodsArray[$index]['charge'] = @round(($values['handling_fee'] / 100) * $info['total_price'], 2);
                  $index++;
                }
              }
            }
          }
        }
      }
    }

    return $shippingMethodsArray;
  }

  /**
   * Return is any shipping method exist
   *
   * @param $store_id
   * @return bool
   */
  public function isAnyShippingMethodExist($store_id) {

    $shippingMethod = $this->select()
            ->from($this->info('name'), array('shippingmethod_id'))
            ->where('store_id = ?', $store_id)
            ->where('status =?', 1)
            ->limit(1)
            ->query()
            ->fetchColumn();
    if (empty($shippingMethod))
      return 0;
    else
      return 1;
  }
  
  //ENABLE/DISABLE THE STATUS OF THE SHIPPING METHODS ACCORDING TO THE MINIMUM SHIPPING COST OF THE STORE
  public function toggleStoreShippingMethods($params) {
    if (!empty($params)) {
      $this->update(array('status' => 1), array('store_id = ?' => $params['store_id'], 'handling_fee >= ?' => $params['minimum_shipping_cost'], 'handling_type = 0'));
      $this->update(array('status' => 0), array('store_id = ?' => $params['store_id'], 'handling_fee < ?' => $params['minimum_shipping_cost'], 'handling_type = 0'));
    }
  }

}

