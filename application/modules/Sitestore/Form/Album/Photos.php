<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Photos.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Album_Photos extends Engine_Form {

  public function init() {

    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
    $this->addElement('Radio', 'cover', array(
        'label' => 'Album Cover',
    ));
    
//     if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
// 			$this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
// 			$this->addElement('Radio', 'store_cover', array(
// 					'label' => 'Make Cover Photo',
// 			));
//     }
    
  }

}

?>