
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

INSERT IGNORE INTO `engine4_sitemobile_menus` (`id`, `name`, `type`, `title`, `order`) VALUES (NULL, 'sitestorereview_profile', 'standard', 'Store Review Profile Options Menu', '999');

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitemobile_menuitems`
--

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES 
('sitestorereview_edit', 'sitestorereview', 'Edit Review', 'Sitestorereview_Plugin_Menus', '', 'sitestorereview_profile', NULL, '0', '1', '1', '1'),
('sitestorereview_delete', 'sitestorereview', 'Delete Review', 'Sitestorereview_Plugin_Menus', '', 'sitestorereview_profile', NULL, '0', '2', '1', '1'),
('sitestorereview_report', 'sitestorereview', 'Report', 'Sitestorereview_Plugin_Menus', '', 'sitestorereview_profile', NULL, '0', '3', '1', '1');

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_sitemobile_searchform` (`name`, `class`, `search_filed_name`, `params`, `script_render_file`, `action`) VALUES
('sitestorereview_index_browse', 'Sitestorereview_Form_Searchwidget', 'search_review', '', '', '');

INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES
('sitestorereview_index_view', 'sitestorereview_profile', 'sitestorereview_review');