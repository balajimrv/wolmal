<?php

class Zephyrtheme_AdminHeaderController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    // Navigation first
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('zephyr_admin_main', array(), 'zephyr_admin_main_header');

    // Get available files
    $image_options = array('' => 'No image / Default');
    $imageExtensions = array('gif', 'jpg', 'jpeg', 'png');
    $it = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
    foreach( $it as $file ) {
      if( $file->isDot() || !$file->isFile() ) continue;
      $basename = basename($file->getFilename());
      if( !($pos = strrpos($basename, '.')) ) continue;
      $ext = strtolower(ltrim(substr($basename, $pos), '.'));
      if( !in_array($ext, $imageExtensions) ) continue;
      $image_options['public/admin/' . $basename] = $basename;
    }

    // Form
    $this->view->form = $form = new Zephyrtheme_Form_Admin_Header();
    $form->header_logo->addMultiOptions($image_options);

    // Main Menu items
    $main_menu_table = Engine_Api::_()->getDbtable('menuItems', 'core');
    $main_menu_select = $main_menu_table->select()
                            ->where('menu = ?', 'core_main')
                            ->where('enabled = ?', 1)
                            ->order('order ASC');
    $main_menu_items = $main_menu_table->fetchAll($main_menu_select);
    $new_items_name = array();
    $after_position = $form->header_iconsdesc->getOrder();

    // Populate Main Menu icons
    foreach ($main_menu_items as $key => $item)
    {
        $after_position++;
        $new_items_name[] = $item->name;
        $form->addElement('Select', $item->name, array(
            'label' => $item->label,
            'value' => $item->params['icon'],
            'class' => 'menu-item-icon',
            'decorators' => array(
                array('ViewScript', array(
                    'viewScript' => '_tableRow.tpl',
                    'placement'  => false
                ))
            ),
            'multiOptions' => $image_options,
            'order' => $after_position
        ));
    }

    $form->addDisplayGroup($new_items_name, 'table_core_main', array(
      'description' => '<thead><tr><th>Menu Item</th><th>Icon</th><th>Options</th></tr></thead>',
      'decorators' => array(
          array('Description', array('placement' => 'append', 'escape' => false)),
          'FormElements',
          array('HtmlTag', array(
              'tag' => 'table',
              'class' => 'admin_table',
              'style' => 'width:100%'
          ))
      )
    ));

    // Zephyr settings
    $settings_api = Engine_Api::_()->getApi('settings', 'core');

    // Populate Zephyr settings
    $form->populate($settings_api->getFlatSetting('zephyr', array()));

    if (!$this->getRequest()->isPost()) return false;
    if (!$form->isValid($this->getRequest()->getPost())) return false;

    // Process form
    $values = $form->getValues();

    // Save Main Menu icons
    foreach ($main_menu_items as $item)
    {
        if (isset($values[$item->name]))
        {
            $icon_data = array('icon' => $values[$item->name]);
            $item->params = empty($item->params) ? $icon_data : array_merge($item->params, $icon_data);

            $item->save();
        }
    }

    // Save Zephyr settings
    $settings_api->zephyr = $values;
	$width_main = preg_replace('/\D/', '', Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.width_main', false));
	$v_constants = array( 'header_style' => $values['header_style'], 'header_layout' => $values['header_layout'], 'header_vertical_totalWidth' => ($width_main + 260).'px' );
	
    // Save as constants
    $zephyr_api = Engine_Api::_()->getApi('theme', 'zephyrtheme');
    $zephyr_api->setOptions($v_constants);

    $form->addNotice('Your changes have been saved.');
  }
}