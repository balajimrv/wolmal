
/**
 * SocialEngine - SocialEngineMods
 *
 */

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('semods', 'SocialEngineMods Library', 'SocialEngineMods Library', '4.0.0', 1, 'extra');

INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`) VALUES
('Statistics', 'semods', 'Semods_Plugin_Task_Statistics', 43200);
