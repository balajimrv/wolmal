<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: varify.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php if ($this->status): ?>

  <script type="text/javascript">
    setTimeout(function() {
      parent.window.location.href = '<?php echo $this->url(array(), 'user_login', true); ?>';
    }, 5000);
  </script>

  <?php echo $this->translate("Your account has been verified. Please wait to be redirected or click %s to login.", $this->htmlLink(array('route' => 'user_login'), $this->translate("here"))) ?>

<?php else: ?>

  <div class="error">
    <span>
  <?php echo $this->translate($this->error) ?>
    </span>
  </div>

<?php endif;