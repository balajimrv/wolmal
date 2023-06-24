<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreproduct_Form_Admin_Settings_Widget extends Engine_Form {
  
  protected function _getWidgetDimensions($pageName, $widget_name, $params)
  {
    $contentTable = Engine_Api::_()->getDbtable('content', 'core');
    $contentTableName = $contentTable->info('name');
    $corePageTable = Engine_Api::_()->getDbtable('pages', 'core');
    $corePageTableName = $corePageTable->info('name');
    
    if( isset($params['isCategory']) && !empty($params['isCategory']) )
    {
      $corePages = $corePageTable->select()
                                 ->from($corePageTableName, array('page_id'))
                                 ->where('name  LIKE ? ', '%' . $pageName . '%')
                                 ->query()->fetchAll();
      
      if( !empty($corePages) )
      {
        foreach($corePages as $corePage)
        {
          $selectWidget = $contentTable->select()
                                       ->from($contentTableName, array('content_id', 'params'))
                                       ->where('page_id =?', $corePage['page_id'])
                                       ->where('name =?', $widget_name);
          $widgetArray = $contentTable->fetchAll($selectWidget);

          if( !empty($widgetArray) )
          {
            foreach ($widgetArray as $widget)
            {
              if( isset($widget->params['defaultWidgetNo']) && !empty($widget->params['defaultWidgetNo']) && $widget->params['defaultWidgetNo'] == $params['defaultWidgetNo'])
                  return Zend_Json::encode(array('height' => $widget->params[$params['height']], 'width' => $widget->params[$params['width']]));
              else
                continue;
            }
          }
        }
        
      }
    }
    else
    {
      $corePageId = $corePageTable->select()
                                  ->from($corePageTableName, array('page_id'))
                                  ->where('name =?', "$pageName")
                                  ->limit(1)
                                  ->query()->fetchColumn();
      
      if( !empty($corePageId) )
      {
        $selectWidget = $contentTable->select()
                                     ->from($contentTableName, array('content_id', 'params'))
                                     ->where('page_id =?', $corePageId)
                                     ->where('name =?', $widget_name);
        $widgetArray = $contentTable->fetchAll($selectWidget);

        if( !empty($widgetArray) )
        {
          foreach ($widgetArray as $widget)
          {
            if( isset($widget->params['defaultWidgetNo']) && !empty($widget->params['defaultWidgetNo']) )
            {
              if( isset($params['height']) && !empty($params['height']) )
                return Zend_Json::encode(array('height' => $widget->params[$params['height']], 'width' => $widget->params[$params['width']]));
              else
                return Zend_Json::encode(array('width' => $widget->params[$params['width']]));
            }
            else
              continue;
          }
        }
      }
    }
    
    
  }

  public function init() {

    $this->setTitle('Store Widget Settings - Default Placed')
              ->setDescription("Here, you can manage the settings of the default widgets which were placed during this plugin's installation. Note: If you remove any default placed widget, then the associated setting with that widget will also be removed from here.");
    
    // Stores - Products Home
    $indexHomeSponsoredWidgetDimension = Zend_Json::decode($this->_getWidgetDimensions('sitestoreproduct_index_home', 'sitestoreproduct.sponsored-sitestoreproduct', array('height' => 'blockHeight', 'width' => 'blockWidth')));
    $indexHomePopularWidgetDimension = Zend_Json::decode($this->_getWidgetDimensions('sitestoreproduct_index_home', 'sitestoreproduct.recently-popular-random-sitestoreproduct', array('height' => 'columnHeight', 'width' => 'columnWidth')));

    if( !empty($indexHomeSponsoredWidgetDimension) || !empty($indexHomePopularWidgetDimension) )
    {
      $this->addElement('Dummy', 'dummy_store_product_home_page' , array ('label' => 'Stores - Products Home', 'value' => 'sitestoreproduct_index_home'));

      if( !empty($indexHomeSponsoredWidgetDimension) )
      {
        $this->addElement('Text', 'store_product_home_ajax_based_product_carousel_width', array(
            'label' => 'AJAX based Products Carousel - Width',
            'value' => $indexHomeSponsoredWidgetDimension['width'],
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Int', true),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));

        $this->addElement('Text', 'store_product_home_ajax_based_product_carousel_height', array(
            'label' => 'AJAX based Products Carousel - Height',
            'value' => $indexHomeSponsoredWidgetDimension['height'],
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Int', true),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));
      }
      
      if( !empty($indexHomePopularWidgetDimension) )
      {
        $this->addElement('Text', 'store_product_home_ajax_based_main_product_width', array(
            'label' => 'Ajax based main Products Home widget - Width',
            'allowEmpty' => false,
            'value' => $indexHomePopularWidgetDimension['width'],
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Int', true),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));

        $this->addElement('Text', 'store_product_home_ajax_based_main_product_height', array(
            'label' => 'Ajax based main Products Home widget - Height',
            'value' => $indexHomePopularWidgetDimension['height'],
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Int', true),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));
      }
    }

    // Stores - Browse Products
    $browseProductWidgetDimension = Zend_Json::decode($this->_getWidgetDimensions('sitestoreproduct_index_index', 'sitestoreproduct.browse-products-sitestoreproduct', array('height' => 'columnHeight', 'width' => 'columnWidth')));
    
    if( !empty($browseProductWidgetDimension) )
    {
      $this->addElement( 'Dummy' , 'dummy_store_browse_product_page' , array ('label' => 'Stores - Browse Products'));
      $this->addElement('Text', 'browse_products_width', array(
          'label' => 'Browse Products - Width',
          'value' => $browseProductWidgetDimension['width'],
          'allowEmpty' => false,
          'required' => true,
          'validators' => array(
              array('NotEmpty', true),
              array('Int', true),
          ),
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
          )
      ));

      $this->addElement('Text', 'browse_products_height', array(
          'label' => 'Browse Products - Height',
          'value' => $browseProductWidgetDimension['height'],
          'allowEmpty' => false,
          'required' => true,
          'validators' => array(
              array('NotEmpty', true),
              array('Int', true),
          ),
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
          )
      ));
    }
    
    // Stores - Products Pinboard
    $pinboardWidgetDimension = Zend_Json::decode($this->_getWidgetDimensions('sitestoreproduct_index_pinboard', 'sitestoreproduct.pinboard-products-sitestoreproduct', array('width' => 'itemWidth')));

    if( !empty($pinboardWidgetDimension) )
    {
      $this->addElement( 'Dummy' , 'dummy_products_pinboard' , array ('label' => 'Stores - Products Pinboard'));
      $this->addElement('Text', 'pinboard_width', array(
          'label' => 'Products Home: Pinboard View - Width',
          'value' => $pinboardWidgetDimension['width'],
          'allowEmpty' => false,
          'required' => true,
          'validators' => array(
              array('NotEmpty', true),
              array('Int', true),
          ),
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
          )
      ));
    }
    
    // Stores - Categories Home
    $categoryGridWidgetDimension = Zend_Json::decode($this->_getWidgetDimensions('sitestoreproduct_index_categories', 'sitestoreproduct.categories-grid-view', array('height' => 'columnHeight', 'width' => 'columnWidth')));
    $categoryPopularWidgetDimension = Zend_Json::decode($this->_getWidgetDimensions('sitestoreproduct_index_categories', 'sitestoreproduct.recently-popular-random-sitestoreproduct', array('height' => 'columnHeight', 'width' => 'columnWidth')));

    if( !empty($categoryGridWidgetDimension) || !empty($categoryPopularWidgetDimension) )
    {
      $this->addElement( 'Dummy' , 'dummy_categories_home' , array ('label' => 'Stores - Categories Home'));
      
      if( !empty($categoryGridWidgetDimension) )
      {
        $this->addElement('Text', 'category_home_width', array(
            'label' => 'Categories: Categories in Grid View - Width',
            'value' => $categoryGridWidgetDimension['width'],
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Int', true),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));

        $this->addElement('Text', 'category_home_height', array(
            'label' => 'Categories: Categories in Grid View - Height',
            'value' => $categoryGridWidgetDimension['height'],
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Int', true),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));
      }
      
      if( !empty($categoryPopularWidgetDimension) )
      {
        $this->addElement('Text', 'categories_ajax_based_main_product_width', array(
            'label' => 'Ajax based main Products Home widget - Width',
            'value' => $categoryPopularWidgetDimension['width'],
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Int', true),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));

        $this->addElement('Text', 'categories_ajax_based_main_product_height', array(
            'label' => 'Ajax based main Products Home widget - Height',
            'value' => $categoryPopularWidgetDimension['height'],
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Int', true),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));
      }
    }

    // Stores - Categories
    $indexCategoryGridWidgetDimension = Zend_Json::decode($this->_getWidgetDimensions('sitestoreproduct_index_category-home_category_', 'sitestoreproduct.categories-grid-view', array('height' => 'columnHeight', 'width' => 'columnWidth', 'defaultWidgetNo' => 7, 'isCategory' => true)));
    $indexCategoryPopularWidgetDimension = Zend_Json::decode($this->_getWidgetDimensions('sitestoreproduct_index_category-home_category_', 'sitestoreproduct.recently-popular-random-sitestoreproduct', array('height' => 'columnHeight', 'width' => 'columnWidth', 'defaultWidgetNo' => 8, 'isCategory' => true)));
    $indexCategoryRatedWidgetDimension = Zend_Json::decode($this->_getWidgetDimensions('sitestoreproduct_index_category-home_category_', 'sitestoreproduct.sponsored-sitestoreproduct', array('height' => 'blockHeight', 'width' => 'blockWidth', 'defaultWidgetNo' => 9, 'isCategory' => true)));
    $indexCategoryViewedWidgetDimension = Zend_Json::decode($this->_getWidgetDimensions('sitestoreproduct_index_category-home_category_', 'sitestoreproduct.sponsored-sitestoreproduct', array('height' => 'blockHeight', 'width' => 'blockWidth', 'defaultWidgetNo' => 10, 'isCategory' => true)));
    $indexCategoryLikedWidgetDimension = Zend_Json::decode($this->_getWidgetDimensions('sitestoreproduct_index_category-home_category_', 'sitestoreproduct.sponsored-sitestoreproduct', array('height' => 'blockHeight', 'width' => 'blockWidth', 'defaultWidgetNo' => 11, 'isCategory' => true)));
    
    if( !empty($indexCategoryGridWidgetDimension) || !empty($indexCategoryRatedWidgetDimension) || !empty($indexCategoryPopularWidgetDimension) || !empty($indexCategoryViewedWidgetDimension) || !empty($indexCategoryLikedWidgetDimension) )
    {
      $this->addElement( 'Dummy' , 'dummy_categories_page' , array ('label' => 'Stores - Categories'));
      
      if( !empty($indexCategoryGridWidgetDimension) )
      {
        $this->addElement('Text', 'categories_width', array(
            'label' => 'Categories: Categories in Grid View - Width',
            'value' => $indexCategoryGridWidgetDimension['width'],
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Int', true),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));

        $this->addElement('Text', 'categories_height', array(
            'label' => 'Categories: Categories in Grid View - Height',
            'value' => $indexCategoryGridWidgetDimension['height'],
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Int', true),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));
      }
      
      if( !empty($indexCategoryPopularWidgetDimension) )
      {
        $this->addElement('Text', 'categories_ajax_based_main_products_home_width', array(
            'label' => 'Ajax based main Products Home widget - Width',
            'value' => $indexCategoryPopularWidgetDimension['width'],
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Int', true),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));

        $this->addElement('Text', 'categories_ajax_based_main_products_home_height', array(
            'label' => 'Ajax based main Products Home widget - Height',
            'value' => $indexCategoryPopularWidgetDimension['height'],
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Int', true),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));
      }
      
      if( !empty($indexCategoryRatedWidgetDimension) )
      {
        $this->addElement('Text', 'most_rated_products_width', array(
            'label' => 'AJAX based Products Carousel  - (Most Rated Products) - Width',
            'value' => $indexCategoryRatedWidgetDimension['width'],
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Int', true),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));

        $this->addElement('Text', 'most_rated_products_height', array(
            'label' => 'AJAX based Products Carousel  - (Most Rated Products) - Height',
            'value' => $indexCategoryRatedWidgetDimension['height'],
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Int', true),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));
      }
      
      if( !empty($indexCategoryViewedWidgetDimension) )
      {
        $this->addElement('Text', 'most_viewed_products_width', array(
            'label' => 'AJAX based Products Carousel  - (Most Viewed Products) - Width',
            'value' => $indexCategoryViewedWidgetDimension['width'],
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Int', true),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));

        $this->addElement('Text', 'most_viewed_products_height', array(
            'label' => 'AJAX based Products Carousel  - (Most Viewed Products) - Height',
            'value' => $indexCategoryViewedWidgetDimension['height'],
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Int', true),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));
      }
      
      if( !empty($indexCategoryLikedWidgetDimension) )
      {
        $this->addElement('Text', 'most_liked_products_width', array(
            'label' => 'AJAX based Products Carousel  - (Most Liked Products) - Width',
            'value' => $indexCategoryLikedWidgetDimension['width'],
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Int', true),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));

        $this->addElement('Text', 'most_liked_products_height', array(
            'label' => 'AJAX based Products Carousel  - (Most Liked Products) - Height',
            'value' => $indexCategoryLikedWidgetDimension['height'],
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Int', true),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));
      }
    }
    
    // Stores - Wishlist Profile
    $wishlistWidgetDimension = Zend_Json::decode($this->_getWidgetDimensions('sitestoreproduct_wishlist_profile', 'sitestoreproduct.wishlist-profile-items', array('width' => 'itemWidth')));

    if( !empty($wishlistWidgetDimension) )
    {
      $this->addElement( 'Dummy' , 'dummy_wishlist_profile' , array ('label' => 'Stores - Wishlist Profile'));
      $this->addElement('Text', 'wishlist_profile_width', array(
          'label' => 'Wishlist Profile: Added Products - Width',
          'value' => $wishlistWidgetDimension['width'],
          'allowEmpty' => false,
          'required' => true,
          'validators' => array(
              array('NotEmpty', true),
              array('Int', true),
          ),
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
          )
      ));
    }
    
    // Store Profile
    $storeProfileWidgetDimension = Zend_Json::decode($this->_getWidgetDimensions('sitestore_index_view', 'sitestoreproduct.store-profile-products', array('height' => 'columnHeight', 'width' => 'columnWidth')));

    if( !empty($storeProfileWidgetDimension) )
    {
      $this->addElement( 'Dummy' , 'dummy_store_profile' , array ('label' => 'Store Profile'));
      $this->addElement('Text', 'manage_products_width', array(
          'label' => 'Manage Products - Width',
          'value' => $storeProfileWidgetDimension['width'],
          'allowEmpty' => false,
          'required' => true,
          'validators' => array(
              array('NotEmpty', true),
              array('Int', true),
          ),
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
          )
      ));

      $this->addElement('Text', 'manage_products_height', array(
          'label' => 'Manage Products - Height',
          'value' => $storeProfileWidgetDimension['height'],
          'allowEmpty' => false,
          'required' => true,
          'validators' => array(
              array('NotEmpty', true),
              array('Int', true),
          ),
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
          )
      ));
    }

    // Store Profile
    $memberProfileWidgetDimension = Zend_Json::decode($this->_getWidgetDimensions('user_profile_index', 'sitestoreproduct.profile-sitestoreproduct', array('height' => 'columnHeight', 'width' => 'columnWidth')));

    if( !empty($memberProfileWidgetDimension) )
    {
      $this->addElement( 'Dummy' , 'dummy_member_profile' , array ('label' => 'Member Profile'));
      $this->addElement('Text', 'member_profile_width', array(
          'label' => 'Member Profile: Profile Products - Width',
          'value' => $memberProfileWidgetDimension['width'],
          'allowEmpty' => false,
          'required' => true,
          'validators' => array(
              array('NotEmpty', true),
              array('Int', true),
          ),
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
          )
      ));

      $this->addElement('Text', 'member_profile_height', array(
          'label' => 'Member Profile: Profile Products - Height',
          'value' => $memberProfileWidgetDimension['height'],
          'allowEmpty' => false,
          'required' => true,
          'validators' => array(
              array('NotEmpty', true),
              array('Int', true),
          ),
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
          )
      ));
    }

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'order' => 500,
    ));
  }
}