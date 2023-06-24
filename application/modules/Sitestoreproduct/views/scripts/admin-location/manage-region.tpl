<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage-region.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<script type="text/javascript">

  function multiDelete()
  {
    return confirm("<?php echo $this->translate("Are you sure you want to delete the selected Country Region?") ?>");
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

<h2 class="fleft">
  <?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin');?>
</h2>


<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='settings clr'>
	<h3><?php echo $this->translate("%s - Manage Regions / States", Zend_Locale::getTranslation($this->country, 'country')); ?></h3>
	<p class="description"><?php echo $this->translate('Below, you can add and manage various regions for this country. Sellers and Buyers belonging to the regions / states added in this country will only be able to sell and purchase products on your site.');?></p>
</div>
<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'location', 'action' => 'index'), $this->translate("Back to Shipping Locations"), array('class' => 'seaocore_icon_back buttonlink')); ?>
<?php 
// SHOW LINK FOR ADD LOCATION
echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'location', 'action' => 'add-region', 'country' => $this->country), $this->translate("Add Regions / States"), array('class' => 'smoothbox buttonlink seaocore_icon_add'));
?>

<br /><br />

<?php if (count($this->paginator)): ?>
  <form id='multidelete_form' method="post" action="<?php echo $this->url(); ?>" onSubmit="return multiDelete()">
    <table class='admin_table' style="width: 60%;">
      <thead>
        <tr>
          <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
          <th class='admin_table_short' align="center"><?php echo $this->translate("ID"); ?></th>
          <th class=""><?php echo $this->translate("Country") ?></th>
          <th class=""><?php echo $this->translate("Regions / States") ?></th>
          <th align="center" class="admin_table_centered"><?php echo $this->translate("Status") ?></th>
          <th align="center" class="admin_table_centered"><?php echo $this->translate("Options") ?></th>
        </tr>
      </thead>
      
      <tbody>
        <?php foreach ($this->paginator as $item): ?>
          <tr>
            <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->region_id ?>' value="<?php echo $item->region_id ?>" /></td>
            <td align="center" class="admin_table_centered"><?php echo $item->region_id ?></td>
            <td align="left" class=""><?php
              echo Zend_Locale::getTranslation($item->country, 'country') ?>
            </td>
            <td align="left" class="">
              <?php echo !empty($item->region)? $item->region: 'ALL'; ?>
            </td>

<!--            SOWING STATUS BUTTON ACCORDING TO STATUS IN DATABASE-->
             <?php if (!empty($item->status)): ?>
                     <td align="center" class="admin_table_centered"><?php
          echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'location', 'action' => 'regionenable', 'regionId' => $item->region_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable1.gif', '', array('title' => $this->translate('Disable Region'))))
                    ?></td>
              <?php else: ?>
                      <td align="center" class="admin_table_centered">
                      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'location', 'action' => 'regionenable', 'regionId' => $item->region_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable0.gif', '', array('title' => $this->translate('Enable Region'))))
              ?>
                      </td>
              <?php endif; ?>

            <td align="center" class="admin_table_centered">
              <?php
              if( !empty($item->region) ):
                echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'location', 'action' => 'edit-location', 'id' => $item->region_id), $this->translate("edit"), array('class' => 'smoothbox')) . ' | ';
              endif;

              echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'location', 'action' => 'delete-location', 'id' => $item->region_id), $this->translate("delete"), array('class' => 'smoothbox'))
              ?>
            </td>
          </tr>
  <?php endforeach; ?>
      </tbody>
    </table>

    <br />
    <input type="hidden" name="totalregions" value="<?php echo $id ?>"/>
    <div class='buttons'>
      <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
    </div>
  </form>

  <br/>
  <div>
  <?php echo $this->paginationControl($this->paginator); ?>
  </div>

<?php else: ?>
  <div class="tip">
    <span>
  <?php echo $this->translate("There are no region entry for this country.") ?>
    </span>
  </div>
<?php endif; ?>


