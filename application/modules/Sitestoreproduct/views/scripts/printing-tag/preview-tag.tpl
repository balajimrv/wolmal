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
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/mooqrcode.min.js');
$return_url = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://";

$height = $this->printingTagItem->height;
$height_div = $height * 37.795; // Converting Height(cm) into Height(pixel)
$width = $this->printingTagItem->width;
$width_div = $width * 37.795;  // Converting Width(cm) into Width(pixel)
$qr_size = $height_div * 0.5;
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

<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/style_print.css');
$fontSettingsArray = Zend_Json::decode($this->printingTagItem->font_settings);
?>
<link href="<?php echo $baseUrl . 'application/modules/Seaocore/externals/styles/style_print.css' ?>" type="text/css" rel="stylesheet" media="print">

<div id="detail_container" style=" height: <?php echo $height_div; ?>px;
     width:<?php echo $width_div; ?>px; background: #DDD; position: relative; margin:10px auto 0;">
  <div  id="title_container" style=" display:<?php
  if (!empty($this->printingTagItem->title))
    echo"block";
  else
    echo"none";
  ?>;top:<?php echo $title_top ?>; left:<?php echo $title_left ?>; font-size: <?php echo $fontSettingsArray['title']['size']; ?>; font-weight: bold; color: <?php echo $fontSettingsArray['title']['color']; ?>; font-family: <?php echo $fontSettingsArray['title']['family']; ?>;">
    <?php echo $this->translate("Product Title"); ?>
  </div>
  <div id="category_container" style="font-size: <?php echo $fontSettingsArray['category']['size']; ?>; color: <?php echo $fontSettingsArray['category']['color']; ?>; font-family: <?php echo $fontSettingsArray['category']['family']; ?>; font-weight: bold; font-style: italic; display:<?php if (!empty($this->printingTagItem->category)) echo"block";
  else echo"none"; ?>;top:<?php echo $category_top ?>; left:<?php echo $category_left ?>; ">
    <?php echo $this->translate("Product Category"); ?>
  </div>
  <div id="price_container" style="font-size: <?php echo $fontSettingsArray['price']['size']; ?>; color: <?php echo $fontSettingsArray['price']['color']; ?>; font-family: <?php echo $fontSettingsArray['price']['family']; ?>; font-weight: bold; display:<?php if (!empty($this->printingTagItem->price)) echo"block";
  else echo"none"; ?>;top:<?php echo $price_top ?>; left:<?php echo $price_left ?>;">
    <?php echo $this->translate("Product Price"); ?>
  </div>
  <div id="qr_container" style="height: <?php echo $fontSettingsArray['qr']['size']; ?>; display:<?php if (!empty($this->printingTagItem->qr)) echo"block";
  else echo"none"; ?>;top:<?php echo $qr_top ?>; left:<?php echo $qr_left ?>;">
    <img style="height:100%" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreproduct/externals/images/qr.png"/> 
  </div>
</div>

<div class="seaocore_members_popup_bottom">
  <button type='button' onclick="parent.Smoothbox.close();"><?php echo $this->translate("Close"); ?></button>
</div>


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

