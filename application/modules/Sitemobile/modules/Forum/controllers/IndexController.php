<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: IndexController.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Forum_IndexController extends Seaocore_Controller_Action_Standard
{
  public function indexAction()
  {
    if ( !$this->_helper->requireAuth()->setAuthParams('forum', null, 'view')->isValid() ) {
      return;
    }

    $categoryTable = Engine_Api::_()->getItemTable('forum_category');
    $this->view->categories = $categoryTable->fetchAll($categoryTable->select()->order('order ASC'));
    
    $forumTable = Engine_Api::_()->getItemTable('forum_forum');
    $forumSelect = $forumTable->select()
      ->order('order ASC')
      ;
    $forums = array();
    foreach( $forumTable->fetchAll() as $forum ) {
      if( Engine_Api::_()->authorization()->isAllowed($forum, null, 'view') ) {
        $order = $forum->order;
        while( isset($forums[$forum->category_id][$order]) ) {
          $order++;
        }
        $forums[$forum->category_id][$order] = $forum;
        ksort($forums[$forum->category_id]);
      }
    }
    $this->view->forums = $forums;
    
    // Render
    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;
  }
}