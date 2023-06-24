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
	$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl
  	              . 'application/modules/Seaocore/externals/styles/styles.css');
?>
<script type="text/javascript">

	function showProductPhoto(ImagePath, category_id, product_id,href) {
    var elem = document.getElementById('product_elements_'+category_id).getElementsByTagName('a'); 
    for(var i = 0; i < elem.length; i++)
    { 
			var cat_productid = elem[i].id;
			$(cat_productid).erase('class');
		}
    $('product_link_class_'+product_id).set('class', 'active');
    
		$('productImage_'+category_id).src = ImagePath;
    $('productImage_'+category_id).getParent('a').set('href',href);
	}

</script>

<ul class="seaocore_categories_box">
  <li> 
    <?php $ceil_count = 0; $k = 0; ?>
    <?php for ($i = 0; $i <= count($this->categories); $i++) { ?>
			<?php if($ceil_count == 0) :?>      
				<div>      
			<?php endif;?>  
			<div class="seaocore_categories_list_row">
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
					<?php $total_subcat = Count($category['category_products']); ?>
					<h6>
						<?php echo $this->htmlLink($this->url(array('category_id' => $category['category_id'], 'categoryname' => Engine_Api::_()->getItem('sitestoreproduct_category', $category['category_id'])->getCategorySlug()), "". $this->categoryRouteName .""), $this->translate($category['category_name'])) ?>
					</h6>	
					<div class="sub_cat" id="subcat_<?php echo $category['category_id'] ?>">

						<?php $total_count = 1; ?>
		
						<?php foreach ($category['category_products'] as $categoryProducts) : ?>

							<?php 
								$imageSrc = $categoryProducts['imageSrc']; 
								if(empty($imageSrc)) {
									$imageSrc = $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/nophoto_product_thumb_icon.png';
								}
								$category_id = $category['category_id'];
								$product_id = $categoryProducts['product_id'];
							?>
              <?php $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $categoryProducts['product_id']); ?>
							<?php if($total_count == 1): ?>
								<div class="seaocore_categories_img" >
                  <a href='<?php echo $sitestoreproduct->getHref() ?>' ><img src="<?php echo $imageSrc; ?>" id="productImage_<?php echo $category['category_id'] ?>" alt="" class="thumb_icon" /></a>
								</div>
								<div id='product_elements_<?php echo $category_id;?>'>
                <?php $href= $sitestoreproduct->getHref();?>
								<?php echo $this->htmlLink($sitestoreproduct->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($categoryProducts['product_title'], $this->title_truncation)." (".$categoryProducts['populirityCount'].")", array('onmouseover' => "javascript:showProductPhoto('$imageSrc', '$category_id', '$product_id','$href');",'title' => $categoryProducts['product_title'], 'class' => 'active', 'id' => "product_link_class_$product_id"));?>
							<?php else: ?> 
                <?php $href= $sitestoreproduct->getHref();?>
								<?php echo $this->htmlLink($sitestoreproduct->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($categoryProducts['product_title'], $this->title_truncation)." (".$categoryProducts['populirityCount'].")", array('onmouseover' => "javascript:showProductPhoto('$imageSrc', '$category_id', '$product_id','$href');",'title' => $categoryProducts['product_title'], 'id' => "product_link_class_$product_id"));?>
							<?php endif; ?>

							<?php $total_count++; ?>
            <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
     <?php if($ceil_count %2 == 0) :?>      
     </div>
     <?php $ceil_count=0; ?>
     <?php endif;?>
    <?php } ?> 
  </li>	
</ul>