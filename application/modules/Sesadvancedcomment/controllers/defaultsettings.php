<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedcomment
 * @package    Sesadvancedcomment
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: defaultsettings.php 2017-01-19 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

$db = Zend_Db_Table_Abstract::getDefaultAdapter();

$table_exist = $db->query('SHOW TABLES LIKE \'engine4_core_likes\'')->fetch();
if (!empty($table_exist)) {
  $column = $db->query('SHOW COLUMNS FROM engine4_core_likes LIKE \'type\'')->fetch();
  if (empty($column)) {
    $db->query('ALTER TABLE `engine4_core_likes` ADD `type` TINYINT(1) DEFAULT "1";');
  }
}

$table_exist = $db->query('SHOW TABLES LIKE \'engine4_activity_likes\'')->fetch();
if (!empty($table_exist)) {
  $column = $db->query('SHOW COLUMNS FROM engine4_activity_likes LIKE \'type\'')->fetch();
  if (empty($column)) {
    $db->query('ALTER TABLE `engine4_activity_likes` ADD `type` TINYINT(1) DEFAULT "1";');
  }
}

$table_exist = $db->query('SHOW TABLES LIKE \'engine4_core_comments\'')->fetch();
if (!empty($table_exist)) {
  $column = $db->query('SHOW COLUMNS FROM engine4_core_comments LIKE \'file_id\'')->fetch();
  if (empty($column)) {
    $db->query('ALTER TABLE `engine4_core_comments` ADD `file_id` INT(11) NOT NULL DEFAULT "0";');
  }
  $column = $db->query('SHOW COLUMNS FROM engine4_core_comments LIKE \'parent_id\'')->fetch();
  if (empty($column)) {
    $db->query('ALTER TABLE `engine4_core_comments` ADD `parent_id` INT(11) NOT NULL DEFAULT "0";');
  }
  $column = $db->query('SHOW COLUMNS FROM engine4_core_comments LIKE \'emoji_id\'')->fetch();
  if (empty($column)) {
    $db->query('ALTER TABLE `engine4_core_comments` ADD `emoji_id` INT(11) NOT NULL DEFAULT "0";');
  }
  $column = $db->query('SHOW COLUMNS FROM engine4_core_comments LIKE \'reply_count\'')->fetch();
  if (empty($column)) {
    $db->query('ALTER TABLE `engine4_core_comments` ADD `reply_count` INT(11) NOT NULL DEFAULT "0";');
  }
  $column = $db->query('SHOW COLUMNS FROM engine4_core_comments LIKE \'preview\'')->fetch();
  if (empty($column)) {
    $db->query('ALTER TABLE `engine4_core_comments` ADD `preview` TINYINT(1) NOT NULL DEFAULT "0";');
  }
  $column = $db->query('SHOW COLUMNS FROM engine4_core_comments LIKE \'showpreview\'')->fetch();
  if (empty($column)) {
    $db->query('ALTER TABLE `engine4_core_comments` ADD `showpreview` TINYINT(1) NOT NULL DEFAULT "0";');
  }
}

$table_exist = $db->query('SHOW TABLES LIKE \'engine4_activity_comments\'')->fetch();
if (!empty($table_exist)) {
  $column = $db->query('SHOW COLUMNS FROM engine4_activity_comments LIKE \'file_id\'')->fetch();
  if (empty($column)) {
    $db->query('ALTER TABLE `engine4_activity_comments` ADD `file_id` INT(11) NOT NULL DEFAULT "0";');
  }
  $column = $db->query('SHOW COLUMNS FROM engine4_activity_comments LIKE \'parent_id\'')->fetch();
  if (empty($column)) {
    $db->query('ALTER TABLE `engine4_activity_comments` ADD `parent_id` INT(11) NOT NULL DEFAULT "0";');
  }
  $column = $db->query('SHOW COLUMNS FROM engine4_activity_comments LIKE \'emoji_id\'')->fetch();
  if (empty($column)) {
    $db->query('ALTER TABLE `engine4_activity_comments` ADD `emoji_id` INT(11) NOT NULL DEFAULT "0";');
  }
  $column = $db->query('SHOW COLUMNS FROM engine4_activity_comments LIKE \'reply_count\'')->fetch();
  if (empty($column)) {
    $db->query('ALTER TABLE `engine4_activity_comments` ADD `reply_count` INT(11) NOT NULL DEFAULT "0";');
  }
  $column = $db->query('SHOW COLUMNS FROM engine4_activity_comments LIKE \'preview\'')->fetch();
  if (empty($column)) {
    $db->query('ALTER TABLE `engine4_activity_comments` ADD `preview` TINYINT(1) NOT NULL DEFAULT "0";');
  }
  $column = $db->query('SHOW COLUMNS FROM engine4_activity_comments LIKE \'showpreview\'')->fetch();
  if (empty($column)) {
    $db->query('ALTER TABLE `engine4_activity_comments` ADD `showpreview` TINYINT(1) NOT NULL DEFAULT "0";');
  }
}

//Default installation work
//Category Icon for Comments
$select = Engine_Api::_()->getDbTable('emotioncategories', 'sesadvancedcomment')->select()->order('category_id ASC');
$paginator = Engine_Api::_()->getDbTable('emotioncategories', 'sesadvancedcomment')->fetchAll($select);
foreach($paginator as $result) {
	$title = lcfirst($result->title);
  if($title == 'in Love') {
    $title = 'inlove';
  }
  if($title == 'in love') {
    $title = 'inlove';
  }
	$PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sesadvancedcomment' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "category" . DIRECTORY_SEPARATOR;
	if (is_file($PathFile . $title . '.png'))  {
		$pngFile = $PathFile . $title . '.png';
		$photo_params = array(
				'parent_id' => $result->category_id,
				'parent_type' => "sesadvancedcomment_category",
		);
		$photoFile = Engine_Api::_()->storage()->create($pngFile, $photo_params);
		if (!empty($photoFile->file_id)) {
			$db->update('engine4_sesadvancedcomment_emotioncategories', array('file_id' => $photoFile->file_id), array('category_id = ?' => $result->category_id));
		}
	}
}

//Emotions Gallery image for Comments
$emotiongalleriesselect = Engine_Api::_()->getDbTable('emotiongalleries', 'sesadvancedcomment')->select()->order('gallery_id ASC');
$paginator = Engine_Api::_()->getDbTable('emotiongalleries', 'sesadvancedcomment')->fetchAll($emotiongalleriesselect);
foreach($paginator as $result) {
	$title = strtolower($result->title);
  if($title == 'lazy life line') {
    $title = 'lazylifeline';
  } else if($title == 'tom and jerry') {
    $title = 'tomandjerry';
  }
	$PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sesadvancedcomment' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "stickers" . DIRECTORY_SEPARATOR . "galleryimages" . DIRECTORY_SEPARATOR;
	if (is_file($PathFile . $title . '.png'))  {
		$pngFile = $PathFile . $title . '.png';
		$photo_params = array(
				'parent_id' => $result->gallery_id,
				'parent_type' => "sesadvancedcomment_gallery",
		);
		$photoFile = Engine_Api::_()->storage()->create($pngFile, $photo_params);
		if (!empty($photoFile->file_id)) {
			$db->update('engine4_sesadvancedcomment_emotiongalleries', array('file_id' => $photoFile->file_id), array('gallery_id = ?' => $result->gallery_id));
		}
	}
} 

//Upload emotion Files in Gallery
$emotionfilesTable = Engine_Api::_()->getDbtable('emotionfiles', 'sesadvancedcomment');
$emotiongalleriesselect = Engine_Api::_()->getDbTable('emotiongalleries', 'sesadvancedcomment')->select()->order('gallery_id ASC');
$paginator = Engine_Api::_()->getDbTable('emotiongalleries', 'sesadvancedcomment')->fetchAll($emotiongalleriesselect);

foreach($paginator as $result) {
  
  $title = $result->title;
  if($title == 'Meep') {
    $title == 'Meep';
  } elseif($title == 'Minions') {
    $title = 'minions';
  } elseif($title == 'Lazy Life Line') {
    $title = 'LazyLifeLine';
  } elseif($title == 'Waddles') {
    $title = 'waddles';
  } elseif($title == 'Panda') {
    $title = 'panda';
  } elseif($title == 'Tom And Jerry') {
    $title = 'tomandjerry';
  }
  
  $PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sesadvancedcomment' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "stickers" . DIRECTORY_SEPARATOR . $title . DIRECTORY_SEPARATOR;

  for($i= 1;$i<=40;$i++) {
    if (is_file($PathFile . $i . '.png')) {
      $item = $emotionfilesTable->createRow();
      $values['gallery_id'] = $result->gallery_id;
      $item->setFromArray($values);
      $item->save();
      $pngFile = $PathFile . $i . '.png';
      $storage = Engine_Api::_()->getItemTable('storage_file');
      $storageObject = $storage->createFile($pngFile, array(
        'parent_id' => $item->getIdentity(),
        'parent_type' => $item->getType(),
        'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
      ));
      // Remove temporary file
      @unlink($file['tmp_name']);
      $item->photo_id = $storageObject->file_id;
      $item->save();
    }
  }
}

$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
("sesadvancedcomment_tagged_people", "sesadvancedcomment", \'{item:$subject} mention you in a {var:$commentLink}.\', 0, "", 1),
("sesadvancedcomment_taggedreply_people", "sesadvancedcomment", \'{item:$subject} mention you in a {var:$commentLink} on comment.\', 0, "", 1);');


$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
("sesadvancedactivity_reacted_love", "sesadvancedactivity", \'{item:$subject} reacted to your {item:$object:$label}.\', 0, "", 1),
("sesadvancedactivity_reacted_haha", "sesadvancedactivity", \'{item:$subject} reacted to your {item:$object:$label}.\', 0, "", 1),
("sesadvancedactivity_reacted_wow", "sesadvancedactivity", \'{item:$subject} reacted to your {item:$object:$label}.\', 0, "", 1),
("sesadvancedactivity_reacted_angry", "sesadvancedactivity", \'{item:$subject} reacted to your {item:$object:$label}.\', 0, "", 1),
("sesadvancedactivity_reacted_sad", "sesadvancedactivity", \'{item:$subject} reacted to your {item:$object:$label}.\', 0, "", 1);');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("sesadvancedcomment_admin_main_managereactions", "sesadvancedcomment", "Manage Reactions", "", \'{"route":"admin_default","module":"sesadvancedcomment","controller":"manage-reactions","action":"index"}\', "sesadvancedcomment_admin_main", "", 5);');

$db->query('DROP TABLE IF EXISTS `engine4_sesadvancedcomment_reactions`;');
$db->query('CREATE TABLE IF NOT EXISTS `engine4_sesadvancedcomment_reactions` (
  `reaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR( 255 ) NOT NULL,
  `file_id` int(11) NOT NULL DEFAULT "0",
  `enabled` TINYINT(1) NOT NULL DEFAULT "1",
  PRIMARY KEY (`reaction_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;');

$db->query('INSERT IGNORE INTO `engine4_sesadvancedcomment_reactions` (`reaction_id`, `title`, `enabled`, `file_id`) VALUES
(1, "Like", 1, 0),
(2, "Love", 1, 0),
(3, "Haha", 1, 0),
(4, "Wow", 1, 0),
(5, "Angry", 1, 0),
(6, "Sad", 1, 0);');

//Upload Reactions
$reactionsTable = Engine_Api::_()->getDbTable('reactions', 'sesadvancedcomment');
$emotiongalleriesselect = $reactionsTable->select()->order('reaction_id ASC');
$paginator = $reactionsTable->fetchAll($emotiongalleriesselect);

foreach($paginator as $result) {
  
  $title = $result->title;
  if($title == 'Like') {
    $title = 'icon-like';
  } elseif($title == 'Love') {
    $title = 'icon-love';
  } elseif($title == 'Sad') {
    $title = 'icon-sad';
  } elseif($title == 'Wow') {
    $title = 'icon-wow';
  } elseif($title == 'Haha') {
    $title = 'icon-haha';
  } elseif($title == 'Angry') {
    $title = 'icon-angery';
  }

  $PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sesadvancedcomment' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR;

	if (is_file($PathFile . $title . '.png'))  {
		$pngFile = $PathFile . $title . '.png';
		$photo_params = array(
				'parent_id' => $result->reaction_id,
				'parent_type' => "sesadvancedcomment_reaction",
		);
		$photoFile = Engine_Api::_()->storage()->create($pngFile, $photo_params);
		if (!empty($photoFile->file_id)) {
			$db->update('engine4_sesadvancedcomment_reactions', array('file_id' => $photoFile->file_id), array('reaction_id = ?' => $result->reaction_id));
		}
	}
}
Engine_Api::_()->getApi('settings', 'core')->setSetting('sesadvancedcomment.managereactions', 1);