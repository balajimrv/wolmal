<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Taxes.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Taxes extends Engine_Db_Table {

  protected $_name = 'sitestoreproduct_taxes';
  protected $_rowClass = 'Sitestoreproduct_Model_Tax';

  /**
   * Return tax object
   *
   * @param array $params
   * @return object
   */
  public function getTaxesPaginator($params=array()) {

    $paginator = Zend_Paginator::factory($this->getTaxesSelect($params));
    if (!empty($params['page']))
      $paginator->setCurrentPageNumber($params['page']);

    if (!empty($params['limit']))
      $paginator->setItemCountPerPage($params['limit']);
    else
      $paginator->setItemCountPerPage(20);

    return $paginator;
  }
  
  public function getTaxesSelect($params) {
    $select = $this->select();
    if (isset($params['store_id'])) {
      $select->where('store_id = ?', $params['store_id']);

      if ($params['store_id'] == 0) {
        $select->where('status = 1');
      }
    }

    return $select->order('creation_date DESC')->order('tax_id DESC');
  }

  /**
   * Return tax for a store
   *
   * @param $store_id
   * @return object
   */
  public function getTaxByStore($store_id, $product_type) {

    $select = $this->select()
                   ->where('store_id = ?', $store_id)
                   ->where('status = 1')
                   ->where('is_vat = 0');
    
    if( $product_type == 'downloadable' )
      $select->where('rate_dependency = 1');
    
    return $select->query()->fetchAll();
  }

  /**
   * Return checkout taxes
   *
   * @param $taxIds
   * @param $address
   * @param $product_type
   * @return object
   */
  public function getCheckoutTaxes($taxIds = null, $address = null, $product_type = null) {
    $shippingRegion = $address['shipping_region_id'];
    $shippingCountry = $address['shipping_country'];
    $billingRegion = $address['billing_region_id'];
    $billingCountry = $address['billing_country'];

    $taxTableName = $this->info('name');
    $taxRateTable = Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct');
    $taxRateTableName = $taxRateTable->info('name');
    
    $taxIds = empty($taxIds) ? '0' : $taxIds;
    $allEnableTaxIds = $this->select()
            ->from($taxTableName, array("tax_id"))
            ->where("($taxTableName.store_id = 0 OR $taxTableName.tax_id IN($taxIds))")
            ->where("$taxTableName.status = 1")
            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);

    $taxRatesArray = array();
    
    foreach($allEnableTaxIds as $key => $taxId){
      $taxRateSelect_step1 = $taxRateSelect_step2 = $taxRateSelect_step3 = array();

    if( $product_type == 'downloadable' )
    {
      $taxRateSelect_step1 = $this->getCheckoutTempTaxes($taxId)
                        ->where("($taxTableName.rate_dependency = 1 
                    AND $taxRateTableName.state = $billingRegion)")->query()->fetchAll();
    }
    else
    {
      $taxRateSelect_step1 = $this->getCheckoutTempTaxes($taxId)
              ->where("($taxTableName.rate_dependency = 0 
                    AND $taxRateTableName.state = $shippingRegion) 
                    OR ($taxTableName.rate_dependency = 1 
                    AND $taxRateTableName.state = $billingRegion)")->query()->fetchAll();
                }
    
    if (empty($taxRateSelect_step1)) {
      if( $product_type == 'downloadable' )
      {
        $taxRateSelect_step2 = $this->getCheckoutTempTaxes($taxId)
                ->where("($taxTableName.rate_dependency = 1 
                        AND $taxRateTableName.state = 0 
                        AND $taxRateTableName.country LIKE '$billingCountry')")->query()->fetchAll();
      }
      else
      {
        $taxRateSelect_step2 = $this->getCheckoutTempTaxes($taxId)
                ->where("($taxTableName.rate_dependency = 0 
                        AND $taxRateTableName.state = 0 
                        AND $taxRateTableName.country LIKE '$shippingCountry') 
                        OR ($taxTableName.rate_dependency = 1 
                        AND $taxRateTableName.state = 0 
                        AND $taxRateTableName.country LIKE '$billingCountry')")->query()->fetchAll();
      }     
      if (empty($taxRateSelect_step2)) {
        if( $product_type == 'downloadable' )
      {
         $taxRateSelect_step3 = $this->getCheckoutTempTaxes($taxId)
                ->where("$taxTableName.rate_dependency = 1 
                        AND $taxRateTableName.country LIKE 'ALL'")->query()->fetchAll();
      }
      else
      {
        $taxRateSelect_step3 = $this->getCheckoutTempTaxes($taxId)
                ->where("$taxRateTableName.country LIKE 'ALL'")->query()->fetchAll();
      }
       
      }
    } 
    $taxRatesArray = @array_merge($taxRatesArray,$taxRateSelect_step1,$taxRateSelect_step2,$taxRateSelect_step3);
    }

    return $taxRatesArray;
  }

  public function getCheckoutTempTaxes($taxId) {
    $taxTableName = $this->info('name');
    $taxRateTableName = Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct')->info('name');
    
    $taxSelect = $this->select()
            ->setIntegrityCheck(false)
            ->from($taxTableName, array("$taxTableName.title", "$taxTableName.store_id"))
            ->join($taxRateTableName, "($taxRateTableName.tax_id = $taxTableName.tax_id)", array("$taxRateTableName.handling_type", "$taxRateTableName.tax_value"))
            ->where("$taxTableName.tax_id = $taxId")
            ->where("$taxRateTableName.status = 1");

    return $taxSelect;
  }
  
  public function isAdminTaxesEnabled()
  {
    $select = $this->select()
                   ->from($this->info('name'), 'tax_id')
                   ->where('store_id = 0 AND status = 1')
                   ->limit(1);

    return $select->query()->fetchColumn();
  }
  
  public function getEnabledTaxes() {
    $select = $this->select()
                   ->from($this->info('name'), 'tax_id')
                   ->where('status = 1')
                   ->where('is_vat = 0');

    return $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
  }
  
  public function getStoreVat($store_id, $attribs, $fetchRow = null) {
    $select = $this->select()
                   ->from($this->info('name'), $attribs)
                   ->where('store_id = ?', $store_id)
                   ->where('is_vat = 1')
                   ->limit(1);

    if(empty($fetchRow))
      return $select->query()->fetchColumn();
    else
      return $this->fetchRow($select);
  }
}