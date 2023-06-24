<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreurl
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

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
<?php $show_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.showurl.column', 1);?>
<?php  $edit_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.edit.url', 0);?>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<h3>
  <?php echo $this->translate('Manage Stores / Marketplace - Ecommerce having Banned URLs'); ?>
</h3>
<div class="tip">
	<span>
		<?php echo $this->translate('Note: You can edit Store URLs from here only if you have enabled the fields: “Custom Store URL” and “Edit Custom Store URL” from Global Settings.');?>
	</span>
</div>
<p>
  <?php echo $this->translate('Below is the list of all the Stores on your site that have been assigned short URLs which are banned. Such a situation can arise because of a newly added banned URL. Here, you can edit the URLs of these Stores to remove the URL conflict.');?>
</p>

<br />

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction){

    if( order == currentOrder ) {
      $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }

</script>

  <?php 
  	if( !empty($this->paginator) ) {
  		$counter=$this->paginator->getTotalItemCount(); 
  	}
  	if(!empty($counter)): 
  
  ?>
	<div class='admin_members_results'>
		<div>
			<?php echo $this->translate(array('%s result found.', '%s results found.', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
		</div>
		<?php echo $this->paginationControl($this->paginator); ?>
	</div>
	<br />
		<table class='admin_table' border="0">
			<thead>
				<tr>
					<th style='width: 4%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('word', 'ASC');"><?php echo $this->translate('Banned URL'); ?></th>
					<th style='width: 4%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate('Store Title'); ?></th>
					<th style='width: 4%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('store_url', 'ASC');"><?php echo $this->translate('Store URL'); ?></th>
          <?php if(!empty($show_url) && !empty($edit_url)):?>
						<th style='width: 4%;' class='admin_table_options' align="left"><?php echo $this->translate('Options'); ?></th>
          <?php endif;?>
				</tr>
			</thead>
			<tbody>
				<?php foreach( $this->paginator as $item ): ?>
					<tr>        
						<td class='admin_table_bold'><?php echo $item->word;?></td>
						<?php             
							$truncation_limit = 16;
							$tmpBody = strip_tags($item->title);
							$item_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
						
						?>
						<td class='admin_table_bold'><?php echo $this->htmlLink($this->item('sitestore_store', $item->store_id)->getHref(),$item_title, array('title' => $item->title, 'target' => '_blank')) ?></td>
						<td class='admin_table_bold'><?php echo $item->store_url;?></td>
						<?php if(!empty($show_url) && !empty($edit_url)):?>
							<td class='admin_table_options'>
								<a href='<?php echo $this->url(array('store_id' => $item->store_id), 'sitestore_edit', true) ?>' ><?php echo $this->translate('edit')?></a>
							</td> 
						<?php endif;?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<br />
<?php else: ?>
	<div class="tip">
		<span>
			<?php echo $this->translate('No Stores on your site have been assigned a banned URL.');?>
		</span>
	</div>
<?php endif; ?>
