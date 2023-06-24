<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesbasic
 * @package    Sesbasic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php 2015-07-25 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesbasic_Widget_SimpleHtmlBlockController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $showWidget = $this->_getParam('show_content', 0);
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity() && empty($showWidget))
      return $this->setNoRender();

    $local_language = $this->view->locale()->getLocale()->__toString();
    $local_language = explode('_', $local_language);
    $language = $local_language[0];
    if ($language == 'en') {
      $column = 'bodysimple';
    } else {
      $column = $language . '_bodysimple';
    }

    $this->view->content = $this->_getParam($column, null);
  }

}
