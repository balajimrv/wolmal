
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitemobile_menus`
--

INSERT IGNORE INTO `engine4_sitemobile_menus` (`id`, `name`, `type`, `title`, `order`) VALUES (NULL, 'sitestorevideo_profile', 'standard', 'Store Video Profile Options Menu', '999');

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitemobile_menuitems`
--

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES 
('sitestorevideo_add', 'sitestorevideo', 'Add Video', 'Sitestorevideo_Plugin_Menus', '', 'sitestorevideo_profile', NULL, '0', '1', '1', '1'),
('sitestorevideo_edit', 'sitestorevideo', 'Edit Video', 'Sitestorevideo_Plugin_Menus', '', 'sitestorevideo_profile', NULL, '0', '2', '1', '1'),
('sitestorevideo_delete', 'sitestorevideo', 'Delete Video', 'Sitestorevideo_Plugin_Menus', '', 'sitestorevideo_profile', NULL, '0', '3', '1', '1');

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_sitemobile_searchform` (`name`, `class`, `search_filed_name`, `params`, `script_render_file`, `action`) VALUES
('sitestorevideo_index_browse', 'Sitestorevideo_Form_Searchwidget', 'search_video', '', '', '');

INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES
('sitestorevideo_index_view', 'sitestorevideo_profile', 'sitestorevideo_video');