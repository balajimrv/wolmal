<?php

class Zephyrtheme_AdminLandingpageController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('zephyr_admin_main', array(), 'zephyr_admin_main_landing');

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
    $this->view->form = $form = new Zephyrtheme_Form_Admin_Landingpage();
	$form->home_ico_one->addMultiOptions($image_options);
	$form->home_ico_two->addMultiOptions($image_options);
	$form->home_ico_three->addMultiOptions($image_options);
	$form->home_ico_four->addMultiOptions($image_options);
	$form->home_commimg->addMultiOptions($image_options);
	$form->home_commbgimg->addMultiOptions($image_options);
	$form->feat1_img->addMultiOptions($image_options);
	$form->feat2_img->addMultiOptions($image_options);
	$form->feat3_img->addMultiOptions($image_options);
	$form->feat4_img->addMultiOptions($image_options);
	$form->feat5_img->addMultiOptions($image_options);
	$form->feat6_img->addMultiOptions($image_options);
	$form->homebottom_img->addMultiOptions($image_options);

    // Slider items
    $slider_items_table = Engine_Api::_()->getDbtable('sliderItems', 'zephyrtheme');
    $slider_items = $slider_items_table->fetchAll($slider_items_table->select());
    $url_helper = $this->_helper->url;

    // START - TAB 1 - "slideshow" widget
    $form->addElement('Dummy', 'slider_options_text', array(
      'content' => '<p class="form-description"></p>
                <a href="' . $url_helper->simple('add-slide', 'index', 'zephyrtheme') .
                 '" class="buttonlink admin_menus_additem smoothbox">Add Item</a>',
      'decorators' => array('ViewHelper'),
      'ignore' => true,
      'order' => 3
    ));
	
	$form->addElement('Text', 'home_slider_height', array(
      'label' => '',
      'description' => 'Slider Height. e.g.: 550px',
      'value' => '550px',
    ));
	
	$form->addElement('Text', 'home_slider_time', array(
      'label' => '',
      'description' => 'Seconds between slides. e.g.: 5',
      'value' => '5',
    ));


    $slider_items_group = array('slider_options_text', 'home_slider_height', 'home_slider_time');
    $index = 0;

    foreach ($slider_items as $item)
    {
        ++$index;
        $form->addElement('Dummy', 'no_' . $item->id, array(
            'content' => '<td class="number"><h3>Slide #' . $index . '</h3></td>',
            'decorators' => array(
                'ViewHelper',
                array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'openOnly' => true, 'class' => 'item_' . $item->id))
            ),
            'ignore' => true
        ));
        $slider_items_group[] = 'no_' . $item->id;

        $background_src = !empty($item->image_path) ? $item->image_path : 'http://placehold.it/1x1';
        $form->addElement('Dummy', 'img_' . $item->id, array(
            'content' => '<img src="' . $background_src . '" alt="" style="width: 100%; height: auto;">',
            'decorators' => array(
                'ViewHelper',
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'image', 'style' => 'overflow: hidden; height: 100px;'))
            ),
            'ignore' => true
        ));
        $slider_items_group[] = 'img_' . $item->id;

        $form->addElement('Dummy', 'actions_' . $item->id, array(
            'content' => '<td><a href="' . $url_helper->simple('edit-slide', 'index', 'zephyrtheme', array('id' => $item->id)) .
                         '" class="smoothbox">' . $this->view->translate('edit') . '</a> |
                            <a href="' . $url_helper->simple('delete-slide', 'index', 'zephyrtheme', array('id' => $item->id)) .
                        '" class="smoothbox">' . $this->view->translate('delete') . '</a></td>',
            'decorators' => array(
                'ViewHelper',
                array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'closeOnly' => true))
            ),
            'ignore' => true
        ));
        $slider_items_group[] = 'actions_' . $item->id;
    }

    $form->addDisplayGroup($slider_items_group, 'slider_options_table', array(
      'decorators' => array(
          'FormElements',
          array(array('row' => 'HtmlTag'), array('tag' => 'tbody')),
          array(array('row' => 'HtmlTag'), array('tag' => 'table', 'class' => 'slider_admin_table', 'id' => 'slider_items')),
          array('HtmlTag', array( 'tag' => 'div', 'id' => 'tab1'))
      ),
      'order' => $form->slider_options_text->getOrder()
    ));

    // Populate form
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $form->populate($settings->getFlatSetting('zephyr', array()));

    if (!$this->getRequest()->isPost()) return false;
    if (!$form->isValid($this->getRequest()->getPost())) return false;

    // Process form
    $values = $form->getValues();

    // Save Zephyr settings
    $settings->zephyr = $values;
	$v_constants = array(
	    'home_style' => $values['home_style']
    );
	
    // Save as constants
    $zephyr_api = Engine_Api::_()->getApi('theme', 'zephyrtheme');
    $zephyr_api->setOptions($v_constants);
	
    $form->addNotice('Your changes have been saved.');
  }
}