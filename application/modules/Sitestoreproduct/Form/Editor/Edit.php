<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Editor_Edit extends Sitestoreproduct_Form_Editor_Create {

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
    parent::init();
    $product_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('product_id');
    $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
    $this->loadDefaultDecorators();
    $sitestoreproduct_title = "<b>" . $sitestoreproduct->getTitle() . "</b>";

    $this
            ->setTitle('Edit an Editor Review')
            ->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("You can edit the editor review for %s below:"), $sitestoreproduct_title))
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))->getDecorator('Description')->setOption('escape', false);

    $this->submit->setLabel('Save Changes');
  }

}