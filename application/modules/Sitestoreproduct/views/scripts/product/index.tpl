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

<script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }
  
  var flag = 0;
  //ADD PRODUCT INTO CART
  function addToCart(product_id)
  {
    //NEW REQUEST ONLY WHEN PREVIOUS WILL COMPLETE
    if( flag == 0 )
    {
      flag = 1;
      var widgets_id = <?php echo $this->identity; ?>
      $('loading_image_'+widgets_id+'_'+product_id).innerHTML = '<img src='+en4.core.staticBaseUrl+'application/modules/Sitestoreproduct/externals/images/loading.gif height=15 width=15>';
        en4.core.request.send(new Request.JSON({
          url : en4.core.baseUrl + 'product/addto-cart/p_id/'+product_id,
          onSuccess: function(responseJSON)
          {
            $('loading_image_'+product_id).innerHTML = '';
            flag = 0;
          }
        })
      );
    }
    
  }
</script>

<div class="headline">
  <?php if( count($this->navigation) > 0 ): ?>
    <div class="tabs">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->navigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>

<div class='layout_right'>
  <?php if( count($this->quickNavigation) > 0 ): ?>
    <div class="quicklinks">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->quickNavigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>

<div class='layout_middle'>
    
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <ul class="sitestoreproducts_browse">
      <?php foreach( $this->paginator as $item ): ?>
        <li>
          <div class='sitestoreproducts_browse_photo'>
            <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon')) ?>
          </div>
          <div class='sitestoreproducts_browse_options'>
            <?php echo '<a href="javascript:void(0)" onclick="addToCart('.$item->product_id.')">Add to Cart</a>'; ?>
						<div id="loading_image_<?php echo $this->identity.'_'.$item->product_id; ?>"></div>
          </div>
          <div class='sitestoreproducts_browse_info'>
            <p class='sitestoreproducts_browse_info_title'>
              <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
            </p>
            <p class='sitestoreproducts_browse_info_date'>
              <?php echo $this->translate('Price');?>
              <?php echo '$' . $item->price; ?>
            </p>
            <p class='sitestoreproducts_browse_info_blurb'>
              <?php echo $this->string()->truncate($this->string()->stripTags($item->description), 300) ?>
            </p>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>

  <?php elseif($this->search): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any products that match your search criteria.');?>
      </span>
    </div>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any products.');?>
        <?php if( $this->canCreate ): ?>
          <?php echo $this->translate('Get started by %1$screating%2$s a new product.', '<a href="'.$this->url(array('action' => 'create'), 'sitestoreproduct_general').'">', '</a>'); ?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>

  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
    //'params' => $this->formValues,
  )); ?>
  
</div>

