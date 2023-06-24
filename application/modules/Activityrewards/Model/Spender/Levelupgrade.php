<?php

class Activityrewards_Model_Spender_Levelupgrade extends Activityrewards_Model_Spender_Abstract
{

  public function onTransactionStart(&$params) {

    $upspender = $params[0];
    $user = $params[1];
    $metadata = $params[2];
    $transaction_params = $params[3];
  
  
    // check if transitioning from levelX to levelY
    if( ($metadata['level_from'] != 0) && ($metadata['level_from'] != $user->level_id)) {
      $params['err_msg'] = 100016037;
      return false;
    }
    
    return true;
  
  }
  
  public function onTransactionSuccess(&$params) {

    $upspender = $params[0];
    $user = $params[1];
    $metadata = $params[2];
    
    $translator = Zend_Registry::get('Zend_Translate');
    
    //$params['transaction_text'] = $translator->translate( '100016066' );
    //$params['transaction_text'] .=  " ({$upspender->userpointspender_title})";
    //$params['transaction_text'] = $params['transaction_text'];

    $params['transaction_text'] =  $upspender->userpointspender_title;

    $params['transaction_message'] = $translator->translate( '100016038' );
    
    $user->level_id = $metadata['level_to'];
    $user->save();

    return true;
  
  }

}