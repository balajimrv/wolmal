<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: profile.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<div class="headline">
  <h2>
    <?php if ($this->viewer->isSelf($this->user)): ?>
      <?php echo $this->translate('Edit My Profile'); ?>
    <?php else: ?>
      <?php echo $this->translate('%1$s\'s Profile', $this->htmlLink($this->user->getHref(), $this->user->getTitle())); ?>
    <?php endif; ?>
  </h2>
  <div class="tabs">
    <?php
    // Render the menu
    echo $this->navigation()
            ->menu()
            ->setContainer($this->navigation)
            ->render();
    ?>
  </div>
</div>

<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
    'topLevelId' => (int) @$this->topLevelId,
    'topLevelValue' => (int) @$this->topLevelValue
))
?>
<script type="text/javascript">
  sm4.core.runonce.add(function() {
    sm4.user.buildFieldPrivacySelector($.mobile.activePage.find('.global_form').find('[data-field-id]'));
  });
</script>
<?php echo $this->form->render($this) ?>
