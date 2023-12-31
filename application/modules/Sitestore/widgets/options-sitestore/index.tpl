<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div id="profile_options">
  <?php
		echo $this->navigation()
          ->menu()
          ->setContainer($this->gutterNavigation)
          ->setUlClass('navigation sitestores_gutter_options')
          ->render();
  ?>
</div>