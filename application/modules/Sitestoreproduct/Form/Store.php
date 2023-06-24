<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Address.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Store extends Engine_Form {
  
  public function init() {
        $this->setTitle('Your Stores')
            ->setDescription('Select a store to add products.');
        $multiOptions = array();
      //  $multiOptions[0] = '';
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $storeTable = Engine_Api ::_()->getDbTable('stores','sitestore');
        $select =    $storeTable->select() 
                                ->from($storeTable->info('name'),array('store_id','title'))
                                ->where('owner_id = ?', $viewer_id);
        $stores = $storeTable->fetchAll($select);
        foreach ($stores as $store) {
       
                $multiOptions[$store['store_id']] = $store['title'];
        }

      $this->addElement('Select', 'stores', array(
          'label' => 'Stores',
          'multiOptions' => $multiOptions,
          'value' => '',
          'setAllowEmpty'=>false,
          'required'=>true
      ));
     
   
    
      $this->addElement('Button', 'submit', array(
        'label' => 'Submit',
        'ignore' => true,
        'type' => 'submit',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'prependText' => ' or ',
        'type' => 'link',
        'link' => true,
        'onclick' => 'parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}