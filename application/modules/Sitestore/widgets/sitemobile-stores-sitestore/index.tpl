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
<?php if ($this->is_ajax_load):?>
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0);
 ?>

<?php if ($this->paginator->count() > 0): ?>
  <form id='filter_form_store' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'index'), 'sitestore_general', true) ?>' style='display: none;'>
    <input type="hidden" id="store" name="store"  value=""/>
  </form>

  <?php if (empty($this->isajax)) : ?>
       <?php if(!empty($this->tag_name)):?>
        <h3><?php echo $this->tag_name;?>&nbsp;<a href="#" data-rel="back">(X)</a></h3>        
       <?php endif;?>
    <?php if( $this->list_view && $this->grid_view):?>
    <div class="p_view_op ui-store-content">
      <a href="<?php echo $this->url(array('view_selected' => 'list')); ?>" class="ui-link-inherit"> <span  class="sm-widget-block"><i class="ui-icon ui-icon-th-list"></i></span></a>
      <a href="<?php echo $this->url(array('view_selected' => 'grid')); ?>" class="ui-link-inherit" ><span  class="sm-widget-block"><i class="ui-icon ui-icon-th-large"></i></span></a>
    </div>
    <?php endif;?>
    <div id="id" class="ui-store-content">
    <?php endif; ?>

    <?php if ($this->view_selected == "list"): ?> 
      <div id="list_view" class="sm-content-list">
        <ul data-role="listview" data-inset="false" >
          <?php foreach ($this->paginator as $sitestore): ?>
            <li data-icon="arrow-r">
              <a href="<?php echo $sitestore->getHref(); ?>">
                <?php echo $this->itemPhoto($sitestore, 'thumb.icon') ?>
                <h3><?php  echo $this->string()->chunk($this->string()->truncate($sitestore->getTitle(), 45), 10); ?></h3>				
                <p>
                  <?php $contentArray = array(); ?>
                  <?php if (in_array('date', $this->contentDisplayArray)): ?>
                    <?php $contentArray[] = $this->timestamp(strtotime($sitestore->creation_date)) ?> 
                  <?php endif; ?>

                  <?php if (in_array('owner', $this->contentDisplayArray)): ?>
                    <?php $contentArray[] = $this->translate('posted by ') . '<b>' . $sitestore->getOwner()->getTitle() . '</b>'; ?>
                  <?php endif; ?>
                  <?php
                  if (!empty($contentArray)) {
                    echo join(" - ", $contentArray);
                  }
                  ?> 
                </p>
           
                <p>                        
                    <?php $contentArray = array(); ?>
                    <?php
                    
                    if (in_array('likeCount', $this->contentDisplayArray)) {
                      $contentArray[] = $this->translate(array('%s like', '%s likes', $sitestore->like_count), $this->locale()->toNumber($sitestore->like_count));
                    }
                    if (in_array('followCount', $this->contentDisplayArray)) {
                      $contentArray[] = $this->translate(array('%s follower', '%s followers', $sitestore->follow_count), $this->locale()->toNumber($sitestore->follow_count));
                    }

                    if (in_array('reviewCount', $this->contentDisplayArray) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview') && !empty($this->ratngShow)) {
                      $contentArray[] = $this->translate(array('%s review', '%s reviews', $sitestore->review_count), $this->locale()->toNumber($sitestore->review_count));
                    }

                    if (in_array('commentCount', $this->contentDisplayArray)) {
                      $contentArray[] = $this->translate(array('%s comment', '%s comments', $sitestore->comment_count), $this->locale()->toNumber($sitestore->comment_count));
                    }

                    if (in_array('viewCount', $this->contentDisplayArray)) {
                      $contentArray[] = $this->translate(array('%s view', '%s views', $sitestore->view_count), $this->locale()->toNumber($sitestore->view_count));
                    }
                    ?>
                    <?php
                    if (!empty($contentArray)) {
                      echo join(" - ", $contentArray);
                    }
                    ?>  
                </p>
                
                <p> 
                  <?php if (in_array('price', $this->contentDisplayArray) && !empty($sitestore->price) && $this->enablePrice): ?>            
                  <?php echo  $this->translate("Price: ") . Engine_Api::_()->sitestore()->getPriceWithCurrency($sitestore->price); ?>
                  <?php endif; ?>
                </p>
                <p>
                  <?php if (in_array('location', $this->contentDisplayArray) && !empty($sitestore->location) && $this->enableLocation): ?>
                    <?php
                    $locationId = Engine_Api::_()->getDbTable('locations', 'sitestore')->getLocationId($sitestore->store_id, $sitestore->location);
                    echo $this->translate("Location: ") . $this->translate($sitestore->location);
                    ?>     
                  <?php endif; ?>                 
                </p>

                <p>
                  <?php if (in_array('ratings', $this->contentDisplayArray) && $this->ratngShow): ?>
                    <?php if (($sitestore->rating > 0)): ?>
                      <?php
                      $currentRatingValue = $sitestore->rating;
                      $difference = $currentRatingValue - (int) $currentRatingValue;
                      if ($difference < .5) {
                        $finalRatingValue = (int) $currentRatingValue;
                      } else {
                        $finalRatingValue = (int) $currentRatingValue + .5;
                      }
                      ?>
                      <span class="list_rating_star" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
                        <?php for ($x = 1; $x <= $sitestore->rating; $x++): ?>
                          <span class="rating_star_generic rating_star" ></span>
                        <?php endfor; ?>
                      <?php if ((round($sitestore->rating) - $sitestore->rating) > 0): ?>
                          <span class="rating_star_generic rating_star_half" ></span>
                      <?php endif; ?>
                      </span>		
                    <?php endif; ?>
                  <?php endif; ?>


                  <?php if (in_array('closed', $this->contentDisplayArray) && $sitestore->closed): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/close.png', '', array('class' => 'icon', 'title' => $this->translate('Closed'))) ?>

                  <?php endif; ?> 

                  <?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.fs.markers', 1)) : ?>
                    <?php if (in_array('featured', $this->contentDisplayArray) && ($sitestore->sponsored == 1)): ?>
                      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
                    <?php endif; ?>
                    <?php if (in_array('sponsored', $this->contentDisplayArray) && ($sitestore->featured == 1)): ?>
          <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
        <?php endif; ?>
      <?php endif; ?>

                </p>
              </a>
            </li>
      <?php endforeach; ?>
        </ul>
      </div>
        <?php endif; ?>
        <?php if ($this->view_selected == "grid"): ?> 
      <div id="grid_view">
        <ul class="p_list_grid">
          <?php foreach ($this->paginator as $sitestore): ?>
            <li style="height:<?php echo $this->columnHeight ?>px;">
              <a href="<?php echo $sitestore->getHref(); ?>" class="ui-link-inherit">
                <div class="p_list_grid_top_sec">
                  <div class="p_list_grid_img">
                    <?php $url = $this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/nophoto_store_thumb_profile.png';
                    $temp_url = $sitestore->getPhotoUrl('thumb.profile');
                    if (!empty($temp_url)): $url = $sitestore->getPhotoUrl('thumb.profile');
                      endif; ?>
                    <span style="background-image: url(<?php echo $url; ?>);"> </span>
                  </div>
                  <div class="p_list_grid_title">
                    <span><?php echo $this->string()->chunk($this->string()->truncate($sitestore->getTitle(), 45), 10); ?></span>
                  </div>
                </div>  
                
                <?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.fs.markers', 1)) : ?>
                  <?php if (in_array('sponsored', $this->contentDisplayArray)&& ($sitestore->sponsored == 1)): ?>
                    <div class="sm-sl" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.color', '#fc0505'); ?>;'>
                      <?php echo $this->translate('SPONSORED'); ?>     				
                    </div>
                  <?php endif; ?>
                  <?php if (in_array('featured', $this->contentDisplayArray)  && ($sitestore->featured == 1)): ?>
                    <i title="<?php echo $this->translate('Featured')?>" class="sm-fl"></i>
                  <?php endif; ?>
                <?php endif; ?>
                
                <div class="p_list_grid_info">	
                  <span class="p_list_grid_stats">
                      <?php if (in_array('ratings', $this->contentDisplayArray) && $this->ratngShow): ?>
                        <?php if (($sitestore->rating > 0)): ?>
                            <?php
                            $currentRatingValue = $sitestore->rating;
                            $difference = $currentRatingValue - (int) $currentRatingValue;
                            if ($difference < .5) {
                              $finalRatingValue = (int) $currentRatingValue;
                            } else {
                              $finalRatingValue = (int) $currentRatingValue + .5;
                            }
                            ?>
                          <span class="list_rating_star" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
          <?php for ($x = 1; $x <= $sitestore->rating; $x++): ?>
                              <span class="rating_star_generic rating_star" ></span>
                          <?php endfor; ?>
                          <?php if ((round($sitestore->rating) - $sitestore->rating) > 0): ?>
                              <span class="rating_star_generic rating_star_half" ></span>
                          <?php endif; ?>
                          </span>		
                        <?php endif; ?>
                      <?php endif; ?>
                      <span class="fright">
                        <?php if (in_array('closed', $this->contentDisplayArray) && $sitestore->closed): ?>
                          <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/close.png', '', array('class' => 'icon', 'title' => $this->translate('Closed'))) ?>								
                        <?php endif;?>                 
                        <?php if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.fs.markers', 1)) :?>
                          <?php if (in_array('sponsored', $this->contentDisplayArray) && $sitestore->sponsored == 1): ?>
                            <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
                          <?php endif; ?>
                          <?php if (in_array('featured', $this->contentDisplayArray) && $sitestore->featured == 1): ?>
                            <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/featured.png', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
                          <?php endif; ?>
                        <?php endif; ?>
                        </span>
                    </span>
                   <span class="p_list_grid_stats">
                      <?php if (in_array('date', $this->contentDisplayArray)): ?>
                        <?php echo $this->timestamp(strtotime($sitestore->creation_date)) ?> 
                      <?php endif; ?>
                    </span>
                    <span class="p_list_grid_stats">
                      <?php if (in_array('owner', $this->contentDisplayArray)): ?>
                        <?php echo $this->translate('posted by ') . '<b>' . $sitestore->getOwner()->getTitle() . '</b>'; ?>
                      <?php endif; ?>
                    </span>
                   <span class="p_list_grid_stats">                                            
                    <?php $contentArray = array(); ?>
                    <?php
                    
                    if (in_array('likeCount', $this->contentDisplayArray)) {
                      $contentArray[] = $this->translate(array('%s like', '%s likes', $sitestore->like_count), $this->locale()->toNumber($sitestore->like_count));
                    }
                    if (in_array('followCount', $this->contentDisplayArray)) {
                      $contentArray[] = $this->translate(array('%s follower', '%s followers', $sitestore->follow_count), $this->locale()->toNumber($sitestore->follow_count));
                    }

                    if (in_array('reviewCount', $this->contentDisplayArray) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview') && !empty($this->ratngShow)) {
                      $contentArray[] = $this->translate(array('%s review', '%s reviews', $sitestore->review_count), $this->locale()->toNumber($sitestore->review_count));
                    }

                    if (in_array('commentCount', $this->contentDisplayArray)) {
                      $contentArray[] = $this->translate(array('%s comment', '%s comments', $sitestore->comment_count), $this->locale()->toNumber($sitestore->comment_count));
                    }

                    if (in_array('viewCount', $this->contentDisplayArray)) {
                      $contentArray[] = $this->translate(array('%s view', '%s views', $sitestore->view_count), $this->locale()->toNumber($sitestore->view_count));
                    }
                    ?>
                    <?php
                    if (!empty($contentArray)) {
                      echo join(" - ", $contentArray);
                    }
                    ?>  
                    </span>
                    <span class="p_list_grid_stats">
                      <?php if (in_array('price', $this->contentDisplayArray) && !empty($sitestore->price) && $this->enablePrice): ?>             
                      <?php echo $this->translate("Price: ") . Engine_Api::_()->sitestore()->getPriceWithCurrency($sitestore->price); ?>
                      <?php endif; ?>
                    </span>
                    <span class="p_list_grid_stats">
                      <?php if (in_array('location', $this->contentDisplayArray) && !empty($sitestore->location) && $this->enableLocation): ?>
                      <?php
                      $locationId = Engine_Api::_()->getDbTable('locations', 'sitestore')->getLocationId($sitestore->store_id, $sitestore->location);
                      echo $this->translate("Location: ") . $this->translate($sitestore->location);
                      ?>     
                      <?php endif; ?>                     
                    </span>
                    
                </div>   
              </a>
            </li>
      <?php endforeach; ?>
        </ul>
      </div>

  <?php endif; ?>  
  <?php if (empty($this->isajax)) : ?>
    <?php if ($this->paginator->count() > 1): ?>
      <?php
      echo $this->paginationControl($this->paginator, null, null, array(
          'query' => $this->formValues,
      ));
      ?>
      <?php endif; ?> 
    </div> 
    <?php endif; ?>



<?php elseif ($this->search): ?>
  <div class="tip">
  <?php
  if (Engine_Api::_()->sitestore()->hasPackageEnable()):
    $createUrl = $this->url(array('action' => 'index'), 'sitestore_packages');
  else:
    $createUrl = $this->url(array('action' => 'create'), 'sitestore_general');
  endif;
  ?>
    <span> <?php echo $this->translate('Nobody has created a store with that criteria.'); ?>
    </span> 
  </div>
<?php else: ?>
  <?php
  if (empty($this->sitestore_generic)) {
    exit();
  }
  ?>
  <div class="tip"> <span> <?php echo $this->translate('No Stores have been created yet.'); ?>
    </span>
  </div>
<?php endif; ?>
<?php else:?>
<div id="layout_sitestore_sitemobile_stores_sitestore_<?php echo $this->identity; ?>">
</div>
<script type="text/javascript">
    var requestParams = $.extend(<?php echo json_encode($this->paramsLocation);?>, {'content_id': '<?php echo $this->identity;?>'});
    var params = {
      'detactLocation': <?php echo $this->detactLocation; ?>,
      'responseContainer': 'layout_sitestore_sitemobile_stores_sitestore_<?php echo $this->identity; ?>',
      'locationmiles': <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>,
      requestParams: requestParams
    };
    sm4.core.runonce.add(function() {
          setTimeout((function() {
            $.mobile.loading().loader("show");
    }), 100);
          sm4.core.locationBased.startReq(params);
    });
</script>  
<?php endif; ?>