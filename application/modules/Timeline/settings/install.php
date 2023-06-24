<?php

class Timeline_Installer extends Engine_Package_Installer_Module
{
    public function onInstall() {
       parent::onInstall();
       $db     = $this->getDb();
       $select = new Zend_Db_Select($db);
       $select
          ->from('engine4_core_pages')
          ->where('name = ?', 'timeline_profile_index')
          ->limit(1);
       $info = $select->query()->fetch();
       if ( empty($info)) {
            $db->insert('engine4_core_pages', array('name' => 'timeline_profile_index',
                                                    'displayname' => 'Member Timeline Profile',
                                                    'title' => 'Member Timeline Profile',
                                                    'description' => "This is a member's profile with timeline layout.",
                                                    'custom' => 0,
                                                    'provides' => 'subject=user'));
            $page_id = $db->lastInsertId('engine4_core_pages');

            // Insert main
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'order' => 1
              ));
            $main_id = $db->lastInsertId('engine4_core_content');

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1
              ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            // Add widget 'Cover'
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                                                      'type' => 'widget',
                                                      'name' => 'timeline.cover',
                                                      'parent_content_id' => $middle_id,
                                                      'order' => 1
                                                      ));
            // Insert tabs
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'timeline.container-tabs',
                'page_id' => $page_id,
                'parent_content_id' => $middle_id,
                'params' => '{"max":"6","title":"","tabs_location":"horizontal","nomobile":"0","name":"timeline.container-tabs"}',
                'order' => 2
              ));
            $tabs_id = $db->lastInsertId('engine4_core_content');

            // Add widget 'Cover'
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                                                      'type' => 'widget',
                                                      'name' => 'timeline.feed',
                                                      'parent_content_id' => $tabs_id,
                                                      'order' => 1,
                                                      'params' => '{"title":"What\'s New"}'
                                                      ));

            $select_user_profile = new Zend_Db_Select($db);
            $select_user_profile->from('engine4_core_pages')
                                ->where('name = ?', 'user_profile_index')
                                ->limit(1);
            $info_user_profile_page = $select_user_profile->query()->fetch();

            $select_user_tab = new Zend_Db_Select($db);
            $select_user_tab->from('engine4_core_content')
                            ->where('page_id = ?', $info_user_profile_page['page_id'])
                            ->where('`name` = "core.container-tabs"')
                            ->limit(1);
            $tab_info = $select_user_tab->query()->fetch();

            $select_user_content = new Zend_Db_Select($db);
            $select_user_content->from('engine4_core_content')
                                ->where('page_id = ?', $info_user_profile_page['page_id'])
                                ->where('`parent_content_id` = ?', $tab_info['content_id'])
                                ->order('order ASC');
            $content_info = $select_user_content->query()->fetchAll();

            $order = 2;
            foreach ($content_info as $value) {
                if ($value['name'] == 'activity.feed') {
                    continue;
                }

                $db->insert('engine4_core_content', array('page_id' => $page_id,
                                                          'type' => $value['type'],
                                                          'name' => $value['name'],
                                                          'parent_content_id' => $tabs_id,
                                                          'order' => $order++,
                                                          'params' => $value['params'],
                                                          'attribs' => $value['attribs']));
            }

       }

       $iconsDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'timeline' . DIRECTORY_SEPARATOR . 'tab_icons';
       if( is_dir($iconsDir) ) {
           $icons = scandir($iconsDir);
           foreach ($icons as $icon) {
               if ($icon != "." && $icon != "..") {
                   if (preg_match('/^tab_(\d+)\.png/i', $icon, $matches)) {
                        $tab_id = (int)$matches[1];
                        $select_tab_info = new Zend_Db_Select($db);
                        $select_tab_info->from('engine4_core_content')
                                        ->where('content_id = ?', $tab_id)
                                        ->limit(1);
                        $tab_icon_info = $select_tab_info->query()->fetch();
                        if (!empty ($tab_icon_info)) {
                            $new_icon_name = $iconsDir . "/" . 'tab_' . $tab_icon_info['name'] . '.png';
                            rename($iconsDir."/".$icon, $new_icon_name);
                        }
                   }
               }
           }
           reset($icons);
       }
   }

}
?>