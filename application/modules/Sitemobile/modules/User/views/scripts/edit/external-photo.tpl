<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: external-photo.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<div>
  <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
  <div class="sm-ui-make-profile-photo">
    <?php echo $this->itemPhoto($this->photo) ?>
  </div>
</div>