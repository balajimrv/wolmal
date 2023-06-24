<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage-day-items.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">

  function multiDelete()
  {
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected review items ?")) ?>');
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

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>



<?php include_once APPLICATION_PATH . '/application/modules/Sitestorereview/views/scripts/_navigationAdmin.tpl'; ?>

<div class='clear'>
  <div class='settings'>
    <form id='multidelete_form' method="post" action="<?php echo $this->url(array('module' => 'sitestorereview', 'controller' => 'settings', 'action' => 'multi-delete'), 'admin_default'); ?>" onSubmit="return multiDelete()" class="global_form">
      <div>
        <h3><?php echo $this->translate("Review of the Day widget") ?> </h3>
        <p class="description">
          <?php echo $this->translate("Add and Manage the reviews on your site to be shown in the Review of the Day widget. You can also mark these items for future dates such that the marked review automatically shows up as Review of the Day on the desired date. Note that for this review of the day to be shown, you must first place the Review of the Day widget at the desired location.") ?>
        </p>
        <?php
        echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestorereview', 'controller' => 'settings', 'action' => 'add-day-item'), $this->translate('Add a Review of the Day'), array(
            'class' => 'smoothbox buttonlink',
            'style' => 'background-image: url('.$this->layout()->staticBaseUrl.'application/modules/Core/externals/images/admin/new_category.png);'))
        ?>	<br/>	<br/>
        <?php if ($this->paginator->getTotalItemCount() > 0): ?>
          <table class='admin_table' width="100%">
            <thead>
              <tr>
								<?php $class = ( $this->order == 'engine4_sitestorereview_reviews.title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                <th style='width: 1%;' align="left"><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
                <th width="250" align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_sitestorereview_reviews.title', 'DESC');"><?php echo $this->translate("Review Title") ?></a>
								</th>

                <th width="200" align="left" class="<?php echo $class ?>"><?php echo $this->translate("Store Title") ?></th>

                <?php $class = ( $this->order == 'start_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                <th width="100" align="center" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('start_date', 'DESC');"><?php echo $this->translate("Start Date") ?></a></th>
                <?php //Start End date work  ?>
                <?php $class = ( $this->order == 'end_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                <th width="100" align="center" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('end_date', 'DESC');"><?php echo $this->translate("End Date") ?></a></th>
                <?php //End End date work  ?>
                <th width="100" align="center"><?php echo $this->translate("Option") ?></th>
              </tr>
            </thead>
            <tbody>
							<?php foreach ($this->paginator as $item): ?>
                <tr>
                  <td><input name='delete_<?php echo $item->itemoftheday_id; ?>' type='checkbox' class='checkbox' value="<?php echo $item->itemoftheday_id ?>"/></td>

                  <td class='admin_table_bold admin-txt-normal' title="<?php echo $this->translate($item->getTitle()) ?>">
                    <a href="<?php echo $this->url(array('owner_id' => $item->owner_id, 'review_id' => $item->resource_id), 'sitestorereview_detail_view') ?>"  target='_blank'>
                    <?php echo $this->translate(Engine_Api::_()->sitestore()->truncation($item->title, 40)) ?></a>
                  </td>

									<td class='admin_table_bold admin-txt-normal'>
										<?php $store = Engine_Api::_()->sitestorereview()->getLinkedStore($item->resource_id); ?>
										<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($store->store_id, $store->owner_id), Engine_Api::_()->sitestore()->truncation($store->title, 40), array('title' => $store->title)); ?>
									</td>

                  <td class="admin_table_centered"><?php echo $item->start_date ?></td>
                  <?php //Start End date work ?>
                  <td class="admin_table_centered"><?php echo $item->end_date ?></td>
                  <?php //End End date work  ?>
                  <td class="admin_table_centered">
										<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestorereview', 'controller' => 'settings', 'action' => 'delete-day-item', 'id' => $item->itemoftheday_id), $this->translate('delete'), array('class' => 'smoothbox',)) ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table><br />
          <div class='buttons'>
            <button type='submit'><?php echo $this->translate('Delete Selected'); ?></button>
          </div>
        <?php else: ?>
          <div class="tip"><span><?php echo $this->translate("No reviews have been marked as Review of the Day."); ?></span> </div><?php endif; ?>
      </div>
    </form>
  </div>
</div>
<?php echo $this->paginationControl($this->paginator); ?>