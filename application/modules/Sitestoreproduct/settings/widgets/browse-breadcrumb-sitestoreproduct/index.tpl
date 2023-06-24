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

<?php if (!empty($this->category_id) || (isset($this->formValues['tag']) && !empty($this->formValues['tag']) && isset($this->formValues['tag_id']) && !empty($this->formValues['tag_id']))): ?>
    <div class="sr_sitestoreproduct_product_breadcrumb">
        <?php if (!empty($this->category_id)): ?>

            <?php echo $this->htmlLink($this->url(array('action' => 'index'), "sitestoreproduct_general"), $this->translate("Browse Products")) ?>

            <?php if ($this->category_name != ''): ?>
                <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
            <?php endif; ?>

            <?php
            $this->category_name = $this->translate($this->category_name);
            $this->subcategory_name = $this->translate($this->subcategory_name);
            $this->subsubcategory_name = $this->translate($this->subsubcategory_name);
            ?>
            <?php if ($this->category_name != '') : ?>
                <?php
                    $getMainCategoryObj = Engine_Api::_()->getItem('sitestoreproduct_category', $this->category_id);
                    $getSubCategoryObj = Engine_Api::_()->getItem('sitestoreproduct_category', $this->subcategory_id);
                ?>
                <?php if (($this->subcategory_name != '') && !empty($getMainCategoryObj)): ?> 

                    <?php echo $this->htmlLink($this->url(array('category_id' => $this->category_id, 'categoryname' => $getMainCategoryObj->getCategorySlug()), "" . $this->categoryRouteName . ""), $this->translate($this->category_name)) ?>
                <?php else: ?>
                    <?php echo $this->translate($this->category_name) ?>   
                <?php endif; ?>
                <?php if ($this->subcategory_name != ''): ?> 
                    <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
                    <?php if (!empty($this->subsubcategory_name) && !empty($getMainCategoryObj) && !empty($getSubCategoryObj)): ?>
                        <?php echo $this->htmlLink($this->url(array('category_id' => $this->category_id, 'categoryname' => $getMainCategoryObj->getCategorySlug(), 'subcategory_id' => $this->subcategory_id, 'subcategoryname' => ucfirst($getSubCategoryObj->getCategorySlug())), "sitestoreproduct_general_subcategory"), $this->translate($this->subcategory_name)) ?>   
                    <?php else: ?>
                        <?php echo $this->translate($this->subcategory_name) ?>       
                    <?php endif; ?>
                    <?php if (!empty($this->subsubcategory_name)): ?>
                        <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
                        <?php echo $this->translate($this->subsubcategory_name); ?>    
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (((isset($this->formValues['tag']) && !empty($this->formValues['tag']) && isset($this->formValues['tag_id']) && !empty($this->formValues['tag_id'])))): ?>
            <?php $tag_value = $this->formValues['tag'];
            $tag_value_id = $this->formValues['tag_id'];
            $browse_url = $this->url(array('action' => 'index'), "sitestoreproduct_general", true) . "?tag=$tag_value&tag_id=$tag_value_id"; ?>
                <?php if ($this->category_name): ?><br /><?php endif; ?>
                <?php echo $this->translate("Showing products tagged with: "); ?>
            <b><a href='<?php echo $browse_url; ?>'>#<?php echo $this->formValues['tag'] ?></a>
            <?php if ($this->current_url2): ?>  
                    <a href="<?php echo $this->url(array('action' => 'index'), "sitestoreproduct_general", true) . "?" . $this->current_url2; ?>"><?php echo $this->translate('(x)'); ?></a></b>
        <?php else: ?>
                <a href="<?php echo $this->url(array('action' => 'index'), "sitestoreproduct_general", true); ?>"><?php echo $this->translate('(x)'); ?></a></b>        
        <?php endif; ?>
    <?php endif; ?>
    </div>
<?php endif; ?>

