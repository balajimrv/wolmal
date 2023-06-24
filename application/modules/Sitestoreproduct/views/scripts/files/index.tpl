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

<?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_DashboardNavigation.tpl'; ?>
<?php $baseUrl = $this->layout()->staticBaseUrl; ?>

<div class="sr_sitestoreproduct_dashboard_content">
  <?php
    if( !empty($this->sitestoreproduct) && !empty($this->sitestore) ):
      echo $this->partial('application/modules/Sitestoreproduct/views/scripts/dashboard/header.tpl', array('sitestoreproduct'=>$this->sitestoreproduct, 'sitestore'=>$this->sitestore));
    endif;
  ?>

  <script type="text/javascript">

  function multiDelete()
	{
		return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected files?")) ?>');
	}

  function selectAll()
  {
    var i;
    var multidelete_form = $('multidelete_form_files');
    var inputs = multidelete_form.elements;
    for (i = 1; i < inputs.length; i++) {
      if (!inputs[i].disabled) {
        inputs[i].checked = inputs[0].checked;
      }
    }
  }
  
  function enablefile(id){
    $('show_file_status_image_' + id).innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/loading.gif" />';
    en4.core.request.send(new Request.JSON({
        url : en4.core.baseUrl + 'sitestoreproduct/files/file-enable',
        method : 'POST',
        data : {
          format : 'json',
          product_id : <?php echo $this->product_id ?>,
          downloadablefile_id : id
        },
        onSuccess : function(responseJSON) {            
                  if( responseJSON.activeFlag == '0') {
                    $('show_file_status_image_' + id).innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/region_enable0.gif" />';
                  }else{
                    $('show_file_status_image_' + id).innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/region_enable1.gif" />';
                 }
          }
        })
      ); 
  }    
</script>

<h3><?php echo $this->translate("Downloadable Information") ?></h3>
<p><?php echo $this->translate("Below, you can upload and manage main files and sample files for this product.") ?></p><br/>
<div class="tabs mbot10">
  <ul class="navigation sr_sitestoreproduct_navigation_common">
    <li class="active" >
      <a class="" href="<?php echo $this->url(array('action' => 'index', 'product_id'=>$this->product_id), 'sitestoreproduct_files', true) ?>" ><?php echo $this->translate("Main Files") ?></a>
      <!--<p><?php echo $this->translate("Here, you can upload and manage all the main files for this product. Below, you can also enable / disable files.") ?></p>-->
    </li>
    <li>
      <a class="" href="<?php echo $this->url(array('action' => 'sample', 'product_id'=>$this->product_id), 'sitestoreproduct_files', true) ?>" ><?php echo $this->translate("Sample Files") ?></a>
      <!--<p><?php echo $this->translate("Here, you can upload and manage all the sample files for this product. Below, you can also enable / disable files.") ?></p>-->
    </li>
  </ul>
</div><br />

<p class="mtop10">
  <?php echo $this->translate('Here, you can upload and manage all the main files for this product. Below, you can also enable / disable files.') ?>
</p>
<br />

<a class="buttonlink seaocore_icon_add" href="<?php echo $this->url(array('action' => 'upload-file', 'product_id'=>$this->product_id, 'type' => 'main'), 'sitestoreproduct_files', true) ?>" ><?php echo $this->translate("Upload File") ?></a>
<br />

<div id="main_files_pagination" class='sitestoreproduct_data_table product_detail_table fleft mtop5'>
  <?php if (@count($this->paginator)): ?>
    <form id='multidelete_form_files' method="post" action="<?php echo $this->url(array('action'=>'multi-delete', 'product_id' => $this->product_id, 'type' => 'main'), 'sitestoreproduct_files', true); ?>" onSubmit="return multiDelete()">
      <table>
        <tr class="product_detail_table_head">
          <th class='store_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>       
          <th><?php echo $this->translate("Title") ?></th>
          <th class="txt_center"><?php echo $this->translate("Max Downloads") ?></th>
          <th class="txt_center"><?php echo $this->translate("File Extension") ?></th>
          <th class="txt_center"><?php echo $this->translate("Status") ?></th>
          <th><?php echo $this->translate("Options") ?></th>
        </tr>
        <?php foreach ($this->paginator as $item): ?>				
          <tr>
            <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->downloadablefile_id ?>' value="<?php echo $item->downloadablefile_id ?>" /></td>
            <td title="<?php echo $item->title ?>"><?php echo $this->string()->truncate($this->string()->stripTags($item->title), 100) ?></td>
            <td class="txt_center"><?php echo empty($item->download_limit) ? '<i>'.$this->translate("Unlimited").'</i>' : $item->download_limit; ?></td>
            <td class="txt_center"><?php echo $item->extension ?></td>
            <!-- SOWING STATUS BUTTON ACCORDING TO STATUS IN DATABASE --> 
            <?php if (!empty($item->status)): ?>
              <td class="txt_center">
                <a id="show_file_status_image_<?php echo $item->downloadablefile_id ?>" href="javascript:void(0);" onclick="enablefile(<?php echo $item->downloadablefile_id ?>)" title="<?php echo $this->translate("Disable This File") ?>"><img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable1.gif' ?>" /></a>
              </td>
            <?php else: ?>
              <td class="txt_center">
                <a id="show_file_status_image_<?php echo $item->downloadablefile_id ?>" href="javascript:void(0);" onclick="enablefile(<?php echo $item->downloadablefile_id ?>)" title="<?php echo $this->translate("Enable This File") ?>"><img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable0.gif' ?>" /></a>
              </td>
            <?php endif; ?>
            <td>
              <a href="javascript:void(0);" onclick='Smoothbox.open("<?php echo $this->url(array('action' => 'edit-file', 'product_id'=>$this->product_id, 'downloadablefile_id' => $item->downloadablefile_id, 'type' => 'main'), 'sitestoreproduct_files', true) ?>");return false;' ><?php echo $this->translate("edit") ?></a>
              |
              <a href="javascript:void(0);" onclick='Smoothbox.open("<?php echo $this->url(array('action' => 'delete-file', 'product_id'=>$this->product_id, 'downloadablefile_id' => $item->downloadablefile_id, 'type' => 'main'), 'sitestoreproduct_files', true) ?>");return false;' ><?php echo $this->translate("delete") ?></a>
              |
              <a href="<?php echo $this->url(array('action' => 'download', 'product_id'=>$this->product_id, 'downloadablefile_id' => $item->downloadablefile_id, 'type' => 'main'), 'sitestoreproduct_files', true) ?>" target="downloadframe"><?php echo $this->translate('download') ?></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>

      <div class='buttons mbot10 mtop10' style="text-align : left;">
        <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
      </div>
    </form>
  </div>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate("There are no product files uploaded yet.") ?>
      </span>
    </div>
  <?php endif; ?>
</div>