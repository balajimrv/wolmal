<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteverify
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq.tpl 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2><?php echo $this->translate("Members Verification Plugin") ?></h2>
<div class="seaocore_admin_tabs">
  <ul class="navigation">
    <li class="active">
      <a href="<?php echo $this->baseUrl() . '/admin/siteverify/settings/readme' ?>" ><?php echo 'Please go through these important points and proceed by clicking the button at the bottom of this page.'; ?></a>

    </li>
  </ul>
</div>

<?php include_once APPLICATION_PATH . '/application/modules/Siteverify/views/scripts/admin-settings/faq_help.tpl'; ?>
<br />
<button onclick="form_submit();"><?php echo 'Proceed to enter License Key'; ?> </button>

<script type="text/javascript" >
  function form_submit() {
    var url = '<?php echo $this->url(array('module' => 'siteverify', 'controller' => 'settings'), 'admin_default', true) ?>';
    window.location.href = url;
  }
</script>