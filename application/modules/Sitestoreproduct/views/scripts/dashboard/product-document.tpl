<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: product-document.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>


<script type="text/javascript" >
  var submitformajax = 1;
   tempEnableFlag = 0;
  function selectAll()
  {
    var i;
    var multidelete_form_table_product_doc = $('multidelete_form_table_product_document');
    var inputs = multidelete_form_table_product_doc.elements;
    for (i = 1; i < inputs.length; i++) {
      if (!inputs[i].disabled) {
        inputs[i].checked = inputs[0].checked;
      }
    }
  }
  
  
   function showsmoothbox(url) {
    Smoothbox.open(url);
  }

  function enabletablerate(id){
    if(tempEnableFlag == 0){
      tempEnableFlag = 1;
      $('show_status_image_' + id).innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';
      en4.core.request.send(new Request.JSON({
        url : en4.core.baseUrl + 'sitestoreproduct/dashboard/product-document-enable',
        method : 'POST',
        data : {
          format : 'json',
          doc_id : id
        },
        onSuccess : function(responseJSON) {
          tempEnableFlag = 0;          
          if( responseJSON.activeFlag == '0') {
            $('show_status_image_' + id).innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/region_enable0.gif" />';
            $("show_status_image_" + id).title = '<?php echo $this->translate("Enable Method"); ?>';
          }else{
            $('show_status_image_' + id).innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/region_enable1.gif" />';
            $("show_status_image_" + id).title = '<?php echo $this->translate("Disable Method"); ?>';
          }
        }
      })
      
    ); 
    }
  }

  en4.core.runonce.add(function(){
    $('multidelete_form_table_product_document').removeEvents('submit').addEvent('submit', function(e) {
      e.stop();

      var i;
      var document_id_array = new Array();
      var multidelete_form_table_product_document = $('multidelete_form_table_product_document');
      var inputs = multidelete_form_table_product_document.elements;
      for (i = 1; i < inputs.length; i++) {
        
        if (!inputs[i].disabled) {
          if(inputs[i].checked ){
            if(inputs[i].value){
              document_id_array[i] = inputs[i].value;
            }
          }
        }
      }
      if(document_id_array.length == 0){
        return alert("<?php echo $this->translate("You don't select any document entry. Please select at least one.") ?>");
      }          
      var cofirmation = confirm("<?php echo $this->translate("Are you sure you want to delete the selected documents?") ?>");
      if(cofirmation != 1){
        return;
      }

      en4.core.request.send(new Request.JSON({
        url : en4.core.baseUrl + 'sitestoreproduct/dashboard/multidelete-documents',
        method : 'POST',
        onRequest: function(){
          $('delete_selected_document_spinner').innerHTML = '<img src='+en4.core.staticBaseUrl+'application/modules/Sitestoreproduct/externals/images/loading.gif height=15 width=15>';
        },
        data : {
          format : 'json',
          doc_id : document_id_array
        },
        onSuccess : function(responseJSON) {
          $('delete_selected_document_spinner').innerHTML = '';
          
          if(responseJSON.success == 1){
            window.location.assign('<?php echo $this->url(array('action' => 'product-document', 'product_id' => $this->product_id), "sitestoreproduct_dashboard", true); ?>');
          }
        }
      })
    ); 
    });  
  });
  
</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_DashboardNavigation.tpl'; ?>


<div class="sr_sitestoreproduct_dashboard_content">
   <h3><?php echo $this->translate('Manage Documents') ?></h3>
  <p class="mbot10"><?php echo $this->translate("Store Admin can create and manage documents of their products."); ?></p>
  <?php
  if (!empty($this->sitestoreproduct) && !empty($this->sitestore)):
    echo $this->partial('application/modules/Sitestoreproduct/views/scripts/dashboard/header.tpl', array('sitestoreproduct' => $this->sitestoreproduct, 'sitestore' => $this->sitestore));
  endif;
  ?>

  <div id="product_create_doc">
    <?php $url = $this->url(array('action' => 'create-document', 'product_id' => $this->sitestoreproduct->product_id), "sitestoreproduct_dashboard", true) ?>
    <a href="<?php echo $url; ?>" id="tablerate_addlocation" class="buttonlink seaocore_icon_add" ><?php echo $this->translate("Create Document") ?></a>


    <div id="store_table_rate">
      <?php if (@count($this->paginator)): ?>

        <div class="sitestoreproduct_data_table product_detail_table fleft mbot10">
          <form id='multidelete_form_table_product_document' method="post">
            <table class="mbot10">
              <tr class="product_detail_table_head">
                <?php if (!empty($this->canEdit)) : ?>
                  <th class='store_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
                <?php endif; ?>
                <th><?php echo $this->translate("ID") ?></th>
                <th><?php echo $this->translate("Title") ?></th>
                <th><?php echo $this->translate("Description") ?></th>
                <th><?php echo $this->translate("Approved") ?></th>
                <th class="txt_center"><?php echo $this->translate("Status") ?></th>
                <th><?php echo $this->translate("Options") ?></th>
              </tr>
         <?php foreach ($this->paginator as $item): ?>
              <tr>
                <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->document_id ?>' value="<?php echo $item->document_id ?>"></td>
                 <td> <?php echo $item->document_id; ?> </td>
                <td> <?php echo $this->translate($item->title); ?> </td>
                <td> <?php echo !empty($item->body)?$this->viewMore($this->translate($item->body), 125):'-'; ?> </td>
               
             <td>
                   <?php if (!empty($item->approve)): 
                     echo "<i>".$this->translate("Approved")."</i>";
                     else:
                     echo "<i>".$this->translate("Dis-Approved")."</i>";
                   endif;?>
                  
                </td>
                <!-- SHOWING STATUS BUTTON ACCORDING TO STATUS IN DATABASE-->
                <td class="txt_center">
                  <?php if (!empty($item->status)): ?>
                    <a id="show_status_image_<?php echo $item->document_id ?>" href="javascript:void(0);" onclick="enabletablerate(<?php echo $item->document_id ?>)" title="<?php echo $this->translate("Disable Method") ?>">
                      <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable1.gif' ?>" />
                    </a>

                  <?php else: ?>

                    <a id="show_status_image_<?php echo $item->document_id ?>" href="javascript:void(0);" onclick="enabletablerate(<?php echo $item->document_id ?>)" title="<?php echo $this->translate("Enable Method") ?>">
                      <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable0.gif' ?>" />
                    </a>
                  <?php endif; ?>
                </td>
             
             <td>
                  <?php if (!empty($this->canEdit)) :
                    
                    $Editurl = $this->url(array('action' => 'edit-document', 'product_id' => $this->sitestoreproduct->product_id, 'doc_id' => $item->document_id), "sitestoreproduct_dashboard", true);
                     ?>
                         <a href=<?php echo $this->url(array('action' => 'download-document')) ?><?php echo '?file_id=' . $item->file_id; ?> target='downloadframe'><?php echo $this->translate('download') ?></a>
                    |
                    <a href="javascript:void(0);" onclick="showAjaxBasedContent('<?php echo $Editurl; ?>')" ><?php echo $this->translate("edit") ?></a>
                    | 
                    <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'dashboard', 'action' => 'delete-document', 'doc_id' => $item->document_id, 'product_id' => $this->product_id), 'default', false) ?>')"><?php echo $this->translate("delete") ?></a>
               
                  <?php else: ?>
                    -
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            </table>
            <?php if (!empty($this->canEdit)) : ?>
              <div class='buttons fleft'>
                <button type='submit' name="submit"><?php echo $this->translate("Delete Selected") ?></button>
                <span id="delete_selected_document_spinner"></span>
              </div>
            <?php endif; ?>
            <br/>
          </form> 
        </div>
      
      
        <div>
		<?php echo $this->paginationControl($this->paginator, null, null, array(
				'pageAsQuery' => true,
			));
		?>
      </div>

      <?php else: ?>
      <br />
        <div id="no_location_tip" class="tip">
          <span>
            <?php echo $this->translate("No document is available for this product.") ?>        </span>
        </div>
      <?php endif; ?>
    </div>

  </div>
  <?php //echo $this->form->render($this); ?>
</div>
</div>
<script type="text/javascript">
  miniLoadingImage = 1;
</script>
