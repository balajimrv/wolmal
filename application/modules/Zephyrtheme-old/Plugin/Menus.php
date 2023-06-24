<?php
class Zephyrtheme_Plugin_Menus
{
    // core_main
    public function onMenuInitialize_CoreMainHome($row)
    {
        $viewer  = Engine_Api::_()->user()->getViewer();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $route   = array(
            'route' => 'default',
            'icon' => $row->params['icon']
        );

        if( $viewer->getIdentity() ) {
            $route['route']  = 'user_general';
            $route['params'] = array(
                'action' => 'home',
            );
            if( 'user'  == $request->getModuleName() &&
                'index' == $request->getControllerName() &&
                'home'  == $request->getActionName() ) {
                $route['active'] = true;
            }
        }

        return $route;
    }
}