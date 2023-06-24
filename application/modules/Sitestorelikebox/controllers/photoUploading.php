<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorelikebox
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: photoUploading.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
foreach ($values as $key => $value) {
  if ($key == 'tab_common') {
    $value = 0;
  }
  if ($key != 'logo_photo')
    Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
  elseif ($key == 'logo_photo' && !empty($value)) {
    $photo = $form->logo_photo;
    if ($photo instanceof Zend_Form_Element_File) {
      $file = $photo->getFileName();
    } else if (is_array($photo) && !empty($photo['tmp_name'])) {
      $file = $photo['tmp_name'];
    } else if (is_string($photo) && file_exists($photo)) {
      $file = $photo;
    }

    $name = basename($file);
    $maxlimit = (int) ini_get('upload_max_filesize') * 1024 * 1024;
    // ALLLOW IMAGE EXTENSION
    $allowed_ext = 'jpg,jpeg,gif,png';
    $match = 0;
    $errorList = array();

    $path = APPLICATION_PATH . '/public/sitestorelikebox/logo';
    if (!is_dir($path) && !mkdir($path, 0777, true)) {
      mkdir(dirname($path));
      chmod(dirname($path), 0777);
      touch($path);
      chmod($path, 0777);
    }
    $values = array();
    @chmod($path, 0777);
    //MINIMUM HIGHT AND WIDTH OF CREATE IMAGE
    $min = 30;
    $createWidth = 120;
    $createHight = 30;
    // SET WIDTH AND HIGHT OF IMAGE
    // Recreate image
    $image = Engine_Image::factory();
    $image->open($file);
    //IMAGE WIDTH
    $dstW = $image->width;
    // IMAGE HIGHT
    $dstH = $image->height;
    $maxH = $createHight;
    $maxW = $createWidth;
    // SET THE IMAGE AND WIDTH BASE ON IMAGE
    $multiplier = min($maxW / $dstW, $maxH / $dstH);
    if ($multiplier > 1) {
      $dstH *= $multiplier;
      $dstW *= $multiplier;
    }

    if (($delta = $maxW / $dstW) < 1) {
      $dstH = round($dstH * $delta);
      $dstW = round($dstW * $delta);
    }
    if (($delta = $maxH / $dstH) < 1) {
      $dstH = round($dstH * $delta);
      $dstW = round($dstW * $delta);
    }

    $createHight = $dstH;
    $createWidth = $dstW;

    if ($createWidth < $min)
      $createWidth = $min;

    if ($createHight < $min)
      $createHight = $min;

    // Resize image
    $image = Engine_Image::factory();
    $image->open($file);
    $image->resample(0, 0, $image->width, $image->height, $createWidth, $createHight)
            ->write($path . '/' . $name)
            ->destroy();
    Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
    $description = '<div class="tip"><span>You have not add logo , please upload logo</span></div>';
    $logo_photo = Engine_Api::_()->getApi('settings', 'core')->getSetting('logo.photo');
    if (!empty($logo_photo)) {
      $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
      $photoName = $view->baseUrl() . '/public/sitestorelikebox/logo/' . $logo_photo;
      $description = '<img src=\'' . $photoName . '\' />';
      $form->logo_photo_preview->setDescription($description);
    }
  }
}