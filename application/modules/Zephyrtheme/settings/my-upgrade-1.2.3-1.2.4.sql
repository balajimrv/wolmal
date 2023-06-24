UPDATE `engine4_core_modules` SET `version` = '1.2.4'  WHERE `name` = 'zephyrtheme';

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes`
 (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
 ('profile_cover_update', 'zephyrtheme', '{item:$subject} has changed the profile cover.', 1, 5, 1, 1, 1, 1);
