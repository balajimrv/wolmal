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
class Sitestorevideo_Widget_TopcreatorsSitestorevideoController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //SEARCH PARAMETER
    $limit = $this->_getParam('itemCount', 5);
    $category_id = $this->_getParam('category_id',0);
    //MAKE PAGINATOR
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('videos', 'sitestorevideo')->topcreatorData($limit, $category_id);

    //NO RENDER
    if ( (Count($paginator) <= 0 ) ) {
      return $this->setNoRender();
    }
  }
}
?>