<?php

class Semods_Loader extends Engine_Loader
{
  
  static $_hooked = false;
  
  var $_trampolines = array('Activity_Model_DbTable_Actions' => 'Semods_Model_DbTable_Actions');
  var $_loader;
  
  
  public static function hook() {
    if(self::$_hooked) {
      return;
    }
    self::$_hooked = true;
    
    new self();
    
  }
  
  public function __construct()
  {
    $this->_loader = Engine_Loader::getInstance();
    Engine_Loader::setInstance($this);
    
    $this->_prefixToPaths = $this->_loader->_prefixToPaths;
    $this->_components = $this->_loader->_components;
    
  }

  public function load($class)
  {
    if($class == 'Activity_Model_DbTable_Actions') {
      $class = "Semods_Model_DbTable_Actions";
    }

    //if($class == 'Authorization_Model_DbTable_Allow') {
    //  $class = "Sempollgroup_Model_DbTable_Allow";
    //}
    
    return parent::load($class);
    
    //return $this->loader->load($class);
  }

  public function addTrampoline($origin, $trampoline) {
    $this->_trampolines[$origin] = $trampoline;
  }
  
  //public function dbg() {
  //
  //  var_dump($this->_loader->_prefixToPaths);
  //  var_dump($this->_prefixToPaths);
  //  
  //}

}

