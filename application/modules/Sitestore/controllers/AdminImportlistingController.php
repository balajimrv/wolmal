<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminImportlistingController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_AdminImportlistingController extends Core_Controller_Action_Admin {

  //ACTION FOR IMPORTING DATA FROM LISTING TO STORE
  public function indexAction() {

    //INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
    ini_set('memory_limit', '2048M');
    set_time_limit(0);

    //START CODE FOR CREATING THE ListingToStoreImport.log FILE
    if (!file_exists(APPLICATION_PATH . '/temporary/log/ListingToStoreImport.log')) {
      $log = new Zend_Log();
      try {
        $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/ListingToStoreImport.log'));
      } catch (Exception $e) {
        //CHECK DIRECTORY
        if (!@is_dir(APPLICATION_PATH . '/temporary/log') && @mkdir(APPLICATION_PATH . '/temporary/log', 0777, true)) {
          $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/ListingToStoreImport.log'));
        } else {
          //Silence ...
          if (APPLICATION_ENV !== 'production') {
            $log->log($e->__toString(), Zend_Log::CRIT);
          } else {
            //MAKE SURE LOGGING DOESN'T CAUSE EXCEPTIONS
            $log->addWriter(new Zend_Log_Writer_Null());
          }
        }
      }
    }

    //GIVE WRITE PERMISSION IF FILE EXIST
    if (file_exists(APPLICATION_PATH . '/temporary/log/ListingToStoreImport.log')) {
      @chmod(APPLICATION_PATH . '/temporary/log/ListingToStoreImport.log', 0777);
    }
    //END CODE FOR CREATING THE ListingToStoreImport.log FILE
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_import');

    //START IMPORTING WORK IF LIST AND SITESTORE IS INSTALLED AND ACTIVATE
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('list') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore')) {

			//ADD NEW COLUMN IN LISTING TABLE
			$db = Engine_Db_Table::getDefaultAdapter();
			$type_array = $db->query("SHOW COLUMNS FROM engine4_list_listings LIKE 'is_import'")->fetch();
			if (empty($type_array)) {
				$run_query = $db->query("ALTER TABLE `engine4_list_listings` ADD `is_import` TINYINT( 2 ) NOT NULL DEFAULT '0' AFTER `subcategory_id` ");
			}

      //START IF IMPORTING IS BREAKED BY SOME REASON
      $listingTable = Engine_Api::_()->getDbTable('listings', 'list');
      $listingTableName = $listingTable->info('name');
      $selectListings = $listingTable->select()
                      ->from($listingTableName, 'listing_id')
											->where('is_import != ?', 1)
                      ->order('listing_id ASC');
      $listingDatas = $listingTable->fetchAll($selectListings);

      $this->view->first_listing_id = $first_listing_id = 0;
      $this->view->last_listing_id = $last_listing_id = 0;

      if (!empty($listingDatas)) {

        $flag_first_listing_id = 1;

        foreach ($listingDatas as $listingData) {

          if ($flag_first_listing_id == 1) {
            $this->view->first_listing_id = $first_listing_id = $listingData->listing_id;
          }
          $flag_first_listing_id++;

          $this->view->last_listing_id = $last_listing_id = $listingData->listing_id;
        }

        if (isset($_GET['assigned_previous_id'])) {
          $this->view->assigned_previous_id = $assigned_previous_id = $_GET['assigned_previous_id'];
        } else {
          $this->view->assigned_previous_id = $assigned_previous_id = $first_listing_id;
        }
      }
      //END IF IMPORTING IS BREAKED BY SOME REASON
      //START IMPORTING IF REQUESTED
      if (isset($_GET['start_import']) && $_GET['start_import'] == 1) {

        //START FETCH CATEGORY WORK
        $storeCategoryTable = Engine_Api::_()->getDbtable('categories', 'sitestore');
        $storeCategoryTableName = $storeCategoryTable->info('name');
        $selectStoreCategory = $storeCategoryTable->select()
                        ->from($storeCategoryTableName, 'category_name')
                        ->where('cat_dependency = ?', 0);
        $storeCategoryDatas = $storeCategoryTable->fetchAll($selectStoreCategory);
        if (!empty($storeCategoryDatas)) {
          $storeCategoryDatas = $storeCategoryDatas->toArray();
        }

        $storeCategoryInArrayData = array();
        foreach ($storeCategoryDatas as $storeCategoryData) {
          $storeCategoryInArrayData[] = $storeCategoryData['category_name'];
        }

        $listCategoryTable = Engine_Api::_()->getDbtable('categories', 'list');
        $listCategoryTableName = $listCategoryTable->info('name');
        $selectListCategory = $listCategoryTable->select()
                        ->from($listCategoryTableName)
                        ->where('cat_dependency = ?', 0);
        $listCategoryDatas = $listCategoryTable->fetchAll($selectListCategory);
        if (!empty($listCategoryDatas)) {
          $listCategoryDatas = $listCategoryDatas->toArray();
          foreach ($listCategoryDatas as $listCategoryData) {
            if (!in_array($listCategoryData['category_name'], $storeCategoryInArrayData)) {
              $newCategory = $storeCategoryTable->createRow();
              //$newCategory->user_id = $listCategoryData['user_id'];
              $newCategory->category_name = $listCategoryData['category_name'];
              $newCategory->cat_dependency = 0;
              $newCategory->cat_order = 9999;
              $newCategory->save();

              $selectListSubCategory = $listCategoryTable->select()
                              ->from($listCategoryTableName)
                              ->where('cat_dependency = ?', $listCategoryData['category_id']);
              $listSubCategoryDatas = $listCategoryTable->fetchAll($selectListSubCategory);
              foreach ($listSubCategoryDatas as $listSubCategoryData) {
                $newSubCategory = $storeCategoryTable->createRow();
                //$newSubCategory->user_id = $listCategoryData['user_id'];
                $newSubCategory->category_name = $listSubCategoryData->category_name;
                $newSubCategory->cat_dependency = $newCategory->category_id;
                $newSubCategory->cat_order = 9999;
                $newSubCategory->save();
              }
            } elseif (in_array($listCategoryData['category_name'], $storeCategoryInArrayData)) {

              $storeCategory = $storeCategoryTable->fetchRow(array('category_name = ?' => $listCategoryData['category_name'], 'cat_dependency = ?' => 0));
              if (!empty($storeCategory))
                $storeCategoryId = $storeCategory->category_id;

              $selectStoreSubCategory = $storeCategoryTable->select()
                              ->from($storeCategoryTableName, array('category_name'))
                              ->where('cat_dependency = ?', $storeCategoryId);
              $storeSubCategoryDatas = $storeCategoryTable->fetchAll($selectStoreSubCategory);
              if (!empty($storeSubCategoryDatas)) {
                $storeSubCategoryDatas = $storeSubCategoryDatas->toArray();
              }

              $storeSubCategoryInArrayData = array();
              foreach ($storeSubCategoryDatas as $storeSubCategoryData) {
                $storeSubCategoryInArrayData[] = $storeSubCategoryData['category_name'];
              }

              $listCategory = $listCategoryTable->fetchRow(array('category_name = ?' => $listCategoryData['category_name'], 'cat_dependency = ?' => 0));
              if (!empty($listCategory))
                $listCategoryId = $listCategory->category_id;

              $selectListSubCategory = $listCategoryTable->select()
                              ->from($listCategoryTableName)
                              ->where('cat_dependency = ?', $listCategoryId);
              $listSubCategoryDatas = $listCategoryTable->fetchAll($selectListSubCategory);
              if (!empty($listSubCategoryDatas)) {
                $listSubCategoryDatas = $listSubCategoryDatas->toArray();
              }

              foreach ($listSubCategoryDatas as $listSubCategoryData) {
                if (!in_array($listSubCategoryData['category_name'], $storeSubCategoryInArrayData)) {
                  $newSubCategory = $storeCategoryTable->createRow();
                 // $current_import = $this->_getParam('current_import');$current_import = $this->_getParam('current_import');
                  $newSubCategory->category_name = $listSubCategoryData['category_name'];
                  $newSubCategory->cat_dependency = $storeCategoryId;
                  $newSubCategory->cat_order = 9999;
                  $newSubCategory->save();
                }
              }
            }
          }
        }
        //END FETCH CATEGORY WOR
        //START COMMAN DATA
        $package_id = Engine_Api::_()->getItemtable('sitestore_package')->fetchRow(array('defaultpackage = ?' => 1))->package_id;
        $package = Engine_Api::_()->getItemTable('sitestore_package')->fetchRow(array('package_id = ?' => $package_id));

        $metaTable = Engine_Api::_()->fields()->getTable('list_listing', 'meta');
        $selectMetaData = $metaTable->select()->where('type = ?', 'currency');
        $metaData = $metaTable->fetchRow($selectMetaData);

        $table = Engine_Api::_()->getDbtable('stores', 'sitestore');

        $likeTable = Engine_Api::_()->getDbtable('likes', 'core');
        $likeTableName = $likeTable->info('name');


        $commentTable = Engine_Api::_()->getDbtable('comments', 'core');
        $commentTableName = $commentTable->info('name');

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion')) {
          $topicTable = Engine_Api::_()->getDbtable('topics', 'list');
          $topicTableName = $topicTable->info('name');
          $storeTopicTable = Engine_Api::_()->getDbtable('topics', 'sitestore');
          $storePostTable = Engine_Api::_()->getDbtable('posts', 'sitestore');

          $postTable = Engine_Api::_()->getDbtable('posts', 'list');
          $postTableName = $postTable->info('name');

          $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'list');
          $storeTopicWatchesTable = Engine_Api::_()->getDbtable('topicwatches', 'sitestore');
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
          $reviewTable = Engine_Api::_()->getDbtable('reviews', 'list');
          $reviewTableName = $reviewTable->info('name');
          $storeReviewTable = Engine_Api::_()->getDbtable('reviews', 'sitestorereview');
          $reviewRatingTable = Engine_Api::_()->getDbtable('ratings', 'sitestorereview');
        }

        $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitestore');

        $listLocationTable = Engine_Api::_()->getDbtable('locations', 'list');

        $storeLocationTable = Engine_Api::_()->getDbtable('locations', 'sitestore');

        $albumTable = Engine_Api::_()->getDbtable('albums', 'sitestore');
        $listPhotoTable = Engine_Api::_()->getDbtable('photos', 'list');
        $storageTable = Engine_Api::_()->getDbtable('files', 'storage');


        $sitestoreFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreform');
        if ($sitestoreFormEnabled) {
          $sitestoreformtable = Engine_Api::_()->getDbtable('sitestoreforms', 'sitestoreform');
          $optionid = Engine_Api::_()->getDbtable('storequetions', 'sitestoreform');
          $table_option = Engine_Api::_()->fields()->getTable('sitestoreform', 'options');
        }

        $writeTable = Engine_Api::_()->getDbtable('writes', 'list');
        $storeWriteTable = Engine_Api::_()->getDbtable('writes', 'sitestore');

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo')) {

          $storeVideoTable = Engine_Api::_()->getDbtable('videos', 'sitestorevideo');
          $storeVideoTableName = $storeVideoTable->info('name');

          $listVideoRating = Engine_Api::_()->getDbTable('ratings', 'video');
          $listVideoRatingName = $listVideoRating->info('name');

          $storeVideoRatingTable = Engine_Api::_()->getDbTable('ratings', 'sitestorevideo');

          $listVideoTable = Engine_Api::_()->getDbtable('clasfvideos', 'list');
          $listVideoTableName = $listVideoTable->info('name');
        }

        $storeAdminTable = Engine_Api::_()->getDbtable('pages', 'core');
        $storeAdminTableName = $storeAdminTable->info('name');
        $storeTable = Engine_Api::_()->getDbtable('contentstores', 'sitestore');
        //END COMMON DATA

        $selectListings = $listingTable->select()
                        ->where('listing_id >= ?', $assigned_previous_id)
                        ->from($listingTableName, 'listing_id')
												->where('is_import != ?', 1)
                        ->order('listing_id ASC');
        $listingDatas = $listingTable->fetchAll($selectListings);

				$next_import_count = 0;
        foreach ($listingDatas as $listingData) {

          $listing_id = $listingData->listing_id;

          if (!empty($listing_id)) {
            $listing = Engine_Api::_()->getItem('list_listing', $listing_id);

            $sitestore = $table->createRow();
            $sitestore->title = $listing->title;
            $sitestore->body = $listing->body;
            $sitestore->overview = $listing->overview;
            $sitestore->owner_id = $listing->owner_id;

            //START FETCH LIST CATEGORY AND SUB-CATEGORY
            if (!empty($listing->category_id)) {
              $listCategory = $listCategoryTable->fetchRow(array('category_id = ?' => $listing->category_id, 'cat_dependency = ?' => 0));
              if (!empty($listCategory)) {
                $listCategoryName = $listCategory->category_name;

                if (!empty($listCategoryName)) {
                  $storeCategory = $storeCategoryTable->fetchRow(array('category_name = ?' => $listCategoryName, 'cat_dependency = ?' => 0));
                  if (!empty($storeCategory)) {
                    $storeCategoryId = $sitestore->category_id = $storeCategory->category_id;
                  }

                  $listSubCategory = $listCategoryTable->fetchRow(array('category_id = ?' => $listing->subcategory_id, 'cat_dependency = ?' => $listing->category_id));
                  if (!empty($listSubCategory)) {
                    $listSubCategoryName = $listSubCategory->category_name;

                    $storeSubCategory = $storeCategoryTable->fetchRow(array('category_name = ?' => $listSubCategoryName, 'cat_dependency = ?' => $storeCategoryId));
                    if (!empty($storeSubCategory)) {
                      $sitestore->subcategory_id = $storeSubCategory->category_id;
                    }
                  }
                }
              }
            }
            //END FETCH LIST CATEGORY AND SUB-CATEGORY

            //START FETCH DEFAULT PACKAGE ID
            if (!empty($package))
              $sitestore->package_id = $package_id;
            //END FETCH DEFAULT PACKAGE ID

            $sitestore->profile_type = 0;

            $sitestore->photo_id = 0;

            //START FETCH PRICE
            if (!empty($metaData)) {
              $field_id = $metaData->field_id;

              $valueTable = Engine_Api::_()->fields()->getTable('list_listing', 'values');
              $selectValueData = $valueTable->select()->where('item_id = ?', $listing_id)->where('field_id = ?', $field_id);
              $valueData = $valueTable->fetchRow($selectValueData);
              if (!empty($valueData)) {
                $sitestore->price = $valueData->value;
              }
            }
            //END FETCH PRICE
            //START GET DATA FROM LISTING
            $sitestore->creation_date = $listing->creation_date;
            $sitestore->modified_date = $listing->modified_date;
            $sitestore->approved = $listing->approved;
            $sitestore->featured = $listing->featured;
            $sitestore->sponsored = $listing->sponsored;

            $sitestore->view_count = 1;
            if ($listing->view_count > 0) {
              $sitestore->view_count = $listing->view_count;
            }

            $sitestore->comment_count = $listing->comment_count;
            $sitestore->like_count = $listing->like_count;
            $sitestore->search = $listing->search;
            $sitestore->closed = $listing->closed;
            $sitestore->draft = $listing->draft;
            $sitestore->offer = 0;

            if (!empty($listing->aprrove_date)) {
              $sitestore->pending = 0;
              $sitestore->aprrove_date = $listing->aprrove_date;
              $sitestore->expiration_date = '2250-01-01 00:00:00';
            }

           	if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
							$sitestore->rating = round($listing->rating, 0);
						}

            $sitestore->save();

						$listing->is_import = 1;
						$listing->save();
						$next_import_count++;
            //END GET DATA FROM LISTING

            //START CREATE NEW STORE URL
            $store_url = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($listing->title))), '-');
            $sitestore_table = Engine_Api::_()->getItemTable('sitestore_store');
            $store = $sitestore_table->fetchRow(array('store_url = ?' => $store_url));
            if (!empty($store)) {
              $sitestore->store_url = $store_url . $sitestore->store_id;
            } else {
              $sitestore->store_url = $store_url;
            }
            //END CREATE NEW STORE URL

            $sitestore->save();

            //START PROFILE MAPS WORK
            Engine_Api::_()->getDbtable('profilemaps', 'sitestore')->profileMapping($sitestore);

//             //EXTRACTING CURRENT ADMIN SETTINGS FOR THIS VIEW STORE.
//             $selectStoreAdmin = $storeAdminTable->select()
//                             ->setIntegrityCheck(false)
//                             ->from($storeAdminTableName)
//                             ->where('name = ?', 'sitestore_index_view');
//             $storeAdminresult = $storeAdminTable->fetchRow($selectStoreAdmin);
//             //NOW INSERTING THE ROW IN STORE TABLE
//             //MAKE NEW ENTRY FOR USER LAYOUT
//             $storeObject = $storeTable->createRow();
//             $storeObject->displayname = ( null !== ($name = $sitestore->title) ? $name : 'Untitled' );
//             $storeObject->title = ( null !== ($name = $sitestore->title) ? $name : 'Untitled' );
//             $storeObject->description = $sitestore->body;
//             $storeObject->name = "sitestore_index_view";
//             $storeObject->url = $storeAdminresult->url;
//             $storeObject->custom = $storeAdminresult->custom;
//             $storeObject->fragment = $storeAdminresult->fragment;
//             $storeObject->keywords = $storeAdminresult->keywords;
//             $storeObject->layout = $storeAdminresult->layout;
//             $storeObject->view_count = $storeAdminresult->view_count;
//             $storeObject->user_id = $sitestore->owner_id;
//             $storeObject->store_id = $sitestore->store_id;
//             $contentStoreId = $storeObject->save();
// 
//             //NOW FETCHING STORE CONTENT DEFAULT SETTING INFORMATION FROM CORE CONTENT TABLE FOR THIS STORE.
//             //NOW INSERTING DEFAULT STORE CONTENT SETTINGS IN OUR CONTENT TABLE
// 						$layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
// 						if (!$layout) {
// 							Engine_Api::_()->getDbtable('content', 'sitestore')->setContentDefault($contentStoreId);
// 						} else {
// 							Engine_Api::_()->getApi('layoutcore', 'sitestore')->setContentDefaultLayout($contentStoreId);
// 						}


            //START FETCH TAG
            $listTags = $listing->tags()->getTagMaps();
            $tagString = '';

            foreach ($listTags as $tagmap) {

              if ($tagString != '')
                $tagString .= ', ';
              $tagString .= $tagmap->getTag()->getTitle();

              $owner = Engine_Api::_()->getItem('user', $listing->owner_id);
              $tags = preg_split('/[,]+/', $tagString);
              $tags = array_filter(array_map("trim", $tags));
              $sitestore->tags()->setTagMaps($owner, $tags);
            }
            //END FETCH TAG

            //START FETCH LIKES
            $selectLike = $likeTable->select()
                            ->from($likeTableName, 'like_id')
                            ->where('resource_type = ?', 'list_listing')
                            ->where('resource_id = ?', $listing_id);
            $selectLikeDatas = $likeTable->fetchAll($selectLike);
            foreach ($selectLikeDatas as $selectLikeData) {
              $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);

              $newLikeEntry = $likeTable->createRow();
              $newLikeEntry->resource_type = 'sitestore_store';
              $newLikeEntry->resource_id = $sitestore->store_id;
              $newLikeEntry->poster_type = 'user';
              $newLikeEntry->poster_id = $like->poster_id;
              $newLikeEntry->creation_date = $like->creation_date;
              $newLikeEntry->save();
            }
            //END FETCH LIKES

            //START FETCH COMMENTS
            $selectLike = $commentTable->select()
                            ->from($commentTableName, 'comment_id')
                            ->where('resource_type = ?', 'list_listing')
                            ->where('resource_id = ?', $listing_id);
            $selectLikeDatas = $commentTable->fetchAll($selectLike);
            foreach ($selectLikeDatas as $selectLikeData) {
              $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

              $newLikeEntry = $commentTable->createRow();
              $newLikeEntry->resource_type = 'sitestore_store';
              $newLikeEntry->resource_id = $sitestore->store_id;
              $newLikeEntry->poster_type = 'user';
              $newLikeEntry->poster_id = $comment->poster_id;
              $newLikeEntry->body = $comment->body;
              $newLikeEntry->creation_date = $comment->creation_date;
              $newLikeEntry->like_count = $comment->like_count;
              $newLikeEntry->save();
            }
            //END FETCH COMMENTS

            //START FETCH PRIVACY
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

            foreach ($roles as $role) {
              if ($auth->isAllowed($listing, $role, 'view')) {
                $values['auth_view'] = $role;
              }
            }

            foreach ($roles as $role) {
              if ($auth->isAllowed($listing, $role, 'comment')) {
                $values['auth_comment'] = $role;
              }
            }

            foreach ($roles as $role) {
              if ($auth->isAllowed($listing, $role, 'photo')) {
                $values['auth_spcreate'] = $role;
              }
            }

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);
            $photoMax = array_search($values['auth_spcreate'], $roles);

            foreach ($roles as $i => $role) {
              $auth->setAllowed($sitestore, $role, 'view', ($i <= $viewMax));
              $auth->setAllowed($sitestore, $role, 'comment', ($i <= $commentMax));
              $auth->setAllowed($sitestore, $role, 'spcreate', ($i <= $photoMax));
            }
            //END FETCH PRIVACY

            //START FETCH DISCUSSION DATA
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion')) {

              foreach ($roles as $i => $role) {
                $auth->setAllowed($sitestore, $role, 'sdicreate', ($i <= $photoMax));
              }
              
              $topicSelect = $topicTable->select()
                              ->from($topicTableName)
                              ->where('listing_id = ?', $listing_id);
              $topicSelectDatas = $topicTable->fetchAll($topicSelect);
              if (!empty($topicSelectDatas)) {
                $topicSelectDatas = $topicSelectDatas->toArray();

                foreach ($topicSelectDatas as $topicSelectData) {
                  $storeTopic = $storeTopicTable->createRow();
                  $storeTopic->store_id = $sitestore->store_id;
                  $storeTopic->user_id = $topicSelectData['user_id'];
                  $storeTopic->title = $topicSelectData['title'];
                  $storeTopic->creation_date = $topicSelectData['creation_date'];
                  $storeTopic->modified_date = $topicSelectData['modified_date'];
                  $storeTopic->sticky = $topicSelectData['sticky'];
                  $storeTopic->closed = $topicSelectData['closed'];
                  $storeTopic->post_count = $topicSelectData['post_count'];
                  $storeTopic->view_count = $topicSelectData['view_count'];
                  $storeTopic->lastpost_id = $topicSelectData['lastpost_id'];
                  $storeTopic->lastposter_id = $topicSelectData['lastposter_id'];
                  $storeTopic->save();

                  //START FETCH TOPIC POST'S
                  $postSelect = $postTable->select()
                                  ->from($postTableName)
                                  ->where('topic_id = ?', $topicSelectData['topic_id'])
                                  ->where('listing_id = ?', $listing_id);
                  $postSelectDatas = $postTable->fetchAll($postSelect);
                  if (!empty($postSelectDatas)) {
                    $postSelectDatas = $postSelectDatas->toArray();

                    foreach ($postSelectDatas as $postSelectData) {
                      $storePost = $storePostTable->createRow();
                      $storePost->topic_id = $storeTopic->topic_id;
                      $storePost->store_id = $sitestore->store_id;
                      $storePost->user_id = $postSelectData['user_id'];
                      $storePost->body = $postSelectData['body'];
                      $storePost->creation_date = $postSelectData['creation_date'];
                      $storePost->modified_date = $postSelectData['modified_date'];
                      $storePost->save();
                    }
                  }
                  //END FETCH TOPIC POST'S

                  //START FETCH TOPIC WATCH
                  $topicWatchData = $topicWatchesTable->fetchRow(array('resource_id = ?' => $listing_id, 'topic_id = ?' => $topicSelectData['topic_id'], 'user_id = ?' => $topicSelectData['user_id']));
                  if (!empty($topicWatchData))
                    $watch = $topicWatchData->watch;

                  $storeTopicWatchesTable->insert(array(
                      'resource_id' => $storeTopic->store_id,
                      'topic_id' => $storeTopic->topic_id,
                      'user_id' => $topicSelectData['user_id'],
                      'watch' => $watch,
                      'store_id' => $sitestore->store_id,
                  ));
                  //END FETCH TOPIC WATCH
                }
              }
            }
            //END FETCH DISCUSSION DATA

            //START FETCH REVIEW DATA
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
              $reviewTableSelect = $reviewTable->select()
                              ->from($reviewTableName, array('MAX(review_id) as review_id'))
                              ->where('listing_id = ?', $listing_id)
                              ->where('owner_id != ?', $listing->owner_id)
                              ->group('owner_id')
                              ->order('review_id ASC');
              $reviewSelectDatas = $reviewTable->fetchAll($reviewTableSelect);
              if (!empty($reviewSelectDatas)) {
                $reviewSelectDatas = $reviewSelectDatas->toArray();
                foreach ($reviewSelectDatas as $reviewSelectData) {
                  $review = Engine_Api::_()->getItem('list_review', $reviewSelectData['review_id']);

                  $storeReview = $storeReviewTable->createRow();
                  $storeReview->store_id = $sitestore->store_id;
                  $storeReview->owner_id = $review->owner_id;
                  $storeReview->title = $review->title;
                  $storeReview->body = $review->body;
                  $storeReview->view_count = 1;
                  $storeReview->recommend = 1;
                  $storeReview->creation_date = $review->creation_date;
                  $storeReview->modified_date = $review->modified_date;
                  $storeReview->save();

                  $reviewRating = $reviewRatingTable->createRow();
                  $reviewRating->review_id = $storeReview->review_id;
                  $reviewRating->category_id = $sitestore->category_id;
                  $reviewRating->store_id = $storeReview->store_id;
                  $reviewRating->reviewcat_id = 0;
                  $reviewRating->rating = round($listing->rating, 0);
                  $reviewRating->save();
                }
              }
            }
            //END FETCH REVIEW DATA

            //START INSERT SOME DEFAULT DATA
            $row = $manageadminsTable->createRow();
            $row->user_id = $sitestore->owner_id;
            $row->store_id = $sitestore->store_id;
            $row->save();

            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            $privacyMax = array_search('everyone', $roles);
            foreach ($roles as $i => $role) {
              $auth->setAllowed($sitestore, $role, 'print', ($i <= $privacyMax));
              $auth->setAllowed($sitestore, $role, 'tfriend', ($i <= $privacyMax));
              $auth->setAllowed($sitestore, $role, 'overview', ($i <= $privacyMax));
              $auth->setAllowed($sitestore, $role, 'map', ($i <= $privacyMax));
              $auth->setAllowed($sitestore, $role, 'insight', ($i <= $privacyMax));
              $auth->setAllowed($sitestore, $role, 'layout', ($i <= $privacyMax));
              $auth->setAllowed($sitestore, $role, 'contact', ($i <= $privacyMax));
              $auth->setAllowed($sitestore, $role, 'form', ($i <= $privacyMax));
              $auth->setAllowed($sitestore, $role, 'offer', ($i <= $privacyMax));
              $auth->setAllowed($sitestore, $role, 'invite', ($i <= $privacyMax));              
            }


            $locationData = $listLocationTable->fetchRow(array('listing_id = ?' => $listing_id));
            if (!empty($locationData)) {
              $sitestore->location = $locationData->location;
              $sitestore->save();

              $storeLocation = $storeLocationTable->createRow();
              $storeLocation->store_id = $sitestore->store_id;
              $storeLocation->location = $sitestore->location;
              $storeLocation->latitude = $locationData->latitude;
              $storeLocation->longitude = $locationData->longitude;
              $storeLocation->formatted_address = $locationData->formatted_address;
              $storeLocation->country = $locationData->country;
              $storeLocation->state = $locationData->state;
              $storeLocation->zipcode = $locationData->zipcode;
              $storeLocation->city = $locationData->city;
              $storeLocation->address = $locationData->address;
              $storeLocation->zoom = $locationData->zoom;
              $storeLocation->save();
            }
            //END INSERT SOME DEFAULT DATA

            //START FETCH PHOTO DATA
            $selectListPhoto = $listPhotoTable->select()
                            ->from($listPhotoTable->info('name'))
                            ->where('listing_id = ?', $listing_id);
            $listPhotoDatas = $listPhotoTable->fetchAll($selectListPhoto);

            $sitstore = Engine_Api::_()->getItem('sitestore_store', $sitestore->store_id);

            if (!empty($listPhotoDatas)) {

              $listPhotoDatas = $listPhotoDatas->toArray();

              if (empty($listing->photo_id)) {
                foreach ($listPhotoDatas as $listPhotoData) {
                  $listing->photo_id = $listPhotoData['photo_id'];
                  break;
                }
              }

              if (!empty($listing->photo_id)) {
                $listPhotoData = $listPhotoTable->fetchRow(array('file_id = ?' => $listing->photo_id));
                if (!empty($listPhotoData)) {
                  $storageData = $storageTable->fetchRow(array('file_id = ?' => $listPhotoData->file_id));

                  if (!empty($storageData)) {

                    $sitstore->setPhoto($storageData->storage_path);

                    $album_id = $albumTable->update(array('photo_id' => $sitstore->photo_id, 'owner_id' => $sitstore->owner_id), array('store_id = ?' => $sitstore->store_id));

                    $storeProfilePhoto = Engine_Api::_()->getDbTable('photos', 'sitestore')->fetchRow(array('file_id = ?' => $sitstore->photo_id));
                    if (!empty($storeProfilePhoto)) {
                      $storeProfilePhotoId = $storeProfilePhoto->photo_id;
                    } else {
                      $storeProfilePhotoId = $sitstore->photo_id;
                    }

                    //START FETCH LIKES
                    $selectLike = $likeTable->select()
                                    ->from($likeTableName, 'like_id')
                                    ->where('resource_type = ?', 'list_photo')
                                    ->where('resource_id = ?', $listing->photo_id);
                    $selectLikeDatas = $likeTable->fetchAll($selectLike);
                    foreach ($selectLikeDatas as $selectLikeData) {
                      $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);

                      $newLikeEntry = $likeTable->createRow();
                      $newLikeEntry->resource_type = 'sitestore_photo';
                      $newLikeEntry->resource_id = $storeProfilePhotoId;
                      $newLikeEntry->poster_type = 'user';
                      $newLikeEntry->poster_id = $like->poster_id;
                      $newLikeEntry->creation_date = $like->creation_date;
                      $newLikeEntry->save();
                    }
                    //END FETCH LIKES

                    //START FETCH COMMENTS
                    $selectLike = $commentTable->select()
                                    ->from($commentTableName, 'comment_id')
                                    ->where('resource_type = ?', 'list_photo')
                                    ->where('resource_id = ?', $listing->photo_id);
                    $selectLikeDatas = $commentTable->fetchAll($selectLike);
                    foreach ($selectLikeDatas as $selectLikeData) {
                      $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

                      $newLikeEntry = $commentTable->createRow();
                      $newLikeEntry->resource_type = 'sitestore_photo';
                      $newLikeEntry->resource_id = $storeProfilePhotoId;
                      $newLikeEntry->poster_type = 'user';
                      $newLikeEntry->poster_id = $comment->poster_id;
                      $newLikeEntry->body = $comment->body;
                      $newLikeEntry->creation_date = $comment->creation_date;
                      $newLikeEntry->like_count = $comment->like_count;
                      $newLikeEntry->save();
                    }
                    //END FETCH COMMENTS

                    //START FETCH TAGGER DETAIL
                    $tagmapsTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
                    $tagmapsTableName = $tagmapsTable->info('name');
                    $selectTagmaps = $tagmapsTable->select()
                                    ->from($tagmapsTableName, 'tagmap_id')
                                    ->where('resource_type = ?', 'list_photo')
                                    ->where('resource_id = ?', $listing->photo_id);
                    $selectTagmapsDatas = $tagmapsTable->fetchAll($selectTagmaps);
                    foreach ($selectTagmapsDatas as $selectTagmapsData) {
                      $tagMap = Engine_Api::_()->getItem('core_tag_map', $selectTagmapsData->tagmap_id);

                      $newTagmapEntry = $tagmapsTable->createRow();
                      $newTagmapEntry->resource_type = 'sitestore_photo';
                      $newTagmapEntry->resource_id = $storeProfilePhotoId;
                      $newTagmapEntry->tagger_type = 'user';
                      $newTagmapEntry->tagger_id = $tagMap->tagger_id;
                      $newTagmapEntry->tag_type = 'user';
                      $newTagmapEntry->tag_id = $tagMap->tag_id;
                      $newTagmapEntry->creation_date = $tagMap->creation_date;
                      $newTagmapEntry->extra = $tagMap->extra;
                      $newTagmapEntry->save();
                    }
                    //END FETCH TAGGER DETAIL
                  }
                }

                $fetchDefaultAlbum = $albumTable->fetchRow(array('store_id = ?' => $sitestore->store_id, 'default_value = ?' => 1));
                if (!empty($fetchDefaultAlbum)) {

                  $order = 999;
                  foreach ($listPhotoDatas as $listPhotoData) {

                    if ($listPhotoData['photo_id'] != $listing->photo_id) {
                      $params = array(
                          'collection_id' => $fetchDefaultAlbum->album_id,
                          'album_id' => $fetchDefaultAlbum->album_id,
                          'store_id' => $sitstore->store_id,
                          'user_id' => $listPhotoData['user_id'],
                          'order' => $order,
                      );

                      $storageData = $storageTable->fetchRow(array('file_id = ?' => $listPhotoData['file_id']));
                      if (!empty($storageData)) {
                        $file = array();
                        $file['tmp_name'] = $storageData->storage_path;
                        $path_array = explode('/', $file['tmp_name']);
                        $file['name'] = end($path_array);
												$storePhoto = Engine_Api::_()->getDbtable('photos', 'sitestore')->createPhoto($params, $file);
                        if (!empty($storePhoto)) {

                          $order++;

                          //START FETCH LIKES
                          $selectLike = $likeTable->select()
                                          ->from($likeTableName, 'like_id')
                                          ->where('resource_type = ?', 'list_photo')
                                          ->where('resource_id = ?', $listPhotoData['photo_id']);
                          $selectLikeDatas = $likeTable->fetchAll($selectLike);
                          foreach ($selectLikeDatas as $selectLikeData) {
                            $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);

                            $newLikeEntry = $likeTable->createRow();
                            $newLikeEntry->resource_type = 'sitestore_photo';
                            $newLikeEntry->resource_id = $storePhoto->photo_id;
                            $newLikeEntry->poster_type = 'user';
                            $newLikeEntry->poster_id = $like->poster_id;
                            $newLikeEntry->creation_date = $like->creation_date;
                            $newLikeEntry->save();
                          }
                          //END FETCH LIKES

                          //START FETCH COMMENTS
                          $selectLike = $commentTable->select()
                                          ->from($commentTableName, 'comment_id')
                                          ->where('resource_type = ?', 'list_photo')
                                          ->where('resource_id = ?', $listPhotoData['photo_id']);
                          $selectLikeDatas = $commentTable->fetchAll($selectLike);
                          foreach ($selectLikeDatas as $selectLikeData) {
                            $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

                            $newLikeEntry = $commentTable->createRow();
                            $newLikeEntry->resource_type = 'sitestore_photo';
                            $newLikeEntry->resource_id = $storePhoto->photo_id;
                            $newLikeEntry->poster_type = 'user';
                            $newLikeEntry->poster_id = $comment->poster_id;
                            $newLikeEntry->body = $comment->body;
                            $newLikeEntry->creation_date = $comment->creation_date;
                            $newLikeEntry->like_count = $comment->like_count;
                            $newLikeEntry->save();
                          }
                          //END FETCH COMMENTS

                          //START FETCH TAGGER DETAIL
                          $selectTagmaps = $tagmapsTable->select()
                                          ->from($tagmapsTableName, 'tagmap_id')
                                          ->where('resource_type = ?', 'list_photo')
                                          ->where('resource_id = ?', $listPhotoData['photo_id']);
                          $selectTagmapsDatas = $tagmapsTable->fetchAll($selectTagmaps);
                          foreach ($selectTagmapsDatas as $selectTagmapsData) {
                            $tagMap = Engine_Api::_()->getItem('core_tag_map', $selectTagmapsData->tagmap_id);

                            $newTagmapEntry = $tagmapsTable->createRow();
                            $newTagmapEntry->resource_type = 'sitestore_photo';
                            $newTagmapEntry->resource_id = $storePhoto->photo_id;
                            $newTagmapEntry->tagger_type = 'user';
                            $newTagmapEntry->tagger_id = $tagMap->tagger_id;
                            $newTagmapEntry->tag_type = 'user';
                            $newTagmapEntry->tag_id = $tagMap->tag_id;
                            $newTagmapEntry->creation_date = $tagMap->creation_date;
                            $newTagmapEntry->extra = $tagMap->extra;
                            $newTagmapEntry->save();
                          }
                          //END FETCH TAGGER DETAIL
                        }
                      }
                    }
                  }
                }
              }
            }
            //END FETCH PHOTO DATA

            //START FETCH SITESTORE-FORM DATA
            $sitestoreFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreform');
            if ($sitestoreFormEnabled) {
              $sitestoreform = $table_option->createRow();
              $sitestoreform->label = $sitstore->title;
              $sitestoreform->field_id = 1;
              $option_id = $sitestoreform->save();
              $optionids = $optionid->createRow();
              $optionids->option_id = $option_id;
              $optionids->store_id = $sitstore->store_id;
              $optionids->save();
              $sitestoreforms = $sitestoreformtable->createRow();
              $sitestoreforms->store_id = $sitstore->store_id;
              $sitestoreforms->save();
            }
            //END FETCH SITESTORE-FORM DATA

            //START FETCH engine4_list_writes DATA
            $writeData = $writeTable->fetchRow(array('listing_id = ?' => $listing_id));
            if (!empty($writeData)) {
              $write = $storeWriteTable->createRow();
              $write->store_id = $sitestore->store_id;
              $write->text = $writeData->text;
              $write->save();
            }
            //END FETCH engine4_list_writes DATA

            //START FETCH VIDEO DATA
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo')) {

              $selectListVideos = $listVideoTable->select()
                              ->from($listVideoTableName, 'video_id')
                              ->where('listing_id = ?', $listing_id)
                              ->group('video_id');
              $listVideoDatas = $listVideoTable->fetchAll($selectListVideos);
              foreach ($listVideoDatas as $listVideoData) {
                $listVideo = Engine_Api::_()->getItem('video', $listVideoData->video_id);
                if (!empty($listVideo)) {
                  $db = $storeVideoTable->getAdapter();
                  $db->beginTransaction();

                  try {
                    $storeVideo = $storeVideoTable->createRow();
                    $storeVideo->store_id = $sitestore->store_id;
                    $storeVideo->title = $listVideo->title;
                    $storeVideo->description = $listVideo->description;
                    $storeVideo->search = $listVideo->search;
                    $storeVideo->owner_id = $listVideo->owner_id;
                    $storeVideo->creation_date = $listVideo->creation_date;
                    $storeVideo->modified_date = $listVideo->modified_date;

                    $storeVideo->view_count = 1;
                    if ($listVideo->view_count > 0) {
                      $storeVideo->view_count = $listVideo->view_count;
                    }

                    $storeVideo->comment_count = $listVideo->comment_count;
                    $storeVideo->type = $listVideo->type;
                    $storeVideo->code = $listVideo->code;
                    $storeVideo->rating = $listVideo->rating;
                    $storeVideo->status = $listVideo->status;
                    $storeVideo->featured = 0;
                    $storeVideo->file_id = 0;
                    $storeVideo->duration = $listVideo->duration;
                    $storeVideo->save();
                    $db->commit();
                  } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                  }

                  //START VIDEO THUMB WORK
                  if (!empty($storeVideo->code) && !empty($storeVideo->type) && !empty($listVideo->photo_id)) {
                    $storageData = $storageTable->fetchRow(array('file_id = ?' => $listVideo->photo_id));
                    if (!empty($storageData)) {
                      $thumbnail = $storageData->storage_path;

                      $ext = ltrim(strrchr($thumbnail, '.'), '.');
                      $thumbnail_parsed = @parse_url($thumbnail);

                      if (@GetImageSize($thumbnail)) {
                        $valid_thumb = true;
                      } else {
                        $valid_thumb = false;
                      }

                      if ($valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
                        $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
                        $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;
                        $src_fh = fopen($thumbnail, 'r');
                        $tmp_fh = fopen($tmp_file, 'w');
                        stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);
                        $image = Engine_Image::factory();
                        $image->open($tmp_file)
                                ->resize(120, 240)
                                ->write($thumb_file)
                                ->destroy();

                        try {
                          $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
                                      'parent_type' => 'sitestorevideo_video',
                                      'parent_id' => $storeVideo->video_id
                                  ));

                          //REMOVE TEMP FILE
                          @unlink($thumb_file);
                          @unlink($tmp_file);
                        } catch (Exception $e) {
                          
                        }

                        $storeVideo->photo_id = $thumbFileRow->file_id;
                        $storeVideo->save();
                      }
                    }
                  }
                  //END VIDEO THUMB WORK

                  //START FETCH TAG
                  $videoTags = $listVideo->tags()->getTagMaps();
                  $tagString = '';

                  foreach ($videoTags as $tagmap) {

                    if ($tagString != '')
                      $tagString .= ', ';
                    $tagString .= $tagmap->getTag()->getTitle();

                    $owner = Engine_Api::_()->getItem('user', $listVideo->owner_id);
                    $tags = preg_split('/[,]+/', $tagString);
                    $tags = array_filter(array_map("trim", $tags));
                    $storeVideo->tags()->setTagMaps($owner, $tags);
                  }
                  //END FETCH TAG

                  //START FETCH LIKES
                  $selectLike = $likeTable->select()
                                  ->from($likeTableName, 'like_id')
                                  ->where('resource_type = ?', 'video')
                                  ->where('resource_id = ?', $listVideoData->video_id);
                  $selectLikeDatas = $likeTable->fetchAll($selectLike);
                  foreach ($selectLikeDatas as $selectLikeData) {
                    $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);

                    $newLikeEntry = $likeTable->createRow();
                    $newLikeEntry->resource_type = 'sitestorevideo_video';
                    $newLikeEntry->resource_id = $storeVideo->video_id;
                    $newLikeEntry->poster_type = 'user';
                    $newLikeEntry->poster_id = $like->poster_id;
                    $newLikeEntry->creation_date = $like->creation_date;
                    $newLikeEntry->save();
                  }
                  //END FETCH LIKES

                  //START FETCH COMMENTS
                  $selectLike = $commentTable->select()
                                  ->from($commentTableName, 'comment_id')
                                  ->where('resource_type = ?', 'video')
                                  ->where('resource_id = ?', $listVideoData->video_id);
                  $selectLikeDatas = $commentTable->fetchAll($selectLike);
                  foreach ($selectLikeDatas as $selectLikeData) {
                    $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

                    $newLikeEntry = $commentTable->createRow();
                    $newLikeEntry->resource_type = 'sitestorevideo_video';
                    $newLikeEntry->resource_id = $storeVideo->video_id;
                    $newLikeEntry->poster_type = 'user';
                    $newLikeEntry->poster_id = $comment->poster_id;
                    $newLikeEntry->body = $comment->body;
                    $newLikeEntry->creation_date = $comment->creation_date;
                    $newLikeEntry->like_count = $comment->like_count;
                    $newLikeEntry->save();
                  }
                  //END FETCH COMMENTS

                  //START UPDATE TOTAL LIKES IN STORE-VIDEO TABLE
                  $selectLikeCount = $likeTable->select()
                                  ->from($likeTableName, array('COUNT(*) AS like_count'))
                                  ->where('resource_type = ?', 'sitestorevideo_video')
                                  ->where('resource_id = ?', $storeVideo->video_id);
                  $selectLikeCounts = $likeTable->fetchAll($selectLikeCount);
                  if (!empty($selectLikeCounts)) {
                    $selectLikeCounts = $selectLikeCounts->toArray();
                    $storeVideo->like_count = $selectLikeCounts[0]['like_count'];
                    $storeVideo->save();
                  }
                  //END UPDATE TOTAL LIKES IN STORE-VIDEO TABLE

                  //START FETCH RATTING DATA
                  $selectVideoRating = $listVideoRating->select()
                                  ->from($listVideoRatingName)
                                  ->where('video_id = ?', $listVideoData->video_id);

                  $listVideoRatingDatas = $listVideoRating->fetchAll($selectVideoRating);
                  if (!empty($listVideoRatingDatas)) {
                    $listVideoRatingDatas = $listVideoRatingDatas->toArray();
                  }

                  foreach ($listVideoRatingDatas as $listVideoRatingData) {

                    $storeVideoRatingTable->insert(array(
                        'video_id' => $storeVideo->video_id,
                        'user_id' => $listVideoRatingData['user_id'],
                        'store_id' => $storeVideo->store_id,
                        'rating' => $listVideoRatingData['rating']
                    ));
                  }
                  //END FETCH RATTING DATA
                }
              }
              //END FETCH VIDEO DATA
            }
          }

          $this->view->assigned_previous_id = $listing_id;

          //CREATE LOG ENTRY IN LOG FILE
          if (file_exists(APPLICATION_PATH . '/temporary/log/ListingToStoreImport.log')) {
            $myFile = APPLICATION_PATH . '/temporary/log/ListingToStoreImport.log';
						$error = Zend_Registry::get('Zend_Translate')->_("can't open file");
            $fh = fopen($myFile, 'a') or die($error);
            $current_time = date('D, d M Y H:i:s T');
            $store_id = $sitestore->store_id;
            $store_title = $sitestore->title;
            $stringData = $this->view->translate('Listing with ID ').$listing_id.$this->view->translate(' is successfully imported into a Store with ID ').$store_id.$this->view->translate(' at ').$current_time.$this->view->translate(". Title of that Store is '").$store_title."'.\n\n";
            fwrite($fh, $stringData);
            fclose($fh);
          }

					if ($next_import_count >= 100) {
						$this->_redirect("admin/sitestore/importlisting/index?start_import=1");
					}
					
        }
      }
    }
  }

  //ACTION FOR IMPORTING DATA FROM CSV FILE
  public function importAction() {

    //INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
    ini_set('memory_limit', '2048M');
    set_time_limit(0);

    $this->_helper->layout->setLayout('admin-simple');

    //MAKE FORM
    $this->view->form = $form = new Sitestore_Form_Admin_Import_Import();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //MAKE SURE THAT FILE EXTENSION SHOULD NOT DIFFER FROM ALLOWED TYPE
      $ext = str_replace(".", "", strrchr($_FILES['filename']['name'], "."));
      if (!in_array($ext, array('csv', 'CSV'))) {
        $error = $this->view->translate("Invalid file extension. Only 'csv' extension is allowed.");
        $error = Zend_Registry::get('Zend_Translate')->_($error);

        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }

      //START READING DATA FROM CSV FILE
      $fname = $_FILES['filename']['tmp_name'];
      $fp = fopen($fname, "r");

      if (!$fp) {
        echo "$fname File opening error";
        exit;
      }
			
			$formData = array();
			$formData = $form->getValues();

			if($formData['import_seperate'] == 1) {
				while ($buffer = fgets($fp, 4096)) {
					$explode_array[] = explode('|', $buffer);
				}
			}
			else {
				while ($buffer = fgets($fp, 4096)) {
					$explode_array[] = explode(',', $buffer);
				}
			}
      //END READING DATA FROM CSV FILE

      $import_count = 0;
      foreach ($explode_array as $explode_data) {

        //GET STORE DETAILS FROM DATA ARRAY
        $values = array();
        $values['title'] = trim($explode_data[0]);
        $values['store_url'] = trim($explode_data[1]);
        $values['category'] = trim($explode_data[2]);
        $values['sub_category'] = trim($explode_data[3]);
        $values['body'] = trim($explode_data[4]);
        $values['price'] = trim($explode_data[5]);
        $values['location'] = trim($explode_data[6]);
        $values['overview'] = trim($explode_data[7]);
        $values['tags'] = trim($explode_data[8]);
        $values['email'] = trim($explode_data[9]);
        $values['website'] = trim($explode_data[10]);
        $values['phone'] = trim($explode_data[11]);
        $values['userclaim'] = trim($explode_data[12]);

        //IF STORE TITLE AND CATEGORY IS EMPTY THEN CONTINUE;
        if (empty($values['title']) || empty($values['category'])) {
          continue;
        }

        $db = Engine_Api::_()->getDbtable('imports', 'sitestore')->getAdapter();
        $db->beginTransaction();

        try {
          $import = Engine_Api::_()->getDbtable('imports', 'sitestore')->createRow();
          $import->setFromArray($values);
          $import->save();

          //COMMIT
          $db->commit();

          if (empty($import_count)) {
            $first_import_id = $last_import_id = $import->import_id;

            //SAVE DATA IN `engine4_sitestore_importfiles` TABLE
            $db = Engine_Api::_()->getDbtable('importfiles', 'sitestore')->getAdapter();
            $db->beginTransaction();

            try {

              //FETCH PRIVACY

              if (empty($formData['auth_view'])) {
                $formData['auth_view'] = "everyone";
              }

              if (empty($formData['auth_comment'])) {
                $formData['auth_comment'] = "everyone";
              }

              //SAVE OTHER DATA IN engine4_sitestore_importfiles TABLE
              $importFile = Engine_Api::_()->getDbtable('importfiles', 'sitestore')->createRow();
              $importFile->filename = $_FILES['filename']['name'];
              $importFile->status = 'Pending';
              $importFile->first_import_id = $first_import_id;
              $importFile->last_import_id = $last_import_id;
              $importFile->current_import_id = $first_import_id;
              $importFile->first_store_id = 0;
              $importFile->last_store_id = 0;
              $importFile->view_privacy = $formData['auth_view'];
              $importFile->comment_privacy = $formData['auth_comment'];
              $importFile->save();

              //COMMIT
              $db->commit();
            } catch (Exception $e) {
              $db->rollBack();
              throw $e;
            }
          } else {

            //UPDATE LAST IMPORT ID
            $last_import_id = $import->import_id;
            $importFile->last_import_id = $last_import_id;
            $importFile->save();
          }

          $import_count++;
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
      }

      //CLOSE THE SMOOTHBOX
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRedirect' => $this->_helper->url->url(array('module' => 'sitestore', 'controller' => 'admin-importlisting', 'action' => 'manage')),
          'parentRedirectTime' => '15',
          'format' => 'smoothbox',
					'messages' => array(Zend_Registry::get('Zend_Translate')->_('CSV file has been imported succesfully !'))
      ));
    }
  }

  //ACTION FOR IMPORTING DATA FROM CSV FILE
  public function dataImportAction() {

    //INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
    ini_set('memory_limit', '2048M');
    set_time_limit(0);

    $this->_helper->layout->setLayout('admin-simple');
    $this->view->importfile_id = $importfile_id = $this->_getParam('importfile_id');
		$current_import = $this->_getParam('current_import');

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //RETURN IF importfile_id IS EMPTY
    if (empty($importfile_id)) {
      return;
    }

    //GET IMPORT FILE OBJECT
    $importFile = Engine_Api::_()->getItem('sitestore_importfile', $importfile_id);
    if (empty($importFile)) {
      return;
    }

		//CHECK IF IMPORT WORK IS ALREADY IN RUNNING STATUS FOR SOME FILE
		$tableImportFile = Engine_Api::_()->getDbTable('importfiles', 'sitestore');
		$importFileStatusData = $tableImportFile->fetchRow(array('status = ?' => 'Running'));
		if(!empty($importFileStatusData) && empty($current_import)) {
			return;
		}

		//UPDATE THE STATUS
		$importFile->status = 'Running';
		$importFile->save();

    $first_import_id = $importFile->first_import_id;
    $last_import_id = $importFile->last_import_id;

    $current_import_id = $importFile->current_import_id;
    $return_current_import_id = $this->_getParam('current_import_id');
    if (!empty($return_current_import_id)) {
      $current_import_id = $this->_getParam('current_import_id');
    }

    //MAKE QUERY
    $tableImport = Engine_Api::_()->getDbtable('imports', 'sitestore');

    $sqlStr = "import_id BETWEEN " . "'" . $current_import_id . "'" . " AND " . "'" . $last_import_id . "'" . "";

    $select = $tableImport->select()
                    ->from($tableImport->info('name'), array('import_id'))
                    ->where($sqlStr);
    $importDatas = $select->query()->fetchAll();

    if (empty($importDatas)) {
      return;
    }

    //START CODE FOR CREATING THE ListingToStoreImport.log FILE
    if (!file_exists(APPLICATION_PATH . '/temporary/log/CSVToStoreImport.log')) {
      $log = new Zend_Log();
      try {
        $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/CSVToStoreImport.log'));
      } catch (Exception $e) {
        //CHECK DIRECTORY
        if (!@is_dir(APPLICATION_PATH . '/temporary/log') &&
                @mkdir(APPLICATION_PATH . '/temporary/log', 0777, true)) {
          $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/CSVToStoreImport.log'));
        } else {
          //Silence ...
          if (APPLICATION_ENV !== 'production') {
            $log->log($e->__toString(), Zend_Log::CRIT);
          } else {
            //MAKE SURE LOGGING DOESN'T CAUSE EXCEPTIONS
            $log->addWriter(new Zend_Log_Writer_Null());
          }
        }
      }
    }

    //GIVE WRITE PERMISSION TO LOG FILE IF EXIST
    if (file_exists(APPLICATION_PATH . '/temporary/log/CSVToStoreImport.log')) {
      @chmod(APPLICATION_PATH . '/temporary/log/CSVToStoreImport.log', 0777);
    }
    //END CODE FOR CREATING THE CSVToStoreImport.log FILE
    //START COLLECTING COMMON DATAS
    $package_id = Engine_Api::_()->getItemtable('sitestore_package')->fetchRow(array('defaultpackage = ?' => 1))->package_id;
    $package = Engine_Api::_()->getItemTable('sitestore_package')->fetchRow(array('package_id = ?' => $package_id));
    $table = Engine_Api::_()->getItemTable('sitestore_store');
    $storeCategoryTable = Engine_Api::_()->getDbtable('categories', 'sitestore');
    $storeAdminTable = Engine_Api::_()->getDbtable('pages', 'core');
    $storeAdminTableName = $storeAdminTable->info('name');
    $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitestore');
    $storeTable = Engine_Api::_()->getDbtable('contentstores', 'sitestore');
    $albumTable = Engine_Api::_()->getDbtable('albums', 'sitestore');
    //END COLLECTING COMMON DATAS

    $import_count = 0;

    //START THE IMPORT WORK
    foreach ($importDatas as $importData) {

      //GET IMPORT FILE OBJECT
      $importFile = Engine_Api::_()->getItem('sitestore_importfile', $importfile_id);

      //BREAK IF STATUS IS STOP
      if ($importFile->status == 'Stopped') {
        break;
      }

      $import_id = $importData['import_id'];
      if (empty($import_id)) {
        continue;
      }

      $import = Engine_Api::_()->getItem('sitestore_import', $import_id);
      if (empty($import)) {
        continue;
      }

      //GET STORE DETAILS FROM DATA ARRAY
      $values = array();
      $values['title'] = $import->title;
      $store_url = $import->store_url;
      $store_category = $import->category;
      $store_subcategory = $import->sub_category;
      $values['body'] = $import->body;
      $values['price'] = $import->price;
      $values['location'] = $import->location;
      $values['overview'] = $import->overview;
      $store_tags = $import->tags;
      $values['userclaim'] = $import->userclaim;
      $values['email'] = $import->email;
      $values['website'] = $import->website;
      $values['phone'] = $import->phone;
      $values['owner_type'] = $viewer->getType();
      $values['owner_id'] = $viewer->getIdentity();
      $values['package_id'] = $package_id;

      //IF STORE TITLE AND CATEGORY IS EMPTY THEN CONTINUE;
      if (empty($values['title']) || empty($store_category)) {
        continue;
      }

      $db = $table->getAdapter();
      $db->beginTransaction();

      try {

        $sitestore = $table->createRow();
        $sitestore->setFromArray($values);

        $sitestore->pending = 0;
        $sitestore->approved = 1;
        $sitestore->aprrove_date = date('Y-m-d H:i:s');

        if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
          $expirationDate = $package->getExpirationDate();
          if (!empty($expirationDate))
            $sitestore->expiration_date = date('Y-m-d H:i:s', $expirationDate);
          else
            $sitestore->expiration_date = '2250-01-01 00:00:00';
        }
        else {
          $sitestore->expiration_date = '2250-01-01 00:00:00';
        }

        $sitestore->view_count = 1;
        $sitestore->save();
        $store_id = $sitestore->store_id;

        $importFile->current_import_id = $import->import_id;
        $importFile->last_store_id = $store_id;
        $importFile->save();

        if (empty($importFile->first_store_id)) {
          $importFile->first_store_id = $store_id;
          $importFile->save();
        }
        $import_count++;

        //START CREATE NEW STORE URL
        if (empty($store_url)) {
          $store_url = $values['title'];
        }
        
				$store_url = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($store_url))), '-');
				
        $store = $table->fetchRow(array('store_url = ?' => $store_url));
        if (!empty($store)) {
          $sitestore->store_url = $store_url . $sitestore->store_id;
        } else {
          $sitestore->store_url = $store_url;
        }
        //END CREATE NEW STORE URL

        $sitestore->store_url = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($sitestore->store_url))), '-');
       
        //START CATEGORY WORK
        $storeCategory = $storeCategoryTable->fetchRow(array('category_name = ?' => $store_category, 'cat_dependency = ?' => 0));
        if (!empty($storeCategory)) {
          $sitestore->category_id = $storeCategory->category_id;

          $storeSubcategory = $storeCategoryTable->fetchRow(array('category_name = ?' => $store_subcategory, 'cat_dependency = ?' => $sitestore->category_id));

          if (!empty($storeSubcategory)) {
            $sitestore->subcategory_id = $storeSubcategory->category_id;
          }

          //START PROFILE MAPS WORK
          Engine_Api::_()->getDbtable('profilemaps', 'sitestore')->profileMapping($sitestore);
        }
        //END CATEGORY WORK
        
         $sitestore->save();
         
        //SAVE TAGS
        $tags = preg_split('/[#]+/', $store_tags);
        $tags = array_filter(array_map("trim", $tags));
        $sitestore->tags()->addTagMaps($viewer, $tags);

        //PUT STORE OWNER IN MANAGE ADMIN TABLE
        $row = $manageadminsTable->createRow();
        $row->user_id = $sitestore->owner_id;
        $row->store_id = $sitestore->store_id;
        $row->save();

        //DEFAULT ENTRIES FOR SITEAPGE-FORM
        $store_id = $sitestore->store_id;
        $sitestoreFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreform');
        if ($sitestoreFormEnabled) {

          $sitestoreformtable = Engine_Api::_()->getDbtable('sitestoreforms', 'sitestoreform');
          $optionid = Engine_Api::_()->getDbtable('storequetions', 'sitestoreform');
          $table_option = Engine_Api::_()->fields()->getTable('sitestoreform', 'options');

          $sitestoreform = $table_option->createRow();
          $sitestoreform->label = $values['title'];
          $sitestoreform->field_id = 1;
          $option_id = $sitestoreform->save();
          $optionids = $optionid->createRow();
          $optionids->option_id = $option_id;
          $optionids->store_id = $store_id;
          $optionids->save();
          $sitestoreforms = $sitestoreformtable->createRow();
          $sitestoreforms->store_id = $store_id;
          $sitestoreforms->save();
        }

        //SET PHOTO
        $album_id = $albumTable->insert(array(
                    'photo_id' => 0,
                    'owner_id' => $sitestore->owner_id,
                    'store_id' => $sitestore->store_id,
                    'title' => $sitestore->title,
                    'creation_date' => $sitestore->creation_date,
                    'modified_date' => $sitestore->modified_date));

        $sitestore->setLocation();

        //SET PRIVACY
        $auth = Engine_Api::_()->authorization()->context;
        $sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
        if (!empty($sitestorememberEnabled)) {
          $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        } else {
          $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        }
        $privacyMax = array_search('everyone', $roles);

        if (empty($importFile->view_privacy)) {
          $importFile->view_privacy = "everyone";
        }

        if (empty($importFile->comment_privacy)) {
          $importFile->comment_privacy = "everyone";
        }

        $viewMax = array_search($importFile->view_privacy, $roles);
        $commentMax = array_search($importFile->comment_privacy, $roles);

        foreach ($roles as $i => $role) {
          $auth->setAllowed($sitestore, $role, 'view', ($i <= $viewMax));
          $auth->setAllowed($sitestore, $role, 'comment', ($i <= $commentMax));
          $auth->setAllowed($sitestore, $role, 'print', ($i <= $privacyMax));
          $auth->setAllowed($sitestore, $role, 'tfriend', ($i <= $privacyMax));
          $auth->setAllowed($sitestore, $role, 'overview', ($i <= $privacyMax));
          $auth->setAllowed($sitestore, $role, 'map', ($i <= $privacyMax));
          $auth->setAllowed($sitestore, $role, 'insight', ($i <= $privacyMax));
          $auth->setAllowed($sitestore, $role, 'layout', ($i <= $privacyMax));
          $auth->setAllowed($sitestore, $role, 'contact', ($i <= $privacyMax));
          $auth->setAllowed($sitestore, $role, 'form', ($i <= $privacyMax));
          $auth->setAllowed($sitestore, $role, 'offer', ($i <= $privacyMax));
          $auth->setAllowed($sitestore, $role, 'invite', ($i <= $privacyMax));
        }

        $sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
        if (!empty($sitestorememberEnabled)) {
          $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
        } else {
          $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
        }

        $createMax = array_search("owner", $roles);

        //START SITESTOREDICUSSION PLUGIN WORK
        $sitestorediscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion');
        if ($sitestorediscussionEnabled) {
          foreach ($roles as $i => $role) {
            $auth->setAllowed($sitestore, $role, 'sdicreate', ($i <= $createMax));
          }
        }
        //END SITESTOREDICUSSION PLUGIN WORK        
        
        //START SITESTOREALBUM PLUGIN WORK
        $sitestorealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum');
        if ($sitestorealbumEnabled) {
          foreach ($roles as $i => $role) {
            $auth->setAllowed($sitestore, $role, 'spcreate', ($i <= $createMax));
          }
        }
        //END SITESTOREALBUM PLUGIN WORK
        //START SITESTOREDOCUMENT PLUGIN WORK
        $sitestoreDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument');
        if ($sitestoreDocumentEnabled) {
          foreach ($roles as $i => $role) {
            $auth->setAllowed($sitestore, $role, 'sdcreate', ($i <= $createMax));
          }
        }
        //END SITESTOREDOCUMENT PLUGIN WORK
        //START SITESTOREVIDEO PLUGIN WORK
        $sitestoreVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo');
        if ($sitestoreVideoEnabled) {
          foreach ($roles as $i => $role) {
            $auth->setAllowed($sitestore, $role, 'svcreate', ($i <= $createMax));
          }
        }
        //END SITESTOREVIDEO PLUGIN WORK
        //START SITESTOREPOLL PLUGIN WORK
        $sitestorePollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorepoll');
        if ($sitestorePollEnabled) {
          foreach ($roles as $i => $role) {
            $auth->setAllowed($sitestore, $role, 'splcreate', ($i <= $createMax));
          }
        }
        //END SITESTOREPOLL PLUGIN WORK
        //START SITESTORENOTE PLUGIN WORK
        $sitestoreNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorenote');
        if ($sitestoreNoteEnabled) {
          foreach ($roles as $i => $role) {
            $auth->setAllowed($sitestore, $role, 'sncreate', ($i <= $createMax));
          }
        }
        //END SITESTORENOTE PLUGIN WORK
        //START SITESTOREEVENT PLUGIN WORK
        $sitestoreEventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent');
        if ($sitestoreEventEnabled) {
          foreach ($roles as $i => $role) {
            $auth->setAllowed($sitestore, $role, 'secreate', ($i <= $createMax));
          }
        }
        //END SITESTOREEVENT PLUGIN WORK
        //Commit
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

//       //EXTRACTING CURRENT ADMIN SETTINGS FOR THIS VIEW STORE.
//       $selectStoreAdmin = $storeAdminTable->select()
//                       ->setIntegrityCheck(false)
//                       ->from($storeAdminTableName)
//                       ->where('name = ?', 'sitestore_index_view');
//       $storeAdminresult = $storeAdminTable->fetchRow($selectStoreAdmin);
// 
//       //NOW INSERTING THE ROW IN STORE TABLE
//       $storeObject = $storeTable->createRow();
//       $storeObject->displayname = ( null !== ($name = $values['title']) ? $name : 'Untitled' );
//       $storeObject->title = ( null !== ($name = $values['title']) ? $name : 'Untitled' );
//       $storeObject->description = $values['body'];
//       $storeObject->name = "sitestore_index_view";
//       $storeObject->url = $storeAdminresult->url;
//       $storeObject->custom = $storeAdminresult->custom;
//       $storeObject->fragment = $storeAdminresult->fragment;
//       $storeObject->keywords = $storeAdminresult->keywords;
//       $storeObject->layout = $storeAdminresult->layout;
//       $storeObject->view_count = $storeAdminresult->view_count;
//       $storeObject->user_id = $values['owner_id'];
//       $storeObject->store_id = $store_id;
//       $contentStoreId = $storeObject->save();
// 
//       //NOW FETCHING STORE CONTENT DEFAULT SETTING INFORMATION FROM CORE CONTENT TABLE FOR THIS STORE.
//       $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
//       if (!$layout) {
//         Engine_Api::_()->getDbtable('content', 'sitestore')->setContentDefault($contentStoreId);
//       } else {
//         Engine_Api::_()->getApi('layoutcore', 'sitestore')->setContentDefaultLayout($contentStoreId);
//       }

      //IF ALL STORES HAS BEEN IMPORTED THAN CHANGE THE STATUS
      if ($importFile->current_import_id == $importFile->last_import_id) {
        $importFile->status = 'Completed';
      }
      $importFile->save();

      //CREATE LOG ENTRY IN LOG FILE
      if (file_exists(APPLICATION_PATH . '/temporary/log/CSVToStoreImport.log')) {

				$stringData = '';
				if($import_count == 1) {
					$stringData .= "\n\n----------------------------------------------------------------------------------------------------------------\n";
					$stringData .= $this->view->translate("Import History of '").$importFile->filename.$this->view->translate("' with file id: ").$importFile->importfile_id.$this->view->translate(", created on ").$importFile->creation_date.$this->view->translate(" is given below.");
					$stringData .= "\n----------------------------------------------------------------------------------------------------------------\n\n";
				}
				
        $myFile = APPLICATION_PATH . '/temporary/log/CSVToStoreImport.log';
        $fh = fopen($myFile, 'a') or die("can't open file");
        $current_time = date('D, d M Y H:i:s T');
        $store_id = $sitestore->store_id;
        $store_title = $sitestore->title;
        $stringData .= $this->view->translate("Successfully created a new store at ").$current_time.$this->view->translate(". ID and title of that Store are ").$store_id.$this->view->translate(" and '").$store_title.$this->view->translate("' respectively.")."\n\n";
        fwrite($fh, $stringData);
        fclose($fh);
      }

      if ($import_count >= 100) {
        $current_import_id = $importFile->current_import_id + 1;
        $this->_redirect("admin/sitestore/importlisting/data-import?importfile_id=$importfile_id&current_import_id=$current_import_id&current_import=1");
      }
    }

    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
				'messages' => array(Zend_Registry::get('Zend_Translate')->_('Importing is done successfully !'))
    ));
  }

  //ACTION FOR MANAGING THE CSV FILES DATAS
  public function manageAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_import');

    //FORM CREATION FOR SORTING
    $this->view->formFilter = $formFilter = new Sitestore_Form_Admin_Import_Filter();
    $store = $this->_getParam('page', 1);

    $tableImportFile = Engine_Api::_()->getDbTable('importfiles', 'sitestore');
    $select = $tableImportFile->select();

		//IF IMPORT IS IN RUNNING STATUS FOR SOME FILE THAN DONT SHOW THE START BUTTON FOR ALL
		$importFileStatusData = $tableImportFile->fetchRow(array('status = ?' => 'Running'));
		$this->view->runningSomeImport = 0;
		if(!empty($importFileStatusData)) {
			$this->view->runningSomeImport = 1;
		}

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
                'order' => 'importfile_id',
                'order_direction' => 'DESC',
                    ), $values);

    $this->view->assign($values);

    $select->order((!empty($values['order']) ? $values['order'] : 'importfile_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->total_slideshows = $paginator->getTotalItemCount();
    $this->view->paginator->setItemCountPerPage(100);
    $this->view->paginator = $paginator->setCurrentPagenumber($store);
  }

  //ACTION FOR STOP IMPORTING DATA
  public function stopAction() {

    //UPDATE THE STATUS TO STOP
    $importfile_id = $this->_getParam('importfile_id');
    $importFile = Engine_Api::_()->getItem('sitestore_importfile', $importfile_id);
    $importFile->status = 'Stopped';
    $importFile->save();

    //REDIRECTING TO MANAGE STORE IF FORCE STOP
    $forceStop = $this->_getParam('forceStop');
    if (!empty($forceStop)) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
    }
  }

  //ACTION FOR ROLLBACK IMPORTING DATA
  public function rollbackAction() {

    //INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
    ini_set('memory_limit', '2048M');
    set_time_limit(0);

    $this->_helper->layout->setLayout('admin-simple');
    $this->view->importfile_id = $importfile_id = $this->_getParam('importfile_id');

    //FETCH IMPORT FILE OBJECT
    $importFile = Engine_Api::_()->getItem('sitestore_importfile', $importfile_id);

    //IF STATUS IS PENDING THAN RETURN
    if ($importFile->status == 'Pending') {
      return;
    }

    $returend_current_store_id = $this->_getParam('current_store_id');
		$redirect = 0;
		if(isset($_GET['redirect'])) {
			$redirect = $_GET['redirect'];
		}

		if(empty($redirect) && isset($_POST['redirect'])) {
			$redirect = $_POST['redirect'];
		}

    //START ROLLBACK IF CONFIRM BY USER OR RETURNED CURRENT STORE ID IS NOT EMPTY
    if (!empty($redirect)) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        $first_store_id = $importFile->first_store_id;
        $last_store_id = $importFile->last_store_id;

        if (!empty($first_store_id) && !empty($last_store_id)) {
          $tableStore = Engine_Api::_()->getDbtable('stores', 'sitestore');

          $current_store_id = $first_store_id;

          if (!empty($returend_current_store_id)) {
            $current_store_id = $returend_current_store_id;
          }

          //MAKE QUERY
          $sqlStr = "store_id BETWEEN " . "'" . $current_store_id . "'" . " AND " . "'" . $last_store_id . "'" . "";

          $select = $tableStore->select()
                          ->from($tableStore->info('name'), array('store_id'))
                          ->where($sqlStr);
          $storeDatas = $select->query()->fetchAll();

          if (!empty($storeDatas)) {
            $rollback_count = 0;
            foreach ($storeDatas as $storeData) {
              $store_id = $storeData['store_id'];

              //DELETE STORE
              Engine_Api::_()->sitestore()->onStoreDelete($store_id);

              $db->commit();

              $rollback_count++;

              //REDIRECTING TO SAME ACTION AFTER EVERY 100 ROLLBACKS
              if ($rollback_count >= 100) {
                $current_store_id = $store_id + 1;
                $this->_redirect("admin/sitestore/importlisting/rollback?importfile_id=$importfile_id&current_store_id=$current_store_id&redirect=1");
              }
            }
          }
        }
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      //UPDATE THE DATA IN engine4_sitestore_importfiles TABLE
      $importFile->status = 'Pending';
      $importFile->first_store_id = 0;
      $importFile->last_store_id = 0;
      $importFile->current_import_id = $importFile->first_import_id;
      $importFile->save();

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
					'messages' => array(Zend_Registry::get('Zend_Translate')->_('Rollbacked successfully !'))
      ));
    }
    $this->renderScript('admin-importlisting/rollback.tpl');
  }

  //ACTION FOR DELETE IMPORT FILES AND IMPORT DATA
  public function deleteAction() {
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->importfile_id = $importfile_id = $this->_getParam('importfile_id');

    //IF CONFIRM FOR DATA DELETION
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        //IMPORT FILE OBJECT
        $importFile = Engine_Api::_()->getItem('sitestore_importfile', $importfile_id);

        if (!empty($importFile)) {

          $first_import_id = $importFile->first_import_id;
          $last_import_id = $importFile->last_import_id;

          //MAKE QUERY FOR FETCH THE DATA
          $tableImport = Engine_Api::_()->getDbtable('imports', 'sitestore');

          $sqlStr = "import_id BETWEEN " . "'" . $first_import_id . "'" . " AND " . "'" . $last_import_id . "'" . "";

          $select = $tableImport->select()
                          ->from($tableImport->info('name'), array('import_id'))
                          ->where($sqlStr);
          $importDatas = $select->query()->fetchAll();

          if (!empty($importDatas)) {
            foreach ($importDatas as $importData) {
              $import_id = $importData['import_id'];

              //DELETE IMPORT DATA BELONG TO IMPORT FILE
              $tableImport->delete(array('import_id = ?' => $import_id));
            }
          }

          //FINALLY DELETE IMPORT FILE DATA
          Engine_Api::_()->getDbtable('importfiles', 'sitestore')->delete(array('importfile_id = ?' => $importfile_id));
        }

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
					'messages' => array(Zend_Registry::get('Zend_Translate')->_('Import data has been deleted successfully !'))
      ));
    }
    $this->renderScript('admin-importlisting/delete.tpl');
  }

  //ACTION FOR DELETE SLIDESHOW AND THEIR BELONGINGS
  public function multiDeleteAction() {
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();

      //IF ADMIN CLICK ON DELETE SELECTED BUTTON
      if (!empty($values['delete'])) {
        foreach ($values as $key => $value) {
          if ($key == 'delete_' . $value) {
            $importfile_id = (int) $value;
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
              //IMPORT FILE OBJECT
              $importFile = Engine_Api::_()->getItem('sitestore_importfile', $importfile_id);

              if (!empty($importFile)) {

                $first_import_id = $importFile->first_import_id;
                $last_import_id = $importFile->last_import_id;

                //MAKE QUERY FOR FETCH THE DATA
                $tableImport = Engine_Api::_()->getDbtable('imports', 'sitestore');

                $sqlStr = "import_id BETWEEN " . "'" . $first_import_id . "'" . " AND " . "'" . $last_import_id . "'" . "";

                $select = $tableImport->select()
                                ->from($tableImport->info('name'), array('import_id'))
                                ->where($sqlStr);
                $importDatas = $select->query()->fetchAll();

                if (!empty($importDatas)) {
                  foreach ($importDatas as $importData) {
                    $import_id = $importData['import_id'];

                    //DELETE IMPORT DATA BELONG TO IMPORT FILE
                    $tableImport->delete(array('import_id = ?' => $import_id));
                  }
                }

                //FINALLY DELETE IMPORT FILE DATA
                Engine_Api::_()->getDbtable('importfiles', 'sitestore')->delete(array('importfile_id = ?' => $importfile_id));
              }

              $db->commit();
            } catch (Exception $e) {
              $db->rollBack();
              throw $e;
            }
          }
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
  }

  //ACTION FOR DOWNLOADING THE CSV TEMPLATE FILE
  public function downloadAction() {
    //GET PATH
    $basePath = realpath(APPLICATION_PATH . "/application/modules/Sitestore/settings");

    $path = $this->_getPath();

    if (file_exists($path) && is_file($path)) {
      //KILL ZEND'S OB
      while (ob_get_level() > 0) {
        ob_end_clean();
      }

      header("Content-Disposition: attachment; filename=" . urlencode(basename($path)), true);
      header("Content-Transfer-Encoding: Binary", true);
      header("Content-Type: application/x-tar", true);
      header("Content-Type: application/force-download", true);
      header("Content-Type: application/octet-stream", true);
      header("Content-Type: application/download", true);
      header("Content-Description: File Transfer", true);
      header("Content-Length: " . filesize($path), true);
      readfile("$path");
    }

    exit();
  }

  protected function _getPath($key = 'path') {
    $basePath = realpath(APPLICATION_PATH . "/application/modules/Sitestore/settings");
    return $this->_checkPath($this->_getParam($key, ''), $basePath);
  }

  protected function _checkPath($path, $basePath) {
    //SANATIZE
    $path = preg_replace('/\.{2,}/', '.', $path);
    $path = preg_replace('/[\/\\\\]+/', '/', $path);
    $path = trim($path, './\\');
    $path = $basePath . '/' . $path;

    //Resolve
    $basePath = realpath($basePath);
    $path = realpath($path);

    //CHECK IF THIS IS A PARENT OF THE BASE PATH
    if ($basePath != $path && strpos($basePath, $path) !== false) {
      return $this->_helper->redirector->gotoRoute(array());
    }
    return $path;
  }

}
?>