<?php
/**
 * Pixythemes
 *
 * @category   Application_Extensions
 * @package    ZephyrTheme
 * @copyright  Pixythemes
 * @license    http://www.pixythemes.com/
 * @author     Pixythemes
 */

class Zephyrtheme_Installer
	extends Engine_Package_Installer_Module
{
	protected $_settings;	
	protected $_defaultSettings;
	protected $_constants;
	protected $_constantsFile;
	
	protected $_db;
	protected $_themeName;
	
	public function onPreinstall()
	{
		$this->_db = $this->getDb();
		
		// Load current Settings from DB
		$rows = $this->_db->select()
		  ->from('engine4_core_settings', array('name', 'value'))
		  ->query()
		  ->fetchAll(Zend_Db::FETCH_NUM);
		  
		$data = array();
		foreach ($rows as $row)
			$this->_expandArray(explode('.', $row[0]), $row[1], $data);
		
		$this->_settings = $data;
		
		// Load Constants
		$this->_constantsFile = APPLICATION_PATH . '/application/settings/constants.xml';
		
		if (file_exists($this->_constantsFile))
            $this->_constants = simplexml_load_file($this->_constantsFile);
        else
            $this->_constants = simplexml_load_string('<root></root>');
			
		parent::onPreInstall();
	}
	
	public function onInstall()
	{
		$this->_themeName = 'zephyr';
		$this->_defaultSettings = array(
			// GENERAL Tab
			'content_style' => '1',
			'newsfeed_style' => '1',
			'responsive_layout' => 'true',
			'footer_gototop' => 1,
			'footer_socialicons' => 1,
			'footer_facebook' => 'https://www.facebook.com/',
			'footer_twitter' => 'https://www.twitter.com/',
			// LANDING PAGE Tab
			'home_style' => '1',
			'home_slider_height' => '550px',
			'home_slider_time' => '5',
			'home_icos' => 3,
			'home_icosimgs' => 0,
			'home_commimgshow' => 1,
			'home_commh1' => 1,
			'home_commh2' => 1,
			'homebottom_h1' => 1,
			'homebottom_h2' => 1,
			'homebottom_button' => 1,
			// HEADER Tab
			'header_layout' => '1',
			'header_style' => '2',
			'header_fixed' => 1,
			'header_mainmenu' => 1,
			'mainmenu_dropdown' => '5',
			'header_iconsshow' => array(0,1),
			'header_loginbutton' => 0,
			'header_signupbutton' => 1,
			'header_iconssize' => '16',
			'header_vertical_totalWidth' => '1330px',
			// FOOTER Tab
			'footer_logged' => 0,
			'footer_notlogged' => 1,
			'footer_style' => '2',
			// STYLING Tab
			'color_scheme' => 'color_one',
			'body_bgcolor' => '#E8EBF2',
			'font_color' => '#333333',
			'font_colorlight' => '#777777',
			'link_color' => '#328dc6',
			'link_colorhover' => '#555555',
			'input_bgcolor' => '#F7F8FA',
			'content_bgcolor' => '#FFFFFF',
			'content_bordercolor' => '#D2DBE8',
			'button_bgcolor' => '#328dc6',
			'button_color' => '#FFFFFF',
			'headline_color' => '#333333',
			'comment_bgcolor' => '#F7F8FA',
			'header_bgcolor' => '#FFFFFF',
			'header_bordercolor' => '#29ABE2',
			'headermenu_bgcolor' => '#f7f7f7',
			'headermenu_border' => 'transparent',
			'headermenu_color' => '#555555',
			'headermenu_colorhover' => '#111111',
			'headermenu_bghover' => '#F2F2F2',
			'header_searchcolor' => '#BBBBBB',
			'footer_bgcolor' => '#2e2e2e',
			'footer_color' => '#888888',
			'footer_linkcolor' => '#328dc6',
			'footer_bordercolor' => 'transparent',
			'body_img' => '',
			'body_imgrepeat' => 'repeat',
			'body_imgfixed' => 'fixed',
			'body_imgcover' => 1,
			'width_main' => '1100px',
			'width_colleft' => '220px',
			'width_colright' => '220px',
			'header_img' => '',
			'header_imgrepeat' => 'no-repeat',
			'header_imgfixed' => 'fixed',
			'header_imgcover' => 'cover',
			'logo_margin' => '2px',
			'header_height' => '52px',
			'header_border' => '0px',
			'header_icons' => '1',
			'footer_img' => '',
			'footer_imgrepeat' => 'no-repeat',
			'footer_imgfixed' => 'fixed',
			'footer_imgcover' => 'cover',
			'footer_border' => '0px',
			'custom_css' => '',

			// TYPOGRAPHY Tab
			'main_font' => 'Arial, Helvetica, sans-serif',
			'headings_font' => '"Trebuchet MS", Helvetica, sans-serif',
			'font_size' => '13px',
			'text_fontweight' => 400,
			'headings_fontweight' => 400,
			'use_googlefonts' => 'true',
			'google_font' => 'Open Sans',
			'google_headingsfont' => 'Raleway',
			'google_fontfix' => '"Open Sans"',
			'google_headingsfontfix' => '"Raleway"'
		);
		
		if (method_exists($this, '_addDefaultSettings'))
			$this->_addDefaultSettings();
		
		$this->_message('Default theme settings applied.');
		
		$this->_setTheme($this->_themeName);		
		
		parent::onInstall();
	}
	
	protected function _addDefaultSettings()
	{
		$this->_setSettings($this->_themeName, $this->_defaultSettings);
		$this->_setConstants($this->_defaultSettings);

        $column_exist = $this->_db->query("SHOW COLUMNS FROM engine4_messages_messages LIKE 'read'")->fetch();
        if (empty($column_exist)) {
            $this->_db->query("ALTER TABLE `engine4_messages_messages` ADD `read` TINYINT( 1 ) NOT NULL DEFAULT '0'");
        }

        $column_exist = $this->_db->query("SHOW COLUMNS FROM engine4_activity_notifications LIKE 'view_notification'")->fetch();
        if (empty($column_exist)) {
            $this->_db->query("ALTER TABLE `engine4_activity_notifications` ADD `view_notification` TINYINT( 1 ) NOT NULL AFTER `date`");
        }

        $this->_db->update('engine4_core_menuitems', array(
            'plugin' => 'Zephyrtheme_Plugin_Menus'
        ), array(
            'name = ?' => 'core_main_home'
        ));

        $this->_addSliderSettings();
	}

	protected function _addSliderSettings()
    {
        // Slider Items table
        $table_exist = $this->_db->query("SHOW TABLES LIKE 'engine4_zephyrtheme_slideritems'")->fetch();

        if (empty($table_exist))
        {
            $this->_db->query("CREATE TABLE IF NOT EXISTS `engine4_zephyrtheme_slideritems` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `image_path` varchar(255) DEFAULT NULL,
              `illustration_path` varchar(255) DEFAULT NULL,
			  `video` text,
              `title` text,
              `subtitle` text,
			  `color` text,
              `text_align` varchar(6) DEFAULT NULL,
			  `show_item` text,
			  `colonetop` varchar(255) DEFAULT NULL,
			  `coltwotop` varchar(255) DEFAULT NULL,
              PRIMARY KEY (`id`)
            )");
        }

        $home_path = APPLICATION_PATH . "/application/modules/Zephyrtheme/externals/images/slider/background.jpg";
        $home_illu = APPLICATION_PATH . "/application/modules/Zephyrtheme/externals/images/slider/illustration.png";
        $admin_path = APPLICATION_PATH . "/public/admin/background.jpg";
        $admin_illu = APPLICATION_PATH . "/public/admin/illustration.png";

        if (is_dir(APPLICATION_PATH . "/public/admin"))
        {
            @copy($home_path, $admin_path);
            @copy($home_illu, $admin_illu);

            $this->_db->insert('engine4_zephyrtheme_slideritems', array(
                'image_path' => 'public/admin/background.jpg',
                'illustration_path' => 'public/admin/illustration.png',
				'video' => '',
                'title' => 'Get engaged in the most active Social Shopping & eCommerce Medium',
                'subtitle' => 'Find new products, Watch out what others says about it, Buy perfect product at affordable price . Everything in the same place!',
				'color' => '#FFFFFF',
                'text_align' => 'right',
				'show_item' => json_encode(array('image', 'firsthl', 'secondhl', 'loginbtn', 'signupbtn')),
				'colonetop' => '100px',
				'coltwotop' => '100px'
            ));
        }
    }
	
	protected function _setSettings($key, $value)
	{
		$key = $this->_normalizeMagicProperty($key);
		
		if (is_array($value))
		{
			foreach ($value as $k => $v)
			{
				$this->_setSettings($key . '.' . $k, $v);
			}
		} else {
			$path = explode('.', $key);
			$final = array_pop($path);
			
			$current =& $this->_settings;
			if (!empty($path))
			{
				foreach ($path as $pathElement)
				{
					if (!isset($current[$pathElement]) || !is_array($current[$pathElement]))
						$current[$pathElement] = array();
						
					$current =& $current[$pathElement];
				}	
			}
			
			// Delete
			/*if (isset($current[$final]) && null === $value)
			{
				$this->_db->delete('engine4_core_settings', array(
				  'name = ?' => $key,
				));
				$this->_db->delete('engine4_core_settings', array(
				  'name LIKE ?' => $key . '.%',
				));
				unset($current[$final]);
			}
			// Update
			elseif (isset($current[$final])) {
				if ($current[$final] !== $value)
				{
				  $this->_db->update('engine4_core_settings', array(
					'value' => $value,
				  ), array(
					'name = ?' => $key,
				  ));
				}
				
				// Reselect?
				$current[$final] = $value;
			}
			// Insert
			else {*/
			if (!isset($current[$final])) {
				$this->_db->insert('engine4_core_settings', array(
				  'name' => $key,
				  'value' => $value,
				));
				
				// Reselect?
				$current[$final] = $value;
			}
		}
	}
	
	protected function _setConstants($options)
	{
		if (is_array($options))
        {
            foreach ($options as $name => $value)
            {
                $nodes = $this->_constants->xpath('/root/constant[name="' . $this->_themeName . '_' . $name . '"]');
 				$name_node = $nodes[0];
				
				// DELETE
				/*if ($name_node != null && empty($name_node->value))
				{
					unset($name_node->{0});
				} 
				// UPDATE
				elseif ($name_node != null) {
					$name_node->value = $value;
				} 
				// INSERT
				else {*/
				if ($name_node == null) {
					$root = $this->_constants->addChild('constant');
                    $root->addChild('name', $this->_themeName . '_' . $name);
                    $root->addChild('value', $value);
				}
            }

            $is_saved = $this->_constants->asXML($this->_constantsFile);

            if ($is_saved)
				$this->_clearScaffoldCache();
        } else {
			$this->_message(__FUNCTION__ . ' requires argument as array.');
        }
	}
	
	protected function _clearScaffoldCache()
	{
		try {
		  Engine_Package_Utilities::fsRmdirRecursive(APPLICATION_PATH . '/temporary/scaffold', false);
		} catch( Exception $e ) {}
	}
	
	protected function _setTheme($name)
	{
		$theme = $this->_db->select()
					->from('engine4_core_themes')
					->where('name = ?', $name)
					->limit(1)
					->query()
					->fetch();
		
		// Deactivate current theme
		$this->_db->update('engine4_core_themes', array(
		  'active' => 0,
		), array(
		  'active = ?' => 1,
		));
		
		// Add theme
		if (!$theme)
		{
			$this->_db->insert('engine4_core_themes', array(
				'name' => 'zephyr',
				'title' => 'Zephyr Theme',
				'description' => '',
				'active' => 1
			));
			
			$this->_message('Theme <i>'. $name .'</i> inserted.');
		}
		// Activate theme
		else {
			$this->_db->update('engine4_core_themes', array(
				'active' => 1,
			), array(
				'name = ?' => $name
			));
		}
		
		$this->_message('Theme <i>'. $name .'</i> activated.');
		
		$this->_message('Please read the theme\'s <a href="http://pixythemes.com/instructions/zephyr-theme-instructions/" target="_blank">instructions</a> to arrange the layout for this theme.');
		
		// Clear Scaffold Cache
		$this->_clearScaffoldCache();
	}
	
	protected function _expandArray(array $path, $value, array &$array)
	{
		$current =& $array;
		foreach( $path as $pathElement )
		{
			if( !isset($current[$pathElement]) || !is_array($current[$pathElement]) ) {
				$current[$pathElement] = array();
			}
			
			$current =& $current[$pathElement];
		}
		$current = $value;
	}
	
	protected function _normalizeMagicProperty($key)
	{
		return str_replace('_', '.', $key);
	}
}