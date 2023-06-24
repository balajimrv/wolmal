<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Filter.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesalbum_Form_Admin_Manage_Filter extends Engine_Form
{
	protected $_isFeatured;
	protected $_isSponsored;
	protected $_creationDate;
	protected $_startDate;
	protected $_endDate;
	protected $_albumTitle;
	protected $_offtheDay;
	public function setAlbumTitle($title) {
  $this->_albumTitle = $title;
    return $this;
  }
  public function getAlbumTitle() {
    return $this->_albumTitle;
  }
	public function setOfftheDay($title) {
  $this->_offtheDay = $title;
    return $this;
  }
  public function getOfftheDay() {
    return $this->_offtheDay;
  }
	public function setStartDate($title) {
  $this->_startDate = $title;
    return $this;
  }
  public function getStartDate() {
    return $this->_startDate;
  }
	public function setEndDate($title) {
  $this->_endDate = $title;
    return $this;
  }
  public function getEndDate() {
    return $this->_endDate;
  }
	public function setCreationDate($title) {
	  $this->_creationDate = $title;
    return $this;
  }

  public function getCreationDate() {
    return $this->_creationDate;
  }
	
	public function setIsSponsored($title) {
    $this->_isSponsored = $title;
    return $this;
  }

  public function getIsSponsored() {
    return $this->_isSponsored;
  }
	public function setIsFeatured($title) {
    $this->_isFeatured = $title;
    return $this;
  }

  public function getIsFeatured() {
    return $this->_isFeatured;
  }

  public function init()
  {
		parent::init();
    $this
      ->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
      ;

    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ))
      ->setMethod('GET');

    $titlename = new Zend_Form_Element_Text('title');
    $titlename
      ->setLabel('Title')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));
	 if($this->getAlbumTitle() == 'yes'){
		$album_title = new Zend_Form_Element_Text('album_title');
    $album_title
      ->setLabel('Album Title')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
			->addDecorator('HtmlTag', array('tag' => 'div'));
	}
	
		$owner_name = new Zend_Form_Element_Text('owner_name');
    $owner_name
      ->setLabel('Owner Name')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));
   
	 if($this->getIsFeatured() != 'no'){
    $is_featured = new Zend_Form_Element_Select('is_featured');
    $is_featured
      ->setLabel('Featured')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions(array(
        '' =>'',
				'1' => 'Yes',
        '0' => 'No',
      ))
      ->setValue('');
	 }
		$offtheday = new Zend_Form_Element_Select('offtheday');
    $offtheday
      ->setLabel('Of The Day')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions(array(
        '' =>'',
				'1' => 'Yes',
        '0' => 'No',
      ))
      ->setValue('');
	if($this->getIsSponsored() != 'no'){
		$is_sponsored = new Zend_Form_Element_Select('is_sponsored');
    $is_sponsored
      ->setLabel('Sponsored')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions(array(
        '' =>'',
				'1' => 'Yes',
        '0' => 'No',
      ))
      ->setValue('');
	}
	$rating = new Zend_Form_Element_Select('rating');
    $rating
      ->setLabel('Rated')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions(array(
        '' =>'',
				'1' => 'Yes',
        '0' => 'No',
      ))
      ->setValue('');
if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum_enable_location', 1)){
	$location = new Zend_Form_Element_Select('location');
    $location
      ->setLabel('Has Location')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions(array(
        '' =>'',
				'1' => 'Yes',
        '0' => 'No',
      ))
      ->setValue('');
}
	if($this->getCreationDate() != 'no'){
		$date = new Zend_Form_Element_Text('creation_date');
    $date
      ->setLabel('Creation Date: ex (2000-12-01)')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));		
	}
	if($this->getStartDate() == 'yes'){
		$startdate = new Zend_Form_Element_Text('starttime');
    $startdate
      ->setLabel('Start Date: ex (2000-12-01)')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));		
	}
	if($this->getEndDate() == 'yes'){
		$enddate = new Zend_Form_Element_Text('endtime');
    $enddate
      ->setLabel('End Date: ex (2000-12-01)')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));		
	}
		 // prepare categories
    $categories = Engine_Api::_()->sesalbum()->getCategories();
    
    if (count($categories)!=0 && strpos($_SERVER['REQUEST_URI'],'photo') === false){
      $categories_prepared['']= "";
      foreach ($categories as $category){
        $categories_prepared[$category->category_id]= $category->category_name;
      }
		
	 // category field
		$category = new Zend_Form_Element_Select('category_id',array('onchange' => 'showSubCategory(this.value)'));
    $category
      ->setLabel('Category')
      ->clearDecorators()
			->setMultiOptions($categories_prepared)
       ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));
     
     //Add Element: Sub Category
		 
		 $subCategory = new Zend_Form_Element_Select('subcat_id',array('onchange' => 'showSubSubCategory(this.value)'));
		  $subCategory
      ->setLabel('2nd-level Category')
      ->clearDecorators()
			->setMultiOptions(array('0'=>''))
       ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));
      //Add Element: Sub Sub Category
			$subsubCategory = new Zend_Form_Element_Select('subsubcat_id');
		  $subsubCategory
      ->setLabel('3rd-level Category')
      ->clearDecorators()
			->setMultiOptions(array('0'=>''))
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));
		
	}
    $submit = new Zend_Form_Element_Button('search', array('type' => 'submit'));
    $submit
      ->setLabel('Search')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
      ->addDecorator('HtmlTag2', array('tag' => 'div'));
		
		$arrayItem = array();
		$arrayItem = !empty($titlename)?	array_merge($arrayItem,array($titlename)) : '';
		$arrayItem = !empty($album_title) ?	array_merge($arrayItem,array($album_title)) : $arrayItem;
		$arrayItem = !empty($owner_name) ?	array_merge($arrayItem,array($owner_name)) : $arrayItem;
		$arrayItem = !empty($is_featured) ?	array_merge($arrayItem,array($is_featured)) : $arrayItem;
		$arrayItem = !empty($is_sponsored)?	array_merge($arrayItem,array($is_sponsored)) : $arrayItem;
		$arrayItem = !empty($location)?	array_merge($arrayItem,array($location)) : $arrayItem;
		$arrayItem = !empty($offtheday) ? array_merge($arrayItem,array($offtheday)) : $arrayItem;
		$arrayItem = !empty($rating)?	array_merge($arrayItem,array($rating)) : $arrayItem;
		$arrayItem = !empty($date)?	array_merge($arrayItem,array($date)) : $arrayItem;
		$arrayItem = !empty($startdate)?	array_merge($arrayItem,array($startdate)) : $arrayItem;
		$arrayItem = !empty($enddate)?	array_merge($arrayItem,array($enddate)) : $arrayItem;
		$arrayItem = !empty($category)?	array_merge($arrayItem,array($category)) : $arrayItem;
		$arrayItem = !empty($subCategory)?	array_merge($arrayItem,array($subCategory)) : $arrayItem;
		$arrayItem = !empty($subsubCategory)?	array_merge($arrayItem,array($subsubCategory)) : $arrayItem;
		$arrayItem = !empty($submit)?	array_merge($arrayItem,array($submit)) : '';
    $this->addElements($arrayItem);
  }
}