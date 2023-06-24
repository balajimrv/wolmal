<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: video-of-day.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
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

<div class='tabs'>
  <ul class="navigation">
    <li class="active">
    <?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestorevideo','controller'=>'settings','action'=>'index'), $this->translate('Stores'), array())
    ?>
    </li>
    <li>
    <?php
      echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestoreproduct','controller'=>'settings','action'=>'show-video'), $this->translate('Products'), array())
    ?>
    </li>			
  </ul>
</div>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<h3>
  <?php echo $this->translate("Video of the Day") ?>
</h3>
<p>
  <?php echo $this->translate('Below you can manage the entries for "Video of the Day" widget. To mark a video, please click on the "Add Video of the Day" link below and select the dates. If more than one videos of the day are found for a date then randomly one will be displayed.') ?>
</p>
<br />
<div class="tip"> <span> <?php echo $this->translate('You should only make those videos as "Video of the Day" which have their view privacy set as \'Everyone\' or \'All Registered Members\'.'); ?> </span> </div>
<br />
<div>
  <a href="<?php echo $this->url(array('action' =>'add-video-of-day')) ?>" class="smoothbox buttonlink seaocore_icon_add" title="<?php echo $this->translate('Add  Video of the Day');?>"><?php echo $this->translate('Add Video of the Day');?></a>
</div>
<br />
<div>
<?php echo $this->videoOfDaysList->getTotalItemCount(). $this->translate(' results found');?>
</div>
<br />
<div>
	<?php if ($this->videoOfDaysList->getTotalItemCount() > 0): ?>
		<div class='admin_search'>
			<?php echo $this->formFilter->render($this) ?>
		</div>
	  <form id='multidelete_form' method="post" action="<?php echo $this->url(array('module' => 'sitestorevideo', 'controller' => 'settings', 'action' => 'multi-delete-video'), 'admin_default'); ?>" onSubmit="return multiDelete()">
		  <table class='admin_table' width="100%">
		    <thead>
		      <tr>
						<th style='width: 1%;' align="left"><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
						<?php $class = ( $this->order == 'engine4_sitestorevideo_videos.title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
						<th width="24%" align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_sitestorevideo_videos.title', 'DESC');"><?php echo $this->translate("Video") ?></a></th>
            <th width="24%" align="left"><?php echo $this->translate("Store Title") ?></th>
						<?php $class = ( $this->order == 'start_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
						<th width="24%" align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('start_date', 'DESC');"><?php echo $this->translate("Start Date") ?></a></th>
						<?php //Start End date work  ?>
						<?php $class = ( $this->order == 'end_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
						<th width="24%" align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('end_date', 'DESC');"><?php echo $this->translate("End Date") ?></a></th>
						<th width="24%" align="left"><?php echo $this->translate("Options");?></th>
		      </tr>
		    </thead>
		    <tbody>
		      <?php foreach ($this->videoOfDaysList as $video): ?>
            <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
						$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.photos-sitestore', $video->store_id, $layout);?>
            <?php $sitestorevideo_object = Engine_Api::_()->getItem('sitestorevideo_video', $video->resource_id);?>
						<td width="1%"><input name='delete_<?php echo $video->itemoftheday_id; ?>' type='checkbox' class='checkbox' value="<?php echo $video->itemoftheday_id ?>"/></td>
						<td width="24%" class=""><?php echo $this->htmlLink($sitestorevideo_object->getHref(array('tab' => $tab_id)),$this->itemPhoto($sitestorevideo_object, 'thumb.normal'),array('title'=>$sitestorevideo_object->getTitle())); ?></td>
            <?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $video->store_id);?>
            <?php             
             	$truncation_limit = 13;
							$tmpBodytitle = strip_tags($sitestore_object->title);
							$item_sitestoretitle = ( Engine_String::strlen($tmpBodytitle) > $truncation_limit ? Engine_String::substr($tmpBodytitle, 0, $truncation_limit) . '..' : $tmpBodytitle );             
            ?>          
              
						<td width="18%" class='admin_table_bold'><?php echo $this->htmlLink($sitestore_object->getHref(), $item_sitestoretitle, array('title' => $sitestore_object->title, 'target' => '_blank')) ?></td>
						<td width="24%"> <?php echo $this->translate(gmdate('M d,Y',strtotime($video->start_date)))?></td>
						<td width="24%"> <?php echo $this->translate(gmdate('M d,Y',strtotime($video->end_date)))?></td>
						<td width="24%">
						<a href='<?php echo $this->url(array('action' => 'delete-video-of-day', 'id' => $video->itemoftheday_id)) ?>' class="smoothbox" title="<?php echo $this->translate("delete") ?>">
						<?php echo $this->translate("delete") ?>
						</a>
						</td>
		      </tr>
		      <?php endforeach;?>
		    </tbody>
		  </table>
		  <br />
		  <div class='buttons'>
		  	<button type='submit'><?php echo $this->translate('Delete Selected'); ?></button>
		  </div>
		</form>
  <?php else: ?>
		<div class="tip"><span><?php echo $this->translate("No videos have been marked as Video of the Day."); ?></span> </div>
  <?php endif;?>
	<br />
	<?php echo $this->paginationControl($this->videoOfDaysList); ?>
</div>
<script type="text/javascript">

  function multiDelete()
  {
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected video ?")) ?>');
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