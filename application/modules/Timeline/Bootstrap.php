<?php

class Timeline_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
    protected function _initPlugins() {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Timeline_Plugin_Core());
    }
}