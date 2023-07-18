<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Authorization
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Create.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Authorization
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Authorization_Form_Admin_Level_Create extends Engine_Form
{
  public function init()
  {
    // Set form attributes
    $this->setTitle('Create Member Level');
    $this->setDescription("AUTHORIZATION_FORM_ADMIN_LEVEL_EDIT_DESCRIPTION");

    // Element: title
    $this->addElement('Text', 'title', array(
      'label' => 'Member Level Name',
      'allowEmpty' => false,
      'required' => true,
    ));

    // Element: description
    $this->addElement('Textarea', 'description', array(
      'label' => 'Description',
      'allowEmpty' => true,
      'required' => false,
    ));

    // Element: type
    $this->addElement('Select', 'type', array(
      'label' => 'Type',
      'description' => 'The type cannot be changed after creation.',
      'multiOptions' => array(
        'admin' => 'Administrator',
        'moderator' => 'Moderator',
        'user' => 'Normal',
      ),
      'value' => 'user',
    ));
    $this->type->getDecorator('Description')->setOption('placement', 'append');

    // Element: parent
    $defaultLevelIdentity = null;
    $parentMultiOptions = array();
	
	/*$level_tbl = Engine_Api::_()->getDbtable('levels', 'authorization');
	$select = new Zend_Db_Select($level_tbl->getAdapter());
	$select = $level_tbl->select()->order('level_id DESC');
    $user_levels = $table->fetchAll($select);*/
	
	$level_tbl = Engine_Api::_()->getDbtable('levels', 'authorization');
	foreach( $level_tbl->fetchAll($level_tbl->select()->order('level_order ASC')) as $level ) {
      if( $level->type == 'public' ) {
        continue;
      }
      $parentMultiOptions[$level->level_id] = $level->getTitle() . ' (' . $this->type->options[$level->type] . ')';
      if( $level->flag == 'default' ) {
        $defaultLevelIdentity = $level->level_id;
      }
    }
    $this->addElement('Select', 'parent', array(
      'label' => 'Copy Values From:',
      'description' => 'You must select a level that is the same type as selected above.',
      'multiOptions' => $parentMultiOptions,
      'value' => $defaultLevelIdentity,
    ));
    $this->parent->getDecorator('Description')->setOption('placement', 'append');
	
	// Element: title
    $this->addElement('Text', 'award', array(
      'label' => 'Award',
      'allowEmpty' => false,
      'required' => false,
    ));
	
	// Element: title
    $this->addElement('Text', 'reward', array(
      'label' => 'Reward',
      'allowEmpty' => false,
      'required' => false,
    ));
	
	$level_tbl = Engine_Api::_()->getDbtable('levels', 'authorization');
	$fetch_order = $level_tbl->fetchRow($level_tbl->select('level_order')->order('level_order DESC'));
	$level_order = $fetch_order['level_order'] + 1;
	// Element: Order
    $this->addElement('Text', 'level_order', array(
      'label' => 'Level Order',
      'allowEmpty' => false,
      'required' => true,
	  'value' => $level_order,
    ));


    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Create Level',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => 'admin/levels',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }
}