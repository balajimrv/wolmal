
<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');
?>
<div class='clear global_form_popup sitestore_upload_csv_popup'>
  <div class='settings' id="import_form">
    <?php if (empty($this->flag)) :
      if (empty($this->shipping_method_exist)) : ?>
        <ul class="form-errors"> 
          <li>
            <?php echo $this->translate("No shipping methods have been configured for this store yet. Please %1sclick here%2s to configure shipping methods for your store so that you can start selling.", '<a href="' . $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'index', 'menuId' => '51', 'method' => 'shipping-methods'), 'sitestore_store_dashboard', true) . '" target="_parent">', '</a>'); ?>
          </li>
        </ul>
        <?php
      elseif ($this->maxLimit >= 0) :
        ?> 
        <div class="sr_sitestoreproduct_form_popup">	
          <?php
          if(empty($this->maxLimit)):
            $this->form->setDescription($this->translate("Add a CSV file to import products corresponding to the entries in it, then click 'Submit'. Below, you can also set the privacy of those products."));            
          else:
            $this->form->setDescription($this->translate("Add a CSV file to import products corresponding to the entries in it, then click 'Submit'. Below, you can also set the privacy of those products.") . "<br /><div class='tip'><span>" . $this->translate(array('You can maximum import  %s product.', 'You can maximum import  %s products.', $this->maxLimit), $this->locale()->toNumber($this->maxLimit)) . "</span></div>"); 
          endif;

          $this->form->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));


          echo $this->form->render($this);
          ?>
        </div>
        <?php
      else :
        ?>
        <ul class="form-errors"> 
          <li>
            <?php echo $this->translate("Product creation limit has been reached. You can't create any more product."); ?>
          </li>
        </ul>	
      <?php
      endif;

    else :
      $this->headLink()
              ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css')->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_dashboard.css');


      $url = $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'import', 'menuId' => 89, 'method' => 'imported-files'), 'sitestore_store_dashboard', false);
      ?>
      <div>
        <?php if (!empty($this->errorCount)): ?>
            <ul class="form-errors">
              <?php
              @rsort($this->errorArray);
              foreach ($this->errorArray as $errorArray){
                echo "<li><ul class='errors'><li>" . $errorArray . "</li></ul></li>";
              }
              ?>
            </ul>
       <?php  endif; ?>
          
       

      </div>  
      <?php if ($this->importedObject) : ?>
        <!--        <ul class="form-notices">
                  <li>
        <?php // echo $this->translate("Total sucessfull entries are :" . $this->successfulEntries);  ?>
                  </li>
                </ul>	-->
 <?php 
 $deleteMsg =".";
 if (!empty($this->errorCount)) 
   $deleteMsg = " " . $this->translate("but there are some errors and which are listed above. Please click on %1sdelete import%2s for deleting the following added products and then re-import after resolving above errors.", '<a href="javascript:void(0)" onclick="indexAction();">', '</a>');
 ?>  
        
        <?php if ($this->successfulEntries > 0) : ?>
          <h3><?php echo $this->translate("Added Products"); ?></h3>
          <ul class="form-notices">
              <li>
              <?php echo $this->translate("%s product's added successfully!! please click on 'Go To CSV Manage Page' button for manage the added CSV files and start importing %s", $this->successfulEntries, $deleteMsg); ?>
              </li>
            </ul>

          <div class="product_detail_table sitestoreproduct_data_table fleft mbot10">
            <table class="mbot10">
              <thead>
                <tr class="product_detail_table_head">
                  <th>
                    <?php echo $this->translate("Title"); ?>
                  </th> 
                  <th>
                    <?php echo $this->translate("Description"); ?>
                  </th> 
                  <th>
                    <?php echo $this->translate("Type"); ?>
                  </th> 
                  <th>
                    <?php echo $this->translate("Category"); ?>
                  </th> 
                  <th>
                    <?php echo $this->translate("Start Date"); ?>
                  </th>
                  <th>
                    <?php echo $this->translate("Product SKU"); ?>
                  </th>

                  <th>
                    <?php echo $this->translate("Price"); ?>
                  </th>
                  <th>
                    <?php echo $this->translate("Weight"); ?>
                  </th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($this->importedObject as $obj) : ?>
                  <tr>
                    <td>
                      <?php echo Engine_Api::_()->sitestoreproduct()->truncation($obj->getTitle()); ?>
                    </td>
                    <td>
                      <?php
                      echo Engine_Api::_()->sitestoreproduct()->truncation($obj->description, 35);
                      ?>
                    </td>
                    <td>
                      <?php echo $obj->product_type; ?>
                    </td>
                    <td>
                      <?php echo Engine_Api::_()->sitestoreproduct()->truncation($obj->category); ?>    </td>
                    <td>
                      <?php
                      $arrDate = explode(" ", $obj->start_date);
                      echo ($arrDate[0]);
                      ?>
                    </td>
                    <td>
                      <?php echo $obj->product_code; ?>
                    </td>
                    <td>
                      <?php echo $obj->price; ?>
                    </td>
                    <td>
                      <?php echo $obj->weight; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <?php $url = $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'import', 'menuId' => 89, 'method' => 'manage'), 'sitestore_store_dashboard', false); ?>
 
          <button onclick='manageAction()'><?php echo $this->translate("Go To CSV Manage Page"); ?></button>
           <?php if (!empty($this->errorCount)): ?>
          or
        <a href="javascript:void(0)" onclick="indexAction()"><?php echo $this->translate("delete import"); ?></a>
        <?php endif; ?>
        <?php endif; ?>
      <?php else : ?>
        <ul class="form-errors">
          <li>
            <?php echo $this->translate('You have not imported any file yet.'); ?>
          </li>
        </ul>	

      <?php endif; ?>
      <script type="text/javascript">
        function manageAction(){
          parent.window.location.href = '<?php echo $url; ?>';
          javascript:parent.Smoothbox.close();
        }  
    function indexAction() {
    var store_id = <?php echo $this->store_id ?>;
    var importFileId = <?php echo $this->importFileId ?>;
    en4.core.request.send(new Request({
      url : en4.core.baseUrl+'sitestoreproduct/import/index',
      method: 'get',
      data : {
        'store_id' : store_id,
        'tempFlag' : 1,
        'importFileId' : importFileId,
        'format' : 'json'
      },

      onSuccess : function(responseJSON) {
        parent.window.location.reload();
        parent.Smoothbox.close();
      }
   }))
  }
 
      </script>
    <?php endif; ?>
  </div>
</div>
<script type="text/javascript">
 <?php if(empty($this->isCommentsAllow)) : ?>
$('auth_comment-wrapper').style.display = "none";
<?php endif; ?>
</script>