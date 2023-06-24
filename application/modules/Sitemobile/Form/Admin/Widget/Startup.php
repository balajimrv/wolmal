<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Startup.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Form_Admin_Widget_Startup extends Engine_Form {
  protected $_mode;

  public function getMode()
  {
    return $this->_mode;
  }

  public function setMode($mode)
  {
    $this->_mode = $mode;
    return $this;
  }

  public function init() {

    $this->setTitle('Choose startup image')
            ->setDescription('Shows your startup image or title. Images are uploaded via the <a href="admin/files" target="_blank">File Media Manager</a>');

    // Get available files
    $logoOptions = array('' => 'Default Image');
    $imageExtensions = array('gif', 'jpg', 'jpeg', 'png');

    $it = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
    foreach ($it as $file) {
      if ($file->isDot() || !$file->isFile())
        continue;
      $basename = basename($file->getFilename());
      if (!($pos = strrpos($basename, '.')))
        continue;
      $ext = strtolower(ltrim(substr($basename, $pos), '.'));
      if (!in_array($ext, $imageExtensions))
        continue;
      $logoOptions['public/admin/' . $basename] = $basename;
    }


    $this->addElement('hidden', 'title', array(
    ));
    $this->addElement('Select', 'image_type', array(
        'label' => 'Select Startup image type',
        'multiOptions' => array('full'=>'Full Page Image','logo'=>'Logo Image'),
            //'onchange' => 'javascript:hide_fields(this.value)'
        'value'=>'logo'
    ));
    $this->addElement('Select', 'logo', array(
        'label' => 'Select Startup image',
        'multiOptions' => $logoOptions,
            //'onchange' => 'javascript:hide_fields(this.value)'
    ));

    if (false) {
      $this->addElement('Text', 'height', array(
          'label' => 'Height in percentage (%)',
          'required' => true,
          'allowEmpty' => false,
          'default' => 100,
      ));

      $this->addElement('Text', 'width', array(
          'label' => 'Width in percentage (%)',
          'required' => true,
          'allowEmpty' => false,
          'default' => 100,
      ));
    }
  }

}