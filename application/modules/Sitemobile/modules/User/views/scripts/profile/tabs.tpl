<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: tabs.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<?php

// This is rendered by application/modules/core/views/scripts/_navJsTabs.tpl
echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->setPartial(array('_navJsTabs.tpl', 'core'))
        ->render()
?>