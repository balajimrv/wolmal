<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: download-products.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/scripts/core.js'); ?>
<div class="sitestoreproduct_manage_account">
	<h3><?php echo $this->translate('My Downloadable Products') ?></h3>

<?php if(COUNT($this->paginator)): ?>
  <p class="mbot10"><?php echo $this->translate('Here, you can download your downloadable products by using the "download" link.'); ?></p>
  <div id="download_products_pagination">
    <div id="download_products_tab">
      <div class="sitestoreproduct_data_table product_detail_table fleft">
        <table>
          <tr class="product_detail_table_head">
            <th><?php echo $this->translate('Order Id') ?></th>
            <th><?php echo $this->translate('File Title') ?></th>
            <th class="txt_center"><?php echo $this->translate('Downloads') ?></th>
            <th class="txt_center"><?php echo $this->translate('Remaining Downloads') ?></th>          
            <th><?php echo $this->translate('Options') ?></th>
          </tr>	

    		<?php foreach( $this->paginator as $item ): ?>
          <tr>
            <?php $tempViewUrl = $this->url(array('action' => 'order-view', 'order_id' => $item->order_id, 'page_viewer' => 1) , 'sitestoreproduct_general', true);  ?>
            <td><a href="javascript:void(0)" onclick = "myAccountUrl('my-orders', 'order-view', '<?php echo $item->order_id; ?>', '<?php echo $tempViewUrl; ?>');"> <?php echo "#" . $item->order_id; ?></a></td>          
            <td title="<?php echo $item->title ?>"><?php echo $this->string()->truncate($this->string()->stripTags($item->title), 30) ?></td>
            <td class="txt_center">
              <span id="download_<?php echo $item->orderdownload_id ?>"><?php echo empty($item->max_downloads) ? '<i>'.$this->translate("Unlimited").'</i>' : $item->downloads; ?></span>
              <input type="hidden" id="download_value_<?php echo $item->orderdownload_id ?>" value="<?php echo $item->downloads ?>" />
            </td>
            <td class="txt_center">
              <span id="remaining_download_<?php echo $item->orderdownload_id ?>"><?php echo empty($item->max_downloads) ? '<i>'.$this->translate("Unlimited").'</i>' : ($item->max_downloads - $item->downloads) ?></span>
              <input type="hidden" id="remaining_download_value_<?php echo $item->orderdownload_id ?>" value="<?php echo $item->max_downloads - $item->downloads ?>" />
            </td>
            <td>
              <?php if( !empty($item->order_status) && $item->order_status != 1) : ?>
                <?php $download_url = $this->url(array('product_id' => Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($item->product_id), 'downloadablefile_id' => Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($item->downloadablefile_id), 'download_id' => Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($item->orderdownload_id)), 'sitestoreproduct_downloads', true) ?>
                <?php if( !empty($item->max_downloads) && (($item->max_downloads - $item->downloads) == 0) ) :  ?>
                  <i><span class="seaocore_txt_light"><?php echo $this->translate("Max. download limit reached") ?></span></i>
                <?php elseif( empty($item->max_downloads) ) : ?>
                  <a href="<?php echo $download_url ?>" target="_downloadframe"><?php echo $this->translate("download") ?></a>
                <?php else: ?>
                  <div id="download_product_link_<?php echo $item->orderdownload_id ?>">
                    <a href="javascript:void(0)" onclick="downloadProduct('<?php echo $download_url ?>', <?php echo $item->orderdownload_id ?>)"><?php echo $this->translate("download") ?></a>
                  </div>
                <?php endif; ?>
              <?php else: ?>
                <i><span class="seaocore_txt_light"><?php echo $this->translate($this->getOrderStatus($item->order_status)); ?></span></i>
              <?php endif; ?>
            </td>
          </tr>
    		<?php endforeach; ?>
        </table>
      </div>
    </div>
    <div>
      <div id="store_download_products_previous" class="paginator_previous">
        <?php
              echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                  'onclick' => '',
                  'class' => 'buttonlink icon_previous'
              ));
        ?>
        <span id="manage_spinner_prev"></span>
      </div>
  
      <div id="store_download_products_next" class="paginator_next">
         <span id="manage_spinner_next"></span>
        <?php
              echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
                  'onclick' => '',
                  'class' => 'buttonlink_right icon_next'
              ));
        ?>
      </div>
    </div>
  </div>
  <?php else: ?>
    <div class="tip mtop10"><span>
    <?php echo $this->translate('There are no downloadable product purchased yet.') ?>
      </span></div>
  <?php endif; ?>
</div>
<script type="text/javascript">

function downloadProduct(download_window_url, download_id)
{
  var remaining_download = parseInt($('remaining_download_value_'+download_id).value);
  $('remaining_download_value_'+download_id).value = remaining_download - 1;
  
  // IF USER COMPLETE HIS ALL DOWNLOADS, TEHN DON'T SHOW DOWNLOAD LINK
  if( $('remaining_download_value_'+download_id).value == 0 )
  {
    $('download_product_link_'+download_id).innerHTML = "<i><span><?php echo $this->translate('Max. download limit reached') ?></span></i>";
  }
  $('remaining_download_'+download_id).innerHTML = $('remaining_download_value_'+download_id).value;
  
  var download = parseInt($('download_value_'+download_id).value);
  $('download_value_'+download_id).value = download + 1;
  $('download_'+download_id).innerHTML = $('download_value_'+download_id).value;

  window.open(download_window_url,'downloadframe');
}


en4.core.runonce.add(function(){
    
    var anchor = document.getElementById('download_products_pagination').getParent();
    document.getElementById('store_download_products_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('store_download_products_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('store_download_products_previous').removeEvents('click').addEvent('click', function(){
       $('manage_spinner_prev').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';
    
      
    var tempManagePaginationUrl = '<?php echo $this->url(array('action' => 'account', 'menuType' => 'download-products', 'page' => $this->paginator->getCurrentPageNumber() - 1), 'sitestoreproduct_general', true); ?>';
    if(tempManagePaginationUrl && typeof history.pushState != 'undefined') { 
      history.pushState( {}, document.title, tempManagePaginationUrl );
    }

      
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'sitestoreproduct/product/download-products',
        data : {
              format : 'html',
              subject : en4.core.subject.guid,
              call_same_action : 1,                        
              page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
        },
         onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {        
            $('manage_spinner_prev').innerHTML = '';
          }
      }), {
        'element' : anchor
      })
    });

    $('store_download_products_next').removeEvents('click').addEvent('click', function(){
     $('manage_spinner_next').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';
    
    var tempManagePaginationUrl = '<?php echo $this->url(array('action' => 'account', 'menuType' => 'download-products', 'page' => $this->paginator->getCurrentPageNumber() + 1), 'sitestoreproduct_general', true); ?>';
    if(tempManagePaginationUrl && typeof history.pushState != 'undefined') { 
      history.pushState( {}, document.title, tempManagePaginationUrl );
    }

      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'sitestoreproduct/product/download-products',
        data : {
              format : 'html',
              subject : en4.core.subject.guid,
              call_same_action : 1,                        
              page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        },
         onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {        
            $('manage_spinner_next').innerHTML = '';
          }
      }), {
        'element' : anchor
    })
  });
    
});


</script>