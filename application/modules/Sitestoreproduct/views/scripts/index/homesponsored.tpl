<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: homesponsored.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php
$ratingValue = $this->ratingType;
$ratingShow = 'small-star';
if ($this->ratingType == 'rating_editor') {
  $ratingType = 'editor';
} elseif ($this->ratingType == 'rating_avg') {
  $ratingType = 'overall';
} else {
  $ratingType = 'user';
}
?>
  <?php $settings = Engine_Api::_()->getApi('settings', 'core'); ?>
  <?php if ($this->direction == 1) { ?>
    <?php $j = 0; ?>
    <?php foreach ($this->sitestoreproducts as $sitestoreproduct): ?>
      <?php
      echo $this->partial(
              'list_carousel.tpl', 'sitestoreproduct', array(
          'sitestoreproduct' => $sitestoreproduct,
          'title_truncation' => $this->title_truncation,
          'ratingShow' => $ratingShow,
          'ratingType' => $ratingType,
          'ratingValue' => $ratingValue,
          'vertical' => $this->vertical,
          'featuredIcon' => $this->featuredIcon,
          'sponsoredIcon' => $this->sponsoredIcon,
          'showOptions' => $this->showOptions,
					'blockHeight' => $this->blockHeight,
          'blockWidth' => $this->blockWidth,
          'newIcon' => $this->newIcon,
          'showAddToCart' => $this->showAddToCart,
          'showinStock' => $this->showinStock,
          'widget_id' => $this->identity,
          'priceWithTitle' => $this->priceWithTitle
      ));
      ?>	 
    <?php endforeach; ?>
    <?php if ($j < ($this->sponserdSitestoreproductsCount)): ?>
      <?php for ($j; $j < ($this->sponserdSitestoreproductsCount); $j++): ?>
        <li class="sr_sitestoreproduct_carousel_content_item b_medium" style="height: <?php echo ($this->blockHeight) ?>px;width : <?php echo ($this->blockWidth) ?>px;">
        </li>
      <?php endfor; ?>
    <?php endif; ?>
  <?php } else { ?>

    <?php for ($i = $this->sponserdSitestoreproductsCount; $i < Count($this->sitestoreproducts); $i++): ?>
      <?php $sitestoreproduct = $this->sitestoreproducts[$i]; ?>
      <?php
      echo $this->partial(
              'list_carousel.tpl', 'sitestoreproduct', array(
          'sitestoreproduct' => $sitestoreproduct,
          'title_truncation' => $this->title_truncation,
          'ratingShow' => $ratingShow,
          'ratingType' => $ratingType,
          'ratingValue' => $ratingValue,
          'vertical' => $this->vertical,
          'featuredIcon' => $this->featuredIcon,
          'sponsoredIcon' => $this->sponsoredIcon,
          'showOptions' => $this->showOptions,
          'blockHeight' => $this->blockHeight,
          'blockWidth' => $this->blockWidth,        
          'newIcon' => $this->newIcon,
          'showAddToCart' => $this->showAddToCart,
          'showinStock' => $this->showinStock,
          'widget_id' => $this->identity,
          'priceWithTitle' => $this->priceWithTitle
      ));
      ?>	
    <?php endfor; ?>

    <?php for ($i = 0; $i < $this->sponserdSitestoreproductsCount; $i++): ?>
      <?php $sitestoreproduct = $this->sitestoreproducts[$i]; ?>
      <?php
      echo $this->partial(
              'list_carousel.tpl', 'sitestoreproduct', array(
          'sitestoreproduct' => $sitestoreproduct,
          'title_truncation' => $this->title_truncation,
          'ratingShow' => $ratingShow,
          'ratingType' => $ratingType,
          'ratingValue' => $ratingValue,
          'vertical' => $this->vertical,
          'featuredIcon' => $this->featuredIcon,
          'sponsoredIcon' => $this->sponsoredIcon,
          'showOptions' => $this->showOptions,
          'blockHeight' => $this->blockHeight,
          'blockWidth' => $this->blockWidth,        
          'newIcon' => $this->newIcon,
          'showAddToCart' => $this->showAddToCart,
          'showinStock' => $this->showinStock,
          'widget_id' => $this->identity,
          'priceWithTitle' => $this->priceWithTitle
      ));
      ?>	
    <?php endfor; ?>
  <?php } ?>

