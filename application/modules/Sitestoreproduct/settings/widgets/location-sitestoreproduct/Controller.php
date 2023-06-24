<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Widget_LocationSitestoreproductController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF NOT AUTHORIZED
        if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
            return $this->setNoRender();
        }

        //GET SUBJECT
        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.locationfield', 0)) {
            return $this->setNoRender();
        }

        //GET LOCATION
        $value['id'] = $sitestoreproduct->getIdentity();

        $this->view->location = $location = Engine_Api::_()->getDbtable('locations', 'sitestoreproduct')->getLocation($value);

        //DONT RENDER IF LOCAITON IS EMPTY
        if (empty($location)) {
            return $this->setNoRender();
        }

    }

}