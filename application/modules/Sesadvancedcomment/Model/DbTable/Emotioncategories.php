<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedcomment
 * @package    Sesadvancedcomment
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Emotioncategories.php 2017-01-19 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesadvancedcomment_Model_DbTable_Emotioncategories extends Engine_Db_Table
{
  protected $_rowClass = 'Sesadvancedcomment_Model_Category';

  public function getPaginator($params = array())
  {
    return Zend_Paginator::factory($this->getCategories($params));
  }
  public function getCategories($params = array()){
     $select = ($this->select());
    if(!empty($params['fetchAll'])){
      return $this->fetchAll($select);  
    }
    return $select;
  }
  
  public function searchResult($text = '') {
  
    
    $galleryTableName = Engine_Api::_()->getItemTable('sesadvancedcomment_emotiongallery')->info('name');
    
    $fileTable = Engine_Api::_()->getItemTable('sesadvancedcomment_emotionfile');
    $fileTableName = $fileTable->info('name');

    $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
    $tmName = $tmTable->info('name');
    
//     $select = $this->select()
//                   ->setIntegrityCheck(false)
//                   ->from($this->info('name'))
//                   
//                   //->joinLeft($galleryTableName,$galleryTableName.'.category_id ='.$this->info('name').'.category_id', null)
//                   ->joinLeft($fileTableName,$fileTableName.'.gallery_id ='.$galleryTableName.'.gallery_id', array('photo_id','files_id'))
//                   
//                   ->where($this->info('name').'.title LIKE ?','%'.$text.'%')
// 
//                 ->joinLeft($tmName, "$tmName.resource_id = $fileTableName.files_id")
//                 ->where($tmName.'.resource_type = ?', 'sesadvancedcomment_files')
//                 ->where($tmName.'.tag_id = ?', '3')
// 
//                   ->where($fileTableName . '.photo_id IS NOT NULL')
//                   ->limit(25)
//                   ->order('Rand()'); //echo $select;die;

    $tagsTable = Engine_Api::_()->getDbtable('tags', 'core');
    $tagsTableName = $tagsTable->info('name');
    
    $results = $tagsTable->select()->from($tagsTableName, array('tag_id'))->where($tagsTableName . '.text =?', $text)->limit(1)->query()->fetchColumn();
    $select = $fileTable->select()
              ->setIntegrityCheck(false)
              ->from($fileTableName)
              ->joinLeft($tmName, "$tmName.resource_id = $fileTableName.files_id")
              ->where($tmName.'.resource_type = ?', 'sesadvancedcomment_files');
              if($results) {
                $select->where($tmName.'.tag_id = ?', $results);
              } else {
                $select->where($tmName.'.tag_id = ?', 0);
              }
              $select->where($fileTableName . '.photo_id IS NOT NULL')
                    ->limit(25)
                    ->order('Rand()');

    return $this->fetchAll($select);
  }
}