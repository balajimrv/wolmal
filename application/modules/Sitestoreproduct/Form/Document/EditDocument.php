<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: EditDocument.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Document_EditDocument extends Sitestoreproduct_Form_Document_AddDocument {

  public function init() {
    parent::init();
    $this->setTitle('Edit Document');
    $this->setDescription('You can edit your document here.');
   
  }

}