<?php

class Activityrewards_Model_Earner_Affiliate extends Activityrewards_Model_Earner_Abstract
{

  public function onTransactionStart(&$params) {

    $upspender = $params[0];
    $user = $params[1];
    $metadata = $params[2];
    $transaction_params = $params[3];
  
  
    // if requirements filled (called when actual vote is being casted)
    if( Semods_Utils::g($transaction_params, 'gotvars', 0) == 1 ) {
      // THIS SHOULD BE CALLED FROM A CALLBACK WITH SOME VARIABLE
  
      // ABORT TRANSACTION
      return false;
    }
  
    return true;

  }
  
  public function onTransactionSuccess(&$params) {

    $upearner = $params[0];
  
    //$params['transaction_text'] = Zend_Registry::get('Zend_Translate')->_( 100016030 );
    //$params['transaction_text'] .=  " ({$upearner->userpointearner_title})";

    $params['transaction_text'] = $upearner->userpointearner_title;
  
    return true;
  }


  public function onTransactionFinished(&$params) {
  
    $upearner = $params[0];
    $user = $params[1];
    $metadata = $params[2];
    $transaction_params = $params[3];
  
    $params['redirect'] = str_replace( array( '[userid]',
                                              '[username]',
                                              '[transactionid]'
                                              ),
                                       array( $user->getIdentity(),
                                              $user->username,
                                              $transaction_params['transaction_id']
                                             ),
                                             html_entity_decode($metadata['url'], ENT_QUOTES, 'UTF-8')
  
                                      );
  
    return true;
  }

}