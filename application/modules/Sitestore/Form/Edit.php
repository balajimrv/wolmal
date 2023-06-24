<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Edit extends Sitestore_Form_Create {

  public $_error = array();
  protected $_item;

  public function getItem() {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item) {
    $this->_item = $item;
    return $this;
  }

  public function init() {
    // call the init of create form


    parent::init();
    $sitestore = $this->getItem();

    $this->setTitle('Edit Store Info')
            ->setDescription('Edit the information of your store and keep it updated.');
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.category.edit', 0) && !empty($sitestore->category_id)) {
      $this->getElement('category_id')
              ->setIgnore(true)
              ->setAttrib('disable', true)
              ->clearValidators()
              ->setRequired(false)
              ->setAllowEmpty(true)
      ;
    }

    $enableSitestoreproduct = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreproduct');
    if( !empty($enableSitestoreproduct) ){
      $this->removeElement('cancel');
      $this->removeDisplayGroup('buttons');
      $this->addDisplayGroup(array(
          'execute',
  //        'cancel',
              ), 'buttons', array(
          'decorators' => array(
              'FormElements',
              'DivDivDivWrapper'
          ),
      ));
    }

    if ($this->location)
      $this->removeElement('location');
    
    
    $this->addElement('Checkbox', 'search', array(
        'label' => 'Show this store in search results.',
        'value' => 1,
        'onchange' => 'showProductStatusRadio();'
    ));
      
    $this->execute->setLabel('Save Changes');
  }

}

?>