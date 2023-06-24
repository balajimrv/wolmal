<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: print-tag.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $countObjects = @count($this->printingTags) ?>
<script type="text/javascript" >
  var submitformajax = 1;
</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_DashboardNavigation.tpl'; ?>

<div class="sr_sitestoreproduct_dashboard_content">
  <?php
  if (!empty($this->sitestoreproduct) && !empty($this->sitestore)):
    echo $this->partial('application/modules/Sitestoreproduct/views/scripts/dashboard/header.tpl', array('sitestoreproduct' => $this->sitestoreproduct, 'sitestore' => $this->sitestore));
  endif;
  ?>
  <?php if (!empty($countObjects)): ?>
    <?php echo $this->translate("Select the product tag configuration below, and click print to print out the tag for this Product."); ?>
    <div class="sitestoreproduct_data_table product_detail_table fleft mbot10">
      <table class="mbot10">
        <tr class="product_detail_table_head">
          <?php if (!empty($this->canEdit)) : ?>

            <th class='store_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
          <?php endif; ?>

          <th><?php echo $this->translate("Tag Name") ?></th>
          <th><?php echo $this->translate("Size") ?></th>
          <th><?php echo $this->translate("Option") ?></th>
        </tr>

        <?php foreach ($this->printingTags as $item):?>
          <tr>
            <td title="<?php echo $item->tag_name ?>"><?php echo $this->string()->truncate($this->string()->stripTags($item->tag_name), 140) ?></td>

            <td>
              <?php echo @round($item->width,2) . " X " . @round($item->height,2) . " cm"; ?>
            </td>
            <!-- SHOWING STATUS BUTTON ACCORDING TO STATUS IN DATABASE-->
            <td>
    <a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'printing-tag', 'action' => 'print', 'printingtag_id' => $item->printingtag_id, 'product_id' => $this->product_id), 'default', false) ?>')"><?php echo $this->translate("print"); ?></a>
            
<!--            |
            
    <a href="javascript:void(0)" onclick="Smoothbox.open('<?php //echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'printing-tag', 'action' => 'delete-tag', 'product_id' => $item->product_id,'tag_id' => $item->printingtag_id), 'default', false) ?>')"><?php //echo $this->translate("delete"); ?></a>-->
        
</td>
            
          <?php endforeach; ?>
      </table>
    </div>
  <?php else : ?>

    <div class='tip'><span> <?php echo $this->translate("There are no Printing Tags available for your product."); ?></span></div>
  <?php endif; ?>
</div>  
