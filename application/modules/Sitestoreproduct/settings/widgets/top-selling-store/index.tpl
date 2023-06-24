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
<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl'; ?>
<?php if ($this->viewType=='listview'): ?>
<ul class="sitestore_sidebar_list">
<?php foreach( $this->top_selling_store as $sitestoreproduct ): ?>
  <li class="sitestoreproduct_q_v_wrap">
    <?php echo $this->htmlLink($sitestoreproduct->getHref(), $this->itemPhoto($sitestoreproduct, 'thumb.icon', array('align' => 'left'))) ?>
    <div class='sitestore_sidebar_list_info'>
      <div class='sitestore_sidebar_list_title'>
        <?php echo $this->htmlLink($sitestoreproduct->getHref(), Engine_Api::_()->sitestoreproduct()->truncation($sitestoreproduct->getTitle(), $this->title_truncation), array('title' => $sitestoreproduct->getTitle())).'<br />' ?>
      </div>
      <div class='sitestore_sidebar_list_details'>
        <?php if ($this->category_name != '') : ?>
          <?php echo $this->translate('Category:'); ?>
          <?php
            echo $this->htmlLink($this->url(array('category_id' => $this->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->category_name)), 'sitestore_general_category'), $this->translate($this->category_name));
          ?>
        <?php elseif ($this->subcategory_name != ''): ?> 
          <?php echo $this->translate('Sub Category:'); ?>
          <?php 
            echo $this->htmlLink($this->url(array('category_id' => $this->subcategory_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->subcategory_name), 'subcategory_id' => $this->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->subcategory_name)), 'sitestore_general_subcategory'), $this->translate($this->subcategory_name));                          
          ?>
				<?php elseif ($this->subsubcategory_name != ''):  ?> 
          <?php echo $this->translate('3rd Level Category:'); ?>
          <?php
            echo $this->htmlLink($this->url(array('category_id' => $this->subcategory_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->subsubcategory_name), 'subsubcategory_id' => $this->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->subsubcategory_name)), 'sitestore_general_subcategory'), $this->translate($this->subsubcategory_name)) ;                              
           ?>
				<?php endif; ?>
      </div>

      <div class='sitestore_sidebar_list_details'>
        <?php if( $this->display_by == 'Price' ):                
                echo $this->translate('Total Sold : %s', Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($sitestoreproduct->grand_total));
              else:
                echo $this->translate('Sold Item : %s', $this->locale()->toNumber($sitestoreproduct->item_count));
              endif; ?>
      </div>

      <div class='sitestore_sidebar_list_details'>
        <?php if(!empty($this->statistics)): ?>  
                <p>
                  <?php 
                    $statistics = '';

                    if(in_array('commentCount', $this->statistics)) {
                      $statistics .= $this->translate(array('%s comment', '%s comments', $sitestoreproduct->comment_count), $this->locale()->toNumber($sitestoreproduct->comment_count)).', ';
                    }
                    
                    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview') ) :
                      if(in_array('reviewCount', $this->statistics) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2)) {
                        $statistics .= $this->translate(array('%s review', '%s reviews', $sitestoreproduct->review_count), $this->locale()->toNumber($sitestoreproduct->review_count)).', ';
                      }
                    endif;
                    
                    if(in_array('followCount', $this->statistics)) {
                      $statistics .= $this->translate(array('%s follow', '%s follows', $sitestoreproduct->follow_count), $this->locale()->toNumber($sitestoreproduct->follow_count)).', ';
                    }

                    if(in_array('viewCount', $this->statistics)) {
                      $statistics .= $this->translate(array('%s view', '%s views', $sitestoreproduct->view_count), $this->locale()->toNumber($sitestoreproduct->view_count)).', ';
                    }

                    if(in_array('likeCount', $this->statistics)) {
                      $statistics .= $this->translate(array('%s like', '%s likes', $sitestoreproduct->like_count), $this->locale()->toNumber($sitestoreproduct->like_count)).', ';
                    }                 

                    $statistics = trim($statistics);
                    $statistics = rtrim($statistics, ',');

                  ?>

                  <?php echo $statistics; ?>
                </p>
              <?php endif; ?>
      </div>
      <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating); ?>
    </div>
  </li>
<?php endforeach; ?>
</ul>

<?php else: ?>
  <?php $isLarge = ($this->columnWidth>200); ?>
  <ul class="sitestore_img_view sitestore_grid_view_sidebar">
    <?php foreach ($this->top_selling_store as $sitestoreproduct): ?>
      <li class="sitestore_browse_thumb <?php if($isLarge): ?>largephoto<?php endif;?>" style="width: <?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;">
        <div class="sitestore_browse_thumb_list"> 
          <a href="<?php echo $sitestoreproduct->getHref() ?>">
            <?php 
            $url = $sitestoreproduct->getPhotoUrl($isLarge ? 'thumb.midum' : 'thumb.normal');
            if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/nophoto_product_thumb_normal.png';
            endif;
            ?>
            <span style="background-image: url(<?php echo $url; ?>); <?php if($isLarge): ?> height:200px; <?php endif;?> "></span>
          </a>
          <div class="sitestore_browse_title">
            <?php echo $this->htmlLink($sitestoreproduct->getHref(), Engine_Api::_()->sitestoreproduct()->truncation($sitestoreproduct->getTitle(), $this->title_truncation), array('title' => $sitestoreproduct->getTitle())) ?>
          </div>
        </div>
       
        <div class="sitestore_browse_thumb_info">
          <div class="sitestore_browse_thumb_stats seaocore_txt_light">
            <?php if ($this->category_name != '') : ?>
              <?php echo $this->translate('Category:'); ?>
              <?php 
                echo $this->htmlLink($this->url(array('category_id' => $this->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->category_name)), 'sitestore_general_category'), $this->translate($this->category_name));  
              ?>
            <?php elseif ($this->subcategory_name != ''): ?> 
              <?php echo $this->translate('Sub Category:'); ?>
              <?php 
                echo $this->htmlLink($this->url(array('category_id' => $this->subcategory_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->subcategory_name), 'subcategory_id' => $this->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->subcategory_name)), 'sitestore_general_subcategory'), $this->translate($this->subcategory_name));            
              ?>
            <?php elseif ($this->subsubcategory_name != ''):  ?> 
              <?php echo $this->translate('3rd Level Category:'); ?>
              <?php 
              echo $this->htmlLink($this->url(array('category_id' => $this->subcategory_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->subsubcategory_name), 'subsubcategory_id' => $this->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->subsubcategory_name)), 'sitestore_general_subcategory'), $this->translate($this->subsubcategory_name));
              ?>
            <?php endif; ?>
          </div>
          <?php if(!empty($this->statistics)): ?>    
            <div class="sitestore_browse_thumb_stats seaocore_txt_light">
            <?php 
                $statistics = '';
                if(in_array('commentCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s comment', '%s comments', $sitestoreproduct->comment_count), $this->locale()->toNumber($sitestoreproduct->comment_count)).', ';
                }

                if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview') ) :
                  if(in_array('reviewCount', $this->statistics) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2)) {
                    $statistics .= $this->translate(array('%s review', '%s reviews', $sitestoreproduct->review_count), $this->locale()->toNumber($sitestoreproduct->review_count)).', ';
                  }
                endif;

                if(in_array('followCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s follow', '%s follows', $sitestoreproduct->follow_count), $this->locale()->toNumber($sitestoreproduct->follow_count)).', ';
                }

                if(in_array('viewCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s view', '%s views', $sitestoreproduct->view_count), $this->locale()->toNumber($sitestoreproduct->view_count)).', ';
                }

                if(in_array('likeCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s like', '%s likes', $sitestoreproduct->like_count), $this->locale()->toNumber($sitestoreproduct->like_count)).', ';
                }                 

                $statistics = trim($statistics);
                $statistics = rtrim($statistics, ',');

              ?>
              <?php echo $statistics; ?> 
            </div>
          <?php endif; ?>
          <div class="sitestore_browse_thumb_stats seaocore_txt_light">
            <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating); ?>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
