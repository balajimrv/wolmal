<?php

class Activityrewards_Plugin_Menus
{
  
  // admin settings
  public function onMenuInitialize_ActivityrewardsEarn() {

    if(Semods_Utils::getSetting('activityrewards.enable_offers',0) == 0) {
      return false;
    }

    return true;
  }

  public function onMenuInitialize_ActivityrewardsSpend() {

    if(Semods_Utils::getSetting('activityrewards.enable_shop',0) == 0) {
      return false;
    }

    return true;
  }

}