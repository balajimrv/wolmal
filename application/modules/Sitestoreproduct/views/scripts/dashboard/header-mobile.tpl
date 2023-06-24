<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: header.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $baseUrl = $this->layout()->staticBaseUrl; ?>
<div class="sr_sitestoreproduct_dashboard_header">
    <span class="fright">
        <?php echo $this->htmlLink($this->sitestoreproduct->getHref(), $this->translate('View Product'), array("class" => 'sitestoreproduct_buttonlink')) ?>
        <?php
        if ($this->sitestoreproduct->store_id) {
            $page = Engine_Api::_()->getItem('sitestore_store', $this->sitestoreproduct->store_id);
            echo $this->htmlLink($page->getHref(), $this->translate('View Store'), array("class" => 'sitestoreproduct_buttonlink'));
            echo "<a onclick=\"window.location.href='" . $this->url(array('controller' => 'product', 'action' => 'manage', 'store_id' => $this->sitestoreproduct->store_id), "sitestoreproduct_extended") . "';return false;\" href='" . $this->url(array('action' => 'store', 'store_id' => $page->store_id), 'sitestore_dashboard', true) . "' class='sitestoreproduct_buttonlink'>" . $this->translate('Manage Products') . "</a>";
        }
        ?>
    </span>
    <span class="sr_sitestoreproduct_dashboard_header_title o_hidden">
        <?php echo $this->translate('Product Dashboard'); ?>:

        <?php echo $this->htmlLink($this->sitestoreproduct->getHref(), $this->sitestoreproduct->getTitle()) ?>
    </span>
    <?php if (empty($this->sitestoreproduct->draft) && empty($this->sitestoreproduct->search) && empty($this->isCopyProduct)) : ?>
        <div id="tipMessageElement" class="tip mtop10">
            <span id="productStatusMessage">
                <?php echo $this->translate("This product is currently disabled and thus it will not be displayed on this site. Please click %shere%s to enable it.", "<a href='javascript:void(0)' onclick='changeProductStatus(" . $this->sitestoreproduct->product_id . ")'>", "</a>"); ?>
            </span>
            <div id="productStatusMessageLoadingImage" style="display: inline-block;" class="mtop10 mleft10"></div>
        </div>

    <?php endif; ?>
</div>

<script type="text/javascript">
    function changeProductStatus(product_id)
    {
        $("productStatusMessageLoadingImage").innerHTML = '<img src=<?php echo $baseUrl; ?>application/modules/Sitestoreproduct/externals/images/loading.gif height=15 width=15>';
        en4.core.request.send(new Request.JSON({
            url: '<?php echo $baseUrl; ?>sitestoreproduct/product/change-product-status/product_id/' + product_id,
            method: 'POST',
            data: {format: 'json'},
            onSuccess: function (responseJSON)
            {
                document.getElementById("productStatusMessageLoadingImage").innerHTML = '';
                if (responseJSON.success)
                    document.getElementById("productStatusMessage").innerHTML = '<ul class="form-notices"><li><?php echo $this->translate("Your product has been enabled successfully.") ?></li></ul>';
                document.getElementById("tipMessageElement").removeClass('tip');
            }
        })
                )
    }
</script>