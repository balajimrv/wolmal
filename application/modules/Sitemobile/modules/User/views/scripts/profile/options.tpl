<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: options.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<div id='profile_options'>
  <?php
  // This is rendered by application/modules/core/views/scripts/_navIcons.tpl
  echo $this->navigation()
          ->menu()
          ->setContainer($this->navigation)
          ->setPartial(array('_navIcons.tpl', 'core'))
          ->render()
  ?>
</div>