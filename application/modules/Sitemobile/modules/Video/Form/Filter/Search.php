<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Search.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_modules_Video_Form_Filter_Search extends Engine_Form {

  public function init() {
    $this
            ->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
            ))
            ->setMethod('GET')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
    ;
    // prepare categories
    $categories = Engine_Api::_()->video()->getCategories();
    $categories_prepared[0] = "All Categories";
    foreach ($categories as $category) {
      $categories_prepared[$category->category_id] = $category->category_name;
    }

    $this->addElement('Text', 'text', array(
        'label' => 'Search',
    ));

    $this->addElement('Hidden', 'tag');

    $this->addElement('Select', 'orderby', array(
        'label' => 'Browse By',
        'multiOptions' => array(
            'creation_date' => 'Most Recent',
            'view_count' => 'Most Viewed',
            'rating' => 'Highest Rated',
        )
    ));

    // category field
    $this->addElement('Select', 'category', array(
        'label' => 'Category',
        'multiOptions' => $categories_prepared
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Search',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}