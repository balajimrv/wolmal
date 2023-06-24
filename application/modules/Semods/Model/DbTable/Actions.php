<?php
class Semods_Model_DbTable_Actions extends Activity_Model_DbTable_Actions 
{

  protected $_name= 'activity_actions';
  
  public function addActivity(Core_Model_Item_Abstract $subject, Core_Model_Item_Abstract $object,
          $type, $body = null, array $params = null)
  {

    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onSemodsAddActivity', array(
      'subject' => $subject,
      'object' => $object,
      'type' => $type,
      'body' => $body,
      'params' => $params,
    ));

    
    $action = parent::addActivity($subject, $object, $type, $body, $params);


    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onSemodsAddActivityAfter', array(
      'subject' => $subject,
      'object' => $object,
      'type' => $type,
      'body' => $body,
      'params' => $params,
      'action' => $action,
    ));
    
    return $action;
    
  }
    
}