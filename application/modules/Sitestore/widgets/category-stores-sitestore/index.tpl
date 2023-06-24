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
<?php 
	$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl
  	              . 'application/modules/Seaocore/externals/styles/styles.css');
  	              
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<script type="text/javascript">

	function showStorePhoto(ImagePath, category_id, store_id) {
    var elem = document.getElementById('store_elements_'+category_id).getElementsByTagName('a'); 
    for(var i = 0; i < elem.length; i++)
    { 
			var cat_storeid = elem[i].id;
			$(cat_storeid).erase('class');
		}
    $('store_link_class_'+store_id).set('class', 'active');
    
		$('storeImage_'+category_id).src = ImagePath;
	}

  var categoryAction =function(category,sub) 
  { if($("tag"))
      $("tag").value='';
    $('category').value = category;
    $('subcategory').value = sub;
    if($('filter_form')) {
	    $('filter_form').submit();
    } else {
    	$('filter_form_category').submit();
    }
  }
  var subcategoryAction = function(category,subcategory) 
  { if($("tag"))
      $("tag").value='';
    $('category').value = category;
    $('subcategory').value = subcategory;
    if($('filter_form')) {
	    $('filter_form').submit();
    } else {
    	$('filter_form_category').submit();
    }
  }
</script>

<ul class="seaocore_categories_box">
  <li> 
    <?php $ceil_count = 0; $k = 0; ?>
    <?php for ($i = 0; $i <= count($this->categories); $i++) { ?>
			<?php if($ceil_count == 0) :?>      
				<div>      
			<?php endif;?>  
          <div class="seaocore_categories_list_row" style="width: <?php echo (round(100/$this->columnCount)-1) ?>%" >
				<?php $ceil_count++;?>				
				<?php $category = "";
					if (isset($this->categories[$k]) && !empty($this->categories[$k])): 
						$category = $this->categories[$k];
					endif;
					$k++;

					if (empty($category)) {
						break;
					}
				?>

				<div class="seaocore_categories_list">
					<?php $total_subcat = Count($category['category_stores']); ?>
					<h6>
						<?php echo $this->htmlLink($this->url(array('category_id' => $category['category_id'], 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($category['category_name'])), 'sitestore_general_category'), $this->translate($category['category_name'])) ?>
					</h6>	
					<div class="sub_cat" id="subcat_<?php echo $category['category_id'] ?>">

						<?php $total_count = 1; ?>
		
						<?php foreach ($category['category_stores'] as $categoryStores) : ?>

							<?php 
								$imageSrc = $categoryStores['imageSrc']; 
								if(empty($imageSrc)) {
									$imageSrc = $this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/nophoto_sitestore_thumb_icon.png';
								}

								$category_id = $category['category_id'];
								$store_id = $categoryStores['store_id'];
							?>
							<?php if($total_count == 1): ?>
								<div class="seaocore_categories_img" >
									<img src="<?php echo $imageSrc; ?>" id="storeImage_<?php echo $category['category_id'] ?>" alt="" class="thumb_icon" />
								</div>
								<div id='store_elements_<?php echo $category_id;?>'>
								<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($categoryStores['store_id'], $categoryStores['owner_id'], $categoryStores['slug']), Engine_Api::_()->sitestore()->truncation($categoryStores['store_title'], 25)." (".$categoryStores['populirityCount'].")", array('onmouseover' => "javascript:showStorePhoto('$imageSrc', '$category_id', '$store_id');",'title' => $categoryStores['store_title'], 'class'=>'active', 'id'=>"store_link_class_$store_id"));?>
							<?php else: ?>
								<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($categoryStores['store_id'], $categoryStores['owner_id'], $categoryStores['slug']), Engine_Api::_()->sitestore()->truncation($categoryStores['store_title'], 25)." (".$categoryStores['populirityCount'].")", array('onmouseover' => "javascript:showStorePhoto('$imageSrc', '$category_id', '$store_id');",'title' => $categoryStores['store_title'], 'id'=>"store_link_class_$store_id"));?>
							<?php endif; ?>

							<?php $total_count++; ?>
            <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
     <?php if($ceil_count % $this->columnCount == 0) :?>      
     </div>
     <?php $ceil_count=0; ?>
     <?php endif;?>
    <?php } ?> 
  </li>	
</ul>