<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: detail.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php
  $reviewApi = Engine_Api::_()->sitestoreproduct();
  $expirySettings = $reviewApi->expirySettings();
?>
<div class="global_form_popup sr_sitestoreproduct_product_details_view">
	<h3><?php echo $this->translate('Product Details'); ?></h3>
	<div class="top clr">
		<?php echo $this->htmlLink($this->sitestoreproductDetail->getHref(), $this->itemPhoto($this->sitestoreproductDetail, 'thumb.icon'), array('target' => '_blank')); ?>
    <?php echo $this->htmlLink($this->sitestoreproductDetail->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($this->sitestoreproductDetail->getTitle(), 19), array('target' => '_blank', 'title' => $this->sitestoreproductDetail->getTitle())) ?>
	</div>
	<table class="clr">
		<tr>
			<td width="200"><b><?php echo $this->translate('Title :'); ?></b></td>
			<td><?php echo $this->translate($this->sitestoreproductDetail->getTitle()); ?>&nbsp;&nbsp;</td>
			<tr >
				<td><b><?php echo $this->translate(' 	Owner :'); ?></b></td>
				<td><?php echo  $this->translate($this->sitestoreproductDetail->getOwner()->getTitle());?></td>
			</tr>
      
      <?php if ($this->sitestoreproductDetail->category_id) : ?>
      <tr>
        
          <?php $category_id = $this->sitestoreproductDetail->category_id; ?>
          <?php $category = Engine_Api::_()->getItem('sitestoreproduct_category', $category_id); ?>
          <?php $categoryName = $category->category_name; ?>
          <?php $categorySlug = $category->getCategorySlug() ?>
          <td><b><?php echo $this->translate('Category:'); ?></b></td> 
          <td>
            <?php echo $this->htmlLink($this->url(array('category_id' => $category_id, 'categoryname' => $categorySlug), "". $this->categoryRouteName .""), $this->translate($categoryName), array('target' => '_blank')) ?>
          </td>	    
        
      </tr>	
      <?php if ($this->sitestoreproductDetail->subcategory_id) : ?>
      <tr>
        
          <?php $subcategory_id = $this->sitestoreproductDetail->subcategory_id; ?>
          <?php $subcategory = Engine_Api::_()->getItem('sitestoreproduct_category', $subcategory_id); ?>
          <?php $subcategoryName = $subcategory->category_name; ?>
          <?php $subcategorySlug = $subcategory->getCategorySlug() ?>
          <td><b><?php echo $this->translate('Subcategory:'); ?></b></td> 
          <td>
            <?php echo $this->htmlLink($this->url(array('category_id' => $category_id, 'categoryname' => $categorySlug, 'subcategory_id' => $subcategory_id, 'subcategoryname' => $subcategorySlug), 'sitestoreproduct_general_subcategory'), $this->translate($subcategoryName), array('target' => '_blank')) ?>
          </td>	    
        
      </tr>
      <tr>
        <?php if ($this->sitestoreproductDetail->subsubcategory_id) : ?>
          <?php $subsubcategory_id = $this->sitestoreproductDetail->subsubcategory_id; ?>
          <?php $subsubcategory = Engine_Api::_()->getItem('sitestoreproduct_category', $subsubcategory_id); ?>
          <?php $subsubCategoryName = $subsubcategory->category_name; ?>
          <?php $subsubcategorySlug = $subsubcategory->getCategorySlug() ?>
          <td><b><?php echo $this->translate('3%s Level Category:', "<sup>rd</sup>"); ?></b></td>
          <td>
            <?php echo $this->htmlLink($this->url(array('category_id' => $category_id, 'categoryname' => $categorySlug, 'subcategory_id' => $subcategory_id, 'subcategoryname' => $subcategorySlug, 'subsubcategory_id' => $subsubcategory_id, 'subsubcategoryname' => $subsubcategorySlug), 'sitestoreproduct_general_subsubcategory'), $this->translate($subsubCategoryName), array('target' => '_blank')) ?>
          </td>
        <?php endif; ?>
      </tr>    
      <?php endif; ?>
      <?php endif; ?>
		
			<tr>
				<td><b><?php echo $this->translate('Featured :'); ?></b></td>
				<td>
					<?php if ($this->sitestoreproductDetail->featured)
						echo $this->translate('Yes');
						else
						echo $this->translate("No") ;?>
				</td>
			</tr>

			<tr>
				<td><b><?php echo $this->translate('Sponsored :'); ?></b></td>
				<td> <?php if ($this->sitestoreproductDetail->sponsored)
						echo $this->translate('Yes');
						else
						echo $this->translate("No") ;?>
				</td>
			</tr>

			<tr>
				<td><b><?php echo $this->translate('Creation Date :'); ?></b></td>
				<td>
				<?php echo $this->translate(gmdate('M d,Y, g:i A',strtotime($this->sitestoreproductDetail->creation_date))); ?>
				</td>
			</tr>
      
			<tr>
				<td><b><?php echo $this->translate('Last Modified Date :'); ?></b></td>
				<td>
				<?php echo $this->translate(gmdate('M d,Y, g:i A',strtotime($this->sitestoreproductDetail->modified_date))); ?>
				</td>
			</tr>      

			<tr>
				<td><b><?php echo $this->translate('Approved :'); ?></b></td>
				<td>
					<?php  if ($this->sitestoreproductDetail->approved)
									echo $this->translate('Yes');
								else
									echo $this->translate("No") ;?>
				</td>
			</tr>

			<tr>
				<td><b><?php echo $this->translate('Approved Date :'); ?></b></td>
				<td>
					<?php if(!empty($this->sitestoreproductDetail->approved_date)): ?>
					<?php echo $this->translate(date('M d,Y, g:i A',strtotime($this->sitestoreproductDetail->approved_date))); ?>
					<?php else:?>
					<?php echo $this->translate('-'); ?>
					<?php endif;?>
				</td>
			</tr>

      <?php if ($this->sitestoreproductDetail->price > 0): ?>
				<tr>
					<td><b><?php echo $this->translate('Price :'); ?></b></td>
					<td><?php echo $this->sitestoreproductDetail->price ?></td>
				</tr>
			<?php endif; ?>
        
      <tr>
        <td><b><?php echo $this->translate('Added in number of Wishlists:'); ?></b></td>
        <td><?php echo Engine_Api::_()->getDbTable('wishlistmaps', 'sitestoreproduct')->getWishlistsProductCount($this->sitestoreproductDetail->product_id) ?></td>
      </tr>     
		
			<tr>
				<td><b><?php echo $this->translate('Views :'); ?></b></td>
				<td><?php echo $this->translate($this->sitestoreproductDetail->view_count ) ;?> </td>
			</tr>

			<tr>
				<td><b><?php echo $this->translate('Comments :'); ?></b></td>
				<td><?php echo $this->translate($this->sitestoreproductDetail->comment_count ) ;?> </td>
			</tr>

			<tr>
				<td><b><?php echo $this->translate('Likes :'); ?></b></td>
				<td><?php echo $this->translate($this->sitestoreproductDetail->like_count ) ;?> </td>
			</tr>

			<tr>
				<td><b><?php echo $this->translate('Reviews :'); ?></b></td>
				<td><?php echo $this->sitestoreproductDetail->review_count ;?> </td>
			</tr>
      <tr>           
				<td><b><?php echo $this->translate('Average Rating :'); ?></b></td>
				<td>
          <?php if($this->sitestoreproductDetail->rating_avg > 0):?>
            <?php echo $this->showRatingStarSitestoreproduct($this->sitestoreproductDetail->rating_avg, 'user', 'small-star'); ?>
          <?php else: ?>
          ---
          <?php endif; ?>
				</td>
			</tr>     
			<tr>           
				<td><b><?php echo $this->translate('Editor Rating :'); ?></b></td>
				<td>
          <?php if($this->sitestoreproductDetail->rating_editor > 0):?>
            <?php echo $this->showRatingStarSitestoreproduct($this->sitestoreproductDetail->rating_editor, 'editor', 'small-star'); ?>
          <?php else: ?>
          ---
          <?php endif; ?>
				</td>
			</tr>
      
      <tr>           
				<td><b><?php echo $this->translate('User Rating :'); ?></b></td>
				<td>
          <?php if($this->sitestoreproductDetail->rating_users > 0):?>
            <?php echo $this->showRatingStarSitestoreproduct($this->sitestoreproductDetail->rating_users, 'user', 'small-star'); ?>
          <?php else: ?>
          ---
          <?php endif; ?>
				</td>
			</tr>
      <?php if ($expirySettings == 2):
        $exp = $this->sitestoreproductDetail->getExpiryTime(); ?>
        <tr>           
          <td><b><?php echo $this->translate('Expiry Date :'); ?></b></td>
          <td>
            <?php if ($exp): ?>
              <?php echo date('M d,Y, g:i A',$exp); ?>
            <?php else: ?>
              ---
            <?php endif; ?>
          </td>
        </tr>
      <?php endif; ?> 
        <?php if ($expirySettings == 1): ?>
        <tr>           
          <td><b><?php echo $this->translate('End Date :'); ?></b></td>
          <td>
            <?php if ($this->sitestoreproductDetail->end_date && $this->sitestoreproductDetail->end_date !='0000-00-00 00:00:00'): ?>
              <?php echo date('M d,Y, g:i A',strtotime($this->sitestoreproductDetail->end_date)); ?>
            <?php else: ?>
              ---
            <?php endif; ?>
          </td>
        </tr>
      <?php endif; ?> 

		</table>
	<br />
	<button  onclick='javascript:parent.Smoothbox.close()' ><?php echo $this->translate('Close')  ?></button>
</div>

<?php if (@$this->closeSmoothbox): ?>
	<script type="text/javascript">
		TB_close();
	</script>
<?php endif; ?>