<?php
class Zephyrtheme_Form_Admin_General extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('General Settings')
      ->setDescription('');
	  
    // Add submit button
    $this->addElement('Button', 'submit_1', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));

	$this->addElement('Select', 'content_style', array(
      'label' => 'Content Style',
      'description' => '',
      'multiOptions' => array(
        '1' => 'Style 1',
        '2' => 'Style 2',
      ),
      'value' => '1',
    ));

	$this->addElement('Select', 'newsfeed_style', array(
      'label' => 'News Feed Style',
      'description' => '',
      'multiOptions' => array(
        '1' => 'Style 1',
        '2' => 'Style 2',
		'3' => 'Style 3',
		'4' => 'Style 4',
      ),
      'value' => '1',
    ));

	$this->addElement('Select', 'responsive_layout', array(
      'label' => 'Responsive Template',
      'description' => 'Do you want the template to be responsive in order to adapt to any screen size, smartphone and tablet?',
      'multiOptions' => array(
        'false' => 'No',
        'true'  => 'Yes',
      ),
      'value' => 'true',
    ));
	  
	$this->addElement('Select', 'footer_gototop', array(
      'label' => 'GoToTop Button',
      'description' => 'Do you want to show the GoToTop button?<br />This button scrolls up fast on click.',
      'multiOptions' => array(
        0 => 'No',
        1 => 'Yes',
      ),
      'value' => 1,
    ));
	$this->getElement('footer_gototop')->getDecorator('Description')->setOption('escape',false);

    $this->addElement('Select', 'profile_coverimg', array(
      'label' => 'Profile Cover - Default Image ',
      'description' => '<span style="font-weight: bold;">Images are uploaded via <a href="' .
          Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/files" target="_blank">
          <span style="font-weight: bold;">File & Media Manager</span></a></span>.<br />
          After uploading an image refresh this page to see it in the select box below.',
    ));
	$this->getElement('profile_coverimg')->getDecorator('Description')->setOption('escape',false);

	$this->addElement('Select', 'footer_socialicons', array(
      'label' => 'Social Icons',
      'description' => 'Do you want to show the Social Icons-Links?<br />They are showing by default on the side of the browser window, but will appear on the footer if you have a low-screen resolution screen or if you are viewing it on a mobile device.',
      'multiOptions' => array(
        0 => 'No',
        1 => 'Yes',
      ),
      'value' => 1,
    ));
	$this->getElement('footer_socialicons')->getDecorator('Description')->setOption('escape',false);

    $this->addElement('Text', 'footer_facebook', array(
      'label' => '',
      'description' => 'Facebook Page Link. e.g.: https://www.facebook.com/pixythemes',
	  'value' => 'https://www.facebook.com/',
    ));

    $this->addElement('Text', 'footer_twitter', array(
      'label' => '',
      'description' => 'Twitter Page Link. e.g.: https://www.twitter.com/pixythemes',
	  'value' => 'https://www.twitter.com/',
    ));

    $this->addElement('Text', 'footer_linkedin', array(
      'label' => '',
      'description' => 'Linkedin Page Link. e.g.: https://www.linkedin.com/profile',
	  'value' => 'https://www.linkedin.com/',
    ));

    $this->addElement('Text', 'footer_google', array(
      'label' => '',
      'description' => 'Google Page Link. e.g.: https://plus.google.com/profile',
	  'value' => 'https://www.google.com/',
    ));

    $this->addElement('Text', 'footer_youtube', array(
      'label' => '',
      'description' => 'YouTube Page Link. e.g.: https://www.youtube.com/profile',
	  'value' => 'https://www.youtube.com/',
    ));
	
	$this->addElement('Text', 'footer_vimeo', array(
      'label' => '',
      'description' => 'Vimeo Page Link. e.g.: https://www.vimeo.com/profile',
	  'value' => '',
    ));
	
	$this->addElement('Text', 'footer_tumblr', array(
      'label' => '',
      'description' => 'Tumblr Page Link. e.g.: https://www.tumblr.com/profile',
	  'value' => '',
    ));
	
	$this->addElement('Text', 'footer_pinterest', array(
      'label' => '',
      'description' => 'Pinterest Page Link. e.g.: https://www.pinterest.com/profile',
	  'value' => '',
    ));
	
	$this->addElement('Text', 'footer_instagram', array(
      'label' => '',
      'description' => 'Instagram Page Link. e.g.: https://www.instagram.com/profile',
	  'value' => '',
    ));
	
	$this->addElement('Text', 'footer_flickr', array(
      'label' => '',
      'description' => 'Flickr Page Link. e.g.: https://www.flickr.com/profile',
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