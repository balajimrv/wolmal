<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreform
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: option-edit.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$baseUrl = $this->baseUrl();
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/modules/Sitestoreform/externals/styles/style_sitestoreform.css')
?>
<?php if ($this->form): ?>
  <?php echo $this->form->render($this) ?>
<?php else: ?>
  <div class="global_form_popup_message">
    <?php echo $this->translate("Changes saved.") ?>
  </div>

  <script type="text/javascript">
    parent.onOptionEdit(
  <?php echo Zend_Json::encode($this->option) ?>,
  <?php echo Zend_Json::encode($this->htmlArr) ?>
      );
        (function() { parent.Smoothbox.close(); }).delay(1000);
  </script>
<?php endif; ?>