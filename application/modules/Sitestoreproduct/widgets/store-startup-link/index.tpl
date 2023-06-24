<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
  
<?php
$title = $this->translate("Open a Store");
//if( empty($this->viewer_id) )
//        $title = $this->translate("Signup & Open a Store");

$link = $this->url(array("action" => "startup"), "sitestoreproduct_general", true);
?>
<?php $this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_dashboard.css'); ?>
                
<div class="sitestoreproduct_openstore_link">
  <a class="txt_center dblock sitestoreproduct_icon_plus" href="<?php echo $link; ?>"><b><?php echo $title; ?></b></a>
</div>
