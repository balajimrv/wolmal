ALTER TABLE `engine4_album_albums` ADD `is_locked` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_album_albums` ADD `password` VARCHAR(255) NULL;
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'album' as `type`,
    'locked' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'album' as `type`,
    'sesalbum_locked' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');