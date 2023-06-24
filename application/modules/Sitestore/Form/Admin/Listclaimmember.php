<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Listclaimmember.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Admin_Listclaimmember extends Engine_Form {

  public function init() {

    $this->setMethod('post');
    $this->setTitle("Add Member")
            ->setDescription('Use the auto-suggest box given below to add a member on whose stores the "Claim this Store" link will appear. (Note that the added member will also have authority to decide whether his store should have this link or not.)');

    $label = new Zend_Form_Element_Text('title');
    $label->setLabel('Start typing the name of the member')
            ->addValidator('NotEmpty')
            ->setRequired(true)
            ->setAttrib('class', 'text')
            ->setAttrib('style', 'width:250px;');

    $this->addElement('Hidden', 'user_id', array());

    $this->addElements(array(
        $label,
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Add Member',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'onclick' => 'javascript:parent.Smoothbox.close()',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}

?>