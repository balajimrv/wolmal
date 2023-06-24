<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: edit.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<div class="headline">
  <h2>
    <?php echo $this->translate('Videos'); ?>
  </h2>
  <div class="tabs">
    <?php
    // Render the menu only for browser
 if (!Engine_Api::_()->sitemobile()->isApp()) {
    echo $this->navigation()
            ->menu()
            ->setContainer($this->navigation)
            ->render();
 }
    ?>
  </div>
</div>

<?php
echo $this->form->render();
?>
