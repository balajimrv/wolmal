<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: IndexController.php 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Core_IndexController extends Seaocore_Controller_Action_Standard {

  public function landingAction() {
    if (Engine_Api::_()->user()->getViewer()->getIdentity()) {
      $formatType = $this->_getParam('formatType', null);
      if ($formatType === 'smjson') {
        $this->view->notSuccessMessage = true;
        return $this->_forward('success', 'utility', 'core', array(
                    'redirect' => $this->_helper->url->url(array('action' => 'home'), 'user_general', true),
                    'messages' => array(),
                ));
      } else {
        return $this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
      }
    }
    $this->addResetContentTriggerEvent();
    // check public settings. Its always enabled for mobile browser and app case.
//    if (!Engine_Api::_()->getApi('settings', 'core')->core_general_portal &&
//            !$this->_helper->requireUser()->isValid() && (!Engine_Api::_()->sitemobile()->isApp()) ) {
//      return;
//    }

    // Render
    $this->_helper->content
            ->setContentName("core_index_index")
            ->setNoRender()
            ->setEnabled()
    ;
  }

}