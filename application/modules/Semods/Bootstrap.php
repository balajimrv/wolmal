<?php
/**
 * SocialEngineMods
 *
 * @category   Application_Extensions
 * @package    Semods
 * @copyright  Copyright 2008-2010 SocialEngineMods
 * @license    http://www.socialenginemods.net/license/
 */
class Semods_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application) {
    
    parent::__construct($application);

    $frontController = Zend_Controller_Front::getInstance();
    
    $frontController->registerPlugin( new Semods_Plugin_Loader() );

    // required?
    //$this->initViewHelperPath();

    $headScript = new Zend_View_Helper_HeadScript();
    $headScript->appendFile('application/modules/Semods/externals/scripts/semods.js');
    
    //$loader = new Semods_Loader();
    
    // @tbd - move MultiText
    // MultiSelect
    $this->initViewHelperPath();
    
  }
  
}