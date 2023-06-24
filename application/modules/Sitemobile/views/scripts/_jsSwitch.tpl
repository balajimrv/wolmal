<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _jsSwitch.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  sm4.core.runonce.add(function() {
    customProfileFiledsSettings[$.mobile.activePage.attr('id')] = {
      topLevelId: '<?php echo sprintf('%d', (int) @$this->topLevelId) ?>',
      topLevelValue: '<?php echo sprintf('%d', (int) @$this->topLevelValue) ?>',
      elementCache: {}
    };
    changeFields(null, null, true);
  });

</script>