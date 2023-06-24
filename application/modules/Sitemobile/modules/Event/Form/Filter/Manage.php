<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Manage.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_modules_Event_Form_Filter_Manage extends Engine_Form {

  public function init() {
    $this->clearDecorators()
            ->addDecorators(array(
                'FormElements',
                array('HtmlTag', array('tag' => 'dl')),
                'Form',
            ))
            ->setMethod('get')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('class', 'filters')
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
            '1' => 'All My Events',
            '2' => 'Only Events I Lead',
        ),
        'decorators' => array(
            'ViewHelper',
            array('HtmlTag', array('tag' => 'dd')),
            array('Label', array('tag' => 'dt', 'placement' => 'PREPEND'))
        )
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