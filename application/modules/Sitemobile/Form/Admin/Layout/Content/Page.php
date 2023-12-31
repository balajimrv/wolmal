<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Page.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Form_Admin_Layout_Content_Page extends Engine_Form {

  public function init() {
    $this
            ->setMethod('post')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'save', 'controller' => 'content', 'module' => 'sitemobile'), 'admin_default', true))
            ->setAttrib('class', 'admin_layoutbox_menu_editinfo_form')
            ->setAttrib('id', 'admin_content_pageinfo')
            ->clearDecorators()
            ->addDecorator('FormElements')
            ->addDecorator('HtmlTag', array('tag' => 'ul'))
            ->addDecorator('FormErrors', array('placement' => 'PREPEND', 'escape' => false))
            ->addDecorator('FormMessages', array('placement' => 'PREPEND', 'escape' => false))
            ->addDecorator('Form')
    ;

    $this->addElement('Text', 'displayname', array(
        'label' => 'Page Name <span>(for your reference only)</span>',
        'decorators' => array(
            array('ViewHelper'),
            array('Label', array('tag' => 'span', 'escape' => false)),
            array('HtmlTag', array('tag' => 'li')),
        ),
    ));

    $this->addElement('Text', 'title', array(
        'label' => 'Page Title <span>(title tag)</span>',
        'decorators' => array(
            array('ViewHelper'),
            array('Label', array('tag' => 'span', 'escape' => false)),
            array('HtmlTag', array('tag' => 'li')),
        ),
    ));

    $siteURL = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'default', true) . 'pages/';
    $this->addElement('Text', 'url', array(
        'label' => 'Page URL',
        'description' => 'The URL may only contain alphanumeric characters and dashes - any other characters will be stripped. The full url will be http://' . $siteURL . '[url]',
        'filters' => array(
            array('PregReplace', array('/[^a-z0-9]+|[-]{2,}/i', '-')),
            array('StringTrim', array("- \n\r\t"))
        ),
        'decorators' => array(
            array('ViewHelper'),
            array('Description', array('escape' => false, 'placement' => 'append')),
            array('Label', array('tag' => 'span', 'escape' => false)),
            array('HtmlTag', array('tag' => 'li')),
        ),
    ));

    $this->addElement('Text', 'description', array(
        'label' => 'Page Description <span>(meta tag)</span>',
        'decorators' => array(
            array('ViewHelper'),
            array('Label', array('tag' => 'span', 'escape' => false)),
            array('HtmlTag', array('tag' => 'li')),
        ),
    ));

    $this->addElement('Text', 'keywords', array(
        'label' => 'Page Keywords <span>(meta tag)</span>',
        'allowEmpty' => false,
        'validators' => array(
        ),
        'decorators' => array(
            array('ViewHelper'),
            array('Label', array('tag' => 'span', 'escape' => false)),
            array('HtmlTag', array('tag' => 'li')),
        ),
    ));

    // Page Audience
    // prepare levels
	$level_tbl = Engine_Api::_()->getDbtable('levels', 'authorization');
	foreach( $level_tbl->fetchAll($level_tbl->select()->order('level_order ASC')) as $level ) {
      $levels_prepared[$level->getIdentity()] = $level->getTitle();
    }
    reset($levels_prepared);
    $this->addElement('Multiselect', 'levels', array(
        'label' => 'Viewing Permissions <span>(member levels)</span>',
        'description' => 'SITEMOBILE_FORM_ADMINS_LAYOUT_CONTENT_PAGE_LEVELS_DESCRIPTION',
        'multiOptions' => $levels_prepared,
        'decorators' => array(
            array('ViewHelper'),
            array('Description', array('escape' => false, 'placement' => 'prepend')),
            array('Label', array('tag' => 'span', 'escape' => false)),
            array('HtmlTag', array('tag' => 'li')),
        ),
    ));

    $this->addElement('Checkbox', 'search', array(
        'label' => 'Show this page in search results?',
    ));

    $this->addElement('Hidden', 'page_id', array(
        'validators' => array(
            array('NotEmpty'),
            array('Int'),
        ),
    ));
  }

}