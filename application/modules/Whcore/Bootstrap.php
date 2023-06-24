<?php

class Whcore_Bootstrap extends Engine_Application_Bootstrap_Abstract {

    public function __construct($application) {
        parent::__construct($application);
        $this->initViewHelperPath();
    }

    protected function _initPlugins() {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Whcore_Plugin_Core());
    }

    protected function _initRouter() {
        $router = Zend_Controller_Front::getInstance()->getRouter();
        $route = new Zend_Controller_Router_Route(
                'whcore/thumb/*', array(
            'module' => 'whcore',
            'controller' => 'thumb',
            'action' => 'index'
                )
        );
        $router->addRoute('whcore_phpthumb', $route);
    }

}