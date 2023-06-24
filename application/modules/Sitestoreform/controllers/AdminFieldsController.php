<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreform
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminFieldsController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreform_AdminFieldsController extends Fields_Controller_AdminAbstract
{
  protected $_fieldType = 'sitestoreform';

  protected $_requireProfileType = false;

  public function indexAction()
  {
    //GET NAVIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestoreform');    

    //CREATE NAVIGATION TABS
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sitestoreform_admin_main', array(), 'sitestoreform_admin_main_fields');

    // Set data
    $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps($this->_fieldType);
    $metaData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMeta($this->_fieldType);
    $optionsData = Engine_Api::_()->getApi('core', 'fields')->getFieldsOptions($this->_fieldType);

    // Get top level fields
    $topLevelMaps = $mapData->getRowsMatching(array('field_id' => 1, 'option_id' => 0));
    $topLevelFields = array();
    foreach( $topLevelMaps as $map ) {
      $field = $map->getChild();
      $topLevelFields[$field->field_id] = $field;
    }
    $this->view->topLevelMaps = $topLevelMaps;
    $this->view->topLevelFields = $topLevelFields;
    
    // Do we require profile type?
    // No
    if( !$this->_requireProfileType ) {
      $this->topLevelOptionId = '0';
      $this->topLevelFieldId = '0';
    }
  }

  public function fieldCreateAction(){
    parent::fieldCreateAction();

		//GET FORM
    $form = $this->view->form;

    if($form){
      $form->setTitle('Add Form Question');
      $form->removeElement('search');
      $form->addElement('hidden', 'search', array('value' => 0,'order' => 1000));
      $form->removeElement('display');
      $form->addElement('hidden', 'display', array('value' => 0,'order' => 10001));
			$form->removeElement('show');
		  $form->addElement('hidden', 'show', array('value' => 0));
		  $form->removeElement('error');
		  $form->removeElement('style');
    }
    
    if ($this->getRequest()->getPost()) {
      $formMapTable = Engine_Api::_()->fields()->getTable('sitestoreform', 'maps');
      $formMapTable->update(array('field_id' => 1), array('option_id = ?' => 0, 'field_id = ?' => 0, '`order` != ?' => 1));
    }
  }

  public function fieldEditAction(){
    parent::fieldEditAction();
    
		//GET FORM
    $form = $this->view->form;

    if($form){
      $form->setTitle('Edit Form Question');
      $form->removeElement('search');
      $form->removeElement('display');
			$form->removeElement('show');
		  $form->addElement('hidden', 'show', array('value' => 0));
		  $form->removeElement('error');
		  $form->removeElement('style');
    }
  }
}