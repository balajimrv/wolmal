<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
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

<?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/sitemobile/scripts/_formCreateReview.tpl'; ?>