<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Metainfo.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Metainfo extends Engine_Form {

  public function init() {

    $this->setTitle('Meta Keywords')
            ->setDescription("Meta keywords are a great way to provide search engines with information about your product so that search engines populate your product in search results. Below, you can add meta keywords for this product. (The tags entered by you for this product will also be added to the meta keywords.)")
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'metainfo');

    $this->addElement('Textarea', 'keywords', array(
        'label' => 'Meta Keywords',
        'description' => 'Separate meta tags with commas.',
        'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                 ),
    ));

    $this->keywords->getDecorator('Description')->setOption('placement', 'append');
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Details',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formMetaInfo.tpl',
                    'class' => 'form element'))),
    ));
  }

}