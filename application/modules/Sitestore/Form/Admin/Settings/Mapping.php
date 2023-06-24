<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Sitestore
* @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Mapping.php 2013-09-02 00:00:00Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class Sitestore_Form_Admin_Settings_Mapping extends Engine_Form
{
  public function init()
  {
		$this
		->setTitle('Delete Category')
		->setDescription('If you want to map Stores belongs to this category, with other category then select the new category otherwise leave the drop-down blank.');

    $this->addElement('Button', 'submit', array(
      'label' => 'Submit',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

		$category_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('catid', null);
    $categories = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategories(null);
    if (count($categories) != 0) {
      $categories_prepared[0] = "";
      foreach ($categories as $category) {
				if($category_id != $category->category_id) {
					$categories_prepared[$category->category_id] = $category->category_name;
				}
      }

			$this->addElement('Select', 'new_category_id', array(
					'label' => 'Category',
					'multiOptions' => $categories_prepared
			));
		}

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
			'onclick' => 'javascript:closeSmoothbox()',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
	}

}