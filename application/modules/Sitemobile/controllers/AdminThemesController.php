<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminThemesController.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_AdminThemesController extends Core_Controller_Action_Admin {

  public function init() {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitemobile_admin_main', array(), 'sitemobile_admin_main_themes');
  }

  public function indexAction() {
    // Get themes
    $themes = $this->view->themes = Engine_Api::_()->getDbtable('themes', 'sitemobile')->fetchAll();
    $activeTheme = $this->view->activeTheme = $themes->getRowMatching('active', 1);

    // Install any themes that are missing from the database table
    $mobileFlag = $reload_themes = false;
    include_once APPLICATION_PATH . "/application/modules/Sitemobile/controllers/license/license2.php";
    foreach (glob(APPLICATION_PATH . '/application/themes/sitemobile_tablet/*', GLOB_ONLYDIR) as $dir) {

      if (file_exists("$dir/manifest.php") && is_readable("$dir/manifest.php") && file_exists("$dir/theme.css") && is_readable("$dir/theme.css")) {
        $name = basename($dir);
        if (!$themes->getRowMatching('name', $name)) {
          $meta = include("$dir/manifest.php");
          $row = $themes->createRow();
          // @todo meta key is deprecated and pending removal in 4.1.0; b/c removal in 4.2.0
          if (isset($meta['package']['meta'])) {
            $meta['package'] = array_merge($meta['package']['meta'], $meta['package']);
            unset($meta['package']['meta']);
          }

          $row->title = $meta['package']['title'];
          $row->name = $name;
          $row->description = isset($meta['package']['description']) ? $meta['package']['description'] : '';
          $row->active = 0;
          $row->save();
          $reload_themes = true;
        }
      }
    }
        
    if( empty($mobileFlag) ) 
      return;
    
    foreach ($themes as $theme) {
      if (!is_dir(APPLICATION_PATH . '/application/themes/sitemobile_tablet/' . $theme->name)) {
        $theme->delete();
        $reload_themes = true;
      }
    }
    if ($reload_themes) {
      $themes = $this->view->themes = Engine_Api::_()->getDbtable('themes', 'sitemobile')->fetchAll();
      $activeTheme = $this->view->activeTheme = $themes->getRowMatching('active', 1);
      if (empty($activeTheme)) {
        $themes->getRow(0)->active = 1;
        $themes->getRow(0)->save();
        $activeTheme = $this->view->activeTheme = $themes->getRowMatching('active', 1);
      }
    }

    // Process each theme
    $manifests = array();
    $writeable = array();
    $modified = array();
    foreach ($themes as $theme) {
      // Get theme manifest
      $themePath = "application/themes/sitemobile_tablet/{$theme->name}";
      $manifest = @include APPLICATION_PATH . "/$themePath/manifest.php";
      if (!is_array($manifest))
        $manifest = array(
            'package' => array(),
            'files' => array()
        );
      sort($manifest['files']);

      // Pre-check manifest thumb
      // @todo meta key is deprecated and pending removal in 4.1.0; b/c removal in 4.2.0
      if (isset($manifest['package']['meta'])) {
        $manifest['package'] = array_merge($manifest['package']['meta'], $manifest['package']);
        unset($manifest['package']['meta']);
      }

      if (!isset($manifest['package']['thumb'])) {
        $manifest['package']['thumb'] = 'thumb.jpg';
      }
      $thumb = preg_replace('/[^A-Z_a-z-0-9\/\.]/', '', $manifest['package']['thumb']);
      if (file_exists(APPLICATION_PATH . "/$themePath/$thumb")) {
        $manifest['package']['thumb'] = "$themePath/{$thumb}";
      } else {
        $manifest['package']['thumb'] = null;
      }

      // Check if theme files are writeable
      $writeable[$theme->name] = false;
      try {
        foreach (array_merge(array(''), $manifest['files']) as $file) {
          if (!file_exists(APPLICATION_PATH . "/$themePath/$file")) {
            throw new Core_Model_Exception('Missing file in theme ' . $manifest['package']['title']);
          } else {
            $this->checkWriteable(APPLICATION_PATH . "/$themePath/$file");
          }
        }
        $writeable[$theme->name] = true;
      } catch (Exception $e) {
        if ($activeTheme->name == $theme->name) {
          $this->view->errorMessage = $e->getMessage();
        }
      }

      // Check if theme files have been modified
      $modified[$theme->name] = array();
      foreach ($manifest['files'] as $path) {
        $originalName = 'original.' . $path;
        if (file_exists(APPLICATION_PATH . "/$themePath/$originalName")) {
          if (file_get_contents(APPLICATION_PATH . "/$themePath/$originalName") != file_get_contents(APPLICATION_PATH . "/$themePath/$path")) {
            $modified[$theme->name][] = $path;
          }
        }
      }
      $manifests[$theme->name] = $manifest;
    }

    $this->view->manifest = $manifests;
    $this->view->writeable = $writeable;
    $this->view->modified = $modified;

    // Get the first active file
    $this->view->activeFileName = $activeFileName = $manifests[$activeTheme->name]['files'][0];
    if (null !== ($rFile = $this->_getParam('file'))) {
      if (in_array($rFile, $manifests[$activeTheme->name]['files'])) {
        $this->view->activeFileName = $activeFileName = $rFile;
      }
    }
    $this->view->activeFileOptions = array_combine($manifests[$activeTheme->name]['files'], $manifests[$activeTheme->name]['files']);
    $this->view->activeFileContents = file_get_contents(APPLICATION_PATH . '/application/themes/sitemobile_tablet/' . $activeTheme->name . '/' . $activeFileName);
  }

  public function changeAction() {
    $themeName = $this->_getParam('theme');
    $themeTable = Engine_Api::_()->getDbtable('themes', 'sitemobile');
    $themeSelect = $themeTable->select()
            ->orWhere('theme_id = ?', $themeName)
            ->orWhere('name = ?', $themeName)
            ->limit(1)
    ;
    $theme = $themeTable->fetchRow($themeSelect);

    if ($theme && $this->getRequest()->isPost()) {
      $db = $themeTable->getAdapter();
      $db->beginTransaction();

      try {
        $themeTable->update(array(
            'active' => 0,
                ), array(
            '1 = ?' => 1,
        ));
        $theme->active = true;
        $theme->save();

        // clear scaffold cache
        Core_Model_DbTable_Themes::clearScaffoldCache();

        // Increment site counter
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $settings->core_site_counter = $settings->core_site_counter + 1;

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }

    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

  public function saveAction() {
    $theme_id = $this->_getParam('theme_id');
    $file = $this->_getParam('file');
    $body = $this->_getParam('body');

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Bad method");
      return;
    }

    if (!$theme_id || !$file || !$body) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Bad params");
      return;
    }

    // Get theme
    $themeName = $this->_getParam('theme');
    $themeTable = Engine_Api::_()->getDbtable('themes', 'sitemobile');
    $themeSelect = $themeTable->select()
            ->orWhere('theme_id = ?', $theme_id)
            ->orWhere('name = ?', $theme_id)
            ->limit(1)
    ;
    $theme = $themeTable->fetchRow($themeSelect);

    if (!$theme) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Missing theme");
      return;
    }

    // Check file
    $basePath = APPLICATION_PATH . '/application/themes/sitemobile_tablet/' . $theme->name;
    $manifestData = include $basePath . '/manifest.php';
    if (empty($manifestData['files']) || !in_array($file, $manifestData['files'])) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Not in theme files");
      return;
    }
    $fullFilePath = $basePath . '/' . $file;
    try {
      $this->checkWriteable($fullFilePath);
    } catch (Exception $e) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Not writeable");
      return;
    }

    // Check for original file (try to create if not exists)
    if (!file_exists($basePath . '/original.' . $file)) {
      if (!copy($fullFilePath, $basePath . '/original.' . $file)) {
        $this->view->status = false;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_("Could not create backup");
        return;
      }
      chmod("$basePath/original.$file", 0777);
    }

    // Now lets write the custom file
    if (!file_put_contents($fullFilePath, $body)) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Could not save contents');
      return;
    }

    // clear scaffold cache
    Core_Model_DbTable_Themes::clearScaffoldCache();

    // Increment site counter
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $settings->core_site_counter = $settings->core_site_counter + 1;

    $this->view->status = true;
  }

  public function revertAction() {
    $theme_id = $this->_getParam('theme_id');

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Bad method");
      return;
    }

    if (!$theme_id) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Bad params");
      return;
    }

    // Get theme
    $themeName = $this->_getParam('theme');
    $themeTable = Engine_Api::_()->getDbtable('themes', 'sitemobile');
    $themeSelect = $themeTable->select()
            ->orWhere('theme_id = ?', $theme_id)
            ->orWhere('name = ?', $theme_id)
            ->limit(1)
    ;
    $theme = $themeTable->fetchRow($themeSelect);

    // Check file
    $basePath = APPLICATION_PATH . '/application/themes/sitemobile_tablet/' . $theme->name;
    $manifestData = include $basePath . '/manifest.php';
    $files = $manifestData['files'];
    $originalFiles = array();
    foreach ($files as $file) {
      if (file_exists("$basePath/original.$file")) {
        $originalFiles[] = $file;
      }
    }

    // Check each file if writeable
    $this->checkWriteable($basePath . '/');
    foreach ($originalFiles as $file) {
      //try {
      $this->checkWriteable($basePath . '/' . $file);
      $this->checkWriteable($basePath . '/original.' . $file);
      //} catch( Exception $e ) {
      //  $this->view->status = false;
      //  $this->view->message = 'Not writeable';
      //  return;
      //}
    }

    // Now undo all of the changes
    foreach ($originalFiles as $file) {
      unlink("$basePath/$file");
      rename("$basePath/original.$file", "$basePath/$file");
    }

    // clear scaffold cache
    Core_Model_DbTable_Themes::clearScaffoldCache();

    // Increment site counter
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $settings->core_site_counter = $settings->core_site_counter + 1;

    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

  public function exportAction() {
    $themes = Engine_Api::_()->getDbtable('themes', 'sitemobile')->fetchAll();
    if (!($row = $themes->getRowMatching('name', $this->_getParam('name')))) {
      throw new Engine_Exception("Theme not found: " . $this->_getParam('name'));
    }
    //$targetFilename = APPLICATION_PATH . '/temporary/theme_export.tar';
    $target_filename = tempnam(APPLICATION_PATH . '/temporary/', 'theme_');
    $template_dir = APPLICATION_PATH . '/application/themes/sitemobile_tablet/' . $row->name;

    $tar = new Archive_Tar($target_filename);
    $tar->setIgnoreRegexp("#CVS|\.svn#");
    $tar->createModify($template_dir, null, dirname($template_dir));
    chmod($target_filename, 0777);
    header('Content-Type: application/x-tar');
    header("Content-disposition: filename={$row->name}.tar");
    readfile($target_filename);
    @unlink($target_filename);
    exit;
  }

  public function cloneAction() {
    $themes = Engine_Api::_()->getDbtable('themes', 'sitemobile')->fetchAll();
    $form = $this->view->form = new Sitemobile_Form_Admin_Themes_Clone();
    $theme_array = array();
    foreach ($themes as $theme) {
      $theme_array[$theme->name] = $theme->title;
    }
    $form->getElement('name')->setMultiOptions($theme_array)->setValue($this->_getParam('name'));

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $orig_theme = $this->_getParam('name');
      if (!($row = $themes->getRowMatching('name', $orig_theme))) {
        throw new Engine_Exception("Theme not found: " . $this->_getParam('name'));
      }
      $new_theme = array(
          'name' => preg_replace('/[^a-z-0-9_]/', '', strtolower($this->_getParam('title'))),
          'title' => $this->_getParam('title'),
          'description' => $this->_getParam('description'),
      );
      $orig_dir = APPLICATION_PATH . '/application/themes/sitemobile_tablet/' . $orig_theme;
      $new_dir = dirname($orig_dir) . '/' . $new_theme['name'];

      Engine_Package_Utilities::fsCopyRecursive($orig_dir, $new_dir);
      chmod($new_dir, 0777);
      foreach (self::rscandir($new_dir) as $file)
        chmod($file, 0777);

      $meta = include("$new_dir/manifest.php");
      // @todo meta key is deprecated and pending removal in 4.1.0; b/c removal in 4.2.0
      if (isset($meta['package']['meta'])) {
        $meta['package'] = array_merge($meta['package']['meta'], $meta['package']);
        unset($meta['package']['meta']);
      }
      $meta['package']['name'] = $new_theme['name'];
      $meta['package']['version'] = null;
      $meta['package']['path'] = substr($new_dir, 1 + strlen(APPLICATION_PATH));
      $meta['package']['title'] = $new_theme['title'];
      $meta['package']['description'] = $new_theme['description'];
      $meta['package']['author'] = $this->_getParam('author', '');
      file_put_contents("$new_dir/manifest.php", '<?php return ' . var_export($meta, true) . '; ?>');

      try {
        $row = Engine_Api::_()->getDbtable('themes', 'sitemobile')->createRow();
        $row->setFromArray(array(
            'name' => $new_theme['name'],
            'title' => $new_theme['title'],
            'description' => $new_theme['description'],
        ));
        $row->save();
      } catch (Exception $e) { /* do nothing */
      }

      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }

  public function uploadAction() {
    $form = $this->view->form = new Core_Form_Admin_Themes_Upload();
    if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {

      if (!isset($_FILES['theme_file']))
        throw new Engine_Exception("Theme file was too large, or was not uploaded.");

      if (!preg_match('/\.tar$/', $_FILES['theme_file']['name']))
        throw new Engine_Exception("Invalid theme file format; must be a TAR file.");

      // extract tar file to temporary directory
      $tmp_dir = tempnam(APPLICATION_PATH . '/temporary/', 'theme_import');
      unlink($tmp_dir);
      mkdir($tmp_dir, 0777, true);
      $tar = new Archive_Tar($_FILES['theme_file']['tmp_name']);
      $tar->extract($tmp_dir);

      // find theme.css
      $dir = $tmp_dir;
      while (!file_exists("$dir/theme.css")) {
        $subdirs = glob("$dir/*", GLOB_ONLYDIR);
        $dir = $subdirs[0];
      }

      // pull manifest.php data
      $meta = array('package' => array(), 'files' => array());
      if (file_exists("$dir/manifest.php")) {
        $meta = include "$dir/manifest.php";
        $post = $this->_getAllParams();
        // @todo meta key is deprecated and pending removal in 4.1.0; b/c removal in 4.2.0
        if (isset($meta['package']['meta'])) {
          $meta['package'] = array_merge($meta['package']['meta'], $meta['package']);
          unset($meta['package']['meta']);
        }
        if (isset($post['title'])) {
          $meta['package']['title'] = $post['title'];
          $meta['package']['name'] = preg_replace('/[^a-z-0-9_]/', '', strtolower($post['title']));
        }
        if (empty($meta['package']['name'])) {
          $meta['package']['name'] = basename($dir);
        }
        if (empty($meta['package']['title'])) {
          $meta['package']['title'] = ucwords(preg_replace('/_\-/', ' ', basename($dir)));
        }
        if (isset($post['description'])) {
          $meta['package']['description'] = $post['description'];
        }
      }

      // move files over recursively
      $destination_dir = APPLICATION_PATH . '/application/themes/sitemobile_tablet/' . $meta['package']['name'];
      rename($dir, $destination_dir);
      chmod($destination_dir, 0777);
      foreach (self::rscandir($destination_dir) as $file) {
        chmod($file, 0777);
      }

      // re-write manifest according to POST paramters
      file_put_contents("$destination_dir/manifest.php", '<?php return ' . var_export($meta, true) . '; ?>');

      // add to database table
      $table = Engine_Api::_()->getDbtable('themes', 'sitemobile');
      $row = $table->createRow();
      // @todo meta key is deprecated and pending removal in 4.1.0; b/c removal in 4.2.0
      if (isset($meta['package']['meta'])) {
        $meta['package'] = array_merge($meta['package']['meta'], $meta['package']);
        unset($meta['package']['meta']);
      }
      $row->name = $meta['package']['name'];
      $row->title = $meta['package']['title'];
      $row->description = $meta['package']['description'];
      $row->active = $this->_getParam('enable', false);
      $row->save();

      // delete temporary directory
      Engine_Package_Utilities::fsRmdirRecursive($tmp_dir, true);

      // clear scaffold cache
      Core_Model_DbTable_Themes::clearScaffoldCache();

      // Increment site counter
      $settings = Engine_Api::_()->getApi('settings', 'core');
      $settings->core_site_counter = $settings->core_site_counter + 1;

      // forward back to index
      $this->_forward('success', 'utility', 'core', array(
          'redirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index')),
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Theme file has been uploaded.')),
          'parentRefresh' => 2000,
      ));
    }
  }

  public function rollerAction() {
    // Get themes
    $themes = $this->view->themes = Engine_Api::_()->getDbtable('themes', 'sitemobile')->fetchAll();
    $activeTheme = $this->view->activeTheme = $themes->getRowMatching('active', 1);

    // Install any themes that are missing from the database table
    $reload_themes = false;



    // Process each theme
    $manifests = array();
    $writeable = array();
    $modified = array();

    $theme = $activeTheme;
    // Get theme manifest
    $themePath = "application/themes/sitemobile_tablet/{$theme->name}";
    $manifest = @include APPLICATION_PATH . "/$themePath/manifest.php";
    if (!is_array($manifest))
      $manifest = array(
          'package' => array(),
          'files' => array()
      );
    sort($manifest['files']);
    // Pre-check manifest thumb
    // @todo meta key is deprecated and pending removal in 4.1.0; b/c removal in 4.2.0
    if (isset($manifest['package']['meta'])) {
      $manifest['package'] = array_merge($manifest['package']['meta'], $manifest['package']);
      unset($manifest['package']['meta']);
    }

    if (!isset($manifest['package']['thumb'])) {
      $manifest['package']['thumb'] = 'thumb.jpg';
    }
    $thumb = preg_replace('/[^A-Z_a-z-0-9\/\.]/', '', $manifest['package']['thumb']);
    if (file_exists(APPLICATION_PATH . "/$themePath/$thumb")) {
      $manifest['package']['thumb'] = "$themePath/{$thumb}";
    } else {
      $manifest['package']['thumb'] = null;
    }


    // Check if theme files are writeable
    $writeable[$theme->name] = false;
    try {
      foreach (array_merge(array(''), $manifest['files']) as $file) {
        if (!file_exists(APPLICATION_PATH . "/$themePath/$file")) {
          throw new Core_Model_Exception('Missing file in theme ' . $manifest['package']['title']);
        } else {
          $this->checkWriteable(APPLICATION_PATH . "/$themePath/$file");
        }
      }
      $writeable[$theme->name] = true;
    } catch (Exception $e) {
      if ($activeTheme->name == $theme->name) {
        $this->view->errorMessage = $e->getMessage();
      }
    }

    // Check if theme files have been modified
    $modified[$theme->name] = array();
    foreach ($manifest['files'] as $path) {
      $originalName = 'original.' . $path;
      if (file_exists(APPLICATION_PATH . "/$themePath/$originalName")) {
        if (file_get_contents(APPLICATION_PATH . "/$themePath/$originalName") != file_get_contents(APPLICATION_PATH . "/$themePath/$path")) {
          $modified[$theme->name][] = $path;
        }
      }
    }
    $manifests[$theme->name] = $manifest;


    $this->view->manifest = $manifests;
    $this->view->writeable = $writeable;
    $this->view->modified = $modified;

    // Get the first active file
    $this->view->activeFileName = $activeFileName = $manifests[$activeTheme->name]['files'][0];
    if (null !== ($rFile = $this->_getParam('file'))) {
      if (in_array($rFile, $manifests[$activeTheme->name]['files'])) {
        $this->view->activeFileName = $activeFileName = $rFile;
      }
    }
    $this->view->activeFileOptions = array_combine($manifests[$activeTheme->name]['files'], $manifests[$activeTheme->name]['files']);
    $this->view->activeFileContents = file_get_contents(APPLICATION_PATH . '/application/themes/sitemobile_tablet/' . $activeTheme->name . '/' . $activeFileName);
  }

  public function previewRollerAction() {
    // Get themes
    $themes = $this->view->themes = Engine_Api::_()->getDbtable('themes', 'sitemobile')->fetchAll();
    $activeTheme = $this->view->activeTheme = $themes->getRowMatching('active', 1);
  }

  public function rollerSaveAction() {
    $body = $this->_getParam('body');

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Bad method");
      return;
    }

    if (!$body) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Bad params");
      return;
    }


    $themes = $this->view->themes = Engine_Api::_()->getDbtable('themes', 'sitemobile')->fetchAll();
    $activeTheme = $this->view->activeTheme = $themes->getRowMatching('active', 1);

    $file = 'theme.css';
    // Check file
    $basePath = APPLICATION_PATH . '/application/themes/sitemobile_tablet/' . $activeTheme->name;
    // Check for original file (try to create if not exists)
    if (!file_exists($basePath . '/original.' . $file)) {
      if (!copy($basePath . "/" . $file, $basePath . '/original.' . $file)) {
        $this->view->status = false;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_("Could not create backup");
        return;
      }
      chmod("$basePath/original.$file", 0777);
    }




    if (file_exists($basePath . '/' . $file)) {
      unlink("$basePath/$file");
    }
    $new_file_name = $basePath . "/" . $file;
    $new_file = fopen($new_file_name, 'w');
    // Now lets write the custom file
    if (!fwrite($new_file, $body)) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Could not save contents');
      return;
    }

    fclose($new_file);
    chmod($new_file_name, 0777);

    // Increment site counter
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $settings->core_site_counter = $settings->core_site_counter + 1;

    $this->view->status = true;
    exit(0);
  }

  /*
    public function deleteAction()
    {
    if ( !$this->getRequest()->isPost() || $this->_getParam('name', false) ) {
    $this->_redirect( Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'index')) );
    }
    $dir = APPLICATION_PATH . '/application/themes/sitemobile_tablet/' . $this->_getParam('name');

    if ($dir == realpath($dir) && is_dir($dir)) {
    try {
    Engine_Package_Utilities::fsRmdirRecursive($dir);
    $row = Engine_Api::_()->getDbtable('themes', 'sitemobile')->getMatching('name', basename($dir));
    $row->delete();

    $active = Engine_Api::_()->getDbtable('themes', 'sitemobile')->getMatching('active', 1);
    if (empty($active)) {
    Engine_Api::_()->getDbtable('themes', 'sitemobile')->getRow(0)->active = true;
    Engine_Api::_()->getDbtable('themes', 'sitemobile')->getRow(0)->save();
    }

    // forward back to index
    $this->_forward('success', 'utility', 'core', array(
    'redirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'index','name'=>null)),
    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Theme file has been deleted.')),
    'parentRefresh'  => 2000,
    ));
    } catch (Exception $e) {
    $this->_forward('success', 'utility', 'core', array(
    'redirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'index','name'=>null)),
    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Theme was not deleted.')),
    'parentRefresh'  => 4000,
    ));
    }
    } else {
    $this->_forward('success', 'utility', 'core', array(
    'redirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'index','name'=>null)),
    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Theme was not found.')),
    'parentRefresh'  => 4000,
    ));
    }
    }
   *
   */

  public function checkWriteable($path) {
    if (!file_exists($path)) {
      throw new Core_Model_Exception('Path doesn\'t exist');
    }
    if (!is_writeable($path)) {
      throw new Core_Model_Exception('Path is not writeable');
    }
    if (!is_dir($path)) {
      if (!($fh = fopen($path, 'ab'))) {
        throw new Core_Model_Exception('File could not be opened');
      }
      fclose($fh);
    }
  }

  /**
   * outputs all files and directories
   * recursively starting with the given
   * $base path. This function is a combination
   * of some of the other snips on the php.net site.
   *
   * @example rscandir(dirname(__FILE__).'/'));
   * @param string $base
   * @param array $data
   * @return array
   */
  public static function rscandir($base='', &$data=array()) {
    $array = array_diff(scandir($base), array('.', '..')); // remove ' and .. from the array */
    foreach ($array as $value) { /* loop through the array at the level of the supplied $base */
      if (is_dir("$base/$value")) { /* if this is a directory */
        $data[] = "$base/$value/"; /* add it to the $data array */
        $data = self::rscandir("$base/$value", $data); /* then make a recursive call with the
          current $value as the $base supplying the $data array to carry into the recursion */
      } elseif (is_file("$base/$value")) { /* else if the current $value is a file */
        $data[] = "$base/$value"; /* just add the current $value to the $data array */
      }
    }
    return $data; // return the $data array
  }

  public Function guidelinesAction() {
    
  }

    private function _getCustomCSS($values) {
    $returnTheme = null;
    if(!empty($values) && isset($values['theme_customization'])) {
      switch($values['theme_customization']) {
        case 0: // DEFAULT THEME
          $returnTheme .= 'theme_color: #3FC8F4; button_border_color:#3da7ca; landingpage_signinbtn:#ff5f3f; landingpage_signupbtn: rgba(255,95,63,.5);';
          break;
        
        case 1: // LIGHTORANGE COLOR BASED THEME
          $returnTheme .= 'theme_color: #E89476; button_border_color:#D9704A; landingpage_signinbtn:#3FC8F4; landingpage_signupbtn: rgba(63, 200, 244, .5);';
          break;
        
        case 2: // LIGHTPINK COLOR BASED THEME
          $returnTheme .= 'theme_color: #BF6F94; button_border_color:#A6567B; landingpage_signinbtn:#3FC8F4; landingpage_signupbtn: rgba(63, 200, 244, .5);';
          break;
          
        case 4: // LIGHTPURPLE COLOR BASED THEME
        $returnTheme .= 'theme_color: #9397E2; button_border_color:#7A7EC9; landingpage_signinbtn:#BF548F; landingpage_signupbtn: rgba(191, 84, 143, .5);';
        break;
        
        case 5: // LIGHTYELLO COLOR BASED THEME
        $returnTheme .= 'theme_color: #E1D998; button_border_color:#CCC483; landingpage_signinbtn:#3FC8F4; landingpage_signupbtn: rgba(63, 200, 244, .5);';
        break;
        
        case 6: // LEAFGREEN COLOR BASED THEME
        $returnTheme .= 'theme_color: #5EC797; button_border_color:#4BB484; landingpage_signinbtn:#FF5F3F; landingpage_signupbtn: rgba(255, 95, 63, .5);';
        break;
        
        case 7: // FADEBLUE  COLOR BASED THEME
        $returnTheme .= 'theme_color: #446EA6; button_border_color:#335D95; landingpage_signinbtn:#FF5F3F; landingpage_signupbtn: rgba(255, 95, 63, .5);';
        break;
        
        case 8: // SEAGREEN COLOR BASED THEME
        $returnTheme .= 'theme_color: #54BFBF; button_border_color:#3EA9A9; landingpage_signinbtn:#3FC8F4; landingpage_signupbtn: rgba(63, 200, 244, .5);';
        break;
        
        case 9: // PURPLE COLOR BASED THEME
        $returnTheme .= 'theme_color: #67568C; button_border_color:#58477D; landingpage_signinbtn:#FF5F3F; landingpage_signupbtn: rgba(255, 95, 63, .5);';
        break;
        
        case 10: // GREEN COLOR BASED THEME
        $returnTheme .= 'theme_color: #038C7E; button_border_color:#067668; landingpage_signinbtn:#FF5F3F; landingpage_signupbtn: rgba(255, 95, 63, .5);';
        break;
        
        case 11: // RED COLOR BASED THEME
        $returnTheme .= 'theme_color: #BD4D4A; button_border_color:#A83835; landingpage_signinbtn:#005DA4; landingpage_signupbtn: rgba(0, 93, 164, .5);';
        break;
        
        case 12: // DARKBLUE COLOR BASED THEME
        $returnTheme .= 'theme_color: #005DA4; button_border_color:#02478E; landingpage_signinbtn:#BF548F; landingpage_signupbtn: rgba(191, 84, 143, .5);';
        break;
        
        case 13: // MAGENTA COLOR BASED THEME
        $returnTheme .= 'theme_color: #A64E5E; button_border_color:#822A3A; landingpage_signinbtn:#038C7E; landingpage_signupbtn: rgba(3, 140, 126, .5);';
        break;
        
        case 14: // YELLOW COLOR BASED THEME
        $returnTheme .= 'theme_color: #E8BA52; button_border_color:#D0A23A; landingpage_signinbtn:#5EC797; landingpage_signupbtn: rgba(94, 199, 151, .5);';
        break;
        
        case 15: // DARKPINK COLOR BASED THEME
        $returnTheme .= 'theme_color: #DC2850; button_border_color:#B8183B; landingpage_signinbtn:#3FC8F4; landingpage_signupbtn: rgba(63, 200, 244, .5);';
        break;
        
        case 3: // CUSTOM COLOR BASED THEME
          $returnTheme .= 'theme_color: ' . $values['siteluminous_theme_color'] . '; button_border_color:' . $values['siteluminous_theme_button_border_color'] . '; landingpage_signinbtn: ' . $values['siteluminous_landingpage_signinbtn'] . '; landingpage_signupbtn: ' . $values['siteluminous_landingpage_signupbtn'] . ';';
          break;
      }
    }
    
    return $returnTheme;
  }
  
  
    public function customizationAction() {
//      $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
//              ->getNavigation('siteluminous_admin_main', array(), 'siteluminous_admin_theme_customization');
        
    $themes = $this->view->themes = Engine_Api::_()->getDbtable('themes', 'sitemobile')->fetchAll();
    $activeTheme = $this->view->activeTheme = $themes->getRowMatching('active', 1);
    $this->view->activeThemeId = $activeThemeId = $activeTheme->theme_id;
    
    $this->view->form = $form = new Sitemobile_Form_Admin_Themes_Customization(array(
            'activeTheme' => $activeTheme
         ));

      if (!$this->getRequest()->isPost())
        return;        

      if (!$form->isValid($this->getRequest()->getPost()))
        return;

      $values = $form->getValues();
      $getCustomCSS = $this->_getCustomCSS($values);
      
      if($values['theme_customization']!= 4)
      $getCustomCSS = $this->_getCustomCSS($values);else{
      $getCustomCSS =  "theme_color:".$values['sitemobile_theme_color']."; button_border_color:".$values['sitemobile_theme_button_border_color']."; landingpage_signinbtn:".$values['sitemobile_landingpage_signinbtn']."; landingpage_signupbtn: ".$values['sitemobile_landingpage_signupbtn'].";";

      }
      $filename = "CustomVariable_".$activeTheme->name.".css";
      $global_directory_name = APPLICATION_PATH . '/public/seaocore_themes';
      $global_settings_file = $global_directory_name . '/'.$filename;
      $is_file_exist = @file_exists($global_settings_file);
      if (empty($is_file_exist)) {
        if (!is_dir($global_directory_name)) {
          @mkdir($global_directory_name, 0777);
        }
        @chmod($global_directory_name, 0777);

        $fh = @fopen($global_settings_file, 'w') or die('Unable to write Constant CSS file; please give the CHMOD 777 recursive permission to the directory /public/, then try again.');
        @fwrite($fh, $getCustomCSS);
        @fclose($fh);

        @chmod($global_settings_file, 0777);
        $successfullyAdded = true;
      } else {
        if (!is_writable($global_settings_file)) {
          @chmod($global_settings_file, 0777);
          if (!is_writable($global_settings_file)) {
            $form->addError('Unable to write Constant CSS file; please give the CHMOD 777 recursive permission to the directory /public/seaocore_themes/, then try again.');
            return;
          }
        }
        $successfullyAdded = @file_put_contents($global_settings_file, $getCustomCSS);
      }

      if (!empty($successfullyAdded)) {
        Core_Model_DbTable_Themes::clearScaffoldCache();
        $form->addNotice('Changes successfully saved. If changes not reflect to frontend then please change the mode to Development.');
      }

     
  }
  
}