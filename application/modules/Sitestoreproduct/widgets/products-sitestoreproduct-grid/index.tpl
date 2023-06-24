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

<?php 
  $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');
  $productTable = Engine_Api::_()->getDbTable('products', 'sitestoreproduct');
?>

  <?php $isLarge = ($this->columnWidth>170); ?>
  <ul class="sitestoreproduct_grid_view sitestoreproduct_sidebar_grid_view mtop10"> 
    <?php foreach ($this->products as $sitestoreproduct): ?>
      <li class="sitestoreproduct_q_v_wrap g_b <?php if($isLarge): ?>largephoto<?php endif;?>" style="width:auto;">
        <div>
          <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)):?>
            <?php if($sitestoreproduct->newlabel):?>
              <i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
            <?php endif;?>
          <?php endif;?>
          <div class="sitestoreproduct_grid_view_thumb_wrapper">
            <?php $product_id = $sitestoreproduct->product_id; ?>
            <?php $quickViewButton = true; ?>
            <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_quickView.tpl'; ?>
            <a href="<?php echo $sitestoreproduct->getHref() ?>" class ="sitestoreproduct_grid_view_thumb">
              <?php
                $url = $sitestoreproduct->getPhotoUrl($isLarge ? 'thumb.midum' : 'thumb.normal');
                if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/nophoto_product_thumb_normal.png'; endif;
              ?>
              <span style="background-image: url(<?php echo $url; ?>); width: <?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;"></span>
            </a>
          </div>  
          <div class="sitestoreproduct_grid_title">
            <span class="fright sitestoreproduct_price_sale">
              <?php // CALLING HELPER FOR GETTING PRODUCT INFORMATIONS 
              echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($productTable->getProductDiscountedPrice($sitestoreproduct->product_id));//$this->getProductInfo($sitestoreproduct, $this->identity, 'grid_view', $this->showAddToCart, $this->showinStock); ?>
            </span>
            <?php echo $this->htmlLink($sitestoreproduct->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestoreproduct->getTitle(), $this->truncation), array('title' => $sitestoreproduct->getTitle())) ?>         
          </div>
      	</div>
    	</li>
		<?php endforeach; ?>
  </ul>