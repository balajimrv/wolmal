<?php

class Semods_Plugin_Core
{
  
  public function getAdminNotifications($event)
  {
    
    $this->checkUpdates();
    $this->checkStatistics();

    if((Semods_Utils::getSetting('semods.upgrade', 0) != 0)) {

      $translate = Zend_Registry::get('Zend_Translate');
      $message = vsprintf($translate->translate(array(
        'Please update the SocialEngineMods Core Library for compatibility, <a style="color: #FFF; font-weight: bold" href="%s">click here for quick upgrade</a>',
      )), array(
        Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'semods', 'controller' => 'upgrade'), 'admin_default', true),
      ));
      
      $message = '<ul class="form-errors"><li><ul class="errors"><li>' . $message . '</li></ul></li></ul>';
  
      $event->addResponse($message);
      
    }
    
  }
  
  public function checkUpdates() {

    if((time() - Semods_Utils::getSetting('semods.packages.lastcheck', 0)) < Semods_Utils::getSetting('semods.packages.checkperiod', 43200) ) {
      return;
    }
    
    Semods_Utils::setSetting('semods.packages.lastcheck', time());
    
    $product = 'semods';
    
    $info = Engine_Api::_()->getDbtable('modules', 'core')->getModule($product);
    
    //$package = sprintf('%s-%s-%s', $info['type'], $info['name'], $info['version']);
    $package = sprintf('%s-%s-%s', 'module', $info['name'], $info['version']);
    
    $client = new Zend_Http_Client();
    $client->setUri( 'http://www.socialenginemods.net/updates' );
    $client->setConfig(array('timeout' => 2));
    $client->setMethod(Zend_Http_Client::GET)->setParameterGet(array(
        'key'   => Engine_Api::_()->getApi('settings', 'core')->getSetting('core.license.key'),
        'host'  => $_SERVER['HTTP_HOST'],
        'guid' => $package,
      ))
      ;

    $response = $client->request();

    if($response->isError()) {
      return;
    }
    
    $ret = Zend_Json::decode($response->getBody());

    if( !is_array($ret) || @$ret['responseStatus'] !== 200 ) {
      return;
    }
    
    if($ret['update'] == 1) {
      Semods_Utils::setSetting('semods.upgrade', 1);
    }
    
    
  }

  public function checkStatistics() {

    if( Engine_Api::_()->getApi('settings', 'core')->semods_statistics_disable ) {
      return;
    }

    if((time() - Semods_Utils::getSetting('semods.statistics.lastcheck', 0)) < Semods_Utils::getSetting('semods.statistics.checkperiod', 43200) ) {
      return;
    }

    Semods_Utils::setSetting('semods.statistics.lastcheck', time());
    
    Engine_Api::_()->semods()->statistics();
    
  }
  
}