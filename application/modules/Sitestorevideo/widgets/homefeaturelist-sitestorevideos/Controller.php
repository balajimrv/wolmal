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
class Sitestorevideo_Widget_HomefeaturelistSitestorevideosController extends Engine_Content_Widget_Abstract {

  //ACTION FOR SHOWING THE MOST RECENT VIDEOS ON STORE HOME / BROWSE
  public function indexAction() {

    //SEARCH PARAMETER
    $params = array();
    $params['zero_count'] = 'featured';
    $params['category_id'] = $this->_getParam('category_id',0);
    $params['limit'] = $this->_getParam('itemCount', 3);

    //MAKE PAGINATOR
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('videos', 'sitestorevideo')->widgetVideosData($params);

    //NO RENDER
    if ( (Count($paginator) <= 0 ) ) {
      return $this->setNoRender();
    }
  }

}

?>