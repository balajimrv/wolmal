<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: make-order.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php if(!empty($this->error_message)) : ?>
<script>
  Smoothbox.open('<ul class="form-error"> <li>The Entered quantity of the selected variation is not available.</li></ul>');
   setTimeout('parent.Smoothbox.close();', 2000);
  </script>
<?php else :?>
<div class='global_form sitefaq_form'>
  <?php echo $this->form->render($this) ?>
</div>
  <?php endif; ?>