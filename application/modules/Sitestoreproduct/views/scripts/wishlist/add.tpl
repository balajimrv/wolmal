<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: add.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php
  $this->headLink()
		->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');
?>
<?php if($this->success == 1): ?>
	<div class="sr_sitestoreproduct_wishlist_popup_list">
    <div class='sr_sitestoreproduct_wishlist_popup_item'>
    	<?php echo $this->htmlLink($this->sitestoreproduct->getHref(), $this->itemPhoto($this->sitestoreproduct, 'thumb.normal'), array('target' => '_blank')); ?>
    </div>
		<div class="sr_sitestoreproduct_wishlist_popup_item_detail">
			<div class="sr_sitestoreproduct_wishlist_popup_item_title">		
				<?php echo $this->htmlLink($this->sitestoreproduct->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($this->sitestoreproduct->getTitle(), 99), array('class' =>'sr_sitestoreproduct_wishlist_popup_item_title', 'target' => '_blank', 'title' => $this->sitestoreproduct->getTitle())) ?>
			</div>
			<?php if(Count($this->wishlistNewDatas)): ?>
	  		<b><?php echo $this->translate("You have added this product to the wishlists:"); ?></b>
	    	<ul class="clr">
	      	<?php foreach($this->wishlistNewDatas as $wishlistNewData): ?>
	      		<li><?php echo $this->htmlLink($wishlistNewData->getHref(),$wishlistNewData->getTitle(), array('target' => '_blank')) ?></li>
	      	<?php endforeach; ?>
	    	</ul>
	   	<?php endif; ?>
	    <?php if(Count($this->wishlistOldDatas)): ?>
	      <b><?php echo $this->translate("You have removed this product from the wishlists:"); ?></b>
		    <ul class="clr">
		      <?php foreach($this->wishlistOldDatas as $wishlistOldData): ?>
		      <li><?php echo $this->htmlLink($wishlistOldData->getHref(),$wishlistOldData->getTitle(), array('target' => '_blank')) ?></li>
		      <?php endforeach; ?>
		    </ul>
	    <?php endif; ?>
	  </div>     
    <div class="clr mtop10 fleft widthfull">
    	<table width="100%">
    		<tr>
	    		<td align="left"><button onclick="javascript:parent.Smoothbox.close()"><?php echo $this->translate('Close'); ?></button></td>
	    		<td class="sr_sitestoreproduct_wishlist_popup_item_detail_more" align="right">
	      		<?php echo $this->htmlLink(array('route' => "sitestoreproduct_wishlist_general", 'action' => 'browse'), $this->translate('Browse Wishlists &raquo;'), array('target' => '_blank')) ?>
	    		</td>
	    	</tr>
	    </table>
    </div>
  </div>
<?php else: ?>
  <?php if(empty($this->can_add)):?>
    <div class="global_form_popup">	
      <div class="tip">
        <span>
          <?php echo $this->translate("Oops! Something went wrong and you can not add this Product to your wishlist. Please try again after sometime."); ?>
        </span>
      </div>
      <button onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("Close");?></button>
    </div>
    <?php return; ?>
  <?php endif;?> 
  <div class='sr_sitestoreproduct_wishlist_popup'>
    <?php echo $this->form->render($this) ?>
  </div>  
<?php endif; ?> 

<?php if( @$this->closeSmoothbox ): ?>
	<script type="text/javascript">
		TB_close();
	</script>
<?php endif; ?>  