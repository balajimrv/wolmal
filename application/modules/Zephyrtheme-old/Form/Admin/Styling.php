<?php
class Zephyrtheme_Form_Admin_Styling extends Engine_Form
{
  public function init()
  {
	// Get available files
	$imageOptions = array('' => 'Default / No image');
	$imageExtensions = array('gif', 'jpg', 'jpeg', 'png');
	$it = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
	foreach( $it as $file ) {
	  if( $file->isDot() || !$file->isFile() ) continue;
	  $basename = basename($file->getFilename());
	  if( !($pos = strrpos($basename, '.')) ) continue;
	  $ext = strtolower(ltrim(substr($basename, $pos), '.'));
	  if( !in_array($ext, $imageExtensions) ) continue;
	  $imageOptions['public/admin/' . $basename] = $basename;
	}
	  
    $this
      ->setTitle('Styling')
      ->setDescription('Style your template. Make it unique!');

    $this->addElement('Radio', 'color_scheme', array(
      'label' => 'Color Schemes',
      'description' => 'When you change the color scheme the color codes entered below will be replaced, and you can change each of them individually if you wish.',
      'multiOptions' => array(
        'color_one' 	=> '<img src="./application/modules/Zephyrtheme/externals/images/colors/color_1.png" alt="Color One"/>',
        'color_two' 	=> '<img src="./application/modules/Zephyrtheme/externals/images/colors/color_2.png" alt="Color Two"/>',
		'color_three' 	=> '<img src="./application/modules/Zephyrtheme/externals/images/colors/color_3.png" alt="Color Three"/>',
		'color_four' 	=> '<img src="./application/modules/Zephyrtheme/externals/images/colors/color_4.png" alt="Color Four"/>',
		'color_five' 	=> '<img src="./application/modules/Zephyrtheme/externals/images/colors/color_5.png" alt="Color Five"/>',
		'color_six' 	=> '<img src="./application/modules/Zephyrtheme/externals/images/colors/color_6.png" alt="Color Six"/>',
		'color_seven' 	=> '<img src="./application/modules/Zephyrtheme/externals/images/colors/color_7.png" alt="Color Seven"/>',
		'color_eight' 	=> '<img src="./application/modules/Zephyrtheme/externals/images/colors/color_8.png" alt="Color Eight"/>',
		'color_nine' 	=> '<img src="./application/modules/Zephyrtheme/externals/images/colors/color_9.png" alt="Color Nine"/>',
		'color_ten' 	=> '<img src="./application/modules/Zephyrtheme/externals/images/colors/color_10.png" alt="Color Ten"/>',
      ),
      'value' => 'color_one',
	  'escape' => false,
    ));

    $this->addElement('Text', 'body_bgcolor', array(
      'label' => 'Background Color',
      'description' => '',
	  'value' => '#E8EBF2',
    ));
	
	$this->addElement('Text', 'font_color', array(
      'label' => 'Text Color',
      'description' => '',
	  'value' => '#333333',
    ));
	
	$this->addElement('Text', 'font_colorlight', array(
      'label' => 'Text Lighter Color',
      'description' => '',
	  'value' => '#777777',
    ));
	
	$this->addElement('Text', 'link_color', array(
      'label' => 'Link Color',
      'description' => '',
	  'value' => '#328dc6',
    ));
	
	$this->addElement('Text', 'link_colorhover', array(
      'label' => 'Link Color Hover',
      'description' => '',
	  'value' => '#555555',
    ));
	
	$this->addElement('Text', 'input_bgcolor', array(
      'label' => 'Input/Select/Textarea Background Color',
      'description' => '',
	  'value' => '#F7F8FA',
    ));
	
	$this->addElement('Text', 'content_bgcolor', array(
      'label' => 'Content Background Color',
      'description' => '',
	  'value' => '#FFFFFF',
    ));
	
	$this->addElement('Text', 'content_bordercolor', array(
      'label' => 'Content Border Color',
      'description' => '',
	  'value' => '#D2DBE8',
    ));
	
	$this->addElement('Text', 'button_bgcolor', array(
      'label' => 'Button Background Color',
      'description' => '',
	  'value' => '#328dc6',
    ));
	
	$this->addElement('Text', 'button_color', array(
      'label' => 'Button Text Color',
      'description' => '',
	  'value' => '#FFFFFF',
    ));
	
	$this->addElement('Text', 'headline_color', array(
      'label' => 'Headline Color',
      'description' => '',
	  'value' => '#333333',
    ));
	
	$this->addElement('Text', 'comment_bgcolor', array(
      'label' => 'Comments Background Color',
      'description' => '',
	  'value' => '#F7F8FA',
    ));
	
	$this->addElement('Text', 'header_bgcolor', array(
      'label' => 'Header Background Color',
      'description' => '',
	  'value' => '#FFFFFF',
    ));
	
	$this->addElement('Text', 'header_bordercolor', array(
      'label' => 'Header Border Color',
      'description' => '',
	  'value' => '#29ABE2',
    ));
	
	$this->addElement('Text', 'headermenu_bgcolor', array(
      'label' => 'Header Main-Menu Background Color',
      'description' => 'Does not apply to all Header Layouts',
	  'value' => '#f7f7f7',
    ));
	
	$this->addElement('Text', 'headermenu_border', array(
      'label' => 'Header Main-Menu Borders Color',
      'description' => '',
	  'value' => 'transparent',
    ));
	
	$this->addElement('Text', 'headermenu_color', array(
      'label' => 'Header Main-Menu Text Color',
      'description' => '',
	  'value' => '#777777',
    ));
	
	$this->addElement('Text', 'headermenu_colorhover', array(
      'label' => 'Header Main-Menu Text Color Hover',
      'description' => '',
	  'value' => '#333333',
    ));
	
	$this->addElement('Text', 'headermenu_bghover', array(
      'label' => 'Header Main-Menu Background Color Hover',
      'description' => '',
	  'value' => '#F2F2F2',
    ));
	
	$this->addElement('Text', 'header_searchcolor', array(
      'label' => 'Header Search Color',
      'description' => '',
	  'value' => '#BBBBBB',
    ));
		
	$this->addElement('Text', 'footer_bgcolor', array(
      'label' => 'Footer Background Color',
      'description' => '',
	  'value' => '#2e2e2e',
    ));
		
	$this->addElement('Text', 'footer_color', array(
      'label' => 'Footer Text Color',
      'description' => '',
	  'value' => '#888888',
    ));
	
	$this->addElement('Text', 'footer_linkcolor', array(
      'label' => 'Footer Link Color',
      'description' => '',
	  'value' => '#328dc6',
    ));
	
	$this->addElement('Text', 'footer_bordercolor', array(
      'label' => 'Footer Border Color',
      'description' => '',
	  'value' => 'transparent',
    ));
	
	// Colors container
	$this->addDisplayGroup(array('body_bgcolor', 'font_color', 'font_colorlight', 'link_color', 'link_colorhover', 'input_bgcolor', 'content_bgcolor', 'content_bordercolor', 'button_bgcolor', 'button_color', 'headline_color', 'comment_bgcolor', 'header_bgcolor', 'header_bordercolor', 'headermenu_bgcolor', 'headermenu_border', 'headermenu_color', 'headermenu_colorhover', 'headermenu_bghover', 'header_searchcolor', 'footer_bgcolor', 'footer_color', 'footer_linkcolor', 'footer_bordercolor'),'colors_group', array('disableLoadDefaultDecorators' => true));
        $colors_group = $this->getDisplayGroup('colors_group');
        $colors_group->setDecorators(array('FormElements', 'Fieldset',
        	array('HtmlTag',array('tag'=>'div','id'=>'colors_group'))
        ));
	
    $this->addElement('Select', 'body_img', array(
      'label' => 'Website Background',
      'description' => 'Background Image<br /><span style="font-weight: bold;">Images are uploaded via <a href="'.Zend_Controller_Front::getInstance()->getBaseUrl().'/admin/files" target="_blank"><span style="font-weight: bold;">File & Media Manager</span></a></span>.<br />After uploading a background image refresh this page to see it in the select box below.',
      'multiOptions' => $imageOptions,

    ));
	$this->getElement('body_img')->getDecorator('Description')->setOption('escape',false);
	
	$this->addElement('Select', 'body_imgrepeat', array(
      'label' => '',
      'description' => 'Background Image Repeat',
      'multiOptions' => array(
        'repeat' => 'repeat = Repeat on all axys',
        'repeat-y' => 'repeat-y = Repeat only vertically',
		'repeat-x' => 'repeat-x = Repeat only horizontally',
		'no-repeat' => 'no-repeat = The image will not be repeated',
      ),
      'value' => 'repeat',
    ));
	
	$this->addElement('Select', 'body_imgfixed', array(
      'label' => '',
      'description' => 'Background Image Fixed',
      'multiOptions' => array(
        'fixed' => 'Yes',
        'inherit' => 'No',
      ),
      'value' => 'fixed',
    ));
	
	$this->addElement('Select', 'body_imgcover', array(
      'label' => '',
      'description' => 'Background Image Cover. Tip: background must be fixed for this to work properly.',
      'multiOptions' => array(
        'cover' => 'Yes',
        'inherit' => 'No',
      ),
      'value' => 'cover',
    ));
	
	$this->addElement('Text', 'width_main', array(
      'label' => 'Width',
      'description' => 'Content Width',
	  'value' => '1100px',
    ));
	
	$this->addElement('Text', 'width_colleft', array(
      'label' => '',
      'description' => 'Column Left Width',
	  'value' => '220px',
    ));
	
	$this->addElement('Text', 'width_colright', array(
      'label' => '',
      'description' => 'Column Right Width',
	  'value' => '220px',
    ));
	
    $this->addElement('Select', 'header_img', array(
      'label' => 'Header Background',
      'description' => 'Background Image<br /><span style="font-weight: bold;">Images are uploaded via <a href="'.Zend_Controller_Front::getInstance()->getBaseUrl().'/admin/files" target="_blank"><span style="font-weight: bold;">File & Media Manager</span></a></span>.<br />After uploading a background image refresh this page to see it in the select box below.',
      'multiOptions' => $imageOptions,
    ));
	$this->getElement('header_img')->getDecorator('Description')->setOption('escape',false);
	
	$this->addElement('Select', 'header_imgrepeat', array(
      'label' => '',
      'description' => 'Background Image Repeat',
      'multiOptions' => array(
        'repeat' => 'repeat = Repeat on all axys',
        'repeat-y' => 'repeat-y = Repeat only vertically',
		'repeat-x' => 'repeat-x = Repeat only horizontally',
		'no-repeat' => 'no-repeat = The image will not be repeated',
      ),
      'value' => 'no-repeat',
    ));
	
	$this->addElement('Select', 'header_imgfixed', array(
      'label' => '',
      'description' => 'Background Image Fixed',
      'multiOptions' => array(
        'fixed' => 'Yes',
        'inherit' => 'No',
      ),
      'value' => 'fixed',
    ));
	
	$this->addElement('Select', 'header_imgcover', array(
      'label' => '',
      'description' => 'Background Image Cover. Tip: background must be fixed for this to work properly.',
      'multiOptions' => array(
        'cover' => 'Yes',
        'inherit' => 'No',
      ),
      'value' => 'cover',
    ));
	
	$this->addElement('Text', 'logo_margin', array(
      'label' => 'Header Logo Margin Top',
      'description' => '',
	  'value' => '2px',
    ));
	
	$this->addElement('Text', 'header_height', array(
      'label' => 'Header Main Elements\' Height',
      'description' => 'You might need to change this to accomodate your logo.',
	  'value' => '52px',
    ));
		
	$this->addElement('Text', 'header_border', array(
      'label' => 'Header Border Thickness',
      'description' => 'In pixels, default is 0px. If you do not wish to have a border you can set this to 0px.',
	  'value' => '0px',
    ));
	
	$this->addElement('Select', 'header_icons', array(
      'label' => 'Header Icons Color',
      'description' => 'Depending on the background color you choose for your Header you might need to change this.',
      'multiOptions' => array(
        '1' => 'Black',
        '2' => 'White',
      ),
      'value' => '1',
    ));

	$this->addElement('Select', 'footer_img', array(
      'label' => 'Footer Background',
      'description' => 'Background Image<br /><span style="font-weight: bold;">Images are uploaded via <a href="'.Zend_Controller_Front::getInstance()->getBaseUrl().'/admin/files" target="_blank"><span style="font-weight: bold;">File & Media Manager</span></a></span>.<br />After uploading a background image refresh this page to see it in the select box below.',
      'multiOptions' => $imageOptions,
    ));
	$this->getElement('footer_img')->getDecorator('Description')->setOption('escape',false);
	
	$this->addElement('Select', 'footer_imgrepeat', array(
      'label' => '',
      'description' => 'Background Image Repeat',
      'multiOptions' => array(
        'repeat' => 'repeat = Repeat on all axys',
        'repeat-y' => 'repeat-y = Repeat only vertically',
		'repeat-x' => 'repeat-x = Repeat only horizontally',
		'no-repeat' => 'no-repeat = The image will not be repeated',
      ),
      'value' => 'no-repeat',
    ));
	
	$this->addElement('Select', 'footer_imgfixed', array(
      'label' => '',
      'description' => 'Background Image Fixed',
      'multiOptions' => array(
        'fixed' => 'Yes',
        'inherit' => 'No',
      ),
      'value' => 'fixed',
    ));
	
	$this->addElement('Select', 'footer_imgcover', array(
      'label' => '',
      'description' => 'Background Image Cover. Tip: background must be fixed for this to work properly.',
      'multiOptions' => array(
        'cover' => 'Yes',
        'inherit' => 'No',
      ),
      'value' => 'cover',
    ));
	
	$this->addElement('Text', 'footer_border', array(
      'label' => 'Footer Border Thickness',
      'description' => 'In pixels, default is 0px. If you do not wish to have a border you can set this to 0px.',
	  'value' => '0px',
    ));
	
	$this->addElement('Textarea', 'custom_css', array(
      'label' => 'Custom CSS',
      'description' => 'If you are looking to make some CSS customizations it is recommended to add your code here.',
	  'value' => '',
    ));
	
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}