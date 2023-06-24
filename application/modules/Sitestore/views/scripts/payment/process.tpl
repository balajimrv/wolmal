<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: process.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div>
  <h2> <?php echo $this->translate("Processing Payment") ?></h2>
  <div>
    <center><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/loader.gif" /></center>
  </div>
  <div id="LoadingImage" class="sitestore_payment_process">
    <?php echo $this->translate("Processing Request. Please wait .....") ?>
  </div>
</div>
<script type="text/javascript">
  window.addEvent('load', function(){
    var url = '<?php echo $this->transactionUrl ?>';
    var data = <?php echo Zend_Json::encode($this->transactionData) ?>;
    var request = new Request.Post({
      url : url,
      data : data
    });
    request.send();
  });
</script>
