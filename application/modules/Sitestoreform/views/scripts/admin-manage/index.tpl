<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreform
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php	$layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);?>
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

<h2 class="fleft"><?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin'); ?></h2>
<?php if (count($this->navigationStore)): ?>
  <div class='seaocore_admin_tabs clr'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigationStore)->render()
  ?>
  </div>
<?php endif; ?>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>
<h3><?php echo $this->translate('Manage Forms in Stores'); ?></h3>
<p>
  <?php echo $this->translate('Here, you can monitor and manage the forms created by Store admins for their stores using the Form Extension. You can also disable form for a Store.');?>
</p>
<br />

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>
  <?php 
  	if( !empty($this->paginator) ) {
  		$counter=$this->paginator->getTotalItemCount(); 
  	}
  	if(!empty($counter)): 
  
  ?>
  <div class='admin_members_results'>
		<div>
			<?php echo $this->translate(array('%s store form found.', '%s store forms found.', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
		</div>
		<?php echo $this->paginationControl($this->paginator); ?>
	</div>
	<br />
  <table class='admin_table' width="100%">
    <thead>
      <tr>
        <th align="center" style="width:1%;"><a href="javascript:void(0);" onclick="javascript:changeOrder('store_id', 'DESC');"><?php echo $this->translate('ID'); ?></a></th>
				<th align="left" style="width:2%;"><a href="javascript:void(0);" onclick="javascript:changeOrder('sitestore_title', 'ASC');"><?php echo $this->translate('Store Title');?></a></th>
        <th align="left" style="width:2%;"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate('Form Title'); ?></a></th>
        <th align="left" style="width:5%;"><a href="javascript:void(0);" onclick="javascript:changeOrder('description', 'ASC');"><?php echo $this->translate('Description'); ?></a></th>
        <th align="left" style="width:2%;"><a href="javascript:void(0);" onclick="javascript:changeOrder('status', 'ASC');"><?php echo $this->translate('Status');?></a></th>        
        <th class='admin_table_options' align="left" style="width:2%;"><?php echo $this->translate('Options'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php if(!empty($counter)): ?>
				<?php foreach( $this->paginator as $item ):
						$sitestore = Engine_Api::_()->getItem('sitestore_store', $item->store_id);
						$isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'form');
					if(empty($isManageAdmin)):
						continue;
					endif;
						?>
					<tr>            
			
						<td class="admin_table_centered"><?php echo $item->store_id ?></td>           
						
						<?php             
							$truncation_limit = 13;
							$tmpBodytitle = strip_tags($item->sitestore_title);
							$item_sitestoretitle = ( Engine_String::strlen($tmpBodytitle) > $truncation_limit ? Engine_String::substr($tmpBodytitle, 0, $truncation_limit) . '..' : $tmpBodytitle );             
						?>          
							
						<td class='admin_table_bold'><?php echo $this->htmlLink($this->item('sitestore_store', $item->store_id)->getHref(), $item_sitestoretitle, array('title' => $item->sitestore_title, 'target' => '_blank')) ?></td>
		
						<?php             
							$truncation_limit = 13;
							$tmpBody = strip_tags($item->title);
							$item_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );             
						?>
						
						<td title="<?php echo $item->title ;?>">
							<?php echo $item_title ;?>
						</td>
						<?php             
							$truncation_limit_decription = 25;
							$tmpBody_decription = strip_tags($item->description);
							$item_description = ( Engine_String::strlen($tmpBody_decription) > $truncation_limit_decription ? Engine_String::substr($tmpBody_decription, 0, $truncation_limit_decription) . '..' : $tmpBody_decription );             
						?>
						<?php if(!empty($item->description)):?> 
						<td title="<?php echo $item->description ;?>">
							<?php echo $item_description ;?>
						</td>
						<?php else: ?>
							<td><?php echo '-' ?></td>
						<?php endif; ?>
						<?php if($item->status == 0): ?>
							<td><?php echo $this->translate('Disabled'); ?></td>
						<?php elseif($item->storeformactive == 1 ): ?>
							<td><?php echo $this->translate('Activated'); ?></td>
						<?php else: ?>
							<td><?php echo $this->translate('De-activated'); ?></td>
						<?php endif; ?>
						
						<?php $tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreform.sitestore-viewform', $item->store_id, $layout);	?>				
							<td class='admin_table_options' style="white-space: nowrap;">	
								<?php if($item->status == 1):?>							 
									<?php echo $this->htmlLink($this->item('sitestore_store', $item->store_id)->getHref(array('tab'=> $tab_id)), $this->translate('view form'), array('target' => '_blank')) ?>
										|
								<?php endif;?>
							<?php if($item->status == 1):?> 
								<?php echo $this->htmlLink(array('route' => 'sitestoreform_disable', 'id' => $item->store_id),$this->translate('disable form'), array(
								'class' => 'smoothbox')) ?>
							<?php else: ?>
							<?php echo $this->htmlLink(array('route' => 'sitestoreform_disable', 'id' => $item->store_id), $this->translate('enable form'), array(
								'class' => 'smoothbox')) ?> 
							<?php endif;?> 
						</td>
					</tr>
				<?php endforeach; ?>
		  <?php endif; ?>
    </tbody>
  </table>
  <?php  echo $this->paginationControl($this->paginator); ?><br  />
<?php else: ?>
	<div class="tip">
		<span>
			<?php echo $this->translate('No results were found.');?>
		</span>
	</div>
<?php endif; ?>
<br />