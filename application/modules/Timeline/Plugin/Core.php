<?php

class Timeline_Plugin_Core extends Zend_Controller_Plugin_Abstract {

    public function routeShutdown() {
        $request = Zend_Controller_Front::getInstance()->getRequest();

        if ($request->getParam('timeline', 0) || ($request->getModuleName() == 'user' && $request->getControllerName() == 'profile' && $request->getActionName() == 'index')) {
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
            if (null === $viewRenderer->view)
                $viewRenderer->initView();

            $path = Engine_Api::_()->getModuleBootstrap('timeline')->getModulePath();
            $view = $viewRenderer->view;
            $view->addHelperPath($path . '/View/Helper', "Timeline_View_Helper_");
            $viewRenderer->setView($view);
        }

        if ($request->getModuleName() == 'user' && $request->getControllerName() == 'profile' && $request->getActionName() == 'index') {

            if (!Engine_Api::_()->core()->hasSubject()) {
                $id = $request->getParam('id');
                if (null !== $id) {
                    $subject = Engine_Api::_()->user()->getUser($id);
                    if ($subject->getIdentity()) {
                        Engine_Api::_()->core()->setSubject($subject);
                    }
                    else
                        return;
                }
                else
                    return;
            }
            else {
                $subject = Engine_Api::_()->core()->getSubject('user');
            }
            if (!Zend_Controller_Action_HelperBroker::getStaticHelper('RequireAuth')->setAuthParams('timeline', $subject, 'timeline_profile')->checkRequire()) {
                return;
            }
            $user_settings = Engine_Api::_()->getDbtable('settings', 'user')->getSetting($subject, 'timeline.profile_layout');
            if (!empty($user_settings) and $user_settings != 'timeline') {
                return;
            }
            $request->setModuleName('timeline');
        }
    }

}