<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: LayoutBlock.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_AdminLayout_LayoutBlock extends Engine_Form {

  public function init() {

    $this
            ->setTitle('Store Profile Layout Settings')
            ->setDescription('Below you can select / unselect the blocks / widgets of "Core" module to be shown on the User Store Profile Layout Editor in the "Available Blocks" section. In this way you can decide that whether or not you want Store Owners to place / remove these blocks from their Store Profile. If you do not select a block below, then that block will NOT be available to Store Owners for placing on the Store and in that case if that widget has been placed by you in the Store Profile Placement area, then Store Owners will NOT be able to remove it or drag-n-drop it and it will be displayed to them as "Locked". Please click on "Save Changes" to save the settings below.');

    $results = $this->getContentAreas();
    $layoutBlockTable = Engine_Api::_()->getDbtable('layoutblocks', 'sitestore');
    foreach ($results as $layoutblock) {
      $this->addElement('dummy', $layoutblock['category'], array(
          'label' => $layoutblock['category'],
      ));

      $replaceString = str_replace(array('-', '.'), "", $layoutblock['name']);
      
      $value = $layoutBlockTable->select()
                      ->from($layoutBlockTable->info('name'), array('value'))
                      ->where("layoutblock_name = ?", $replaceString)
                      ->query()
                      ->fetchColumn();
      $this->addElement('Checkbox', $replaceString, array(
          'label' => $layoutblock['title'],
          'value' => $value,
      ));
    }    
    
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

    public function getContentAreas() {

    $contentAreas = array();
    //FROM MODULES
    $modules = Zend_Controller_Front::getInstance()->getControllerDirectory();
    foreach ($modules as $module => $path) {
      if($module == 'core') {
        $contentManifestFile = dirname($path) . '/settings/content.php';
        if (!file_exists($contentManifestFile))
          continue;
        $ret = include $contentManifestFile;
        $contentAreas = array_merge($contentAreas, (array) $ret);
      }
    }

    //FROM WIDGETS
//    $it = new DirectoryIterator(APPLICATION_PATH . '/application/widgets');
//    foreach ($it as $dir) {
//      if (!$dir->isDir() || $dir->isDot())
//        continue;
//      $path = $dir->getPathname();
//      $contentManifestFile = $path . '/' . 'manifest.php';
//      if (!file_exists($contentManifestFile))
//        continue;
//      $ret = include $contentManifestFile;
//      if (!is_array($ret))
//        continue;
//      array_push($contentAreas, $ret);
//    }

    return $contentAreas;
  }
}
?>