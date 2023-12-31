<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Authorization
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Abstract.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Authorization
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Authorization_Form_Admin_Level_Abstract extends Engine_Form
{
  protected $_moderator;

  protected $_public;

  protected $_nonBooleanFields =array();

  protected $_nonBooleanFormElements = array(
    'Engine_Form_Element_Text',
    'Engine_Form_Element_Select',
    'Engine_Form_Element_MultiCheckbox'
  );

  public function setModerator($moderator)
  {
    $this->_moderator = (bool) $moderator;
    return $this;
  }

  public function isModerator()
  {
    return (bool) $this->_moderator;
  }

  public function setPublic($public)
  {
    $this->_public = (bool) $public;
    return $this;
  }

  public function isPublic()
  {
    return (bool) $this->_public;
  }

  public function setNonBooleanFormElements($nonBooleanFormElements)
  {
    if( !is_array($nonBooleanFormElements) ) {
      return;
    }
    $this->_nonBooleanFormElements = $nonBooleanFormElements;
    return $this;
  }

  // Form elements with NonBoolean values
  public function nonBooleanFields()
  {
    $formElements = $this->getElements();
    foreach( $formElements as $element ) {
      if( in_array($element->getType(), $this->_nonBooleanFormElements) ) {
        $this->_nonBooleanFields[] = $element->getName();
      }
    }
    return $this->_nonBooleanFields;
  }

  public function init()
  {
    // Change description decorator
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));

    // Prepare user levels
    $levelOptions = array();
    
	$level_tbl = Engine_Api::_()->getDbtable('levels', 'authorization');
	foreach( $level_tbl->fetchAll($level_tbl->select()->order('level_order ASC')) as $level ) {
      $levelOptions[$level->level_id] = $level->getTitle();
    }

    // Element: level_id
    $this->addElement('Select', 'level_id', array(
      'label' => 'Member Level',
      'multiOptions' => $levelOptions,
      'onchange' => 'javascript:fetchLevelSettings(this.value);',
      'ignore' => true,
    ));




    // Add submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'order' => 100000,
    ));
  }
}