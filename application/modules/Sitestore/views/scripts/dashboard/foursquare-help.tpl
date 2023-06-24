<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: foursquarehelp.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="sitestore_claim_turms">
  <b><?php echo $this->translate('Obtaining code for your Save to foursquare Button.') ?></b>
  <ol>
    <li><?php echo $this->translate("Next to your foursquare venue or foursquare tip, you’ll notice a button: &lt;/&gt; , like in the 2 pictures below.") ?></li>
    <li><?php echo $this->translate("Click on &lt;/&gt;.") ?></li>
    <li><?php echo $this->translate("Choose your button color.") ?></li>
    <li><?php echo $this->translate("Copy the embed code for your Save to foursquare Button.") ?></li>
  </ol>
  <br /><br /><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/foursquarehelp1.jpg" alt="" />
  <br /><br /><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/foursquarehelp2.jpg" alt="" />
</div>

<style type="text/css">
  *{
    font-size:12px;
    font-family:Arial, Helvetica, sans-serif;
  }
  .sitestore_claim_turms
  {
    margin:10px;
  }
  ol{
    float:left;
    width:100%;
    clear:both;
    margin-bottom:10px;
  }
  ol li
  {
    margin-left:30px;
    clear:both;
    margin-top: 5px;
  }
  p{
    margin-left:30px;
    float:left;
  }
</style>