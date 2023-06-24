<?php
class Zephyrtheme_Form_Admin_Typography extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Typography')
      ->setDescription('Settings that affect the typography across the template.');
	  
    // Add submit button
    $this->addElement('Button', 'submit_1', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));

    $this->addElement('Select', 'main_font', array(
      'label' => 'Main Font',
      'description' => '<span style="font-weight: bold;">You can see the web safe fonts <a href="http://www.w3schools.com/cssref/css_websafe_fonts.asp" target="_blank"><span style="font-weight: bold;">here</span></a></span>',
      'multiOptions' => array(
        'Georgia, serif' => 'Georgia, serif',
        '"Palatino Linotype", "Book Antiqua", Palatino, serif' => '"Palatino Linotype", "Book Antiqua", Palatino, serif',
		'"Times New Roman", Times, serif' => '"Times New Roman", Times, serif',
		'Arial, Helvetica, sans-serif' => 'Arial, Helvetica, sans-serif',
		'"Arial Black", Gadget, sans-serif' => '"Arial Black", Gadget, sans-serif',
		'"Comic Sans MS", cursive, sans-serif' => '"Comic Sans MS", cursive, sans-serif',
		'Impact, Charcoal, sans-serif' => 'Impact, Charcoal, sans-serif',
		'"Lucida Sans Unicode", "Lucida Grande", sans-serif' => '"Lucida Sans Unicode", "Lucida Grande", sans-serif',
		'Tahoma, Geneva, sans-serif' => 'Tahoma, Geneva, sans-serif',
		'"Trebuchet MS", Helvetica, sans-serif' => '"Trebuchet MS", Helvetica, sans-serif',
		'Verdana, Geneva, sans-serif' => 'Verdana, Geneva, sans-serif',
		'"Courier New", Courier, monospace' => '"Courier New", Courier, monospace',
		'"Lucida Console", Monaco, monospace' => '"Lucida Console", Monaco, monospace',
      ),
	  'value' => 'Arial, Helvetica, sans-serif',
    ));
	$this->getElement('main_font')->getDecorator('Description')->setOption('escape',false);
	
    $this->addElement('Select', 'headings_font', array(
      'label' => 'Font for Headings',
      'description' => '<span style="font-weight: bold;">You can see the web safe fonts <a href="http://www.w3schools.com/cssref/css_websafe_fonts.asp" target="_blank"><span style="font-weight: bold;">here</span></a></span>',
      'multiOptions' => array(
        'Georgia, serif' => 'Georgia, serif',
        '"Palatino Linotype", "Book Antiqua", Palatino, serif' => '"Palatino Linotype", "Book Antiqua", Palatino, serif',
		'"Times New Roman", Times, serif' => '"Times New Roman", Times, serif',
		'Arial, Helvetica, sans-serif' => 'Arial, Helvetica, sans-serif',
		'"Arial Black", Gadget, sans-serif' => '"Arial Black", Gadget, sans-serif',
		'"Comic Sans MS", cursive, sans-serif' => '"Comic Sans MS", cursive, sans-serif',
		'Impact, Charcoal, sans-serif' => 'Impact, Charcoal, sans-serif',
		'"Lucida Sans Unicode", "Lucida Grande", sans-serif' => '"Lucida Sans Unicode", "Lucida Grande", sans-serif',
		'Tahoma, Geneva, sans-serif' => 'Tahoma, Geneva, sans-serif',
		'"Trebuchet MS", Helvetica, sans-serif' => '"Trebuchet MS", Helvetica, sans-serif',
		'Verdana, Geneva, sans-serif' => 'Verdana, Geneva, sans-serif',
		'"Courier New", Courier, monospace' => '"Courier New", Courier, monospace',
		'"Lucida Console", Monaco, monospace' => '"Lucida Console", Monaco, monospace',
      ),
	  'value' => '"Trebuchet MS", Helvetica, sans-serif',
    ));
	$this->getElement('headings_font')->getDecorator('Description')->setOption('escape',false);

    $this->addElement('Text', 'font_size', array(
      'label' => 'Font Size',
      'description' => 'e.g.: <span style="font-weight: bold;">13px</span>.',
	  'value' => '13px',
    ));
	$this->getElement('font_size')->getDecorator('Description')->setOption('escape',false);
	
	$this->addElement('Select', 'text_fontweight', array(
      'label' => 'Text - Font Weight',
      'description' => '',
      'multiOptions' => array(
        400 =>  'normal',
		700 =>  'bold',
      ),
      'value' => 400,
    ));
	$this->getElement('text_fontweight')->getDecorator('Description')->setOption('escape',false);
	
	$this->addElement('Select', 'headings_fontweight', array(
      'label' => 'Headings - Font Weight',
      'description' => '',
      'multiOptions' => array(
        400 =>  'normal',
		700 =>  'bold',
      ),
      'value' => 400,
    ));
	$this->getElement('headings_fontweight')->getDecorator('Description')->setOption('escape',false);

	$this->addElement('Select', 'use_googlefonts', array(
      'label' => 'Use Google Fonts?',
      'description' => 'If you will use Google Fonts, the web safe fonts selected above will not be taken into consideration anymore.',
      'multiOptions' => array(
        'false' => 'No',
        'true' => 'Yes',
      ),
      'value' => 'true',
    ));
	$this->getElement('use_googlefonts')->getDecorator('Description')->setOption('escape',false);

	$this->addElement('Text', 'google_font', array(
      'label' => 'Google Font to replace Main Font',
      'description' => '<span style="font-weight: bold;">Google Fonts can be seen <a href="https://www.google.com/fonts" target="_blank"><span style="font-weight: bold;">here</span></a></span>.<br />Enter the name of the Google Font you wish to use.<br />This will replace the Main Font.<br />e.g.: <span style="font-weight: bold;">Open Sans</span>',
	  'value' => 'Open Sans',
    ));
	$this->getElement('google_font')->getDecorator('Description')->setOption('escape',false);

	$this->addElement('Text', 'google_headingsfont', array(
      'label' => 'Google Font for Headings',
      'description' => '<span style="font-weight: bold;">Google Fonts can be seen <a href="https://www.google.com/fonts" target="_blank"><span style="font-weight: bold;">here</span></a></span>.<br />Enter the name of the Google Font you wish to use for Headings(* h1, h2, h3, h4, h5, h6).<br />e.g.: <span style="font-weight: bold;">Raleway</span>',
	  'value' => 'Raleway',
    ));
	$this->getElement('google_headingsfont')->getDecorator('Description')->setOption('escape',false);

	$this->addElement('Dummy', 'google_fontsetsdesc', array(
		'label' => '<span style="font-weight: bold;">Charachter Sets for Google Fonts</span>',
		'description' => 'Warning: Not all Google Fonts are supporting all charachters sets, you will have to check with <a href="http://www.google.com/fonts/" target="_blank">Google Fonts</a>.',
		'ignore' => true,
		'decorators' => array( array('Label', array('escape'=>false)), array('Description', array('escape'=>false)), ),
	));
	$this->addElement('Checkbox', 'googlefontset_latinext', array(
	  'label' => 'Latin Extended', 'description' => '', 'value' => false ));
	$this->addElement('Checkbox', 'googlefontset_cyrillicext', array(
	  'label' => 'Cyrillic Extended', 'description' => '', 'value' => false ));
	$this->addElement('Checkbox', 'googlefontset_cyrillic', array(
	  'label' => 'Cyrillic', 'description' => '', 'value' => false ));
	$this->addElement('Checkbox', 'googlefontset_greekext', array(
	  'label' => 'Greek Extended', 'description' => '', 'value' => false ));
	$this->addElement('Checkbox', 'googlefontset_greek', array(
	  'label' => 'Greek Extended', 'description' => '', 'value' => false ));
	$this->addElement('Checkbox', 'googlefontset_vietnamese', array(
	  'label' => 'Vietnamese', 'description' => '', 'value' => false ));


    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}