<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: compare.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php if (empty($this->lists)): ?>
  <div id="sr_sitestoreproduct_compareview">
    <a href="javascript: history.go(-1)" class="buttonlink fright sr_sitestoreproduct_item_icon_back"><?php echo $this->translate("Go back to previous page") ?></a>
  </div>

  <div class='tip'>
    <span>
      <?php echo $this->translate('Please select some products for the comparison.') ?>
    </span>
  </div>
<?php else: ?>
  <?php 
    $this->headTranslate(array('Compare All', 'Remove All', 'Compare', 'Show Compare Bar', 'Please select more than one product for the comparison.', 'Hide Compare Bar'));
  $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');
  $this->headScript()
          ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/scripts/core.js')->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/seaomooscroll/SEAOMooHorizontalScrollBar.js')
          ;
  ?>
  <div id="sr_sitestoreproduct_compareview">
    <div class="sr_sitestoreproduct_product_breadcrumb o_hidden">
    	<?php echo $this->translate("%s Compare"." ".$this->translate($this->category->getTitle()), $this->heading." &raquo;") ?>
    </div>
    
    <div class="sr_sitestoreproduct_compareview_header o_hidden mbot10">
    	<a href="javascript: history.go(-1)" class="buttonlink fright sr_sitestoreproduct_item_icon_back mtop5"><?php echo $this->translate("Go back to previous page") ?></a>
    	<span class="o_hidden"><?php echo $this->translate("Comparison chart") ?> </span>
		</div>
    
    <?php $addFieldId =1; ?>
    <div class="sr_sitestoreproduct_comparison_content b_dark">
      <div class="comparisonHeader" id="comparisonHeader">
        <div class="scrollbarArea"></div>

        <div class="itemThumbnail compareField"></div>
        <div class="itemTitle compareField <?php echo "compare_row_".$addFieldId++ ?>" style="height: 30px; "></div>

        <div class="compareField compareFieldBlank" style="height: 12px;"></div>
        
        <div class="compareField compareFieldBlank" style="height: 30px;"></div>

        <?php if ($this->compareSettingList->editor_rating && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 1 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3)): ?>
          <div class="fieldGroup compareField" style="height: 15px; "><?php echo $this->translate("Editor Ratings") ?></div>    
          <div class="itemEditorRating compareField" style="height: 20px; "><?php echo $this->translate("Overall Rating") ?></div>
          <?php foreach ($this->ratingsParams as $param):?>
            <?php if(in_array($param->ratingparam_id, $this->compareSettingListEditorRatingFields)):?>
              <div class="itemUserRating compareField" style="height: 20px; "><?php echo $this->translate($param->ratingparam_name) ?></div>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endif; ?>
        <?php if ($this->compareSettingList->user_rating && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3)): ?>
          <div class="fieldGroup compareField" style="height: 15px; "><?php echo $this->translate("User Ratings") ?></div>
          <div class="itemUserRating compareField" style="height: 20px; "><?php echo $this->translate("Overall Rating") ?></div>
          <?php foreach ($this->ratingsParams as $param):?>
            <?php if(in_array($param->ratingparam_id, $this->compareSettingListUserRatingFields)):?>
              <div class="itemUserRating compareField" style="height: 20px; "><?php echo $this->translate($param->ratingparam_name) ?></div>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endif; ?>
        <?php $row = 0; ?>

        <?php if ($this->compareSettingList->tags || ($this->compareSettingList->price)): ?>  

          <div class="fieldGroup compareField" style="height: 15px; "><?php echo $this->translate("Information") ?></div>
          <?php if ($this->compareSettingList->tags): ?>
            <div class=" compareField <?php echo "compare_row_".$addFieldId++ ?> <?php echo (($row++) % 2 == 1) ? 'alt' : '' ?>" >
              <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct_brands', 1)):?>
                <?php echo $this->translate('Brand') ?>
              <?php else: ?>
                <?php echo $this->translate('Tags') ?>
              <?php endif; ?>
            </div>
          <?php endif; ?> 
          <?php if ($this->compareSettingList->price): 
          $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);
          ?>
            <div class=" compareField <?php echo (($row++) % 2 == 1) ? 'alt' : '' ?>" style="height: <?php echo empty($isDownPaymentEnable)? '30px;': '70px;'; ?>;"><?php echo $this->translate("Price") ?></div>
          <?php endif; ?>

        <?php endif; ?>
        <?php $row = 0; ?>
        <?php foreach ($this->customFields as $key => $field): ?>
          <?php if (in_array($key, $this->compareSettingListCustomFields)): ?>
            <?php if ($field['type'] == 'heading'): ?>
              <div class="fieldGroup compareField <?php echo "compare_row_".$addFieldId++ ?> " style="min-height: 15px; "><?php echo $this->translate($field['lable']) ?></div>
              <?php $row = 0; ?>
            <?php elseif ($field['type'] == 'textarea'): ?>
              <div class="itemSummary compareField <?php echo "compare_row_".$addFieldId++ ?>  <?php echo (($row++) % 2 == 1) ? 'alt' : '' ?>" ><?php echo $this->translate($field['lable']) ?></div>         
            <?php else: ?>
              <div class=" compareField <?php echo "compare_row_".$addFieldId++ ?> <?php echo (($row++) % 2 == 1) ? 'alt' : '' ?>" ><?php echo $this->translate($field['lable']) ?></div>
            <?php endif; ?>
          <?php endif; ?>
        <?php endforeach; ?>

        <?php if ($this->compareSettingList->views || $this->compareSettingList->comments || $this->compareSettingList->likes || ($this->compareSettingList->reviews && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) > 1)): ?>        
          <div class="fieldGroup compareField" style="height: 15px; "><?php echo $this->translate("Statistics") ?></div>     
          <?php $row = 0; ?>
          <?php if ($this->compareSettingList->views): ?>
            <div class=" compareField  <?php echo (($row++) % 2 == 1) ? 'alt' : '' ?>" style="height: 15px; "><?php echo $this->translate("Views") ?></div>
          <?php endif; ?>
            <?php if($this->isCommentsAllow): ?>
          <?php if ($this->compareSettingList->comments): ?>
            <div class=" compareField <?php echo (($row++) % 2 == 1) ? 'alt' : '' ?>" style="height: 15px; "><?php echo $this->translate("Comments") ?></div>
          <?php endif; ?>
            <?php endif; ?>
          <?php if ($this->compareSettingList->likes): ?>
            <div class=" compareField <?php echo (($row++) % 2 == 1) ? 'alt' : '' ?>" style="height: 15px; "><?php echo $this->translate("Likes") ?></div>
          <?php endif; ?>
          <?php if ($this->compareSettingList->reviews && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) > 1): ?>
            <div class=" compareField <?php echo (($row++) % 2 == 1) ? 'alt' : '' ?>" style="height: 15px; "><?php echo $this->translate("Reviews") ?></div>
          <?php endif; ?>
        <?php endif; ?>
        
            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.locationfield', 0)) : ?>
            <?php // if($this->compareSettingList->location) :?>
            <!--<div class="fieldGroup compareField" style="height: 15px; "><?php echo $this->translate("Location") ?></div>--> 
            <?php // $row = 0; ?>
          <?php // if ($this->compareSettingList->location): ?>
            <!--<div class=" compareField  <?php echo (($row++) % 2 == 1) ? 'alt' : '' ?>" style="height: 15px; "><?php // echo $this->translate("Location") ?></div>-->
          <?php // endif; ?>
            <?php // endif; ?>
            <?php endif; ?>
            
        <?php if ($this->compareSettingList->summary): ?>
          <div class="fieldGroup compareField" style="height: 15px; "><?php echo $this->translate("Summary") ?></div>
          <div class="compareFieldBlank itemSummary compareField <?php echo "compare_row_".$addFieldId++ ?>" ></div>
        <?php endif; ?>
        
        <div class="compareField compareFieldBlank" style="height: 30px; "></div>
        <div class="compareField compareFieldBlank" style="height: 12px; "></div>

        <div class="itemTitle compareField <?php echo "compare_row_".$addFieldId++ ?>" style="height: 30px; "></div>
        <div class="itemThumbnail compareField"></div>
        <div class="scrollbarArea"></div>
      </div>
      <div class="comparedProducts scroll-pane" id="comparedProductsPanel" style="overflow-x: hidden; overflow-y: hidden; ">
    
        <div class="scrollbarArea" id ="scrollbar_before">		
        </div>
        <div id="scroll-areas-main" >
          <div id="scroll-areas" style="overflow: hidden;"> 
            <div class="scroll-content" id="scroll-content" style="margin-left: 0px; ">
              <?php foreach ($this->lists as $sitestoreproduct): ?>
                <?php $addFieldId =1; ?>
                <div class="scroll-content-item item<?php echo $sitestoreproduct->getIdentity() ?>" rel="<?php echo $this->compareSettingList->category_id ?>" style="width: 400px; ">
                  <div class="itemThumbnail compareField">
                    <div class="b_medium itemphoto">
                      <table>
                        <tr>
                          <td class="prelative sitestoreproduct_q_v_wrap" width="100%" height="100%" valign="middle" align="center">
                            <?php $product_id = $sitestoreproduct->product_id; ?>
                            <?php $quickViewButton = true; ?>
                            <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_quickView.tpl'; ?>
                            <?php echo $this->htmlLink($sitestoreproduct->getHref(), $this->itemPhoto($sitestoreproduct, 'thumb.normal', $sitestoreproduct->getTitle())); ?>
                          </td>
                        </tr>
                      </table>			
                      <span class="removeComparedProduct" style="display: none;"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreproduct/externals/images/closebox.png" width="25" height="25" id="compare_removeItem<?php echo $sitestoreproduct->getIdentity() ?>" rel="<?php echo $this->compareSettingList->category_id ?>" alt="<?php echo $this->translate("Remove product") ?>" class="removeProduct"/></span>
                    </div>	
                  </div>
                  <div class="itemTitle compareField <?php echo "compare_row_".$addFieldId++ ?>" style="min-height: 30px; ">
                    <?php echo $this->htmlLink($sitestoreproduct->getHref(), $sitestoreproduct->getTitle()) ?>
                  </div>          

                  <div class="compareField" style="height: 12px; ">
                    <?php echo $this->addToWishlistSitestoreproduct($sitestoreproduct, array('classIcon' => 'sr_sitestoreproduct_wishlist_href_link', 'classLink' => ''));?>
                  </div>
                  
                  <div class="compareField" style="height: 30px; ">
                    <?php echo $this->addToCart($sitestoreproduct, 1, 'top', true); ?>
                  </div>
                  

                  <!--Editor Rating -->
                  <?php if ($this->compareSettingList->editor_rating && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 1 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3)): ?>
                    <div class="fieldGroup compareField" style="height: 15px; "></div>
                    <div class="itemEditorRating compareField" style="height: 20px; "><?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor,'editor','big-star', '', true); ?>
                    </div>
                    <?php $ratings=$sitestoreproduct->ratingBaseCategory('editor');
                    foreach ($this->ratingsParams as $param):?>
                    <?php if(in_array($param->ratingparam_id, $this->compareSettingListEditorRatingFields)):?>
                    <div class="itemUserRating compareField" style="height: 20px; ">
                      <?php
                      if (!isset($ratings[$param->ratingparam_id])):
                        $ratings[$param->ratingparam_id] = 0;
                      endif;
                      ?>
                      <?php echo $this->showRatingStarSitestoreproduct($ratings[$param->ratingparam_id],'user','small-box'); ?>
                    </div>
                    <?php endif; ?>
                   <?php endforeach; ?>
                  <?php endif; ?>
                
                  <!-- User Ratings-->
                  <?php if ($this->compareSettingList->user_rating && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3)): ?>
                    <div class="fieldGroup compareField" style="height: 15px; "></div>
                    <div class="itemUserRating compareField" style="height: 20px; ">
                    <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users,'user','big-star'); ?></div>
                    <?php $ratings=$sitestoreproduct->ratingBaseCategory('user');
                    foreach ($this->ratingsParams as $param):?>
                    <?php if(in_array($param->ratingparam_id, $this->compareSettingListUserRatingFields)):?>
                    <div class="itemUserRating compareField" style="height: 20px; ">
                      <?php
                      if (!isset($ratings[$param->ratingparam_id])):
                        $ratings[$param->ratingparam_id] = 0;
                      endif;
                      ?>
                      <?php echo $this->showRatingStarSitestoreproduct($ratings[$param->ratingparam_id],'user','small-box'); ?>
                    </div>
                    <?php endif; ?>
                   <?php endforeach; ?>
                  <?php endif; ?>
                  <!--  Info      -->

                  <?php $row = 0; ?>

                  <?php if ($this->compareSettingList->tags || ($this->compareSettingList->price)): ?>  

                    <div class="fieldGroup compareField" style="height: 15px; "></div>
                    <?php if ($this->compareSettingList->tags): ?>
                      <div class="compareField <?php echo "compare_row_".$addFieldId++ ?> <?php echo (($row++) % 2 == 1) ? 'alt' : '' ?>" >
                        <?php $tagCount = 0;
                        foreach ($sitestoreproduct->tags()->getTagMaps() as $tagmap): ?><?php if (!empty($tagmap->getTag()->text)): ?><?php $tag = $tagmap->getTag(); ?><?php if(!empty($tagCount)): echo ", ";endif; ?><a href='<?php echo $this->url(array('action' => 'index'), "sitestoreproduct_general"); ?>?tag=<?php echo urlencode($tag->text) ?>&tag_id=<?php echo $tag->tag_id ?>'><?php echo $tag->text ?></a><?php $tagCount++; ?><?php endif;endforeach; ?>
                        <?php if (empty($tagCount)): echo "-";
                        endif; ?>           
                      </div>
                    <?php endif; ?>
                    <?php if ($this->compareSettingList->price): 
                    $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);
                    ?>
                      <div class="compareField compareFieldprice <?php echo (($row++) % 2 == 1) ? 'alt' : '' ?>" style="height: <?php echo empty($isDownPaymentEnable)? '30px;': '70px;'; ?>; ">
                        <?php echo Engine_Api::_()->sitestoreproduct()->getProductDiscount($sitestoreproduct); ?>
                      </div>                      
                    <?php endif; ?>

                  <?php endif; ?>
                  <?php
                    if($this->proifle_map_ids):
                      $fieldStructure = $this->fieldsApi->getFieldsStructurePartial($sitestoreproduct);
                      $row = 0;
                      $fieldsValues = array();
                      foreach ($fieldStructure as $map) :
                        $field = $map->getChild();
                         if (in_array($field->field_id, $this->compareSettingListCustomFields)): 
                           if ($field->type != 'heading'): 
                             $fieldsValues[$field->field_id] = $this->compareProfileFieldsSitestoreproduct($sitestoreproduct, $fieldStructure, $map, $field); 
                           endif;
                         endif;
                      endforeach; 
                    ?>
                    
                    <?php foreach ($this->customFields as $key => $field): ?>
                      <?php if (in_array($key, $this->compareSettingListCustomFields)): ?>
                        <?php if ($field['type'] == 'heading'): ?>
                          <div class="fieldGroup compareField <?php echo "compare_row_".$addFieldId++ ?>" ></div>
                        <?php $row = 0; ?>
                        <?php else: ?>
                        <?php $str = isset ($fieldsValues[$key])? $fieldsValues[$key]:null; $str=strip_tags($str); ?>      
                          <?php if ($field['type'] == 'textarea'): ?>
                            <div class="itemSummary <?php echo "compare_row_".$addFieldId++ ?> compareField <?php echo (($row++) % 2 == 1) ? 'alt' : '' ?>" >
                              <?php echo!empty($str) ? $this->seacore_api->seaocoreTruncateText($str, 500) : '-'; ?>
                            </div>
                          <?php else: ?>
                            <div class="compareField <?php echo "compare_row_".$addFieldId++ ?> <?php echo (($row++) % 2 == 1) ? 'alt' : '' ?>" ><?php echo!empty($str) ? $str : '-'; ?></div>
                          <?php endif; ?>
                        <?php endif; ?>
                     <?php endif; ?>
                    <?php endforeach; ?>    
                  <?php endif; ?>
              <!--     Statics   -->
                  <?php $row = 0; ?>
                  <?php if ($this->compareSettingList->views || $this->compareSettingList->comments || $this->compareSettingList->likes || ($this->compareSettingList->reviews && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) > 1)): ?>
                    <div class="fieldGroup compareField" style="height: 15px; "></div>
                    <?php if ($this->compareSettingList->views): ?>
                      <div class="compareField <?php echo (($row++) % 2 == 1) ? 'alt' : '' ?>" style="height: 15px; "><?php echo $sitestoreproduct->view_count ?></div>
                    <?php endif; ?>
                      <?php ?>
                       <?php if($this->isCommentsAllow): ?>
                    <?php if ($this->compareSettingList->comments): ?>
                      <div class="compareField <?php echo (($row++) % 2 == 1) ? 'alt' : '' ?>" style="height: 15px; "><?php echo $sitestoreproduct->comment_count ?></div>
                    <?php endif; ?>
                      <?php endif; ?>
                    <?php if ($this->compareSettingList->likes): ?>
                      <div class="compareField <?php echo (($row++) % 2 == 1) ? 'alt' : '' ?>" style="height: 15px; "><?php echo $sitestoreproduct->like_count ?></div>
                    <?php endif; ?>
                    <?php if ($this->compareSettingList->reviews && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) > 1): ?>
                      <div class="compareField <?php echo (($row++) % 2 == 1) ? 'alt' : '' ?>" style="height: 15px; "><?php echo $sitestoreproduct->review_count ?></div>
                    <?php endif; ?>
                  <?php endif; ?>
                      
                  <!--  Location      -->
                  
                  <?php // if($this->compareSettingList->location && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.locationfield', 0)) :?>
                   <!--<div class="fieldGroup compareField" style="height: 15px; "></div>-->
                    <!--<div class="itemSummary compareField <?php // echo "compare_row_".$addFieldId++ ?>" >-->
                      <?php // echo $this->seacore_api->seaocoreTruncateText($sitestoreproduct->location, 500); ?>
                    <!--</div>-->
                  <?php // endif; ?>
                   
                  <!--  Summary      -->
                  <?php if ($this->compareSettingList->summary): ?>
                    <div class="fieldGroup compareField" style="height: 15px; "></div>
                    <div class="itemSummary compareField <?php echo "compare_row_".$addFieldId++ ?>" >
                      <?php echo $this->seacore_api->seaocoreTruncateText($sitestoreproduct->body, 500); ?>
                    </div>
                  <?php endif; ?>
                  
                  <div class="compareField" style="height: 30px; ">
                    <?php echo $this->addToCart($sitestoreproduct, 1, 'bottom', true); ?>
                  </div>
                    
                  <div class="compareField" style="height: 12px; ">
                    <?php echo $this->addToWishlistSitestoreproduct($sitestoreproduct, array('classIcon' => 'sr_sitestoreproduct_wishlist_href_link', 'classLink' => ''));?>
                  </div>

                  <div class="itemTitle compareField <?php echo "compare_row_".$addFieldId++ ?>" style="min-height: 30px; ">
                    <?php echo $this->htmlLink($sitestoreproduct->getHref(), $sitestoreproduct->getTitle()) ?>
                  </div>
                  <div class="itemThumbnail compareField">
                    <div class="b_medium itemphoto">
                      <table>
                        <tr>
                          <td class="prelative sitestoreproduct_q_v_wrap" width="100%" height="100%" valign="middle" align="center">
                            <?php $product_id = $sitestoreproduct->product_id; ?>
                            <?php $quickViewButton = true; ?>
                            <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_quickView.tpl'; ?>
                            <?php echo $this->htmlLink($sitestoreproduct->getHref(), $this->itemPhoto($sitestoreproduct, 'thumb.normal', $sitestoreproduct->getTitle())); ?>
                          </td>
                        </tr>
                      </table>	
                      <span class="removeComparedProduct" style="display: none;"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreproduct/externals/images/closebox.png" width="25" height="25" id="compare_removeItem<?php echo $sitestoreproduct->getIdentity() ?>" rel="<?php echo $this->compareSettingList->category_id ?>" alt="<?php echo $this->translate("Remove item") ?>" class="removeProduct"></span>
                    </div>
                  </div>
                </div>     
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        <div class="scrollbarArea" id ="scrollbar_after">		
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript">
      compareSitestoreproductDefault={
        enabel:true,
        compareUrl:'<?php echo $this->url(array (),'sitestoreproduct_compare',true) ?>'
      }
    var totalLsit=<?php echo $this->totalList ?>;
    var scrollBarContentArea;
    en4.core.runonce.add(function(){
      resetContent();    
      $$(".removeComparedProduct").addEvent('click', function(event){
        var el = $(event.target); 
        el.getParent('.scroll-content-item').fade('out');     
        var list_id = el.get('id').substr(18);   
        (function(){        
          el.getParent('.scroll-content-item').destroy();
          totalLsit = totalLsit-1;
          resetContent();
          scrollBarContentArea.update();
        }).delay(500);

        if(typeof compareSitestoreproductContent =='undefined'){
        compareSitestoreproductContent  = new compareSitestoreproduct();
        }
        
        if( compareSitestoreproductContent){
          compareSitestoreproductContent.removeProductFromComparison(event);
        }
      });
       (function(){      
        $('comparisonHeader').getElements('.compareField').each(function(element){
          element.get('class').split(' ').each(function(className){
            className = className.trim();
            if( className.match(/^compare_row_[0-9]+$/) ) { 
              var MaxHeight=0; 
              $$('.'+className).each(function(el){

                if(MaxHeight <el.offsetHeight)
                  MaxHeight = el.offsetHeight;
              });

              MaxHeight= MaxHeight-10;
              $$('.'+className).setStyle('height',MaxHeight+'px');
            }
          });
        });
      $('scroll-areas').setStyle('height', $('scroll-content').offsetHeight+'px');
      $('scroll-areas').setStyle('width', $('comparedProductsPanel').offsetWidth+'px');
        scrollBarContentArea = new SEAOMooHorizontalScrollBar('scroll-areas-main', 'scroll-areas', {
        'arrows': false,
        'horizontalScroll': true,
        'horizontalScrollElement':'scrollbar_after',
        'horizontalScrollBefore':true,
        'horizontalScrollBeforeElement':'scrollbar_before'
        });
      }).delay(700);           
    });

    var resetContent= function(){
      var width = ($('comparedProductsPanel').offsetWidth /totalLsit ); 
      width = width - 2;
      if(width < 180)  width = 180;
      width++;
      var numberOfItem=($('comparedProductsPanel').offsetWidth /width);
      var numberOfItemFloor = Math.floor(numberOfItem);
      var extra = (width*(numberOfItem - numberOfItemFloor)/numberOfItemFloor);
      width = width +extra;
      $('scroll-content').setStyle('width', (width*totalLsit)+'px');
      $('scroll-content').getElements('.scroll-content-item').each(function(el){
        el.setStyle('width', width+'px');
        if(numberOfItemFloor < totalLsit){
          el.getElements('.removeComparedProduct').each(function(elremove){
            elremove.setStyle('display', 'inline-block');
          });
        }else {
          $$('.scrollbarArea').setStyle('display', 'none');
          if(totalLsit <= 2){
            el.getElements('.removeComparedProduct').each(function(elremove){
              elremove.setStyle('display', 'none');   
            });
          }
        }

      });
    }
    var tagAllAction = function(tag_id, tag){
      $('tag').value = tag;
      $('tag_id').value = tag_id;
      $('filter_form_tagscloud').submit();
    }
  </script>

<?php endif; ?>