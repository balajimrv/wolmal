<?php

class Semods_Plugin_Task_Statistics extends Core_Plugin_Task_Abstract
{
  public function execute()
  {

    if( Engine_Api::_()->getApi('settings', 'core')->semods_statistics_disable ) {
      $this->_setWasIdle();
      return;
    }

    Engine_Api::_()->semods()->statistics(10);

    Semods_Utils::setSetting('semods.statistics.lastcheck', time());
    
  }

}