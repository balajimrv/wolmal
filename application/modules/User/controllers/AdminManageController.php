<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: AdminManageController.php 9919 2013-02-16 00:46:04Z matthew $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class User_AdminManageController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->formFilter = $formFilter = new User_Form_Admin_Manage_Filter();
    $page = $this->_getParam('page', 1);

    $table = Engine_Api::_()->getDbtable('users', 'user');
    $select = $table->select();

    // Process form
    $values = array();
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    }

    foreach( $values as $key => $value ) {
      if( null === $value ) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
      'order' => 'user_id',
      'order_direction' => 'DESC',
    ), $values);

    $this->view->assign($values);

    // Set up select info
    $select->order(( !empty($values['order']) ? $values['order'] : 'user_id' ) . ' ' . ( !empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    if( !empty($values['displayname']) ) {
      $select->where('displayname LIKE ?', '%' . $values['displayname'] . '%');
    }
    if( !empty($values['username']) ) {
      $select->where('username LIKE ?', '%' . $values['username'] . '%');
    }
    if( !empty($values['email']) ) {
      $select->where('email LIKE ?', '%' . $values['email'] . '%');
    }
    if( !empty($values['level_id']) ) {
      $select->where('level_id = ?', $values['level_id'] );
    }
    if( isset($values['enabled']) && $values['enabled'] != -1 ) {
      $select->where('enabled = ?', $values['enabled'] );
    }
    if( !empty($values['user_id']) ) {
      $select->where('user_id = ?', (int) $values['user_id']);
    }

    // Filter out junk
    $valuesCopy = array_filter($values);
    // Reset enabled bit
    if( isset($values['enabled']) && $values['enabled'] == 0 ) {
      $valuesCopy['enabled'] = 0;
    }

    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator->setCurrentPageNumber( $page );
    $this->view->formValues = $valuesCopy;

    $this->view->superAdminCount = count(Engine_Api::_()->user()->getSuperAdmins());
    $this->view->hideEmails = _ENGINE_ADMIN_NEUTER;
    //$this->view->formDelete = new User_Form_Admin_Manage_Delete();

    $this->view->openUser = (bool) ( $this->_getParam('open') && $paginator->getTotalItemCount() == 1 );
  }

  public function multiModifyAction()
  {
    if( $this->getRequest()->isPost() ) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key=>$value) {
        if( $key == 'modify_' . $value ) {
          $user = Engine_Api::_()->getItem('user', (int) $value);
          if( $values['submit_button'] == 'delete' ) {
            if( $user->level_id != 1 ) {
              $user->delete();
            }
          } else if( $values['submit_button'] == 'approve' ) {
            $old_status = $user->enabled;
            $user->enabled = 1;
            $user->approved = 1;
            $user->save();

            // ORIGINAL WAY
            if( $old_status == 0 ) {
              // trigger `onUserEnable` hook
              $payload = array(
                'user' => $user,
                'shouldSendWelcomeEmail' => Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.enablewelcomeemail', 0),
                'shouldSendApprovedEmail' => true
              );
              Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserEnable', $payload);
            }
          }
        }
      }
    }

    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

  public function editAction()
  {
    $id = $this->_getParam('id', null);
    $user = Engine_Api::_()->getItem('user', $id);
    $userLevel = Engine_Api::_()->getItem('authorization_level', $user->level_id);
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewerLevel = Engine_Api::_()->getItem('authorization_level', $viewer->level_id);
    $superAdminLevels = Engine_Api::_()->getItemTable('authorization_level')->fetchAll(array(
      'flag = ?' => 'superadmin',
    ));

    if( !$user || !$userLevel || !$viewer || !$viewerLevel ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    $this->view->user = $user;
    $this->view->form = $form = new User_Form_Admin_Manage_Edit(array(
      'userIdentity' => $id,
    ));

    // Do not allow editing level if the last superadmin
    if( $userLevel->flag == 'superadmin' && count(Engine_Api::_()->user()->getSuperAdmins()) == 1 ) {
      $form->removeElement('level_id');
    }

    // Do not allow admins to change to super admin
    if( $viewerLevel->flag != 'superadmin' && $form->getElement('level_id') ) {
      if( $userLevel->flag == 'superadmin' ) {
        $form->removeElement('level_id');
      } else {
        foreach( $superAdminLevels as $superAdminLevel ) {
          unset($form->getElement('level_id')->options[$superAdminLevel->level_id]);
        }
      }
    }

    // Get values
    $values = $user->toArray();
    unset($values['password']);
    if( _ENGINE_ADMIN_NEUTER ) {
      unset($values['email']);
    }

    // Get networks
    $select = Engine_Api::_()->getDbtable('membership', 'network')->getMembershipsOfSelect($user);
    $networks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll($select);
    $values['network_id'] = $oldNetworks = array();
    foreach( $networks as $network ) {
      $values['network_id'][] = $oldNetworks[] = $network->getIdentity();
    }

    // Check if user can be enabled?
    $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
    if( !$subscriptionsTable->check($user) && !$values['enabled'] ) {
      $form->enabled->setAttrib('disable', array('enabled'));
      $note = '<p>Note: You cannot enable a member using this form if he / she has not '
        . 'yet chosen a subscription plan for their account. You can just approve them '
        . 'here after which they\'ll be able to choose a subscription plan before trying '
        . 'to login on your site.</p>';
    } elseif( 2 === (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.verifyemail', 0) ) {
      $note = '<p>Note - Member can only be enabled when they are both approved and verified.</p>';
    } else {
      $note = '<p>Note - Member can only be enabled after they have been approved.</p>';
    }

    $form->addElement('note', 'desc', array(
      'value' => $note,
      'order' => 9
    ));

    // Populate form
    $form->populate($values);

    // Check method/valid
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $values = $form->getValues();

    // Check password validity
    if( empty($values['password']) && empty($values['password_conf']) ) {
      unset($values['password']);
      unset($values['password_conf']);
    } else if( $values['password'] != $values['password_conf'] ) {
      return $form->getElement('password')->addError('Passwords do not match.');
    } else {
      unset($values['password_conf']);
    }

    // Process
    $oldValues = $user->toArray();

    // Set new network
    $userNetworks = $values['network_id'];
    unset($values['network_id']);
    if($userNetworks == NULL) { $userNetworks = array(); }
    $joinIds = array_diff($userNetworks, $oldNetworks);
    foreach( $joinIds as $id ) {
      $network = Engine_Api::_()->getItem('network', $id);
      $network->membership()->addMember($user)
          ->setUserApproved($user)
          ->setResourceApproved($user);
    }
    $leaveIds = array_diff($oldNetworks, $userNetworks);
    foreach( $leaveIds as $id ) {
      $network = Engine_Api::_()->getItem('network', $id);
      if( !is_null($network) ){
        $network->membership()->removeMember($user);
      }
    }

    // Check for null usernames
    if( $values['username'] == '' ) {
      // If value is "NULL", then set to zend Null
        $values['username'] = new Zend_Db_Expr("NULL");
    }

    $user->setFromArray($values);
    $user->save();


    if( !$oldValues['enabled'] && $values['enabled'] ) {
      // trigger `onUserEnable` hook
      $payload = array(
        'user' => $user,
        'shouldSendWelcomeEmail' => Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.enablewelcomeemail', 0),
        'shouldSendApprovedEmail' => true
      );
      Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserEnable', $payload);
    } else if( $oldValues['enabled'] && !$values['enabled'] ) {
      // trigger `onUserDisable` hook
      Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserDisable', $user);
    }


    // Forward
    return $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh' => true,
      'format'=> 'smoothbox',
      'messages' => array('Your changes have been saved.')
    ));
  }

  public function deleteAction()
  {
    $id = $this->_getParam('id', null);
    $this->view->user = $user = Engine_Api::_()->getItem('user', $id);
    $this->view->form = $form = new User_Form_Admin_Manage_Delete();
    // deleting user
    //$form->user_id->setValue($id);

    if( $this->getRequest()->isPost() ) {
      $db = Engine_Api::_()->getDbtable('users', 'user')->getAdapter();
      $db->beginTransaction();

      try {
        $user->delete();

        $db->commit();
      } catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }

      return $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'format'=> 'smoothbox',
        'messages' => array('This member has been successfully deleted.')
      ));
    }
  }

  public function loginAction()
  {
    $id = $this->_getParam('id');
    $user = Engine_Api::_()->getItem('user', $id);

    // @todo change this to look up actual superadmin level
    if( $user->level_id == 1 || !$this->getRequest()->isPost() ) {
      if( null === $this->_helper->contextSwitch->getCurrentContext() ) {
        return $this->_helper->redirector->gotoRoute(array('action' => 'index', 'id' => null));
      } else {
        $this->view->status = false;
        $this->view->error = true;
        return;
      }
    }

    // Login
    Zend_Auth::getInstance()->getStorage()->write($user->getIdentity());

    // Redirect
    if( null === $this->_helper->contextSwitch->getCurrentContext() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    } else {
      $this->view->status = true;
      return;
    }
  }

  public function statsAction()
  {
    $id = $this->_getParam('id', null);
    $this->view->user = $user = Engine_Api::_()->getItem('user', $id);

    $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($user);

    if( !empty($fieldsByAlias['profile_type']) ) {
      $optionId = $fieldsByAlias['profile_type']->getValue($user);
      if( $optionId ) {
        $optionObj = Engine_Api::_()->fields()
          ->getFieldsOptions($user)
          ->getRowMatching('option_id', $optionId->value);
        if( $optionObj ) {
          $this->view->memberType = $optionObj->label;
        }
      }
    }

    // Networks
    $select = Engine_Api::_()->getDbtable('membership', 'network')->getMembershipsOfSelect($user)
      ->where('hide = ?', 0);
    $this->view->networks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll($select);

    // Friend count
    $this->view->friendCount = $user->membership()->getMemberCount($user);
  }
  
  public function inviterAction()
  {
    $id = $this->_getParam('id', null);
    $this->view->user = $user = Engine_Api::_()->getItem('user', $id);

	$inviterTable = Engine_Api::_()->getDbTable('invites', 'inviter');
	$inviterTableName = $inviterTable->info('name');
	
	$userTable = Engine_Api::_()->getItemTable('user');
	$userTableName = $userTable->info('name');
	
    
	$selectTable = $userTable->select()
	                ->setIntegrityCheck(false)
	               ->from($userTableName, array('displayname','user_id','email','level_id'))
	               ->where($inviterTableName.".new_user_id =?",$id)
	               ->join($inviterTableName,$inviterTableName.'.user_id='.$userTableName.'.user_id','');
	$this->view->inviter = $userTable->fetchAll($selectTable);
	
	$previousTable = $userTable->select()
	                ->setIntegrityCheck(false)
	               ->from($userTableName, array('displayname','user_id','email','level_id'))
	               ->where($inviterTableName.".previous_user_id =?",$id)
	               ->order('previous_user_update_date DESC')
	               ->join($inviterTableName, $inviterTableName.'.user_id='.$userTableName.'.user_id', array('referred_date'));
	$this->view->previous_inviter = $userTable->fetchAll($previousTable);
	
  }
}
