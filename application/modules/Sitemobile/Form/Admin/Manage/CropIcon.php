<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: CropIcon.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Form_Admin_Manage_CropIcon extends Engine_Form {

  public function init() {
    $this
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAttrib('name', 'EditPhoto');

    $this->addElement('Hidden', 'coordinates', array(
        'filters' => array(
            'HtmlEntities',
        )
    ));
  }

}