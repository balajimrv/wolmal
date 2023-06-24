<?php

class Timeline_Widget_CoverController extends Engine_Content_Widget_Abstract
{
    public function  indexAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = null;
        if( Engine_Api::_()->core()->hasSubject('user') ) {
            // Get subject
            $subject = Engine_Api::_()->core()->getSubject('user');
            $this->view->auth = $subject->authorization()->isAllowed($viewer, 'view'); 
        }
        if( !empty($subject) ) {
            $this->view->owner_user =  $owner_user = $subject;
            $this->view->is_owner = $is_owner = ($subject->getIdentity() == $viewer->getIdentity());
            $menu_params = array();
            $friend_menu = User_Plugin_Menus::onMenuInitialize_UserProfileFriend(null);
            if ( is_array($friend_menu) && !empty($friend_menu) && $friend_menu['params']['action'] == 'add' ) {
                $menu_params[] = $friend_menu;
            }
            $message_menu = Messages_Plugin_Menus::onMenuInitialize_UserProfileMessage(null);
            if ( is_array($message_menu) && !empty($message_menu) ) {
                $menu_params[] = $message_menu;
            }
            $this->view->menu_params = $menu_params;

            $addition_menu_params = array();
            $block_menu = User_Plugin_Menus::onMenuInitialize_UserProfileBlock(null);
            if ( is_array($block_menu) && !empty($block_menu) ) {
                $addition_menu_params[] = $block_menu;
            }
            $Report_menu = User_Plugin_Menus::onMenuInitialize_UserProfileReport(null);
            if ( is_array($Report_menu) && !empty($Report_menu) ) {
                $addition_menu_params[] = $Report_menu;
            }
            if (method_exists('User_Plugin_Menus', 'onMenuInitialize_UserProfileAdmin')) {
                $admin_menu = User_Plugin_Menus::onMenuInitialize_UserProfileAdmin(null);
                if ( is_array($admin_menu) && !empty($admin_menu) ) {
                    $addition_menu_params[] = $admin_menu;
                }
            }
            if (method_exists('User_Plugin_Menus', 'onMenuInitialize_UserProfileEdit')) {
                $admin_menu = User_Plugin_Menus::onMenuInitialize_UserProfileEdit(null);
                if ( is_array($admin_menu) && !empty($admin_menu) ) {
                    $addition_menu_params[] = $admin_menu;
                }
            }
            if ( is_array($friend_menu) && !empty($friend_menu)) {
                if (isset($friend_menu['params']['action']) && $friend_menu['params']['action'] != 'add') {
                    $addition_menu_params[] = $friend_menu;
                }
                else {
                    foreach ($friend_menu as $friend_menu_one) {
                        if (isset($friend_menu_one['params']['action']) && $friend_menu_one['params']['action'] != 'add') {
                            $addition_menu_params[] = $friend_menu_one;
                        }
                    }
                }
            }
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('poke')) {
                $poke_menu = Poke_Plugin_Menus::onMenuInitialize_UserProfilePoke(null);
                if ( is_array($poke_menu) && !empty($poke_menu) ) {
                    $addition_menu_params[] = $poke_menu;
                }
            }
            $this->view->addition_menu_params = $addition_menu_params;
            $like_profile = $this->view->like_profile = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('like');
            if ($like_profile) {
                $this->view->is_enabled = (bool)( $viewer->getIdentity());
                $this->view->is_allowed = (bool)(Engine_Api::_()->like()->isAllowed($subject));
            }
        } else {
            $this->view->owner_user =  $owner_user = $viewer;
            $this->view->is_owner = $is_owner = true;
        }
        if (!$owner_user->getIdentity())
            return $this->setNoRender ();
        $this->view->web_cam_on = false;
        $this->view->avatar_collection_on = false;
        if ($is_owner) {
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('avatar')) {
                $tmp_auth = Zend_Controller_Action_HelperBroker::getStaticHelper('RequireAuth');
                $this->view->web_cam_on = $tmp_auth->setAuthParams('avatar', null, 'web_cam')->checkRequire();
                $this->view->avatar_collection_on = $tmp_auth->setAuthParams('avatar', null, 'avatarcollection')->checkRequire();
            }
        }
        $this->view->cover = Engine_Api::_()->timeline()->getTimeLineCover($owner_user);
        $this->view->avatar_position = $this->_getParam('avatar_position', 'left');
    }
}
