<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: WishlistController.php 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_WishlistController extends Siteapi_Controller_Action_Standard {

    //COMMON FUNCTION WHICH CALL AUTOMATICALLY BEFORE EVERY ACTION OF THIS CONTROLLER
    public function init() {

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();

        $product_id = $this->getRequestParam('product_id');

        if (!empty($product_id)) {

            //GET product TYPE ID
            $product = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        }

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_wishlist', null, "view")->isValid())
            $this->respondWithError('unauthorized');
    }

    /**
     * RETURN THE LIST AND DETAILS OF ALL WISHLIST WITH SEARCH PARAMETERS.
     * 
     * @return array
     */
    public function browseAction() {
        $this->validateRequestMethod();
        // Prepare the response
        $params = $response = array();
        $params = $this->_getAllParams();
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        //GET PAGINATOR
        $params['pagination'] = 1;

        $paginator = Engine_Api::_()->getDbtable('wishlists', 'sitestoreproduct')->getBrowseWishlists($params);
        $page = $this->_getParam('page', 1);
        $limit = $this->_getParam('limit', 20);
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page);
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $totalItemCount = $paginator->getTotalItemCount();
        $totalPages = ceil(($totalItemCount) / $limit);
        $response['totalItemCount'] = $totalItemCount;
        if (!empty($totalItemCount)) {
            foreach ($paginator as $wishlistObj) {
                $wishlist = $wishlistObj->toArray();

                if (isset($wishlist['body']) && !empty($wishlist['body']))
                    $wishlist['body'] = strip_tags($wishlist['body']);

                $lists = $wishlistObj->getWishlistMap(array('orderby' => 'product_id'));
                $count = $lists->getTotalItemCount();
                $tempproducts = array();
                $counter = 0;
                if (empty($count) || !isset($count) || $count == 0) {
                    $tempproducts['listing_images_' . $counter] = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($wishlistObj);
                } else {
                    foreach ($lists as $products) {
                        if ($counter >= 3)
                            break;
                        else {
                            $counter++;
                            $tempproducts['listing_images_' . $counter] = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($products);
                        }
                    }
                }
                $wishlist = array_merge($wishlist, $tempproducts);
                $check_availability = Engine_Api::_()->sitestoreproduct()->check_availability('sitestoreproduct_wishlist', $wishlistObj->wishlist_id);
                $tempMenu = array();
                if (!empty($viewer_id)) {
                    if (empty($check_availability)) {
                        $wishlist['isLike'] = 0;
                        $tempMenu[] = array(
                            'name' => 'like',
                            'label' => $this->translate('Like'),
                            'url' => '/like',
                            'urlParams' => array(
                                "subject_type" => 'sitestoreproduct_wishlist',
                                'subject_id' => $wishlistObj->getIdentity()
                            )
                        );
                    } else {
                        $wishlist['isLike'] = 1;
                        $tempMenu[] = array(
                            'name' => 'like',
                            'label' => $this->translate('Unlike'),
                            'url' => '/unlike',
                            'urlParams' => array(
                                "subject_type" => 'sitestoreproduct_wishlist',
                                'subject_id' => $wishlistObj->getIdentity()
                            )
                        );
                    }

                    // follow 
                    $wishlist['is_followed'] = (int)Engine_Api::_()->getDbTable("follows" , "Seaocore")->isFollow($wishlistObj , $viewer);

                    if($wishlist['is_followed'])
                        $label = $this->translate("Unfollow");
                    else
                        $label = $this->translate("Follow");

                    $tempMenu[] = array(
                        'name' => 'follow',
                        'label' => $label,
                        'url' => '/advancedactivity/feeds/follow',
                        'urlParams' => array(
                            "resource_type" => 'sitestoreproduct_wishlist',
                            "resource_id" => $wishlistObj->getIdentity()
                        )
                    );

                    $wishlist['gutterMenu'] = $tempMenu;
                }
                $tempResponse[] = $wishlist;
            }
        }
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        $can_create = ($viewer_id) ? 1 : 0;
        $response['canCreate'] = $can_create;
        if (!empty($tempResponse))
            $response['response'] = $tempResponse;
        $this->respondWithSuccess($response, true);
    }

    /**
     * Return the "Diary Browse Search" form. 
     * 
     * @return array
     */
    public function searchFormAction() {

        // Validate request methods
        $this->validateRequestMethod();
        $response = array();
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('sitestoreproduct_product', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        try {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'sitestoreproduct')->getWishlistSearchForm();

            $this->respondWithSuccess($response, false);
        } catch (Expection $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /**
     * Add item to wishlist.
     * 
     * @return status
     */
    public function addAction() {

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        //GET PAGE ID AND CHECK PAGE ID VALIDATION
        $product_id = $this->_getParam('product_id');
        if (empty($product_id)) {
            $this->respondWithError('no_record');
        }

        //GET VIEWER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        //GET USER DIARIES
        $wishlistTable = Engine_Api::_()->getDbtable('wishlists', 'sitestoreproduct');
        $wishlistDatas = $wishlistTable->userWishlists($viewer);
        $wishlistDataCount = Count($wishlistDatas);
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $this->_getParam('product_id'));
        if (empty($sitestoreproduct)) {
            $this->respondWithError('no_record');
        }

        //FORM GENERATION
        if ($this->getRequest()->isGet()) {
            $response['form'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitestoreproduct')->getAddToWishlistForm($product_id);
            if(count($response['form'])>4)
            {
                $response['add_wishlist_description'] = $this->translate('Please select the wishlists in which you want to add this Product.');
                $response['create_wishlist_descriptions'] = $this->translate('You can also add this Product in a new wishlist below:');
            }
            else
            {
                // $response['add_wishlist_description'] = $this->translate('Please select the wishlists in which you want to add this Product.');
                $response['create_wishlist_descriptions'] = $this->translate('You have not created any wishlist yet. Get Started by creating and adding Products!');
            }
            $this->respondWithSuccess($response, true);
        } else if ($this->getRequest()->isPost()) {

            //GET FOLLOW TABLE
            $followTable = Engine_Api::_()->getDbTable('follows', 'seaocore');
            $values = $this->_getAllParams();
            //CHECK FOR NEW ADDED DIARY TITLE OR 
            if (!empty($values['body']) && empty($values['title'])) {
                $this->respondWithError('parameter_missing');
            }

            // CHECK FOR TITLE IF NO DIARY
            if (empty($wishlistDatas) && empty($values['title']))
                $this->respondWithError('Title feild required');

            //GET DIARY PAGE TABLE
            $wishlistEventTable = Engine_Api::_()->getDbtable('wishlistmaps', 'sitestoreproduct');

            $wishlistOldIds = array();

            //GET NOTIFY API
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');


            //WORK ON PREVIOUSLY CREATED DIARY
            if (!empty($wishlistDatas)) {
                foreach ($wishlistDatas as $wishlistData) {
                    $key_name = 'wishlist_' . $wishlistData->wishlist_id;
                    if (isset($values[$key_name]) && !empty($values[$key_name])) {

                        // IF ALREADY PRESENT THAN CONTINUE
                        $productAlreadyExists = $wishlistEventTable->select()
                                                                    ->where('wishlist_id = ?' , $wishlistData->wishlist_id)
                                                                    ->where('product_id = ?' , $product_id)
                                                                    ->query()
                                                                    ->fetchALL();

                        if(count($productAlreadyExists))
                            continue;

                        $wishlistEventTable->insert(array(
                            'wishlist_id' => $wishlistData->wishlist_id,
                            'product_id' => $product_id,
                        ));

                        //DIARY COVER PHOTO
                        $wishlistTable->update(
                                array(
                            'product_id' => $product_id,
                                ), array(
                            'wishlist_id = ?' => $wishlistData->wishlist_id,
                            'product_id = ?' => 0
                                )
                        );

                        //GET FOLLOWERS
                        // $followers = $followTable->getFollowers('sitestoreproduct_wishlist', $wishlistData->wishlist_id, $viewer_id);
                        // foreach ($followers as $follower) {
                        //   $followerObject = Engine_Api::_()->getItem('user', $follower->poster_id);
                        //   $wishlist = Engine_Api::_()->getItem('sitestoreproduct_wishlist', $wishlistData->wishlist_id);
                        //   $http = _ENGINE_SSL ? 'https://' : 'http://';
                        //   $wishlist_link = '<a href="' . $http . $_SERVER['HTTP_HOST'] . '/' . $wishlist->getHref() . '">' . $wishlist->getTitle() . '</a>';
                        //   $notifyApi->addNotification($followerObject, $viewer, $sitestoreproduct, 'sitestoreproduct_wishlist_followers', array("wishlist" => $wishlist_link));
                        // }

                        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                        $action = $activityApi->addActivity($viewer, $wishlistData, "sitestoreproduct_wishlist_add_product", '', array('product' => array($sitestoreproduct->getType(), $sitestoreproduct->getIdentity())));

                        if ($action)
                            $activityApi->attachActivity($action, $sitestoreproduct);
                    }
                    $in_key_name = 'inWishlist_' . $wishlistData->wishlist_id;
                    if (isset($values[$in_key_name]) && empty($values[$in_key_name])) {
                        $wishlistOldIds[$wishlistData->wishlist_id] = $wishlistData;
                        $wishlistEventTable->delete(array('wishlist_id = ?' => $wishlistData->wishlist_id, 'product_id = ?' => $product_id));

                        //DELETE ACTIVITY FEED
                        $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                        $actionTableName = $actionTable->info('name');

                        $action_id = $actionTable->select()
                                ->setIntegrityCheck(false)
                                ->from($actionTableName, 'action_id')
                                ->joinInner('engine4_activity_attachments', "engine4_activity_attachments.action_id = $actionTableName.action_id", array())
                                ->where('engine4_activity_attachments.id = ?', $product_id)
                                ->where($actionTableName . '.type = ?', "sitestoreproduct_wishlist_add_product")
                                ->where($actionTableName . '.subject_type = ?', 'user')
                                ->where($actionTableName . '.object_type = ?', 'sitestoreproduct_wishlist')
                                ->where($actionTableName . '.object_id = ?', $wishlistData->wishlist_id)
                                ->query()
                                ->fetchColumn();

                        if (!empty($action_id)) {
                            $activity = Engine_Api::_()->getItem('activity_action', $action_id);
                            if (!empty($activity)) {
                                $activity->delete();
                            }
                        }
                    }
                }
            }

            if (!empty($values['title'])) {

                $db = Engine_Db_Table::getDefaultAdapter();
                $db->beginTransaction();

                try {
                    //CREATE DIARY
                    $wishlist = $wishlistTable->createRow();
                    $wishlist->setFromArray($values);
                    $wishlist->owner_id = $viewer_id;
                    $wishlist->product_id = $product_id; //DIARY COVER PHOTO
                    $wishlist->save();

                    //PRIVACY WORK
                    $auth = Engine_Api::_()->authorization()->context;
                    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

                    if (empty($values['auth_view'])) {
                        $values['auth_view'] = 'everyone';
                    }

                    $viewMax = array_search($values['auth_view'], $roles);
                    foreach ($roles as $i => $role) {
                        $auth->setAllowed($wishlist, $role, 'view', ($i <= $viewMax));
                    }

                    $db->commit();
                    $wishlistEventTable->insert(array(
                        'wishlist_id' => $wishlist->wishlist_id,
                        'product_id' => $product_id,
                        'date' => new Zend_Db_Expr('NOW()')
                    ));

                    $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                    $action = $activityApi->addActivity($viewer, $wishlist, "sitestoreproduct_wishlist_add_product", '', array('product' => array($sitestoreproduct->getType(), $sitestoreproduct->getIdentity())));
                    if ($action) {
                        $activityApi->attachActivity($action, $sitestoreproduct);
                    }
                } catch (Exception $e) {
                    $db->rollback();
                    throw $e;
                }
            }

            $data = array();
            $data['wishlistPresent'] = Engine_Api::_()->getApi("Siteapi_Core","sitestoreproduct")->checkForWishlist($sitestoreproduct); 
            $this->respondWithSuccess($data);
        }
    }

    /**
     * Return the Create Wishlist Form.
     * 
     * @return array
     */
    public function createAction() {
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        //GET VIEWER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //FORM GENERATION
        if ($this->getRequest()->isGet()) {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'sitestoreproduct')->getCreateWishlistForm();
            $this->respondWithSuccess($response, true);
        } else if ($this->getRequest()->isPost()) {

            //GET DIARY TABLE
            $wishlistTable = Engine_Api::_()->getItemTable('sitestoreproduct_wishlist');
            $db = $wishlistTable->getAdapter();
            $db->beginTransaction();

            try {
                //GET FORM VALUES
                $values = $this->_getAllParams();
                if (empty($values['title'])) {
                    $this->respondWithValidationError('validation_fail', 'Please complete this field - it is required.');
                }
                $values['owner_id'] = $viewer->getIdentity();

                //CREATE DIARY
                $wishlist = $wishlistTable->createRow();
                $wishlist->setFromArray($values);
                $wishlist->save();

                //PRIVACY WORK
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

                if (empty($values['auth_view'])) {
                    $values['auth_view'] = 'owner';
                }
                $viewMax = array_search($values['auth_view'], $roles);

                foreach ($roles as $i => $role) {
                    $auth->setAllowed($wishlist, $role, 'view', ($i <= $viewMax));
                }
                $db->commit();
                // Change request method POST to GET
                $this->setRequestMethod();
                $this->_forward('profile', 'wishlist', 'sitestoreproduct', array(
                    'wishlist_id' => $wishlist->getIdentity()
                ));
            } catch (Exception $e) {
                $db->rollback();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * Return the Wishlist View page.
     * 
     * @return array
     */
    public function profileAction() {
        // Validate request methods
        $this->validateRequestMethod();
        //GET DIARY ID AND SUBJECT
        if (Engine_Api::_()->core()->hasSubject())
            $subject = $wishlist = Engine_Api::_()->core()->getSubject('sitestoreproduct_wishlist');

        $wishlist_id = $this->_getParam('wishlist_id');
        if (isset($wishlist_id) && !empty($wishlist_id)) {
            $subject = $wishlist = Engine_Api::_()->getItem('sitestoreproduct_wishlist', $wishlist_id);
            if (isset($wishlist) && !empty($wishlist))
                Engine_Api::_()->core()->setSubject($wishlist);
            else
                $this->respondWithError('no_record');
        } else {
            $this->respondWithError('no_record');
        }

        if (empty($wishlist)) {
            $this->respondWithError('no_record');
        }

        $wishlist_id = $this->_getParam('wishlist_id');

        $viewer = Engine_Api::_()->user()->getViewer();

        //INCREASE VIEW COUNT IF VIEWER IS NOT OWNER
        if (!$wishlist->getOwner()->isSelf($viewer)) {
            $wishlist->view_count++;
            $wishlist->save();
        }

        // PREPARE RESPONSE ARRAY
        $bodyParams['response'] = $subject->toArray();

        if (isset($bodyParams['response']['body']) && !empty($bodyParams['response']['body']))
            $bodyParams['response']['body'] = strip_tags($bodyParams['response']['body']);

        $bodyParams['response'] = array_merge($bodyParams['response'], Engine_Api::_()->getApi('Core', 'siteapi')->getContentUrl($subject));

        $viewer_id = $viewer->getIdentity();
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $showMessageOwner = 0;
        $showMessageOwner = Engine_Api::_()->authorization()->getPermission($level_id, 'messages', 'auth');
        if ($showMessageOwner != 'none') {
            $showMessageOwner = 1;
        }

        $messageOwner = 1;
        if ($wishlist->owner_id == $viewer_id || empty($viewer_id) || empty($showMessageOwner)) {
            $messageOwner = 0;
        }
        //GET LEVEL SETTING
        $can_create = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestoreproduct_wishlist', "create");
        $bodyParams['response']['wishlist_creator_name'] = $wishlist->getOwner()->getTitle();

        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($subject, true);
        $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);
        $perms = array();
        //PRIVACY WORK
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        $perms = array();
        foreach ($roles as $roleString) {
            $role = $roleString;
            if ($auth->isAllowed($su, $role, 'view')) {
                $perms['auth_view'] = $roleString;
            }
        }
        $bodyParams['response'] = array_merge($bodyParams['response'], $perms);

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        try {
            //FETCH RESULTS
            $paginator = Engine_Api::_()->getDbTable('wishlistmaps', 'sitestoreproduct')->wishlistProducts($wishlist->wishlist_id);
            $paginator->setItemCountPerPage($itemCount);
            $paginator->setCurrentPageNumber($this->_getParam('currentpage', 1));
            $total_item = $paginator->getTotalItemCount();
            $bodyParams['response']['totalproducts'] = $total_item;

            foreach ($paginator as $sitestoreproductObj) {
                $sitestoreproduct = $sitestoreproductObj->toArray();

                $sitestoreproduct['information'] = Engine_Api::_()->getApi("Siteapi_Core","sitestoreproduct")->getPriceFields($sitestoreproductObj);

                if (isset($sitestoreproductObj->owner_id) && !empty($sitestoreproductObj->owner_id)) {
                    // Add owner images
                    $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($sitestoreproductObj, true);
                    $sitestoreproduct = array_merge($sitestoreproduct, $getContentImages);

                    $sitestoreproduct["owner_title"] = $sitestoreproductObj->getOwner()->getTitle();
                }

                if (empty($sitestoreproduct['price']))
                    unset($sitestoreproduct['price']);

                // Set the price & currency 
                if (isset($sitestoreproduct['price']) && $sitestoreproduct['price'] > 0)
                    $sitestoreproduct['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');

                // Add images  
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($sitestoreproductObj);
                $sitestoreproduct = array_merge($sitestoreproduct, $getContentImages);

                if (isset($wishlist->product_id) && !empty($wishlist->product_id) && $wishlist->product_id == $sitestoreproductObj->product_id) {
                    if (!isset($bodyParams['response']['image']) && empty($bodyParams['response']['image']))
                        $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);
                }


                $isAllowedView = $sitestoreproductObj->authorization()->isAllowed($viewer, 'view');
                $sitestoreproduct["allow_to_view"] = empty($isAllowedView) ? 0 : 1;

                $isAllowedEdit = $sitestoreproductObj->authorization()->isAllowed($viewer, 'edit');
                $sitestoreproduct["edit"] = empty($isAllowedEdit) ? 0 : 1;

                $isAllowedDelete = $sitestoreproductObj->authorization()->isAllowed($viewer, 'delete');
                $sitestoreproduct["delete"] = empty($isAllowedDelete) ? 0 : 1;
                $productMenu = array();
                $productMenu[] = array(
                    'name' => 'remove',
                    'label' => $this->translate('Remove'),
                    'url' => 'sitestore/product/wishlist/remove',
                    'urlParams' => array(
                        "product_id" => $sitestoreproductObj->getIdentity(),
                        'wishlist_id' => $wishlist->getIdentity()
                    )
                );
                if ($wishlist->owner_id == $viewer_id || $level_id == 1)
                    $sitestoreproduct['gutter_menu'] = $productMenu;
                $tempResponse[] = $sitestoreproduct;
            }
            if (!empty($tempResponse)) {
                $bodyParams['response']['products'] = $tempResponse;
            }

            if (!isset($bodyParams['response']['image']) && empty($bodyParams['response']['image'])) {
                $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);
            }

            if ($viewer_id) {
                $wishlistMenus[] = array(
                    'name' => 'memberWishlist',
                    'label' => $this->translate($wishlist->getOwner()->getTitle() . "'s" . " Wishlists"),
                    'url' => 'sitestore/product/wishlist',
                    'urlParams' => array(
                        "text" => $wishlist->getOwner()->getTitle())
                );
                // if ($can_create) {
                //     $wishlistMenus[] = array(
                //         'name' => 'create',
                //         'label' => $this->translate('Create New Wishlist'),
                //         'url' => 'sitestore/product/wishlist/create',
                //     );
                // }
                if (!empty($messageOwner)) {
                    $wishlistMenus[] = array(
                        'name' => 'messageOwner',
                        'label' => $this->translate('Message Owner'),
                        'url' => 'sitestore/product/wishlist/message-owner',
                        'urlParams' => array(
                            "subject_id" => $wishlist->getIdentity())
                    );
                }
                $wishlistMenus[] = array(
                    'name' => 'report',
                    'label' => $this->translate('Report'),
                    'url' => 'report/create/subject/' . $subject->getGuid(),
                    'urlParams' => array(
                        "type" => $wishlist->getType(),
                        "id" => $wishlist->getIdentity()
                    )
                );

                $wishlistMenus[] = array(
                    'name' => 'share',
                    'label' => $this->translate('Share'),
                    'url' => 'activity/share',
                    'urlParams' => array(
                        "type" => $wishlist->getType(),
                        "id" => $wishlist->getIdentity()
                    )
                );

                if ($wishlist->owner_id == $viewer_id || $level_id == 1) {
                    $wishlistMenus[] = array(
                        'name' => 'edit',
                        'label' => $this->translate('Edit Wishlist'),
                        'url' => 'sitestore/product/wishlist/edit/' . $wishlist->getIdentity(),
                    );
                    $wishlistMenus[] = array(
                        'name' => 'delete',
                        'label' => $this->translate('Delete Wishlist'),
                        'url' => 'sitestore/product/wishlist/delete/' . $wishlist->getIdentity(),
                    );
                }
            }

            $wishlistMenus[] = array(
                'name' => 'tellafriend',
                'label' => $this->translate('Tell A Friend'),
                'url' => 'sitestore/product/wishlist/tell-a-friend',
                'urlParams' => array(
                    "subject_id" => $wishlist->getIdentity())
            );
            if (!empty($wishlistMenus)) {
                $bodyParams['gutterMenus'] = $wishlistMenus;
            }

            $bodyParams['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');

            $this->respondWithSuccess($bodyParams);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /**
     * Return the Message Owner Form and Send message.
     * 
     * @return array
     */
    public function messageOwnerAction() {

        //LOGGED IN USER CAN SEND THE MESSAGE
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET EVENT ID AND OBJECT
        $wishlist_id = $this->_getParam("subject_id");
        $wishlist = Engine_Api::_()->getItem('sitestoreproduct_wishlist', $wishlist_id);

        if (empty($wishlist))
            $this->respondWithError('no_record');

        $owner_id = $wishlist->owner_id;

        //OWNER CANT SEND A MESSAGE TO HIMSELF
        if ($viewer_id == $wishlist->owner_id) {
            $this->respondWithError('unauthorized');
        }

        //MAKE FORM
        if ($this->getRequest()->isGet()) {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'sitestoreproduct')->getMessageOwnerForm();
            $this->respondWithSuccess($response, true);
        } else if ($this->getRequest()->isPost()) {
            $values = $this->_getAllParams();


            $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
            $db->beginTransaction();

            try {

                $is_error = 0;
                if (empty($values['title'])) {
                    $this->respondWithValidationError('validation_fail', 'Subject field is required');
                }

                $recipients = preg_split('/[,. ]+/', $owner_id);

                //LIMIT RECIPIENTS
                $recipients = array_slice($recipients, 0, 1000);

                //CLEAN THE RECIPIENTS FOR REPEATING IDS
                $recipients = array_unique($recipients);

                //GET USER
                $user = Engine_Api::_()->getItem('user', $wishlist->owner_id);

                $wishlist_title = $wishlist->getTitle();
                $wishlist_title_with_link = '<a href = http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('wishlist_id' => $wishlist_id, 'slug' => $wishlist->getSlug()), "sitestoreproduct_wishlist_view") . ">$wishlist_title</a>";

                $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send($viewer, $recipients, $values['title'], $values['body'] . "<br><br>" . 'This message corresponds to the Wishlist: ' . $wishlist_title_with_link);

                try {
                    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $conversation, 'message_new');
                } catch (Exception $e) {
                    //Blank Exception
                }
                //INCREMENT MESSAGE COUNTER
                Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessagex());
            }
        }
    }

    /**
     * Return the Diary Edit Form.
     * 
     * @return array
     */
    public function editAction() {

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        $wishlist_id = $this->_getParam('wishlist_id');
        
        $wishlist = Engine_Api::_()->getItem("sitestoreproduct_wishlist",$wishlist_id);
        
        if(!$wishlist)
            $this->respondWithError("no_record");

        //GET VIEWER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $level_id = $viewer->level_id;


        if ($level_id != 1 && $wishlist->owner_id != $viewer_id) {
            $this->respondWithError('unauthorized');
        }
        //GET USER DIARIES
        $wishlistTable = Engine_Api::_()->getDbtable('wishlists', 'sitestoreproduct');
        $wishlistDatas = $wishlistTable->userWishlists($viewer);
        //PRIVACY WORK
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        $perms = array();
        foreach ($roles as $roleString) {
            $role = $roleString;
            if ($auth->isAllowed($wishlist, $role, 'view')) {
                $perms['auth_view'] = $roleString;
            }
        }
        //FORM GENERATION
        if ($this->getRequest()->isGet()) {
            $formValues = $wishlist->toArray();
            $formValues = array_merge($formValues, $perms);

            if (isset($formValues['body']) && !empty($formValues['body']))
                $formValues['body'] = strip_tags($formValues['body']);


            $this->respondWithSuccess(array(
                'form' => Engine_Api::_()->getApi('Siteapi_Core', 'sitestoreproduct')->getCreateWishlistForm(),
                'formValues' => $formValues,
                'create_wishlist_descriptions' => $this->translate('Edit your wishlist over here and then click on "Save" to save it.'),
            ));
        }

        //FORM VALIDATION
        else if ($this->getRequest()->isPut() || $this->getRequest()->isPost()) {

            $db = Engine_Api::_()->getItemTable('sitestoreproduct_product')->getAdapter();
            $db->beginTransaction();
            try {
                $values = array();
                $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'sitestoreproduct')->getCreateWishlistForm();
                foreach ($getForm as $element) {

                    if (isset($_REQUEST[$element['name']]))
                        $values[$element['name']] = $_REQUEST[$element['name']];
                }
                if (empty($values['title'])) {
                    $validationMessage = "title is required";
                    $this->respondWithValidationError('validation_fail', $validationMessage);
                }

                $wishlist->setFromArray($values)->save();
                $db->commit();

                //PRIVACTY WORK
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

                if (empty($values['auth_view'])) {
                    $values['auth_view'] = 'owner';
                }

                $viewMax = array_search($values['auth_view'], $roles);
                foreach ($roles as $i => $role) {
                    $auth->setAllowed($wishlist, $role, 'view', ($i <= $viewMax));
                }
                $db->commit();
                // Change request method POST to GET
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

    /**
     * Return the Diary Tell A Friend Form.
     * 
     * @return array
     */
    public function tellAFriendAction() {
        $wishlist_id = $this->_getParam('subject_id', null);
        $wishlist = Engine_Api::_()->getItem('sitestoreproduct_wishlist', $wishlist_id);
        $errorMessage = array();
        if (empty($wishlist))
            $this->respondWithError('no_record');

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET FORM
        if ($this->getRequest()->isGet()) {
            $response['form'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitestoreproduct')->getTellAFriendForm();
            if (!empty($viewer_id))
                $response['formValues'] = array(
                    'sender_name' => $viewer->displayname,
                    'sender_email' => $viewer->email
                );
            $this->respondWithSuccess($response, true);
        } else if ($this->getRequest()->isPost()) {

            $values = $this->_getAllParams();

            if (empty($values['sender_email']) && !isset($values['sender_email'])) {
                $errorMessage[] = $this->translate("Your Email field is required");
            }

            if (empty($values['sender_name']) && !isset($values['sender_name'])) {
                $errorMessage[] = $this->translate("Your Name field is required");
            }

            if (empty($values['message']) && !isset($values['message'])) {
                $errorMessage[] = $this->translate("Message field is required");
            }

            if (empty($values['receiver_emails']) && !isset($values['receiver_emails'])) {
                $errorMessage[] = $this->translate("To field is required");
            }

            if (isset($errorMessage) && !empty($errorMessage) && count($errorMessage) > 0)
                $this->respondWithValidationError('validation_fail', $errorMessage);

            //EXPLODE EMAIL IDS
            $reciver_ids = explode(',', $values['receiver_emails']);

            if (!empty($values['send_me'])) {
                $reciver_ids[] = $values['sender_email'];
            }

            $sender_email = $values['sender_email'];
            $heading = $wishlist->title;
            //CHECK VALID EMAIL ID FORMAT
            $validator = new Zend_Validate_EmailAddress();
            $validator->getHostnameValidator()->setValidateTld(false);

            if (!$validator->isValid($sender_email)) {
                $errorMessage['sender_email'] = $this->translate('Invalid sender email address value');
                $this->respondWithValidationError('validation_fail', $errorMessage);
            }

            foreach ($reciver_ids as $receiver_id) {
                $receiver_id = trim($receiver_id, ' ');
                if (!$validator->isValid($receiver_id)) {
                    $errorMessage['receiver_emails'] = $this->translate('Please enter correct email address of the receiver(s).');
                    $this->respondWithValidationError('validation_fail', $errorMessage);
                }
            }

            $sender = $values['sender_name'];
            $message = $values['message'];

            $wishlistLink = $wishlist->getHref();
            if (strstr($wishlistLink, '/products/')) {
                $wishlistLink = str_replace("/products", "", $wishlistLink);
            }

            Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_ids, 'SITESTOREPRODUCT_TELLAFRIEND_EMAIL', array(
                'host' => $_SERVER['HTTP_HOST'],
                'sender' => $sender,
                'heading' => $heading,
                'message' => '<div>' . $message . '</div>',
                'object_link' => $wishlistLink,
                'email' => $sender_email,
                'queue' => true
            ));
            $this->successResponseNoContent('no_content', true);
        }
    }

    /**
     * Remove product from wishlist.
     * 
     * @return status
     */
    public function removeAction() {

        // Validate request methods
        $this->validateRequestMethod('POST');

        //GET DIARY ID AND SUBJECT
        if (Engine_Api::_()->core()->hasSubject())
            $wishlist = Engine_Api::_()->core()->getSubject('sitestoreproduct_wishlist');

        $wishlist_id = $this->_getParam('wishlist_id');
        if (isset($wishlist_id) && !empty($wishlist_id)) {
            $subject = $wishlist = Engine_Api::_()->getItem('sitestoreproduct_wishlist', $wishlist_id);
            Engine_Api::_()->core()->setSubject($wishlist);
        } else {
            $this->respondWithError('no_record');
        }


        if (empty($wishlist))
            $this->respondWithError('no_record');

        $wishlist_id = $this->_getParam('wishlist_id');

        $viewer = Engine_Api::_()->user()->getViewer();

        //GET EVENT ID AND EVENT
        $product_id = $this->_getParam('product_id');
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        if (empty($sitestoreproduct) && !isset($sitestoreproduct))
            $this->respondWithError('no_record');

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

            //DELETE FROM DATABASE
            Engine_Api::_()->getDbtable('wishlistmaps', 'sitestoreproduct')->delete(array('wishlist_id = ?' => $wishlist_id, 'product_id = ?' => $product_id));

            try {
                //DELETE ACTIVITY FEED
                //SQL ERROR TO BE CORRECTED
                $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                $actionTableName = $actionTable->info('name');

                $action_id = $actionTable->select()
                        ->setIntegrityCheck(false)
                        ->from($actionTableName, 'action_id')
                        ->joinInner('engine4_activity_attachments', "engine4_activity_attachments.action_id = $actionTableName.action_id", array())
                        ->where('engine4_activity_attachments.id = ?', $product_id)
                        ->where($actionTableName . '.type = ?', "sitestoreproduct_wishlist_add_product")
                        ->where($actionTableName . '.subject_type = ?', 'user')
                        ->where($actionTableName . '.object_type = ?', 'sitestoreproduct_product')
                        ->where($actionTableName . '.object_id = ?', $product_id)
                        //->where($actionTableName . '.params like(?)', '{"child_id":' . $wishlist_id . '}')
                        ->query()
                        ->fetchColumn();
            } catch (Exception $ex) {
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
            if (!empty($action_id)) {
                $activity = Engine_Api::_()->getItem('activity_action', $action_id);
                if (!empty($activity)) {
                    $activity->delete();
                }
            }
            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Delete Diary.
     * 
     * @return status
     */
    public function deleteAction() {
        // Validate request methods
        $this->validateRequestMethod('DELETE');

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');


        //GET DIARY ID
        $wishlist_id = $this->_getParam('wishlist_id');

        $wishlist = Engine_Api::_()->getItem('sitestoreproduct_wishlist', $wishlist_id);

        if (empty($wishlist) && !isset($wishlist))
            $this->respondWithError('no_record');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $level_id = $viewer->level_id;

        if ($level_id != 1 && $wishlist->owner_id != $viewer_id) {
            $this->respondWithError('unauthorized');
        }
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

            //DELETE DIARY CONTENT
            $wishlist->delete();

            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function wishlistOptionsAction()
    {
        $viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $product_id = $this->_getParam('product_id');

        if(!$product_id)
            $this->respondWithValidationError("parameter_missing" , "product_id missing");

        $product = Engine_Api::_()->getItem("sitestoreproduct_product" , $product_id);

        $wishlistTable = Engine_Api::_()->getDbtable('wishlists' , 'sitestoreproduct');

        if(!$product_id)
            $this->respondWithError("no_record");

        $db = $wishlistTable->getAdapter();

        $sql = "SELECT a.title,a.wishlist_id , b.product_id from engine4_sitestoreproduct_wishlists a inner join engine4_sitestoreproduct_wishlistmaps b on a.wishlist_id=b.wishlist_id where b.product_id=? and a.owner_id=?";

        $result = $db->query($sql,array($product_id , $viewer_id))->fetchALL();

        if(!$result)
            $this->respondWithError("no_record");

        if($this->getRequest()->isGet())
        {
            $form = array();

            foreach($result as $row => $value)
            {
                $form[] = array(
                        'name' => 'wishlist_'.$value['wishlist_id'],
                        'label' => $this->translate(ucfirst($value['title'])),
                        'type' => 'checkbox',
                        'value' => 1,
                    );
            }

            $this->respondWithSuccess($form , true);
        }

        if($this->getRequest()->isPost())
        {
            $values = $this->_getAllParams();
            $count = count($result);

            foreach ($result as $key => $value) {
                if(isset($values['wishlist_'.$value['wishlist_id']]) && empty($values['wishlist_'.$value['wishlist_id']]))
                {
                    $delete = $db->delete('engine4_sitestoreproduct_wishlistmaps',array(
                                'product_id = ?' => $value['product_id'],
                                'wishlist_id = ?' => $value['wishlist_id'],
                            ));
                    $count--;
                }
            }
            if(!$count)
                $this->respondWithError("no_record");
            else
                $this->successResponseNoContent("no_content");
        }

    }

}
