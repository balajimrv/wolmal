<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<div class="sr_sitestoreproduct_product_breadcrumb">
  <a href="<?php echo $this->url(array('action' => 'index'), 'sitestoreproduct_general', true);?>"><?php echo $this->translate("Browse Products");?></a>
  <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
  <a href="<?php echo $this->url(array('action' => 'categories'), 'sitestoreproduct_general', true);?>"><?php echo $this->translate("All Categories");?></a>  
  <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
  <?php echo $this->translate($this->category->getTitle(true)); ?>
</div>
