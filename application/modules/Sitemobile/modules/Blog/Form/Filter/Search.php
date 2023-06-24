<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Search.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_modules_Blog_Form_Filter_Search extends Engine_Form {

  public function init() {
    $this
            ->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
            ))
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setMethod('GET')
    ;

    $this->addElement('Text', 'search', array(
        'label' => 'Search Blogs'
    ));

    $this->addElement('Select', 'orderby', array(
        'label' => 'Browse By',
        'multiOptions' => array(
            'creation_date' => 'Most Recent',
            'view_count' => 'Most Viewed',
        )
    ));

    $this->addElement('Select', 'draft', array(
        'label' => 'Show All Entries',
        'multiOptions' => array(
            '' => 'All Entries',
            '0' => 'Only Published Entries',
            '1' => 'Only Drafts',
        )
    ));

    $this->addElement('Select', 'show', array(
        'label' => 'Show',
        'multiOptions' => array(
            '1' => 'Everyone\'s Blogs',
            '2' => 'Only My Friends\' Blogs',
        )
    ));

    $this->addElement('Select', 'category', array(
        'label' => 'Category',
        'multiOptions' => array(
            '0' => 'All Categories',
        )
    ));

		$categories = Engine_Api::_()->getDbtable('categories', 'blog')->getCategoriesAssoc();
		if (!empty($categories) && is_array($categories) && $this->getElement('category')) {
			$this->getElement('category')->addMultiOptions($categories);
		}

    $this->addElement('Hidden', 'page', array(
        'order' => 100
    ));

    $this->addElement('Hidden', 'tag', array(
        'order' => 101
    ));

    $this->addElement('Hidden', 'start_date', array(
        'order' => 102
    ));

    $this->addElement('Hidden', 'end_date', array(
        'order' => 103
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Search',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}