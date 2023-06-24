<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Manage.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_modules_Group_Form_Filter_Manage extends Engine_Form {

  public function init() {
    $this->clearDecorators()
            ->addDecorators(array(
                'FormElements',
                array('HtmlTag', array('tag' => 'dl')),
                'Form',
            ))
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setMethod('get')
    ;

    $this->addElement('Text', 'text', array(
        'label' => 'Search:',
        'decorators' => array(
            'ViewHelper',
            array('HtmlTag', array('tag' => 'dd')),
            array('Label', array('tag' => 'dt', 'placement' => 'PREPEND'))
        )
    ));

    $this->addElement('Select', 'view', array(
        'label' => 'View:',
        'multiOptions' => array(
            '1' => 'All My Groups',
            '2' => 'Only Groups I Lead',
        ),
        'decorators' => array(
            'ViewHelper',
            array('HtmlTag', array('tag' => 'dd')),
            array('Label', array('tag' => 'dt', 'placement' => 'PREPEND'))
        ),
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Search',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
            array('HtmlTag', array('tag' => 'dd'))
        ),
    ));
  }

}