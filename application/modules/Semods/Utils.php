<?php

class Semods_Utils
{

  protected static $_cache = null;
  
  public static function getCache($cache_key)
  {
      
    if(is_null(self::$_cache)) {
      self::$_cache = (Zend_Registry::get('Zend_Cache') instanceof Zend_Cache_Core) ? Zend_Registry::get('Zend_Cache') : false;
    }
    
    if(self::$_cache) {
      return self::$_cache->load($cache_key);
    }
    
    return null;
      
  }


  public static function setCache($data, $cache_key)
  {
      
    if(is_null(self::$_cache)) {
      self::$_cache = (Zend_Registry::get('Zend_Cache') instanceof Zend_Cache_Core) ? Zend_Registry::get('Zend_Cache') : false;
    }
    
    if(self::$_cache) {
      return self::$_cache->save($data, $cache_key);
    }
    
    return false;
      
  }
  
  public static function removeCache($cache_key)
  {
      
    if(is_null(self::$_cache)) {
      self::$_cache = (Zend_Registry::get('Zend_Cache') instanceof Zend_Cache_Core) ? Zend_Registry::get('Zend_Cache') : false;
    }
    
    if(self::$_cache) {
      return self::$_cache->remove($cache_key);
    }
    
    return false;
      
  }

  public static function getUserId() {
    return Engine_Api::_()->user()->getViewer()->getIdentity();
  }

  public static function getUser() {
    return Engine_Api::_()->user()->getViewer();
  }

  public static function isUser() {
    try
    {
      $viewer = Engine_Api::_()->user()->getViewer();
    }
    catch( Exception $e )
    {
      $viewer = null;
    }

    $ret = false;
    if( $viewer instanceof Core_Model_Item_Abstract && $viewer->getIdentity() )
    {
      $ret = true;
    }
    
    return $ret;
  }
  
  
  
  public static function getSetting($key, $default = null) {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    return $settings->getSetting($key, $default);
  }

  public static function setSetting($key, $value) {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    return $settings->setSetting($key, $value);
  }
  
  function g($var, $key, $default = null){ 
      return isset($var[$key]) ? $var[$key] : $default;
  }

}