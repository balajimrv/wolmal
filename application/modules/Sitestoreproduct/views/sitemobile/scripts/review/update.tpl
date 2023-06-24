<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagereview
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>


<?php 
$breadcrumb = array(
    array("href"=>$this->sitestoreproduct->getHref(),"title"=>$this->sitestoreproduct->getTitle(),"icon"=>"arrow-r"),
    array("href"=>$this->sitestoreproduct->getHref(array('tab' => $this->content_id)),"title"=>"Review","icon"=>"arrow-d")
     );
echo $this->breadcrumb($breadcrumb);
?>


<?php echo $this->form->render($this) ?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/sitemobile/scripts/_formUpdateReview.tpl'; ?>