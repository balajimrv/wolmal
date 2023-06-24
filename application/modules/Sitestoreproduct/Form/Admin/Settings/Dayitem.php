<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Dayitem.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreproduct_Form_Admin_Settings_Dayitem extends Engine_Form {

  //Changes in onchange event function for mobile mode.
  protected $_mode = false;

  public function getMode() {
    return $this->_mode;
  }

  public function setMode($mode) {
    $this->_mode = $mode;
    return $this;
  }
  
  public function init() {
   
    $this->setMethod('post');
    $this->setTitle('Product of the Day')
         ->setDescription('Displays Product of the day as selected by you from below. You can use this widget to highlight any Product created at your site using the auto-suggest box below.');
    
    $this->addElement('Select', 'ratingType', array(
        'label' => 'Rating Type',
        'multiOptions' => array('rating_avg' => 'Avg Rating', 'rating_editor' => 'Editor Rating', 'rating_users' => 'User Rating', 'rating_both' => 'Both User and Editor Rating')
    ));
   
//    DISPLAYS THIS SETTING OPTION ONLY WHEN DAYITEM FOR FULLSITE
    if (!$this->getMode()) {
    $this->addElement('Radio', 'add_to_cart', array(
        'label' => "Do you want to show cart options like 'Add to Cart'or 'Out of Stock'.",
        'multiOptions' => array('1' => 'Yes', '0' => 'No'),
        'value' => 1
    ));
    }
    
    $this->addElement('Radio', 'in_stock', array(
        'label' => "Do you want to show the available stock quantity.",
        'multiOptions' => array('1' => 'Yes', '0' => 'No'),
        'value' => 1
    ));

    //VALUE FOR BORDER COLOR.
    $this->addElement('Text', 'product_title', array(
        'label' => 'Product',
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '/application/modules/Sitestoreproduct/views/scripts/admin-settings/add-day-item.tpl',
                    'class' => 'form element')))
    ));

    $this->addElement('text', 'product_id', array());

    // Start time
    $start = new Engine_Form_Element_CalendarDateTime('starttime');
    $start->setLabel("Start Time");
    $start->setAllowEmpty(false);
    $this->addElement($start);
    
    // End time
    $end = new Engine_Form_Element_CalendarDateTime('endtime');
    $end->setLabel("End Time");
    $end->setAllowEmpty(false);
    $this->addElement($end);
    
    //SHOW PREFIELD START AND END DATETIME
    $httpReferer = $_SERVER['HTTP_REFERER'];
    if(!empty($httpReferer) && strstr($httpReferer,'?page=')) { 
      $httpRefererArray = explode('?page=', $httpReferer);
      $store_id = (int) $httpRefererArray['1'];
      if(!empty($store_id) && is_numeric($store_id)) {
        
        //GET CONTENT TABLE
        $tableContent = Engine_Api::_()->getDbtable('content', 'core');
        $tableContentName = $tableContent->info('name');

        //GET CONTENT
        $params = $tableContent->select()
                ->from($tableContentName, array('params'))
                ->where('page_id = ?', $store_id)
                ->where('name = ?', 'sitestoreproduct.item-sitestoreproduct')
                ->query()
                ->fetchColumn();      
        
        if(!empty($params)) {
          $params = Zend_Json_Decoder::decode($params);
          if(isset($params['starttime']) && !empty($params['starttime'])) {
            $start->setValue($params['starttime']);
          }

          if(isset($params['endtime']) && !empty($params['endtime'])) {
            $end->setValue($params['endtime']);
          }
        }
      }
    }    

    //$this->addElement('Hidden', 'nomobile', array());
  }

}