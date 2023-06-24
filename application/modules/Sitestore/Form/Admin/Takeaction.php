<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Takeaction.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Admin_Takeaction extends Engine_Form {

  public function init() {

    $store_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_id', null);
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $url = $view->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($store_id)), 'sitestore_entry_view', true);
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $sitestore_title = "<a href='$url' target='_blank'>$sitestore->title</a>";

    $this->setMethod('post');
    $this->setTitle("Take an Action")
            ->setDescription("Please take an appropriate action for this store:" . $sitestore_title);

    $this->addElement('Button', 'submit', array(
        'label' => 'Save',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}

?>