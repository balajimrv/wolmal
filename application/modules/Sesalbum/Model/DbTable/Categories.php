<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Categories.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Model_DbTable_Categories extends Engine_Db_Table {
  protected $_rowClass = 'Sesalbum_Model_Category';
	 protected $_name = 'album_categories';
	 protected $_searchTriggers = false;
	 public function getCategoryId($slug = null){
		if($slug){
			$tableName = $this->info('name');
			$select = $this->select()
				->from($tableName)
				->where($tableName.'.slug = ?',$slug);
			$row = $this->fetchRow($select);
			if(empty($row)){
					$category_id = $slug;
			}else
				$category_id = $row->category_id;
		}
		if(isset($category_id))
			return $category_id;
		else
			return ;
	}
		public function deleteCategory($params = array()){
			$isValid= false;
			if(count($params)>0){
				if($params->subcat_id != 0){
					$Subcategory =	$this->getModuleSubsubcategory(array('column_name'=>'*','category_id'=>$params->category_id));
					if(count($Subcategory)>0)
						$isValid = false;
					else
						$isValid = true;
				}else	if($params->subsubcat_id != 0){
					$isValid = true;
				}else{
					$category = $this->getModuleSubcategory(array('column_name'=>'*','category_id'=>$params->category_id));
					if(count($category)>0)
						$isValid = false;
					else
						$isValid = true;
				}
			}
			return $isValid;
		}
		public function getBreadcrumb($params = array()){
			$category= false;
			$Subcategory = false;
			$subSubcategory = false;
			if(count($params)>0){
				if($params->subcat_id != 0){
					$category =	$this->getModuleCategory(array('column_name'=>'*','category_id'=>$params->subcat_id));
					$Subcategory =	$this->getModuleCategory(array('column_name'=>'*','category_id'=>$params->category_id));
				}else	if($params->subsubcat_id != 0){
					$subSubcategory = $this->getModuleCategory(array('column_name'=>'*','category_id'=>$params->category_id));
					$Subcategory =	$this->getModuleCategory(array('column_name'=>'*','category_id'=>$params->subsubcat_id));
					$category =	$this->getModuleCategory(array('column_name'=>'*','category_id'=>$Subcategory[0]['subcat_id']));
				}else
					$category = $this->getModuleCategory(array('column_name'=>'*','category_id'=>$params->category_id));
			}
			 return array('category'=>$category,'subcategory'=>$Subcategory,'subSubcategory'=>$subSubcategory);
		}
		public function slugExists($slug = '',$id = ''){
			if($slug != ''){
				$tableName = $this->info('name');
					$select = $this->select()
						->from($tableName)
						->where($tableName.'.slug = ?',$slug);
				if($id != ''){
					$select = $select->where('id != ?',$id);	
				}
					$row = $this->fetchRow($select);
					if(empty($row)){
							return true;
					}else
						return false;
			}
			return false;
		}
		public function orderNext($params = array()){
			$category_select = $this->select()
							->from($this->info('name'), '*')
							->limit(1)
							->order('order DESC');
			if(isset($params['category_id'])){
					$category_select = $category_select->where('subcat_id = ?', 0)->where('subsubcat_id = ?', 0);
			}else if(isset($params['subsubcat_id'])){
					$category_select = $category_select->where('subsubcat_id = ?', $params['subsubcat_id']);
			}else if(isset($params['subcat_id'])){
				$category_select = $category_select->where('subcat_id = ?', $params['subcat_id']);
			}
			$category_select = $this->fetchRow($category_select);
			if(empty($category_select))
				$order = 1;
			else
				$order = $category_select['order']+1;
			return $order;
		}
		public function getCategoryForWidget($params = array()){
		if(isset($params['column_name'])){
			$column  = $params['column_name'];	
		}else
			$column = '*';
		 $tableName = $this->info('name');
   	 $category_select = $this->select()
            ->from($tableName, $column)
            ->where($tableName.'.subcat_id = ?', 0)
            ->where($tableName.'.subsubcat_id = ?', 0);
		$albumTable = Engine_Api::_()->getDbTable('albums', 'sesalbum')->info('name');
		if(isset($params['type']) && $params['type'] == 'album'){
			$category_select= $category_select->setIntegrityCheck(false);
			$category_select = $category_select->joinLeft($albumTable,"$albumTable.category_id=$tableName.category_id",array("total_item_categories"=>"COUNT(distinct album_id)"));
																					
			if(($params['show_category_has_count']) == 'yes'){
			$category_select = $category_select->having("COUNT($albumTable.category_id) > 0")
																					->group("$albumTable.category_id");
					$category_select->order('total_item_categories DESC');
			}else if($params['show_count'] == 'yes'){
				$category_select = $category_select->group("$tableName.category_id");
					$category_select->order('total_item_categories DESC');
			}
		}else{
			$photoTable = Engine_Api::_()->getDbTable('photos', 'sesalbum')->info('name');
			$category_select= $category_select->setIntegrityCheck(false);
			$category_select = $category_select
																->joinLeft($albumTable,"$albumTable.category_id=$tableName.category_id",array('album_id'))
																->joinLeft($photoTable,"$photoTable.album_id=$albumTable.album_id",array("total_item_categories"=>"COUNT(distinct $photoTable.photo_id)",'photo_id'));
			if(($params['show_category_has_count']) == 'yes'){
			$category_select = $category_select->having("COUNT(distinct $photoTable.photo_id) > 0")
																					->group("$tableName.category_id");
					$category_select->order('total_item_categories DESC');
			}else if($params['show_count'] == 'yes'){
				$category_select = $category_select->group("$tableName.category_id");
					$category_select->order('total_item_categories DESC');
			}
		}
		$category_select = $category_select->group("$tableName.category_id");
		$category_select = $category_select->order('order DESC');
    if (isset($params['category_id']) && !empty($params['category_id']))
      $category_select = $category_select->where($tableName.'.category_id = ?', $params['category_id']);
		if(count($params) && isset($params['paginator'])){
			return  Zend_Paginator::factory($category_select);
		}
    return $this->fetchAll($category_select);
		}
	  public function getCategory($params = array(),$customParams = array()) {
		if(isset($params['column_name'])){
			$column  = $params['column_name'];	
		}else
			$column = '*';
    $tableName = $this->info('name');
    $category_select = $this->select()
            ->from($tableName, $column)
            ->where($tableName.'.subcat_id = ?', 0)
            ->where($tableName.'.subsubcat_id = ?', 0);
		if(isset($params['criteria']) && $params['criteria'] == 'alphabetical')
			$category_select->order($tableName.'.category_name');		
		if((isset($params['hasAlbum']) && $params['hasAlbum']) || isset($params['countAlbums']) || (isset($params['criteria']) && $params['criteria'] == 'most_album')){
				$albumTable = Engine_Api::_()->getDbTable('albums', 'sesalbum')->info('name');
				$category_select= $category_select->setIntegrityCheck(false);
				if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.wall.profile', 1))
				$category_select->where($albumTable.'.type IS NULL');
				$category_select = $category_select->joinLeft($albumTable,"$albumTable.category_id=$tableName.category_id",array("total_album_categories"=>"COUNT($albumTable.album_id)"));
																						
				if(empty($params['countAlbums'])){
				$category_select = $category_select->having("COUNT($albumTable.category_id) > 0")
																						->group("$albumTable.category_id");
				if(isset($params['albumDesc'])){
					if($params['criteria'] && $params['criteria'] == 'most_album'){
						$category_select->order('total_album_categories DESC');
					}
				}
				}else{
					$category_select = $category_select->group("$tableName.category_id");
					if($params['criteria'] && $params['criteria'] == 'most_album'){
						$category_select->order('total_album_categories DESC');
					}
				}
		}
				
		$category_select = $category_select->order('order DESC');
    if (isset($params['category_id']) && !empty($params['category_id']))
      $category_select = $category_select->where($tableName.'.category_id = ?', $params['category_id']);
		
		if(count($params) && isset($params['paginator'])){
			return  Zend_Paginator::factory($category_select);
		}
    return $this->fetchAll($category_select);
  }
		public function order($categoryType = 'category_id',$categoryTypeId){
			// Get a list of all corresponding category, by order
			$table = Engine_Api::_()->getItemTable('sesalbum_category');
			$currentOrder = $table->select()
							->from($table, 'category_id')
							->order('order DESC');	
			if($categoryType != 'category_id')
				$currentOrder = $currentOrder->where($categoryType.' = ?', $categoryTypeId);
			else
				$currentOrder = $currentOrder->where('subcat_id = ?', 0)->where('subsubcat_id = ?', 0);
			return $currentOrder->query()->fetchAll(Zend_Db::FETCH_COLUMN);
		}
	  public function getMapping($params = array()) {
    $select = $this->select()->from($this->info('name'), $params);
    $mapping = $this->fetchAll($select);
    if (!empty($mapping)) {
      return $mapping->toArray();
    }
    return null;
  }
	public function getMapId($categoryId = ''){
			$tableName = $this->info('name');
			if($categoryId){
					$category_map_id = $this->select()
            ->from($tableName, 'profile_type')
            ->where('category_id = ?', $categoryId);
					$category_map_id = $this->fetchAll($category_map_id);
					if(isset($category_map_id[0]->profile_type)){
						return $category_map_id[0]->profile_type;	
					}else
						return 0 ;
			}
	}
	public function getSubCatMapId($subcategoryId = ''){
			$tableName = $this->info('name');
			if($subcategoryId != ''){
					$category_map_id = $this->select()
            ->from($tableName, 'profile_type')
            ->where('category_id = ?', $subcategoryId);
					$category_map_id = $this->fetchAll($category_map_id);
					if(isset($category_map_id[0]->profile_type)){
						return $category_map_id[0]->profile_type;	
					}else
						return 0 ;
			}
	}
	public function getSubSubCatMapId($subsubcategoryId = ''){
			$tableName = $this->info('name');
			if($subsubcategoryId != ''){
					$category_map_id = $this->select()
            ->from($tableName, 'profile_type')
            ->where('category_id = ?', $subsubcategoryId);
					$category_map_id = $this->fetchAll($category_map_id);
					if(isset($category_map_id[0]->profile_type)){
						return $category_map_id[0]->profile_type;	
					}else
						return 0 ;
			}
	}
  public function getCategoriesAssoc($params = array()) {
    $stmt = $this->select()
            ->from($this, array('category_id', 'category_name'))
            ->where('subcat_id = ?', 0)
            ->where('subsubcat_id = ?', 0);
    if (isset($params['module'])) {
      $stmt = $stmt->where('resource_type = ?', $params['module']);
    }
    $stmt = $stmt->order('order DESC')
            ->query()
            ->fetchAll();
    $data = array();
    if (isset($params['module']) && $params['module'] == 'group') {
      $data[] = '';
    }
    foreach ($stmt as $category) {
      $data[$category['category_id']] = $category['category_name'];
    }
    return $data;
  }
  public function getColumnName($params = array()) {
    $tableName = $this->info('name');
    $category_select = $this->select()
            ->from($tableName, $params['column_name']);
    if (isset($params['category_id']))
      $category_select = $category_select->where('category_id = ?', $params['category_id']);
		if (isset($params['subcat_id']))
      $category_select = $category_select->where('subcat_id = ?', $params['subcat_id']);
    return $category_select = $category_select->query()->fetchColumn();
  }
  public function getModuleSubcategory($params = array()) {
		$tableName = $this->info('name');
    $category_select = $this->select()
            ->from($tableName, $params['column_name']);
    if (isset($params['category_id']))
      $category_select = $category_select->where($tableName.'.subcat_id = ?', $params['category_id']);	 
			if(isset($params['countAlbums'])){
				$albumTable = Engine_Api::_()->getDbTable('albums', 'sesalbum')->info('name');
				$category_select= $category_select->setIntegrityCheck(false);
				$category_select = $category_select->joinLeft($albumTable,"$albumTable.subcat_id=$tableName.category_id",array("total_albums_categories"=>"COUNT($albumTable.photo_id)"));
				$category_select = $category_select->group("$tableName.category_id");
				$category_select->order('total_albums_categories DESC');
		}	
		$category_select = $category_select->order('order DESC');
    return $this->fetchAll($category_select);
  }
	public function getModuleCategory($params = array()) {
    $category_select = $this->select()
            ->from($this->info('name'), $params['column_name']);
    if (isset($params['category_id']))
      $category_select = $category_select->where('category_id = ?', $params['category_id']);	
$category_select = $category_select->order('order DESC');
    return $this->fetchAll($category_select);
  }
  public function getModuleSubsubcategory($params = array()) {
		$tableName = $this->info('name');
    $category_select = $this->select()
            ->from($this->info('name'), $params['column_name']);
    if (isset($params['category_id']))
      $category_select = $category_select->where($tableName.'.subsubcat_id = ?', $params['category_id']);
		if(isset($params['countAlbums'])){
				$albumTable = Engine_Api::_()->getDbTable('albums', 'sesalbum')->info('name');
				$category_select= $category_select->setIntegrityCheck(false);
				$category_select = $category_select->joinLeft($albumTable,"$albumTable.subsubcat_id=$tableName.category_id",array("total_albums_categories"=>"COUNT($albumTable.album_id)"));
																						
					$category_select = $category_select->group("$tableName.category_id");
					$category_select->order('total_albums_categories DESC');
		}
		$category_select = $category_select->order('order DESC');
    return $this->fetchAll($category_select);
  }
}