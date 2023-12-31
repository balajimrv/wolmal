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
class Sitestoreproduct_Form_Wishlist_Edit extends Sitestoreproduct_Form_Wishlist_Create {

  public function init() {
    
    parent::init();
    $this->setTitle('Edit Wishlist')
            ->setDescription('Edit your wishlist over here and then click on "Save Changes" to save it.');
    $this->submit->setLabel('Save Changes');
  }

}