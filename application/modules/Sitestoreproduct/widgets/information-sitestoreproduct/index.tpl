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

<ul class="sr_sitestoreproduct_sidebar_list_info sr_sitestoreproduct_side_widget">
	<li>
		<?php if(in_array('ownerPhoto', $this->showContent)):?>
		<?php echo $this->htmlLink($this->sitestoreproduct->getParent(), $this->itemPhoto($this->sitestoreproduct->getParent(), 'thumb.icon', '' , array('align' => 'center')), array('class'=> 'sr_sitestoreproduct_sidebar_list_info_photo fleft')) ?>
		<?php endif ;?>
    <?php if(in_array('ownerName', $this->showContent)):?>
    	<div class="o_hidden">
      	<?php echo $this->htmlLink($this->sitestoreproduct->getParent(), $this->sitestoreproduct->getParent()->getTitle()) ?><br /><?php echo $this->translate("(Owner)"); ?>
      </div>
    <?php endif ;?>  

    <?php if(in_array('stores', $this->showContent)):?>
      <div class="o_hidden">
        <?php echo $this->translate(array('Store (%s)', 'Stores (%s)', $this->storeCount), $this->htmlLink(array('module'=> 'sitestore', 'controller' => 'index', 'action' => 'view-owner-stores', 'route' => 'default', 'store_id' => $this->storeObj->store_id, 'owner_id' => $this->storeObj->owner_id ), $this->translate($this->locale()->toNumber($this->storeCount)), array('class' => 'smoothbox', 'title' => $this->translate(array('Store (%s)', 'Stores (%s)', $this->storeCount), $this->locale()->toNumber($this->storeCount))))); ?> 
			</div>
		<?php endif ;?> 
	</li>
      
  <?php if (in_array('tags', $this->showContent) && count($this->sitestoreproductTags) > 0): $tagCount = 0; ?>
    <li>
      <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct_brands', 1)): ?><?php echo $this->translate('Brand'); ?><?php else:?><?php echo $this->translate('Tags'); ?><?php endif; ?> - 
      <?php foreach ($this->sitestoreproductTags as $tag): ?>
        <?php if (!empty($tag->getTag()->text)): ?>
          <?php $tag->getTag()->text = $this->string()->escapeJavascript($tag->getTag()->text) ?>
          <?php if (empty($tagCount)): ?>
            <a href='<?php echo $this->url(array('action' => 'index'), "sitestoreproduct_general"); ?>?tag=<?php echo urlencode($tag->getTag()->text) ?>&tag_id=<?php echo $tag->getTag()->tag_id ?>'>#<?php echo $tag->getTag()->text ?></a>
            <?php $tagCount++;
          else: ?>
            <a href='<?php echo $this->url(array('action' => 'index'), "sitestoreproduct_general"); ?>?tag=<?php echo urlencode($tag->getTag()->text) ?>&tag_id=<?php echo $tag->getTag()->tag_id ?>'>#<?php echo $tag->getTag()->text ?></a>
          <?php endif; ?>
        <?php endif; ?>
      <?php endforeach; ?>
    </li>
  <?php endif; ?>
    
  <?php if(in_array('category', $this->showContent)): ?>
		<li>
        <div class="sitestoreproduct_grid_stats clr">
                <a href="<?php echo $this->url(array('category_id' => $this->sitestoreproduct->category_id, 'categoryname' => $this->sitestoreproduct->getCategory()->getCategorySlug()), "". $this->categoryRouteName ."" ); ?>"> <?php echo $this->translate($this->sitestoreproduct->getCategory()->getTitle(true)) ?> </a>
              </div>
		</li>
  <?php endif ;?>
    
	<li>
		<ul>
      <?php if (in_array('modifiedDate', $this->showContent)):?>
        <li>
          <?php echo $this->translate('Last updated %s', $this->timestamp($this->sitestoreproduct->modified_date)) ?>
        </li>        
      <?php endif; ?>
        
      <?php 

        $statistics = '';

        if(in_array('commentCount', $this->showContent)) {
          $statistics .= $this->translate(array('%s comment', '%s comments', $this->sitestoreproduct->comment_count), $this->locale()->toNumber($this->sitestoreproduct->comment_count)).', ';
        }

        if(in_array('viewCount', $this->showContent)) {
          $statistics .= $this->translate(array('%s view', '%s views', $this->sitestoreproduct->view_count), $this->locale()->toNumber($this->sitestoreproduct->view_count)).', ';
        }

        if(in_array('likeCount', $this->showContent)) {
          $statistics .= $this->translate(array('%s like', '%s likes', $this->sitestoreproduct->like_count), $this->locale()->toNumber($this->sitestoreproduct->like_count)).', ';
        }                 

        $statistics = trim($statistics);
        $statistics = rtrim($statistics, ',');

      ?>

      <?php echo $statistics; ?>
        
		</ul>
    
	</li>
  
  <?php if (in_array('location', $this->showContent) && !empty($this->sitestoreproduct->location) && $this->enableLocation): ?>
    <li>
      <?php echo $this->translate($this->sitestoreproduct->location); ?>&nbsp;-
      <b>
        <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $this->sitestoreproduct->product_id, 'resouce_type' => 'sitestoreproduct_product'), $this->translate("Get Directions"), array('class' => 'smoothbox')); ?>
      </b>
    </li>
  <?php endif; ?> 

</ul>