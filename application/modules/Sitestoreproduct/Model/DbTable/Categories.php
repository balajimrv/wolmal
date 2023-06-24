<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Categories.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Categories extends Engine_Db_Table {

  protected $_rowClass = 'Sitestoreproduct_Model_Category';
  protected $_categories = array();
  /**
   * Return subcaregories
   *
   * @param int category_id
   * @return all sub categories
   */
  public function getSubCategories($category_id, $countOnly = 0) {

    //RETURN IF CATEGORY ID IS EMPTY
    if (empty($category_id)) {
      return;
    }

    //MAKE QUERY
    $select = $this->select();
    
    if($countOnly) {
      return $select->from($this->info('name'), array('COUNT(*) AS total_subcats'))
                  ->where('cat_dependency = ?', $category_id)
                  ->query()
                  ->fetchColumn();
    }
    else {
      $select->from($this->info('name'), array('*'))
              ->where('cat_dependency = ?', $category_id)
              ->order('cat_order');
      return $this->fetchAll($select);
    }    
  }

  /**
   * Get category object
   * @param int $category_id : category id
   * @return category object
   */
  public function getCategory($category_id) {
    if (empty($category_id))
      return;
    if (!array_key_exists($category_id, $this->_categories)) {
      $this->_categories[$category_id] = $this->find($category_id)->current();
    }
    return $this->_categories[$category_id];
  }

  public function getCategoriesList($cat_depandancy= -1) {

    $select = $this->select()->order('cat_order');

    if ($cat_depandancy != -1) {
      $select->where('cat_dependency = ?', $cat_depandancy);
    }

    //RETURN DATA
    return $this->fetchAll($select);
  }
  
  /**
   * Return categories
   *
   * @param array $category_ids
   * @return all categories
   */
  public function getCategories($category_ids = null, $count_only = 0, $sponsored = 0, $cat_depandancy = 0, $limit = 0, $orderBy = 'cat_order') {

    //MAKE QUERY
    $select = $this->select();  

    if ($orderBy == 'category_name') {
      $select->order('category_name');
    } else {
      $select->order('cat_order');
    }

    if (!empty($cat_depandancy)) {
      $select->where('cat_dependency = ?', 0);
    }

    if (!empty($sponsored)) {
      $select->where('sponsored = ?', 1);
    }

    if (!empty($category_ids)) {
      foreach ($category_ids as $ids) {
        $categoryIdsArray[] = "category_id = $ids";
      }
      $select->where("(" . join(") or (", $categoryIdsArray) . ")");
    }

    if (!empty($count_only)) {
      return $select->from($this->info('name'), 'category_id')->query()->fetchColumn();
    }

    if (!empty($limit)) {
      $select->limit($limit);
    }
  
    //RETURN DATA
    return $this->fetchAll($select);
  }


  public function similarItemsCategories($element_value, $element_type) {

    $categoriesTable = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct');
    $select = $this->select()
            ->from($this->info('name'), array('category_id', 'category_name'))
            ->where("$element_type = ?", $element_value);

    if ($element_type == 'category_id') {
      $select->where('cat_dependency = ?', 0)->where('subcat_dependency = ?', 0);
    } elseif ($element_type == 'cat_dependency') {
      $select->where('subcat_dependency = ?', 0);
    } elseif ($element_type == 'subcat_dependency') {
      $select->where('cat_dependency = ?', $element_value);
    }

    $categoriesData = $this->fetchAll($select);

    $categories = array();
    if (Count($categoriesData) > 0) {
      foreach ($categoriesData as $category) {
        $data = array();
        $data['category_name'] = Zend_Registry::get('Zend_View')->translate($category->category_name);
        $data['category_id'] = $category->category_id;
        $categories[] = $data;
      }
    }

    return $categories;
  }

  /**
   * Get Mapping array
   *
   */
  public function getMapping($profileTypeName = 'profile_type') {

    //MAKE QUERY
    $select = $this->select()->from($this->info('name'), array('category_id', "$profileTypeName"));

    //FETCH DATA
    $mapping = $this->fetchAll($select);

    //RETURN DATA
    if (!empty($mapping)) {
      return $mapping->toArray();
    }

    return null;
  }

  public function getChildMapping($category_id, $profileTypeName = 'profile_type') {

    //GET CATEGORY TABLE NAME
    $categoryTableName = $this->info('name');

    $category = Engine_Api::_()->getItem('sitestoreproduct_category', $category_id);

    $select = $this->select()
            ->from($categoryTableName, 'category_id')
            ->where("$profileTypeName != ?", 0)
            ->where("cat_dependency = $category->category_id OR subcat_dependency = $category->category_id");

    return $this->fetchAll($select);
  }

  public function getChilds($category_id) {

    //GET CATEGORY TABLE NAME
    $categoryTableName = $this->info('name');

    $category = Engine_Api::_()->getItem('sitestoreproduct_category', $category_id);

    $select = $this->select()
            ->from($categoryTableName)
            ->where("cat_dependency = ?", $category_id);

    //IF SUBCATEGORY THEN FETCH 3RD LEVEL CATEGORY
    if ($category->cat_dependency != 0 && $category->subcat_dependency == 0) {
      $select->where("subcat_dependency = ?", $category_id);
    }
    //IF CATEGORY THEN FETCH SUB-CATEGORY
    elseif ($category->cat_dependency == 0 && $category->subcat_dependency == 0) {
      $select->where("subcat_dependency = ?", 0);
    }
    //IF 3RD LEVEL CATEGORY
    else {
      return array();
    }

    return $this->fetchAll($select);
  }

  /**
   * Get profile_type corresponding to category_id
   *
   * @param int category_id
   */
  public function getProfileType($categoryIds = array(), $categoryId = 0, $profileTypeName = 'profile_type') {

    if (!empty($categoryIds)) {
      $profile_type = 0;
      foreach ($categoryIds as $value) {
        $profile_type = $this->select()
                ->from($this->info('name'), array("$profileTypeName"))
                ->where("category_id = ?", $value)
                ->query()
                ->fetchColumn();

        if (!empty($profile_type)) {
          return $profile_type;
        }
      }

      return $profile_type;
    } elseif (!empty($categoryId)) {

      //FETCH DATA
      $profile_type = $this->select()
              ->from($this->info('name'), array("$profileTypeName"))
              ->where("category_id = ?", $categoryId)
              ->query()
              ->fetchColumn();

      return $profile_type;
    }

    return 0;
  }

  public function getAllProfileTypes($categoryIds = array(), $also_find_nested=0) {

    $levelOfCategory = count($categoryIds);
    if (empty($levelOfCategory))
      return;
    $categoryIdsFinal = $categoryIds;
    if ($also_find_nested) {
      if ($levelOfCategory < 3) {
        $subCategoryIds = array();
        if ($levelOfCategory == 1) {
          $categories = $this->getChilds($categoryIds[0]);
          foreach ($categories as $category) {
            $categoryIdsFinal[] = $subCategoryIds[] = $category->category_id;
          }
        } else {
          $subCategoryIds[] = $categoryIds[1];
        }

        foreach ($subCategoryIds as $cateory_id) {
          $categories = $this->getChilds($cateory_id);
          foreach ($categories as $category) {
            $categoryIdsFinal[] = $category->category_id;
          }
        }
      }
    }
    return $this->select()
                    ->from($this->info('name'), array('profile_type'))
                    ->where("category_id In(?)", $categoryIdsFinal)
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);
  }

  /**
   * Gets all categories and subcategories
   *
   * @param string $category_id
   * @param string $fieldname
   * @param int $sitestoreproductCondition
   * @param string $sitestoreproduct
   * @param  all categories and subcategories
   */
  public function getCategorieshasproducts($category_id=null, $fieldname, $limit=null) {

    //GET CATEGORY TABLE NAME
    $tableCategoriesName = $this->info('name');

    //GET PRODUCTS TABLE
    $tableProduct = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
    $tableProductName = $tableProduct->info('name');

    //MAKE QUERY
    $select = $this->select()->setIntegrityCheck(false)->from($tableCategoriesName);

    $select = $select->join($tableProductName, $tableProductName . '.' . $fieldname . '=' . $tableCategoriesName . '.category_id', null);

    if (!empty($order)) {
      $select->order("$order");
    }

    $select = $select->where($tableCategoriesName . '.cat_dependency = ' . $category_id)
            ->group($tableCategoriesName . '.category_id')
            ->order('cat_order');

    if (!empty($limit)) {
      $select = $select->limit($limit);
    }

    $select->where($tableProductName . '.approved = ?', 1)->where($tableProductName . '.draft = ?', 0)->where($tableProductName . '.search = ?', 1);
    
    if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
      $select->where($tableProductName . '.closed = ?', 0);
    }    
    
    $select = $tableProduct->expirySQL($select);
    $select = $tableProduct->getNetworkBaseSql($select, array('not_groupBy' => 1));

    //RETURN DATA
    return $this->fetchAll($select);
  }

  public function getCatDependancyArray() {

    $cat_dependency = $this->select()->from($this->info('name'), 'cat_dependency')->where('cat_dependency <>?', 0)->group('cat_dependency')->query()->fetchAll(Zend_Db::FETCH_COLUMN);

    return $cat_dependency;
  }

  public function getSubCatDependancyArray() {

    $subcat_dependency = $this->select()->from($this->info('name'), 'subcat_dependency')->where('subcat_dependency <>?', 0)->group('subcat_dependency')->query()->fetchAll(Zend_Db::FETCH_COLUMN);

    return $subcat_dependency;
  }


  public function getCategoriesArray($params = array()) {
    
    $select = $this->select()
            ->from($this->info('name'), 'category_id');
    
    if(isset($params['cat_dependency'])) {
      $select->where('cat_dependency = ?', $params['cat_dependency']);
    }
         
    if(isset($params['subcat_dependency'])) {
      $select->where('subcat_dependency = ?', $params['subcat_dependency']);
    }    

    return $select->order('cat_order ASC')->query()->fetchAll(Zend_Db::FETCH_COLUMN);
  }
  
  public function getCategoriesHavingNoChield($arrayLevels = array()) {

    $categoryTableName = $this->info('name');
    $select = $this->select()
            ->from($categoryTableName, array('category_id', 'category_name','cat_dependency', '', 'subcat_dependency'))
            //->where("category_id NOT IN (SELECT cat_dependency FROM $categoryTableName)")
            ->order('cat_order');
    
    if(!empty($arrayLevels) && Count($arrayLevels) < 3) {
      
      if(!in_array('category', $arrayLevels) && in_array('subcategory', $arrayLevels) && in_array('subusbcategory', $arrayLevels)) {
        $select->where("cat_dependency != 0 OR subcat_dependency != 0");
      }
      elseif(in_array('category', $arrayLevels) && !in_array('subcategory', $arrayLevels) && in_array('subusbcategory', $arrayLevels)) {
        $select->where("(cat_dependency = 0 AND subcat_dependency = 0) OR (cat_dependency != 0 AND subcat_dependency != 0)");
      }
      elseif(in_array('category', $arrayLevels) && in_array('subcategory', $arrayLevels) && !in_array('subusbcategory', $arrayLevels)) {
        $select->where("(cat_dependency = 0 AND subcat_dependency = 0) OR (cat_dependency != 0 AND subcat_dependency = 0)");
      }
      elseif(!in_array('category', $arrayLevels) && !in_array('subcategory', $arrayLevels) && in_array('subusbcategory', $arrayLevels)) {
        $select->where("cat_dependency != 0 AND subcat_dependency != 0");
      }
      elseif(in_array('category', $arrayLevels) && !in_array('subcategory', $arrayLevels) && !in_array('subusbcategory', $arrayLevels)) {
        $select->where("cat_dependency = 0 AND subcat_dependency = 0");
      }
      elseif(!in_array('category', $arrayLevels) && in_array('subcategory', $arrayLevels) && !in_array('subusbcategory', $arrayLevels)) {
        $select->where("cat_dependency != 0 AND subcat_dependency = 0");
      }      
    }

    //RETURN DATA
    return $this->fetchAll($select);
  }    
  
  public function getCategoriesDetails($arrayLevels) {

    $categories = $this->getCategoriesHavingNoChield($arrayLevels);
    
    $categories_prepared = array();
    foreach ($categories as $category) {
      $categoryArray = array();
      if($category->cat_dependency == 0 && $category->subcat_dependency == 0) {
        $categoryArray['category_id'] = $category->category_id;
        $categoryArray['categoryname'] = $category->category_name;
        $categoryArray['subcategory_id'] = 0;
        $categoryArray['subcategoryname'] = '';
        $categoryArray['subsubcategory_id'] = 0;
        $categoryArray['subsubcategoryname'] = '';        
      }
      elseif($category->cat_dependency != 0 && $category->subcat_dependency == 0) {
        $categoryMain = Engine_Api::_()->getItem('sitestoreproduct_category', $category->cat_dependency);
        $categoryArray['category_id'] = $categoryMain->category_id;
        $categoryArray['categoryname'] = $categoryMain->category_name;
        $categoryArray['subcategory_id'] = $category->category_id;
        $categoryArray['subcategoryname'] = $category->category_name;
        $categoryArray['subsubcategory_id'] = 0;
        $categoryArray['subsubcategoryname'] = '';            
      }
      elseif($category->cat_dependency != 0 && $category->subcat_dependency != 0) {
        $categorySub = Engine_Api::_()->getItem('sitestoreproduct_category', $category->cat_dependency);
        $categoryMain = Engine_Api::_()->getItem('sitestoreproduct_category', $categorySub->cat_dependency);
        $categoryArray['category_id'] = $categoryMain->category_id;
        $categoryArray['categoryname'] = $categoryMain->category_name;
        $categoryArray['subcategory_id'] = $categorySub->category_id;
        $categoryArray['subcategoryname'] = $categorySub->category_name;
        $categoryArray['subsubcategory_id'] = $category->category_id;
        $categoryArray['subsubcategoryname'] = $category->category_name;     
      }      
      
      $categories_prepared[$category->category_id] = $categoryArray;
    }

    //RETURN DATA
    return $categories_prepared;
  }    
  
  /**
   * Gets all parent categories
   *
   * @return object
   */
  public function getAllParentCategory()
  {
    $select = $this->select()
                   ->from($this->info('name'), array("category_id", "category_name", "cat_order"))
                   ->where("cat_dependency = 0 AND subcat_dependency = 0");
    
    return $this->fetchAll($select);
  }

   /**
   * Return categories
   *
   * @param int $home_page_display
   * @return categories
   */
  public function getCategoriesByLevel($level = null) {

    $select = $this->select()->order('cat_order');
    switch ($level) {
      case 'category':
        $select->where('cat_dependency =?', 0);
        break;
      case 'subcategory':
        $select->where('cat_dependency !=?', 0);
        $select->where('subcat_dependency =?', 0);
        break;
      case 'subsubcategory':
        $select->where('cat_dependency !=?', 0);
        $select->where('subcat_dependency !=?', 0);
        break;
    }

    return $this->fetchAll($select);
  }
  
  public function getCategoriesIdByName($category,$subcategory,$subsubcategory){
    $getCatId = $this->select()
            ->from($this->info('name'), array("category_id"))
            ->where('category_name =?', $category)     
            ->where('cat_dependency =?', $subcategory)
            ->where('subcat_dependency =?', $subsubcategory)
            ->query()->fetchColumn();
    
    $catId = !empty($getCatId)? $getCatId: 0;
    return $catId;
  }
  
   public function getCategoryNameById($category_id) {
    $getCategoryName = $this->select()
                    ->from($this->info('name'), array("category_name"))
                    ->where('category_id =?', $category_id)
                    ->limit(1)
                    ->query()->fetchColumn();
    
    return $getCategoryName;
  }
  
  

}