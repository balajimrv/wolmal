<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Ratings.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Model_DbTable_Ratings extends Engine_Db_Table
{
  protected $_rowClass = "Sesalbum_Model_Rating";
	protected $_name = "sesalbum_ratings";
	// rating functions
	public function getRating($resource_id,$resource_type = 'album')
  {
    $rating_sum = $this->select()
      ->from($this->info('name'), new Zend_Db_Expr('SUM(rating)'))
      ->group('resource_id')
      ->where('resource_id = ?', $resource_id)
			->where('resource_type =?',$resource_type)
      ->query()
      ->fetchColumn(0)
      ;
    $total = $this->ratingCount($resource_id,$resource_type);
    if ($total) $rating = $rating_sum/$this->ratingCount($resource_id,$resource_type);
    else $rating = 0;
    
    return $rating;
  }
	public function getRatingItems($params = array()){
    $select = $this->select()
      ->from($this->info('name'))
      ->where('resource_type = ?', $params['type'])
			->order('resource_id DESC');
		 $albumTableName = Engine_Api::_()->getItemTable('album')->info('name');
		if($params['type'] == 'album_photo'){
			$photoTableName = Engine_Api::_()->getItemTable('album_photo')->info('name');
			$select = $select->joinLeft($photoTableName, $photoTableName . '.photo_id = ' . $this->info('name') . '.resource_id', null)
											->where($photoTableName.'.photo_id != ?','');
			$select = $select ->joinLeft($albumTableName, $albumTableName . '.album_id = ' . $photoTableName . '.album_id', null)
												->where($albumTableName.'.album_id !=?','');
		}else{
			$select = $select ->joinLeft($albumTableName, $albumTableName . '.album_id = ' . $this->info('name') . '.resource_id', null)
												->where($albumTableName.'.album_id !=?','');
		}		
		if(isset($params['user_id']))
			$select= $select->where('user_id =?',$params['user_id']);
		return  Zend_Paginator::factory($select);
	}
  public function getRatings($resource_id,$resource_type = 'album')
  {
    $rName = $this->info('name');
    $select = $this->select()
                    ->from($rName)
                    ->where($rName.'.resource_id = ?', $resource_id)
										->where('resource_type =?',$resource_type );
    $row = $this->fetchAll($select);
    return $row;
  }
  
  public function checkRated($resource_id, $user_id,$resource_type = 'album')
  {
    $isRated = $this->select()
										->from($this->info('name'),'rating_id')
                    ->where('resource_type = ?', $resource_type)
                    ->where('user_id = ?', $user_id)
										->where('resource_id =?',$resource_id)
										->limit(1)
										->query()
										->fetchColumn();    
    if ($isRated>0) 
			return true;
		
    return false;
  }
  public function setRating($resource_id, $user_id, $rating,$resource_type = 'album'){
    $rName = $this->info('name');
    $row = $this->select()
                    ->from($rName,new Zend_Db_Expr('COUNT(rating_id) as total_rating'))
                    ->where($rName.'.resource_id = ?', $resource_id)
										->where('resource_type =?',$resource_type)
                    ->where($rName.'.user_id = ?', $user_id)
										->limit(1)->query()->fetchColumn();
    if ($row == 0) {
      // create rating
      Engine_Api::_()->getDbTable('ratings', 'sesalbum')->insert(array(
        'resource_id' => $resource_id,
        'user_id' => $user_id,
        'rating' => $rating,
				'resource_type' =>$resource_type
      ));
    }else{
			 Engine_Api::_()->getDbTable('ratings', 'sesalbum')->update(array(
					'rating' => $rating,
				),array(
				'resource_id = ?' => $resource_id,
				'user_id = ?' => $user_id,
				'resource_type = ?' =>$resource_type
			 ));
		}
  }
  public function ratingCount($resource_id = NULL,$resource_type = 'album'){
    $rName = $this->info('name');
    return $this->select()
                    ->from($rName,new Zend_Db_Expr('COUNT(rating_id) as total_rating'))
										->where('resource_type =?',$resource_type)
                    ->where($rName.'.resource_id = ?', $resource_id)
										->limit(1)->query()->fetchColumn();
  }
	public function getCountUserRate($resource_type = 'album',$resource_id){
		$rName = $this->info('name');
    $rating_sum = $this->select()
            ->from($rName, new Zend_Db_Expr('COUNT(rating_id)'))
            ->where('resource_id = ?', $resource_id)
            ->where('resource_type = ?', $resource_type)
            ->group('resource_id')
            ->group('resource_type')
            ->query()
            ->fetchColumn();
		if(!$rating_sum)
			return 0 ;
    return $rating_sum;
	}
	 public function getSumRating($resource_id, $resource_type) {
    $rName = $this->info('name');
    $rating_sum = $this->select()
            ->from($rName, new Zend_Db_Expr('SUM(rating)'))
            ->where('resource_id = ?', $resource_id)
            ->where('resource_type = ?', $resource_type)
            ->group('resource_id')
            ->group('resource_type')
            ->query()
            ->fetchColumn();
    return $rating_sum;
  }
}