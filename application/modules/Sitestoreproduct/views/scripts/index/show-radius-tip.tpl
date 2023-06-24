<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Module.php 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<form class="global_form_popup">
    <div class="tip">
      <span>
       <?php echo $this->translate('Radius targeting (also known as proximity targeting or "Target a radius") allows you to search content within a certain distance from the selected location, rather than choosing individual city, region, or country. If you want to search content in specific city, region, or country then simply do not select this option.');?>
      </span></div><br />
      <div class="wrapper">
        <button class="button"onclick='javascript:parent.Smoothbox.close()' style="text-align: center"><?php echo $this->translate('close'); ?>
        </button>
    </div>
</form>

<?php if (@$this->closeSmoothbox): ?>
    <script type="text/javascript">
      TB_close();
    </script>
<?php endif; ?>

<style type="text/css">
.wrapper {
text-align: center;
}
</style>