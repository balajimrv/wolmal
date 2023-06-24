<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _quickView.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php if( empty($quickViewButton) ) : 
        $quickViewClass = "clr sitestoreproduct_quick_view_link"; 
      else: 
        $quickViewClass = "sitestoreproduct_quick_view_btn"; 
      endif; ?>

<a class="<?php echo $quickViewClass ?>" href="javascript:void(0);" onclick="productQuickView(<?php echo $product_id ?>)">
  <?php echo $this->translate('Quick View'); ?>        
</a>