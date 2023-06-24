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
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');

$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/scripts/slideitmoo-1.1_full_source.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/scripts/core.js');
?>

<?php $settings = Engine_Api::_()->getApi('settings', 'core'); ?>

<a id="" class="pabsolute"></a>
<?php $navsPRE = 'sr_sitestoreproduct_SlideItMoo_' . $this->identity; ?>
<?php if( !empty($this->showPagination) ) : ?>
<script language="javascript" type="text/javascript">
  var slideshow;
  window.addEvents ({
    'domready': function() {
      slideshow = new SlideItMoo({
        overallContainer: '<?php echo $navsPRE ?>_outer',
        elementScrolled: '<?php echo $navsPRE ?>_inner',
        thumbsContainer: '<?php echo $navsPRE ?>_items',
        thumbsContainerOuter: '<?php echo $navsPRE ?>_outer',
        itemsVisible:'<?php echo $this->limit; ?>',
        elemsSlide:'<?php echo $this->limit; ?>',
        duration:<?php echo $this->interval; ?>,
        itemsSelector: '<?php echo $this->vertical ? '.sr_sitestoreproduct_carousel_content_item' : '.sr_sitestoreproduct_carousel_content_item'; ?>' ,
        itemsSelectorLoading:'<?php echo $this->vertical ? 'sr_sitestoreproduct_carousel_loader' : 'sr_sitestoreproduct_carousel_loader'; ?>' ,
        itemWidth:<?php echo $this->vertical ? ($this->blockWidth) : ($this->blockWidth + 24); ?>,
        itemHeight:<?php echo $this->vertical ? ($this->blockHeight + 18) : ($this->blockHeight + 6); ?>,
        showControls:1,
        slideVertical: <?php echo $this->vertical ?>,
        startIndex:1,
        totalCount:'<?php echo $this->totalCount; ?>',
        contentstartIndex:-1,
        url:en4.core.baseUrl+'sitestoreproduct/index/homesponsored',
        
        params:{
          vertical:<?php echo $this->vertical ?>,
          ratingType:'<?php echo $this->ratingType ?>',
          fea_spo:'<?php echo $this->fea_spo ?>',
          popularity:'<?php echo $this->popularity ?>',
          category_id:'<?php echo $this->category_id ?>',
          subcategory_id:'<?php echo $this->subcategory_id ?>',
          subsubcategory_id:'<?php echo $this->subsubcategory_id ?>',
          title_truncation:'<?php echo $this->title_truncation ?>',
          featuredIcon:'<?php echo $this->featuredIcon ?>',
          sponsoredIcon:'<?php echo $this->sponsoredIcon ?>',
          showOptions:<?php if($this->showOptions): echo  json_encode($this->showOptions); else: ?>  {'no':1} <?php endif;?>,
          blockHeight: '<?php echo $this->blockHeight ?>',
          blockWidth: '<?php echo $this->blockWidth ?>',
          newIcon:'<?php echo $this->newIcon ?>',
          widget_id: '<?php echo $this->identity ?>',
          showAddToCart: '<?php echo $this->showAddToCart ?>',
          showinStock: '<?php echo $this->showinStock ?>',
          showPagination: '<?php echo $this->showPagination ?>',
          priceWithTitle: '<?php echo $this->priceWithTitle ?>'
        },
        navs:{
          fwd:'<?php echo $navsPRE . ($this->vertical ? "_forward" : "_right") ?>',
          bk:'<?php echo $navsPRE . ($this->vertical ? "_back" : "_left") ?>'
        },
        transition: Fx.Transitions.linear, /* transition */
        onChange: function() { 
        }
      });
    }
  });
</script>
<?php endif; ?>

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

<?php if ($this->vertical): ?> 
  <?php $sitestoreproduct_advsitestoreproduct = true; ?>
  <div id="<?php echo $navsPRE ?>_outer" class="sr_sitestoreproduct_carousel_vertical sr_sitestoreproduct_carousel">
    <div id="<?php echo $navsPRE ?>_inner" class="sr_sitestoreproduct_carousel_content b_medium" style="width:<?php echo $this->blockWidth + 8; ?>px;">
      <ul id="<?php echo $navsPRE ?>_items" class="sitestoreproduct_grid_view">
        <?php foreach ($this->products as $sitestoreproduct): ?>
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
      </ul>
    </div>
    <?php if( !empty($this->showPagination) ) : ?>
      <div class="sr_sitestoreproduct_carousel_controller">
        <div class="sr_sitestoreproduct_carousel_button sr_sitestoreproduct_carousel_up" id="<?php echo $navsPRE ?>_back" style="display:none;">
          <i></i>
        </div>
        <div class="sr_sitestoreproduct_carousel_button sr_sitestoreproduct_carousel_up_dis" id="<?php echo $navsPRE ?>_back_dis" style="display:block;">
          <i></i>
        </div>

        <div class="sr_sitestoreproduct_carousel_button sr_sitestoreproduct_carousel_down fright" id ="<?php echo $navsPRE ?>_forward">
          <i></i>
        </div>
        <div class="sr_sitestoreproduct_carousel_button sr_sitestoreproduct_carousel_down_dis fright" id="<?php echo $navsPRE ?>_forward_dis" style="display:none;">
          <i></i>
        </div>
      </div>
    <?php endif; ?>
    <div class="clr"></div>
  </div>
<?php else: ?>
  <div id="<?php echo $navsPRE ?>_outer" class="sr_sitestoreproduct_carousel sr_sitestoreproduct_carousel_horizontal" style="width: <?php echo (($this->limit <= $this->totalCount ? $this->limit : $this->totalCount) * ($this->blockWidth + 24)) + 60 ?>px; height: <?php echo ($this->blockHeight + 10) ?>px;">
    <?php if( !empty($this->showPagination) ) : ?>
      <div class="sr_sitestoreproduct_carousel_button sr_sitestoreproduct_carousel_left" id="<?php echo $navsPRE ?>_left" style="display:none;">
        <i></i>
      </div>
      <div class="sr_sitestoreproduct_carousel_button sr_sitestoreproduct_carousel_left_dis" id="<?php echo $navsPRE ?>_left_dis" style="display:<?php echo $this->limit < $this->totalCount ? "block;" : "none;" ?>">
        <i></i>
      </div>
    <?php endif; ?>
    <div id="<?php echo $navsPRE ?>_inner" class="sr_sitestoreproduct_carousel_content" style="height: <?php echo ($this->blockHeight + 11) ?>px;">
      <ul id="<?php echo $navsPRE ?>_items" class="sitestoreproduct_grid_view">
        <?php $i = 0; ?>
        <?php foreach ($this->products as $sitestoreproduct): ?>
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
          <?php $i++; ?>

        <?php endforeach; ?>
      </ul>
    </div>
    <?php if( !empty($this->showPagination) ) : ?>
      <div class="sr_sitestoreproduct_carousel_button sr_sitestoreproduct_carousel_right" id ="<?php echo $navsPRE ?>_right" style="display:<?php echo $this->limit < $this->totalCount ? "block;" : "none;" ?>">
        <i></i>
      </div>
      <div class="sr_sitestoreproduct_carousel_button sr_sitestoreproduct_carousel_right_dis" id="<?php echo $navsPRE ?>_right_dis" style="display:none;">
        <i></i>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>