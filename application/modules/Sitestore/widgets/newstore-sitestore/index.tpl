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
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php if ($this->creationLink && count($this->quickNavigation) > 0): ?>
  <div class="quicklinks">
    <?php
			echo $this->navigation()
            ->menu()
            ->setContainer($this->quickNavigation)
            ->render();
    ?>
  </div>
<?php elseif($this->canCreate):?>
  <div class="clr">
    <ul class="quicklinks">
     <li><a href='<?php echo $this->url(array("action"=>"startup"), 'sitestoreproduct_general', true) ?>' class='buttonlink seaocore_icon_add'><?php echo $this->translate('Open a New Store');?></a> </li>
    </ul> 
  </div>  
<?php endif; ?>
