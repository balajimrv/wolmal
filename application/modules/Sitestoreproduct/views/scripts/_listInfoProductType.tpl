<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _listInfoProductType.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<!-- FOR GROUPED PRODUCT -->
<?php if( $this->sitestoreproduct->product_type == 'grouped' ): ?>
  <?php if( @count($this->groupedProducts) > 0 ) : ?>
    <div class="sitestoreproduct_pcbox_w">
      <div class="sitestoreproduct_pcbox_t">
        <table class="widthfull">
          <thead>
            <tr class="head">
              <th></th>
              <th><?php echo $this->translate("Grouped Products") ?></th>
              <th><?php echo $this->translate("Unit Price") ?></th>
              <th><?php echo $this->translate("Qty") ?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach( $this->groupedProducts as $product ):  ?>
              <tr>
                <td title="<?php echo $product->getTitle() ?>">
                  <?php echo $this->htmlLink($product->getHref(),$this->itemPhoto($product, 'thumb.icon', $product->getTitle())) ?>   
                </td>
                <td title="<?php echo $product->getTitle() ?>">
                  <?php echo $this->htmlLink($product->getHref(), $product->getTitle()); ?>
                </td>
                <td>
                  <?php echo Engine_Api::_()->sitestoreproduct()->getProductDiscount($product, false) ?>
                </td>
                <td>
                  <?php echo $product->min_order_quantity; ?>
                </td>         
              </tr>                
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>  
      <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_productAvailability.tpl'; ?> 
      </div>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('There are no product available for grouping.'); ?>
      </span>
    </div>
  <?php endif; ?>
<?php endif; ?>

<!-- FOR BUNDLED PRODUCT -->
<?php if($this->sitestoreproduct->product_type == 'bundled'): ?>
  <div class="sitestoreproduct_pcbox_w">
    <div class="sitestoreproduct_pcbox_t">
      <table class="widthfull">
        <thead>
          <tr class="head">
            <th></th>
            <th><?php echo $this->translate("Bundled Products") ?></th>
            <th style="width:30%;"><?php echo $this->translate("Qty") ?></th> 
          </tr>
        </thead>
        <tbody>
          <?php foreach( $this->bundledProducts as $product ):  ?>
            <tr>
              <td title="<?php echo $product->getTitle() ?>">
                <?php echo $this->htmlLink($product->getHref(),$this->itemPhoto($product, 'thumb.icon', $product->getTitle())) ?>   
              </td>
              <td title="<?php echo $product->getTitle() ?>">
                <?php echo $this->htmlLink($product->getHref(), $product->getTitle()) ?>
                
                <?php if( ($product->product_type == 'configurable' || $product->product_type == 'virtual') && !empty($this->bundle_product_attributes[$product->product_id]) ) : ?>
                  <?php //$this->bundle_product_attributes[$product->product_id] ?>
                  <?php $makeFieldValueArray = Engine_Api::_()->sitestoreproduct()->makeFieldValueArray($this->bundle_product_attributes[$product->product_id]); ?>
                  <span class="p_conf f_small seaocore_txt_light">
                    <?php foreach($makeFieldValueArray as $key => $makeFieldValue) : ?>
                      <?php echo "<b>.".$this->translate($key)."</b>:". $makeFieldValue ."<br/>"; ?>
                    <?php endforeach; ?>
                  </span>
                <?php endif; ?>
              </td>
              <td>
                <?php echo $this->translate('1') ?>
              </td>         
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_productAvailability.tpl'; ?> 
  </div>
<?php endif; ?>

<!-- FOR DOWNLOADABLE PRODUCT -->
<?php if($this->sitestoreproduct->product_type == 'downloadable'): ?>
  <div class="sitestoreproduct_pcbox_w">
    <div class="sitestoreproduct_pcbox_t mbot5">
      <?php if( @count($this->downloadableProducts) > 0 ) : ?>
        <?php if( @count($this->downloadableProducts) == 1 ) : ?>
          <b><i><?php echo $this->translate("A Short Sample"); ?></i></b>
        <?php else: ?>
          <b><i><?php echo $this->translate("Some Short Samples"); ?></i></b>
        <?php endif; ?>
        <div class="clr p5 sitestore_sample_files"> 
          <?php foreach ($this->downloadableProducts as $item): ?>
          &raquo; <a href="<?php echo $this->url(array('action' => 'download-sample', 'product_id'=> $this->sitestoreproduct->product_id, 'downloadablefile_id' => $item->downloadablefile_id), 'sitestoreproduct_product_general', true) ?>" target="downloadframe"><?php echo $item->title ?></a><br />
          <?php endforeach; ?> 
        </div>
      <?php  //else : ?>
        <?php //echo $this->translate('Currently, there are no sample files available for download.') ?>
      <?php endif; ?>
    </div>
    <?php if(empty($this->isAnyFileExist)) : ?>
      <div class="seaocore_txt_red"><?php echo $this->translate('There are no files available for download yet.') ?></div>
    <?php else: ?>
      <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_productAvailability.tpl'; ?> 
    <?php endif; ?>
  </div>
<?php endif; ?>

<!-- FOR SIMPLE PRODUCT -->
<?php if($this->sitestoreproduct->product_type == 'simple'): ?>
  <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_productAvailability.tpl'; ?> 
<?php endif; ?>