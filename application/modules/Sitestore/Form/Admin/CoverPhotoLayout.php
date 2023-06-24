<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Layoutdefault.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Admin_CoverPhotoLayout extends Engine_Form {

  public function init() {

    $this
            ->setAttrib('id', 'cover-form-upload')
						->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'save-cover-store-layout')));

    $this->addElement('Radio', 'sitestore_layout_coverphoto', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formRadioButtonStructureCover.tpl',
                    'class' => 'form element'
            )))));

    $this->addElement('Button', 'submit2', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));
  }

}

?>