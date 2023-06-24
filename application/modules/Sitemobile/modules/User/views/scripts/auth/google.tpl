<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: google.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<div class="tip">
  <span>
    <?php if ($this->error == 'access_denied'): ?>
      <?php echo $this->translate('You must grant access to login using Google.') ?>
    <?php else: ?>
      <?php echo $this->translate('An unknown error has occurred.') ?>
    <?php endif ?>
  </span>
</div>
