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

<?php if( !empty($this->siteAdminTipMessage) ) : ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("Only seller can create taxes. So there are no 'Admin Configured Taxes'.") ?>
    </span>
  </div>
  <?php return; ?>
<?php elseif( !empty($this->sellerTipMessage) ) : ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("Only site administrator can create taxes. So there are no 'Sellers Configured Taxes'.") ?>
    </span>
  </div>
  <?php return; ?>
<?php endif; ?>

<?php if( empty($this->showTipMessage) && empty($this->showVatForm) ) : ?>
<script type="text/javascript">

var currentOrder = '<?php echo $this->order ?>';
	var currentOrderDirection = '<?php echo $this->order_direction ?>';
	var changeOrder = function(order, default_direction) {
		if( order == currentOrder ) {
			$('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
		} 
		else {
			$('order').value = order;
			$('order_direction').value = default_direction;
		}
		$('filter_form').submit();
	}
  
  function multiDelete()
	{
		return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected tax entries ?")) ?>');
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

<div class="tip">
  <span>
    <?php echo 'If you enable Direct Payment method for stores on your website from Global Settings, then your Admin configured taxes will become irrelevant. In this case, all Admin configured taxes will be disabled.'; ?>
  </span>
</div>

<?php if($this->type == 0 ) : ?>
<h3 style="margin-bottom:6px;"><?php echo $this->translate("Admin Configured Taxes"); ?></h3>
<p><?php echo $this->translate("Below you can manage the taxes created by you for orders on your site. For each product ordered on your website multiple taxes can be charged. Some of these taxes can be created by you here and other taxes can be created by the sellers for their products. 
<br /><br />You can configure tax depending on order billing / shipping locations. For each tax, you can configure tax percentage / amount for various locations. The amount for the taxes created by you will not come in the payable amount to the sellers whereas the amount for the taxes created by sellers will be payable to them.") ?></p>
<?php else : ?>
<h3 style="margin-bottom:6px;"><?php echo $this->translate("Sellers Configured Taxes"); ?></h3>
<p><?php echo $this->translate("Below you can manage the taxes created by sellers for orders on your site. For each product ordered on your website, sellers can charge multiple taxes for the products created by them.
<br /><br />You can configure tax depending on order billing / shipping locations. For each tax, you can configure tax percentage / amount for various locations. The amount for the taxes created by sellers will be payable to them.") ?></p>
<?php endif; ?>
<br style="clear:both;" />

<div class="admin_search">
  <div class="search">
    <form method="post" class="global_form_box" action="">
		<input type="hidden" name="post_search" /> 
      <div>
	      <label>
	      	<?php echo  $this->translate("Title") ?>
	      </label>
	      <?php if( empty($this->tax_title)):?>
	      	<input type="text" name="tax_title" /> 
	      <?php else: ?>
	      	<input type="text" name="tax_title" value="<?php echo $this->translate($this->tax_title)?>"/>
	      <?php endif;?>
      </div>
    
    <?php if($this->type != 0): ?>
         <div>
	      <label>
	      	<?php echo  $this->translate("Store Title") ?>
	      </label>
	      <?php if( empty($this->store_title)):?>
	      	<input type="text" name="store_title" /> 
	      <?php else: ?>
	      	<input type="text" name="store_title" value="<?php echo $this->translate($this->store_title)?>"/>
	      <?php endif;?>
      </div>
        <?php endif; ?>
          
    <?php if( empty($this->isVatAllow) ) : ?>
      <div>
	    	<label>
	      	<?php echo  $this->translate("Tax depends on") ?>	
	      </label>
        <select id="" name="rate_dependency">
          <option value="0" ></option>
          <option value="1" <?php if( $this->rate_dependency == 1) echo "selected";?> ><?php echo $this->translate("Shipping Address") ?></option>
          <option value="2" <?php if( $this->rate_dependency == 2) echo "selected";?> ><?php echo $this->translate("Billing Address") ?></option>
         </select>
      </div>

      <div>
	    	<label>
	      	<?php echo  $this->translate("Status") ?>	
	      </label>
        <select id="" name="status">
          <option value="0" ></option>
          <option value="1" <?php if( $this->status == 1) echo "selected";?> ><?php echo $this->translate("Disabled") ?></option>
          <option value="2" <?php if( $this->status == 2) echo "selected";?> ><?php echo $this->translate("Enabled") ?></option>
         </select>
      </div>
    <?php endif; ?>
 
      <div class="mtop10">
        <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>
      </div>

    </form>
  </div>
</div>
<br />

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>
<br />


<div class='admin_members_results'>
  <?php
  if (!empty($this->paginator)) {
    $counter = $this->paginator->getTotalItemCount();
  }
  if (!empty($counter)):
    ?>
    <div class="">
      <?php echo $this->translate(array('%s tax entry found.', '%s tax entries found.', $counter), $this->locale()->toNumber($counter)) ?>
    </div>
  <?php endif; ?> 
</div>

<?php if($this->type == 0 && empty($this->directPayment) && empty($this->isVatAllow) ) : ?>
<br />
<?php
// SHOW LINK FOR ADD TAX
echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'tax', 'action' => 'add-tax', 'type' => $this->type), $this->translate("Create New Tax"), array('class' => 'smoothbox buttonlink seaocore_icon_add'));
?>
<br />
<?php endif; ?>
<br />

<?php if (!empty($counter)): ?>
  <form id='multidelete_form' method="post" action="<?php echo $this->url(array('action'=>'multi-delete')); ?>" onSubmit="return multiDelete()">
  <table class='admin_table' style="width: <?php echo empty($this->type)? '55%;':'80%;'; ?>">
    <thead>
      <tr>
        <?php if( empty($this->isVatAllow) ) : ?>
          <th class='admin_table_short' style='width: 1%;' ><input onclick='selectAll();' name="type_<?php echo $this->type ?>" type='checkbox' class='checkbox' /></th>
        <?php endif; ?>
        
        <?php if($this->type == 1): ?>
          <?php $class = ( $this->order == 'tax_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
            <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('tax_id', 'DESC');"><?php echo $this->translate('ID'); ?></a></th>
        <?php endif; ?> 
          
         <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
					<th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate('Title'); ?></a></th>
          
        <?php if($this->type != 0): ?>
         <?php $class = ( $this->order == 'page_title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
					<th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('page_title', 'ASC');"><?php echo $this->translate('Store'); ?></a></th>
        <?php endif; ?>
          
        <?php if( empty($this->isVatAllow) ) : ?>
        <?php $class = ( $this->order == 'rate_dependency' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="<?php echo $class ?>"  title="<?php echo $this->translate('Rate Depends On'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('rate_dependency', 'DESC');"><?php echo $this->translate('Tax depends on'); ?></a></th>
          
			 <?php $class = ( $this->order == 'status' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="<?php echo $class ?> admin_table_centered"  title="<?php echo $this->translate('Status'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('status', 'DESC');"><?php echo $this->translate('Status'); ?></a></th>
        <?php endif; ?>
          
        <th style='width: 3%;'><?php echo $this->translate("Options") ?></th>
      </tr>
      
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <?php if( empty($this->isVatAllow) ) : ?>
            <td class="admin_table_centered">
              <input type='checkbox' class='checkbox' name='delete_<?php echo $item->tax_id ?>' value="<?php echo $item->tax_id ?>" />
            </td>
          <?php endif; ?>
          
          <?php if($this->type == 1): ?>
            <td class='admin-txt-normal'><?php echo $item->tax_id ?></td>
          <?php endif; ?>
            
          <td  title="<?php echo $item->title ?>">
            <?php echo $this->string()->truncate($this->string()->stripTags($item->title), 100) ?>
          </td>
              
          <?php if( !empty($this->type) && !empty($item->store_id) ) : ?>
            <?php $storeItem = $this->item('sitestore_store', $item->store_id); ?>
            <?php if( empty($storeItem) ) : ?>
              <td><i>Store Deleted</i></td>
            <?php else: ?>
              <td  class='admin_table_bold'>
                <?php  echo $this->htmlLink($storeItem->getHref(), Engine_Api::_()->sitestoreproduct()->truncation($storeItem->getTitle(), 20), array('title' => $storeItem->getTitle(), 'target' => '_blank')) ?>
              </td>
            <?php endif; ?>
          <?php endif; ?>
              
          <?php if( empty($this->isVatAllow) ) : ?>
            <!--SHOWING rate_dependency IF 0 : Shipping 1 : Billing  -->
            <?php if($item->rate_dependency == 0): ?>
            <td class=""><?php echo $this->translate("Shipping Address") ?></td>
            <?php else: ?>
            <td class=""><?php echo $this->translate("Billing Address") ?></td>
            <?php endif; ?>
              
            <!--SHOWING STATUS BUTTON ACCORDING TO STATUS IN DATABASE-->
            <?php if (!empty($item->status)): ?>
              <td align="center" class="admin_table_centered">
                <?php if( empty($this->directPayment) ) : ?>
                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'tax', 'action' => 'tax-enable', 'id' => $item->tax_id, 'type' => $this->type), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable1.gif', '', array('title' => $this->translate('Disable Tax')))) ?>
                <?php else: ?>
                <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable1.gif', '') ?>
                <?php endif; ?>
              </td>
             <?php else: ?>
               <td align="center" class="admin_table_centered">
                 <?php if( empty($this->directPayment) ) : ?>
                     <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'tax', 'action' => 'tax-enable', 'id' => $item->tax_id, 'type' => $this->type), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable0.gif', '', array('title' => $this->translate('Enable Tax')))) ?>
               <?php else: ?>
                 <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable0.gif', '') ?>
               <?php endif; ?>
               </td>
             <?php endif; ?>
          <?php endif; ?>
                      
          <td class='admin_table_options' >
            <?php if( empty($this->type) ) : ?>
              <?php if( empty($this->isVatAllow) ) : ?>
                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'tax', 'action' => 'manage-rate', 'tax_id' => $item->tax_id, 'type' => $this->type), $this->translate("manage locations")) ?> | 
                <?php if( empty($this->directPayment) ) : ?>
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'tax', 'action' => 'edit-tax', 'id' => $item->tax_id), $this->translate("edit"), array('class' => 'smoothbox')) ?> | 
                <?php endif; ?>
                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'tax', 'action' => 'delete-tax', 'id' => $item->tax_id), $this->translate("delete"), array('class' => 'smoothbox')) ?>
              <?php else: ?>
                // open form      
              <?php endif; ?>
            <?php else: ?>
              <?php if( empty($storeItem) ) : ?>
                <?php echo '-'; ?>
              <?php else: ?>
                <?php if( empty($this->isVatAllow) ) : ?>
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'tax', 'action' => 'manage-rate', 'tax_id' => $item->tax_id, 'type' => $this->type), $this->translate("manage locations")); ?> | 
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'tax', 'action' => 'edit-tax', 'id' => $item->tax_id), $this->translate("edit"), array('class' => 'smoothbox')); ?> | 
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'tax', 'action' => 'delete-tax', 'id' => $item->tax_id), $this->translate("delete"), array('class' => 'smoothbox')); ?>
                <?php else: ?>
                  <?php echo $this->htmlLink($this->url(array('action' => 'store', 'store_id' => $item->store_id, 'type' => 'tax', 'menuId' => 52, 'method' => 'vat'), 'sitestore_store_dashboard', false), 'edit', array('target' => '_blank')); ?>
                <?php endif; ?>
              <?php endif; ?>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
   
    <?php if( empty($this->isVatAllow) ) : ?>
    <div class='buttons'>
      <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
    </div>
    <?php endif; ?>
  </form>
<br />
  <div>
		<?php echo $this->paginationControl($this->paginator, null, null, array(
				'pageAsQuery' => true,
				'query' => $this->formValues,
			));
		?>
  </div>
	<br />
  
<?php elseif($this->type == 0): ?>
  <div id="no_location_tip" class="tip">
        <span>
      <?php echo $this->translate("There are no taxes found.") ?>        
        </span>
      </div>
  <?php else: ?>
  <div id="no_location_tip" class="tip">
        <span>
      <?php echo $this->translate("There are no taxes found.") ?>        
        </span>
      </div>
<?php endif; ?>
<?php elseif( !empty($this->showVatForm) ) : ?>
  <div class='seaocore_settings_form'>
    <div class='settings'>
      <?php echo $this->form->render($this); ?>
    </div>
  </div>
  
  <script type="text/javascript">
    window.addEvent('domready', function() {
      showPriceType();
    });
    function showPriceType(){
      if(document.getElementById('handling_type')){
        if(document.getElementById('handling_type').value == 1) {
          document.getElementById('tax_price-wrapper').style.display = 'none';
          document.getElementById('tax_rate-wrapper').style.display = 'block';

        } else{
          document.getElementById('tax_price-wrapper').style.display = 'block';
          document.getElementById('tax_rate-wrapper').style.display = 'none';
        }
      }
    }
  </script>
<?php endif;