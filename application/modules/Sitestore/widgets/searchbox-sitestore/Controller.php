<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Widget_SearchboxSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    
    // Prepare form
    $this->view->form = $form = new Sitestore_Form_Searchbox();
    $content_id =  $this->view->identity;
    $widgetname = 'sitestore.searchbox-sitestore';
    $filtercategory_id = Engine_Api::_()->sitestore()->getSitestoreCategoryid($content_id,$widgetname);
    if(!empty($filtercategory_id)) {
      $this->view->category_id = $filtercategory_id;
    }
    else {
      $this->view->category_id = 0;
    }
	}
}

?>