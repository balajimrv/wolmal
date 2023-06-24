<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Plugin_Core extends Zend_Controller_Plugin_Abstract {

  private function _createStoreBySubscribedPlan($params = null){   
    $isStoreExist = false;
    $paymentSubscriptionTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
    $paymentSubscriptionTableName = $paymentSubscriptionTable->info('name');
    
    $planmapsTable = Engine_Api::_()->getDbtable('planmaps', 'sitestore');
    $planmapsTableName = $planmapsTable->info('name');
    $status = 'initial';

    if(!empty($params) && array_key_exists('plane_id', $params) && !empty($params['plane_id'])){
      $isFree = Engine_Api::_()->getItem('payment_package', $params['plane_id'])->isFree();
      $sessionUserSession = new Zend_Session_Namespace('Payment_Subscription');
      if (!empty($sessionUserSession->user_id)) {
        $viewer = Engine_Api::_()->getItem('user', $sessionUserSession->user_id);
      } else {
        $viewer = Engine_Api::_()->user()->getViewer();
      }

      if (!empty($isFree)) {
        $store_package_id = $planmapsTable->select()
                ->from($planmapsTableName, array('package_id as map_package_id'))
                ->where('plan_id = ?', $params['plane_id'])
                ->query()
                ->fetchColumn();
        $status = 'active';
      }
    }else {
      $viewer = Engine_Api::_()->user()->getViewer();
      $select = $paymentSubscriptionTable->select()
              ->setIntegrityCheck(false)
              ->from($paymentSubscriptionTableName)
              ->joinLeft($planmapsTableName, "$paymentSubscriptionTableName.package_id = $planmapsTableName.plan_id", array('package_id as map_package_id', 'planmap_id'))
              ->where($paymentSubscriptionTableName . '.user_id = ?', $viewer->getIdentity())
              ->where('active = ?', true)
              ->Limit(1);
      
      $subscriptionObj = $paymentSubscriptionTable->fetchRow($select);
      $store_package_id  = $subscriptionObj->map_package_id;
      $status = 'active';
    }
    
    if(!empty($params) && array_key_exists('check_store_count', $params)) {
      $store = Engine_Api::_()->getDbTable('stores', 'sitestore');
      $isStoreExist = $store->select()
                      ->from($store->info('name'), array('store_id'))
                      ->where("owner_id = ?", $viewer->getIdentity())
                      ->limit(1)
                      ->query()->fetchColumn();
    }
    
    if(empty($isStoreExist) && !empty($store_package_id)){  
      $table = Engine_Api::_()->getItemTable('sitestore_store');
      $sitestore = $table->createRow();
      $sitestore->title = $viewer->displayname;
      $sitestore->store_url = $viewer->username . '_' . $viewer->user_id;
      $sitestore->owner_id = $viewer->getIdentity();
      $sitestore->package_id = $store_package_id;
      $sitestore->draft = 0;
    
      $package = Engine_Api::_()->getItemTable('sitestore_package')->fetchRow(array('package_id = ?' => $store_package_id, 'enabled = ?' => '1'));      

      if(!isset($package->sponsored) || empty($package->sponsored))
        $sitestore->sponsored = 0;
      else
        $sitestore->sponsored = $package->sponsored;
      
      if(!isset($package->featured) || empty($package->featured))
        $sitestore->featured = 0;
      else
        $sitestore->featured = $package->featured;

      
      if(!isset($package->approved) || empty($package->approved))
        $sitestore->approved = 0;
      else
        $sitestore->approved = $package->approved;
      
//      if ($package->isFree())
//        $sitestore->approved = $package->approved;
//      else
//        $sitestore->approved = 0;
      
      if (!empty($sitestore->approved)) {
        $sitestore->pending = 0;
        $sitestore->aprrove_date = date('Y-m-d H:i:s');
        $sitestore->status = $status;
        
          $expirationDate = $package->getExpirationDate();
          if (!empty($expirationDate))
            $sitestore->expiration_date = date('Y-m-d H:i:s', $expirationDate);
          else
            $sitestore->expiration_date = '2250-01-01 00:00:00';
      }

      $sitestore->save();
                              
        $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitestore');
        $row = $manageadminsTable->createRow();
        $row->user_id = $viewer->getIdentity();
        $row->store_id = $sitestore->store_id;
        $row->save();
    }
  }
  
  public function onUserSignupAfter($event){
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      $step_table = Engine_Api::_()->getDbtable('signup', 'user');
      $step_row = $step_table->fetchRow($step_table->select()->where('class = ?', 'Payment_Plugin_Signup_Subscription'));
      if (!empty($step_row) && empty($step_row->enable)) {
        $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
        $package = $packagesTable->fetchRow(array(
            '`default` = ?' => true,
            'enabled = ?' => true,
            'price <= ?' => 0,
                ));

        if (!empty($package)) {
          $this->_createStoreBySubscribedPlan(array('plane_id' => $package->package_id));
        }
      } else {
        $this->_createStoreBySubscribedPlan();
      }
    }
  }

  public function routeShutdown(Zend_Controller_Request_Abstract $request) {
    if (substr($request->getPathInfo(), 1, 5) == "admin") {
      $module = $request->getModuleName();
      $controller = $request->getControllerName();
      $action = $request->getActionName();
      if ($module == 'core' && $controller == 'admin-content' && $action == 'index') {
        $sitestoreLayoutCreate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate');
        if (!empty($sitestoreLayoutCreate)) {
          $store_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page', null);
          if (!empty($store_id)) {
            $corestoreTable = Engine_Api::_()->getDbtable('pages', 'core');
            $corestoreTableName = $corestoreTable->info('name');
            $select = $corestoreTable->select()
                    ->from($corestoreTableName)
                    ->where('page_id' . ' = ?', $store_id)
                    ->where('name' . ' = ?', 'sitestore_index_view')
                    ->limit(1);
            $corestoreTableInfo = $corestoreTable->fetchRow($select);
          }
          if (!empty($corestoreTableInfo)) {
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            $redirector->gotoRoute(array('module' => 'sitestore', 'controller' => 'layout', 'action' => 'layout', 'store' => $store_id), 'admin_default', false);
          }
        }
      }
    }

    //CHECK IF ADMIN
    if (substr($request->getPathInfo(), 1, 5) == "admin") {
      return;
    }
   
    $module = $request->getModuleName();
		$controller = $request->getControllerName();
		$action = $request->getActionName();
        
    //WORK FOR AUTOMATIC CREATION OF STORE ON SIGNUP WHEN SUBSCRIPTION IS CHOOSEN AT LAST STARTS HERE
    if ($module == 'payment' && $controller == 'subscription' && $action == 'index') {
      if(!empty($_POST['package_id'])){
        // FOR FREE SUBSCRIPTION
         if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        $this->_createStoreBySubscribedPlan(array('plane_id' => $_POST['package_id'], 'check_store_count' => true));
         }
      }
    } elseif ($module == 'payment' && $controller == 'subscription' && $action == 'finish') {
      // FOR PAID SUBSCRIPTION
      $state = $request->getParam('state');
      if (!empty($state) && $state = 'active') {
        if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
          $this->_createStoreBySubscribedPlan(array('check_store_count' => true));
        }
      }
    }
        
    // SITESTOREURL WORK START
    $sitestoreUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreurl');
    if (!empty($sitestoreUrlEnabled) && (($module == 'sitestore' && ($controller == 'index' || $controller == 'mobi') && $action == 'view') || ($module == 'core'))) {
      $front = Zend_Controller_Front::getInstance();

      // GET THE URL OF STORE
      $urlO = $request->getRequestUri();
      $storeurl = '';

      // GET THE ROUTE BY WHICH STORE WILL BE OPEN IF SHORTEN STOREURL IS DISABLED
      $routeStartS = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manifestUrlS', "store");

      // GET THE BASE URL
      $base_url = $front->getBaseUrl();

      // MAKE A STRING OF BASEUL WITH ROUTESTART
      $string_url = $base_url . '/' . $routeStartS.'/';

      // FIND OUT THE POSITION OF ROUTESTART IF EXIST
      $pos_routestart = strpos($urlO, $string_url);
      if ($pos_routestart === false) {
        $index_routestart = 0;
        $storeurlArray = explode($base_url . '/', $urlO);
        $mainStoreurl = strstr($storeurlArray[1], '/');

        // CHECK BASEDIRECTORY IS EXIST OR NOT
        if (empty($mainStoreurl)) {
          if (isset($storeurlArray[1])) {
            $storeurl = $storeurlArray[1];
          }
        } else {
          $storeurl = $mainStoreurl;
        }
      } else {
        $index_routestart = 1;
        $storeurlArray = explode($string_url, $urlO);
        $final_url = $storeurlArray[1];
        $mainStoreurl = explode('/', $final_url);
        if (isset($mainStoreurl[1]))
          $storeurl = $mainStoreurl[1];
      }

      // GET THE STORE LIKES AFTER WHICH SHORTEN STOREURL WILL BE WORK 
      $store_likes = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.likelimit.forurlblock', "5");
      $params_array = array();
      if ($front->getBaseUrl() == '' && empty($index_routestart)) {
        $params_array = $storeurlArray;
        $params_array[0] = NULL;
        array_shift($params_array);
      } else {
        $params_array = explode('/', $storeurlArray[1]);
      }

      if (!empty($index_routestart)) {
        if (isset($params_array['1']))
          $storeurl = $params_array['1'];
      }
      else {
        $storeurl = $params_array['0'];
      }
      
      $storeurl = explode('?',$storeurl);
      $storeurl = $storeurl[0];

      // MAKE THE OBJECT OF SITESTORE
      $sitestoreObject = Engine_Api::_()->getItem('sitestore_store', Engine_Api::_()->sitestore()->getStoreId($storeurl));

      $bannedStoreurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');

      // GET THE ARRAY OF BANNED STOREURLS
      $urlArray = $bannedStoreurlsTable->select()->from($bannedStoreurlsTable, 'word')
                      ->where('word = ?', $storeurl)
                      ->query()->fetchColumn();
      $change_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.change.url', 1);
      if (empty($urlArray) && (!empty($change_url)) && !empty($sitestoreObject) && ($sitestoreObject->like_count >= $store_likes)) {
        if ((!empty($index_routestart))) {
          $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
          unset($params_array[0]);
          $redirector->gotoUrl(implode("/", $params_array));
        }
        $request->setModuleName('sitestore');
        $request->setControllerName('index');
        $request->setActionName('view');
        $request->setParam("store_url", $storeurl);
        $count = count($params_array);
        for ($i = 1; $i <= $count; $i++) {
          if( array_key_exists($i, $params_array) ) {
            if(!empty($params_array[$i])) {
                                                        $request->setParam($params_array[$i], $params_array[++$i]);
            }
          }
        }
        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
          $sr_response = Engine_Api::_()->sitemobile()->setupRequest($request);
        }
      }
    }
    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('mobi'))
      return;

    $mobile = $request->getParam("mobile");
    $session = new Zend_Session_Namespace('mobile');

    if ($mobile == "1") {
      $mobile = true;
      $session->mobile = true;
    } elseif ($mobile == "0") {
      $mobile = false;
      $session->mobile = false;
    } else {
      if (isset($session->mobile)) {
        $mobile = $session->mobile;
      } else {
        //CHECK TO SEE IF MOBILE
        if (Engine_Api::_()->mobi()->isMobile()) {
          $mobile = true;
          $session->mobile = true;
        } else {
          $mobile = false;
          $session->mobile = false;
        }
      }
    }

    if (!$mobile) {
      return;
    }
    $module = $request->getModuleName();
    $controller = $request->getControllerName();
    $action = $request->getActionName();
    if ($module == "sitestore") {
      if ($controller == "index" && $action == "home") {
        $request->setControllerName('mobi');
        $request->setActionName('home');
      }

      if ($controller == "index" && $action == "index") {
        $request->setControllerName('mobi');
        $request->setActionName('index');
      }

      if ($controller == "index" && $action == "view") {

        $request->setControllerName('mobi');
        $request->setActionName('view');
      }

      if ($controller == "index" && $action == "map") {
        $request->setControllerName('index');
        $request->setActionName('mobilemap');
      }
    }

    //CREATE LAYOUT
    $layout = Zend_Layout::startMvc();

    //SET OPTIONS
    $layout->setViewBasePath(APPLICATION_PATH . "/application/modules/Mobi/layouts", 'Core_Layout_View')
            ->setViewSuffix('tpl')
            ->setLayout(null);
  }

  public function onRenderLayoutDefault($event) {
    $sitestore_layout_setting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layout.setting', 1);
    $sitestore_hide_left_container = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.hide.left.container', 0);
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();
		$membercategory_ids='';
		$sitestore_slding_effect = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.slding.effect', 1);
		$show_option = 1;
    if ($module == "sitestore") {
      if ($controller == "index" && $action == "index") {
        $siteinfo = $view->layout()->siteinfo;
        if (Zend_Controller_Front::getInstance()->getRequest()->getParam('categoryname', null)) {
          $siteinfo['keywords'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('categoryname', null);
        }
        if (Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategoryname', null)) {
          $siteinfo['keywords'] .= ',' . Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategoryname', null);
        }

        if (!empty($_GET['location'])) {
          if (Zend_Controller_Front::getInstance()->getRequest()->getParam('location', null)) {
            $siteinfo['keywords'] .= ',';
          }
          $siteinfo['keywords'] .= $_GET['location'];
        }
        $view->layout()->siteinfo = $siteinfo;
      }

      $category_name = "";
      $subcategory_name = "";
      if (Zend_Controller_Front::getInstance()->getRequest()->getParam('store_id', null)) {
        $currentstoreid = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_id', null);
        $sitestoreObject = Engine_Api::_()->getItem('sitestore_store', $currentstoreid);
        $siteinfo = $view->layout()->siteinfo;
        $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($sitestoreObject->category_id);
        if (!empty($row->category_name)) {
          $category_name = $row->category_name;
          $siteinfo['keywords'] = $category_name;
        }
        $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($sitestoreObject->subcategory_id);
        if (!empty($row->category_name)) {
          $subcategory_name = $row->category_name;
          $siteinfo['keywords'] .= ',' . $subcategory_name;
        }

        if (!empty($sitestoreObject->location)) {
          $siteinfo['keywords'] .= ',' . $sitestoreObject->location;
        }
        $view->layout()->siteinfo = $siteinfo;
      }
      if ($controller == "index" && $action == "view") {
        if (Zend_Controller_Front::getInstance()->getRequest()->getParam('store_url', null)) {
          $currenttabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', 0);
          $store_url = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_url', null);
          $contentinformation = 0;
          $id = Engine_Api::_()->sitestore()->getStoreId($store_url);
          $sitestore_title = '';
          if (!empty($id)) {
            $sitestore = Engine_Api::_()->getItem('sitestore_store', $id);
            $sitestore_title = Engine_Api::_()->sitestore()->parseString($sitestore->title);
            $sitestore_title = str_replace('"', "'", $sitestore_title);
          }

          $content_id = 0;
          $content_id3 = 0;
          $tab_id = 0;
          $tab_id3 = 0;
          $tab_id4 = 0;
          $tab_id5 = 0;
          $widgetinformation = 0;
          $tempcontent_name = "";
          $tempcontent_id = 0;
          $newtabid = 0;
          $itemAlbumCount = 10;
          $itemPhotoCount = 100;
          if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0) && !empty($sitestore)) {
          $row = Engine_Api::_()->getDbtable('contentstores', 'sitestore')->getContentStoreId($id);
            if ($row !== null) {
              $contentstore_id = $row->contentstore_id;
              $siteinfo = $view->layout()->siteinfo;
              $siteinfo['description'] = $row->description;
              $siteinfo['keywords'] = $row->keywords;
              $view->layout()->siteinfo = $siteinfo;
              $rowinfo = Engine_Api::_()->getDbtable('content', 'sitestore')->getContentInformation($contentstore_id);

              if (!empty($rowinfo)) {
                foreach ($rowinfo as $key => $value) {
                  if ($value->name == 'advancedactivity.home-feeds') {
                    $content_id = $tab_id = $value->content_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id;
                    }
                  } elseif ($value->name == 'seaocore.feed') {
                    $content_id = $tab_id = $value->content_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id;
                    }
                  } elseif ($value->name == 'activity.feed') {
                    $content_id = $tab_id = $value->content_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id;
                    }
                  } else if ($value->name == 'core.profile-links') {
                    $content_id3 = $tab_id3 = $value->content_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id3;
                    }
                  } else if ($value->name == 'core.html-block') {
                    $content_id4 = $tab_id4 = $value->content_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id4;
                    }
                  } else if ($value->name == 'sitestoreproduct.store-profile-products') {
                    $content_id5 = $tab_id5 = $value->content_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id5;
                    }
                  }
                }
              }
							if (!empty($contentstore_id)) {
								$contentinfo = Engine_Api::_()->getDbtable('content', 'sitestore')->getContentByWidgetName('core.container-tabs', $contentstore_id);
								if (empty($contentinfo)) {
									$contentinformation = 0;
								} else {
									$contentinformation = 1;
								}

								$contentwidgetinfo = Engine_Api::_()->getDbtable('content', 'sitestore')->getContentByWidgetName('sitestore.widgetlinks-sitestore', $contentstore_id);
								if (empty($contentwidgetinfo)) {
									$widgetinformation = 0;
								} else {
									$widgetinformation = 1;
								}
							}
							$default_content_id = Engine_Api::_()->getDbtable('content', 'sitestore')->getContentId($contentstore_id, $sitestore);
							$tempcontent_name = $default_content_id['content_name'];
							$tempcontent_id = $default_content_id['content_id'];
							if (empty($default_content_id['itemAlbumCount'])) {
								$itemAlbumCount = 10;
							} else {
								$itemAlbumCount = $default_content_id['itemAlbumCount'];
							}
							if (empty($default_content_id['itemPhotoCount'])) {
								$itemPhotoCount = 100;
							} else {
								$itemPhotoCount = $default_content_id['itemPhotoCount'];
							}
							if (empty($default_content_id['resource_type_integration'])) {
								$resource_type_integration = Zend_Controller_Front::getInstance()->getRequest()->getParam('resource_type', 0);
								$ads_display_integration = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitestore.ad.$resource_type_integration", 3);
							} else {
								$resource_type_integration = $default_content_id['resource_type_integration'];
								$ads_display_integration = $default_content_id['ads_display_integration'];
							}

							$newtabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', $tempcontent_id);
							$tab_main = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
							if ($tab_main) {
								$tempcontent_name = Engine_Api::_()->getDbtable('content', 'sitestore')->getCurrentTabName($tab_main);
								$tempcontent_id = $tab_main;
							}
            } else {
              $contentstore_id = Engine_Api::_()->sitestore()->getWidgetizedStore()->page_id;
              $siteinfo = $view->layout()->siteinfo;
              $siteinfo['description'] = Engine_Api::_()->sitestore()->getWidgetizedStore()->description;
              $siteinfo['keywords'] = Engine_Api::_()->sitestore()->getWidgetizedStore()->keywords;
              $view->layout()->siteinfo = $siteinfo;
              $rowinfo = Engine_Api::_()->getDbtable('admincontent', 'sitestore')->getContentInformation($contentstore_id);

              if (!empty($rowinfo)) {
                foreach ($rowinfo as $key => $value) {
                  if ($value->name == 'advancedactivity.home-feeds') {
                    $content_id = $tab_id = $value->admincontent_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id;
                    }
                  } elseif ($value->name == 'seaocore.feed') {
                    $content_id = $tab_id = $value->admincontent_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id;
                    }
                  } elseif ($value->name == 'activity.feed') {
                    $content_id = $tab_id = $value->admincontent_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id;
                    }
                  } else if ($value->name == 'core.profile-links') {
                    $content_id3 = $tab_id3 = $value->admincontent_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id3;
                    }
                  } else if ($value->name == 'core.html-block') {
                    $content_id4 = $tab_id4 = $value->admincontent_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id4;
                    }
                  } else if ($value->name == 'sitestoreproduct.store-profile-products') {
                    $content_id5 = $tab_id5 = $value->content_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id5;
                    }
                  }
                }
              }
							if (!empty($contentstore_id)) {
								$contentinfo = Engine_Api::_()->getDbtable('admincontent', 'sitestore')->getContentByWidgetName('core.container-tabs', $contentstore_id);
								if (empty($contentinfo)) {
									$contentinformation = 0;
								} else {
									$contentinformation = 1;
								}

								$contentwidgetinfo = Engine_Api::_()->getDbtable('admincontent', 'sitestore')->getContentByWidgetName('sitestore.widgetlinks-sitestore', $contentstore_id);
								if (empty($contentwidgetinfo)) {
									$widgetinformation = 0;
								} else {
									$widgetinformation = 1;
								}
							}
							$default_content_id = Engine_Api::_()->getDbtable('admincontent', 'sitestore')->getContentId($contentstore_id, $sitestore);
							$tempcontent_name = $default_content_id['content_name'];
							$tempcontent_id = $default_content_id['content_id'];
							if (empty($default_content_id['itemAlbumCount'])) {
								$itemAlbumCount = 10;
							} else {
								$itemAlbumCount = $default_content_id['itemAlbumCount'];
							}
							if (empty($default_content_id['itemPhotoCount'])) {
								$itemPhotoCount = 100;
							} else {
								$itemPhotoCount = $default_content_id['itemPhotoCount'];
							}
							if (empty($default_content_id['resource_type_integration'])) {
								$resource_type_integration = Zend_Controller_Front::getInstance()->getRequest()->getParam('resource_type', 0);
								$ads_display_integration = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitestore.ad.$resource_type_integration", 3);
							} else {
								$resource_type_integration = $default_content_id['resource_type_integration'];
								$ads_display_integration = $default_content_id['ads_display_integration'];
							}

							$newtabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', $tempcontent_id);
							$tab_main = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
							if ($tab_main) {
								$tempcontent_name = Engine_Api::_()->getDbtable('admincontent', 'sitestore')->getCurrentTabName($tab_main);
								$tempcontent_id = $tab_main;
							}
            }
              $select_offer = Engine_Api::_()->getDbtable('admincontent', 'sitestore')->select()
                            ->from(Engine_Api::_()->getDbtable('admincontent', 'sitestore')->info('name'), array('params'))
                            ->where('admincontent_id = ?', $tempcontent_id)
                            ->where('type = ?', 'widget')
                            ->where('name = ?', 'sitestoreoffer.profile-sitestoreoffers'); 
              $storeoffers_params = $select_offer->query()->fetchColumn();
              $statistics = array("startdate", "enddate", "couponcode", 'discount', 'claim' ,'expire');
              if (!empty($storeoffers_params)) {
                $storeParamsDecodedArray = Zend_Json_Decoder::decode($storeoffers_params); 
                if($storeParamsDecodedArray['statistics']){
                    $statistics = $storeParamsDecodedArray['statistics']; 
                }
              } 
          } else {
            $table = Engine_Api::_()->getDbtable('pages', 'core');
            $select = $table->select()
                    ->where('name = ?', 'sitestore_index_view')
                    ->limit(1);
            $row = $table->fetchRow($select);
            if ($row !== null) {
              $store_id = $row->page_id;
              $table = Engine_Api::_()->getDbtable('content', 'core');
              $select = $table->select()
                      ->where("name IN ('sitestore.info-sitestore', 'seaocore.feed', 'advancedactivity.home-feeds', 'activity.feed', 'sitestore.location-sitestore', 'core.profile-links', 'core.html-block', 'sitestoreproduct.store-profile-products')")
                      ->where('page_id = ?', $store_id)
                      ->order('content_id ASC');
              $rowinfo = $table->fetchAll($select);
              if (!empty($rowinfo)) {
                foreach ($rowinfo as $key => $value) {
                  if ($value->name == 'advancedactivity.home-feeds') {
                    $content_id = $tab_id = $value->content_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id;
                    }
                  } elseif ($value->name == 'seaocore.feed') {
                    $content_id = $tab_id = $value->content_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id;
                    }
                  } elseif ($value->name == 'activity.feed') {
                    $content_id = $tab_id = $value->content_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id;
                    }
                  } else if ($value->name == 'core.profile-links') {
                    $content_id3 = $tab_id3 = $value->content_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id3;
                    }
                  } else if ($value->name == 'core.html-block') {
                    $content_id4 = $tab_id4 = $value->content_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id4;
                    }
                  } else if ($value->name == 'sitestoreproduct.store-profile-products') {
                    $content_id5 = $tab_id5 = $value->content_id;
                    if (empty($currenttabid)) {
                      $currenttabid = $content_id5;
                    }
                  }
                }
              }
            }

            $table = Engine_Api::_()->getDbtable('content', 'core');
            $tablename = $table->info('name');
            if (!empty($store_id)) {
              $selectContent = $table->select()
                      ->from($tablename)
                      ->where('name =?', 'core.container-tabs')
                      ->where('page_id =?', $store_id)
                      ->limit(1);
              $contentinfo = $selectContent->query()->fetchAll();
              if (empty($contentinfo)) {
                $contentinformation = 0;
              } else {
                $contentinformation = 1;
              }
            }

            if (!empty($store_id)) {
              $selectContent = $table->select()
                      ->from($tablename)
                      ->where('name =?', 'sitestore.widgetlinks-sitestore')
                      ->where('page_id =?', $store_id)
                      ->limit(1);
              $contentwidgetinfo = $selectContent->query()->fetchAll();
              if (empty($contentwidgetinfo)) {
                $widgetinformation = 0;
              } else {
                $widgetinformation = 1;
              }
            }

            $selectStore = Engine_Api::_()->sitestore()->getWidgetizedStore();
            if (!empty($selectStore)) {
              $storeid = $selectStore->page_id;
              if (!empty($storeid)) {
                $tableCore = Engine_Api::_()->getDbtable('content', 'core');
                $select = $tableCore->select();
                $select_content = $select
                        ->from($tableCore->info('name'))
                        ->where('page_id = ?', $storeid)
                        ->where('type = ?', 'container')
                        ->where('name = ?', 'main')
                        ->limit(1);
                $content = $select_content->query()->fetchAll();
                if (!empty($content)) {
                  $select = $tableCore->select();
                  $select_container = $select
                          ->from($tableCore->info('name'), array('content_id'))
                          ->where('page_id = ?', $storeid)
                          ->where('type = ?', 'container')
                          ->where('name = ?', 'middle')
                          ->limit(1);
                  $container = $select_container->query()->fetchAll();
                  if (!empty($container)) {
                    $select = $tableCore->select();
                    $container_id = $container[0]['content_id'];
                    $select_middle = $select
                            ->from($tableCore->info('name'))
                            ->where('parent_content_id = ?', $container_id)
                            ->where('type = ?', 'widget')
                            ->where('name = ?', 'core.container-tabs')
                            ->where('page_id = ?', $storeid)
                            ->limit(1);
                    $middle = $select_middle->query()->fetchAll();
                    if (!empty($middle)) {
                      $content_id = $middle[0]['content_id'];
                    } else {
                      $content_id = $container_id;
                    }
                  }
                }

                if (!empty($content_id) && !empty($sitestore)) {
                  $select = $tableCore->select();
									$select_middle = $select
													->from($tableCore->info('name'), array('content_id', 'name', 'params'))
													->where('parent_content_id = ?', $content_id)
													->where('type = ?', 'widget')
													->where("name NOT IN ('sitestore.title-sitestore', 'seaocore.like-button','seaocore.seaocore-follow', 'sitestore.photorecent-sitestore', 'Facebookse.facebookse-commonlike', 'sitestore.thumbphoto-sitestore', 'sitestore.contactdetails-sitestore','sitelike.common-like-button')")
													->where('page_id = ?', $storeid);
                 

                  $middle = $select_middle->query()->fetchAll();

                  $itemAlbumCount = 10;
                  $itemPhotoCount = 100;
                  $select = $tableCore->select();
                  $select_photo = $select
                          ->from($tableCore->info('name'), array('params'))
                          ->where('parent_content_id = ?', $content_id)
                          ->where('type = ?', 'widget')
                          ->where('name = ?', 'sitestore.photos-sitestore')
                          ->where('page_id = ?', $storeid)
                          ->order('order DESC');

                  $middlePhoto = $select_photo->query()->fetchColumn();
                  if (!empty($middlePhoto)) {
                    $photoParamsDecodedArray = Zend_Json_Decoder::decode($middlePhoto);
                    if (isset($photoParamsDecodedArray['itemCount']) && !empty($photoParamsDecodedArray)) {
                      $itemAlbumCount = $photoParamsDecodedArray['itemCount'];
                    }
                    if (isset($photoParamsDecodedArray['itemCount_photo']) && !empty($photoParamsDecodedArray)) {
                      $itemPhotoCount = $photoParamsDecodedArray['itemCount_photo'];
                    }
                  }

                  $flag = false;
                  $editpermission = '';
                  $isManageAdmin = '';
                  $resource_type_integration = Zend_Controller_Front::getInstance()->getRequest()->getParam('resource_type', 0);
                  $ads_display_integration = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitestore.ad.$resource_type_integration", 3);
                  $viewer = Engine_Api::_()->user()->getViewer();
                  $viewer_id = $viewer->getIdentity();
                  if (!empty($viewer_id)) {
                    $level_id = $viewer->level_id;
                  } else {
                    $level_id = 0;
                  }
                  foreach ($middle as $value) {
                    $content_name = $value['name'];
                    switch ($content_name) {
                      case 'sitestore.overview-sitestore':
                        if (!empty($sitestore)) {
                          $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'overview');
                          if (!empty($isManageAdmin)) {
                            $editpermission = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
                            if (!empty($editpermission) && empty($sitestore->overview)) {
                              $flag = true;
                            } elseif (empty($editpermission) && empty($sitestore->overview)) {
                              $flag = false;
                            } elseif (!empty($editpermission) && !empty($sitestore->overview)) {
                              $flag = true;
                            } elseif (empty($editpermission) && !empty($sitestore->overview)) {
                              $flag = true;
                            }
                          }
                        }
                        break;
                      case 'sitestore.location-sitestore':
                        if (!empty($sitestore)) {
                          $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'map');
                          if (!empty($isManageAdmin)) {
                            $value['id'] = $sitestore->getIdentity();
                            $location = Engine_Api::_()->getDbtable('locations', 'sitestore')->getLocation($value);
                            if (!empty($location)) {
                              $flag = true;
                            }
                          }
                        }
                        break;
                      case 'seaocore.feed':
                        $flag = true;
                        break;
                      case 'advancedactivity.home-feeds':
                        $flag = true;
                        break;
                      case 'activity.feed':
                        $flag = true;
                        break;
                      case 'sitestore.info-sitestore':
                        $flag = true;
                        break;
                      case 'core.html-block':
                        $flag = true;
                        break;
                      case 'core.profile-links':
                        $flag = true;
                        break;
                      case 'sitestoreintegration.profile-items':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration')) {
                          $content_params = $value['params'];
                          $paramsDecodedArray = Zend_Json_Decoder::decode($content_params);
                          $resource_type_integration = $paramsDecodedArray['resource_type'];
                          $ads_display_integration = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitestore.ad.$resource_type_integration", 3);

                          if (Zend_Controller_Front::getInstance()->getRequest()->getParam('resource_type', 0)) {
                            $resource_type_integration = Zend_Controller_Front::getInstance()->getRequest()->getParam('resource_type', 0);
                            $ads_display_integration = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitestore.ad.$resource_type_integration", 3);
                          }
                          //PACKAGE BASE AND MEMBER LEVEL SETTINGS PRIYACY START
                          if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                            if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", $resource_type_integration)) {
                              $flag = true;
                            }
                          } else {
                            $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, $resource_type_integration);
                            if (!empty($isStoreOwnerAllow)) {
                              $flag = true;
                            }
                          }
                          //PACKAGE BASE AND MEMBER LEVEL SETTINGS PRIYACY END
                        }
                        break;
                      case 'sitestorenote.profile-sitestorenotes':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorenote')) {
                          if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                            if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorenote") == 1) {
                              $flag = true;
                            }
                          } else {
                            $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'sncreate');
                            if (!empty($isStoreOwnerAllow)) {
                              $flag = true;
                            }
                          }
                          //TOTAL NOTES
                          $noteCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitestorenote', 'notes');
                          $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'sncreate');
                          if (!empty($isManageAdmin) || !empty($noteCount)) {
                            $flag = true;
                          } else {
                            $flag = false;
                          }
                        }
                        break;
                      case 'sitestoreevent.profile-sitestoreevents':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent')) {
                          if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                            if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreevent") == 1) {
                              $flag = true;
                            }
                          } else {
                            $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'secreate');
                            if (!empty($isStoreOwnerAllow)) {
                              $flag = true;
                            }
                          }
                          //TOTAL EVENTS
                          $eventCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitestoreevent', 'events');
                          $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'secreate');
                          if (!empty($isManageAdmin) || !empty($eventCount)) {
                            $flag = true;
                          } else {
                            $flag = false;
                          }
                        }
                        break;
                        case 'document.contenttype-documents':
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')) {
              if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoredocument") == 1) {
                  $flag = true;
                }
              } else {
                $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'sdcreate');
                if (!empty($isStoreOwnerAllow)) {
                  $flag = true;
                }
              }
              $documentCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'document', 'documents');
              $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'sdcreate');
              if (!empty($isManageAdmin) || !empty($documentCount)) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;
			case 'sitevideo.contenttype-videos':
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')) {
              if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorevideo") == 1) {
                  $flag = true;
                }
              } else {
                $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'svcreate');
                if (!empty($isStoreOwnerAllow)) {
                  $flag = true;
                }
              }
              //TOTAL EVENTS
							$videoCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitevideo', 'videos');
              $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'svcreate');
              if (!empty($isManageAdmin) || !empty($videoCount)) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;									case 'siteevent.contenttype-events':
													if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
														if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
															if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreevent") == 1) {
																$flag = true;
															}
														} else {
															$isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'secreate');
															if (!empty($isStoreOwnerAllow)) {
																$flag = true;
															}
														}
														//TOTAL EVENTS
														
														$isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'secreate');
														if (!empty($isManageAdmin)) {
															$flag = true;
														} else {
															$flag = false;
														}
													}
													break;
                      case 'sitestore.discussion-sitestore':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion')) {
                          if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                            if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorediscussion") == 1) {
                              $flag = true;
                            }
                          } else {
                            $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'sdicreate');
                            if (!empty($isStoreOwnerAllow)) {
                              $flag = true;
                            }
                          }
                          //TOTAL TOPICS
                          $topicCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitestore', 'topics');
                          $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'sdicreate');
                          if (!empty($isManageAdmin) || !empty($topicCount)) {
                            $flag = true;
                          } else {
                            $flag = false;
                          }
                        }
                        break;
                      case 'sitestore.photos-sitestore':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')) {
                          if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                            if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorealbum") == 1) {
                              $flag = true;
                            }
                          } else {
                            $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'spcreate');
                            if (!empty($isStoreOwnerAllow)) {
                              $flag = true;
                            }
                          }
                          //TOTAL ALBUMS
                          $albumCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitestore', 'albums');
                          $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'spcreate');
                          if (!empty($isManageAdmin) || !empty($albumCount)) {
                            $flag = true;
                          } else {
                            $flag = false;
                          }
                        }
                        break;
                      case 'sitestoremusic.profile-sitestoremusic':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic')) {
                          if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                            if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoremusic") == 1) {
                              $flag = true;
                            }
                          } else {
                            $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'smcreate');
                            if (!empty($isStoreOwnerAllow)) {
                              $flag = true;
                            }
                          }
                          //TOTAL PLAYLISTS
                          $musicCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitestoremusic', 'playlists');
                          $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'smcreate');
                          if (!empty($isManageAdmin) || !empty($musicCount)) {
                            $flag = true;
                          } else {
                            $flag = false;
                          }
                        }
                        break;
                        
                      case 'sitestoremember.profile-sitestoremembers':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
                          if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                            if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoremember") == 1) {
                              $flag = true;
                            }
                          } else {
                            $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'smecreate');
                            if (!empty($isStoreOwnerAllow)) {
                              $flag = true;
                            }
                          }
                          //TOTAL PLAYLISTS
                          $memberCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitestore', 'membership');
                          $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'smecreate');
                          if (!empty($isManageAdmin) || !empty($memberCount)) {
                            $flag = true;
                          } else {
                            $flag = false;
                          }
                        }
                        break;
                        
                      case 'sitestoredocument.profile-sitestoredocuments':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument')) {
                          if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                            if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoredocument") == 1) {
                              $flag = true;
                            }
                          } else {
                            $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'sdcreate');
                            if (!empty($isStoreOwnerAllow)) {
                              $flag = true;
                            }
                          }
                          //TOTAL DOCUMENTS
                          $documentCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitestoredocument', 'documents');
                          $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'sdcreate');
                          if (!empty($isManageAdmin) || !empty($documentCount)) {
                            $flag = true;
                          } else {
                            $flag = false;
                          }
                        }
                        break;
                      case 'sitestorereview.profile-sitestorereviews':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
                          //TOTAL REVIEW
                          $reviewCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitestorereview', 'reviews');
                          $level_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestorereview_review', 'create');
                          if (!empty($level_allow) || !empty($reviewCount)) {
                            $flag = true;
                          } else {
                            $flag = false;
                          }
                        }
                        break;
                      case 'sitestorevideo.profile-sitestorevideos':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo')) {
                          if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                            if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorevideo") == 1) {
                              $flag = true;
                            }
                          } else {
                            $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'svcreate');
                            if (!empty($isStoreOwnerAllow)) {
                              $flag = true;
                            }
                          }
                          //TOTAL VIDEO
                          $videoCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitestorevideo', 'videos');
                          $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'svcreate');
                          if (!empty($isManageAdmin) || !empty($videoCount)) {
                            $flag = true;
                          } else {
                            $flag = false;
                          }
                        }
                        break;
                      case 'sitestorepoll.profile-sitestorepolls':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorepoll')) {
                          if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                            if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorepoll") == 1) {
                              $flag = true;
                            }
                          } else {
                            $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'splcreate');
                            if (!empty($isStoreOwnerAllow)) {
                              $flag = true;
                            }
                          }
                          //TOTAL POLL
                          $pollCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitestorepoll', 'polls');
                          $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'splcreate');
                          if (!empty($isManageAdmin) || !empty($pollCount)) {
                            $flag = true;
                          } else {
                            $flag = false;
                          }
                        }
                        break;
                      case 'sitestoreoffer.profile-sitestoreoffers':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer')) {
                          if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                            if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreoffer") == 1) {
                              $flag = true;
                            }
                          } else {
                            $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'offer');
                            if (!empty($isStoreOwnerAllow)) {
                              $flag = true;
                            }
                          }
                          //TOTAL OFFERS
                          $can_edit = 1;
                          $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
                          if (empty($isManageAdmin)) {
                            $can_edit = 0;
                          }

                          $can_offer = 1;
                          $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'offer');

                          if (empty($isManageAdmin)) {
                            $can_offer = 0;
                          }

                          $can_create_offer = '';

                          //OFFER CREATION AUTHENTICATION CHECK
                          if ($can_edit == 1 && $can_offer == 1) {
                            $can_create_offer = 1;
                          }

                          //TOTAL OFFER
                          $offerCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitestoreoffer', 'offers');
                          if (!empty($can_create_offer) || !empty($offerCount)) {
                            $flag = true;
                          } else {
                            $flag = false;
                          }
                        }
                        break;
                      case 'sitestoreform.sitestore-viewform':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreform')) {
                          if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                            if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreform") == 1) {
                              $flag = true;
                            }
                          } else {
                            $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'form');
                            if (!empty($isStoreOwnerAllow)) {
                              $flag = true;
                            }
                          }
                        }
                        break;
											case 'sitestoretwitter.feeds-sitestoretwitter':
												if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoretwitter')) {
													$isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'twitter');
													if (!empty($isManageAdmin)) {
														$flag = true;
													}
												}
												break;
										case 'sitestoreproduct.store-profile-products':

											if (empty($sitestore->approved) || !empty($sitestore->closed) || empty($sitestore->search) || empty($sitestore->draft) || !empty($sitestore->declined)) {
												$flag = false;
											} else {
												$flag = true;
											}
				
										//PACKAGE BASE PRIYACY START
											if (!Engine_Api::_()->sitestore()->hasPackageEnable() && !empty($flag)) {
												$canStoreCreate = Engine_Api::_()->sitestoreproduct()->getLevelSettings("allow_store_create");
												if (empty($canStoreCreate)) {
													$flag = false;
												} else {
													$flag = true;
												}
												} 
												break;
											}
                    if (!empty($flag)) {
                      $content_ids = $value['content_id'];
                      $content_names = $value['name'];
                      break;
                    }
                  }
                }

                $params = array('content_id' => $content_ids, 'content_name' => $content_names);

                $tempcontent_name = $params['content_name'];
                $tempcontent_id = $params['content_id'];

                $newtabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', $tempcontent_id);

                 //////////////////////
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
									$select = $tableCore->select();
									$select_member = $select
																	->from($tableCore->info('name'), array('params'))
																	->where('content_id = ?', $newtabid)
																	->where('type = ?', 'widget')
																	->where('name = ?', 'sitestoremember.profile-sitestoremembers');
									$member_params = $select_member->query()->fetchColumn();
									if (!empty($member_params)) {
										$photoParamsDecodedArray = Zend_Json_Decoder::decode($member_params); 
										if (isset($photoParamsDecodedArray['show_option']) && !empty($photoParamsDecodedArray)) {
											$show_option = $photoParamsDecodedArray['show_option'];
										} else {
									   	$show_option = 1;
										}
										if (isset($photoParamsDecodedArray['membercategory_id']) && !empty($photoParamsDecodedArray)) {
											$membercategory_id = $photoParamsDecodedArray['membercategory_id'];
											$membercategory_ids =  json_encode($membercategory_id);
										}
									}
								}
								//////////
                
                $tab_main = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
                if ($tab_main) {
                  $current_tab_name = $tableCore->select()
                          ->from($tableCore->info('name'), array('name'))
                          ->where('content_id = ?', $tab_main)
                          ->query()
                          ->fetchColumn();
                  $tempcontent_name = $current_tab_name;
                  $tempcontent_id = $tab_main;
                  $select_offer = Engine_Api::_()->getDbtable('content', 'core')->select()
                            ->from(Engine_Api::_()->getDbtable('content', 'core')->info('name'), array('params'))
                            ->where('content_id = ?', $tempcontent_id)
                            ->where('type = ?', 'widget')
                            ->where('name = ?', 'sitestoreoffer.profile-sitestoreoffers'); 
              $storeoffers_params = $select_offer->query()->fetchColumn();
              $statistics = array("startdate", "enddate", "couponcode", 'discount', 'claim' ,'expire');
              if (!empty($storeoffers_params)) {
                $storeParamsDecodedArray = Zend_Json_Decoder::decode($storeoffers_params); 
                if(isset($storeParamsDecodedArray['statistics'])){
                    $statistics = $storeParamsDecodedArray['statistics']; 
                }
              } 
                }
                
                
              }
            }
          }

          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && !empty($contentinfo)) {
            $store_communityads = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1);
          } else {
            $store_communityads = 0;
          }

          $siteinfo = $view->layout()->siteinfo;
          if (!empty($sitestore)) {
            if ($sitestore->category_id) {
              $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($sitestore->category_id);
              if (!empty($row->category_name)) {
                $category_name = $row->category_name;
                $siteinfo['keywords'] = $category_name;
              }
              $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($sitestore->subcategory_id);
              if (!empty($row->category_name)) {
                $subcategory_name = $row->category_name;
                $siteinfo['keywords'] .= ',' . $subcategory_name;
              }
            }
            if (!empty($sitestore->location)) {
              $siteinfo['keywords'] .= ',' . $sitestore->location;
            }
          }
          $script = null;
          $view->layout()->siteinfo = $siteinfo;
          $is_ajax = Zend_Controller_Front::getInstance()->getRequest()->getParam('isajax', null);
          if (empty($is_ajax)) {
            $routeStartS = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manifestUrlS', "store");
            $store_url_integration = $routeStartS . '/' . Engine_Api::_()->sitestore()->getStoreUrl($sitestore->store_id);
            
            //echo $statistics;die;
            $overview = $view->translate('Overview');
            $form = $view->translate('Form');
            $review = $view->translate('Reviews');
            $document = $view->translate('Documents');
            $offer = $view->translate('Offers');
            $poll = $view->translate('Polls');
            $event = $view->translate('Events');
            $note = $view->translate('Notes');
            $photo = $view->translate('Photos');
            $discussion = $view->translate('Discussions');
            $map = $view->translate('Map');
            $link = $view->translate('Links');
            $video = $view->translate('Videos');
            $music = $view->translate('Music');
            $member = $view->translate('Member');
            $store_url = Engine_Api::_()->sitestore()->getStoreUrl($sitestore->store_id);
            $script = <<<EOF
      var sitestore_layout_setting = '$sitestore_layout_setting';
	    var store_communityads = '$store_communityads';
	    var contentinformation = '$contentinformation';
	    var store_showtitle = 0;
      var store_hide_left_container = '$sitestore_hide_left_container';
			var sitestore_slding_effect = '$sitestore_slding_effect';
	    var prev_tab_class = '';
	    if(contentinformation == 0) {
	      store_showtitle = 1;
	    }
      window.addEvent('domready', function() {
	    	if($('main_tabs')) {
					switch ("$tempcontent_name") {
            case 'sitestore.photos-sitestore':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Photo, '$tempcontent_id', 'photo', 'sitestore', 'photos-sitestore', store_showtitle, 'null', photo_ads_display, store_communityad_integration, adwithoutpackage,$itemAlbumCount, $itemPhotoCount);
						  	if($('global_content').getElement('.layout_sitestore_photos_sitestore')) {
									hideLeftContainer (photo_ads_display, store_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitestore_photos_sitestore';
								store_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitestore_photos_sitestore')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitestore_photos_sitestore'));
                }
							}
            break;
            case 'sitestorevideo.profile-sitestorevideos':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Video, '$tempcontent_id', 'video', 'sitestorevideo', 'profile-sitestorevideos', store_showtitle, 'null', video_ads_display, store_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitestorevideo_profile_sitestorevideos')) {
									hideLeftContainer (video_ads_display, store_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitestorevideo_profile_sitestorevideos';
								store_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitestorevideo_profile_sitestorevideos')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitestorevideo_profile_sitestorevideos'));
                }
							}
            break;
            case 'sitestorenote.profile-sitestorenotes':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Note, '$tempcontent_id', 'note', 'sitestorenote', 'profile-sitestorenotes', store_showtitle, 'null', note_ads_display, store_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitestorenote_profile_sitestorenotes')) {
									hideLeftContainer (note_ads_display, store_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitestorenote_profile_sitestorenotes';
								store_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitestorenote_profile_sitestorenotes')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitestorenote_profile_sitestorenotes'));
                }
							}
            break;
            case 'sitestorereview.profile-sitestorereviews':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Review, '$tempcontent_id', 'review', 'sitestorereview', 'profile-sitestorereviews', store_showtitle,'$store_url', review_ads_display, store_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitestorereview_profile_sitestorereviews')) {
									hideLeftContainer (review_ads_display, store_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitestorereview_profile_sitestorereviews';
								store_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitestorereview_profile_sitestorereviews')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitestorereview_profile_sitestorereviews'));
                }
							}
            break;
            case 'sitestoreform.sitestore-viewform':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Form, '$tempcontent_id', 'form', 'sitestoreform', 'sitestore-viewform', store_showtitle, '$store_url', form_ads_display, store_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitestoreform_sitestore_viewform')) {
									hideLeftContainer (form_ads_display, store_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitestoreform_sitestore_viewform';
								store_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitestoreform_sitestore_viewform')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitestoreform_sitestore_viewform'));
                }
							}
            break;
            case 'sitestoredocument.profile-sitestoredocuments':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Document, '$tempcontent_id', 'document', 'sitestoredocument', 'profile-sitestoredocuments', store_showtitle, 'null', document_ads_display, store_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitestoredocument_profile_sitestoredocuments')) {
									hideLeftContainer (document_ads_display, store_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitestoredocument_profile_sitestoredocuments';
								store_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitestoredocument_profile_sitestoredocuments')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitestoredocument_profile_sitestoredocuments'));
                }
							}
            break;
            case 'sitestoreevent.profile-sitestoreevents':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Event, '$tempcontent_id', 'event', 'sitestoreevent', 'profile-sitestoreevents', store_showtitle,'null', event_ads_display, store_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitestoreevent_profile_sitestoreevents')) {
									hideLeftContainer (event_ads_display, store_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitestoreevent_profile_sitestoreevents';            
								store_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitestoreevent_profile_sitestoreevents')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitestoreevent_profile_sitestoreevents'));
                }
							}
            break;
            case 'siteevent.contenttype-events':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Event, '$tempcontent_id', 'event', 'siteevent', 'contenttype-events', store_showtitle,'null', event_ads_display, store_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_siteevent_contenttype_events')) {
									hideLeftContainer (event_ads_display, store_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_siteevent_contenttype_events';            
								page_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_siteevent_contenttype_events')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_siteevent_contenttype_events'));
                }
							}
            break;
            case 'sitestorepoll.profile-sitestorepolls':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Poll, '$tempcontent_id', 'poll', 'sitestorepoll', 'profile-sitestorepolls', store_showtitle,'null', poll_ads_display, store_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitestorepoll_profile_sitestorepolls')) {
									hideLeftContainer (poll_ads_display, store_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitestorepoll_profile_sitestorepolls';            
								store_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitestorepoll_profile_sitestorepolls')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitestorepoll_profile_sitestorepolls'));
                }
							}
            break;
            case 'sitestoremusic.profile-sitestoremusic':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Music, '$tempcontent_id', 'music', 'sitestoremusic', 'profile-sitestoremusic', store_showtitle,'null', music_ads_display, store_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitestoremusic_profile_sitestoremusic')) {
									hideLeftContainer (music_ads_display, store_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitestoremusic_profile_sitestoremusic';            
								store_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitestoremusic_profile_sitestoremusic')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitestoremusic_profile_sitestoremusic'));
                }
							}
            break;
            
            case 'sitestoremember.profile-sitestoremembers':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Member, '$tempcontent_id', 'member', 'sitestoremember', 'profile-sitestoremembers', store_showtitle,'null', member_ads_display, store_communityad_integration,adwithoutpackage, 'null', 'null', 'null', 'null', 'null', 'null', 'null','$show_option', '$membercategory_ids', '1');
						  	if($('global_content').getElement('.layout_sitestoremember_profile_sitestoremember')) {
									hideLeftContainer (member_ads_display, store_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitestoremember_profile_sitestoremember';            
								store_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitestoremember_profile_sitestoremember')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitestoremember_profile_sitestoremember'));
                }
							}
            break;
            
            case 'sitestoreoffer.profile-sitestoreoffers':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Offer, '$tempcontent_id', 'offer', 'sitestoreoffer', 'profile-sitestoreoffers', store_showtitle,'null', offer_ads_display, store_communityad_integration,adwithoutpackage, null, null, null, null, null, null, null,'$statistics');
						  	if($('global_content').getElement('.layout_sitestoreoffer_profile_sitestoreoffers')) {
									hideLeftContainer (offer_ads_display, store_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitestoreoffer_profile_sitestoreoffers';
								store_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitestoreoffer_profile_sitestoreoffers')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitestoreoffer_profile_sitestoreoffers'));
                }
							}
            break;
            case 'sitestore.discussion-sitestore':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Discusssion, '$tempcontent_id', 'discussion', 'sitestore', 'discussion-sitestore', store_showtitle, 'null', discussion_ads_display, store_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitestore_discussion_sitestore')) {
									hideLeftContainer (discussion_ads_display, store_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitestore_discussion_sitestore';
                store_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitestore_discussion_sitestore')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitestore_discussion_sitestore'));
                }
							}
            break;
            case 'sitestore.overview-sitestore':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                hideLeftContainer (overview_ads_display, store_communityad_integration, adwithoutpackage);
                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitestore_overview_sitestore';
         
                store_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitestore_overview_sitestore')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitestore_overview_sitestore'));
                }
							}
            break;
            case 'core.profile-links':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {

                if($('main_tabs').getElement('.tab_layout_core_profile_links')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_core_profile_links'));
                }
								store_showtitle = 0;
                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_core_profile_links';
							}
            break;
            case 'sitestore.location-sitestore':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
      					hideLeftContainer (location_ads_display, store_communityad_integration, adwithoutpackage);

                if($('main_tabs').getElement('.tab_layout_sitestore_location_sitestore')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitestore_location_sitestore'));
                }
								store_showtitle = 0;
                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitestore_location_sitestore';
							}
            break;
            case 'sitestore.info-sitestore':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
								store_showtitle = 0;
                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitestore_info_sitestore';
                if($('main_tabs').getElement('.tab_layout_sitestore_info_sitestore')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitestore_info_sitestore'));
                }
							}
            break;
          case 'sitestoreintegration.profile-items':       
             if(is_ajax_divhide == '' && "$tab_main" == '') {
               if($newtabid == "$tempcontent_id" && $newtabid != 0) {                
                ShowContent('$tempcontent_id', execute_Request_$resource_type_integration, '$tempcontent_id', 'null', 'sitestoreintegration', 'profile-items', store_showtitle, '$store_url_integration', $ads_display_integration, store_communityad_integration,
  adwithoutpackage, null,null,'$resource_type_integration', null, 1);
                  prev_tab_id = "$newtabid";
                  prev_tab_class = 'layout_sitestoreintegration_profile_items';
                  if($('global_content').getElement('.layout_sitestoreintegration_profile_items')) {
                    $('global_content').getElement('.layout_sitestoreintegration_profile_items').style.display = 'block';
                  }
               }
             }
            break;
            case 'sitestoretwitter.feeds-sitestoretwitter':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
								store_showtitle = 0;
                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitestoretwitter_feeds_sitestoretwitter';
                if($('main_tabs').getElement('.tab_layout_sitestoretwitter_feeds_sitestoretwitter')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitestoretwitter_feeds_sitestoretwitter'));
                }
							}
            break;  
            case 'core.html-block':
              tabContainerSwitch($('main_tabs').getElement('.tab_layout_core_html_block'));
              break;
            case 'activity.feed':
              tabContainerSwitch($('main_tabs').getElement('.tab_layout_activity_feed'));
              break;
						case 'sitestoreproduct.store-profile-products': 
              tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitestoreproduct_store_profile_products'));
              break;
            case 'seaocore.feed':
              tabContainerSwitch($('main_tabs').getElement('.tab_layout_seaocore_feed'));
              break;
            case 'advancedactivity.home-feeds':
              tabContainerSwitch($('main_tabs').getElement('.tab_layout_advancedactivity_home_feeds'));
              break;
					 }
			    if($('main_tabs').getElement('.tab_$tab_id')){
			      $('main_tabs').getElement('.tab_$tab_id').addEvent('click', function() {
							if($('profile_status')) {
								$('profile_status').innerHTML = "<h2>$sitestore_title</h2>";
							}
							if($('main_tabs').getElement('.tab_layout_activity_feed')) {
								tabContainerSwitch($('main_tabs').getElement('.tab_layout_activity_feed'));
							}
					
							prev_tab_id = '$tab_id';
							setLeftLayoutForStore();
							if($('global_content').getElement('.layout_seaocore_feed')) {
								$('global_content').getElement('.layout_seaocore_feed').id = "layout_seaocore_feed";
								scrollToTopForStore($("global_content").getElement(".tab_layout_seaocore_feed"));
							} else if($('global_content').getElement('.layout_activity_feed')) {
								$('global_content').getElement('.layout_activity_feed').id = "layout_activity_feed";
								scrollToTopForStore($("global_content").getElement(".tab_layout_activity_feed"));
							} else if($('global_content').getElement('.layout_advancedactivity_home_feeds')) {
								$('global_content').getElement('.layout_advancedactivity_home_feeds').id = "layout_advancedactivity_home_feeds";
								scrollToTopForStore($("global_content").getElement(".tab_layout_advancedactivity_home_feeds"));
							}
						});
				  }      
  
    
          
          if($('main_tabs').getElement('.tab_$tab_id3')){
			      $('main_tabs').getElement('.tab_$tab_id3').addEvent('click', function() {
            setLeftLayoutForStore();
            if($('global_content').getElement('.layout_seaocore_feed')) {
							$('global_content').getElement('.layout_seaocore_feed').id = "layout_seaocore_feed";
							scrollToTopForStore($("global_content").getElement(".tab_layout_seaocore_feed"));
            } else if($('global_content').getElement('.layout_activity_feed')) {
							$('global_content').getElement('.layout_activity_feed').id = "layout_activity_feed";
							scrollToTopForStore($("global_content").getElement(".tab_layout_activity_feed"));
            } else if($('global_content').getElement('.layout_advancedactivity_home_feeds')) {
							$('global_content').getElement('.layout_advancedactivity_home_feeds').id = "layout_advancedactivity_home_feeds";
							scrollToTopForStore($("global_content").getElement(".tab_layout_advancedactivity_home_feeds"));
            }

            if($('main_tabs').getElement('.tab_layout_core_profile_links')) {
              tabContainerSwitch($('main_tabs').getElement('.tab_layout_core_profile_links'));
            }

			      prev_tab_id = '$tab_id3';
						
           
            });
				  }
          if($('main_tabs').getElement('.tab_$tab_id4')){
			      $('main_tabs').getElement('.tab_$tab_id4').addEvent('click', function() {
//             if($('main_tabs').getElement('.tab_layout_core_html_block')) {
//               tabContainerSwitch($('main_tabs').getElement('.tab_layout_core_html_block'));
//             }

			      prev_tab_id = '$tab_id4';
						setLeftLayoutForStore();
            });
				  }
				}
				else
	      {          
	       switch ("$tempcontent_name") {
            case 'sitestore.photos-sitestore':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitestore_title &raquo; $photo </h2>";
								}
								$('global_content').getElement('.layout_sitestore_photos_sitestore > h3').innerHTML = "<div class='layout_simple_head'>$sitestore_title's  $photo</div>";
		            ShowContent('$tempcontent_id', execute_Request_Photo, '$tempcontent_id', 'photo', 'sitestore', 'photos-sitestore', store_showtitle, 'null', photo_ads_display, store_communityad_integration, adwithoutpackage,$itemAlbumCount, $itemPhotoCount);
						  	if($('global_content').getElement('.layout_sitestore_photos_sitestore')) {
									$('global_content').getElement('.layout_sitestore_photos_sitestore').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitestore_photos_sitestore';
							  }
							  hideWidgets();
							}
            break;
            case 'sitestorevideo.profile-sitestorevideos':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitestore_title &raquo; $video </h2>";
								}
								$('global_content').getElement('.layout_sitestorevideo_profile_sitestorevideos > h3').innerHTML = "<div class='layout_simple_head'>$sitestore_title's  $video</div>";
		            ShowContent('$tempcontent_id', execute_Request_Video, '$tempcontent_id', 'video', 'sitestorevideo', 'profile-sitestorevideos', store_showtitle, 'null', video_ads_display, store_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitestorevideo_profile_sitestorevideos')) {
									$('global_content').getElement('.layout_sitestorevideo_profile_sitestorevideos').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitestorevideo_profile_sitestorevideos';
							  }
								hideWidgets();
							}
            break;
            case 'sitestorenote.profile-sitestorenotes':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitestore_title &raquo; $note </h2>";
								}
								$('global_content').getElement('.layout_sitestorenote_profile_sitestorenotes > h3').innerHTML = "<div class='layout_simple_head'>$sitestore_title's  $note</div>";
		            ShowContent('$tempcontent_id', execute_Request_Note, '$tempcontent_id', 'note', 'sitestorenote', 'profile-sitestorenotes', store_showtitle, 'null', note_ads_display, store_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitestorenote_profile_sitestorenotes')) {
									$('global_content').getElement('.layout_sitestorenote_profile_sitestorenotes').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitestorenote_profile_sitestorenotes';
							  }
								hideWidgets();
							}
            break;
            case 'sitestorereview.profile-sitestorereviews':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitestore_title &raquo; $review </h2>";
								}
								$('global_content').getElement('.layout_sitestorereview_profile_sitestorereviews > h3').innerHTML = "<div class='layout_simple_head'>$sitestore_title's  $review </div>";
		            ShowContent('$tempcontent_id', execute_Request_Review, '$tempcontent_id', 'review', 'sitestorereview', 'profile-sitestorereviews', store_showtitle,'$store_url', review_ads_display, store_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitestorereview_profile_sitestorereviews')) {
									$('global_content').getElement('.layout_sitestorereview_profile_sitestorereviews').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitestorereview_profile_sitestorereviews';
							  }
								hideWidgets();
							}
            break;
            case 'sitestoreform.sitestore-viewform':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitestore_title &raquo; $form </h2>";
								}
								$('global_content').getElement('.layout_sitestoreform_sitestore_viewform > h3').innerHTML = "<div class='layout_simple_head'>$sitestore_title's  $form </div>";
		            ShowContent('$tempcontent_id', execute_Request_Form, '$tempcontent_id', 'form', 'sitestoreform', 'sitestore-viewform', store_showtitle, '$store_url', form_ads_display, store_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitestoreform_sitestore_viewform')) {
									$('global_content').getElement('.layout_sitestoreform_sitestore_viewform').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitestoreform_sitestore_viewform';
							  }
								hideWidgets();
							}
            break;
            case 'sitestoredocument.profile-sitestoredocuments':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitestore_title &raquo; $document </h2>";
								}
								$('global_content').getElement('.layout_sitestoredocument_profile_sitestoredocuments > h3').innerHTML = "<div class='layout_simple_head'>$sitestore_title's  $document </div>";
		            ShowContent('$tempcontent_id', execute_Request_Document, '$tempcontent_id', 'document', 'sitestoredocument', 'profile-sitestoredocuments', store_showtitle, 'null', document_ads_display, store_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitestoredocument_profile_sitestoredocuments')) {
									$('global_content').getElement('.layout_sitestoredocument_profile_sitestoredocuments').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitestoredocument_profile_sitestoredocuments';
							  }
								hideWidgets();
							}
            break;
            case 'sitestoreevent.profile-sitestoreevents':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitestore_title &raquo; $event </h2>";
								}
								$('global_content').getElement('.layout_sitestoreevent_profile_sitestoreevents > h3').innerHTML = "<div class='layout_simple_head'>$sitestore_title's  $event </div>";
		            ShowContent('$tempcontent_id', execute_Request_Event, '$tempcontent_id', 'event', 'sitestoreevent', 'profile-sitestoreevents', store_showtitle,'null', event_ads_display, store_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitestoreevent_profile_sitestoreevents')) {
									$('global_content').getElement('.layout_sitestoreevent_profile_sitestoreevents').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitestoreevent_profile_sitestoreevents';
							  }
								hideWidgets();
							}
            break;
            case 'siteevent.contenttype-events':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitestore_title &raquo; $event </h2>";
								}
								$('global_content').getElement('.layout_siteevent_contenttype_events > h3').innerHTML = "<div class='layout_simple_head'>$sitestore_title's  $event </div>";
		            ShowContent('$tempcontent_id', execute_Request_Event, '$tempcontent_id', 'event', 'siteevent', 'contenttype-events', store_showtitle,'null', event_ads_display, store_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_siteevent_contenttype_events')) {
									$('global_content').getElement('.layout_siteevent_contenttype_events').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_siteevent_contenttype_events';
							  }
								hideWidgets();
							}
            break;
            case 'sitestorepoll.profile-sitestorepolls':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitestore_title &raquo; $poll </h2>";
								}
								$('global_content').getElement('.layout_sitestorepoll_profile_sitestorepolls > h3').innerHTML = "<div class='layout_simple_head'>$sitestore_title's  $poll </div>";
		            ShowContent('$tempcontent_id', execute_Request_Poll, '$tempcontent_id', 'poll', 'sitestorepoll', 'profile-sitestorepolls', store_showtitle,'null', poll_ads_display, store_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitestorepoll_profile_sitestorepolls')) {
									$('global_content').getElement('.layout_sitestorepoll_profile_sitestorepolls').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitestorepoll_profile_sitestorepolls';
							  }
								hideWidgets();
							}
            break;
            case 'sitestoremusic.profile-sitestoremusic':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitestore_title &raquo; $music </h2>";
								}
								$('global_content').getElement('.layout_sitestoremusic_profile_sitestoremusic > h3').innerHTML = "<div class='layout_simple_head'>$sitestore_title's  $music </div>";
		            ShowContent('$tempcontent_id', execute_Request_Music, '$tempcontent_id', 'music', 'sitestoremusic', 'profile-sitestoremusic', store_showtitle,'null', music_ads_display, store_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitestoremusic_profile_sitestoremusic')) {
									$('global_content').getElement('.layout_sitestoremusic_profile_sitestoremusic').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitestoremusic_profile_sitestoremusic';
							  }
								hideWidgets();
							}
            break;
            
            case 'sitestoremember.profile-sitestoremembers':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitestore_title &raquo; $member </h2>";
								}
								$('global_content').getElement('.layout_sitestoremember_profile_sitestoremember > h3').innerHTML = "<div class='layout_simple_head'>$sitestore_title's  $member </div>";
		            ShowContent('$tempcontent_id', execute_Request_Member, '$tempcontent_id', 'member', 'sitestoremember', 'profile-sitestoremembers', store_showtitle,'null', member_ads_display, store_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitestoremember_profile_sitestoremember')) {
									$('global_content').getElement('.layout_sitestoremember_profile_sitestoremember').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitestoremember_profile_sitestoremember';
							  }
								hideWidgets();
							}
            break;
            
            
            case 'sitestoreoffer.profile-sitestoreoffers':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitestore_title &raquo; $offer </h2>";
								}
								$('global_content').getElement('.layout_sitestoreoffer_profile_sitestoreoffers > h3').innerHTML = "<div class='layout_simple_head'>$sitestore_title's  $offer </div>";
		            ShowContent('$tempcontent_id', execute_Request_Offer, '$tempcontent_id', 'offer', 'sitestoreoffer', 'profile-sitestoreoffers', store_showtitle,'null', offer_ads_display, store_communityad_integration,adwithoutpackage);
						  	if($('global_content').getElement('.layout_sitestoreoffer_profile_sitestoreoffers')) {
									$('global_content').getElement('.layout_sitestoreoffer_profile_sitestoreoffers').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitestoreoffer_profile_sitestoreoffers';
							  }
								hideWidgets();
							}
            break;
            case 'sitestore.discussion-sitestore':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Discusssion, '$tempcontent_id', 'discussion', 'sitestore', 'discussion-sitestore', store_showtitle, 'null', discussion_ads_display, store_communityad_integration,adwithoutpackage);
		            if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitestore_title &raquo; $discussion </h2>";
								}
								$('global_content').getElement('.layout_sitestore_discussion_sitestore > h3').innerHTML = "<div class='layout_simple_head'>$sitestore_title's  $discussion </div>";
						  	if($('global_content').getElement('.layout_sitestore_discussion_sitestore')) {
									$('global_content').getElement('.layout_sitestore_discussion_sitestore').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitestore_discussion_sitestore';
							  }
								hideWidgets();
							}
            break;
            case 'sitestore.overview-sitestore':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {

                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitestore_title &raquo; $overview </h2>";
								}
								$('global_content').getElement('.layout_sitestore_overview_sitestore > h3').innerHTML = "<div class='layout_simple_head'>$sitestore_title's  $overview</div>";

						    if($('global_content').getElement('.layout_sitestore_overview_sitestore')) {
									 $('global_content').getElement('.layout_sitestore_overview_sitestore').style.display = 'block';
									 prev_tab_id = "$newtabid";
									 prev_tab_class = 'layout_sitestore_overview_sitestore';
							  }
								hideWidgetsForModule('sitestoreoverview');
							}
            break;
            case 'core.profile-links':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitestore_title &raquo; $link </h2>";
								}
								$('global_content').getElement('.layout_core_profile_links > h3').innerHTML = "<div class='layout_simple_head'>$sitestore_title's  $link</div>";
                hideWidgetsForModule('sitestorelink');
							}
            break;
            case 'sitestore.location-sitestore':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitestore_title &raquo; $map </h2>";
								}
								$('global_content').getElement('.layout_sitestore_location_sitestore > h3').innerHTML = "<div class='layout_simple_head'>$sitestore_title's $map</div>";
						    if($('global_content').getElement('.layout_sitestore_location_sitestore')) {
									 $('global_content').getElement('.layout_sitestore_location_sitestore').style.display = 'block';
									 prev_tab_id = "$newtabid";
									 prev_tab_class = 'layout_sitestore_location_sitestore';
							  }
								hideWidgetsForModule('sitestorelocation');
							}
            break;
            case 'sitestoreintegration.profile-items':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {                
                ShowContent('$tempcontent_id', execute_Request_$resource_type_integration, '$tempcontent_id', 'null', 'sitestoreintegration', 'profile-items', store_showtitle, '$store_url_integration', $ads_display_integration, store_communityad_integration,
  adwithoutpackage, null,null,'$resource_type_integration', null, 1);
                  prev_tab_id = "$newtabid";
                  prev_tab_class = 'layout_sitestoreintegration_profile_items';
                  if($('global_content').getElement('.layout_sitestoreintegration_profile_items')) {
                    $('global_content').getElement('.layout_sitestoreintegration_profile_items').style.display = 'block';
                  }
                 hideWidgetsForModule('sitestoreintegration');
               }
            break;          
            case 'activity.feed':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
						    hideWidgetsForModule('sitestoreactivityfeed');

								if($('global_content').getElement('.layout_sitestore_store_cover_information_sitestore')) { 	                            $('global_content').getElement('.layout_sitestore_store_cover_information_sitestore').style.display = 'block';
								}
		
		if($('global_content').getElement('.layout_sitecontentcoverphoto_content_cover_photo')) { 	                            				  $('global_content').getElement('.layout_sitecontentcoverphoto_content_cover_photo').style.display = 'block';
		}
								if($('global_content').getElement('.layout_sitestoremember_storecover_photo_sitestoremembers')) { 	                            	$('global_content').getElement('.layout_sitestoremember_storecover_photo_sitestoremembers').style.display = 'block';
								}
							}
            break;
           case 'seaocore.feed':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                hideWidgetsForModule('sitestoreseaocoreactivityfeed');

								if($('global_content').getElement('.layout_sitestore_store_cover_information_sitestore')) { 	                            $('global_content').getElement('.layout_sitestore_store_cover_information_sitestore').style.display = 'block';
								}
				if($('global_content').getElement('.layout_sitecontentcoverphoto_content_cover_photo')) { 	                            				  $('global_content').getElement('.layout_sitecontentcoverphoto_content_cover_photo').style.display = 'block';
		}
								if($('global_content').getElement('.layout_sitestoremember_storecover_photo_sitestoremembers')) { 	                            	$('global_content').getElement('.layout_sitestoremember_storecover_photo_sitestoremembers').style.display = 'block';
								}
						  }
            break;
           case 'advancedactivity.home-feeds':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                hideWidgetsForModule('sitestoreadvancedactivityactivityfeed');

								if($('global_content').getElement('.layout_sitestore_store_cover_information_sitestore')) { 	                            $('global_content').getElement('.layout_sitestore_store_cover_information_sitestore').style.display = 'block';
								}
		if($('global_content').getElement('.layout_sitecontentcoverphoto_content_cover_photo')) { 	                            				  $('global_content').getElement('.layout_sitecontentcoverphoto_content_cover_photo').style.display = 'block';
		}		
								if($('global_content').getElement('.layout_sitestoremember_storecover_photo_sitestoremembers')) { 	                            	$('global_content').getElement('.layout_sitestoremember_storecover_photo_sitestoremembers').style.display = 'block';
								}
						  }
            break;            
            case 'sitestore.info-sitestore':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
						    hideWidgetsForModule('sitestoreinfo');

							}
            break;
            case 'sitestoretwitter.feeds-sitestoretwitter':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
						    hideWidgetsForModule('sitestoretwitter');

							}
            break;
					}
				  if($widgetinformation == 0) {
				    if($('global_content').getElement('.layout_sitestore_location_sitestore')) {
							$('global_content').getElement('.layout_sitestore_location_sitestore').style.display = 'block';
						}
						if($('global_content').getElement('.layout_sitestore_info_sitestore')) {
							$('global_content').getElement('.layout_sitestore_info_sitestore').style.display = 'block';
						}

						if($('global_content').getElement('.layout_core_profile_links')) {
							$('global_content').getElement('.layout_core_profile_links').style.display = 'block';
						}
						if($('global_content').getElement('.layout_sitestore_store_cover_information_sitestore')) { 	                            $('global_content').getElement('.layout_sitestore_store_cover_information_sitestore').style.display = 'block';
					}
 		if($('global_content').getElement('.layout_sitecontentcoverphoto_content_cover_photo')) { 	                            				  $('global_content').getElement('.layout_sitecontentcoverphoto_content_cover_photo').style.display = 'block';
		}
						if($('global_content').getElement('.layout_sitestoremember_storecover_photo_sitestoremembers')) { 	                            $('global_content').getElement('.layout_sitestoremember_storecover_photo_sitestoremembers').style.display = 'block';
					}
					}

    			$$('.tab_$tab_id').addEvent('click', function() {
    			  if($('profile_status')) {
    			    $('profile_status').innerHTML = "<h2>$sitestore_title</h2>";
						}
						if($('global_content').getElement('.layout_sitestore_location_sitestore')) {
							$('global_content').getElement('.layout_sitestore_location_sitestore').style.display = 'none';
						}
            if($('global_content').getElement('.layout_sitestore_photos_sitestore')) {
					    $('global_content').getElement('.layout_sitestore_photos_sitestore').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitestorevideo_profile_sitestorevideos')) {
					    $('global_content').getElement('.layout_sitestorevideo_profile_sitestorevideos').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitestore_discussion_sitestore')) {
					    $('global_content').getElement('.layout_sitestore_discussion_sitestore').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitestoreoffer_profile_sitestoreoffers')) {
					    $('global_content').getElement('.layout_sitestoreoffer_profile_sitestoreoffers').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitestoredocument_profile_sitestoredocuments')) {
					    $('global_content').getElement('.layout_sitestoredocument_profile_sitestoredocuments').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitestorereview_profile_sitestorereviews')) {
					    $('global_content').getElement('.layout_sitestorereview_profile_sitestorereviews').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitestorepoll_profile_sitestorepolls')) {
					    $('global_content').getElement('.layout_sitestorepoll_profile_sitestorepolls').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitestorenote_profile_sitestorenotes')) {
					    $('global_content').getElement('.layout_sitestorenote_profile_sitestorenotes').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitestoreevent_profile_sitestoreevents')) {
					    $('global_content').getElement('.layout_sitestoreevent_profile_sitestoreevents').style.display = 'none';
            }
            if($('global_content').getElement('.layout_siteevent_contenttype_events')) {
					    $('global_content').getElement('.layout_siteevent_contenttype_events').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitestoreintegration_profile_items')) {					    
              $$('.layout_sitestoreintegration_profile_items').setStyle('display', 'none');
            } 
            if($('global_content').getElement('.layout_sitestoreintegration_profile_items')) {
					    $('global_content').getElement('.layout_sitestoreintegration_profile_items').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitestoremusic_profile_sitestoremusic')) {
					    $('global_content').getElement('.layout_sitestoremusic_profile_sitestoremusic').style.display = 'none';
            }
      			if($('global_content').getElement('.layout_sitestoreform_sitestore_viewform')) {
						  $('global_content').getElement('.layout_sitestoreform_sitestore_viewform').style.display = 'none';
            }
						if($('global_content').getElement('.layout_core_profile_links')) {
							$('global_content').getElement('.layout_core_profile_links').style.display = 'none';
						}
						if($('global_content').getElement('.layout_sitestore_info_sitestore')) {
							$('global_content').getElement('.layout_sitestore_info_sitestore').style.display = 'none';
						}
// 						if($('global_content').getElement('.layout_sitestoretwitter_feeds_sitestoretwitter')) {
// 							$('global_content').getElement('.layout_sitestoretwitter_feeds_sitestoretwitter').style.display = 'none';
// 						}
						if($('global_content').getElement('.layout_sitestore_overview_sitestore')) {
							$('global_content').getElement('.layout_sitestore_overview_sitestore').style.display = 'none';
					  }
						if($('global_content').getElement('.layout_activity_feed')) {
							$('global_content').getElement('.layout_activity_feed').style.display = 'block';
						}
        	  if($('global_content').getElement('.layout_seaocore_feed')) {
							$('global_content').getElement('.layout_seaocore_feed').style.display = 'block';
						}
            if($('global_content').getElement('.layout_advancedactivity_home_feeds')) {
							$('global_content').getElement('.layout_advancedactivity_home_feeds').style.display = 'block';
						}

            if($('global_content').getElement('.layout_seaocore_feed')) {
							$('global_content').getElement('.layout_seaocore_feed').id = "layout_seaocore_feed";
							scrollToTopForStore($('layout_seaocore_feed'));
            } else if($('global_content').getElement('.layout_activity_feed')) {
							$('global_content').getElement('.layout_activity_feed').id = "layout_activity_feed";
							scrollToTopForStore($('layout_activity_feed'));
            } else if($('global_content').getElement('.layout_advancedactivity_home_feeds')) {
							$('global_content').getElement('.layout_advancedactivity_home_feeds').id = "layout_advancedactivity_home_feeds";
							scrollToTopForStore($('layout_advancedactivity_home_feeds'));
            }

						if($('global_content').getElement('.layout_sitestore_store_cover_information_sitestore')) { 	                            $('global_content').getElement('.layout_sitestore_store_cover_information_sitestore').style.display = 'block';
					}
 		if($('global_content').getElement('.layout_sitecontentcoverphoto_content_cover_photo')) { 	                            				  $('global_content').getElement('.layout_sitecontentcoverphoto_content_cover_photo').style.display = 'block';
		}
						if($('global_content').getElement('.layout_sitestoremember_storecover_photo_sitestoremembers')) { 	                            $('global_content').getElement('.layout_sitestoremember_storecover_photo_sitestoremembers').style.display = 'block';
					}

//				    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '$tab_id') {
//				      $('id_' + prev_tab_id).style.display = "none";
//				    }
			    prev_tab_id = '$tab_id';
				 });

    			$$('.tab_$tab_id5').addEvent('click', function() {
    			  if($('profile_status')) {
    			    $('profile_status').innerHTML = "<h2>$sitestore_title</h2>";
						}
						if($('global_content').getElement('.layout_sitestore_location_sitestore')) {
							$('global_content').getElement('.layout_sitestore_location_sitestore').style.display = 'none';
						}
            if($('global_content').getElement('.layout_sitestore_photos_sitestore')) {
					    $('global_content').getElement('.layout_sitestore_photos_sitestore').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitestorevideo_profile_sitestorevideos')) {
					    $('global_content').getElement('.layout_sitestorevideo_profile_sitestorevideos').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitestore_discussion_sitestore')) {
					    $('global_content').getElement('.layout_sitestore_discussion_sitestore').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitestoreoffer_profile_sitestoreoffers')) {
					    $('global_content').getElement('.layout_sitestoreoffer_profile_sitestoreoffers').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitestoredocument_profile_sitestoredocuments')) {
					    $('global_content').getElement('.layout_sitestoredocument_profile_sitestoredocuments').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitestorereview_profile_sitestorereviews')) {
					    $('global_content').getElement('.layout_sitestorereview_profile_sitestorereviews').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitestorepoll_profile_sitestorepolls')) {
					    $('global_content').getElement('.layout_sitestorepoll_profile_sitestorepolls').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitestorenote_profile_sitestorenotes')) {
					    $('global_content').getElement('.layout_sitestorenote_profile_sitestorenotes').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitestoreevent_profile_sitestoreevents')) {
					    $('global_content').getElement('.layout_sitestoreevent_profile_sitestoreevents').style.display = 'none';
            }
            if($('global_content').getElement('.layout_siteevent_contenttype_events')) {
					    $('global_content').getElement('.layout_siteevent_contenttype_events').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitestoreintegration_profile_items')) {					    
              $$('.layout_sitestoreintegration_profile_items').setStyle('display', 'none');
            } 
            if($('global_content').getElement('.layout_sitestoreintegration_profile_items')) {
					    $('global_content').getElement('.layout_sitestoreintegration_profile_items').style.display = 'none';
            }
            if($('global_content').getElement('.layout_sitestoremusic_profile_sitestoremusic')) {
					    $('global_content').getElement('.layout_sitestoremusic_profile_sitestoremusic').style.display = 'none';
            }
      			if($('global_content').getElement('.layout_sitestoreform_sitestore_viewform')) {
						  $('global_content').getElement('.layout_sitestoreform_sitestore_viewform').style.display = 'none';
            }
						if($('global_content').getElement('.layout_core_profile_links')) {
							$('global_content').getElement('.layout_core_profile_links').style.display = 'none';
						}
						if($('global_content').getElement('.layout_sitestore_info_sitestore')) {
							$('global_content').getElement('.layout_sitestore_info_sitestore').style.display = 'none';
						}
// 						if($('global_content').getElement('.layout_sitestoretwitter_feeds_sitestoretwitter')) {
// 							$('global_content').getElement('.layout_sitestoretwitter_feeds_sitestoretwitter').style.display = 'none';
// 						}
						if($('global_content').getElement('.layout_sitestore_overview_sitestore')) {
							$('global_content').getElement('.layout_sitestore_overview_sitestore').style.display = 'none';
					  }
						if($('global_content').getElement('.layout_activity_feed')) {
							$('global_content').getElement('.layout_activity_feed').style.display = 'block';
						}
        	  if($('global_content').getElement('.layout_seaocore_feed')) {
							$('global_content').getElement('.layout_seaocore_feed').style.display = 'block';
						}
            if($('global_content').getElement('.layout_advancedactivity_home_feeds')) {
							$('global_content').getElement('.layout_advancedactivity_home_feeds').style.display = 'block';
						}

            if($('global_content').getElement('.layout_seaocore_feed')) {
							$('global_content').getElement('.layout_seaocore_feed').id = "layout_seaocore_feed";
							scrollToTopForStore($('layout_seaocore_feed'));
            } else if($('global_content').getElement('.layout_activity_feed')) {
							$('global_content').getElement('.layout_activity_feed').id = "layout_activity_feed";
							scrollToTopForStore($('layout_activity_feed'));
            } else if($('global_content').getElement('.layout_advancedactivity_home_feeds')) {
							$('global_content').getElement('.layout_advancedactivity_home_feeds').id = "layout_advancedactivity_home_feeds";
							scrollToTopForStore($('layout_advancedactivity_home_feeds'));
            }

						if($('global_content').getElement('.layout_sitestore_store_cover_information_sitestore')) { 	                            $('global_content').getElement('.layout_sitestore_store_cover_information_sitestore').style.display = 'block';
					}
 		if($('global_content').getElement('.layout_sitecontentcoverphoto_content_cover_photo')) { 	                            				  $('global_content').getElement('.layout_sitecontentcoverphoto_content_cover_photo').style.display = 'block';
		}
						if($('global_content').getElement('.layout_sitestoremember_storecover_photo_sitestoremembers')) { 	                            $('global_content').getElement('.layout_sitestoremember_storecover_photo_sitestoremembers').style.display = 'block';
					}

//				    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '$tab_id') {
//				      $('id_' + prev_tab_id).style.display = "none";
//				    }
			    prev_tab_id = '$tab_id5';
				 });
  			$$('.tab_$tab_id3').addEvent('click', function() {
  			  if($('profile_status')) {
  			    $('profile_status').innerHTML = "<h2>$sitestore_title &raquo; $link </h2>";
					}
					$('global_content').getElement('.layout_core_profile_links > h3').innerHTML = "<div class='layout_simple_head'>$sitestore_title's $link</div>";

					if($('global_content').getElement('.layout_sitestore_location_sitestore')) {
						$('global_content').getElement('.layout_sitestore_location_sitestore').style.display = 'none';
					}
					if($('global_content').getElement('.layout_core_profile_links')) {
						$('global_content').getElement('.layout_core_profile_links').style.display = 'block';
					}
					if($('global_content').getElement('.layout_sitestore_info_sitestore')) {
						$('global_content').getElement('.layout_sitestore_info_sitestore').style.display = 'none';
					}

					if($('global_content').getElement('.layout_activity_feed')) {
						$('global_content').getElement('.layout_activity_feed').style.display = 'none';
					}
        	if($('global_content').getElement('.layout_seaocore_feed')) {
						$('global_content').getElement('.layout_seaocore_feed').style.display = 'none';
					}
          if($('global_content').getElement('.layout_advancedactivity_home_feeds')) {
					 	$('global_content').getElement('.layout_advancedactivity_home_feeds').style.display = 'none';
					}
					if($('global_content').getElement('.layout_sitestore_overview_sitestore')) {
						$('global_content').getElement('.layout_sitestore_overview_sitestore').style.display = 'none';
				  }
						if($('global_content').getElement('.layout_sitestore_store_cover_information_sitestore')) { 	                              $('global_content').getElement('.layout_sitestore_store_cover_information_sitestore').style.display = 'block';
					  }
 		if($('global_content').getElement('.layout_sitecontentcoverphoto_content_cover_photo')) { 	                            				  $('global_content').getElement('.layout_sitecontentcoverphoto_content_cover_photo').style.display = 'block';
		}
						if($('global_content').getElement('.layout_sitestoremember_storecover_photo_sitestoremembers')) { 	                             $('global_content').getElement('.layout_sitestoremember_storecover_photo_sitestoremembers').style.display = 'block';
					  }
// 			    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '$tab_id3') {
// 			      $('id_' + prev_tab_id).style.display = "none";
// 			    }
		    prev_tab_id = '$tab_id3';
		 	});        
     
          
     }
	 });
	 window.addEvent('domready', function() {

      if($('thumb_icon')) {
	      if($currenttabid == 0) {
	       $('thumb_icon').style.display = 'none';
			  }
	    }
		});
EOF;
          }
          if ("$tempcontent_name" == 'sitestore.discussion-sitestore' || "$tempcontent_name" == 'sitestore.photos-sitestore' || "$tempcontent_name" == 'sitestorevideo.profile-sitestorevideos' || "$tempcontent_name" == 'sitestorenote.profile-sitestorenotes' || "$tempcontent_name" == 'sitestorereview.profile-sitestorereviews' || "$tempcontent_name" == 'sitestoreform.sitestore-viewform' || "$tempcontent_name" == 'sitestoredocument.profile-sitestoredocuments' || "$tempcontent_name" == 'sitestoreevent.profile-sitestoreevents' || "$tempcontent_name" == 'sitestorepoll.profile-sitestorepolls' || "$tempcontent_name" == 'sitestoremusic.profile-sitestoremusic' || "$tempcontent_name" == 'sitestoremember.profile-sitestoremembers' || "$tempcontent_name" == 'sitestoreoffer.profile-sitestoreoffers' || "$tempcontent_name" == 'sitestoretwitter.feeds-sitestoretwitter' || "$tempcontent_name" == 'sitestoreproduct.store-profile-products' || "$tempcontent_name" == 'siteevent.contenttype-events') {
            Engine_Api::_()->sitestore()->showAdWithPackage($sitestore);
            $view->headScript()
                    ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/hideWidgets.js');
          }

          $view->headScript()
                  ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/hideTabs.js')
                  ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/core.js');

          $view->headScript()
                  ->appendScript("   var store_communityads = '$store_communityads';
	    var contentinformation = '$contentinformation';
	    var store_showtitle = 0;
	    var prev_tab_class = '';");
          if (!empty($script)) {
            $view->headScript()
                    ->appendScript($script);
          }
        }
      }
    }
  }

  public function onStatistics($event) {
    $table = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), 'COUNT(*) AS count');
    $event->addResponse($select->query()->fetchColumn(0), 'store');
  }

  public function onUserDeleteBefore($event) {
    $payload = $event->getPayload();

    if ($payload instanceof User_Model_User) {

      $user_id = $payload->getIdentity();

      //GET STORE TABLE
      $sitestoreTable = Engine_Api::_()->getDbtable('stores', 'sitestore');

      Engine_Api::_()->getDbtable('claims', 'sitestore')->delete(array('user_id =?' => $user_id));

      Engine_Api::_()->getDbtable('listmemberclaims', 'sitestore')->delete(array('user_id = ?' => $user_id));

      Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->delete(array('user_id = ?' => $user_id));

      //START ALBUM CODE
      $table = Engine_Api::_()->getItemTable('sitestore_photo');
      $select = $table->select()->where('user_id = ?', $user_id);
      $rows = $table->fetchAll($select);
      if (!empty($rows)) {
        foreach ($rows as $photo) {
          $photo->delete();
        }
      }

      $table = Engine_Api::_()->getItemTable('sitestore_album');
      $select = $table->select()->where('owner_id = ?', $user_id);
      $rows = $table->fetchAll($select);
      if (!empty($rows)) {
        foreach ($rows as $album) {
          $album->delete();
        }
      }
      //END ALBUM CODE
      //START DISUCSSION CODE
      $sitestoreDiscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion');
      if ($sitestoreDiscussionEnabled) {

        $table = Engine_Api::_()->getItemTable('sitestore_topic');
        $select = $table->select()->where('user_id = ?', $user_id);
        $rows = $table->fetchAll($select);
        if (!empty($rows)) {
          foreach ($rows as $topic) {
            $topic->delete();
          }
        }

        $table = Engine_Api::_()->getItemTable('sitestore_post');
        $select = $table->select()->where('user_id = ?', $user_id);
        $rows = $table->fetchAll($select);
        if (!empty($rows)) {
          foreach ($rows as $post) {
            $post->delete();
          }
        }

        Engine_Api::_()->getDbtable('topicwatches', 'sitestore')->delete(array('user_id = ?' => $user_id));
      }
      //END DISUCSSION CODE

      $sitestoreSelect = $sitestoreTable->select()->where('owner_id = ?', $user_id);

      foreach ($sitestoreTable->fetchAll($sitestoreSelect) as $sitestore) {
        Engine_Api::_()->sitestore()->onStoreDelete($sitestore->store_id);
      }

      //LIKE COUNT DREASE FORM STORE TABLE.
      $likesTable = Engine_Api::_()->getDbtable('likes', 'core');
      $likesTableSelect = $likesTable->select()->where('poster_id = ?', $payload->getIdentity())->Where('resource_type = ?', 'sitestore_store');
      $results = $likesTable->fetchAll($likesTableSelect);
      foreach ($results as $user) {
        $resource = Engine_Api::_()->getItem('sitestore_store', $user->resource_id);
        $resource->like_count--;
        $resource->save();
      }
    }
  }

  public function addActivity($event) {
    $payload = $event->getPayload();
    $subject = $payload['subject'];
    $object = $payload['object'];

    // Only for object=event
    if (strpos( $payload['type'],'like_')===false && $object instanceof Sitestore_Model_Store /* &&
      Engine_Api::_()->authorization()->context->isAllowed($object, 'member', 'view') */) {
      $event->addResponse(array(
          'type' => 'sitestore_store',
          'identity' => $object->getIdentity()
      ));
    }
  }

  public function getActivity($event) {
    // Detect viewer and subject
    $payload = $event->getPayload();
    $user = null;
    $subject = null;
    if ($payload instanceof User_Model_User) {
      $user = $payload;
    } else if (is_array($payload)) {
      if (isset($payload['for']) && $payload['for'] instanceof User_Model_User) {
        $user = $payload['for'];
      }
      if (isset($payload['about']) && $payload['about'] instanceof Core_Model_Item_Abstract) {
        $subject = $payload['about'];
      }
    }
    if (null === $user) {
      $viewer = Engine_Api::_()->user()->getViewer();
      if ($viewer->getIdentity()) {
        $user = $viewer;
      }
    }
    if (null === $subject && Engine_Api::_()->core()->hasSubject()) {
      $subject = Engine_Api::_()->core()->getSubject();
    }


    // Get like stores
    if ($user && empty($subject)) {
      $settingsCoreApi = Engine_Api::_()->getApi('settings', 'core');
      if ($settingsCoreApi->sitestore_feed_type && $settingsCoreApi->sitestore_feed_onlyliked) {
        $data = Engine_Api::_()->sitestore()->getMemberLikeStoresOfIds($user);
        if (!empty($data) && is_array($data)) {
          $event->addResponse(array(
              'type' => 'sitestore_store',
              'data' => $data,
          ));
        }
      }
    } else if ($subject && ($subject->getType() == 'sitestore_store')) {
      $settingsCoreApi = Engine_Api::_()->getApi('settings', 'core');
      if ($settingsCoreApi->sitestore_feed_type) {
        $event->addResponse(array(
            'type' => 'sitestore_store',
            'data' => array($subject->getIdentity()),
        ));
      }
    } else if ($subject && ($subject->getType() == 'user')) {
//
//      $content = Engine_Api::_()->getApi('settings', 'core')
//              ->getSetting('activity.content', 'everyone');
//      $contentBaseFlage = false;
//      if ($content == 'everyone') {
//        $contentBaseFlage = true;
//      } else if ($user) {
//        switch ($content) {
//          case 'networks':
//            $networkTable = Engine_Api::_()->getDbtable('membership', 'network');
//            $userIds = $networkTable->getMembershipsOfIds($user);
//            $subjectIds = $networkTable->getMembershipsOfIds($subject);
//            $comanIds = array_intersect($userIds, $subjectIds);
//            if (!empty($comanIds)) {
//              $contentBaseFlage = true;
//              break;
//            }
//          case 'friends':
//            $friendsIds = $subject->membership()->getMembershipsOfIds();
//           
//            if (in_array($user->getIdentity(), $friendsIds)) {
//              $contentBaseFlage = true;
//              break;
//            }
//            break;
//        }
//      }
//      if ($contentBaseFlage) {
      $data = Engine_Api::_()->getApi('subCore', 'sitestore')->getMemberFeedsForStoreOfIds($subject);
      $event->addResponse(array(
          'type' => 'sitestore_store',
          'data' => $data,
      ));
//      }
    }
  }
  public function onActivityActionCreateAfter($event) {
	
		$payload = $event->getPayload();		
		if ($payload->object_type == 'sitestore_store' && ($payload->getTypeInfo()->type == 'sitestore_post_self' || $payload->getTypeInfo()->type == 'sitestore_post') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {

			$viewer = Engine_Api::_()->user()->getViewer();
			$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
			
			$notidicationSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.feed.type', 0);
			
			$store_id = $payload->getObject()->store_id;
			$user_id = $payload->getSubject()->user_id;

			$subject = Engine_Api::_()->getItem('sitestore_store', $store_id);
			$owner = $subject->getOwner();

			$notifications = Engine_Api::_()->getDbtable('notifications', 'activity');

			//previous notification is delete.
			$notifications->delete(array('type =?' => "sitestore_notificationpost", 'object_type = ?' => "sitestore_store", 'object_id = ?' => $store_id, 'subject_id = ?' => $user_id));

			//GET STORE TITLE
			$storetitle = $subject->title;

			//STORE URL
			$store_url = Engine_Api::_()->sitestore()->getStoreUrl($subject->store_id);

			//GET STORE URL
			$store_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('store_url' => $store_url), 'sitestore_entry_view', true);

			//MAKING STORE TITLE LINK
			$store_title_link = '<a href="' . $store_baseurl . '"  >' . $storetitle . ' </a>';

			//GET LOGGED IN USER INFORMATION
			$viewer = Engine_Api::_()->user()->getViewer();

			//Poster title and photo with link.
			$posterTitle = $viewer->getTitle();
			$posterUrl = $viewer->getHref();
			$poster_baseurl = 'http://' . $_SERVER['HTTP_HOST']. $posterUrl;
			$poster_title_link = "<a href='$poster_baseurl' style='font-weight:bold;text-decoration:none;'>" . $posterTitle . " </a>";

			$moduletype = 'user';
                        //GETTING THE USER PHOTO.
                        $file = $viewer->getPhotoUrl('thumb.icon');
                        if (empty($file)) {
                         $photo = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/application/modules/'.  ucfirst($moduletype).'/externals/images/nophoto_'.$moduletype.'_thumb_icon.png';
                       } else {
                          if (strpos($file, 'http') === FALSE) 
                                                   $photo = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $file;
                                     else
                                       $photo = $file;
                        }
			
			$image = "<img src='$photo' />";
			$post_baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . $payload->getHref();
			$post = $posterTitle . ' posted in your store: ' . $storetitle;
			$postbody = $payload->body;
			$body_content = "<table cellspacing='0' cellpadding='0' border='0' style='border-collapse:collapse;' width='90%'><tr><td style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding:20px;background-color:#fff;border-left:none;border-right:none;border-top:none;border-bottom:none;'><table cellspacing='0' cellpadding='0' style='border-collapse:collapse;' width='100%'><tr><td colspan='2' style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;border-bottom:1px solid #dddddd;padding-bottom:5px;'><a style='font-weight:bold;margin-bottom:10px;text-decoration:none;' href='$post_baseUrl'>" . $post . "</a></td></tr><tr><td valign='top' style='padding:10px 15px 10px 10px;'><a href='$poster_baseurl'  >" . $image . " </a></td><td valign='top' style='padding-top:10px;font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;width:100%;text-align:left;'><table cellspacing='0' cellpadding='0' style='border-collapse:collapse;width:100%;'><tr><td style='font-
			size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;'>" .$poster_title_link. "<br /><span style='color:#333333;margin-top:5px;display:block;'>" . $postbody . "</span></td></tr></table></td></tr></table></td></tr></table>";

			$manageTable = Engine_Api::_()->getDbtable('manageadmins', 'sitestore');
			$payload_body = strip_tags($payload->body);
			$payload_body = Engine_String::strlen($payload_body) > 50 ? Engine_String::substr($payload_body, 0, (53 - 3)) . '...' : $payload_body;

			//FETCH DATA
			$manageAdminsIds = $manageTable->getManageAdmin($store_id, $user_id);
			$sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');

			foreach ($manageAdminsIds as $value) {
				$action_notification = unserialize($value['action_notification']);
				$user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
				if (empty($sitestorememberEnabled)) {
					if (!empty($value['notification']) && in_array('posted', $action_notification)) {
						$row = $notifications->createRow();
						$row->user_id = $user_subject->getIdentity();
						// 								if (!empty($notidicationSettings)) {
						// 									$row->subject_type = $subject->getType();
						// 									$row->subject_id = $subject->getIdentity();
						// 								}
						// 								else {
						$row->subject_type = $viewer->getType();
						$row->subject_id = $viewer->getIdentity();
						//}
						$row->object_type = $subject->getType();
						$row->object_id = $subject->getIdentity();
						$row->type = 'sitestore_notificationpost';
						$row->params = null;
						$row->date = date('Y-m-d H:i:s');
						$row->save();
					}
				}
				if(!empty($value['email'])) {
					Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, 'SITESTORE_POSTNOTIFICATION_EMAIL', array(
					'store_title' => $storetitle,
					'body_content' => $body_content,
					'post_body_body' => $payload_body,
					));
				}
			}

			//START NOTIFICATION TO ALL FOLLOWERS.
			$isStoreAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer->getIdentity(), $store_id);
			if (!empty($isStoreAdmins)) {
				$followersIds = Engine_Api::_()->getDbTable('follows', 'seaocore')->getFollowers('sitestore_store', $store_id, $viewer->getIdentity());
				$notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
				if (!empty($followersIds)) {
					//previous notification is delete.
					$notificationsTable->delete(array('type =?' => "sitestore_notificationpost", 'object_type = ?' => "sitestore_store", 'object_id = ?' => $store_id, 'subject_id = ?' => $store_id, 'subject_type = ?' => 'sitestore_store'));
					foreach ($followersIds as $value) {
						$user_subject = Engine_Api::_()->user()->getUser($value['poster_id']);
						$row = $notificationsTable->createRow();
						$row->user_id = $user_subject->getIdentity();
						if (!empty($notidicationSettings)) {
							$row->subject_type = $subject->getType();
							$row->subject_id = $subject->getIdentity();
						}
						else {
							$row->subject_type = $viewer->getType();
							$row->subject_id = $viewer->getIdentity();
						}
						$row->type = "sitestore_notificationpost";
						$row->object_type = $subject->getType();
						$row->object_id = $subject->getIdentity();
						$row->params = null;
						$row->date = date('Y-m-d H:i:s');
						$row->save();
					}
				}
			}
			//END NOTIFICATION TO ALL FOLLOWERS.
		}
  }
}
