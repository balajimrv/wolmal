<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Taxrates.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Taxrates extends Engine_Db_Table {

  protected $_name = 'sitestoreproduct_tax_rates';
  protected $_rowClass = 'Sitestoreproduct_Model_Taxrate';

  /**
   * Return tax rate object
   *
   * @param array $params
   * @return object
   */
  public function getTaxRatesPaginator($params=array()) {

    $paginator = Zend_Paginator::factory($this->getTaxRatesSelect($params));
    if (!empty($params['page']))
      $paginator->setCurrentPageNumber($params['page']);

    if (empty($params['limit']))
      $paginator->setItemCountPerPage(20);
    else
      $paginator->setItemCountPerPage($params['limit']);

    return $paginator;
  }

  public function getTaxRatesSelect($params) {

    $taxRateTableName = $this->info('name');
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
    $enableCountryString = @implode(',', $regionArray);
    
    $select = $this->select()
                   ->setIntegrityCheck(false)
                   ->from($taxRateTableName)
                   ->joinLeft($regionsTableName, '(' . $taxRateTableName . '.state=' . $regionsTableName . '.region_id)', array($regionsTableName . '.region'))
                   ->where("($taxRateTableName.country LIKE 'ALL') OR ($taxRateTableName.state = 0 and $taxRateTableName.country in ($enableCountryString)) OR ($taxRateTableName.state != 0 AND $regionsTableName.country_status = 1 AND $regionsTableName.status = 1)");

    if (!empty($params['status'])) {
      $select->where("$taxRateTableName.status = ?", $params['status']);
    }

    if (!empty($params['tax_id'])) {
      $select->where('tax_id = ?', $params['tax_id']);
    }

    $select->order($taxRateTableName . '.creation_date DESC')
           ->order("$taxRateTableName.taxrate_id DESC");

    return $select;
  }

  /**
   * Return tax rate of tax_id
   *
   * @param array $params
   * @return object
   */
  public function getTaxRatesById($params = array() ) {
    $select = $this->getTaxRatesSelect($params);
    return $this->fetchAll($select);
  }
  
  /**
   * Check added tax rates locations
   *
   * @param array $params
   * @return array
   */
  public function checkAddedRatesLocations($params){
    
    $returnArray = array();
    $selectAllContry = $this->select()
                            ->from($this->info('name'), array('country'))
                            ->where('tax_id = ?', $params['tax_id'])
                            ->where("country LIKE 'ALL'")
                            ->limit(1)
                            ->query()
                            ->fetch();
    
    if(!empty($selectAllContry)){
      $returnArray['all_country'] = 1;
    }else{
      $returnArray['all_country'] = 0;
    }
    
    $shippingCountries = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->getCountryAddTaxRate(array('tax_id' => $params['tax_id']));

    if(empty($shippingCountries)){
     $returnArray['all_regions'] = 1;
    }else{
     $returnArray['all_regions'] = 0;
    }

    return $returnArray;
  }
  
  public function getVatAttribs($vat_id) {
    $select = $this->select()
                   ->from($this->info('name'), array("handling_type", "tax_value"))
                   ->where('tax_id = ?', $vat_id);

    return $this->fetchRow($select);
  }

}