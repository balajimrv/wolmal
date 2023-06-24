<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Advancedactivity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Advancedactivity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Advancedactivity_LinkController extends Core_Controller_Action_Standard
{
  public function init()
  {
    $this->_helper->contextSwitch
      ->addActionContext('create', 'json')
      //->addActionContext('preview', 'json')
      ->initContext();
  }

  public function createAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireAuth()->setAuthParams('core_link', null, 'create')->isValid() ) return;
    
    // Make form
    $this->view->form = $form = new Core_Form_Link_Create();
    $translate        = Zend_Registry::get('Zend_Translate');

    // Check method
    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = $translate->_('Invalid method');
      return;
    }

    // Check data
    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error = $translate->_('Invalid data');
    }

    // Process
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $table = Engine_Api::_()->getDbtable('links', 'core');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $link = Engine_Api::_()->getApi('links', 'advancedactivity')->createLink($viewer, $form->getValues());

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $this->view->status   = true;
    $this->view->message  = $translate->_('Link created');
    $this->view->identity = $link->getIdentity();
  }
}
