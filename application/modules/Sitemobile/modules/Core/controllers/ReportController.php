<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: ReportController.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Core_ReportController extends Core_Controller_Action_Standard
{
  public function init()
  {
    $this->_helper->requireUser();
    $this->_helper->requireSubject();
  }

  public function createAction()
  {

    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    $this->view->clear_cache = true;
    $this->view->form = $form = new Core_Form_Report();
    $form->populate($this->_getAllParams());
  
    if (Engine_Api::_()->sitemobile()->isApp()) {
      Zend_Registry::set('setFixedCreationForm', true);
       if($subject->getMediaType() == "image"){
      Zend_Registry::set('setFixedCreationHeaderTitle', str_replace(' New ', ' ', 'Report Photo'));
      }else{
      Zend_Registry::set('setFixedCreationHeaderTitle', str_replace(' New ', ' ', 'Report'));
      }
      Zend_Registry::set('setFixedCreationHeaderSubmit', 'Report');
      $this->view->form->setTitle('');
      $this->view->form->removeElement('cancel');
      $this->view->form->setDescription('');
      $this->view->form->getElement('category')->setLabel('Reason');
      $this->view->form->setAttrib('id', 'form_report');
      Zend_Registry::set('setFixedCreationFormId', '#form_report');
      $this->view->form->removeElement('submit');
      $this->view->form->removeElement('execute');
      $form->setTitle('');
    }
    
    
    if( !$this->getRequest()->isPost() )
    {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $table = Engine_Api::_()->getItemTable('core_report');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = Engine_Api::_()->user()->getViewer();
      
      $report = $table->createRow();
      $report->setFromArray(array_merge($form->getValues(), array(
        'subject_type' => $subject->getType(),
        'subject_id' => $subject->getIdentity(),
        'user_id' => $viewer->getIdentity(),
      )));
      $report->save();

      // Increment report count
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.reports');

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    
    // Close smoothbox
    if(Engine_Api::_()->sitemobile()->isApp()) {
                return $this->_forward('success', 'utility', 'core', array(
                            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your report has been submitted.')),
                            'layout' => 'default-simple',
                            'parentRedirect' => 'backToPage',
                ));
    }
    $currentContext = $this->_helper->contextSwitch->getCurrentContext();
    if( null === $currentContext )
    {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    else if( 'smoothbox' === $currentContext )
    {
      return $this->_forward('success', 'utility', 'core', array(
        'messages' => $this->view->translate('Your report has been submitted.'),
        'smoothboxClose' => true,
        'parentRefresh' => false,
      ));
    }
  }
}