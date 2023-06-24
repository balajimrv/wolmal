<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Browse.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_modules_Event_Form_Filter_Browse extends Engine_Form {

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
            ->setAttrib('id', 'filters')
    ;

    $this->addElement('Text', 'search_text', array(
        'label' => 'Search Events:',
        'decorators' => array(
            'ViewHelper',
            array('HtmlTag', array('tag' => 'dd')),
            array('Label', array('tag' => 'dt', 'placement' => 'PREPEND'))
        ),
    ));

    $this->addElement('Select', 'category_id', array(
        'label' => 'Category:',
        'data-rel' => "dialog",
        'multiOptions' => array(
            '' => 'All Categories',
        ),
        'decorators' => array(
            'ViewHelper',
            array('HtmlTag', array('tag' => 'dd')),
            array('Label', array('tag' => 'dt', 'placement' => 'PREPEND'))
        ),
    ));

		foreach (Engine_Api::_()->getDbtable('categories', 'event')->select()->order('title ASC')->query()->fetchAll() as $row) {
			$this->category_id->addMultiOption($row['category_id'], $row['title']);
		}

    $this->addElement('Select', 'view', array(
        'label' => 'View:',
        'multiOptions' => array(
            '0' => 'Everyone\'s Events',
            '1' => 'Only My Friends\' Events',
        ),
        'decorators' => array(
            'ViewHelper',
            array('HtmlTag', array('tag' => 'dd')),
            array('Label', array('tag' => 'dt', 'placement' => 'PREPEND'))
        ),
    ));

    $this->addElement('Select', 'order', array(
        'label' => 'List By:',
        'multiOptions' => array(
            'starttime ASC' => 'Start Time',
            'creation_date DESC' => 'Recently Created',
            'member_count DESC' => 'Most Popular',
        ),
        'decorators' => array(
            'ViewHelper',
            array('HtmlTag', array('tag' => 'dd')),
            array('Label', array('tag' => 'dt', 'placement' => 'PREPEND'))
        ),
        'value' => 'creation_date DESC',
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