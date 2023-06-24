<?php
class Zephyrtheme_Form_Admin_Landingpage extends Engine_Form
{
  public function init()
  {
	$lp_menu  = '<div class="tabs tabs_lp"><ul class="navigation">';
	  $lp_menu .= '<li class="active"><a href="#tab1">Slideshow</a></li>';
	  $lp_menu .= '<li><a href="#tab2">Network Features</a></li>';
	  $lp_menu .= '<li><a href="#tab3">Community Presentation</a></li>';
	  $lp_menu .= '<li><a href="#tab4">Featured on</a></li>';
	  $lp_menu .= '<li><a href="#tab5">Bottom Signup</a></li>';
	$lp_menu .= '</ul></div>';

    $this
      ->setTitle('Landing Page')
      ->setDescription('Settings that affect only the Landing Page.');
	  
    // Add submit button
    $this->addElement('Button', 'submit_1', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));

	$this->addElement('Dummy', 'lp_desc', array(
		'description' => '<span style="font-weight: bold;">Each section of the Landing Page is a widget.</span><br />To remove a widget of the Landing Page or to rearrange them go to <a href="'.Zend_Controller_Front::getInstance()->getBaseUrl().'/admin/content?page=3" target="_blank"><span style="font-weight: bold;">Layout Editor</span></a>.<br /><br /><span style="font-weight: bold;">Images are uploaded via <a href="'.Zend_Controller_Front::getInstance()->getBaseUrl().'/admin/files" target="_blank"><span style="font-weight: bold;">File & Media Manager</span></a></span>.<br />After uploading a image refresh this page to see it in the select box below.<br /><br />To edit or translate any phrase you see on the Landing Page go to <span style="font-weight: bold;"><a href="'.Zend_Controller_Front::getInstance()->getBaseUrl().'/admin/language" target="_blank"><span style="font-weight: bold;">Language Manager</span></a> > edit phrases</span><br /><br />',
		'ignore' => true,
		'decorators' => array( array('Description', array('escape'=>false)), ),
	));
	
	$this->addElement('Radio', 'home_style', array(
      'label' => 'Landing Page Style',
      'description' => 'The <span style="font-weight: bold;">Wide</span> Landing Page is taking the full width of the browser.<br />The <span style="font-weight: bold;">Compact</span> Landing Page is taking the width of the content.',
      'multiOptions' => array(
        '1' => 'Wide',
        '2' => 'Compact',
      ),
      'value' => '1',
    ));
	$this->getElement('home_style')->getDecorator('Description')->setOption('escape',false);

	$this->addElement('Dummy', 'lp_menu', array(
		'description' => $lp_menu,
		'ignore' => true,
		'decorators' => array( array('Description', array('escape'=>false)), ),
	));
	

	// START - TAB 2 - "home-boxes" widget
	$this->addElement('Dummy', 'home_nf', array(
		'description' => 'These settings affect the boxes with icons/images from the Landing Page.',
		'ignore' => true,
		'decorators' => array( array('Description', array('escape'=>false)), ),
	));

	$this->addElement('Select', 'home_icos', array(
      'label' => 'Numbers of boxes',
      'description' => '',
      'multiOptions' => array(
        0 => '1',
        1 => '2',
		2 => '3',
		3 => '4',
      ),
      'value' => 3,
    ));

	$this->addElement('Radio', 'home_icosimgs', array(
      'label' => 'What to show?',
      'description' => '',
      'multiOptions' => array(
        0 => 'Image & Title & Text',
        1 => 'Image & Title',
		2 => 'Image & Text',
		3 => 'Title & Text',
      ),
      'value' => 0,
    ));
	
	$this->addElement('Select', 'home_ico_one', array(
      'label' => '1st Box',
      'description' => 'Logo image',
    ));
	
	$this->addElement('Text', 'home_icoone_link', array(
      'label' => '',
      'description' => 'Link',
      'value' => '',
    ));

	$this->addElement('Select', 'home_ico_two', array(
      'label' => '2nd Box',
      'description' => 'Logo image',
    ));
	
	$this->addElement('Text', 'home_icotwo_link', array(
      'label' => '',
      'description' => 'Link',
      'value' => '',
    ));

	$this->addElement('Select', 'home_ico_three', array(
      'label' => '3rd Box',
      'description' => 'Logo image',
    ));
	
	$this->addElement('Text', 'home_icothree_link', array(
      'label' => '',
      'description' => 'Link',
      'value' => '',
    ));

	$this->addElement('Select', 'home_ico_four', array(
      'label' => '4th Box',
      'description' => 'Logo image',
    ));
	
	$this->addElement('Text', 'home_icofour_link', array(
      'label' => '',
      'description' => 'Link',
      'value' => '',
    ));

	// START - TAB 3 - "home-community" widget
    $this->addElement('Select', 'home_commimg', array(
      'label' => 'Image',
      'description' => '',
    ));
	
	$this->addElement('Select', 'home_commimgshow', array(
      'label' => '',
      'description' => 'Show image?',
      'multiOptions' => array(
        0 => 'No',
        1 => 'Yes',
      ),
      'value' => 1,
    ));

    $this->addElement('Select', 'home_commbgimg', array(
      'label' => 'Background Image',
      'description' => '',
    ));

	$this->addElement('Select', 'home_commh1', array(
      'label' => '<h2> Headline',
      'description' => 'Show first Headline?',
      'multiOptions' => array(
        0 => 'No',
        1 => 'Yes',
      ),
      'value' => 1,
    ));

	$this->addElement('Select', 'home_commh2', array(
      'label' => '<h3> Headline',
      'description' => 'Show second Headline?',
      'multiOptions' => array(
        0 => 'No',
        1 => 'Yes',
      ),
      'value' => 1,
    ));
		
	// START - TAB 4 - "homr-logos" widget
	$this->addElement('Dummy', 'home_fo', array(
		'description' => 'An icon will become visible once a company logo-image is select.<br />The logo images should be 150 x 74px',
		'ignore' => true,
		'decorators' => array( array('Description', array('escape'=>false)), ),
	));
	
	$this->addElement('Select', 'feat_linktarget', array(
      'label' => 'Open the links in new window?',
      'description' => '',
      'multiOptions' => array(
        0 => 'No',
        1 => 'Yes',
      ),
      'value' => 1,
    ));

	$this->addElement('Text', 'feat1_link', array(
      'label' => '1st Company',
      'description' => 'Link',
      'value' => '',
    ));
	$this->addElement('Select', 'feat1_img', array(
      'label' => '',
      'description' => 'Logo',
    ));
	$this->addElement('Text', 'feat1_text', array(
      'label' => '',
      'description' => 'Text',
      'value' => '',
    ));

	$this->addElement('Text', 'feat2_link', array(
      'label' => '2nd Company',
      'description' => 'Link',
      'value' => '',
    ));
	$this->addElement('Select', 'feat2_img', array(
      'label' => '',
      'description' => 'Logo',
    ));
	$this->addElement('Text', 'feat2_text', array(
      'label' => '',
      'description' => 'Text',
      'value' => '',
    ));

	$this->addElement('Text', 'feat3_link', array(
      'label' => '3rd Company ',
      'description' => 'Link',
      'value' => '',
    ));
	$this->addElement('Select', 'feat3_img', array(
      'label' => '',
      'description' => 'Logo',
    ));
	$this->addElement('Text', 'feat3_text', array(
      'label' => '',
      'description' => 'Text',
      'value' => '',
    ));

	$this->addElement('Text', 'feat4_link', array(
      'label' => '4th Company',
      'description' => 'Link',
      'value' => '',
    ));
	$this->addElement('Select', 'feat4_img', array(
      'label' => '',
      'description' => 'Logo',
    ));
	$this->addElement('Text', 'feat4_text', array(
      'label' => '',
      'description' => 'Text',
      'value' => '',
    ));

	$this->addElement('Text', 'feat5_link', array(
      'label' => '5th Company',
      'description' => 'Link',
      'value' => '',
    ));
	$this->addElement('Select', 'feat5_img', array(
      'label' => '',
      'description' => 'Logo',
    ));
	$this->addElement('Text', 'feat5_text', array(
      'label' => '',
      'description' => 'Text',
      'value' => '',
    ));

	$this->addElement('Text', 'feat6_link', array(
      'label' => '6th Company',
      'description' => 'Link',
      'value' => '',
    ));
	$this->addElement('Select', 'feat6_img', array(
      'label' => '',
      'description' => 'Logo',
    ));
	$this->addElement('Text', 'feat6_text', array(
      'label' => '',
      'description' => 'Text',
      'value' => '',
    ));
	
	// START - TAB 5 - "home-bottom" widget
    $this->addElement('Select', 'homebottom_img', array(
      'label' => 'Background Image',
      'description' => '',
    ));

	$this->addElement('Select', 'homebottom_h1', array(
      'label' => '<h2> Headline',
      'description' => 'Show first Headline?',
      'multiOptions' => array(
        0 => 'No',
        1 => 'Yes',
      ),
      'value' => 1,
    ));

	$this->addElement('Select', 'homebottom_h2', array(
      'label' => '<h3> Headline',
      'description' => 'Show second Headline?',
      'multiOptions' => array(
        0 => 'No',
        1 => 'Yes',
      ),
      'value' => 1,
    ));
	
	$this->addElement('Select', 'homebottom_button', array(
      'label' => '"Join Today" Button',
      'description' => 'Show the button?',
      'multiOptions' => array(
        0 => 'No',
        1 => 'Yes',
      ),
      'value' => 1,
    ));
 
			
	// Tab 2
	$this->addDisplayGroup(array('home_nf', 'home_icos', 'home_icosimgs', 'home_ico_one', 'home_icoone_link', 'home_ico_two', 'home_icotwo_link', 'home_ico_three', 'home_icothree_link', 'home_ico_four', 'home_icofour_link'),'tab2', array('disableLoadDefaultDecorators' => true));
        $tab2 = $this->getDisplayGroup('tab2');
        $tab2->setDecorators(array('FormElements', 'Fieldset',
        	array('HtmlTag',array('tag'=>'div','id'=>'tab2'))
        ));
		
	// Tab 3
	$this->addDisplayGroup(array('home_commimg', 'home_commimgshow', 'home_commbgimg', 'home_commh1', 'home_commh2'),'tab3', array('disableLoadDefaultDecorators' => true));
        $tab3 = $this->getDisplayGroup('tab3');
        $tab3->setDecorators(array('FormElements', 'Fieldset',
        	array('HtmlTag',array('tag'=>'div','id'=>'tab3'))
        ));

	// Tab 4
	$this->addDisplayGroup(array('home_fo', 'feat_linktarget', 'feat1_link', 'feat1_img', 'feat1_text', 'feat2_link', 'feat2_img', 'feat2_text', 'feat3_link', 'feat3_img', 'feat3_text', 'feat4_link', 'feat4_img', 'feat4_text', 'feat5_link', 'feat5_img', 'feat5_text', 'feat6_link', 'feat6_img', 'feat6_text' ),'tab4', array('disableLoadDefaultDecorators' => true));
        $tab4 = $this->getDisplayGroup('tab4');
        $tab4->setDecorators(array('FormElements', 'Fieldset',
        	array('HtmlTag',array('tag'=>'div','id'=>'tab4'))
        ));
		
	// Tab 5
	$this->addDisplayGroup(array('homebottom_img', 'homebottom_h1', 'homebottom_h2', 'homebottom_button'),'tab5', array(
	    'disableLoadDefaultDecorators' => true
    ));
		$tab5 = $this->getDisplayGroup('tab5');
		$tab5->setDecorators(array('FormElements', 'Fieldset',
			array('HtmlTag',array('tag'=>'div','id'=>'tab5'))
		));
		

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'order' => 1000 // this has to be last!
    ));
  }
}