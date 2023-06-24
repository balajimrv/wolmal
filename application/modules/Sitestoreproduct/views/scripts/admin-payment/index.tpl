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

<h3><?php echo $this->translate("Payment Requests from Sellers"); ?></h3>
<p class="description"><?php echo $this->translate('Below, you can manage payment requests made by the sellers on your site for the sales from their stores. You can approve payment requested by the sellers by using the "approve" link and while approving, you can add response message and choose the amount to approve. You can also use the filters below to filter the requests.'); ?></p>

<div class="admin_search sitestoreproduct_admin_search">
  <div class="search">
    <form method="post" class="global_form_box" action="">
		<input type="hidden" name="post_search" /> 
      <div>
	      <label>
	      	<?php echo  $this->translate("Title") ?>
	      </label>
	      <?php if( empty($this->title)):?>
	      	<input type="text" name="title" /> 
	      <?php else: ?>
	      	<input type="text" name="title" value="<?php echo $this->translate($this->title)?>"/>
	      <?php endif;?>
      </div>

          
      <div>
	      <label>
	      	<?php echo  $this->translate("Request Date: ex (2000-12-25)") ?>
	      </label>
	      <?php if( empty($this->request_date)):?>
	      	<input type="text" name="request_date" /> 
	      <?php else: ?>
	      	<input type="text" name="request_date" value="<?php echo $this->translate($this->request_date)?>"/>
	      <?php endif;?>
      </div>

    <div>
	      <label>
	      	<?php echo  $this->translate("Response Date: ex (2000-12-25)") ?>
	      </label>
	      <?php if( empty($this->response_date)):?>
	      	<input type="text" name="response_date" /> 
	      <?php else: ?>
	      	<input type="text" name="response_date" value="<?php echo $this->translate($this->response_date)?>"/>
	      <?php endif;?>
      </div>
    
    <div>
	      <label>
	      	<?php echo  $this->translate("Requested Amount") ?>
	      </label>
      <div>
	      <?php if( $this->request_min_amount == ''):?>
	      	<input type="text" name="request_min_amount" placeholder="min" class="input_field_small" /> 
	      <?php else: ?>
	      	<input type="text" name="request_min_amount" placeholder="min" value="<?php echo $this->translate($this->request_min_amount)?>" class="input_field_small" />
	      <?php endif;?>

	      <?php if( $this->request_max_amount == ''):?>
	      	<input type="text" name="request_max_amount" placeholder="max" class="input_field_small" /> 
	      <?php else: ?>
	      	<input type="text" name="request_max_amount" placeholder="max" value="<?php echo $this->translate($this->request_max_amount)?>" class="input_field_small" />
	      <?php endif;?>
          </div>
            
      </div>
    
    <div>
	      <label>
	      	<?php echo  $this->translate("Response Amount") ?>
	      </label>
      <div>
	      <?php if( $this->response_min_amount == ''):?>
	      	<input type="text" name="response_min_amount" placeholder="min" class="input_field_small" /> 
	      <?php else: ?>
	      	<input type="text" name="response_min_amount" placeholder="min" value="<?php echo $this->translate($this->response_min_amount)?>" class="input_field_small" />
	      <?php endif;?>

	      <?php if( $this->response_max_amount == ''):?>
	      	<input type="text" name="response_max_amount" placeholder="max" class="input_field_small" /> 
	      <?php else: ?>
	      	<input type="text" name="response_max_amount" placeholder="max" value="<?php echo $this->translate($this->response_max_amount)?>" class="input_field_small" />
	      <?php endif;?>
          </div>
            
      </div>
    
      <div>
	    	<label>
	      	<?php echo  $this->translate("Request Status") ?>	
	      </label>
        <select id="" name="request_status">
          <option value="0" ><?php echo $this->translate("Select") ?></option>
          <option value="1" <?php if( $this->status == 1) echo "selected";?> ><?php echo $this->translate("Requested") ?></option>
          <option value="3" <?php if( $this->status == 3) echo "selected";?> ><?php echo $this->translate("Completed") ?></option>
          <option value="2" <?php if( $this->status == 2) echo "selected";?> ><?php echo $this->translate("Canceled") ?></option>
         </select>
      </div>
 
      <div class="clear mtop10">
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
      <?php echo $this->translate(array('%s payment request found.', '%s payment requests found.', $counter), $this->locale()->toNumber($counter)) ?>
    </div>
  <?php else: ?>
    <div class="tip"><span>
        <?php echo $this->translate("No results were found.") ?></span>
    </div>
  <?php endif; ?> 
</div>
<br />


<?php if (!empty($counter)): ?>
<div style="overflow-x:scroll;">
  <table class='admin_table seaocore_admin_table' style="width: 100%;">
  <thead>
    <tr>
      <?php $class = ( $this->order == 'request_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
					<th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('request_id', 'DESC');"><?php echo $this->translate('ID'); ?></a></th>
          
      <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
					<th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate('Store'); ?></a></th>
          
      <?php $class = ( $this->order == 'request_amount' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
					<th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('request_amount', 'DESC');"><?php echo $this->translate('Requested Amount'); ?></a></th>
          
      <th class='admin_table_short'><?php echo $this->translate("Request Message") ?></th>
      <th class='admin_table_short'><?php echo $this->translate("Request Date") ?></th>
      
      <?php $class = ( $this->order == 'response_amount' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
					<th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('response_amount', 'DESC');"><?php echo $this->translate('Response Amount'); ?></a></th>
      <th class='' style='width: 1%;'><?php echo $this->translate("Response Message") ?></th>
      <th class=''  style='width: 1%;'><?php echo $this->translate("Response Date") ?></th>
      <th class=''  style='width: 1%;'><?php echo $this->translate("Status") ?></th>
      <th class=''  style='width: 1%;'><?php echo $this->translate("Payment") ?></th>
      <th style='width: 3%;'><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <?php foreach( $this->paginator as $payment):?>
  <tr>
    <?php
      if( empty($payment->request_message) || $payment->request_message == '' )
        $request_message = '-';
      else
        $request_message = $payment->request_message;
      
      if( empty($payment->response_message) || $payment->response_message == '' )
        $response_message = '-';
      else
        $response_message = $payment->response_message;
      
//      $request_message = empty($payment->request_message) ? '-' : $payment->request_message; 
//      $response_message = empty($payment->response_message) ? '-' : $payment->response_message;
      $response_amount = empty($payment->response_amount) ? '-' : Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($payment->response_amount); 
      if($payment->response_date == '0000-00-00 00:00:00') 
        $response_date = '-'; 
      else
        $response_date = $payment->response_date; 
      if( $payment->request_status == 0 ):
          $request_status = 'Requested';
        elseif( $payment->request_status == 1 ):
          $request_status = '<i><font color="red">Deleted</font></i>';
        elseif( $payment->request_status == 2 ):
          $request_status = '<i><font color="green">Completed</font></i>';  
        endif;
        
        if( $payment->payment_status != 'active' ):
          $payment_status = 'No';
        else:
          $payment_status = 'Yes';
        endif;  
    ?>
    <td class='admin_table_short'><?php echo $payment->request_id ?></td>
    <td class='admin_table_short admin_table_bold'>
      <?php $storeItem = $this->item('sitestore_store', $payment->store_id); ?>
      <?php if( empty($storeItem) ): ?>
        <i>Store Deleted</i>
      <?php else: ?>
        <?php echo $this->htmlLink($storeItem->getHref(), Engine_Api::_()->sitestoreproduct()->truncation($storeItem->getTitle(), 10), array('title' => $storeItem->getTitle(), 'target' => '_blank')) ?>
      <?php endif; ?>
    </td>
    <td class='admin_table_short'><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($payment->request_amount) ?></td>
    <td class='admin_table_short'><?php echo Engine_Api::_()->sitestoreproduct()->truncation($request_message, 30) ?></td>
    <td class='admin_table_short'><?php echo $payment->request_date ?></td>
    <td class='admin_table_short'><?php echo $response_amount ?></td>
    <td class='admin_table_short'><?php echo Engine_Api::_()->sitestoreproduct()->truncation($response_message, 30) ?></td>
    <td class='admin_table_short'><?php echo $response_date ?></td>
    <td class='admin_table_short'><?php echo $this->translate($request_status) ?></td>
    <td class='admin_table_short'><?php echo $this->translate("%s", $payment_status) ?></td>
    <td>
      <?php if( empty($storeItem) ): ?>
        <?php echo '-'; ?>
      <?php else: ?>
      <?php 
      $delete_url = $this->url(array(
                      'module' => 'sitestoreproduct',
                      'controller' => 'payment',
                      'action' => 'delete-payment-request',
                      'request_id' => $payment->request_id,
                      'store_id' => $payment->store_id
                    ), 'admin_default', true);
      $view_url = $this->url(array(
                      'module' => 'sitestoreproduct',
                      'controller' => 'payment',
                      'action' => 'view-payment-request',
                      'request_id' => $payment->request_id
                    ), 'admin_default', true);
      $make_payment_url = $this->url(array(
                      'module' => 'sitestoreproduct',
                      'controller' => 'payment',
                      'action' => 'process-payment',
                      'request_id' => $payment->request_id
                    ), 'admin_default', true);
      ?>
<?php echo '<a href="javascript:void(0)" onclick="Smoothbox.open(\''.$view_url.'\')"> details </a>';
if( empty($payment->request_status) ): 
  if( $payment->payment_status !== 'active' ): 
    if( $payment->payment_status == 'initial' ): 
      echo  ' | '.$this->htmlLink($this->url(array(
                'module' => 'sitestoreproduct',
                'controller' => 'payment',
                'action' => 'process-payment',
                'request_id' => $payment->request_id,
              ), 'admin_default', true),$this->translate("approve"));
    else :
      echo ' | <a href="javascript:void(0)" onclick="Smoothbox.open(\''.$make_payment_url.'\')">approve payment</a>';
    endif;
  endif; 
  if( $payment->request_status == 0 ) :
    echo ' | <a href="javascript:void(0)" onclick="Smoothbox.open(\''.$delete_url.'\')"> delete </a>';
  endif;
endif;
echo '|' . $this->htmlLink($this->url(array('action' => 'store', 'store_id' => $payment->store_id, 'type' => 'product', 'menuId' => 56, 'method' => 'payment-to-me' ), 'sitestore_store_dashboard', true), $this->translate("store payment details"), array('target' => '_blank'));
?>
      <?php endif; ?>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
</div>  
<div class="clr mtop10">
		<?php echo $this->paginationControl($this->paginator, null, null, array(
				'pageAsQuery' => true,
				'query' => $this->formValues,
			));
		?>
  </div>
	<br />
<?php endif; ?>