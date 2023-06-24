<?php

$db = Engine_Db_Table::getDefaultAdapter();
$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("siteverify_admin_main_level", "siteverify", "Member Level Settings", "", \'{"route":"admin_default","module":"siteverify","controller":"level"}\', "siteverify_admin_main", "", 2),
("siteverify_admin_main_manage", "siteverify", "Manage Verifications", "", \'{"route":"admin_default","module":"siteverify","controller":"manage"}\', "siteverify_admin_main", "", 3),
("siteverify_admin_main_approve", "siteverify", "Approve Verifications", "", \'{"route":"admin_default","module":"siteverify","controller":"approve"}\', "siteverify_admin_main", "", 4);');

//Member profile page
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_pages')
        ->where('name = ?', 'user_profile_index')
        ->limit(1);
$page_id = $select->query()->fetchObject()->page_id;
if (!empty($page_id)) {
  $select = new Zend_Db_Select($db);
  $select
          ->from('engine4_core_content')
          ->where('page_id = ?', $page_id)
          ->where('type = ?', 'container')
          ->where('name = ?', 'main')
          ->limit(1);
  $container_id = $select->query()->fetchObject()->content_id;
  if (!empty($container_id)) {
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_content')
            ->where('parent_content_id = ?', $container_id)
            ->where('type = ?', 'container')
            ->where('name = ?', 'middle')
            ->limit(1);
    $middle_id = $select->query()->fetchObject()->content_id;
    if (!empty($middle_id)) {
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_content')
              ->where('parent_content_id = ?', $middle_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'siteverify.verify-button');
      $info = $select->query()->fetch();
      if (empty($info)) {
        // tab on profile
        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'siteverify.verify-button',
            'parent_content_id' => $middle_id,
            'order' => 1,
            'params' => '{"title":""}',
        ));
      }
    }
  }
}



  $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemember');
  if(!empty($isModEnabled)) {
    //START UPDATE BROWSE MEMBER PAGE WIDGET.
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_content', array('content_id', 'params'))
            ->where('type =?', 'widget')
            ->where('name =?', 'sitemember.browse-members-sitemember');
    $fetch = $select->query()->fetchAll();
    foreach ($fetch as $modArray) {
        $params = Zend_Json::decode($modArray['params']);
        if(is_array($params)) {
          if(!in_array("verifyCount", $params['memberInfo']))
            $params['memberInfo'][] = "verifyCount";
          
          if(!in_array("verifyLabel", $params['memberInfo']))
            $params['memberInfo'][] = "verifyLabel";
          
          $paramss = Zend_Json::encode($params);
          $tableObject = Engine_Api::_()->getDbtable('content', 'core');
          $tableObject->update(array("params" => $paramss), array("content_id =?" => $modArray['content_id']));
        }
    }
    //END UPDATE BROWSE MEMBER PAGE WIDGET.
  }
  
  
  
  $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteusercoverphoto');
  if(!empty($isModEnabled)) {
    //START UPDATE MEMBER PROFILE COVER PHOTO.
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_content', array('content_id', 'params'))
            ->where('type =?', 'widget')
            ->where('name =?', 'siteusercoverphoto.user-cover-photo');
    $fetch = $select->query()->fetchAll();
    foreach ($fetch as $modArray) {
        $params = Zend_Json::decode($modArray['params']);
        if(is_array($params)) {
          if(!in_array("verify", $params['memberInfo']))
            $params['showContent'][] = "verify";
          
          $paramss = Zend_Json::encode($params);
          $tableObject = Engine_Api::_()->getDbtable('content', 'core');
          $tableObject->update(array("params" => $paramss), array("content_id =?" => $modArray['content_id']));          
        }
    }
    //END UPDATE MEMBER PROFILE COVER PHOTO
  }