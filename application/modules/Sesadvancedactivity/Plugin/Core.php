<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedactivity
 * @package    Sesadvancedactivity
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Core.php  2017-01-12 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */


class Sesadvancedactivity_Plugin_Core
{
  public function onRenderLayoutMobileDefault($event) {
    return $this->onRenderLayoutDefault($event,'simple');
  }
	public function onRenderLayoutMobileDefaultSimple($event) {
    return $this->onRenderLayoutDefault($event,'simple');
  }
	public function onRenderLayoutDefaultSimple($event) {
    return $this->onRenderLayoutDefault($event,'simple');
  }
	public function onRenderLayoutDefault($event,$mode=null){
		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
		$viewer = Engine_Api::_()->user()->getViewer();		
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$moduleName = $request->getModuleName();
		$actionName = $request->getActionName();
		$controllerName = $request->getControllerName();
		
		
		$script = '';
		
		$script .=
      "var sesAdvancedActivity = 1;";
    $script .=
    "var sesadvancedactivitybigtext = ".Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.bigtext',1).";
    var sesAdvancedactivityfonttextsize = ".Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.fonttextsize',24).";
    var sesAdvancedactivitytextlimit = ".Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.textlimit',120).";
    ";
    $view->headScript()->appendScript($script);
   if(!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesadvancedcomment')){
      $emojiContent = $view->partial('emojicontent.tpl','sesadvancedactivity',array());
      $search = array(
          '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
          '/[^\S ]+\</s',  // strip whitespaces before tags, except space
          '/(\s)+/s'       // shorten multiple whitespace sequences
      );
      $replace = array(
          '>',
          '<',
          '\\1'
      );
      $emojiContent = preg_replace($search, $replace, $emojiContent);

      $script = "sesJqueryObject(document).ready(function() {
        sesJqueryObject('".$emojiContent.'<a href="javascript:;" class="exit_emoji_btn notclose" style="display:none;">'."').appendTo('body');
      });";
      

      $view->headScript()->appendScript($script);
   }
	}
  public function onItemDeleteBefore($event)
  {
    $item = $event->getPayload();

    Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->delete(array(
      'subject_type = ?' => $item->getType(),
      'subject_id = ?' => $item->getIdentity(),
    ));
    
    Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->delete(array(
      'object_type = ?' => $item->getType(),
      'object_id = ?' => $item->getIdentity(),
    ));

    if( $item instanceof User_Model_User ) {
      Engine_Api::_()->getDbtable('notifications', 'sesadvancedactivity')->delete(array(
        'user_id = ?' => $item->getIdentity(),
      ));
    }
    
    Engine_Api::_()->getDbtable('notifications', 'sesadvancedactivity')->delete(array(
      'subject_type = ?' => $item->getType(),
      'subject_id = ?' => $item->getIdentity(),
    ));

    Engine_Api::_()->getDbtable('notifications', 'sesadvancedactivity')->delete(array(
      'object_type = ?' => $item->getType(),
      'object_id = ?' => $item->getIdentity(),
    ));

    Engine_Api::_()->getDbtable('stream', 'sesadvancedactivity')->delete(array(
      'subject_type = ?' => $item->getType(),
      'subject_id = ?' => $item->getIdentity(),
    ));

    Engine_Api::_()->getDbtable('stream', 'sesadvancedactivity')->delete(array(
      'object_type = ?' => $item->getType(),
      'object_id = ?' => $item->getIdentity(),
    ));

    // Delete all attachments and parent posts
    $attachmentTable = Engine_Api::_()->getDbtable('attachments', 'sesadvancedactivity');
    $attachmentSelect = $attachmentTable->select()
      ->where('type = ?', $item->getType())
      ->where('id = ?', $item->getIdentity())
      ;

    $attachmentActionIds = array();
    foreach( $attachmentTable->fetchAll($attachmentSelect) as $attachmentRow )
    {
      $attachmentActionIds[] = $attachmentRow->action_id;
    }

    if( !empty($attachmentActionIds) ) {
      $attachmentTable->delete('action_id IN('.join(',', $attachmentActionIds).')');
      Engine_Api::_()->getDbtable('stream', 'sesadvancedactivity')->delete('action_id IN('.join(',', $attachmentActionIds).')');
    }
    
  }
   public function onUserLogoutAfter(){
    //unset linkedin session content
     unset($_SESSION['linkedin_lock']);
     unset($_SESSION['linkedin_uid']);
     unset($_SESSION['linkedin_secret']);
     unset($_SESSION['linkedin_token']);
     unset($_SESSION['linkedin_token']);
     unset($_SESSION['linkedin_access']);
  }
  public function getActivity($event)
  {
    // Detect viewer and subject
    $payload = $event->getPayload();
    $user = null;
    $subject = null;
    if( $payload instanceof User_Model_User ) {
      $user = $payload;
    } else if( is_array($payload) ) {
      if( isset($payload['for']) && $payload['for'] instanceof User_Model_User ) {
        $user = $payload['for'];
      }
      if( isset($payload['about']) && $payload['about'] instanceof Core_Model_Item_Abstract ) {
        $subject = $payload['about'];
      }
    }
    if( null === $user ) {
      $viewer = Engine_Api::_()->user()->getViewer();
      if( $viewer->getIdentity() ) {
        $user = $viewer;
      }
    }
    if( null === $subject && Engine_Api::_()->core()->hasSubject() ) {
      $subject = Engine_Api::_()->core()->getSubject();
    }
   
    // Members Lists
    if( $user ) {
      $data = array();
      $data = Engine_Api::_()->getDbTable('actions','sesadvancedactivity')->getListsIds();

      if( !empty($data) ) {
        $event->addResponse(array(
          'type' => 'members_list',
          'data' => $data,
        ));
      }
    }

  }

  public function addActivity($event)
  {}
}