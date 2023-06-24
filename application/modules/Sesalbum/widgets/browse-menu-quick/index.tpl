<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php if( count($this->quickNavigation) > 0 ): ?>
  <div class="quicklinks">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->quickNavigation)
        ->render();
    ?>
  </div>
<?php endif; ?>