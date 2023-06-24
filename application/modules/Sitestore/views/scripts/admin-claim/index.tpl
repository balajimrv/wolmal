<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
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
  
  en4.core.runonce.add(function(){$$('th.admin_table_short input[type=checkbox]').addEvent('click', function(){ $$('input[type=checkbox]').set('checked', $(this).get('checked', false)); })});

  var delectSelected =function(){
    var checkboxes = $$('input[type=checkbox]');
    var selecteditems = [];
    checkboxes.each(function(item, index){
      var checked = item.get('checked', false);
      var value = item.get('value', false);
      if (checked == true && value != 'on'){
        selecteditems.push(value);
      }
    });
    $('ids').value = selecteditems;
    $('delete_selected').submit();
  }
</script>
  
<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<h2 class="fleft"><?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin'); ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<h3><?php echo $this->translate('Manage Claims for Stores'); ?></h3>
<p><?php echo $this->translate('Whenever someone makes a claim for a store, that claim comes to you (admin) for review. Below, you can configure the settings for store claims and manage the claims made for stores.'); ?></p><br />
    
<?php if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.claimlink', 1)) :?>
	<div class="tip">
		<span><?php echo $this->translate("Note: Store Claims are disabled from Global Settings.") ?></span>
	</div>
<?php else:?>

	<div class='tabs'>
		<ul class="navigation">
		  <li class="active">
		 	<?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestore','controller'=>'claim','action'=>'index'), $this->translate('Claimable Store Creators'), array())
		  ?>
			</li>
			<li>
		  <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestore','controller'=>'claim','action'=>'processclaim'), $this->translate('Store Claims'), array())
		  ?>
			</li>			
		</ul>
	</div>
	
	<div class='clear'>
		<h3><?php echo $this->translate("Claimable Store Creators") ?> </h3>
		<p class="description">
			<?php echo $this->translate('Though using the "Claim a Store" link a user can make a claim for any store, the stores created by users listed below get the "Claim this Store" link on the store itself. This would be useful in cases like if you have certain members whose job is to create only those stores on your site which could later be easily claimed by their rightful owners. Below, you can also add and manage such store creators. (Note that a store can be claimed by a member only if his member level has the permission to do so from the member level settings.)') ?>
		</p>
	  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestore', 'controller' => 'claim', 'action' => 'listclaimmember'), $this->translate('Add Member'), array(
		'class' => 'smoothbox buttonlink icon_sitestore_admin_add'
		)) ?>	
		<br/>	<br/>
		  <?php 
		  	if( !empty($this->paginator) ) {
		  		$counter=$this->paginator->getTotalItemCount(); 
		  	}
		  	if(!empty($counter)): 		  
		  ?>
			<table class='admin_table' width="80%">
				<thead>
					<tr>
						<th style='width: 1%;' class='admin_table_short'><input type='checkbox' class='checkbox' /></th>
						<?php $class = ( $this->order == 'engine4_sitestore_listmemberclaims.user_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
						<th width="70" align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_sitestore_listmemberclaims.user_id', 'DESC');"><?php echo $this->translate("Member Id") ?></a></th>
						<?php $class = ( $this->order == 'engine4_users.displayname' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
						<th width="70" align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_users.displayname', 'DESC');"><?php echo $this->translate("Display Name") ?></a></th>
						<?php $class = ( $this->order == 'engine4_users.username' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
						<th width="70" align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_users.username', 'DESC');"><?php echo $this->translate("Username") ?></a></th>
						<th width="70" align="left"><?php echo $this->translate("Option") ?></th>
					</tr>
				</thead>					
				<tbody>
					<?php foreach ($this->paginator as $item): ?>
						<tr>
							<td><input name='delete_<?php echo $item->listmemberclaim_id ?>' type='checkbox' class='checkbox' value="<?php echo $item->listmemberclaim_id ?>"/></td>
							<td class='admin_table_bold admin-txt-normal'><?php echo $item->user_id;?></td>								
							<td class='admin_table_user'><?php echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $this->item('user', $item->user_id)->username, array('target' => '_blank')) ?></td>								
							<td class='admin_table_user'><?php echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $this->item('user', $item->user_id)->displayname, array('target' => '_blank')) ?></td>		
							<td align="left">
							<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestore', 'controller' => 'claim', 'action' => 'delete-claimable-member', 'user_id'=> $item->user_id), $this->translate('remove'), array(
							'class' => 'smoothbox',)) ?>
							</td>
						</tr>
					<?php  endforeach; ?>							
				</tbody>
			</table>
			<?php echo $this->paginationControl($this->paginator); ?>
		<?php else:?>
			<div class="tip">
			 <span><?php  echo $this->translate("No such member has been found whose stores can be claimed easily."); ?></span> 	                   </div>
		<?php endif;?>				
		<?php if(!empty($counter)):  ?>
			<br>
			 <div class='buttons clear'>
			  <button onclick="javascript:delectSelected();" type='submit'>
			    <?php echo $this->translate("Remove Selected") ?>
			  </button>
			</div>
			<form id='delete_selected' method='post' action='<?php echo $this->url(array('action' =>'multi-delete-claimable-member')) ?>'>
			  <input type="hidden" id="ids" name="ids" value=""/>
			</form>
		<?php endif;?>
	</div>
<?php endif;?>
