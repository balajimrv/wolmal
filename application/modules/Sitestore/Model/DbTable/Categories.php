<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Categories.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Model_DbTable_Categories extends Engine_Db_Table {

  protected $_rowClass = 'Sitestore_Model_Category';

  /**
   * Return subcaregories
   *
   * @param int category_id
   * @return all sub categories
   */
  public function getSubCategories($category_id) {
    if (empty($category_id)) {
      return;
    }
    $select = $this->select()->from($this->info('name'), array('category_name', 'category_id', 'cat_order', 'cat_dependency'))->where('cat_dependency = ?', $category_id)->order('cat_order');
    return $this->fetchAll($select);
  }

  /**
   * Return category name for the store
   *
   * @param int category_id
   * @return Zend_Db_Table_Select
   */
  public function getCategory($category_id) {
    if (empty($category_id)) {
      return;
    }
    $select = $this->select()->from($this->info('name'), array('category_name', 'category_id'))->where('category_id =?', $category_id)->order('cat_order');
    return $this->fetchRow($select);
  }

  /**
   * Return categories
   *
   * @param int $home_store_display
   * @return categories
   */
  public function getCategories($home_store_display=0) {
    $cateName = $this->info('name');
    $select = $this->select()->where('cat_dependency =?', 0)->order('cat_order');
    if (!empty($home_store_display)) {
      $table = Engine_Api::_()->getDbtable('stores', 'sitestore');
      $rName = $table->info('name');
      $select->setIntegrityCheck(false)
              ->from($cateName)
              ->joinLeft($rName, $rName . '.category_id = ' . $cateName . '.category_id', array('count(' . $rName . '.category_id ) as count'))
              ->group($cateName . '.category_id')
              ->order('cat_order ASC');
    }
    return $this->fetchAll($select);
  }

  /**
   * Return categories
   *
   * @param int $home_store_display
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

  /**
   * Return slug
   *
   * @param int $categoryname
   * @return categoryname
   */
  public function getCategorySlug($categoryname) {
    $showslug = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.categorywithslug', 1);
    $slug = $categoryname;
//     if (!empty($showslug)) {
// 			setlocale(LC_CTYPE, 'pl_PL.utf8');
// 			$slug = iconv('UTF-8', 'ASCII//TRANSLIT', $slug);
// 			$slug = strtolower($slug);
// 			$slug = strtr($slug, array('&' => '-', '"' => '-', '&' . '#039;' => '-', '<' => '-', '>' => '-', '\'' => '-'));
// 			$slug = preg_replace('/^[^a-z0-9]{0,}(.*?)[^a-z0-9]{0,}$/si', '\\1', $slug);
// 			$slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
// 			$slug = preg_replace('/[\-]{2,}/', '-', $slug);
//     } 
    return Engine_Api::_()->seaocore()->getSlug($slug, 225);
  }

  /**
   * Gets all categories and subcategories
   *
   * @param string $category_id
   * @param string $fieldname
   * @param int $storeCondition
   * @param string $store
   * @param  all categories and subcategories
   */
  public function getAllCategories($category_id, $fieldname, $storeCondition, $store, $subcat = null, $limit = 0) {
    $tableCategoriesName = $this->info('name');
    $tableStore = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $tableStoreName = $tableStore->info('name');
    $select = $this->select()->setIntegrityCheck(false)
            ->from($tableCategoriesName);
    if ($subcat == 1) {
      $select = $select->joinLeft($tableStoreName, $tableStoreName . '.' . $fieldname . '=' . $tableCategoriesName . '.category_id', array('count(DISTINCT ' . $tableStoreName . '.' . $store . ' ) as count'));
    } else {
      $select = $select->joinLeft($tableStoreName, $tableStoreName . '.' . $fieldname . '=' . $tableCategoriesName . '.category_id', array('count(DISTINCT ' . $tableStoreName . '.store_id ) as count'));
      // $select = $select->joinLeft($tableStoreName, $tableStoreName . '.' . $fieldname . '=' . $tableCategoriesName . '.category_id', array('count( ' . $tableStoreName . '.' . $fieldname . ' ) as count'));
    }

    $select = $select->where($tableCategoriesName . '.cat_dependency = ' . $category_id)
            ->group($tableCategoriesName . '.category_id')
            ->order('cat_order');

    if (!empty($limit)) {
      $select = $select->limit($limit);
    }
    
    if ($storeCondition == 1) {
      $select->where($tableStoreName . '.closed = ?', '0')
              ->where($tableStoreName . '.approved = ?', '1')
              ->where($tableStoreName . '.draft = ?', '1');
      $select->where($tableStoreName . ".search = ?", 1);
      if (Engine_Api::_()->sitestore()->hasPackageEnable())
        $select->where($tableStoreName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
      //START NETWORK WORK
      // if ($subcat == 1) {
      $select = $tableStore->getNetworkBaseSql($select, array('not_groupBy' => 1));
      // }
      //END NETWORK WORK     
    }

    return $this->fetchAll($select);
  }

  public function getCatDependancyArray() {

    $cat_dependency = $this->select()->from($this->info('name'), 'cat_dependency')->where('cat_dependency <>?', 0)->group('cat_dependency')->query()->fetchAll(Zend_Db::FETCH_COLUMN);

    return $cat_dependency;
  }
  
  public function getCategoriesHavingNoChield($arrayLevels = array(), $showAllCategories = 0) {

    $categoryTableName = $this->info('name');
    $select = $this->select()
            ->from($categoryTableName, array('category_id', 'category_name', 'cat_dependency', 'subcat_dependency'))
            ->order('cat_order');

    if (!$showAllCategories) {
      $tableStore = Engine_Api::_()->getDbtable('stores', 'sitestore');
      $tableStoreName = $tableStore->info('name');
      $select = $this->select()->setIntegrityCheck(false)->from($categoryTableName);
    }

    $addedStoreJoin = 0;
    if (!empty($arrayLevels) && Count($arrayLevels) < 3) {

      if (!in_array('category', $arrayLevels) && in_array('subcategory', $arrayLevels) && in_array('subusbcategory', $arrayLevels)) {

        if (!$showAllCategories) {
          $select->join($tableStoreName, "$tableStoreName.subcategory_id=$categoryTableName.category_id OR $tableStoreName.subsubcategory_id=$categoryTableName.category_id", null);
          $addedStoreJoin = 1;
        }

        $select->where("cat_dependency != 0 OR subcat_dependency != 0");
      } elseif (in_array('category', $arrayLevels) && !in_array('subcategory', $arrayLevels) && in_array('subusbcategory', $arrayLevels)) {

        if (!$showAllCategories) {
          $select->join($tableStoreName, "$tableStoreName.category_id=$categoryTableName.category_id OR $tableStoreName.subsubcategory_id=$categoryTableName.category_id", null);
          $addedStoreJoin = 1;
        }

        $select->where("(cat_dependency = 0 AND subcat_dependency = 0) OR (cat_dependency != 0 AND subcat_dependency != 0)");
      } elseif (in_array('category', $arrayLevels) && in_array('subcategory', $arrayLevels) && !in_array('subusbcategory', $arrayLevels)) {

        if (!$showAllCategories) {
          $select->join($tableStoreName, "$tableStoreName.category_id=$categoryTableName.category_id OR $tableStoreName.subcategory_id=$categoryTableName.category_id", null);
          $addedStoreJoin = 1;
        }

        $select->where("(cat_dependency = 0 AND subcat_dependency = 0) OR (cat_dependency != 0 AND subcat_dependency = 0)");
      } elseif (!in_array('category', $arrayLevels) && !in_array('subcategory', $arrayLevels) && in_array('subusbcategory', $arrayLevels)) {

        if (!$showAllCategories) {
          $select->join($tableStoreName, "$tableStoreName.subsubcategory_id=$categoryTableName.category_id", null);
          $addedStoreJoin = 1;
        }

        $select->where("cat_dependency != 0 AND subcat_dependency != 0");
      } elseif (in_array('category', $arrayLevels) && !in_array('subcategory', $arrayLevels) && !in_array('subusbcategory', $arrayLevels)) {

        if (!$showAllCategories) {
          $select->join($tableStoreName, "$tableStoreName.category_id=$categoryTableName.category_id", null);
          $addedStoreJoin = 1;
        }

        $select->where("cat_dependency = 0 AND subcat_dependency = 0");
      } elseif (!in_array('category', $arrayLevels) && in_array('subcategory', $arrayLevels) && !in_array('subusbcategory', $arrayLevels)) {

        if (!$showAllCategories) {
          $select->join($tableStoreName, "$tableStoreName.subcategory_id=$categoryTableName.category_id", null);
          $addedStoreJoin = 1;
        }

        $select->where("cat_dependency != 0 AND subcat_dependency = 0");
      }
    }

    if (!$addedStoreJoin && !$showAllCategories) {
      $select->join($tableStoreName, "$tableStoreName.category_id=$categoryTableName.category_id", null);
    }

    if (!$showAllCategories) {
      $select->where($tableStoreName . '.approved = ?', 1)->where($tableStoreName . '.draft = ?', 1)->where($tableStoreName . '.search = ?', 1)->where($tableStoreName . '.closed = ?', 0);
      $select = $tableStore->getNetworkBaseSql($select, array('not_groupBy' => 1));
    }

    $select->order('cat_order');

    //RETURN DATA
    return $this->fetchAll($select);
  }
  
  public function getCategoriesDetails($arrayLevels) {

    $categories = $this->getCategoriesHavingNoChield($arrayLevels);

    $categories_prepared = array();
    foreach ($categories as $category) {
      $categoryArray = array();
      if ($category->cat_dependency == 0 && $category->subcat_dependency == 0) {
        $categoryArray['category_id'] = $category->category_id;
        $categoryArray['categoryname'] = $category->category_name;
        $categoryArray['subcategory_id'] = 0;
        $categoryArray['subcategoryname'] = '';
        $categoryArray['subsubcategory_id'] = 0;
        $categoryArray['subsubcategoryname'] = '';
      } elseif ($category->cat_dependency != 0 && $category->subcat_dependency == 0) {
        $categoryMain = Engine_Api::_()->getItem('sitestore_category', $category->cat_dependency);
        $categoryArray['category_id'] = $categoryMain->category_id;
        $categoryArray['categoryname'] = $categoryMain->category_name;
        $categoryArray['subcategory_id'] = $category->category_id;
        $categoryArray['subcategoryname'] = $category->category_name;
        $categoryArray['subsubcategory_id'] = 0;
        $categoryArray['subsubcategoryname'] = '';
      } elseif ($category->cat_dependency != 0 && $category->subcat_dependency != 0) {
        $categorySub = Engine_Api::_()->getItem('sitestore_category', $category->cat_dependency);
        $categoryMain = Engine_Api::_()->getItem('sitestore_category', $categorySub->cat_dependency);
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

}

?>