<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  var importfile_id;
  function startImporting(file_id) {
    var store_id = <?php echo $this->store_id ?>;
    Smoothbox.open($('startImporting').innerHTML);
    importfile_id = file_id;
    en4.core.request.send(new Request({
      url : en4.core.baseUrl+'sitestoreproduct/import/data-import?importfile_id='+importfile_id+'&store_id='+store_id,
      method: 'get',
      data : {
        'store_id' : store_id
        //'format' : 'json',
      },

      onSuccess : function(responseJSON) {
        parent.window.location.reload();
        parent.Smoothbox.close();
					
      }
    }))
  }

  function stopImoprt() {
    var store_id = <?php echo $this->store_id ?>;
    var request = new Request.JSON({
      'url' : en4.core.baseUrl+'sitestoreproduct/import/stop?importfile_id='+importfile_id+'&store_id='+store_id,
      'data' : {
        'format' : 'json'
             
      },
      onSuccess : function(responseJSON) {
        parent.window.location.reload();
        parent.Smoothbox.close();
      }
    });
    request.send();
  }

  function multiDelete()
  {
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure that you want to delete the selected file entries ? These will not be recoverable after being deleted. Note that deleting them will also delete the corresponding entries which were going to be used to import the Products from those files.")) ?>');
  }

  function selectAll()
  {
    var i;
    var multidelete_form = $('multidelete_form');
    var inputs = multidelete_form.elements;
    for (i = 1; i < inputs.length - 1; i++) {
      if (!inputs[i].disabled) {
        inputs[i].checked = inputs[0].checked;
      }
    }
  }


</script>

<div id="startImporting" style="display:none;">
  <center class="bold">
    <?php echo $this->translate("Importing products..."); ?>
  </center>
  <center class="mtop10">
    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreproduct/externals/images/loader.gif" alt="Importing products" />
  </center>
  <br />
  <center><button name="submit" id="submit" type="submit" onclick='stopImoprt();'><?php echo $this->translate("Stop"); ?></button></center>
</div>
<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'> <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?> </div>
<?php endif; ?>
<h3><?php echo $this->translate("Manage CSV Import Files"); ?></h3>

<?php if ($this->paginator->getTotalItemCount()): ?>	

  <p class="form-description"><?php echo $this->translate('This page contains all the CSV files uploaded by you for importing Products from them. You can start, stop, rollback and delete the import corresponding to each file now. Rollback will delete all the products imported from that file. Delete will only delete those entries which were going to be used to import the corresponding Products from that file and also delete the entry of that file from here. Below are the meanings of status for the Files uploaded:'); ?></p> 

  <ul class="importlisting_form_list">

    <li>
      <?php echo $this->translate("Pending: You have not started the product importing from this file. Click on 'start' link and start importing."); ?>
    </li>

    <li>
      <?php echo $this->translate("Running: You have started the product importing from this file."); ?>
    </li>

    <li>
      <?php echo $this->translate("Stopped: You have stopped the product importing from this file. You can continue it anytime from the same point."); ?>
    </li>

    <li>
      <?php echo $this->translate("Completed: Product importing has been done successfully from this file."); ?>
    </li>

  </ul>
  <br />
  <p class="form-description"><?php echo $this->translate('NOTE: Please start only one import at a time. Initializing more than 1 parallel imports may create problems.') ?></p> 
  <br />
  <?php if ((!empty($this->maxLimit)) && ($this->maxLimit < 1)) : ?>
     <ul class="form-errors"> 
    <li>
      <?php echo $this->translate('Maximum product creation limit reached.'); ?>
    </li>
     </ul>
 <?php endif; ?>
<?php $url = $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'import', 'menuId' => 89, 'method' => 'index'), 'sitestore_store_dashboard', false) ?>
<a href="<?php echo $url ?>" class="buttonlink sitestoreproduct_import_icon_import"><?php echo $this->translate("Import a new File"); ?></a>
&nbsp;
  <?php $productInfo = $this->paginator->getPages(); ?>

  <?php echo $this->translate("Showing %s-%s of %s files.", $productInfo->firstItemNumber, $productInfo->lastItemNumber, $productInfo->totalItemCount); ?>
  
  <form id='multidelete_form' name='multidelete_form' method="post" action="<?php echo $this->url(array('action' => 'multi-delete', 'store_id' => $this->store_id)); ?>" >
    <div class="product_detail_table sitestoreproduct_data_table fleft mbot10">
      <table class="mbot10" width='100%'>
        <thead>
          <tr class="product_detail_table_head">
         <?php if(!empty($this->canEdit)):?>   <th width="1%" align="left"><input onclick="selectAll()" type='checkbox' class='checkbox'></th> <?php endif; ?>
            <th width="30%" align="left"><?php echo $this->translate("File Name"); ?></th>
            <th width="15%" align="left"><?php echo $this->translate("Upload Date / Time"); ?></th>            <th width="10%" align="left"><?php echo $this->translate("Status"); ?></th>
        <?php if(!empty($this->canEdit)): ?>    <th width="20%" align="left"><?php echo $this->translate("Options"); ?></th> <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($this->paginator as $item): ?>
            <tr>
              <?php if(!empty($this->canEdit)):?> 
              <td width="1%">
                <input name='delete_<?php echo $item->importfile_id; ?>' type='checkbox' class='checkbox' value="<?php echo $item->importfile_id ?>"/>
              </td>
              <?php endif; ?>
              <td width="30%">
                <span title='<?php echo $item->filename; ?>'>
                  <?php $widget_title = strip_tags($item->filename);
                  $widget_title = Engine_String::strlen($widget_title) > 50 ? Engine_String::substr($widget_title, 0, 50) . '..' : $widget_title; ?>
                  <?php echo $widget_title ?>
                </span>
              </td>

              <td width="15%">
                <?php echo $item->creation_date ?>
              </td>

              <td width="10%">
                <span title='<?php echo $item->status; ?>'>
                  <?php if ($item->status == 'Pending'): ?>
                    <?php echo $this->translate("Pending"); ?>
                  <?php elseif ($item->status == 'Running'): ?>
                    <?php echo $this->translate("Running"); ?>
                  <?php elseif ($item->status == 'Completed'): ?>
                    <?php echo $this->translate("Completed"); ?>
                  <?php elseif ($item->status == 'Stopped'): ?>
                    <?php echo $this->translate("Stopped"); ?>
                  <?php endif; ?>
                </span>
              </td>
 <?php if(!empty($this->canEdit)): ?> 
              <td width="20%" style="text-align:left;">
                <?php if ($item->status != 'Running'): ?>
                  <?php if ($item->status != 'Completed' && empty($this->runningSomeImport) && ($this->maxLimit >= 0)): ?>
                    <a href="javascript:void(0);" onclick='startImporting("<?php echo $item->importfile_id ?>");'><?php echo $this->translate('Start') ?></a>
                    |
                  <?php endif; ?>
                  <?php if ($item->status != 'Pending'): ?>
                    <a href="javascript:void(0)" onClick="Smoothbox.open('<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'import', 'action' => 'rollback', 'importfile_id' => $item->importfile_id)) ?>')"><?php echo $this->translate('Rollback'); ?></a>
                    |

                  <?php endif; ?>
                    <a href="javascript:void(0)" onClick="Smoothbox.open('<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'import', 'action' => 'delete', 'importfile_id' => $item->importfile_id)) ?>')"><?php echo $this->translate('Delete'); ?></a>
                <?php else: ?>
                  <?php echo $this->htmlLink(array('module' => 'sitestoreproduct', 'controller' => 'import', 'action' => 'stop', 'importfile_id' => $item->importfile_id, 'store_id' => $this->store_id), $this->translate('Stop')) ?>
                <?php endif; ?>
              </td>
              <?php endif; ?>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <br /><?php echo $this->paginationControl($this->paginator); ?>
    <br />
    &nbsp;<button type='submit' name="delete" onclick="return multiDelete()" value="delete_image"><?php echo $this->translate('Delete Selected'); ?></button>	&nbsp;&nbsp;&nbsp;
  </form>

<?php else: ?>
  <ul class="form-errors">
    <li>
      <?php echo $this->translate('You have not imported any file yet.'); ?>
    </li>
  </ul>	
<?php endif; ?>

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
    /*    border-radius: 7px;
        background-color: #E9F4FA;*/
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
  .importlisting_form > div .success-message > span{
    border: 1px solid #CCCCCC;
    background-color: #E9FAEB;
  }
  .importlisting_form > div .error-message > span{
    border: 1px solid #ff0000;
    background-color: #ffe3e3;
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
