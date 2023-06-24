<div class="importlisting_form">

  <h3><?php echo $this->translate('Import products from a CSV file'); ?></h3>

  <p>
    <?php echo $this->translate("This tool allows you to import products corresponding to the entries from a .csv file. Before starting to use this tool, please read the following points carefully."); ?>
  </p>

  <ul class="importlisting_form_list">

    <li>
      <?php echo $this->translate("Don't add any new column in the csv file from which importing has to be done."); ?>
    </li>

    <li>
      <?php echo $this->translate("The data in the files should be pipe('|') separated and in a particular format or ordering. So, there should be no pipe('|') in any individual column of the CSV file. If you want to add comma(',') separated data in the CSV file, then you can select the comma(',') option during the CSV file upload process. Note: There is one drawback of using the comma(',') separated data that you will not be able to use comma in fields like title and description etc. for the entries in the CSV file."); ?>
    </li>

    <li>
      <?php echo $this->translate("Product type and category name are the required fields for all the entries in the file."); ?>
    </li>
    
    <li>
      <?php echo $this->translate("You will not be allowed to import the Group type products and Bundled type products. Please find the allowed products' types below, which you may use in importing CSV file:"); ?>
      <br />
      &nbsp;&nbsp;- <b><?php echo $this->translate('Simple Type Product: simple'); ?></b>
      <br />
      &nbsp;&nbsp;- <b><?php echo $this->translate('Configurable Type Product: configurable'); ?></b>
      <br />
      &nbsp;&nbsp;- <b><?php echo $this->translate('Virtual Type Product: virtual'); ?></b>
      <br />
      &nbsp;&nbsp;- <b><?php echo $this->translate('Downloadable Type Product: downloadable'); ?></b> 
    </li>

    <li>
      <?php echo $this->translate("Attributes of the Configurable type products, Virtual type products and downloadable files of the Downloadable type products will not be allowed to import."); ?>
    </li>

    <li>
      <?php echo $this->translate("You can import the maximum of available products at a time. For example, If your store allow you to create 50 products and you have already created 20 products in the same store then you will allow to import the maximum of 30 products."); ?>
    </li>

    <li>
      <?php echo $this->translate("You can also 'Stop' and 'Rollback' the import process. 'Stop' will just stop the import process going on at that time from that file and 'Rollback' will undo or delete all the products created from that CSV import file till that time."); ?>
    </li>

    <li>
      <?php echo $this->translate("Files must be in the CSV format to be imported. You can also download the demo template below for your reference."); ?>
    </li>
    
    <li>
      <?php echo $this->translate("Please make sure that the products you are importing are not already in the store database. (Double entries for one single product may later result in issues such as stock management errors.)"); ?>
    </li>

  </ul>

  <br />

  <a href=<?php echo $this->url(array('action' => 'download')) ?><?php echo '?path=' . urlencode('example_product_import.csv'); ?> target='downloadframe' class="buttonlink sitestoreproduct_import_icon_download mright5"><?php echo $this->translate('Download the CSV template') ?></a>

  <a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $this->url(array('route' => 'default', 'module' => 'sitestoreproduct', 'controller' => 'import', 'action' => 'import')) ?>')" class ="buttonlink sitestoreproduct_import_icon_import mright5"><?php echo $this->translate('Import a file') ?></a>

  <?php if (!empty($this->canEdit) && !empty($this->rowCount)) : ?>
    <?php $url = $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'import', 'menuId' => 89, 'method' => 'manage'), 'sitestore_store_dashboard', false) ?>
    <a href="<?php echo $url; ?>"class ="buttonlink sitestoreproduct_import_icon_import mright5"><?php echo $this->translate('Manage Imported files') ?></a>
  <?php endif; ?>

  <br />
  <br />

</div>		



<style type="text/css">
  .importlisting_form_list {
    list-style: decimal outside none !important;
    padding-left: 10px;
  }
  .importlisting_form_list > li {
    margin-left: 20px;
    padding: 7px 12px 7px 1px;
  }
  .importlisting_form{
    -moz-border-radius: 7px;
    -webkit-border-radius: 7px;
    /*	//border-radius: 7px;*/
    /*	background-color: #E9F4FA;*/
    padding: 10px;
    float: left;
    overflow: hidden;
    margin-bottom:15px;
  }
  .importlisting_form > div{
    background: #fff;
    border: 1px solid #d7e8f1;
    overflow: hidden;
    padding: 20px;
  }
  .importlisting_elements{
    clear:both;
    border: 1px solid #EEEEEE;
    overflow:hidden;
    float:left;
    width:500px;
  }
  .importlisting_elements span{
    background-color: #F8F8F8;
    clear: both;
    float: left;
    font-size: 13px;
    width: 498px;
    padding:5px 3px;
  }
  .importlisting_elements span + span{
    border-top: 1px solid #EEEEEE;
  }
  .importlisting_elements span img{
    margin-right:10px;
    vertical-align:middle;
    float:left;
    margin-top:3px;
  }
  .importlisting_elements span b{
    float:left;
    margin-right:10px;
    max-width:450px;
  }
  .importlisting_elements span a{
    font-weight:bold;
    cursor:pointer;
  }
  .importlisting_elements span.green{
    color:green;
  }
  .importlisting_elements span.red{
    color:red;
  }
  .importlisting_form > div .import_button{
    clear: both;
    float: left;
    margin-top: 10px;
  }
  .importlisting_form > div .error-message, 
  .importlisting_form > div .success-message{
    float:left;
    width:100%;
  }
  .importlisting_form > div .error-message > span, 
  .importlisting_form > div .success-message > span{
    background-position: 8px 5px;
    background-repeat: no-repeat;
    border-radius: 3px 3px 3px 3px;
    clear: left;
    float: left;
    margin:0 0 15px 0;
    overflow: hidden;
    padding: 5px 15px 5px 32px;
  }
  .importlisting_form > div strong{
    font-weight:bold;
    font-size:14px;
  }
  .sitestoreproduct_import_icon_download {
    background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/download.gif);
  }
  .sitestoreproduct_import_icon_import {
    background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/import.png);
  }

</style>