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

<style>
    ul.admin_fields .field_extraoptions_contents_wrapper {
        display: none;
        overflow: hidden;
        position: absolute;
        min-width: 200px;
        padding: 4px;
        background-color: rgba(0, 0, 0, 0.3);
        z-index: 100;
        margin-top: 24px;
        margin-left: 24px;
    }
    ul.admin_fields .field_extraoptions.active .field_extraoptions_contents_wrapper {
        display: block;
        cursor: default;
    }
    .admin_field_dependent_field_wrapper{
        display: none;
    }
</style>

<?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_MobileDashboardNavigation.tpl'; ?>

<div class="sr_sitestoreproduct_dashboard_content">
    <?php echo $this->partial('application/modules/Sitestoreproduct/views/scripts/dashboard/header-mobile.tpl', array('sitestoreproduct'=>$this->sitestoreproduct));?>
    <?php
    $baseUrl = $this->layout()->staticBaseUrl;
    $this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
            . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproductform.css')
    ?>

    <script type="text/javascript">
        var option_id = '<?php echo $this->option_id; ?>';
        var product_id = '<?php echo $this->sitestoreproduct->product_id; ?>';
        '<?php $product = Engine_Api::_()->getItem('sitestoreproduct_product', $this->sitestoreproduct->product_id);
    $productPriceAfterDiscount = Engine_Api::_()->sitestoreproduct()->getProductDiscount($product, '', '', 1); ?>';
    </script>

    <?php
    // Render the admin js
    echo $this->render('_jsMobileAdminSitestoreproduct.tpl')
    ?>
    <div class="sitestoreproduct_form_separator b_medium"></div>
    <h3><?php echo $this->translate('Product Attributes') ?></h3>
    <p><?php echo $this->translate("Below, you can create multiple attributes for your product like size, color, etc. These attributes will be visible to buyers on the product profile page and they will be able to select desired attribute values while adding the product to cart. You will be able to create product variations with all the 'Select Box' type attributes listed here.") ?></p>
    <br />
    <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.combination', 1)): ?>
    <div class='tip'>
    <span>
      <?php echo $this->translate("<b>NOTE:</b> you must create product variations using “Select Box” type product attributes if you want buyers to choose the product variations with attributes that they want to purchase."); ?>
      </span>
        <br />
        <?php endif;?>

        <div class="seaocore_add mtop10">
            <a href="javascript:void(0);" onclick="window.location.href='<?php echo $this->layout()->staticBaseUrl;?>sitestoreproduct/siteform/field-create-mobile/option_id/<?php echo sprintf('%d', $this->topLevelOptionId) ?>/parent_id/<?php echo sprintf('%d', $this->topLevelFieldId) ?>/product_id/<?php echo $this->sitestoreproduct->product_id;?>';return false;"
               class="buttonlink seaocore_icon_add admin_fields_options_addquestion"><?php echo $this->translate("Add Product Attribute") ?></a>
        </div>
        <ul class="admin_fields">
            <?php foreach ($this->secondLevelMaps as $map): ?>
                <?php echo $this->mobileAdminFieldMeta($map,
                    array(
                        "productPrice" => $productPriceAfterDiscount,
                        "product_id" => $this->sitestoreproduct->product_id,
                        "staticBaseUrl" => $this->layout()->staticBaseUrl,
                    )) ?>
            <?php endforeach; ?>
        </ul>
    </div>