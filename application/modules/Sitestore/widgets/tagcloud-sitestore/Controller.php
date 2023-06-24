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
class Sitestore_Widget_TagcloudSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');
    
    //CONSTRUCTING TAG CLOUD
    $tag_array = array();
    $this->view->category_id = $category_id = $this->_getParam('category_id',0);
		$storeTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
		$this->view->count_only = $storeTable->getTagCloud(20,$category_id, 1);
		if($this->view->count_only <= 0) {
			return $this->setNoRender();
		}

    $tag_cloud_array = $storeTable->getTagCloud(20, $category_id, 0);

    foreach ($tag_cloud_array as $vales) {
      $tag_array[$vales['text']] = $vales['Frequency'];
      $tag_id_array[$vales['text']] = $vales['tag_id'];
    }

    if (!empty($tag_array)) {
      $max_font_size = 18;
      $min_font_size = 12;
      $max_frequency = max(array_values($tag_array));
      $min_frequency = min(array_values($tag_array));
      $spread = $max_frequency - $min_frequency;
      if ($spread == 0) {
        $spread = 1;
      }
      $step = ($max_font_size - $min_font_size) / ($spread);

      $tag_data = array('min_font_size' => $min_font_size, 'max_font_size' => $max_font_size, 'max_frequency' => $max_frequency, 'min_frequency' => $min_frequency, 'step' => $step);

      $this->view->tag_data = $tag_data;
      $this->view->tag_id_array = $tag_id_array;
    }
    $this->view->tag_array = $tag_array;

		if(empty($this->view->tag_array)) {
			return $this->setNoRender();
		}
  }

}
?>