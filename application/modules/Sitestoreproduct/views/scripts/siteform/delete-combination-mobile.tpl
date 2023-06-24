<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete-combination.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<form method="post" class="global_form_popup" data-ajax="false">
  <div>
    <h3><?php echo $this->translate('Delete Variation ?'); ?></h3>
    <p>
      <?php echo $this->translate('Are you sure that you want to delete this variation ?'); ?>
    </p>
    <br />
    <p>
      <button type='submit'><?php echo $this->translate('Delete'); ?></button>
      or <a href='javascript:void(0);' onclick='window.location.href="<?php echo $this->url(array(
          'module' => 'sitestoreproduct',
          'controller' => 'siteform',
          'action' => 'product-category-attributes-mobile',
          'product_id' => $this->product_id
      ), 'default', true);?>"'><?php echo $this->translate('cancel'); ?></a>
    </p>
  </div>
</form>