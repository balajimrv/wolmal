<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_IndexController extends Siteapi_Controller_Action_Standard {

    public $_configurableOptions;

    /*
     * Creates the store object 
     */

    public function init() {
        $viewer = Engine_Api::_()->user()->getViewer();

        $store_id = $this->_getParam('store_id');

        if (!empty($store_id)) {
            $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
            if (!empty($sitestore))
                Engine_Api::_()->core()->setSubject($sitestore);
        }

        // Authorization check
        if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', $viewer, "view")->isValid())
            $this->respondWithError('unauthorized');
    }

    /*
     * Store product paginator
     */

    public function indexAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (Engine_Api::_()->core()->hasSubject('sitestore_store'))
            $sitestore = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
        else
            $this->respondWithError('no_record');

        $this->validateRequestMethod();

        $values = $this->_getAllParams();

        if (empty($values['page']))
            $values['page'] = 1;

        if (empty($values['limit']))
            $values['limit'] = 20;

        $values['type'] = "browse";

        $this->respondWithSuccess($this->getStoreProductData($values),true);
    }

    /*
     * browse action
     */

    public function browseAction() {
        $this->validateRequestMethod();

        $values = $this->_getAllParams();

        if (empty($values['page']))
            $values['page'] = 1;

        if (empty($values['limit']))
            $values['limit'] = 20;
        
        $values['type'] = "browse";

        $this->respondWithSuccess($this->getStoreProductData($values),true);
    }

    /*
     * Store product paginator
     */

    public function manageAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!$viewer_id)
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject('sitestore_store'))
            $sitestore = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
        else
            $this->respondWithError('no_record');

        $this->validateRequestMethod();

        $values = $this->getAllParams();

        if (empty($values['page']))
            $values['page'] = 1;

        if (empty($values['limit']))
            $values['limit'] = 20;

        //MAKE DATA ARRAY
        $values['user_id'] = $viewer_id;
        $values['type'] = 'manage';
        $values['orderby'] = 'product_id';

        $this->respondWithSuccess($this->getStoreProductData($values),true);
    }

    private function getStoreProductData($values = array()) {
        $response = $tempResponse = array();
        $response['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // wishlist work
        if ($viewer_id) {
            $wishlistMapsTable = Engine_Api::_()->getDbTable('wishlistmaps', 'sitestoreproduct');
            $wishlistTable = Engine_Api::_()->getDbTable('wishlists', 'sitestoreproduct');
            $wishlistTableName = $wishlistTable->info('name');
            $wishlistMapsTableName = $wishlistMapsTable->info("name");
            $wishlistProducts = array();

            $select = $wishlistTable->select()
                    ->distinct()
                    ->setIntegrityCheck(false)
                    ->from($wishlistTableName)
                    ->joinInner($wishlistMapsTableName, "$wishlistTableName.wishlist_id = .$wishlistMapsTableName.wishlist_id")
                    ->where($wishlistTableName . '.owner_id = ?', $viewer_id);

            $wishlistData = $select->query()->fetchAll();

            if (!empty($wishlistData)) {
                foreach ($wishlistData as $row => $value) {
                    $wishlistProducts[$value['product_id']][] = array('label' => $this->translate($value['title']), 'wishlist_id' => $value['wishlist_id']);
                }
            }
        }
        // wishlist work ends


        if (!empty($viewer_id)) {
            $level_id = Engine_Api::_()->user()->getViewer()->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $action = $values['action'];

        $paginator = Engine_Api::_()->getApi('Siteapi_Core', 'sitestoreproduct')->getStoreProductPaginator($values);

        $response['totalItemCount'] = $paginator->getTotalItemCount();

        $can_view = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestoreproduct_product', "view");

        if ($paginator)
            foreach ($paginator as $row => $value) {
                // authorization check
                if ($can_view == 0 && ((!empty($product->draft) || empty($product->approved)) && ($product->owner_id != $viewer_id))) {
                    --$response['totalItemCount'];
                    continue;
                }

                $data = Engine_Api::_()->getApi('Siteapi_Core', 'sitestoreproduct')->getProduct($value, $values);
                $data['information'] = Engine_Api::_()->getApi("Siteapi_Core","sitestoreproduct")->getPriceFields($value);

                // Provides info if product is in stock
                $data['in_stock_product'] = (int) ($data['stock_unlimited'] || $data['in_stock']);
                $temp_allowed_selling = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($value->store_id);
                $data['canAddToCart'] = 0;
                if (!empty($temp_allowed_selling) && $value->allow_purchase && ($data['in_stock_product']))
                    $data['canAddToCart'] = 1;

                $data['discounted_amount'] = (float) $data['price'] - (float) $data['discount_amount'];

                if (!empty($wishlistProducts[$value->getIdentity()]))
                    $data['wishlist'] = $wishlistProducts[$value->getIdentity()];

                $response['response'][] = $data;
            }

        $response['orderby'] = $this->_getOrderByOptions();

        if ($values['action'] == 'category')
            return $response['response'];

        $this->respondWithSuccess($response,false);
    }

    /*
     *  view Product
     */

    public function viewAction() {

        $response = $tempResponse = array();

        $values = $this->getAllParams();
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();
        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $product_id = $this->_getParam('product_id');
        $productTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');       

        if (!$product_id)
            $this->respondWithValidationError('parameter_missing', 'product_id missing');

        $product = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        if (!$product)
            $this->respondWithError('no_record');

        $sitestore = Engine_Api::_()->getItem('sitestore_store' , $product->store_id );

        $can_view = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestoreproduct_product', "view");

        //AUTHORIZATION CHECK
        if ($can_view != 2 && ((!empty($product->draft) || empty($product->approved)) && ($product->owner_id != $viewer_id)))
            $this->respondWithSuccess('unauthorized');

        if ($can_view != 2 && ($product->owner_id != $viewer_id)) {
            $reviewApi = Engine_Api::_()->sitestoreproduct();
            $expirySettings = $reviewApi->expirySettings();
            if ($expirySettings == 2) {
                $approveDate = $reviewApi->adminExpiryDuration();
                if ($approveDate > $product->approved_date) {
                    $this->respondWithError('unauthorized');
                }
            }
        }

        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
        if (empty($isManageAdmin)) {
            $this->respondWithError('unauthorized');
        }

        if (!Engine_Api::_()->sitestore()->canViewStore($sitestore)) {
            $this->respondWithSuccess("unauthorized");
        }

        ++$product->view_count;
        $product->save();

        //SET PRODUCT VIEW DETAILS
        if (!empty($viewer_id)) {
            Engine_Api::_()->getDbtable('vieweds', 'sitestoreproduct')->setVieweds($product_id, $viewer_id);
        }

        $response = $product->toArray();
        // var_dump(Engine_Api::_()->getDbTable("likes","core")->getLikeCount($product));
        // die;
        // $response['like_count'] = Engine_Api::_()->getDbTable("likes","core")->getLikeCount($product);

        if ($product->store_id) {
            $sitestore = Engine_Api::_()->getItem('sitestore_store', $product->store_id);
            $response['store_title'] = $sitestore->getTitle();
        }

        // Provides info if product is in stock
        $response['in_stock_product'] = (int) ($product->stock_unlimited || $product->in_stock);
        $error = $this->canAddtoCartError($product);
        if($error)
        {
            $response['error'] = $error ;
            $response['canAddtoCart'] = 0 ;
        }
        else
            $response['canAddtoCart'] = 1;

        $response = array_merge($response, Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($product));
        $response = array_merge($response, Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($product, true));
        // other info price work start

        $response['wishlistPresent'] = Engine_Api::_()->getApi("Siteapi_Core","sitestoreproduct")->checkForWishlist($product);

        $host = $this->getSiteUrl();

        $scriptNametemp = explode("/", $_SERVER['SCRIPT_NAME']);
        $scriptName = $scriptNametemp[1];

        // other info price work ends
        if ($product->product_type == 'downloadable') {

            //PAGINATOR FOR SAMPLE FILES
            $downloadableProducts = Engine_Api::_()->getDbtable('downloadablefiles', 'sitestoreproduct')->getSampleFiles(array('product_id' => $product_id));

            $isAnyFileExist = Engine_Api::_()->getDbtable('downloadablefiles', 'sitestoreproduct')->IsAnyMainFileExist($product_id);

            if(!$isAnyMainFileExist)
            {
                $error = $this->translate("This product doesn't have any file to download");
                $response['canAddToCart'] = 0;
            }

            if (count($downloadableProducts) > 0) {
                foreach ($downloadableProducts as $sampleFiles) {
                    // $path = "/stores/product/download-sample/product_id/" . $product_id . "/downloadablefile_id/" . $sampleFiles->downloadablefile_id;

                    $downloadablefileItem = Engine_Api::_()->getItem('sitestoreproduct_downloadablefile', $sampleFiles->downloadablefile_id);

                    if (empty($downloadablefileItem) || $downloadablefileItem->type != 'sample')
                        continue;

                    $downloadablefile_name = $downloadablefileItem->filename;
                    $sampleDownloadFile['title'] = $downloadablefileItem->getTitle();
                    $url = $this->_helper->url->url(array('action' => 'download-sample', 'product_id'=> $product_id, 'downloadablefile_id' => $sampleFiles->downloadablefile_id), 'sitestoreproduct_product_general', true);                    
                    $sampleDownloadFile['filepath'] = $host.$url;
                    $fileArray[] = $sampleDownloadFile;
                }
                if (isset($fileArray) && !empty($fileArray))
                    $response['sampleFiles'] = $fileArray;
            }
        }

        //VIEW GROUP PRODUCTS
        if ($product->product_type == 'grouped') {

            $params = array();
            $params['product_type'] = 'grouped';
            $params['product_id'] = $product_id;

            $groupedProducts = $productTable->getCombinedProducts($params);
            $minprice = 0.00;
            foreach ($groupedProducts as $individualProduct) {

                if($this->canAddtoCartError($individualProduct))
                    continue;

                $indProduct = $individualProduct->toArray();
                $indProduct['information'] = Engine_Api::_()->getApi("Siteapi_Core","sitestoreproduct")->getPriceFields($individualProduct);
                
                if($minprice==0.00 || $minprice > $indProduct['price'])
                    $minprice = $indProduct['price'];

                $indProduct = array_merge($indProduct, Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($individualProduct));
                $indProductArray[] = $indProduct;

                if(empty($indProductArray))
                {
                    $error = $this->translate("No product is associated with this grouped product which is in stock");
                    $response['canAddToCart'] = 0;
                }
            }
            if (count($indProductArray) > 0) {
                $response['groupedProducts'] = $indProductArray;
            }

            $response['price'] = $minprice ;
        }


        if ($product->product_type == 'bundled') {
            $params = array();
            $params['product_id'] = $product_id;
            $bundledProducts = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getCombinedProducts($params);
            $tempBundleProductInfo = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct')->getColumnValue($product->product_id, "product_info");
            $bundleProductInfo = @unserialize($tempBundleProductInfo);
            if (!empty($bundleProductInfo) && !empty($bundleProductInfo['bundle_product_attribute'])) {
                $bundle_product_attributes = $bundleProductInfo['bundle_product_attribute'];
            }
            foreach ($bundledProducts as $individualProduct) {
                
                if($this->canAddtoCartError($individualProduct))
                    continue;

                $indProduct = $individualProduct->toArray();
                $indProduct['price'] = $productTable->getProductDiscountedPrice($individualProduct->product_id);
                $indProduct['information'] = Engine_Api::_()->getApi("Siteapi_Core","sitestoreproduct")->getPriceFields($individualProduct);
                $indProduct = array_merge($indProduct, Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($individualProduct));
                $indProductArray[] = $indProduct;
                if(empty($indProductArray))
                {
                    $error = $this->translate("No product is associated with this bundled product which is in stock");
                    $response['canAddToCart'] = 0;
                }
            }
            if (count($indProductArray) > 0) {
                $response['bundledProducts'] = $indProductArray;
            }
        }

        $response['menu'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitestoreproduct')->gutterMenus($sitestore, $product);
        $response['tabs'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitestoreproduct')->profileTabs($sitestore, $product);
        $response['owner_title'] = $product->getOwner()->getTitle();
        $response = array_merge($response, Engine_Api::_()->getApi('Core', 'siteapi')->getContentURL($product->getOwner(), "owner_url"));
        $response = array_merge($response, Engine_Api::_()->getApi('Core', 'siteapi')->getContentURL($product));
        $response['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');

        $photos = Engine_Api::_()->getDbtable('photos', 'sitestoreproduct')->GetProductPhoto($product->getIdentity());

        if ($photos) {
            foreach ($photos as $row => $value) {
                $photo = Engine_Api::_()->getItem('sitestoreproduct_photo', $value['photo_id']);
                $contentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($photo);
                unset($contentImages['content_url']);
                $response['images'][] = $contentImages;
            }
        }

        if ($product->product_type == 'configurable') {
            $form = Engine_Api::_()->getApi('Siteapi_Core', 'sitestoreproduct')->getCombinationOptions($product);
            $allowCombinations = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.combination', 1);
            if (!empty($form))
                $response['config'] = $form;
        }

        $productTags = $product->tags()->getTagMaps();
        $tagString = '';

        foreach ($productTags as $tagmap) {

            if ($tagString !== '')
                $tagString .= ', ';
            $tagString .= $tagmap->getTag()->getTitle();
        }

        if ($tagString)
            $response['tags'] = $tagString;

        $isAllowedView = $product->authorization()->isAllowed($viewer, 'view');
        $response["allow_to_view"] = empty($isAllowedView) ? 0 : 1;

        $isAllowedEdit = $product->authorization()->isAllowed($viewer, 'edit');
        $response["edit"] = empty($isAllowedEdit) ? 0 : 1;

        $isAllowedDelete = $product->authorization()->isAllowed($viewer, 'delete');
        $response["delete"] = empty($isAllowedDelete) ? 0 : 1;

        $like = $product->likes()->isLike($viewer);
        $response["is_liked"] = ($like) ? 1 : 0;

        $follow = Engine_Api::_()->getDbtable('follows', 'seaocore')->isFollow($product, $viewer);

        $response['is_followed'] = $follow ? 1 : 0;

        $response['information'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitestoreproduct')->getInformation($product, $values);

        $this->respondWithSuccess($response, false);
    }

    /*
     * product photos
     */
    public function photosAction() {
        $response = $tempResponse = array();

        $values = $this->getAllParams();
        $viewer = Engine_Api::_()->user()->getViewer();

        if (Engine_Api::_()->core()->hasSubject('sitestore_store'))
            $sitestore = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
        elseif ($values['action'] != 'category')
            $this->respondWithError('no_record');

        $product_id = $this->_getParam('product_id');

        if (!$product_id)
            $this->respondWithValidationError('parameter_missing', 'product_id missing');

        $product = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        if (!$product)
            $this->respondWithError('no_record');

        $productPhotos = Engine_Api::_()->getDbTable('photos', 'sitestoreproduct')->GetProductPhoto($product->getIdentity());

        $response['totalItemCount'] = count($productPhotos);

        if ($productPhotos) {
            foreach ($productPhotos as $row => $value) {
                $photo = Engine_Api::_()->getItem('sitestoreproduct_photo', $value['photo_id']);
                $data = $photo->toArray();
                $data = array_merge($data, Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($photo, false));
                $response['images'][] = $data;
            }
        }

        $this->respondWithSuccess($response, false);
    }

    /**
     *  Send a message to page owner
     */
    public function messageownerAction() {

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        $response = $tempResponse = array();

        $values = $this->getAllParams();
        $viewer = Engine_Api::_()->user()->getViewer();

        if (Engine_Api::_()->core()->hasSubject('sitestore_store'))
            $sitestore = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
        elseif ($values['action'] != 'category')
            $this->respondWithError('no_record');

        $product_id = $this->_getParam('product_id');

        if (!$product_id)
            $this->respondWithValidationError('parameter_missing', 'product_id missing');

        $product = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        if (!$product)
            $this->respondWithError('no_record');

        // Page owner can't send message to himself
        if ($viewer_id == $product->owner_id)
            $this->respondWithError('unauthorized');

        if ($this->getRequest()->isGet()) {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'Sitestoreproduct')->getMessageOwnerForm();
            $this->respondWithSuccess($response, true);
        } elseif ($this->getRequest()->isPost()) {

            // Get admins id for sending message
            $manageAdminData = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->getManageAdmin($sitestore->getIdentity());
            $manageAdminData = $manageAdminData->toArray();

            $recipients = array();
            if (!empty($manageAdminData)) {
                foreach ($manageAdminData as $key => $user_ids) {
                    $user_id = $user_ids['user_id'];
                    if ($viewer_id != $user_id) {
                        $recipients[] = $user_id;
                    }
                }
            }

            $values = $this->getAllParams();
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitestoreproduct')->getMessageOwnerFormValidators();
            $values['validators'] = $validators;
            $validationMessage = $this->isValid($values);

            // Response validation error
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
            $db->beginTransaction();

            try {
                // Limit recipients if it is not a special list of members
                $recipients = array_slice($recipients, 0, 1000);
                $recipients = array_unique($recipients);
                $recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);
                $sitestoreproduct_title = $product->title;
                $contentData = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($product, false);

                $product_title_with_link = "<a href ='" . $contentData['content_url'] . "'>$sitestoreproduct_title</a>";

                $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
                        $viewer, $recipients, $values['title'], $values['body'] . "<br><br>" . $this->translate("This message corresponds to the Product:") . $product_title_with_link
                );

                foreach ($recipientsUsers as $user) {
                    if ($user->getIdentity() == $viewer->getIdentity()) {
                        continue;
                    }

                    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
                            $user, $viewer, $conversation, 'message_new'
                    );
                }

                // Increment message counter
                Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
                $db->commit();
                $this->successResponseNoContent('no_content');
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     *  Message friends about this page
     */
    public function tellafriendAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (Engine_Api::_()->core()->hasSubject('sitestore_store'))
            $sitestore = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
        elseif ($values['action'] != 'category')
            $this->respondWithError('no_record');

        $product_id = $this->_getParam('product_id');

        if (!$product_id)
            $this->respondWithValidationError('parameter_missing', 'product_id missing');

        $product = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        if (!$product)
            $this->respondWithError('no_record');

        // Check user validation
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        // Get viewer
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // Get form
        if ($this->getRequest()->isGet()) {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'sitestoreproduct')->getTellAFriendForm();

            if ($viewer_id) {
                $response['formValues']['sender_email'] = $viewer->email;
                $response['formValues']['sender_name'] = $this->translate($viewer->displayname);
            }

            $this->respondWithSuccess($response, true);
        } else if ($this->getRequest()->isPost()) {

            // Get form values
            $values = $this->_getAllParams();

            // Start form validation
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitestoreproduct')->tellaFriendFormValidators();
            $values['validators'] = $validators;
            $validationMessage = $this->isValid($values);

            // Response validation error
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }
            // Explode email ids
            $reciver_ids = explode(',', $values['receiver_emails']);
            if (!empty($values['send_me'])) {
                $reciver_ids[] = $values['sender_email'];
            }
            $sender_email = $values['sender_email'];

            $heading = $product->title;

            // Check valid email id format
            $validator = new Zend_Validate_EmailAddress();
            $validator->getHostnameValidator()->setValidateTld(false);

            if (!$validator->isValid($sender_email)) {
                $this->respondWithValidationError('validation_fail', 'Invalid sender email address value');
            }

            if (!empty($reciver_ids)) {
                foreach ($reciver_ids as $receiver_id) {
                    $receiver_id = trim($receiver_id, ' ');
                    ($reciver_ids);
                    if (!$validator->isValid($receiver_id)) {
                        $this->respondWithValidationError('validation_fail', 'Please enter correct email address of the receiver(s).');
                    }
                }
            }

            $sender = $values['sender_name'];
            $message = $values['message'];
            $contentData = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($product, false);

            try {
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_ids, 'SITESTOREPRODUCT_TELLAFRIEND_EMAIL', array(
                    'host' => $_SERVER['HTTP_HOST'],
                    'sender' => $sender,
                    'heading' => $heading,
                    'message' => '<div>' . $message . '</div>',
                    'object_link' => $contentData['content_url'],
                    'email' => $sender_email,
                    'queue' => true
                ));
            } catch (Exception $ex) {
                $this->respondWithError('internal_server_error', $ex->getMessage());
            }
            $this->successResponseNoContent('no_content');
        }
    }

    //ACTION FOR ASK OPINION ABOUT PRODUCT
    public function askopinionAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (Engine_Api::_()->core()->hasSubject('sitestore_store'))
            $sitestore = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
        elseif ($values['action'] != 'category')
            $this->respondWithError('no_record');

        $product_id = $this->_getParam('product_id');

        if (!$product_id)
            $this->respondWithValidationError('parameter_missing', 'product_id missing');

        $product = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        if (!$product)
            $this->respondWithError('no_record');

        // Check user validation
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        // Get viewer
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // Get form
        if ($this->getRequest()->isGet()) {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'sitestoreproduct')->getTellAFriendForm();

            if ($viewer_id) {
                $response['formValues']['sender_email'] = $viewer->email;
                $response['formValues']['sender_name'] = $this->translate($viewer->displayname);
            }

            $this->respondWithSuccess($response, true);
        } else if ($this->getRequest()->isPost()) {

            // Get form values
            $values = $this->_getAllParams();

            // Start form validation
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitestoreproduct')->tellaFriendFormValidators();
            $values['validators'] = $validators;
            $validationMessage = $this->isValid($values);

            // Response validation error
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }
            // Explode email ids
            $reciver_ids = explode(',', $values['receiver_emails']);
            if (!empty($values['send_me'])) {
                $reciver_ids[] = $values['sender_email'];
            }
            $sender_email = $values['sender_email'];

            $heading = $product->title;

            // Check valid email id format
            $validator = new Zend_Validate_EmailAddress();
            $validator->getHostnameValidator()->setValidateTld(false);

            if (!$validator->isValid($sender_email)) {
                $this->respondWithValidationError('validation_fail', 'Invalid sender email address value');
            }

            if (!empty($reciver_ids)) {
                foreach ($reciver_ids as $receiver_id) {
                    $receiver_id = trim($receiver_id, ' ');
                    ($reciver_ids);
                    if (!$validator->isValid($receiver_id)) {
                        $this->respondWithValidationError('validation_fail', 'Please enter correct email address of the receiver(s).');
                    }
                }
            }

            $sender = $values['sender_name'];
            $message = $values['message'];
            $contentData = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($product, false);

            try {
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_ids, 'SITESTOREPRODUCT_ASKOPINION_EMAIL', array(
                    'host' => $_SERVER['HTTP_HOST'],
                    'sender' => $sender,
                    'heading' => $heading,
                    'message' => '<div>' . $message . '</div>',
                    'object_link' => $contentData['content_url'],
                    'email' => $sender_email,
                    'queue' => true
                ));
            } catch (Exception $ex) {
                $this->respondWithError('internal_server_error', $ex->getMessage());
            }
            $this->successResponseNoContent('no_content');
        }
    }

    /*
     * Product search form
     */

    public function productSearchFormAction() {
        $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'sitestoreproduct')->productSearchForm());
    }

    /*
     * get variation options
     */

    public function variationOptionAction() {
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();
        $values = $this->_getAllParams();

        $defaultPrice = $this->_getParam('price', 0);

        // match option_id
        $key = array_values(preg_grep("/^select_([0-9]+)/", array_keys($values)));

        if (!empty($key))
            $key = $key[0];
        else
            $this->respondWithValidationError('parameter_missing', 'option parameter missing');

        $combination_attribute_id = $values[$key];

        if (!$values['product_id'])
            $this->respondWithValidationError('parameter_missing', 'product_id missing');

        $product = Engine_Api::_()->getItem('sitestoreproduct_product', $values['product_id']);
        $profileFields = Engine_Api::_()->getDbtable('Productfields', 'sitestoreproduct');
        $cartProductFieldsMeta = Engine_Api::_()->getDbTable('CartproductFieldMeta', 'sitestoreproduct');
        $cartProductFieldsOptions = Engine_Api::_()->getDbtable('CartproductFieldOptions', 'sitestoreproduct');
        $combinationAttributesTable = Engine_Api::_()->getDbTable('CombinationAttributes', 'sitestoreproduct');
        $combinationTable = Engine_Api::_()->getDbTable('Combinations', 'sitestoreproduct');
        $combinationAttributeMapsTable = Engine_Api::_()->getDbTable('CombinationAttributeMap', 'sitestoreproduct');
        $combinationTableName = $combinationTable->info('name');
        $combinationAttributesTableName = $combinationAttributesTable->info('name');
        $combinationAttributeMapsTableName = $combinationAttributeMapsTable->info('name');
        $cartProductFieldsMetaTableName = $cartProductFieldsMeta->info('name');
        $cartProductFieldsOptionsTableName = $cartProductFieldsOptions->info('name');

        $combinationAttributeSelect = $combinationAttributesTable->select()
                ->from($combinationAttributesTableName)
                ->setIntegrityCheck(false)
                ->joinInner($combinationAttributeMapsTableName, $combinationAttributeMapsTableName . ".attribute_id = " . $combinationAttributesTableName . ".attribute_id", array('combination_id'))
                ->joinInner($combinationTableName, $combinationTableName . ".combination_id = " . $combinationAttributeMapsTableName . ".combination_id", array('status', 'quantity'))
                ->where("$combinationAttributesTableName.combination_attribute_id = ?", $combination_attribute_id);

        $combinationAttributeData = $combinationAttributeSelect->query()->fetchAll();

        if (!$combinationAttributeData)
            $this->respondWithError("no_record");

        $price = $combinationAttributeData[0]['price'];

        if (!isset($defaultPrice) || empty($defaultPrice))
            $defaultPrice = $product->price;

        if ($combinationAttributeData[0]['price_increment'])
            $defaultPrice += $price;
        else
            $defaultPrice -= $price;

        $combinationidsArray = array();
        $order = $combinationAttributeData[0]['order'];

        foreach ($combinationAttributeData as $row)
            $combinationidsArray[] = $row['combination_id'];

        $combinationAttributeSelect = $combinationAttributesTable->select()
                ->from($combinationAttributesTableName)
                ->setIntegrityCheck(false)
                ->joinInner($combinationAttributeMapsTableName, $combinationAttributeMapsTableName . ".attribute_id = " . $combinationAttributesTableName . ".attribute_id", array())
                ->joinInner($cartProductFieldsMetaTableName, "$cartProductFieldsMetaTableName.field_id = $combinationAttributesTableName.field_id", array('label as field_label'))
                ->joinInner($cartProductFieldsOptionsTableName, "$cartProductFieldsOptionsTableName.option_id = $combinationAttributesTableName.combination_attribute_id", array('label'))
                ->where("$combinationAttributeMapsTableName.combination_id in (?)", $combinationidsArray)
                ->where("$combinationAttributesTableName.order = ?", $order + 1);

        $combinationNextData = $combinationAttributeSelect->query()->fetchALL();

        // if(empty($combinationNextData))
        //     $this->respondWithSuccess(array('productPrice' => $defaultNamespace->productPrice));

        $fieldArray = $response = array();

        foreach ($combinationNextData as $attribute) {
            $price = $attribute['price'];
            unset($data);
            $data = array('label' => $attribute['label']);
            $data['price'] = number_format($price, 2);
            $data['price_increment'] = false;
            if ($attribute['price_increment'])
                $data['price_increment'] = true;

            if (empty($fieldArray)) {
                $fieldArray = array(
                    'name' => "select_" . $attribute['field_id'],
                    'label' => $attribute['field_label'],
                    'type' => 'select',
                    'order' => $attribute['order'] + 1,
                    'multiOptions' => array(
                        '0' => $this->translate('-- select --'),
                        $attribute['combination_attribute_id'] => $data,
                    ),
                );
            } else
                $fieldArray['multiOptions'][$attribute['combination_attribute_id']]['label'] = $this->translate($attribute['label']);
        }

        $response['field'] = $fieldArray;
        $response['productPrice'] = $defaultPrice;

        if (empty($response['field'])) {
            foreach ($combinationAttributeData as $row => $combination) {
                if ($combination['quantity']) {
                    $response['quantity_available'] = $combination['quantity'];
                    $response['combination_id'] = $combination['combination_id'];
                    $response['status'] = $combination['status'];
                    break;
                }
            }

            if (!$response['status'])
                $response['error'] = $this->translate("This combination is not enabled , please choose another one");

            if (!$response['quantity_available'])
                $response['error'] = $this->translate("The product with these combinations is out of stock");
        }

        $this->respondWithSuccess($response, false);
    }

    /*
     * Add to cart
     */

    public function addToCartAction() {
        $this->validateRequestMethod("POST");
        // Get viewer
        $viewer = Engine_Api::_()->user()->getViewer();
        $values = $this->_getAllParams();
        $product_id = $values['product_id'];

        $configData = $values['product_config'] ? Zend_Json::decode(urldecode($values['product_config'])) : 0;

        if($configData)
        {
            foreach($configData as $row => $value)
                if(is_string($value) && strpos($value, ',')!==false)
                    $configData[$row] = explode(',', $value);
        }
        $productTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');

        $viewer_id = $viewer->getIdentity();

        if (!empty($viewer_id)) {
            $level_id = Engine_Api::_()->user()->getViewer()->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        if (!$values['product_id'])
            $this->respondWithValidationError('parameter_missing', 'product_id missing');

        $db = Engine_Db_Table::getDefaultAdapter();

        $productObj = $product = Engine_Api::_()->getItem('sitestoreproduct_product', $values['product_id']);

        if (!$productObj)
            $this->respondWithError('no_record');

        if ($productObj->product_type == 'configurable') {

            $sql = "select count(*) as count from engine4_sitestoreproduct_combination_attributes where product_id='".$values['product_id']."'";
            $result = $db->query($sql)->fetchALL();
            
            if($result[0]['count']==0)
                $this->respondWithError('unauthorized', "Store Admin didn't define and configurations for this product");

            if (!$configData['combination_id'])
                $this->respondWithError('unauthorized', 'Please select configurations of product');

            // check for whether can apply this combination id
            $combinationTable = Engine_Api::_()->getDbTable("combinations", "sitestoreproduct");

            $select = $combinationTable->select()
                    ->where('combination_id = ?', $configData['combination_id']);

            $combination = $select->query()->fetchALL();

            if (!$combination)
                $this->respondWithError('unauthorized', 'No such Combination exists');

            $combination = $combination[0];

            if (!$combination['status'])
                $this->respondWithError("unauthorized", $this->translate("The combination does not exist"));

            unset($configData['combination_id']);
        }

        if ($product->product_type == 'downloadable') {
            $isAnyFileExist = Engine_Api::_()->getDbtable('downloadablefiles', 'sitestoreproduct')->isAnyMainFileExist($product->getIdentity());

            if (empty($isAnyFileExist))
                $this->respondWithError('unauthorized', 'Product is downloadable but there are no downloadable files present, please contact the store admin');
        }

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
            if (!empty($productObj->draft) || !empty($productObj->closed) || empty($productObj->search) || empty($productObj->approved) || $productObj->start_date > date('Y-m-d H:i:s') || ($productObj->end_date < date('Y-m-d H:i:s') && !empty($productObj->end_date_enable))) {
                $this->respondWithError('unauthorized', $this->translate("This product is currently not available for purchase"));
            }
        } else {
            if (!empty($productObj->draft) || empty($productObj->search) || empty($productObj->approved) || $productObj->start_date > date('Y-m-d H:i:s') || ($productObj->end_date < date('Y-m-d H:i:s') && !empty($productObj->end_date_enable))) {
                $this->respondWithError('unauthorized', $this->translate("This product is currently not available for purchase"));
            }
        }

        $temp_allowed_selling = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($productObj->store_id);
        if (empty($temp_allowed_selling) || empty($productObj->allow_purchase)) {
            $this->respondWithError('unauthorized', $this->translate("This product is currently not available for purchase"));
        }


        // if the user is not logged in then we will just give them success message and android/ios developers will save the product with configurations on their side

        if (!$viewer_id)
            $this->successResponseNoContent('no_content');

        $productsArray = array();

        if ($productObj->product_type == 'grouped') {
            $params = array();
            $params['product_type'] = 'grouped';
            $params['product_id'] = $productObj->getIdentity();

            $groupedProducts = $productTable->getCombinedProducts($params);

            foreach ($groupedProducts as $individualProduct) {
                $temp_allowed_selling = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($individualProduct->store_id);

                if (!(!empty($temp_allowed_selling) && $product->allow_purchase && ((int) ($product->stock_unlimited || $product->in_stock))))
                    continue;
                $productsArray[] = $individualProduct->getIdentity();
            }
        }

        if (empty($productsArray))
            $productsArray[] = $productObj->getIdentity();

        $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
        $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);

        $cartTable = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct');
        $cart_product_table = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct');

        $cart_id = $cartTable->getCartId($viewer_id);

        $db = $cartTable->getAdapter();
        $db->beginTransaction();

        try {
            foreach ($productsArray as $row => $product_id) {
                $productObj = Engine_Api::_()->getItem("sitestoreproduct_product", $product_id);
                if (empty($cart_id)) {
                    $row = $cartTable->createRow();
                    $row->setFromArray(array('owner_id' => $viewer_id));
                    $cart_id = $row->save();

                    $lastInsertId = $cart_product_table->insert(array('cart_id' => $cart_id, 'product_id' => $product_id, 'quantity' => $productObj->min_order_quantity));

                    if ($productObj->product_type == 'configurable') {
                        $cartProductFieldValue = Engine_Api::_()->getDbtable('CartProductFieldValues', 'sitestoreproduct');
                        unset($configData['combination_id']);
                        foreach ($configData as $row => $value) {
                            $key = explode('_', $row);
                            if (count($key) == 2) {
                                $cartProductFieldValue->insert(array('item_id' => $lastInsertId, 'field_id' => $key[1], 'value' => $value, 'category_attribute' => 1));
                            } elseif (count($key) == 3) {
                                if (is_array($value)) {
                                    foreach ($value as $subrow => $subvalue) {
                                        $cartProductFieldValue->insert(array('item_id' => $lastInsertId, 'field_id' => $key[2], 'value' => $subvalue, 'category_attribute' => 0, 'index' => $subrow));
                                    }
                                    continue;
                                }

                                $cartProductFieldValue->insert(array('item_id' => $lastInsertId, 'field_id' => $key[2], 'value' => $value, 'category_attribute' => 0));
                            }
                        }
                    }
                } else {
                    // CHECK PRODUCT PAYMENT TYPE => DOWNPAYMENT OR NOT
                    $ids = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct')->getProductCounts($cart_id);
                    if (!empty($directPayment) && !empty($isDownPaymentEnable) && !empty($ids)) {
                        $productIds = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->getCartProductIds($cart_id);
                        $product_ids = implode(",", $productIds);

                        $selectedProductDownpaymentValue = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct')->getColumnValue($productObj->product_id, 'downpayment_value');

                        $cartProductPaymentType = Engine_Api::_()->sitestoreproduct()->getProductPaymentType($product_ids);

                        if (empty($selectedProductDownpaymentValue) && !empty($cartProductPaymentType)) {
                            $this->respondWithValidationError('validation_fail', $this->translate("You can't add to cart this product right now as your cart contain products which have enabled downpayment and for this product downpayment is not enabled."));
                        } else if (!empty($selectedProductDownpaymentValue) && empty($cartProductPaymentType)) {
                            $this->respondWithValidationError('validation_fail', $this->translate("You can't add to cart this product right now as your cart contain products for which downpayment is not enabled and for this product downpayment is enabled."));
                        }
                    }

                    $cart_product_values = $cart_product_table->getConfigurationId($product_id, $cart_id);
                    $cart_product_obj = null;
                    if (!empty($cart_product_values) && $productObj->product_type == 'configurable') {
                        foreach ($cart_product_values as $row => $value) {
                            $cartProduct = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $value);
                            $fieldValues = Engine_Api::_()->fields()->getFieldsValues($cartProduct);
                            $cartProductFieldValue = $fieldValues->getRowsMatching(array(
                                'item_id' => $cartProduct->getIdentity(),
                            ));
                            $fieldvalueArray = array();
                            foreach ($cartProductFieldValue as $fieldIndex => $fieldValue) {
                                $fieldValue = $fieldValue->toArray();
                                if ($fieldValue['category_attribute'])
                                    $fieldvalueArray['select_' . $fieldValue['field_id']] = $fieldValue['value'];
                                else {
                                    if (isset($fieldvalueArray[$productObj->store_id . '_' . $productObj->getIdentity() . '_' . $fieldValue['field_id']])) {
                                        if (!is_array($fieldvalueArray[$productObj->store_id . '_' . $productObj->getIdentity() . '_' . $fieldValue['field_id']])) {
                                            $tempdata = $fieldvalueArray[$productObj->store_id . '_' . $productObj->getIdentity() . '_' . $fieldValue['field_id']];
                                            $fieldvalueArray[$productObj->store_id . '_' . $productObj->getIdentity() . '_' . $fieldValue['field_id']] = array();
                                            $fieldvalueArray[$productObj->store_id . '_' . $productObj->getIdentity() . '_' . $fieldValue['field_id']][] = $tempdata;
                                        }
                                        $fieldvalueArray[$productObj->store_id . '_' . $productObj->getIdentity() . '_' . $fieldValue['field_id']][] = $fieldValue['value'];
                                        continue;
                                    }
                                    $fieldvalueArray[$productObj->store_id . '_' . $productObj->getIdentity() . '_' . $fieldValue['field_id']] = $fieldValue['value'];
                                }
                            }
                            $array_diff_assoc = Engine_Api::_()->sitestoreproduct()->multidimensional_array_diff($fieldvalueArray, $configData);
                            if ($array_diff_assoc) {
                                $cart_product_obj = $cartProduct;
                                break;
                            }
                        }
                    } else if (!empty($cart_product_values))
                        $cart_product_obj = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $cart_product_values[0]);



                    // (array('cart_id = ?' => $cart_id, 'product_id =?' => $product_id));
                    // IF PRODUCT IS NOT IN VIEWER CART, THEN ADD IT TO CART
                    if (empty($cart_product_obj)) {
                        $lastInsertId = $cart_product_table->insert(array('cart_id' => $cart_id, 'product_id' => $product_id, 'quantity' => $productObj->min_order_quantity));

                        if ($productObj->product_type == 'configurable') {
                            $cartProductFieldValue = Engine_Api::_()->getDbtable('CartProductFieldValues', 'sitestoreproduct');
                            foreach ($configData as $row => $value) {
                                $key = explode('_', $row);

                                if (count($key) == 2) {
                                    $cartProductFieldValue->insert(array('item_id' => $lastInsertId, 'field_id' => $key[1], 'value' => $value, 'category_attribute' => 1));
                                } elseif (count($key) == 3) {
                                    if (is_array($value)) {
                                        foreach ($value as $subrow => $subvalue) {
                                            try {
                                                $cartProductFieldValue->insert(array('item_id' => $lastInsertId, 'field_id' => $key[2], 'value' => $subvalue, 'category_attribute' => 0, 'index' => $subrow));
                                            } catch (Exception $e) {
                                                // null exception
                                            }
                                        }
                                        continue;
                                    }
                                    $cartProductFieldValue->insert(array('item_id' => $lastInsertId, 'field_id' => $key[2], 'value' => $value, 'category_attribute' => 0));
                                }
                            }
                        }
                    } else {
                        $product_qty = $cart_product_obj->quantity;
                        $updatedQty = $product_qty + 1;

                        if (empty($productObj->stock_unlimited) && empty($productObj->in_stock)) {
                            $this->respondWithValidationError('validation_fail', $this->translate("This product is currently not available for purchase."));
                        } elseif (empty($productObj->stock_unlimited) && $productObj->in_stock < $updatedQty) {
                            if ($productObj->in_stock == 1)
                                $this->respondWithValidationError('validation_fail', $this->translate("Only 1 quantity of this product is available in stock. Please enter the quantity as 1."));
                            else
                                $this->respondWithValidationError('validation_fail', $this->translate("Only %s quantities of this product are available in stock. Please enter the quantity less than or equal to %s.", $productObj->in_stock, $productObj->in_stock));
                        }
                        else if (!empty($productObj->max_order_quantity) && $updatedQty > $productObj->max_order_quantity) {
                            if ($productObj->max_order_quantity == 1)
                                $this->respondWithValidationError('validation_fail', $this->translate("You can purchase maximum 1 quantity of this product in a single order. So, please enter the quantity as 1."));
                            else
                                $this->respondWithValidationError('validation_fail', $this->translate("You can purchase maximum %s quantities of this product in a single order. So, please enter the quantity as less than or equal to %s.", $productObj->max_order_quantity, $productObj->max_order_quantity));
                        }
                        else {
                            $dbsql = $cart_product_table->getAdapter();
                            $sql = "update " . $cart_product_table->info('name') . " set quantity=quantity+1 where cartproduct_id=?";
                            $query = $dbsql->query($sql,array($cart_product_obj->getIdentity()));
                        }
                    }
                }
            }
            $db->commit();
            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Returns Categories , Sub-Categories, SubSub-Categories and pages array
     * 
     */
    public function categoryAction() {
        // Validate request method
        $this->validateRequestMethod();

        // Get viewer
        $viewer = Engine_Api::_()->user()->getViewer();

        // Prepare response
        $values = $response = array();
        $category_id = $this->getRequestParam('category_id', null);
        $subCategory_id = $this->getRequestParam('subcategory_id', null);
        $subsubcategory_id = $this->getRequestParam('subsubcategory_id', null);
        $showAllCategories = $this->getRequestParam('showAllCategories', 1);
        $showCategories = $this->getRequestParam('showCategories', 1);
        $showProducts = $this->getRequestParam('showProducts', 1);
        if ($this->getRequestParam('showCount')) {
            $showCount = 1;
        } else {
            $showCount = $this->getRequestParam('showCount', 0);
        }

        $orderBy = $this->getRequestParam('orderBy', 'category_name');

        $productApi = Engine_Api::_()->getApi('Siteapi_Core', 'sitestoreproduct');
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        $categories = array();
        // Get pages table
        $tableSitepage = Engine_Api::_()->getDbtable('stores', 'sitestore');
        $sitepageShowAllCategories = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.categorywithslug', 1);
        $showAllCategories = !empty($sitepageShowAllCategories) ? $showAllCategories : 0;

        if ($showCategories) {
            $product = 1;
            if ($showAllCategories)
                $product = 0;

            $category_info = $productApi->getAllCategories(0, 'category_id', $product, '', '', '');

            $categoriesCount = count($category_info);

            foreach ($category_info as $value) {
                $sub_cat_array = array();
                $category_array = array('category_id' => $value->category_id,
                    'category_name' => $this->translate($value->category_name),
                    'order' => $value->cat_order,
                    'images' => Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($value),
                );
                if ($showCount)
                    $category_array['count'] = $value->count;

                $response['categories'][] = $category_array;
            }

            if (!empty($category_id)) {

                $product = 1;
                if ($showAllCategories)
                    $product = 0;

                $category_info2 = $productApi->getAllCategories($category_id, 'subcategory_id', $product, '', '', '');
                foreach ($category_info2 as $subresults) {
                    $tmp_array = array('sub_cat_id' => $subresults->category_id,
                        'sub_cat_name' => $this->translate($subresults->category_name),
                        'order' => $subresults->cat_order);

                    if ($showCount)
                        $tmp_array['count'] = $subresults->count;

                    $response['subCategories'][] = $tmp_array;
                }
            }

            if (!empty($subCategory_id)) {

                $product = 1;
                if ($showAllCategories)
                    $product = 0;

                $category_info2 = $productApi->getAllCategories($subCategory_id, 'subsubcategory_id', $product, '', '', '');
                foreach ($category_info2 as $subsubresults) {
                    $tmp_array = array('tree_sub_cat_id' => $subsubresults->category_id,
                        'tree_sub_cat_name' => $this->translate($subsubresults->category_name),
                        'order' => $subsubresults->cat_order);

                    if ($showCount)
                        $tmp_array['count'] = $subsubresults->count;

                    $response['subsubCategories'][] = $tmp_array;
                }
            }
        }

        // show categories at browse page
        // if ($this->getRequestParam('action', null) == 'browse')
        //     return $response;

        if ($showProducts && isset($category_id) && !empty($category_id)) {

            $params = array();

            $itemCount = $params['itemCount'] = $this->_getParam('itemCount', 0);

            // Get categories
            $categories = array();

            $category_products_array = array();

            $values = $this->getAllParams();
            $values['action'] = "category";

            if (empty($values['page']))
                $values['page'] = 1;

            if (empty($values['limit']))
                $values['limit'] = 20;

            if (@$values['show'] == 2) {
                $friends = $viewer->membership()->getMembers();

                foreach ($friends as $friend)
                    $values['users'][] = $friend->user_id;
            } else if (!isset($values['show']))
                $values['show'] = 3;

            // get the stores
            if(isset($values['subCategory_id']))
                $values['subcategory_id'] = $values['subCategory_id'];

            $response['response'] = $this->getStoreProductData($values);
        }

        if (isset($categoriesCount) && !empty($categoriesCount))
            $response['totalItemCount'] = $categoriesCount;

        $response['canCreate'] = Engine_Api::_()->authorization()->isAllowed('sitestore_store', $viewer, 'create');
        $response['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');

        $this->respondWithSuccess($response, false);
    }

    private function _getOrderByOptions() {
        $settings = $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $storesearchsettings = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');

        $row = $storesearchsettings->getFieldsOptions('sitestoreproduct', 'orderby');
        if (!empty($row) && !empty($row->display)) {
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2) {
                if (Engine_Api::_()->sitestore()->isCommentsAllow("sitestoreproduct_product")) {
                    $multiOptionsOrderBy = array(
                        '' => "",
                        'price_low_to_high' => 'Price low to high',
                        'price_high_to_low' => 'Price high to low',
                        'discount_amount' => 'Most Discounted',
                        'title' => "Alphabetic",
                        'product_id' => 'Most Recent',
                        'view_count' => 'Most Viewed',
                        'like_count' => "Most Liked",
                        'comment_count' => "Most Commented",
                        'review_count' => "Most Reviewed",
                        'rating_avg' => "Most Rated",
                    );
                } else {
                    $multiOptionsOrderBy = array(
                        '' => "",
                        'price_low_to_high' => 'Price low to high',
                        'price_high_to_low' => 'Price high to low',
                        'discount_amount' => 'Most Discounted',
                        'title' => "Alphabetic",
                        'product_id' => 'Most Recent',
                        'view_count' => 'Most Viewed',
                        'like_count' => "Most Liked",
//            'comment_count' => "Most Commented",
                        'review_count' => "Most Reviewed",
                        'rating_avg' => "Most Rated",
                    );
                }
            } elseif (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 1) {
                if (Engine_Api::_()->sitestore()->isCommentsAllow("sitestoreproduct_product")) {
                    $multiOptionsOrderBy = array(
                        '' => "",
                        'price_low_to_high' => 'Price low to high',
                        'price_high_to_low' => 'Price high to low',
                        'discount_amount' => 'Most Discounted',
                        'title' => "Alphabetic",
                        'product_id' => 'Most Recent',
                        'view_count' => 'Most Viewed',
                        'like_count' => "Most Liked",
                        'comment_count' => "Most Commented",
                        'rating_avg' => "Most Rated",
                    );
                } else {
                    $multiOptionsOrderBy = array(
                        '' => "",
                        'price_low_to_high' => 'Price low to high',
                        'price_high_to_low' => 'Price high to low',
                        'discount_amount' => 'Most Discounted',
                        'title' => "Alphabetic",
                        'product_id' => 'Most Recent',
                        'view_count' => 'Most Viewed',
                        'like_count' => "Most Liked",
//            'comment_count' => "Most Commented",
                        'rating_avg' => "Most Rated",
                    );
                }
            } else {
                if (Engine_Api::_()->sitestore()->isCommentsAllow("sitestoreproduct_product")) {
                    $multiOptionsOrderBy = array(
                        '' => "",
                        'price_low_to_high' => 'Price low to high',
                        'price_high_to_low' => 'Price high to low',
                        'discount_amount' => 'Most Discounted',
                        'title' => "Alphabetic",
                        'product_id' => 'Most Recent',
                        'view_count' => 'Most Viewed',
                        'like_count' => "Most Liked",
                        'comment_count' => "Most Commented",
                    );
                } else {
                    $multiOptionsOrderBy = array(
                        '' => "",
                        'price_low_to_high' => 'Price low to high',
                        'price_high_to_low' => 'Price high to low',
                        'discount_amount' => 'Most Discounted',
                        'title' => "Alphabetic",
                        'product_id' => 'Most Recent',
                        'view_count' => 'Most Viewed',
                        'like_count' => "Most Liked",
//            'comment_count' => "Most Commented",
                    );
                }
            }
        }
        return $multiOptionsOrderBy;
    }

    private function getSiteUrl() {
        $staticBaseUrl = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.static.baseurl', null);
        $serverHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
        $getDefaultStorageId = Engine_Api::_()->getDbtable('services', 'storage')->getDefaultServiceIdentity();
        $getDefaultStorageType = Engine_Api::_()->getDbtable('services', 'storage')->getService($getDefaultStorageId)->getType();
        $getHost = '';
        $getHost = !empty($staticBaseUrl) ? $staticBaseUrl : $serverHost;

        return $getHost;
    }

    private function canAddtoCartError($product)
    {
        $error = null;
        if(!$product)
            return $this->translate("NO such product exists");
        
        $in_stock_product = (int) ($product->stock_unlimited || $product->in_stock);
        if ((!$in_stock_product))
            $error = $this->translate("Product is out of stock");

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
            if (!empty($product->draft) || !empty($product->closed) || empty($product->search) || empty($product->approved) || $product->start_date > date('Y-m-d H:i:s') || ($product->end_date < date('Y-m-d H:i:s') && !empty($product->end_date_enable))) {
                $error = $this->translate("This product is currently not available for purchase.");
            }
        } else {
            if (!empty($product->draft) || empty($product->search) || empty($product->approved) || $product->start_date > date('Y-m-d H:i:s') || ($product->end_date < date('Y-m-d H:i:s') && !empty($product->end_date_enable))) {
                $error = $this->translate("This product is currently not available for purchase.");
            }
        }

        $temp_allowed_selling = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($product->store_id);
        if (empty($temp_allowed_selling) || empty($product->allow_purchase)) {
            $error =  $this->translate("This product is currently not available for purchase.");
        }

        return $error;
    }

    public function getcombinationAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $values = $this->_getAllParams();
        $product_id = $values['product_id'];
        if(!$product_id)
            $this->respondWithValidationError("parameter_missing" , "product_id missing");

        $product = Engine_Api::_()->getItem("sitestoreproduct_product" , $product_id);
        
        if(!$product)
            $this->respondWithError("no_record");

        $productsTable = Engine_Api::_()->getDbTable("products" , "sitestoreproduct");
        $db = $productsTable->getAdapter();

        $attributeIdsString = "";

        foreach($values as $key => $value)
        {
            $keyArray = explode("_", $key);
            if($keyArray[0] == 'select')
            {
                $sql = "select attribute_id from engine4_sitestoreproduct_combination_attributes where field_id='".$keyArray[1]."' and combination_attribute_id='".$value."'";

                $result = $db->query($sql)->fetch();

                $attributeIdsString .= "'".$result['attribute_id']."',";
            }
        }

        $attributeIdsString = trim($attributeIdsString, ",");

        $sql = "select * from engine4_sitestoreproduct_combination_attributes_map 
                group by combination_id
                having attribute_id in ($attributeIdsString)";

        $result = $db->query($sql)->fetch();

        $response = array();

        if(!$result['combination_id'])
            $this->respondWithError('no_record');

        $response['combination_id'] = $result['combination_id'];
        $combinationPrice = Engine_Api::_()->getApi("Siteapi_Core" , 'sitestore')->getcombinationPrice($response['combination_id'] , $product_id);

        $isVatAllow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0);

        if (!empty($isVatAllow)) {
                $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product);
            $productDiscountedPrice = number_format($productPricesArray['product_price_after_discount'], 2);

        } else {
            $productDiscountedPrice = $productsTable->getProductDiscountedPrice($product->product_id);
        }

        $response['price'] = $productDiscountedPrice + $combinationPrice;

        $this->respondWithSuccess($response , true);

    }

}
