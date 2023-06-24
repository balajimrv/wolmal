<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Widget_TagcloudSitestoreproductController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    if (Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {

      //GET SUBJECT
      $sitestoreproduct = Engine_Api::_()->core()->getSubject();

      //GET OWNER INFORMATION
      $this->view->owner_id = $owner_id = $sitestoreproduct->owner_id;
      $this->view->owner = $sitestoreproduct->getOwner();
    } else {
      $this->view->owner_id = $owner_id = 0;
    }

    //HOW MANY TAGS WE HAVE TO SHOW
    $category_id = Zend_Registry::isRegistered('sitestoreproductCategoryId') ?  Zend_Registry::get('sitestoreproductCategoryId') : null;
    $total_tags = $this->_getParam('itemCount', 25);
    
    if(empty($category_id)) {
      $category_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('category_id', null);
    }

    //CONSTRUCTING TAG CLOUD
    $tag_array = array();
    $sitestoreproduct_api = Engine_Api::_()->sitestoreproduct();
    $this->view->count_only = $sitestoreproduct_api->getTags($owner_id, 0, 1, $category_id);
    if ($this->view->count_only <= 0) {
      return $this->setNoRender();
    }

    //FETCH TAGS
    $tag_cloud_array = $sitestoreproduct_api->getTags($owner_id, $total_tags, 0, $category_id);

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

    if (empty($this->view->tag_array)) {
      return $this->setNoRender();
    }

    $element = $this->getElement();
    if ($this->view->owner_id == 0) {
      $element->setTitle(sprintf($this->view->translate($element->getTitle(), (int) $this->view->count_only)));
    } else {
      $element->setTitle(sprintf($this->view->translate($element->getTitle(), $this->view->owner->getTitle())));
    }
  }

}