<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Logo.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  window.addEvent('domready', function() {
    hide_fields();
  });
  //  FUNCTION TO HIDE & SHOW TEXT FIELDS HEIGHT & WIDTH
  function hide_fields()
  {
    if($('logo').value == ''){
      document.getElementById('height-wrapper').style.display = 'none';
      document.getElementById('width-wrapper').style.display = 'none';
    }else{
      document.getElementById('height-wrapper').style.display = 'block';
      document.getElementById('width-wrapper').style.display = 'block';
    }
  }
</script>

<?php

class Sitemobile_Form_Admin_Widget_Logo extends Engine_Form {

  public function init() {

    // Set form attributes
    $this
            ->setTitle('Site Logo')
            ->setDescription('Shows your site-wide main logo or title. Images are uploaded via the <a href="admin/files" target="_blank">File Media Manager</a>.')
    ;

    // Get available files
    $logoOptions = array('' => 'Text-only (No logo)');
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
    $this->addElement('Select', 'logo', array(
        'label' => 'Site Logo',
        'multiOptions' => $logoOptions,
        'onchange' => 'javascript:hide_fields(this.value)'
    ));

    $this->addElement('Select', 'alignment', array(
        'label' => 'Logo Alignment',
        'multiOptions' => array(
            "left" => "Left",
            "right" => "Right",
            "center" => "Center",
        ),
    ));
    $this->addElement('Text', 'height', array(
        'label' => 'Height',
        'required' => true,
        'allowEmpty' => false,
    ));

    $this->addElement('Text', 'width', array(
        'label' => 'Width',
        'required' => true,
        'allowEmpty' => false,
    ));
  }

}