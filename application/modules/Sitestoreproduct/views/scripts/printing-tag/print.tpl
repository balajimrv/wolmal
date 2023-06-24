<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: print.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<link href="<?php echo $baseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_print.css' ?>" type="text/css" rel="stylesheet" media="print">
<div class="sitestore_tags_print_wrapper">
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_print.css');
$fontSettingsArray = Zend_Json::decode($this->printingTagItem->font_settings);

$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/mooqrcode.min.js');
$return_url = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://";
$tempFlag = 1;
$isProductAvailable = false;
foreach ($this->paginator as $paginator) {
  if( $paginator->product_type == 'downloadable' || $paginator->product_type == 'virtual' )
    continue;
  
  
  if(empty($isProductAvailable)): ?>
    <div id='print_title_and_description'>
      <h3><?php echo $this->translate("Print the Products"); ?></h3>
      <p class='mbot10'><?php echo $this->translate("Click on 'Take Print' button for start the printing. Virtual Product and Downloadable Product will not allow to print."); ?></p>
    </div>
  <?php endif;
  
  $isProductAvailable = true;
  
  if ($tempFlag > 1)
    echo '';

  $height = $this->printingTagItem->height;
  $height_div = $height * 37.795; // Converting Height(cm) into Height(pixel)
  $width = $this->printingTagItem->width;
  $width_div = $width * 37.795;  // Converting Width(cm) into Width(pixel)
//$qr_size = $height_div * 0.5;
// WORK TO SHOW THE POSITION OF DETAILS IN CONFIGURATION PANEL USING COORDINATES
  $coordinateString = $this->printingTagItem->coordinates;
  $coordinates = @explode("|", $coordinateString);

  $titleCoodinatesArray = $coordinates[0];
  if ($titleCoodinatesArray != null) {
    $title_Array = @explode(",", $titleCoodinatesArray);
    $title_top = $title_Array[0];
    $title_left = $title_Array[1];
  }

  $categoryCoodinatesArray = $coordinates[1];
  if ($categoryCoodinatesArray != null) {
    $category_Array = @explode(",", $categoryCoodinatesArray);
    $category_top = $category_Array[0];
    $category_left = $category_Array[1];
  }

  $priceCoodinatesArray = $coordinates[2];
  if ($priceCoodinatesArray != null) {
    $price_Array = @explode(",", $priceCoodinatesArray);
    $price_top = $price_Array[0];
    $price_left = $price_Array[1];
  }

  $sizeCoodinatesArray = $coordinates[3];
  if ($sizeCoodinatesArray != null) {
    $size_Array = @explode(",", $sizeCoodinatesArray);
    $size_top = $size_Array[0];
    $size_left = $size_Array[1];
  }

  $qrCoodinatesArray = $coordinates[4];
  if ($qrCoodinatesArray != null) {
    $qr_Array = @explode(",", $qrCoodinatesArray);
    $qr_top = $qr_Array[0];
    $qr_left = $qr_Array[1];
  }
  ?>

 
        <div id="detail_container" class="tag_container_wrapper" style=" height: <?php echo $height_div; ?>px; width:<?php echo $width_div; ?>px;">
          <div  id="title_container_<?php echo $tempFlag ?>" style="font-size: <?php echo $fontSettingsArray['title']['size']; ?>; font-weight: bold; color: <?php echo $fontSettingsArray['title']['color']; ?>; font-family: <?php echo $fontSettingsArray['title']['family']; ?>; display:<?php
          if (!empty($this->printingTagItem->title))
            echo"block";
          else
            echo"none";
          ?>;top:<?php echo $title_top ?>; left:<?php echo $title_left ?>;">
                <?php echo $this->translate($paginator->getTitle()); ?>
          </div>
          <div id="category_container_<?php echo $tempFlag ?>" style="font-size: <?php echo $fontSettingsArray['category']['size']; ?>; color: <?php echo $fontSettingsArray['category']['color']; ?>; font-family: <?php echo $fontSettingsArray['category']['family']; ?>; font-weight: bold; font-style: italic; display:<?php
          if (!empty($this->printingTagItem->category))
            echo"block";
          else
            echo"none";
          ?>;top:<?php echo $category_top ?>; left:<?php echo $category_left ?>;">
  <?php echo $paginator->getCategory()->getTitle(true) ?>
          </div>
          <div id="price_container_<?php echo $tempFlag ?>" style="font-size: <?php echo $fontSettingsArray['price']['size']; ?>; color: <?php echo $fontSettingsArray['price']['color']; ?>; font-family: <?php echo $fontSettingsArray['price']['family']; ?>; font-weight: bold; display:<?php
          if (!empty($this->printingTagItem->price))
            echo"block";
          else
            echo"none";
          ?>;top:<?php echo $price_top ?>; left:<?php echo $price_left ?>;">
          <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($paginator->price); ?>
          </div>
          <div id="qr_container_<?php echo $tempFlag ?>" style="height: <?php echo $fontSettingsArray['qr']['size']; ?>; display:<?php
               if (!empty($this->printingTagItem->qr))
                 echo"block";
               else
                 echo"none";
               ?>;top:<?php echo $qr_top ?>; left:<?php echo $qr_left ?>;">

          </div>
        </div>

<script type="text/javascript">  
  

  
   window.addEvent('domready', function() {
   var tempFlag = <?php echo $tempFlag; ?>;
      if(document.id("qr_container_" + tempFlag)){
        var qrCodeSize = '<?php echo $fontSettingsArray['qr']['size']; ?>';
        qrCodeSize = qrCodeSize.replace('px', '');
        document.id("qr_container_" + tempFlag).qrCode({'width': qrCodeSize, 'height': qrCodeSize, 'value': '<?php echo $return_url . $_SERVER['HTTP_HOST'] . $paginator->getHref(); ?>'});
      }
  });
</script>
<?php
  $tempFlag++;

}

if( !empty($isProductAvailable) ):
?>

<div id="printdiv" class="print_button">
  <button class="buttonlink" onclick="printData()" align="right">
<?php echo $this->translate('Take Print') ?>
  </button> <?php echo $this->translate('or'); ?>
  <a href="javascript:void(0);" name="cancel" onclick="closeSmooth();"><?php echo $this->translate("cancel") ?></a>
</div>
<?php else: ?>
<div class="tip">
  <span>
    <?php echo $this->translate("There are no product's available for print. Please check the product type of the selected products because 'Virtual Products' and 'Downloadable Products' are not allow to print."); ?>
  </span>
</div>
<?php endif; ?>

</div>



<script type="text/javascript">
  function printData() {
    document.getElementById('printdiv').style.display = "none";
    document.getElementById('print_title_and_description').style.display = "none";
    window.print();
    setTimeout(function() {
      document.getElementById('printdiv').style.display = "block";
      document.getElementById('print_title_and_description').style.display = "block";
    }, 500);
  }

  function closeSmooth() {
    parent.Smoothbox.close();
  }
</script>

<style>
  #detail_container {
    border:1px solid #000;
    background: #DDD;
    position: relative;
  }
  #detail_container > div{
    position: absolute;
  }

</style>