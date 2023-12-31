<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Changecategory.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Admin_Changecategory extends Engine_Form {

  public function init() {

    $this->setMethod('post');
    $this->setTitle("Change Category");
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOptions(array('placement' => 'PREPEND'));
    $this->getDecorator('Description')->setOption('escape', false);
    $this->setDescription('Select a category, sub-category and 3<sup>rd</sup> level category for this store from the list of categories and corresponding sub-categories given below and then click on "Save Changes" to save them.');
    $categories = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategories();
    if (count($categories) != 0) {
      $categories_prepared[0] = "";
      foreach ($categories as $category) {
        $categories_prepared[$category->category_id] = $category->category_name;
      }

      $this->addElement('Select', 'category_id', array(
          'label' => 'Category',
          'allowEmpty' => false,
          'required' => true,
          'multiOptions' => $categories_prepared,
          'onchange' => "subcategory(this.value, '', '');",
      ));
    }

    $this->addElement('Select', 'subcategory_id', array(
        'RegisterInArrayValidator' => false,
        'allowEmpty' => true,
        'required' => false,
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => 'application/modules/Sitestore/views/scripts/_formSubcategory.tpl',
                    'class' => 'form element')))
    ));

    $this->addElement('Select', 'subsubcategory_id', array(
        'RegisterInArrayValidator' => false,
        'allowEmpty' => true,
        'required' => false,
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => 'application/modules/Sitestore/views/scripts/_formSubcategory.tpl',
                    'class' => 'form element')))
    ));

    $this->addElement('Button', 'execute', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
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

    $this->addDisplayGroup(array(
        'execute',
        'cancel',
            ), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
    ));
  }

}

?>