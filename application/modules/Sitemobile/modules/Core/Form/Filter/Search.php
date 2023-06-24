<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Search.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_modules_Core_Form_Filter_Search extends Engine_Form
{
  public function init()
  {
    
    $this
      ->setMethod('get')
      ->setDecorators(array('FormElements', 'Form'));
       $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $this->setAction($view->url(array('action' => 'index','controller'=>'search','module'=>'core'), 'default', true));
    
    $this->addElement('Text', 'query', array(
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Select', 'type', array(
      'multiOptions' => array(
        '' => 'Everything',
      ),
      'decorators' => array(
        'ViewHelper',
      ),
    ));
$searchApi = Engine_Api::_()->getApi('globalsearch', 'sitemobile');
//$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    // Get available types
    $availableTypes = $searchApi->getAvailableTypes();
    if (is_array($availableTypes) && count($availableTypes) > 0) {
      $options = array();
      foreach ($availableTypes as $index => $type) {
        $options[$type] = $view->translate(strtoupper('ITEM_TYPE_' . $type));
      }
      $this->type->addMultiOptions($options);
    } else {
      $this->removeElement('type');
    }
    $this->addElement('Button', 'submit', array(
      'label' => 'Search',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
  }
}