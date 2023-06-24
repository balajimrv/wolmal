<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AddRegion.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreproduct_Form_Admin_Location_AddRegion extends Sitestoreproduct_Form_Admin_Location_AddLocation {

  public function init() {
    
    parent::init();
    
    $this->setTitle('Add Regions / States');
    
    $this->removeElement('Add Regions / States');
    $this->removeElement('all_regions');
    
    $this->addElement('Text', 'country', array(
        'label' => 'Country',
        'order' => 0,
        'attribs' => array('disabled' => 'disabled'),
    ));
    
    $this->submit->setLabel('Add Regions / States');
  }

}