<?php

class Activityrewards_Api_Core extends Core_Api_Abstract
{

  public function getItemsPaginator($params = array(), $customParams = null)
  {
    $paginator = Zend_Paginator::factory($this->getItemsSelect($params, $customParams));
    if( !empty($params['page']) )
    {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if( !empty($params['limit']) )
    {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }
  
  public function getItemsSelect($params = array(), $customParams = null)
  {
    
    $viewer = Engine_Api::_()->user()->getViewer();
    
    // $params['type'] must present
    $table =  Engine_Api::_()->getDbtable($params['type'], 'activityrewards');
    $typeTable =  Engine_Api::_()->getDbtable($params['type'] . 'type', 'activityrewards');
    $typeTableName = $typeTable->info('name');
    
    $rName = $table->info('name');

    $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
    $tmName = $tmTable->info('name');


    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($rName)
      ->joinLeft($typeTableName, "$rName.userpoint{$params['type']}_type = $typeTableName.userpoint{$params['type']}type_type","*")
      ->where("userpoint{$params['type']}_type >= 100")
      ->order( !empty($params['orderby']) ? $rName.'.'.$params['orderby'].' DESC' : $rName.'.userpoint' . $params['type'] . '_date DESC' );


    // if admin - no per-level filtering, no enabled/disabled filtering
    if(!Engine_Api::_()->getApi('core', 'authorization')->isAllowed('admin', null, 'view')) {
      $select->where("FIND_IN_SET({$viewer->level_id},userpoint{$params['type']}_levels) OR userpoint{$params['type']}_levels = '' OR ISNULL(userpoint{$params['type']}_levels)");
      $select->where("userpoint{$params['type']}_enabled = 1");
    }

    if( !empty($params['tag']) )
    {
      $select
        ->setIntegrityCheck(false)
        ->joinLeft($tmName, "$tmName.resource_id = $rName.userpoint{$params['type']}_id")
        ->where($tmName.'.resource_type = ?', 'activityrewards_'.$params['type'])
        ->where($tmName.'.tag_id = ?', $params['tag']);
    }


    // Could we use the search indexer for this?
    if( !empty($params['search']) )
    {
      $select->where($rName.".userpoint{$params['type']}_title LIKE ? OR ".$rName.".userpoint{$params['type']}_body LIKE ?", '%'.$params['search'].'%');
    }

    if( !empty($params['start_date']) )
    {
      $select->where($rName.".userpoint{$params['type']}_date > ?", date('Y-m-d', $params['start_date']));
    }

    if( !empty($params['end_date']) )
    {
      $select->where($rName.".userpoint{$params['type']}_date < ?", date('Y-m-d', $params['end_date']));
    }
    
    return $select;
  }


}