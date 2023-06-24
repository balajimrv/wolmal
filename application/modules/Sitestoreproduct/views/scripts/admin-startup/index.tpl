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
<h2>
  <?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin') ?>
</h2>

<script type="text/javascript">

function multiDelete()
{
  return confirm("<?php echo $this->translate("Are you sure you want to delete the selected 'Help & Learn More' pages? They will not be recoverable after being deleted.") ?>");
}

function selectAll()
{
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}
</script>

<?php if( count($this->navigation) ): ?>
<div class='seaocore_admin_tabs'>
  <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>

<h3><?php echo $this->translate('Manage Store Startup Pages') ?></h3>
 <p><?php echo $this->translate("This page enables you to manage the store startup pages of your site, and create attractive sub pages to explain about the stores on your site to get store owners interested. You can offer guidelines to store owners about getting started, basics of stores, success stories of other stores on your site and other aspects. You can also offer tips to store owners to create attractive stores on your community. <br /> The Store Startup section is comprised of 4 pages. You can edit, view and disable pages. The default pages that come with this plugin can be disabled.") ?>
</p>
<br style="clear:both;" />

<?php
	if( count($this->paginator) ):
?>
<form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete();" style="float:left;width:50%">
  <table class='admin_table'>
    <thead>
      <tr>
        <th class='admin_table_short'>
        	<input onclick='selectAll();' type='checkbox' class='checkbox' />
        </th>
        <th class='admin_table_short' align="center">
					<?php 
						if( empty($this->id_orderby) ) { $orderby = 1; } else { $orderby = 0; }
						echo $this->translate("ID");
					?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Page Title"); ?>
        </th>
        
<!--        <th align="left">
        	<?php // echo $this->translate("Short Description"); ?>
        </th>-->
        
        <th align="center">
        	<?php echo $this->translate("Status"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Options"); ?>
        </th>
      </tr>
    </thead>
    <tbody>
    	<?php foreach ($this->paginator as $item): ?>
        <tr>
          <td><input type='checkbox' name='delete_<?php echo $item->startuppages_id ;?>' value='<?php echo $item->startuppages_id  ?>' class='checkbox' value="<?php echo $item->startuppages_id  ?>" <?php if( empty($item->delete) ){ echo 'DISABLED'; } ?> /></td>
          <td class="admin_table_centered"><?php echo $item->startuppages_id; ?></td>
					<?php						
						if ( !empty($item->title) ) {
							$tmpBody = strip_tags($item->title);
							$title = Engine_String::strlen($tmpBody) > 20 ? Engine_String::substr($tmpBody, 0, 20) . '..' : $tmpBody;
						} else {
							$title = '-';
						}
					?>
          <td><?php echo $title; ?></td>
<!--          <td><?php // echo $item->short_description; ?></td>-->
	  <?php if (empty($item->status)) {
      ?>
        <td class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'startup', 'action' => 'status',  'id' => $item->startuppages_id, 'status' => 1), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable0.gif', '', array('title' => $this->translate('Enable Page'))), array('class' => 'smoothbox')) ?></td>
      <?php } else {
      ?>
        <td class=" admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'startup', 'action' => 'status',  'id' => $item->startuppages_id, 'status' => 0), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable1.gif', '', array('title' => $this->translate('Disable Page'))), array('class' => 'smoothbox')) ?></td>
      <?php } ?>
          <td>
          <?php
						if ( empty($item->page_id) ) {
							$pageId = 'none';
						} else {
							$pageId = $item->page_id;
						}
                                                switch($item->startuppages_id){
                                                    case 1: $action = "get-started"; break;
                                                    case 2: $action = "basic"; break;
                                                    case 3: $action = "stories"; break;
                                                    case 4: $action = "tools"; break;
                                                }
						echo $this->htmlLink(array('route' => 'sitestoreproduct_general', 'action' => $action), $this->translate("view"), array('target' => '_blank'));

						echo " | " . $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'startup', 'action' => 'edit', 'startuppages_id' => $item->startuppages_id), $this->translate("edit"));
					?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <br />
<!--  <div class='buttons'>
  	<button type='submit'><?php // echo $this->translate("Delete Selected") ?></button>
  </div>-->
</form>
<br />
<div>
	<?php echo $this->paginationControl($this->paginator); ?>
</div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no help pages available.") ?>
    </span>
  </div>
<?php endif; ?>