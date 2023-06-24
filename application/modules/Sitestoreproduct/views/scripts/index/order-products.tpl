<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: order-products.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<div class="seaocore_members_popup seaocore_members_popup_notbs">
  <div class="top">
    <div class="heading">
      <?php echo $this->translate("Products Purchased"); ?>
    </div>
  </div>	
  <div class="seaocore_members_popup_content">
    <?php foreach ($this->products as $data) {
        $product = $data['products'];
        $quantity = $data['qty'];
        $title = $this->htmlLink($product->getHref(), $product->getTitle(), array("target" => "_parent"));
        
        if( !empty($data['configuration']) ) :
          $configuration = Zend_Json::decode($data['configuration']);
          $title .= ' (';
          $tempConfigCount = 0;
          foreach($configuration as $config_name => $config_value):
            if( !empty($tempConfigCount) ) :
              $title .= ', ';
            endif;
            $title .= "<b>$config_name:</b> $config_value";
            $tempConfigCount++;
          endforeach;
          $title .= ')';
        endif;
        
        $price = $product->price;
        $image = $this->htmlLink($product->getHref(), $this->itemPhoto($product, 'thumb.profile'), array("target" => "_parent"));
      ?>

      <div class="item_member_list">
        <div class="item_member_thumb">
          <?php echo $image; ?>
        </div>
        <div class="item_member_details">
          <div class="item_member_name">
            <?php echo $title; ?>
            <?php echo "<br>" . $quantity . '</strong> x ' . $product->price; ?>
          </div>		
        </div>
      </div>
    <?php } ?>
  </div>
</div>
<div class="seaocore_members_popup_bottom">
  <button type='button' onclick="parent.Smoothbox.close();"><?php echo $this->translate("Close"); ?></button>
</div>
