<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreurl
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreurl_AdminSettingsController extends Core_Controller_Action_Admin {

  //ACTION FOR SENDING THE EMAIL
  public function indexAction() {
    
  //GET NAVIGATION
  $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
          ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_global_url');
  
  //GET NAVIGATION
  $this->view->navigationStoreGlobal = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('sitestoreurl_admin_main', array(), 'sitestoreurl_admin_global_url');   

   $db = Engine_Db_Table::getDefaultAdapter();
	//START LANGUAGE WORK
	Engine_Api::_()->getApi('language', 'sitestore')->languageChanges();
	//END LANGUAGE WORK
    //GET FORM
    $this->view->form = $form = new Sitestoreurl_Form_Admin_Global();

    if( !$this->getRequest()->isPost() ) {
        Engine_Api::_()->sitestoreurl()->setBandURL();
    }
     // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    $values = $form->getValues();
    // Okay, save
    if(($values['sitestore_likelimit_forurlblock'] >= 0)) {
			foreach( $values as $key => $value ) {
				if($value != '') {
					Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
				}
			}
    }
    else {
			$error = Zend_Registry::get('Zend_Translate')->_('The value that you enter for Likes Limit for Active Short URL should be 0 or greater.');
			$form->getDecorator('errors')->setOption('escape', false);
			$form->addError($error);
			return;
    }
    
    Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestoreurl_is_enable', 1);
    $this->view->form = $form = new Sitestoreurl_Form_Admin_Global();
    $form->addNotice('Your changes have been saved.');
    $is_element = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreurl.is.enable', 0);
    $query_flag = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreurl.query.flag', 0);
    	
//		if(!empty($is_element) && empty($query_flag)) {
//			Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestoreurl.query.flag', 1);
//			$db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`,
//				`menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
//				("sitestoreurl_admin_blockurl", "sitestoreurl", "Banned URLs", "",
//				\'{"route":"admin_default","module":"sitestoreurl","controller":"settings","action":"banningurl"}\',
//				"sitestoreurl_admin_main", "", 1,0,2),
//				( "sitestoreurl_admin_main_url", "sitestoreurl", "Stores with Banned URLs", NULL,
//				\'{"route":"admin_default","module":"sitestoreurl","controller":"settings","action":"storeurl"}\',
//				"sitestoreurl_admin_main", NULL, 1, 0, 3);');
//				return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
//		}
  }

  //ACTION FOR BANNEDURL
  public function banningurlAction() {

  //GET NAVIGATION
  $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
          ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_global_url');
  
  //GET NAVIGATION
  $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('sitestoreurl_admin_main', array(), 'sitestoreurl_admin_blockurl');   

    $this->view->formFilter = $formFilter = new Sitestoreurl_Form_Admin_Manage_Filter();
    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    if (isset($_POST['search'])) {
      if (!empty($_POST['word'])) {
        $this->view->word = $_POST['word'];
        $values['word'] = $_POST['word'];
      }
    }
  
    $values = array_merge(array(
                'order' => 'bannedpageurl_id',
                'order_direction' => 'DESC',
                    ), $values);

    $this->view->assign($values);

     // Load all words
    $bannedStoreurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
    $this->view->paginator = $paginator = $bannedStoreurlsTable->getWords($values);
    $store=$this->_getParam('store',1);
    $this->view->paginator->setItemCountPerPage(500);
    $this->view->paginator->setCurrentPageNumber($store);
 
  }

  public function createUrlAction() {

     // Get form
    $this->view->form = $form = new Sitestoreurl_Form_Admin_Settings_Blockurl();

     // Load all words
    $bannedStoreurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
    
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {

			// Process
			$db = Engine_Api::_()->getDbtable('settings', 'core')->getAdapter();
			$db->beginTransaction();
			
			try {
				$values = $form->getValues();

				// Save Banned Words
				$bannedWordsNew = preg_split('/\s*[,\n]+\s*/', $values['bannedwords']);
			
				$words = array_map('strtolower', array_filter(array_values($bannedWordsNew)));

				$data = $bannedStoreurlsTable->select()
																		->from($bannedStoreurlsTable, 'word')
																		->query()
																		->fetchAll(Zend_Db::FETCH_COLUMN);
				if(in_array($words[0],$data)) {
					$form->addError(Zend_Registry::get('Zend_Translate')->_('This URL already exists.'));
						return;
				}
				$bannedStoreurlsTable->setWords($bannedWordsNew);
				$db->commit();
				//$form->addNotice('Your changes have been saved.');
			} catch( Exception $e ) {
				$db->rollback();
				throw $e;
			}
			$this->_forward('success', 'utility', 'core', array(
						'smoothboxClose' => 10,
						'parentRefresh'=> 10,
						'messages' => array('')
				));
    }
 
  }

  public function editUrlAction() {

    // Get form
    $this->view->form = $form = new Sitestoreurl_Form_Admin_Settings_Blockurl();
    $url_id = $this->_getParam('id');
    $bannedStoreurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
    $data = $bannedStoreurlsTable->select()
        ->from($bannedStoreurlsTable)
        ->where('bannedpageurl_id =?',$url_id)
        ->query()
        ->fetchAll();
    $form->setField($data);

    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {

			// Process
			$db = Engine_Api::_()->getDbtable('settings', 'core')->getAdapter();
			$db->beginTransaction();
			
			
			try {
				$values = $form->getValues();
				// Save Banned Words
				$bannedWordsNew = preg_split('/\s*[,\n]+\s*/', $values['bannedwords']);
			
				$words = array_map('strtolower', array_filter(array_values($bannedWordsNew)));

				$data = $bannedStoreurlsTable->select()
					->from($bannedStoreurlsTable, 'word')
					->where('bannedpageurl_id !=?',$url_id)
					->query()
					->fetchAll(Zend_Db::FETCH_COLUMN);
				if(in_array($words[0],$data)) {
					$form->addError(Zend_Registry::get('Zend_Translate')->_('This URL already exists.'));
						return;
				}
				$bannedStoreurlsTable->update(array('word' => $words[0]), array('bannedpageurl_id = ?' => $url_id));
				$db->commit();
			} catch( Exception $e ) {
				$db->rollback();
				throw $e;
			}
			$this->_forward('success', 'utility', 'core', array(
						'smoothboxClose' => 10,
						'parentRefresh'=> 10,
						'messages' => array('')
				));
    }

  }

  public function deleteUrlAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

		//GET CATEGORY ID AND CHECK VALIDATION
    $this->view->id = $id = $this->_getParam('id');

    if(empty($id)) {
    	die('No identifier specified');
    }
    if( $this->getRequest()->isPost()) {

			//BEGIN TRANSACTION
    	$db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        $bannedStoreurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
				$selectdata = $bannedStoreurlsTable->select()
        ->where('bannedpageurl_id =?',$id);
        $result_url = $bannedStoreurlsTable->fetchRow($selectdata);
       $result_url->delete();
				//COMMIT
        $db->commit();
      }

      catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }

  }
  
   public function addUrlAction() {

     // Get form
    $this->view->form = $form = new Sitestoreurl_Form_Admin_Settings_Addurl();

     if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
			$bannedStoreurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
			// Process
			$db = Engine_Api::_()->getDbtable('settings', 'core')->getAdapter();
			$db->beginTransaction();
			
			
			try {
				$values = $form->getValues();

        $includeModules = array("sitestore" => "sitestore","sitestoredocument" => 'Documents', "sitestoreoffer" => 'Offers', "sitestoreform" => "Form", "sitestorediscussion" => "Discussions", "sitestorenote" => "Notes", "sitestorealbum" => "Photos", "sitestorevideo" => "Videos", "sitestoreevent" => "Events", "sitestorepoll" => "Polls", "sitestoreinvite" => "Invite & Promote", "sitestorebadge" => "Badges", "sitestorelikebox" => "External Badge", "sitestoremusic" => "Music","sitestore" => "sitestore","sitestoredocument" => 'Documents', "sitestoreoffer" => 'Offers', "sitestoreform" => "Form", "sitestorediscussion" => "Discussions", "sitestorenote" => "Notes", "sitestorealbum" => "Photos", "sitestorevideo" => "Videos", "sitestoreevent" => "Events", "sitestorepoll" => "Polls", "sitestoreinvite" => "Invite & Promote", "sitestorebadge" => "Badges", "sitestorelikebox" => "External Badge", "sitestoremusic" => "Music","list"=>"
list");
				$moduleTable = Engine_Api::_()->getDbtable('modules', 'core');
				$select = $moduleTable->select()->where('enabled = ?', 1);
				$enableAllModules = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
				$enableModules = array_intersect(array_keys($includeModules), $enableAllModules);

        if(!in_array($values['module_name'],$enableModules)) {
					$file_path = APPLICATION_PATH . "/application/modules/" . ucfirst($values['module_name']) . "/settings/manifest.php";
					if (@file_exists($file_path)) {
						$ret = include $file_path;
						$is_exist = array();
						if (isset($ret['routes'])) {
							foreach ($ret['routes'] as $item) {
								$route = $item['route'];
								$route_array =  explode('/',$route);
								$route_url = strtolower($route_array[0]);
								
								$data = $bannedStoreurlsTable->select()
																						->from($bannedStoreurlsTable, 'bannedpageurl_id')
																						->where('word =?',$route_url)
																						->query()
																						->fetchAll(Zend_Db::FETCH_COLUMN);

								if(empty($data)) { 
									if(!empty($route_url) && !in_array($route_url,$is_exist)) {
										$db->query("INSERT IGNORE INTO `engine4_seaocore_bannedpageurls` (`bannedpageurl_id`, `word`) VALUES ('','".$route_url. "')");
									}
								}
								else {
									$bannedStoreurlsTable->update(array('word' => $route_url), array('bannedpageurl_id = ?' => $data[0]));
								}
								$is_exist[] = $route_url;
							}
						}
					} 
        }
        else {
						if($moduleName == 'sitestore' || $moduleName == 'sitestore') {
							$name = $moduleName .'.manifestUrlS';
						}
						else {
							$name = $moduleName .'.manifestUrl';
						}
						$settingTable = Engine_Api::_()->getDbtable('settings', 'core');
						$select = $settingTable->select()
                                   ->from($settingTable,'value')
                                  ->where('name = ?', $name);
						$route_url = strtolower($select->query()->fetchAll(Zend_Db::FETCH_COLUMN));
						if(!empty($route_url)) {
							$db->query("INSERT IGNORE INTO `engine4_seaocore_bannedpageurls` (`bannedpageurl_id`, `word`) VALUES ('','".$route_url. "')");
						}
					}


			  /*data = $bannedStoreurlsTable->select()
					->from($bannedStoreurlsTable, 'word')
					->where('word !=?',$values[])
					->query()
					->fetchAll(Zend_Db::FETCH_COLUMN);*/
				$bannedStoreurlsTable->update(array('word' => $words[0]), array('bannedpageurl_id = ?' => $url_id));
				$db->commit();
			} catch( Exception $e ) {
				$db->rollback();
				throw $e;
			}
			$this->_forward('success', 'utility', 'core', array(
						'smoothboxClose' => 10,
						'parentRefresh'=> 10,
						'messages' => array('')
				));
    }


  }

  public function storeurlAction() {
    
    
    //GET NAVIGATION
  $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
          ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_global_url');
  
  //GET NAVIGATION
  $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('sitestoreurl_admin_main', array(), 'sitestoreurl_admin_main_url'); 
 
    //FORM GENERATION
    $this->view->formFilter = $formFilter = new Sitestoreurl_Form_Admin_Manage_Filter();
     $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
                'order' => 'bannedpageurl_id',
                'order_direction' => 'DESC',
                    ), $values);

    $this->view->assign($values);
  
    $this->view->paginator = $paginator = Engine_Api::_()->sitestoreurl()->getBlockUrl($values);
    $store=$this->_getParam('store',1);
    $this->view->paginator->setItemCountPerPage(20);
    $this->view->paginator->setCurrentPageNumber($store);
  }

}

?>
