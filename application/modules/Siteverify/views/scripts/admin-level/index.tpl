<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteverify
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  var fetchLevelSettings = function(level_id) {
    window.location.href = en4.core.baseUrl + 'admin/siteverify/level/index/id/' + level_id;
  };
</script>
<h2>
<?php echo $this->translate('Members Verification Plugin') ?>
</h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
  </div>
  <?php endif; ?>
<div class='clear seaocore_settings_form'>
  <div class='settings'>
<?php echo $this->form->render($this) ?>
  </div>
</div>

<script type="text/javascript">
  function allowVerify() {
    if ($('allow_verify-1').checked) {
      $('auth_verify-wrapper').style.display = 'block';
    } else {
      $('auth_verify-wrapper').style.display = 'none';
    }
  }
  
  window.addEvent('domready', function() {
    allowVerify();
  });
</script>


