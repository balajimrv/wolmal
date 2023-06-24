<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  var tagAction =function(tag) 
  {
    $('tag').value = tag;
    $('filter_form').submit();
  }
</script>

<ul class="sitestore_sidebar_info">
<?php
  $temp_Form_Action = $this->url(array('action' => 'index'), 'sitestore_general', true);
?>

  <form id='filter_form' class='global_form_box' method='get' action='<?php echo $temp_Form_Action; ?>' style='display: none;'>
    <input type="hidden" id="tag" name="tag" value=""/>
    <input type="hidden" id="category" name="category" value=""/>
    <input type="hidden" id="subcategory" name="subcategory" value=""/>
    <input type="hidden" id="subsubcategory" name="subsubcategory" value=""/>
    <input type="hidden" id="categoryname" name="categoryname" value=""/>	
    <input type="hidden" id="subcategoryname" name="subcategoryname" value=""/>
    <input type="hidden" id="subsubcategoryname" name="subsubcategoryname" value=""/>
    <input type="hidden" id="start_date" name="start_date" value="<?php if ($this->start_date)
  echo $this->start_date; ?>"/>
    <input type="hidden" id="end_date" name="end_date" value="<?php if ($this->end_date)
             echo $this->end_date; ?>"/>
  </form>
  <?php if(in_array('ownerPhoto', $this->showContent) || in_array('ownerName', $this->showContent)):?>
		<li>
			<?php if(in_array('ownerPhoto', $this->showContent)):?>
				<?php echo $this->htmlLink($this->sitestore->getParent(), $this->itemPhoto($this->sitestore->getParent(), 'thumb.icon', '' , array('align' => 'center')), array('class'=> 'fleft sitestore_sidebar_info_photo')) ?>
			<?php endif ;?>
			<?php if(in_array('ownerName', $this->showContent)):?>
				<div class="o_hidden">
					<?php echo $this->htmlLink($this->sitestore->getParent(), $this->sitestore->getParent()->getTitle()) ?><br /><?php echo $this->translate("(Owner)"); ?>
				</div>
			<?php endif ;?>  
          	<?php if(in_array('stores', $this->showContent)):?>
				<div class="o_hidden">
          <?php echo $this->translate(array('Store (%s)', 'Stores (%s)', $this->storeCount), $this->htmlLink(array('module'=> 'sitestore', 'controller' => 'index', 'action' => 'view-owner-stores', 'route' => 'default', 'store_id' => $this->sitestore->store_id, 'owner_id' => $this->sitestore->owner_id ), $this->translate($this->locale()->toNumber($this->storeCount)), array('class' => 'smoothbox', 'title' => $this->translate(array('Store (%s)', 'Stores (%s)', $this->storeCount), $this->locale()->toNumber($this->storeCount))))); ?> 
        </div>
			<?php endif ;?> 
		</li>
  <?php endif ;?>

  <?php if(in_array('categoryName', $this->showContent)):?>
		<li>
      <?php
          $temp_general_category = $this->url(array('action' => 'index', 'category_id' => $this->sitestore->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->category_name)), 'sitestoreproduct_product_general');
      ?>
			<?php if ($this->category_name != '' && $this->subcategory_name == '') : ?>
				<?php echo $this->translate('Category:') . ' '; 
				echo $this->htmlLink($temp_general_category, $this->translate($this->category_name)) ?>
			<?php elseif ($this->category_name != '' && $this->subcategory_name != ''): ?> 
				<?php echo $this->translate('Category:') . ' '; ?>
				<?php echo $this->htmlLink($temp_general_category, $this->translate($this->category_name)) ?>
				<?php if (!empty($this->category_name)): echo '&raquo;';endif;
          $temp_general_subcategory = $this->url(array('action' => 'index', 'category_id' => $this->sitestore->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->category_name), 'subcategory_id' => $this->sitestore->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->subcategory_name)), 'sitestoreproduct_product_general');
          echo $this->htmlLink($temp_general_subcategory, $this->translate($this->subcategory_name)) ?>
				<?php if(!empty($this->subsubcategory_name)): echo '&raquo;';
          $temp_general_subsubcategory = $this->url(array('action' => 'index', 'category_id' => $this->sitestore->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->category_name), 'subcategory_id' => $this->sitestore->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->subcategory_name),'subsubcategory_id' => $this->sitestore->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->subsubcategory_name)), 'sitestoreproduct_product_general');
					echo $this->htmlLink($temp_general_subsubcategory, $this->translate($this->subsubcategory_name)) ?>
				<?php endif; ?>
				<?php endif; ?>
		</li>
  <?php endif ;?>

  <?php if (in_array('tags', $this->showContent) && count($this->sitestoreTags) > 0): $tagCount = 0; ?>
    <li>
      <?php echo $this->translate('Tags:'); ?>
      <?php foreach ($this->sitestoreTags as $tag): ?>
        <?php if (!empty($tag->getTag()->text)): ?>
          <?php if (empty($tagCount)): ?>
            <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'>#<?php echo $tag->getTag()->text ?></a>
            <?php $tagCount++;
          else: ?>
            <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'>#<?php echo $tag->getTag()->text ?></a>
          <?php endif; ?>
      <?php endif; ?>
    <?php endforeach; ?>
    </li>
  <?php endif; ?>


  <li>
    <ul>
    
      <?php if (in_array('modifiedDate', $this->showContent)):?>
				<li>
					<?php echo $this->translate('Last updated %s', $this->timestamp($this->sitestore->modified_date)) ?>
				</li>       
      <?php endif;?>

      <?php 

        $statistics = '';

        if(in_array('commentCount', $this->showContent)) {
          $statistics .= $this->translate(array('%s comment', '%s comments', $this->sitestore->comment_count), $this->locale()->toNumber($this->sitestore->comment_count)).', ';
        }

        if(in_array('viewCount', $this->showContent)) {
          $statistics .= $this->translate(array('%s view', '%s views', $this->sitestore->view_count), $this->locale()->toNumber($this->sitestore->view_count)).', ';
        }

        if(in_array('likeCount', $this->showContent)) {
          $statistics .= $this->translate(array('%s like', '%s likes', $this->sitestore->like_count), $this->locale()->toNumber($this->sitestore->like_count)).', ';
        }                 

        if(in_array('followerCount', $this->showContent) &&  isset($this->sitestore->follow_count)) {
          $statistics .= $this->translate(array('%s follower', '%s followers', $this->sitestore->follow_count), $this->locale()->toNumber($this->sitestore->follow_count)).', ';
        }       

        if(in_array('memberCount', $this->showContent) && isset($this->sitestore->member_count)) {
				 $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'storemember.member.title' , 1);
			   if ($this->sitestore->member_title && $memberTitle) : 
				 if ($this->sitestore->member_count == 1) :  $statistics .=  $this->sitestore->member_count . ' member'.', '; else: 	 $statistics .=  $this->sitestore->member_count . ' ' .  $this->sitestore->member_title.', '; endif; 
				 else : 
					$statistics .= $this->translate(array('%s member', '%s members', $this->sitestore->member_count), $this->locale()->toNumber($this->sitestore->member_count)).', ';
				 endif;
        }       

        $statistics = trim($statistics);
        $statistics = rtrim($statistics, ',');

      ?>

      <li><?php echo $statistics; ?></li>
    </ul>
  </li>

  <?php if ($this->sitestore->price > 0 && in_array('price', $this->showContent)): ?>
    <li>
    	<b>
      	<?php echo Engine_Api::_()->sitestore()->getPriceWithCurrency($this->sitestore->price); ?>
      </b>	
    </li>   
  <?php endif; ?>  
  
  <?php if (in_array('location', $this->showContent) && !empty($this->sitestore->location)): ?>
    <li>
      <?php echo $this->translate($this->sitestore->location); ?>&nbsp;-
      <b>
        <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $this->sitestore->store_id, 'resouce_type' => 'sitestore_store'), $this->translate("Get Directions"), array('class' => 'smoothbox')); ?>
      </b>
    </li>
  <?php endif; ?> 
    
</ul>