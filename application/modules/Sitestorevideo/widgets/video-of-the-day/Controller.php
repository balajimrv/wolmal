<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorevideo_Widget_VideoOfTheDayController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $this->view->videoOfDay = $videoOfDay = Engine_Api::_()->getDbtable('videos', 'sitestorevideo')->videoOfDay();
    if (empty($videoOfDay)) {
      return $this->setNoRender();
    }
  }

}
?>