<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
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

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<h3>
  <?php echo $this->translate('Manage Store Coupons'); ?>
</h3>

<p>
  <?php echo $this->translate('Here, you can see all the Store coupons your users have created. You can use this store to monitor these coupons and delete offensive ones if necessary. Here, you can also mark coupons as Hot. Hot coupons are shown in the Hot Store Coupons widget.'); ?>
</p>

<br />

<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction) {

    if (order == currentOrder) {
      $('order_direction').value = (currentOrderDirection == 'ASC' ? 'DESC' : 'ASC');
    } else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }

  function multiDelete()
  {
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected store coupons ?")) ?>');
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

<div class="admin_search">
  <div class="search">
    <form method="post" class="global_form_box" action="">
      <div>
        <label>
          <?php echo $this->translate("Title") ?>
        </label>
        <?php if (empty($this->title)): ?>
          <input type="text" name="title" /> 
        <?php else: ?>
          <input type="text" name="title" value="<?php echo $this->translate($this->title) ?>"/>
        <?php endif; ?>
      </div>
      <div>
        <label>
          <?php echo $this->translate("Coupon Code") ?>
        </label>
        <?php if (empty($this->coupon_code)): ?>
          <input type="text" name="coupon_code" /> 
        <?php else:?>
          <input type="text" name="coupon_code" value="<?php echo $this->coupon_code?>"/>
        <?php endif;?>
      </div>
      <div>
        <label>
          <?php echo $this->translate("Owner") ?>
        </label>	
        <?php if (empty($this->owner)): ?>
          <input type="text" name="owner" /> 
        <?php else: ?> 
          <input type="text" name="owner" value="<?php echo $this->translate($this->owner) ?>" />
        <?php endif; ?>
      </div>
      <div>
        <label>
          <?php echo $this->translate("Store Name") ?>
        </label>
        <?php if (empty($this->sitestore_title)): ?>
          <input type="text" name="sitestore_title" /> 
        <?php else: ?>
          <input type="text" name="sitestore_title" value="<?php echo $this->translate($this->sitestore_title) ?>"/>
        <?php endif; ?>
      </div>
      <div>
        <label>
          <?php echo $this->translate("Hot Coupons") ?>	
        </label>
        <select id="" name="hotoffer">
          <option value="0" ><?php echo $this->translate("") ?></option>
          <option value="2" <?php if ($this->hotoffer == 2) echo "selected"; ?> ><?php echo $this->translate("Yes") ?></option>
          <option value="1" <?php if ($this->hotoffer == 1) echo "selected"; ?> ><?php echo $this->translate("No") ?></option>
        </select>
      </div>
      <div style="margin:10px 0 0 10px;">
        <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>
      </div>
    </form>
  </div>
</div>

<br />

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<?php
if (!empty($this->paginator)) {
  $counter = $this->paginator->getTotalItemCount();
}
if (!empty($counter)):
  ?>
  <div class='admin_members_results'>
    <div>
      <?php echo $this->translate(array('%s store coupon found.', '%s store coupons found.', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
    </div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>
  <br />

  <form id='multidelete_form' method="post" action="<?php echo $this->url(array('action' => 'multi-delete')); ?>" onSubmit="return multiDelete()">
    <table class='admin_table seaocore_admin_table' border="0">
      <thead>
        <tr>
          <th style='width: 1%;' align="left"><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
          <th style='width: 4%;' align="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('offer_id', 'DESC');"><?php echo $this->translate('ID'); ?></a></th>
          <th style='width: 4%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate('Title'); ?></a></th>
          <th style='width: 4%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate('Owner'); ?></a></th>
          <th style='width: 4%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('sitestore_title', 'ASC');"><?php echo $this->translate('Store Name'); ?></a></th>
          <th style='width: 4%;' align="left"><?php echo $this->translate('Coupon Code'); ?></th>
          <th style='width: 4%;' align="left"><?php echo $this->translate('Discount'); ?></th>
          <th style='width: 4%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('hotoffer', 'ASC');"><?php echo $this->translate('Hot Coupons'); ?></a></th>
          <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('sticky', 'ASC');"><?php echo $this->translate('Featured'); ?></a></th>
          <th style='width: 4%;' align="left"><?php echo $this->translate('Approved'); ?></th>
          <th style='width: 4%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate('Starting Date'); ?></a></th>
          <th style='width: 4%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('end_time', 'DESC');"><?php echo $this->translate('Expiration Date'); ?></a></th>
          <th style='width: 4%;' class='admin_table_options' align="left"><?php echo $this->translate('Options'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($counter)): ?>
          <?php foreach ($this->paginator as $item): ?>
            <tr>        
              <td><input name='delete_<?php echo $item->offer_id; ?>' type='checkbox' class='checkbox' value="<?php echo $item->offer_id ?>"/></td>
              <td class="admin_table_centered"><?php echo $item->offer_id ?></td>
              <?php
              $truncation_limit = 16;
              $tmpBody = strip_tags($item->title);
              $item_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
              ?>

              <td class='admin_table_bold' title="<?php echo $item->title; ?>"><?php echo $item_title; ?></td>

              <td class='admin_table_bold'><?php echo $this->htmlLink($this->item('user', $item->owner_id)->getHref(), $item->truncateOwner($this->user($item->owner_id)->username), array('title' => $item->username, 'target' => '_blank')) ?></td>

      <?php
      $truncation_limit = 16;
      $tmpBodytitle = strip_tags($item->sitestore_title);
      $item_sitestoretitle = ( Engine_String::strlen($tmpBodytitle) > $truncation_limit ? Engine_String::substr($tmpBodytitle, 0, $truncation_limit) . '..' : $tmpBodytitle );
      ?>					
              <td class='admin_table_bold'><?php echo $this->htmlLink($this->item('sitestore_store', $item->store_id)->getHref(), $item_sitestoretitle, array('title' => $item->sitestore_title, 'target' => '_blank')) ?></td>
              <?php
      $truncation_limit = 16;
      $tmpcouponcode = strip_tags($item->coupon_code);
      $item_couponcode = ( Engine_String::strlen($tmpcouponcode) > $truncation_limit ? Engine_String::substr($tmpcouponcode, 0, $truncation_limit) . '..' : $tmpcouponcode );
      ?>
              <td class='admin_table_bold' title="<?php echo $item->coupon_code; ?>"><?php echo $item_couponcode; ?></td>
              <?php
              if ($item->discount_type == 1):?>
              <?php
                $price = $item->discount_amount;
                $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
                $view = Zend_Registry::get('Zend_View');
                $priceStr = $view->locale()->toCurrency($price, $currency, array('precision' => 2));?>
                <td class='admin_table_bold' title="<?php echo $item->discount_amount; ?>"><?php echo $priceStr; ?></td>
               
                <?php else:
                ?>
                <td class='admin_table_bold' title="<?php echo $item->discount_amount; ?>"><?php echo $item->discount_amount . '%'; ?></td>
              <?php endif; ?>
              <?php if ($item->hotoffer == 1): ?>
                <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'sitestoreoffer_hotoffer', 'id' => $item->offer_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreoffer/externals/images/sitestoreoffer_approved1.gif', '', array('title' => $this->translate('Remove from Hot Coupons')))) ?> 
                </td>       
              <?php else: ?>  
                <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'sitestoreoffer_hotoffer', 'id' => $item->offer_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestoreoffer/externals/images/sitestoreoffer_approved0.gif', '', array('title' => $this->translate('Add to Hot Coupons')))) ?>
                </td>
              <?php endif; ?>
              <?php if ($item->sticky == 1): ?>
                <td align="center" class="admin_table_centered"> <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/sitestore_goldmedal1.gif' title= '<?php echo $this->translate('Featured'); ?>' >
                <?php else: ?>  
                <td align="center" class="admin_table_centered"> <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/sitestore_goldmedal0.gif' title='<?php echo $this->translate('Un-featured'); ?>'>
                </td>
              <?php endif; ?>
              <?php if ($item->approved == 1): ?>
                <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'sitestoreoffer_approved', 'id' => $item->offer_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif', '', array('title' => $this->translate('Make Dis-Approved')))) ?>
                </td>      
              <?php else: ?>  
                <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'sitestoreoffer_approved', 'id' => $item->offer_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/disapproved.gif', '', array('title' => $this->translate('Make Approved')))) ?>
                </td> 
              <?php endif; ?>
              <td><?php echo $this->translate(gmdate('M d,Y',strtotime($item->start_time))) ?></td>
              <?php $today = date("Y-m-d H:i:s"); ?>
              <?php if ($item->end_settings == 1 && ($item->end_time >= $today)): ?>
                <td><?php echo $this->translate(gmdate('M d,Y', strtotime($item->end_time))) ?></td>
              <?php elseif ($item->end_settings == 0): ?>
                <td><?php echo $this->translate('Never Expires'); ?></td>
              <?php else: ?>
                <td><?php echo $this->translate('Expired'); ?></td>
              <?php endif; ?>
              <td class='admin_table_options'>
                <?php
                echo $this->htmlLink(
                        array('route' => 'sitestoreoffer_details', 'id' => $item->offer_id), $this->translate('details'), array('class' => 'smoothbox'))
                ?>
                |
                 <?php echo $this->htmlLink(array('route' => 'sitestoreoffer_general', 'action' => 'edit','store_id'=>$item->store_id,'offer_id'=>$item->offer_id), $this->translate('edit'), array('target' => '_blank')) ?>	
                |
                <?php
                echo $this->htmlLink(array('route' => 'sitestoreoffer_delete', 'id' => $item->offer_id), $this->translate('delete'), array(
                    'class' => 'smoothbox',
                ))
                ?>
              </td> 
            </tr>
    <?php endforeach; ?>
  <?php endif; ?>
      </tbody>
    </table>
    <br />
    <div class='buttons'>
      <button type='submit'><?php echo $this->translate('Delete Selected'); ?></button>
    </div>
  </form>
    <?php else: ?>
  <div class="tip">
    <span>
  <?php echo $this->translate('No results were found.'); ?>
    </span>
  </div>
<?php endif; ?>

<style type="text/css">
  table.admin_table tbody tr td {
    white-space: nowrap;
  }
  .stores{margin-top:15px;}	
</style>