<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
$subcategory=array();
$subsubcategory=array();
if(!empty($this->sitestore->subcategory_id)) {
$subcategory = array("href"=>$this->url(array('category_id' => $this->sitestore->category_id, 'categoryname' => Engine_Api::_()->getItem('sitestore_category', $this->sitestore->category_id)->getCategorySlug(), 'subcategory_id' => $this->sitestore->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('sitestore_category', $this->sitestore->subcategory_id)->getCategorySlug()), "sitestore_general_subcategory") ,"title"=>$this->translate($this->subcategory_name),"icon"=>"arrow-r");
}
if(!empty($this->sitestore->subsubcategory_id)) {
$subsubcategory = array("href"=>$this->url(array('category_id' => $this->sitestore->category_id, 'categoryname' => Engine_Api::_()->getItem('sitestore_category', $this->sitestore->category_id)->getCategorySlug(), 'subcategory_id' => $this->sitestore->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('sitestore_category', $this->sitestore->subcategory_id)->getCategorySlug(), 'subsubcategory_id' => $this->sitestore->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getItem('sitestore_category', $this->sitestore->subsubcategory_id)->getCategorySlug()), "sitestore_general_subsubcategory"),"title"=>$this->translate($this->subsubcategory_name),"icon"=>"arrow-r");
}

$breadcrumb = array(
    array("href"=>$this->url(array(),'sitestore_general', false),"title"=>"Stores Home","icon"=>"arrow-r"),
    array("href"=>$this->url(array('category_id' => $this->sitestore->category_id, 'categoryname' => Engine_Api::_()->getItem('sitestore_category', $this->sitestore->category_id)->getCategorySlug()), "sitestore_general_category"),"title"=>$this->translate($this->category_name),"icon"=>"arrow-r"),
    $subcategory,$subsubcategory,
		array("title"=>$this->sitestore->getTitle(),"icon"=>"arrow-d"),
     );

echo $this->breadcrumb($breadcrumb);
?>