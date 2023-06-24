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

<?php if( empty($this->isProductProfile) ) : ?>
  <?php if($this->createAllow == 1):?>
    <button class="sr_sitestoreproduct_review_button" onclick="writeAReview('create');"><?php echo $this->translate("Write a Review") ?></button>
  <?php elseif($this->createAllow == 2):?>
    <button class="sr_sitestoreproduct_review_button" onclick="writeAReview('update');"><?php echo $this->translate("Update your Review") ?></button>
  <?php endif;?>
<?php else: ?> 
<!--IF WIDGET IS CALLED BY PRODUCT PROFILE PAGE OR QUICK VIEW-->
  <?php $tab_id = Engine_Api::_()->sitestoreproduct()->existWidget('sitestoreproduct_reviews', 0); ?>
  <?php if($this->createAllow == 1):?>
    <?php if( empty($this->isQuickView) ) : ?>
      <a href="javascript:void(0)" class="sitestoreproduct_review_link" onclick="writeAReview('create');">
        <?php echo $this->translate("Write a Review") ?>
      </a>
    <?php else: ?>
      <a class="sitestoreproduct_review_link" href='<?php echo $this->url(array('product_id' => $this->sitestoreproduct->product_id, 'slug' => $this->sitestoreproduct->getSlug(), 'tab' => $tab_id), 'sitestoreproduct_entry_view', true) ?>' onclick="Smoothbox.close();">
        <?php echo $this->translate('Write a Review'); ?>
      </a>
    <?php endif; ?>
  <?php elseif($this->createAllow == 2):?>
    <?php if( empty($this->isQuickView) ) : ?>
      <a href="javascript:void(0)" class="sitestoreproduct_review_link" onclick="writeAReview('update');"><?php echo $this->translate("Update your Review") ?></a>
    <?php else: ?>
      <a class="sitestoreproduct_review_link" href='<?php echo $this->url(array('product_id' => $this->sitestoreproduct->product_id, 'slug' => $this->sitestoreproduct->getSlug(), 'tab' => $tab_id), 'sitestoreproduct_entry_view', true) ?>' onclick="Smoothbox.close();"><?php echo $this->translate('Update your Review'); ?></a>
    <?php endif; ?>
  <?php endif;?>
<?php endif;?>

  
<script type="text/javascript">
  function writeAReview(option){
    <?php if($this->product_profile_page): ?>
      if($('main_tabs') && $('main_tabs').getElement('.tab_layout_sitestoreproduct_user_sitestoreproduct')){
        if($('sitestoreproduct_create') && $('main_tabs').getElement('.tab_layout_sitestoreproduct_user_sitestoreproduct').hasClass('active')){
          window.location.hash = 'sitestoreproduct_create';
          return;
        } else if($('sitestoreproduct_update') && $('main_tabs').getElement('.tab_layout_sitestoreproduct_user_sitestoreproduct').hasClass('active')){
          window.location.hash = 'sitestoreproduct_update';
          return;
        } 
        tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitestoreproduct_user_sitestoreproduct'));
          <?php if($this->contentDetails && isset ($this->contentDetails->params['loaded_by_ajax']) && $this->contentDetails->params['loaded_by_ajax']): ?>
        var params = {
          requestParams :<?php echo json_encode($this->contentDetails->params) ?>,
          responseContainer :$$('.layout_sitestoreproduct_user_sitestoreproduct')
        }

        params.requestParams.content_id = '<?php echo $this->contentDetails->content_id ?>';
        en4.sitestoreproduct.ajaxTab.sendReq(params);
        <?php endif; ?>
        if(option == 'create') {
          (function(){
            window.location.hash = 'sitestoreproduct_create';
          }).delay(3000);
        } else if(option == 'update') {
          (function(){
            window.location.hash = 'sitestoreproduct_update';
          }).delay(3000);
        }
      } else {
        if(option == 'create') {
// 						(function(){
            window.location.hash = 'sitestoreproduct_create';
// 						}).delay(3000);
        } else if(option == 'update') {
// 						(function(){
            window.location.hash = 'sitestoreproduct_update';
// 						}).delay(3000);
        }
      }
      <?php else:?>
      window.location.href="<?php echo $this->sitestoreproduct->getHref(); ?>";
      <?php endif;?>
    }
  </script>