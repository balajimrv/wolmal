<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    IndexController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_StoreController extends Siteapi_Controller_Action_Standard {

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
     * Get store view
     */

    public function viewAction() {

        $viewer = Engine_Api::_()->user()->getViewer();

        $this->validateRequestMethod();

        if (Engine_Api::_()->core()->hasSubject('sitestore_store'))
            $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
        else
            $this->respondWithError('no_record');

        $response = $subject->toArray();

        // Set the store url
        $contentUrl = Engine_Api::_()->getApi('Core', 'siteapi')->getContentURL($subject);
        $response = @array_merge($response, $contentUrl);


        $like = $subject->likes()->isLike($viewer);
        $response["is_liked"] = ($like) ? 1 : 0;

        $follow = Engine_Api::_()->getDbtable('follows', 'seaocore')->isFollow($subject, $viewer);

        $response['is_followed'] = $follow ? 1 : 0;

        $response['owner_title'] = $subject->getOwner()->getTitle();

        // Set the store images
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($subject);
        $response = array_merge($response, $getContentImages);

        // Set the store owner images
        $getContentOwnerImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($subject, true);
        $response = array_merge($response, $getContentOwnerImages);

        // Set the gutter menus
        $response['gutterMenu'] = $this->_getGutterMenu($subject);

        $response = array_merge($response, Engine_Api::_()->getApi('Siteapi_Core', 'sitestore')->getInformation($subject));

        // Set the profile tabs
        $response['profileTabs'] = $this->_getProfileTabs($subject);
        $overview = $response['overview'];
        if (!empty($overview)) {
            $staticBaseUrl = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.static.baseurl', null);
            $serverHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
            $getDefaultStorageId = Engine_Api::_()->getDbtable('services', 'storage')->getDefaultServiceIdentity();
            $getDefaultStorageType = Engine_Api::_()->getDbtable('services', 'storage')->getService($getDefaultStorageId)->getType();

            $this->getHost = '';
            if ($getDefaultStorageType == 'local')
                $this->getHost = !empty($staticBaseUrl) ? $staticBaseUrl : $serverHost;
            $response['overview'] = str_replace('src="/', 'src="' . $this->getHost . '/', $overview);
            $response['overview'] = str_replace('"', "'", $response['overview']);
        }
        $response['overview'] = ($response['overview']) ? $this->translate($response['overview']) : "";

        $this->respondWithSuccess($response);
    }

    /*
     * Gutter Menu: Open / Close store
     */

    public function closeAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (Engine_Api::_()->core()->hasSubject('sitestore_store'))
            $sitestore = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
        else
            $this->respondWithError('no_record');

        $this->validateRequestMethod("POST");
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $sitestore->closed = !$sitestore->closed;
            $sitestore->save();
            $db->commit();
            $this->successResponseNoContent('no_content');
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * delete Store
     */

    public function deleteAction() {
        $viewer = Engine_Api::_()->user()->getViewer();


        // Require user
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject('sitestore_store'))
            $sitestore = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
        else
            $this->respondWithError('no_record');

        $this->validateRequestMethod("DELETE");

        // Start manage-admin check
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'delete');
        if (empty($isManageAdmin))
            $this->respondWithError('unauthorized');
        // End manage-admin check

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            Engine_Api::_()->sitestore()->onStoreDelete($sitestore->getIdentity());
            $db->commit();
            $this->successResponseNoContent('no_content');
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * Tabbed: Get store information
     */

    public function informationAction() {
        $this->validateRequestMethod();

        if (Engine_Api::_()->core()->hasSubject('sitestore_store'))
            $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
        else
            $this->respondWithError('no_record');

        try {
            $getProfileInfo = Engine_Api::_()->getApi('Siteapi_Core', 'sitestore')->getInformation($subject);
            if (isset($_REQUEST['field_order']) && !empty($_REQUEST['field_order']) && $_REQUEST['field_order'] == 1) {

                $getProfileInfo = Engine_Api::_()->getApi('Core', 'siteapi')->responseFormat($getProfileInfo);
            }
            $this->respondWithSuccess($getProfileInfo);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * Overview of Store
     */

    public function overviewAction() {

        $viewer = Engine_Api::_()->user()->getViewer();

        // Require user
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        $this->validateRequestMethod();

        if (Engine_Api::_()->core()->hasSubject('sitestore_store'))
            $sitestore = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
        else
            $this->respondWithError('no_record');

        $this->respondWithSuccess($this->translate($subject->overview));
    }

    /*
     * Photo Controller
     */

    public function albumAction() {
        $tempResponse = array();
        $viewer = Engine_Api::_()->user()->getViewer();

        // Require user
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject('sitestore_store'))
            $sitestore = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
        else
            $this->respondWithError('no_record');

        $this->validateRequestMethod();

        $albums = Engine_Api::_()->getDbtable('albums', 'sitestore')->getAlbums($subject);

        $response = array();
        $response['totalItemCount'] = $albums->count();

        if ($albums) {
            foreach ($albums as $row => $album) {
                $data = $album->toArray();
                $data = array_merge($data, Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($album));
                $data = array_merge($data, Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($album, true));
                $data['photoCount'] = Engine_Api::_()->getDbtable('photos', 'sitestore')->getPhotosCount(array('album_id' => $album->getIdentity()));
                $tempResponse[] = $data;
            }
            $response['response'] = $tempResponse;
        }

        $this->respondWithSuccess($response);
    }

    /*
     * Invite Via Email
     */

    public function inviteAction() {
        // Get viewer
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (Engine_Api::_()->core()->hasSubject('sitestore_store'))
            $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
        else
            $this->respondWithError('no_record');

        // Get form
        if ($this->getRequest()->isGet()) {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'Sitestore')->inviteForm();
            $this->respondWithSuccess($response, true);
        } else if ($this->getRequest()->isPost()) {

            $values = $this->_getAllParams();

            if (!isset($values['emails']) || empty($values['emails']))
                $this->respondWithValidationError("parameter_missing", "Please Enter the emails");

            if (!isset($values['message']) || empty($values['message']))
                $this->respondWithValidationError("parameter_missing", "Please enter the message");

            $emails = explode(",", $values['emails']);

            foreach ($emails as $email) {
                if (!empty($email)) {
                    if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
                        $this->respondWithValidationError("parameter_missing", 'Please enter valid email address.');
                    }
                }
            }

            // Sendemails
            try {
                Engine_Api::_()->getApi("Siteapi_Core", "sitestore")->sendInvites($emails, $subject->getIdentity(), $viewer->getIdentity(), $message);
                $this->successResponseNoContent("no_content");
            } catch (Exception $ex) {
                echo $ex;
                die;
                $this->respondWithValidationError("internal_server_error", $ex->getMessage());
            }
        }
    }

    /*
     * Get the Gutter Menu for store profile page.
     * 
     * @param $subject object
     * @return array
     */

    private function _getGutterMenu($subject) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $menu = array();

        if ($subject->authorization()->isAllowed($viewer, 'delete')) {
            if ($subject->closed) {
                $menu[] = array(
                    'name' => 'open',
                    'label' => $this->translate('Open Store'),
                    'url' => 'sitestore/close/' . $subject->getIdentity(),
                );
            } else {
                $menu[] = array(
                    'name' => 'close',
                    'label' => $this->translate('Close Store'),
                    'url' => 'sitestore/close/' . $subject->getIdentity(),
                );
            }
        }

        // Reviews Work Start
        if ($viewer_id) {
            $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitestorereview');
            $hasPosted = $reviewTable->canPostReview($subject->getIdentity(), $viewer_id);
            if ($hasPosted) {
                $menu[] = array(
                    'name' => 'update_review',
                    'label' => $this->translate("Update Review"),
                    'url' => 'sitestore/review/edit/' . $subject->getIdentity() . '/' . $hasPosted,
                );
            } else if ($viewer_id != $subject->owner_id) {
                $menu[] = array(
                    'name' => 'create_review',
                    'label' => $this->translate("Create Review"),
                    'url' => 'sitestore/reviews/create/' . $subject->getIdentity(),
                );
            }
        }
        // Reviews Work Ends

        if ($subject->authorization()->isAllowed($viewer, 'delete')) {
            $menu[] = array(
                'name' => 'delete',
                'label' => $this->translate('Delete Store'),
                'url' => 'sitestore/delete/' . $subject->getIdentity(),
            );
        }

        $menu[] = array(
            'name' => 'tellafriend',
            'label' => $this->translate('Tell a friend'),
            'url' => 'sitestore/tellafriend/' . $subject->getIdentity()
        );

        if ($viewer->getIdentity()) {
            $menu[] = array(
                'name' => 'share',
                'label' => $this->translate('Share This Store'),
                'url' => 'activity/share',
                'urlParams' => array(
                    "type" => $subject->getType(),
                    "id" => $subject->getIdentity()
                )
            );

            $menu[] = array(
                'name' => 'report',
                'label' => $this->translate('Report This Store'),
                'url' => 'report/create/subject/' . $subject->getGuid(),
                'urlParams' => array(
                    "type" => $subject->getType(),
                    "id" => $subject->getIdentity()
                )
            );
        }

        // For Cart
        if (isset($_REQUEST['cart']) && !empty($_REQUEST['cart'])) {
            $menu[] = array(
                'label' => $this->translate('Cart'),
                'name' => 'cart'
            );
        }

        return $menu;
    }

    /**
     *  Message friends about this page
     */
    public function tellafriendAction() {

        // Get viewer
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // Get form
        if ($this->getRequest()->isGet()) {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'Sitestore')->getTellAFriendForm();
            if (isset($viewer_id) && !empty($viewer_id)) {
                $response['formValues']['sender_name'] = $viewer->displayname;
                $response['formValues']['sender_email'] = $viewer->email;
            }
            $this->respondWithSuccess($response, true);
        } else if ($this->getRequest()->isPost()) {

            // Form validation
            // Get page id and object
            $store_id = $this->_getParam('store_id', $this->_getParam('store_id', null));
            $subject = Engine_Api::_()->getItem('sitestore_store', $store_id);

            if (empty($subject))
                $this->respondWithError('no_record');


            // Get form values
            $values = $this->_getAllParams();

            // Start form validation
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitestore')->tellaFriendFormValidators();
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

            $heading = $subject->title;

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
            try {
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_ids, 'SITESTORE_TELLAFRIEND_EMAIL', array(
                    'host' => $_SERVER['HTTP_HOST'],
                    'sender_name' => $sender,
                    'store_title' => $heading,
                    'message' => '<div>' . $message . '</div>',
                    'object_link' => 'http://' . $_SERVER['HTTP_HOST'] . Engine_Api::_()->sitestore()->getHref($subject->store_id, $subject->owner_id, $subject->getSlug()),
                    'sender_email' => $sender_email,
                    'queue' => true
                ));
            } catch (Exception $ex) {
                $this->respondWithError('internal_server_error', $ex->getMessage());
            }
            $this->successResponseNoContent('no_content', true);
        }
    }

    /*
     * Get the Profile Tabs for store profile page.
     * 
     * @param $subject object
     * @return array
     */

    private function _getProfileTabs($subject) {
        $tabsMenu = array();

        $productmoduleenabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreproduct');
        if ($productmoduleenabled) {
            $storeProductsCount = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getProductsCount($subject->getIdentity(), 'store_id');
            if ($storeProductsCount && !$subject->closed) {
                $tabsMenu[] = array(
                    'label' => $this->translate('Products'),
                    'name' => 'products',
                    'count' => $storeProductsCount,
                    'url' => 'sitestore/product/browse/',
                    'urlParams' => array(
                        'store_id' => $subject->getIdentity()
                    )
                );
            }
        }

        // Prepare updated count
        $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
        $updates_count = $streamTable->select()
                        ->from($streamTable->info('name'), 'count(*) as count')
                        ->where('object_id = ?', $subject->getIdentity())
                        ->where('object_type = ?', $subject->getType())
                        ->where('target_type = ?', $subject->gettype())
                        ->where('type like ?', "%post%")
                        ->query()->fetchColumn();
        if ($updates_count) {
            $tabsMenu[] = array(
                'label' => $this->translate('Updates'),
                'name' => 'update',
                'count' => $updates_count,
                'url' => 'sitestore/updates/' . $subject->getIdentity(),
            );
        }

        $tabsMenu[] = array(
            'label' => $this->translate('Info'),
            'name' => 'information',
            'url' => 'sitestore/information/' . $subject->getIdentity(),
        );


        if (strlen($subject->overview) > 0) {
            $tabsMenu[] = array(
                'label' => $this->translate('Overview'),
                'name' => 'overview',
                'url' => 'sitestore/overview/' . $subject->getIdentity(),
            );
        }

        $storePhotosCount = Engine_Api::_()->getdbtable('photos', 'sitestore')->countTotalPhotos(array('store_id' => $subject->getIdentity()));
        if ($storePhotosCount) {
            $tabsMenu[] = array(
                'label' => $this->translate("Photos"),
                'name' => 'photos',
                'count' => $storePhotosCount,
                'url' => 'sitestore/photos/browse-album/' . $subject->getIdentity(),
            );
        }

        // Get reviews count
        $reviewCount = Engine_Api::_()->getDbtable('reviews', 'sitestorereview')->totalReviews($subject->getIdentity());
        if ($reviewCount) {
            $tabsMenu[] = array(
                'label' => $this->translate("Reviews"),
                'name' => 'reviews',
                'count' => $reviewCount,
                'url' => 'sitestore/reviews/browse/' . $subject->getIdentity(),
            );
        }

        // @Todo: Bhawin: "form" should be "forum". Please discuss it with me once.
        // $tabsMenu[] = array(
        //     'label' => $this->translate('Forum'),
        //     'name' => 'form',
        //     'url' => 'sitestore/form/' . $subject->getIdentity(),
        // );

        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity() && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer')) {
            $offerCount = Engine_Api::_()->getDbtable('offers', 'sitestoreoffer')->getStoreOfferCount($subject->getIdentity());
            if ($offerCount) {
                $tabsMenu[] = array(
                    'label' => $this->translate("Coupons"),
                    'name' => 'coupons',
                    'count' => $offerCount,
                    'url' => 'sitestore/offers/browse/' . $subject->getIdentity(),
                );
            }
        }

        // Video Tab
        $videoTable = Engine_Api::_()->getDbtable("videos", "sitestorevideo");
        $videoCount = $videoTable->getStoreVideoCount($subject->getIdentity());
        if ($videoCount) {
            $tabsMenu[] = array(
                'label' => $this->translate("Videos"),
                'name' => 'videos',
                'count' => $videoCount,
                'url' => 'videosgeneral/',
                'urlParams' => array(
                    'subject_type' => $subject->getType(),
                    'subject_id' => $subject->getIdentity(),
                ),
            );
        }
        // Video Tab

        return $tabsMenu;
    }

}
