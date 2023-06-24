<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Category.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_Category extends Core_Model_Item_Abstract {

  protected $_searchTriggers = false;

  public function getTitle($inflect = false) {
    if ($inflect) {
      return ucwords($this->category_name);
    } else {
      return $this->category_name;
    }
  }

  public function getHref($params = array()) {

    if ($this->subcat_dependency) {
      $type = 'subsubcategory';
      $params['subsubcategory_id'] = $this->category_id;
      $params['subsubcategoryname'] = $this->getCategorySlug();
      $cat =  $this->getTable()->getCategory($this->cat_dependency);
      $params['subcategory_id'] = $cat->category_id;
      $params['subcategoryname'] = $cat->getCategorySlug();
      $cat = $this->getTable()->getCategory( $cat->cat_dependency);
      $params['category_id'] = $cat->category_id;
      $params['categoryname'] = $cat->getCategorySlug();
    } else if ($this->cat_dependency) {
      $type = 'subcategory';
      $params['subcategory_id'] = $this->category_id;
      $params['subcategoryname'] = $this->getCategorySlug();
      $cat = $this->getTable()->getCategory($this->cat_dependency);
      $params['category_id'] = $cat->category_id;
      $params['categoryname'] = $cat->getCategorySlug();
    } else {
      $type = 'category';
      $params['category_id'] = $this->category_id;
      $params['categoryname'] = $this->getCategorySlug();
    }
    
    $route = "sitestoreproduct_general_" . $type;
    if(Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) { 
      if($type == 'category') {
        $route = Engine_Api::_()->sitestoreproduct()->getCategoryHomeRoute();
      }
    }
    
    $params = array_merge(array(
        'route' => $route,
        'reset' => true,
            ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble($params, $route, $reset);
  }

  /**
   * Return slug corrosponding to category name
   *
   * @return categoryname
   */
  public function getCategorySlug() {

    if(!empty($this->category_slug)) {
      return $this->category_slug;
    } else {
      return Engine_Api::_()->seaocore()->getSlug($this->category_name, 225); 
    }
  }

  /**
   * Set category icon
   *
   */
  public function setPhoto($photo, $isMainPhoto = false) {
    if ($photo instanceof Zend_Form_Element_File) {
      $file = $photo->getFileName();
    } else if (is_array($photo) && !empty($photo['tmp_name'])) {
      $file = $photo['tmp_name'];
    } else if (is_string($photo) && file_exists($photo)) {
      $file = $photo;
    } else {
      return;
    }

    if (empty($file))
      return;

    //GET PHOTO DETAILS
    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $mainName = $path . '/' . $name;

    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $photo_params = array(
        'parent_id' => $viewer_id,
        'parent_type' => "sitestoreproduct_product",
    );

    //RESIZE IMAGE WORK
    if( empty($isMainPhoto) ) {
      $image = Engine_Image::factory();
      $image->open($file);
      $image->open($file)
              ->resample(0, 0, $image->width, $image->height, $image->width, $image->height)
              ->write($mainName)
              ->destroy();
    }else {
      
      // Add autorotation for uploded images. It will work only for SocialEngine-4.8.9 Or more then.
      $usingLessVersion = Engine_Api::_()->seaocore()->usingLessVersion('core', '4.8.9');
      if(!empty($usingLessVersion)) {
        //RESIZE IMAGE (PROFILE)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(300, 500)
                ->write($mainName)
                ->destroy();
      }else {      
        //RESIZE IMAGE (PROFILE)
        $image = Engine_Image::factory();
        $image->open($file)
                ->autoRotate()
                ->resize(300, 500)
                ->write($mainName)
                ->destroy();
      }
    }

    try {
      $photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);
    } catch (Exception $e) {
      if ($e->getCode() == Storage_Api_Storage::SPACE_LIMIT_REACHED_CODE) {
        echo $e->getMessage();
        exit();
      }
    }

    return $photoFile;
  }

  public function hasChild() {
    $table = $this->getTable();
    //RETURN RESULTS
    return $table->select()
                    ->from($table, new Zend_Db_Expr('COUNT(cat_dependency)'))
                    ->where('cat_dependency = ?', $this->category_id)
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
  }

  public function applyCompare() {
    
    $categories_ids = array();
    $table = $this->getTable();
    // 3rdlevel case
    $parent_compare_category_id = 0;
    if (!empty($this->cat_dependency) && !empty($this->subcat_dependency)) {
      $cat_levle = 3;
      $sub_cat = $table->getCategory($this->cat_dependency);
      if ($sub_cat->apply_compare) {
        $parent_compare_category_id = $sub_cat->category_id;
      } else {
        $parent_compare_category_id = $sub_cat->cat_dependency;
      }
    } elseif (!empty($this->cat_dependency)) { // subcategory Case
      $cat_levle = 2;
      $cat = $table->getCategory($this->cat_dependency);
      if ($cat->apply_compare) {
        $parent_compare_category_id = $cat->category_id;
      }
    } else { // category case
      $cat_levle = 1;
      $parent_compare_category_id = 0;
    }


    if ($parent_compare_category_id) {
      $table->update(array('apply_compare' => 1), array('cat_dependency = ?' => $parent_compare_category_id));
      $parent_cat = $table->getCategory($parent_compare_category_id);
      $parent_cat->apply_compare = 0;
      $parent_cat->save();
      if ($cat_levle == 3) {
        $table->update(array('apply_compare' => 1), array('cat_dependency = ?' => $this->cat_dependency));
        $parent_cat = $table->getCategory($this->cat_dependency);
        $parent_cat->apply_compare = 0;
        $parent_cat->save();
      }
    }

    if ($cat_levle <= 2) {
      $table->update(array('apply_compare' => 0), array('cat_dependency = ?' => $this->category_id));
      if ($cat_levle == 1) {
        $subCategories = $table->getSubCategories($this->category_id);
        foreach ($subCategories as $subcategory) {
          $table->update(array('apply_compare' => 0), array('cat_dependency = ?' => $subcategory->category_id));
        }
      }
    }

    $this->apply_compare = 1;
    $this->save();
  }

  public function afterCreate() {
    
    $table = $this->getTable();
    $compareFlage = true;
    if (!empty($this->cat_dependency) && !empty($this->subcat_dependency)) {
      $subCat = $table->getCategory($this->cat_dependency);
      if ($subCat->apply_compare) {
        $compareFlage = false;
      } else {
        $cat = $table->getCategory($subCat->cat_dependency);
        if ($cat->apply_compare) {
          $compareFlage = false;
        }
      }
    } elseif (!empty($this->cat_dependency) && empty($this->subcat_dependency)) {
      $firstlevelCat = $table->getCategory($this->cat_dependency);
      if ($firstlevelCat->apply_compare) {
        $compareFlage = false;
      }
    }
    if ($compareFlage) {
      $this->apply_compare = 1;
      $this->save();
    }
    $compareSettingsTable = Engine_Api::_()->getDbtable('compareSettings', 'sitestoreproduct');
    $select = $compareSettingsTable->select()
            ->from($compareSettingsTable->info('name'))
            ->where('category_id = ?', $this->category_id)
            ->limit(1);
    $isRowExist = $compareSettingsTable->fetchRow($select);
    if (!empty($isRowExist)) {
      $select = $compareSettingsTable->select()
              ->from($compareSettingsTable->info('name'));
      $rowObjects = $compareSettingsTable->fetchAll($select);
      foreach ($rowObjects as $rowObj) {
        $isCategoryAvailable = Engine_Api::_()->getItem('sitestoreproduct_category', $rowObj->category_id);
        if (empty($isCategoryAvailable)) {
          $rowObj->delete();
        }
      }
    }
    
    $compareSettingsTable->insert(array('category_id' => $this->category_id));
  }

  protected function _delete() {

    $compareSettingsTable = Engine_Api::_()->getDbtable('compareSettings', 'sitestoreproduct');
    $compareSettingsTable->delete(array('category_id = ?' => $this->category_id));

    $ratingParamsTable = Engine_Api::_()->getDbtable('ratingparams', 'sitestoreproduct');
    $select = $ratingParamsTable->select()
            ->from($ratingParamsTable->info('name'), 'ratingparam_id')
            ->where('category_id = ?', $this->category_id)
            ->where('resource_type = ?', 'sitestoreproduct_product');

    $ratingParams = $ratingParamsTable->fetchAll($select);
    foreach ($ratingParams as $ratingParam) {
      Engine_Api::_()->getItem('sitestoreproduct_ratingparam', $ratingParam->ratingparam_id)->delete();
    }

    //FIRST SAVE PAGE ID'S CORROSPONDING TO CATEGORY ID FOR UPDATION AFTER DELETE FROM RATING TABLE
    $tableRating = Engine_Api::_()->getDbtable('ratings', 'sitestoreproduct');

    $tableRating->delete(array('ratingparam_id != ?' => 0, 'category_id = ?' => $this->category_id, 'resource_type =?' => 'sitestoreproduct_product'));

    $tableRating->update(array('category_id' => 0), array('category_id = ?' => $this->category_id, 'resource_type =?' => 'sitestoreproduct_product'));
    
    Engine_Api::_()->getApi('productType', 'sitestoreproduct')->categoryWidgetizedPagesDelete($this->category_id);

    parent::_delete();
  }

}