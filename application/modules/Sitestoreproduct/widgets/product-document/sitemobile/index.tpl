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
if($this->loaded_by_ajax):?>
  <script type="text/javascript">
    var params = {
      requestParams :<?php echo json_encode($this->params) ?>,
      responseContainer :$$('.layout_sitestoreproduct_product_document')
    }
    en4.sitestoreproduct.ajaxTab.attachEvent('<?php echo $this->identity ?>',params);
  </script>
<?php endif;?>
<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_dashboard.css');
$documentsByProduct = $this->documentsByProduct;
?>

<div id="store_table_rate">
  <?php if ((@count($this->documentsByProduct))): ?>

    <div class="sitestoreproduct_data_table product_detail_table fleft mbot10">
      <form id='multidelete_form_table_product_document' method="post">
        <table class="mbot10">

          <?php $tempFlag = false; ?>
     <?php foreach ($this->documentsByProduct as $item): 
      
       if((empty($item->privacy) && empty($this->temp_result)))
               continue;
       if(empty($tempFlag)): ?>
          <tr class="product_detail_table_head">

            <th><?php echo $this->translate("Title") ?></th>
            <th><?php echo $this->translate("Description") ?></th>
            <th><?php echo $this->translate("Options") ?></th>
          </tr>
          <?php endif;
          $tempFlag = true; ?>
           <tr>
              <td> <?php echo $this->translate($item->title); ?> </td>
              <td> <?php echo $this->viewMore($this->translate($item->body), 125); ?> </td>
            <td>
                <a href= "<?php echo $this->url(array('action' => 'download-document', 'product_id' => $this->sitestoreproduct->product_id, 'file_id' => $item->file_id), "sitestoreproduct_dashboard", true);?>" target='downloadframe'><?php echo $this->translate('download') ?></a>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>

      </form> 
    </div>
  <?php if(empty($tempFlag)): ?>
    <div id="no_location_tip" class="tip">
      <span>
        <?php echo $this->translate("No document is available for this product.") ?>        </span>
    </div>
  <?php endif; ?>
  <?php else: ?>
    <div id="no_location_tip" class="tip">
      <span>
        <?php echo $this->translate("No document is available for this product.") ?>        </span>
    </div>
  <?php endif; ?>
</div>
