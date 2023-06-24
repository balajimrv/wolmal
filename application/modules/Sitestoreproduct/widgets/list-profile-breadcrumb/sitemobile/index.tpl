<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php 
$subcategory=array();
$subsubcategory=array();
if(!empty($this->sitestoreproduct->subcategory_id)) {
	$subcategory = array("href"=>$this->url(array('category_id' => $this->sitestoreproduct->category_id, 'categoryname' => Engine_Api::_()->getItem('sitestoreproduct_category', $this->sitestoreproduct->category_id)->getCategorySlug(), 'subcategory_id' => $this->sitestoreproduct->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('sitestoreproduct_category', $this->sitestoreproduct->subcategory_id)->getCategorySlug()), "sitestoreproduct_general_subcategory"),"title"=>$this->translate($this->subcategory_name),"icon"=>"arrow-r");
}
if(!empty($this->sitestoreproduct->subsubcategory_id)) {
	$subsubcategory = array("href"=>$this->url(array('category_id' => $this->sitestoreproduct->category_id, 'categoryname' => Engine_Api::_()->getItem('sitestoreproduct_category', $this->sitestoreproduct->category_id)->getCategorySlug(), 'subcategory_id' => $this->sitestoreproduct->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('sitestoreproduct_category', $this->sitestoreproduct->subcategory_id)->getCategorySlug(), 'subsubcategory_id' => $this->sitestoreproduct->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getItem('sitestoreproduct_category', $this->sitestoreproduct->subsubcategory_id)->getCategorySlug()), "sitestoreproduct_general_subsubcategory"),"title"=>$this->translate($this->subsubcategory_name),"icon"=>"arrow-r");
}

$breadcrumb = array(
	array("href"=>$this->url(array('action' => 'home'), 'sitestoreproduct_general', true),"title"=>$this->translate("Products Home"),"icon"=>"arrow-r"),
	array("href"=>$this->url(array('category_id' => $this->sitestoreproduct->category_id, 'categoryname' => Engine_Api::_()->getItem('sitestoreproduct_category', $this->sitestoreproduct->category_id)->getCategorySlug()), "sitestoreproduct_general_category"),"title"=>$this->translate($this->category_name),"icon"=>"arrow-r"),
	$subcategory,$subsubcategory,
	array("title"=>$this->sitestoreproduct->getTitle(),"icon"=>"arrow-d"),
);
echo $this->breadcrumb($breadcrumb);
?>

