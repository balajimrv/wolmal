<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<style type="text/css">
  #<?php echo $this->pageId ?> .ui-content{
    position: relative;
    z-index: 1;
  }
  #<?php echo $this->pageId ?> .layout_page_core_index_index, #<?php echo $this->pageId ?> .layout_page_core_index_index a{
    color: #fff;
    text-shadow: none;
  }

/*  #<?php //echo $this->pageId ?> .ui-content{
    background-image: url(<?php //echo $this->imageUrl ?>);
    background-size: cover;
    background-position: center;*/
    /*
    background: transparent !important;
border-radius: 0px !important;
border: 0px solid rgb(228, 228, 228) !important;
border-image-source: initial !important;
border-image-slice: initial !important;
border-image-width: initial !important;
border-image-outset: initial !important;
border-image-repeat: initial !important;
padding: 0px !important;
margin: 0px !important;
-webkit-box-shadow: rgb(221, 221, 221) 0px 0px 0px 0px !important;
box-shadow: rgb(221, 221, 221) 0px 0px 0px 0px !important;
    */
  /*}*/
</style>
<div class="waterMarkImage">
  <img style="position: absolute; margin: 0px; padding: 0px; border: none; width: 100%; height: 100%; max-width: none; z-index: -999999; left: 0px; top: 0px;" src="<?php echo $this->imageUrl ?>">
  <div style="position: absolute; margin: 0px; padding: 0px; border: none; width: 100%; height: 100%; max-width: none; z-index: -999999; left: 0px; top: 0px;background-color:rgba(0, 0, 0, 0.5)"></div>
</div>