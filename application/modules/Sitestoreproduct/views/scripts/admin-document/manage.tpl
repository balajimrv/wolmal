<?php $this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_dashboard.css');
        ?>
<h2 class="fleft">
  <?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin');?>
</h2>

<?php if (count($this->navigationStore)): ?>
  <div class='seaocore_admin_tabs clr'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigationStore)->render()
  ?>
  </div>
<?php endif; ?>

<?php if (count($this->navigationSubStore)): ?>
  <div class='tabs'>
  <?php
  echo $this->navigation()->menu()->setContainer($this->navigationSubStore)->render()
  ?>
  </div>
<?php endif; ?>

<div class='tabs'>
  <ul class="navigation">
    <li>
    <?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestoreproduct','controller'=>'document','action'=>'index'), $this->translate('Global Settings'), array())
    ?>
    </li>
    <li class="active">
    <?php
      echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestoreproduct','controller'=>'document','action'=>'manage'), $this->translate('Manage Documents'), array())
    ?>
    </li>			
  </ul>
</div>
<script type="text/javascript">
   <?php $url = $this->url(array('controller' => 'document','action' => 'manage' ), 'admin_default', false); ?>
   tempApproveFlag = 0;
   tempEnableFlag = 0;
   var submitformajax = 1;
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
   function enabledocument(id){
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
          }else{
            $('show_status_image_' + id).innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/region_enable1.gif" />';
          }
        }

      })
    ); 
    }
    }
    function approvedocument(id){

      if(tempApproveFlag == 0){
      tempApproveFlag = 1;
      $('show_approve_image_' + id).innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';
      en4.core.request.send(new Request.JSON({
        url : en4.core.baseUrl + 'sitestoreproduct/dashboard/product-document-approve',
        method : 'POST',
        data : {
          format : 'json',
          doc_id : id
        },
        onSuccess : function(responseJSON) {
          tempApproveFlag = 0;
          if( responseJSON.activeFlag == '0') {
            $('show_approve_image_' + id).innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/region_enable0.gif" />';
            $('show_approve_image_' + id).title = '<?php echo $this->translate("Make Approved") ?>';
          }else{
            $('show_approve_image_' + id).innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/region_enable1.gif" />';
            $('show_approve_image_' + id).title = '<?php echo $this->translate("Make Dis-Approved") ?>';
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
      var cofirmation = confirm("<?php echo $this->translate("Are you sure you want to delete the selected document?") ?>");
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
        window.location.href = '<?php echo $url; ?>';
          }
        }
      })
    ); 
    });   });
  </script>

<div id="store_table_rate">
  <?php if (@count($this->paginator)): ?>

    <div class="clr">
      <form id='multidelete_form_table_product_document' method="post">
        <table class="admin_table" width="100%">
          <thead>
          <tr class="">

            <th class='store_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
            <th><?php echo $this->translate("ID") ?></th>
            <th><?php echo $this->translate("Title") ?></th>
            <th><?php echo $this->translate("Description") ?></th>
              <th class="txt_center"><?php echo $this->translate("Approved") ?></th>
            <th><?php echo $this->translate("Options") ?></th>
          </tr>
          </thead>

          <?php 
          foreach ($this->paginator as $item): ?>
            <tr>
              <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->document_id ?>' value="<?php echo $item->document_id ?>"></td>
              <td> <?php echo $item->document_id; ?> </td>
              <td> <?php echo $this->translate($item->title); ?> </td>
              <td> <?php echo !empty($item->body)? $this->viewMore($this->translate($item->body), 125): '-'; ?> </td>
                    
                  <td class="txt_center">
                  <?php if (!empty($item->approve)): ?>
                    <a id="show_approve_image_<?php echo $item->document_id ?>" href="javascript:void(0);" onclick="approvedocument(<?php echo $item->document_id ?>)" title="<?php echo $this->translate("Make Dis-Approved") ?>">
                      <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable1.gif' ?>" />
                    </a>

                  <?php else: ?>

                    <a id="show_approve_image_<?php echo $item->document_id ?>" href="javascript:void(0);" onclick="approvedocument(<?php echo $item->document_id ?>)" title="<?php echo $this->translate("Make Approved") ?>">
                      <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable0.gif' ?>" />
                    </a>
                  <?php endif; ?>
                </td>
              <td>
                 <?php
                $Editurl = $this->url(array('action' => 'edit-document', 'product_id' => $item->product_id, 'doc_id' => $item->document_id), "sitestoreproduct_dashboard", true);
                  $deleteUrl = $this->url(array('action' => 'delete-document', 'product_id' => $item->product_id, 'doc_id' => $item->document_id), "sitestoreproduct_dashboard", true);
                    ?>
                              <a href= "<?php echo $this->url(array('action' => 'download-document', 'product_id' => $item->product_id, 'file_id' => $item->file_id), "sitestoreproduct_dashboard", true);?>" target='downloadframe'><?php echo $this->translate('download') ?></a>
                    |
                    <a href="<?php echo $this->url(array('action' => 'edit-document', 'doc_id' => $item->document_id, 'product_id' => $item->product_id), "sitestoreproduct_dashboard", true); ?>" target="_blank" ><?php echo $this->translate("edit") ?></a>
                    | 
                    <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'dashboard', 'action' => 'delete-document', 'doc_id' => $item->document_id, 'product_id' => $item->product_id), 'default', false) ?>')"><?php echo $this->translate("delete") ?></a>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
     <div class='buttons fleft'>
                <button type='submit' name="submit"><?php echo $this->translate("Delete Selected") ?></button>
                <span id="delete_selected_document_spinner"></span>
              </div>
      </form> 
    </div>
  <?php else: ?>
    <div id="no_location_tip" class="tip">
      <span>
        <?php echo $this->translate("No document is available for this product.") ?>        </span>
    </div>
  <?php endif; ?>
</div>
     <div>
		<?php echo $this->paginationControl($this->paginator, null, null, array(
				'pageAsQuery' => true,
			));
		?>
      </div>
