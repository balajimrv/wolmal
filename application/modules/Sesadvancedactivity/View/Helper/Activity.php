<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedactivity
 * @package    Sesadvancedactivity
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Activity.php  2017-01-12 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesadvancedactivity_View_Helper_Activity extends Zend_View_Helper_Abstract
{
  public function activity(Sesadvancedactivity_Model_Action $action = null, array $data = array(), $method = null, $show_all_comments = false)
  {
    if( null === $action )
    {
      return '';
    }
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')
        ->getAllowed('user', $viewer, 'activity');
    $form = new Sesadvancedactivity_Form_Comment();
    $data = array_merge($data, array(
      'actions' => array($action),
      'commentForm' => $form,
      'user_limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userlength'),
      'allow_delete' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userdelete'),
      'activity_moderate' =>$activity_moderate,
      'viewAllComments' => $show_all_comments,
      'ulInclude'=> empty($data['ulInclude']) ? true : false,
      'onlyComment'=> empty($data['onlyComment']) ? true : false,
      'userphotoalign' => !empty($data['userphotoalign']) ? $data['userphotoalign'] : 'left',
      
    ));

    if($method == 'update'){
     if(!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesadvancedcomment')){
      return $this->view->partial(
        '_activityComments.tpl',
        'sesadvancedactivity',
        $data
      );
     }else{
       $type = !empty($data['type']) ? $data['type'] : '';
       // If has a page, display oldest to newest
        if( null !== ( $page = $data['page']) ) {
          $comments = $action->getComments('0',$page,$type);
          $data['comments'] = $comments;
          $data['page'] = $page;
        } else {
          // If not has a page, show the
          $comments = $action->getComments(0,'zero',$type);
          $data['comments'] = $comments;
          $data['page'] = 0;
        }
       
        return $this->view->partial(
        '_activityComments.tpl',
        'sesadvancedcomment',
        $data
      );
       
     }
    }
    else{
      return $this->view->partial(
        '_activityText.tpl',
        'sesadvancedactivity',
        $data
        );
      }
    }
}