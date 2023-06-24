<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Searchitems.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Wishlist_Searchitems extends Engine_Form {

  public function init() {

    $this->setAttribs(array(
                'id' => 'wishlist_items_filter_form',
                'class' => 'global_form_box',
            ))
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    parent::init();

    $this->addElement('Text', 'search', array(
        'label' => 'Search:',
        'filters' => array(
             'StripTags',
            new Engine_Filter_Censor(),
        )
    ));
    
    $categories = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getCategories(null, 0, 0, 1);
    if (count($categories) != 0) {
      $categories_prepared[0] = "";
      foreach ($categories as $category) {
        $categories_prepared[$category->category_id] = $category->category_name;
      }

      $this->addElement('Select', 'category_id', array(
          'label' => 'Category',
          'multiOptions' => $categories_prepared,
          'onchange' => "addOptions(this.value, 'cat_dependency', 'subcategory_id', 0);"
      ));

      $this->addElement('Select', 'subcategory_id', array(
          'RegisterInArrayValidator' => false,
          'decorators' => array(array('ViewScript', array(
                      'viewScript' => 'application/modules/Sitestoreproduct/views/scripts/wishlist/_browse_search_category.tpl',
                      'class' => 'form element')))
      ));
    }    
    if(Engine_Api::_()->sitestore()->isCommentsAllow("sitestoreproduct_product")){
    $this->addElement('Select', 'orderby', array(
        'label' => 'Browse By:',
        'multiOptions' => array(
            'rating_avg' => 'Most Rated (Overall)',
            'rating_editor' => 'Most Rated (Editor)',
            'rating_users' => 'Most Rated (Users)',
            'review_count' => 'Most Reviewed',
            'date' => 'Recently Added',
            'view_count' => 'Most Viewed',
            'like_count' => 'Most Liked',
            'comment_count' => 'Most Commented',
        ),
        'value' => 'date'
    ));
    }else{
        $this->addElement('Select', 'orderby', array(
        'label' => 'Browse By:',
        'multiOptions' => array(
            'rating_avg' => 'Most Rated (Overall)',
            'rating_editor' => 'Most Rated (Editor)',
            'rating_users' => 'Most Rated (Users)',
            'review_count' => 'Most Reviewed',
            'date' => 'Recently Added',
            'view_count' => 'Most Viewed',
            'like_count' => 'Most Liked',
//            'comment_count' => 'Most Commented',
        ),
        'value' => 'date'
    ));
    }
    $this->addElement('hidden', 'viewType', array(
        'value' => 'pin'
    ));

    $this->addElement('Button', 'done', array(
        'label' => 'Search',
        'type' => 'Submit',
        'ignore' => true,
    ));
  }

}