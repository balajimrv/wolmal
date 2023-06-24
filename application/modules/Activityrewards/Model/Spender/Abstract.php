<?php

class Activityrewards_Model_Spender_Abstract 
{

  public function onTransactionStart(&$params) {
    return true;
  }

  public function onTransactionSuccess(&$params) {
    return true;
  }

  public function onTransactionFail(&$params) {
    return true;
  }  

  public function onTransactionFinished(&$params) {
    return true;
  }

}