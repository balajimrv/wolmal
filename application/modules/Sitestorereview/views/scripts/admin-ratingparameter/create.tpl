<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="settings admin_review_popup">
  <?php echo $this->form->setAttrib('class', 'global_form')->render($this) ?>
</div>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>