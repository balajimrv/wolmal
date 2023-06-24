<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage-rate.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>


<script type="text/javascript">

  function multiDelete()
  {
    return confirm("<?php echo $this->translate("Are you sure you want to delete the selected Taxes?") ?>");
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
  
  function showsmoothbox(url) {
    Smoothbox.open(url);
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

<div class='tabs'>
  <ul class="navigation">
    <li class="<?php echo ( $this->type == 0 ? 'active' : '') ?>">
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'tax', 'type' => 0), $this->translate('Admin Configured Taxes'), array()) ?>

    </li>
    <li class="<?php echo ( $this->type != 0 ? 'active' : '') ?>">
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'tax', 'type' => 1), $this->translate('Sellers Configured Taxes'), array()) ?>
    </li>
  </ul>
</div>

<?php if($this->type == 0 ) : ?>
<h3 style="margin-bottom:6px;"><?php echo $this->translate("%s - Manage Locations", $this->taxTitle); ?></h3>
<?php $backToTaxTitle =  $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'tax', 'type' => $this->type), $this->translate("Back to Admin Configured Taxes"), array('class' => 'seaocore_icon_back buttonlink')) ?>
<p><?php echo $this->translate('Below, you can manage all the locations on which this tax will be applicable.') ?></p>
<?php else : ?>
<h3 style="margin-bottom:6px;"><?php echo $this->translate("%s - Manage Locations", $this->taxTitle); ?></h3>
<?php $backToTaxTitle = $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'tax', 'type' => $this->type), $this->translate("Back to Sellers Configured Taxes"), array('class' => 'seaocore_icon_back buttonlink')) ?>
<p><?php echo $this->translate('Below, you can manage all the locations on which this tax will be applicable.') ?></p>
<?php endif; ?>
<br class="clr" />

<!--<br style="clear:both;" /><br style="margin-bottom:6px;"/>-->

<?php if(empty($this->taxIdInvalid)): ?>
<?php
// SHOW LINK FOR ADD RATE
echo $backToTaxTitle;
echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'tax', 'action' => 'add-rate', 'tax_id' => $this->tax_id), $this->translate("Add Location"), array('class' => 'smoothbox buttonlink seaocore_icon_add'));
?>

<br /><br />

<?php if (count($this->paginator)): ?>
  <form id='multidelete_form' method="post" action="<?php echo $this->url(); ?>" onSubmit="return multiDelete()">
    <table class='admin_table'>
      <thead>
        <tr>
          <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
          <th class='admin_table_short' align="center"><?php echo $this->translate("ID"); ?></th>
          <th align="left" ><?php echo $this->translate("Country"); ?></th>
          <th align="left" ><?php echo $this->translate("Regions / States"); ?></th>
          <th align="center" class="admin_table_centered"><?php echo $this->translate("Tax Rate") ?></th>
          <th align="left" ><?php echo $this->translate("Creation Date") ?></th>
          <th align="center" class="admin_table_centered" ><?php echo $this->translate("Status") ?></th>
          <th align="left" ><?php echo $this->translate("Options") ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($this->paginator as $item): ?>				
          <tr>
            <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->taxrate_id ?>' value="<?php echo $item->taxrate_id ?>" /></td>
            <td class="admin_table_centered"><?php echo $item->taxrate_id ?></td>
            
        <?php if ($item->country == 'ALL'): ?>
                <td align="left" ><?php echo $this->translate("All Countries") ?></td>
              <?php else: ?>
                <td align="left"><?php echo Zend_Locale::getTranslation($item->country, 'country') ?></td>
              <?php endif; ?> 
            
         <?php if ($item->country == 'ALL'): ?>
                <td align="left"> - </td>
              <?php elseif($item->state == 0): ?>
                <td align="left"><?php echo $this->translate('All Regions / States') ?></td>
              <?php else: ?>
                <td align="left"><?php echo $item->region ?></td>
              <?php endif; ?>

         <?php if (empty($item->handling_type)): ?>
                <td align="center" class="admin_table_centered"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($item->tax_value); ?></td>
              <?php else: ?>
                <td align="center" class="admin_table_centered"><?php echo round($item->tax_value, 2) ?> %</td>
              <?php endif; ?>

              <td align="left"><?php echo $this->translate(gmdate('M d,Y, g:i A',strtotime($item->creation_date))) ?></td>
            
            <!--            SOWING STATUS BUTTON ACCORDING TO STATUS IN DATABASE-->
             <?php if (!empty($item->status)): ?>
               <td align="center" class="admin_table_centered"><?php
          echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'tax', 'action' => 'taxrate-enable', 'id' => $item->taxrate_id, 'tax_id' => $this->tax_id, 'type' => $this->type), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable1.gif', '', array('title' => $this->translate('Disable Tax'))))
                    ?></td>
              <?php else: ?>
                <td align="center" class="admin_table_centered">
                      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'tax', 'action' => 'taxrate-enable', 'id' => $item->taxrate_id, 'tax_id' => $this->tax_id, 'type' => $this->type), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable0.gif', '', array('title' => $this->translate('Enable Tax'))))
              ?></td>
              <?php endif; ?>
                
            <td align="left">
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'tax', 'action' => 'edit-rate', 'id' => $item->taxrate_id, 'type' => 'edit'), $this->translate("edit"), array('class' => 'smoothbox'))
              ?>
              |
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'tax', 'action' => 'delete-rate', 'id' => $item->taxrate_id), $this->translate("delete"), array('class' => 'smoothbox'))
              ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <br />
    <div class='buttons'>
      <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
    </div>
  </form>

  <br />

  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("No locations for this tax has been added yet.") ?>
    </span>
  </div>
<?php endif; ?>
  
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no locations available for this tax.") ?>
    </span>
  </div>
<?php endif; ?>

