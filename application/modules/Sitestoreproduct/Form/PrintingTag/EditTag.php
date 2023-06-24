<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: EditTag.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_PrintingTag_EditTag extends Sitestoreproduct_Form_PrintingTag_AddTag {

  public function init() {
    parent::init();
    $this->setTitle('Edit tag configuration');
    $this->setDescription('Manage your Printing Tag information to keep it updated for your products.');
    $this->submit->setLabel('Save Changes');
  }

}