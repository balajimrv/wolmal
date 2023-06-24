<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: shipping-methods.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_dashboard.css');
?>
<div class="global_form_popup" style="width: 900px;">

  <?php if (empty($this->noCountryEnable)) : ?>
    <div id="no_country_tip" class="tip">
      <span>
        <?php echo $this->translate("There are no location configured by site administrator for the shipment.") ?>
      </span>
    </div>
    <?php
    return;
  endif;
  ?> 

  <div class="invoice_add_details">
    <ul class="o_hidden">
      <li class="mtop5">
        <div class="invoice_order_info fleft"><b><?php echo $this->translate('Product:'); ?>&nbsp;</b></div>
        <div><b><?php echo $this->sitestoreproduct->getTitle(); ?></b></div>
      </li>
      <li class="mtop5">
        <div class="invoice_order_info fleft"><b><?php echo $this->translate('Price:'); ?>&nbsp;</b></div>
        <div><b><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->sitestoreproduct->price); ?></b></div>
      </li>
      <li class="mtop5">
        <div class="invoice_order_info fleft"><b><?php echo $this->translate('Weight:'); ?>&nbsp;</b></div>
        <div><b><?php echo!empty($this->sitestoreproduct->weight) ? @round($this->sitestoreproduct->weight, 2) . " $this->weightUnit" : 'N/A'; ?></b></div>
      </li>
    </ul>
  </div>

  <div class="seaocore_tbs_cont" id="dynamic_menus_content">
    <div class="sitestoreproduct_manage_store">
      <h3><?php echo $this->translate('Shipping Methods of %1s%s%2s', "<a href=" . $this->sitestore->getHref() . " target='_parent'>", $this->sitestore->title, "</a>") ?></h3>

      <div id="manage_order_pagination">

        <div id="store_table_rate">
<?php if (@count($this->paginator)): ?>
            <div class="sitestoreproduct_data_table product_detail_table">
              <table class="mbot10">
                <tr class="product_detail_table_head">
                  <th><?php echo $this->translate("Title") ?></th>
                  <th><?php echo $this->translate("Country") ?></th>
                  <th><?php echo $this->translate("Regions / States") ?></th>
                  <th><?php echo $this->translate("Weight Limit") ?></th>
                  <th><?php echo $this->translate("Delivery Time") ?></th>                
                  <th><?php echo $this->translate("Dependency") ?></th>
                  <th><?php echo $this->translate("Limit") ?></th>
                  <th><?php echo $this->translate("Charge on") ?></th>
                  <th><?php echo $this->translate("Price / Rate") ?></th>
                  <th><?php echo $this->translate("Shipping Price") ?></th>
                </tr>
  <?php foreach ($this->paginator as $item): ?>
                  <tr>
                    <td title="<?php echo $item->title ?>"><?php echo $this->string()->truncate($this->string()->stripTags($item->title), 10) ?></td>
                    <td><?php echo ($item->country != 'ALL' ? Zend_Locale::getTranslation($item->country, 'country') : $this->translate('All')); ?></td>
                    <td><?php echo ($item->country != 'ALL' ? (empty($item->region) ? $this->translate('All') : $item->region_name) : '-') ?></td>                  
                    <td>
                      <?php if ($item->dependency == 1): ?>   
                        <?php echo ' - ' ?>
                      <?php else : ?>
                        <?php echo @round($item->allow_weight_from, 2) . ' - ' . (!empty($item->allow_weight_to) ? round($item->allow_weight_to, 2) . " $this->weightUnit" : $this->translate('NA')) ?>
    <?php endif; ?>
                    </td>
                    <td title="<?php echo $item->delivery_time; ?>">
    <?php echo Engine_Api::_()->sitestoreproduct()->truncation($item->delivery_time, 13); ?>
                    </td>
                    <td>
                      <?php if ($item->dependency == 0): ?>
                        <?php echo $this->translate("Cost") ?>
                      <?php elseif ($item->dependency == 1): ?>
                        <?php echo $this->translate("Weight") ?>
                      <?php else : ?>
                        <?php echo $this->translate("Quantity") ?>
    <?php endif; ?>
                    </td>
                    <td>
                      <?php if ($item->dependency == 0): ?>   
                        <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($item->ship_start_limit) . ' - ' . (!empty($item->ship_end_limit) ? Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($item->ship_end_limit) : $this->translate('NA')) ?>
                      <?php elseif ($item->dependency == 1): ?>
                        <?php echo round($item->ship_start_limit, 2) . ' - ' . (!empty($item->ship_end_limit) ? round($item->ship_end_limit, 2) . " $this->weightUnit" : $this->translate('NA')) ?>
                      <?php else : ?>
                        <?php echo $item->ship_start_limit . ' - ' . (!empty($item->ship_end_limit) ? $item->ship_end_limit : $this->translate('NA')) ?>
    <?php endif; ?>
                    </td>
                    <td>
                      <?php if ($item->dependency == 1): ?>
                        <?php if ($item->ship_type == 0): ?>
                          <?php echo $this->translate("Order Weight") ?>
                        <?php else: ?>
                          <?php echo $this->translate("Per Unit Weight") ?>
                        <?php endif; ?>
                      <?php else : ?>
                        <?php if ($item->ship_type == 0): ?>                   
                          <?php echo $this->translate("Per Order") ?>
                        <?php else: ?>
                          <?php echo $this->translate("Per Item") ?>
                        <?php endif; ?>
    <?php endif; ?>
                    </td>
                    <td>
                          <?php if ($item->handling_type == 0): ?>
                            <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($item->handling_fee); ?>
                          <?php else: ?>
                            <?php echo @round($item->handling_fee, 2) ?>%
                          <?php endif; ?>
                        </td>
                    <td>
                      <?php
                      if ($item->ship_type == 0) {
                        if ($item->handling_type == 0) {
                          $temp_handling_fee = @round($item->handling_fee, 2);
                        } else {
                          $temp_handling_fee = @round(($item->handling_fee / 100) * $this->sitestoreproduct->price, 2);
                        }
                      } else {
                        $temp_handling_fee = @round(ceil($this->sitestoreproduct->weight) * $item->handling_fee, 2);
                      }
                      ?>
    <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($temp_handling_fee); ?>
                    </td>
                  </tr>
  <?php endforeach; ?>
              </table>
              <br/> 
            </div>
<?php else: ?>
            <div id="no_location_tip" class="tip">
              <span>
  <?php echo $this->translate("No shipping methods have been configured yet for this store.") ?>        
              </span>
            </div>
<?php endif; ?>

        </div>
      </div>
    </div>
  </div>
  <button type='button' onclick="javascript:parent.Smoothbox.close()"><?php echo $this->translate("Close") ?></button>
</div>