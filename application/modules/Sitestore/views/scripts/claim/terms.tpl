<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: terms.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="sitestore_claim_turms">
  <b><?php echo $this->translate('Terms of claiming a Store') ?></b>
  <ol>
    <li><?php echo $this->translate('STORE_TERMS_CLAIM_1') ?></li>		
    <li><?php echo $this->translate('STORE_TERMS_CLAIM_2') ?></li>
  </ol>
  <br />
  <p><?php echo $this->translate('Thank you for your cooperation.') ?></p>
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