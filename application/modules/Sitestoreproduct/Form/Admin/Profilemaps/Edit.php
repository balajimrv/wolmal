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

class Sitestoreproduct_Form_Admin_Profilemaps_Edit extends Engine_Form {

  public function init() {
    
    $this->loadDefaultDecorators();
    $this->setMethod('post')
            ->setTitle("Edit Profile Type")
            ->setAttrib('class', 'global_form_box')
            ->setDescription("After selecting a profile type, click on 'Save'.");
    
    $this->getDecorator('Description')->setOption('escape', false);
    //GET CURRENT PROFILE TYPE
    $profile_type = Zend_Controller_Front::getInstance()->getRequest()->getParam('profile_type', null);

    $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('sitestoreproduct_product');
    if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
      $profileTypeField = $topStructure[0]->getChild();
      $options = $profileTypeField->getOptions();

      if (count($options) > 0) {
        $options = $profileTypeField->getElementParams('sitestoreproduct_product');
        unset($options['options']['order']);
        unset($options['options']['multiOptions']['0']);

        //REMOVE CURRENT PROFILE TYPE
        unset($options['options']['multiOptions'][$profile_type]);

        $this->addElement('Select', 'profile_type', array_merge($options['options'], array(
                    'required' => true,
                    'allowEmpty' => false,
                )));
      } else if (count($options) == 1) {
        $this->addElement('Hidden', 'profile_type', array(
            'value' => $options[0]->option_id
        ));
      }
    }

    $this->addElement('Button', 'yes_button', array(
        'label' => 'Save',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => '',
        'onClick' => 'javascript:parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));
    $this->addDisplayGroup(array('yes_button', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}