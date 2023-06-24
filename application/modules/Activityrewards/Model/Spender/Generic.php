<?php

class Activityrewards_Model_Spender_Generic extends Activityrewards_Model_Spender_Abstract
{

  public function onTransactionStart(&$params) {

    $upspender = $params[0];
    $user = $params[1];
    $metadata = $params[2];
    $transaction_params = $params[3];
  
  
    if($metadata['t'] == 0) {
      $params['transaction_record'] = 0;
    }
    
    return true;
  
  }
  
  public function onTransactionSuccess(&$params) {
    
    $upspender = $params[0];
    
    $translator = Zend_Registry::get('Zend_Translate');
    
    //$params['transaction_text'] = $translator->translate( '100016066' );
    //$params['transaction_text'] .=  " ({$upspender->userpointspender_title})";
    //$params['transaction_text'] = $params['transaction_text'];

    $params['transaction_text'] =  $upspender->userpointspender_title;

    return true;
  
  }

  public function onTransactionFinished(&$params) {
    
    $upspender = $params[0];
    $user = $params[1];
    $metadata = $params[2];
    $transaction_params = $params[3];
    
    if( !empty($metadata['url']) ) {
      $redirect_url = trim($metadata['url']);
    
      $params['redirect'] = html_entity_decode($redirect_url, ENT_QUOTES, 'UTF-8');
    
    }
  
    return true;
  }

}