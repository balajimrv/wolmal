<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestorereview_ReviewController extends Siteapi_Controller_Action_Standard {

    /*
    *   Checks auth and gets subject
    *
    *
    */
    public function init() {
        
        $viewer = Engine_Api::_()->user()->getViewer();

        $store_id = $this->_getParam('store_id');
        if (!empty($store_id)) {
            $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
            if (!empty($sitestore))
                Engine_Api::_()->core()->setSubject($sitestore);
        }
        
    }

    /**
     * Create a review
     * 
     */
    public function createAction() {
        if (Engine_Api::_()->core()->hasSubject('sitestore_store'))
            $sitestore = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
        else
            $this->respondWithError('no_record');

        // Get viewer info
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!empty($viewer_id)) {
          $level_id = Engine_Api::_()->user()->getViewer()->level_id;
         } else {
          $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
         }

        $create_level_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestorereview_review', "create");

        if (!$create_level_allow)
            $this->respondWithError('unauthorized');


        // Check if this user has already reviewed on this store
        $hasPostedReview = Engine_Api::_()->getDbtable('reviews', 'sitestorereview')->canPostReview($sitestore->store_id, $viewer_id);
        
        if ($hasPostedReview) {
            $this->respondWithError('review_already_present');
        }
        
        // Core settings
        $coreApi = Engine_Api::_()->getApi('settings', 'core');
        $sitestorereview_proscons = $coreApi->getSetting('sitestorereview.proscons', 1);
        $sitestorereview_limit_proscons = $coreApi->getSetting('sitestorereview.limit.proscons', 500);
        $sitestorereview_recommend = $coreApi->getSetting('sitestorereview.recommend', 1);

        // Fetch review categories
        $categoryIdsArray = array();
        $categoryIdsArray[] = $sitestore->category_id;
        $categoryIdsArray[] = $sitestore->subcategory_id;
        $categoryIdsArray[] = $sitestore->subsubcategory_id;

        $ratingParams = Engine_Api::_()->getDbtable('reviewcats', 'sitestorereview')->reviewParams($sitestore->category_id);
        $ratingParamsArray = $ratingParams->toArray();

        if ($this->getRequest()->isGet()) {

            $ratingParam = array();
            $ratingParam[] = array(
                'type' => 'Rating',
                'name' => 'review_rate_0',
                'label' => $this->translate('Overall Rating')
            );

            $profileTypeReview = Engine_Api::_()->getDbtable('Profilemaps', 'sitestore')->getProfileType($sitestore->category_id);

            foreach ($ratingParams as $ratingparam_id) {
                $ratingParam[] = array(
                    'type' => 'Rating',
                    'name' => 'review_rate_' . $ratingparam_id->reviewcat_id,
                    'label' => $ratingparam_id->reviewcat_name
                );
            }
            $response['form'] = Engine_Api::_()->getApi('Siteapi_Core', 'Sitestorereview')->getReviewCreateForm(array("settingsReview" => array('sitestorereview_proscons' => $sitestorereview_proscons, 'sitestorereview_limit_proscons' => $sitestorereview_limit_proscons, 'sitestorereview_recommend' => $sitestorereview_recommend), 'item' => $sitestore, 'profileTypeReview' => $profileTypeReview));
            $response['ratingParams'] = $ratingParam;
            $this->respondWithSuccess($response, true);
        }

        if ($this->getRequest()->isPost()) {
            // Convert post data into an array
            $values = $postData = $this->_getAllParams();
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'Sitestorereview')->getReviewCreateForm(array("settingsReview" => array('sitestorereview_proscons' => $sitestorereview_proscons, 'sitestorereview_limit_proscons' => $sitestorereview_limit_proscons, 'sitestorereview_recommend' => $sitestorereview_recommend), 'item' => $sitestore, 'profileTypeReview' => $profileTypeReview));
            foreach ($getForm as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            // Start form validation
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitestorereview')->getReviewCreateFormValidators(array("settingsReview" => array('sitestorereview_proscons' => $sitestorereview_proscons, 'sitestorereview_limit_proscons' => $sitestorereview_limit_proscons, 'sitestorereview_recommend' => $sitestorereview_recommend), 'item' => $sitestore, 'profileTypeReview' => $profileTypeReview));
            $values['validators'] = $validators;
            $validationMessage = $this->isValid($values);

            if(!is_array($validationMessage))
                $validationMessage = array();

            // Minlength params
            if(isset($postData['pros']) && !isset($validationMessage['pros']) && strlen($postData['pros'])<10)
                $validationMessage['pros'] = $this->translate("Please enter at least 10 characters , You entered ".strlen($postData['pros']));

            if(isset($postData['cons']) && !isset($validationMessage['cons']) && strlen($postData['cons'])<10)
                $validationMessage['cons'] = $this->translate("Please enter at least 10 characters , You entered ".strlen($postData['cons']));

            if(isset($postData['title']) && !isset($validationMessage['title']) && strlen($postData['title'])<10)
                $validationMessage['title'] = $this->translate("Please enter at least 10 characters , You entered ".strlen($postData['title']));

            if(isset($postData['body']) && !isset($validationMessage['body']) && strlen($postData['body'])<10)
                $validationMessage['body'] = $this->translate("Please enter at least 10 characters , You entered ".strlen($postData['body']));
            //MinLength Params Ends

            // Response validation error
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                $values['owner_id'] = $viewer_id;
                $values['resource_id'] = $sitestore->store_id;
                $values['resource_type'] = $sitestore->getType();
                $values['profile_type_review'] = $profileTypeReview;
                $values['type'] = $viewer_id ? 'user' : 'visitor';

                if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.recommend', 1)) {
                    $values['recommend'] = 0;
                }
                // Add review
                $reviewTable = Engine_Api::_()->getDbtable('reviews', 'sitestorereview');
                $review = $reviewTable->createRow();
                $review->setFromArray($values);
                $review->view_count = 1;
                $review->save();

                $review_id = $review->review_id;
                
                // increment review count
                if (!empty($viewer_id))
                    $sitestore->review_count++;
                $sitestore->save();


                $reviewRatingTable = Engine_Api::_()->getDbtable('ratings', 'sitestorereview');
                if (!empty($review_id)) {
                    $reviewRatingTable->delete(array('review_id = ?' => $review->review_id));
                }
                
                
                //Insert rating params
                if(isset($postData['review_rate_0']) && !empty($postData['review_rate_0']))
                {
                    $newRating = $reviewRatingTable->createRow();
                    $newRating->review_id = $review->getIdentity();
                    $newRating->store_id = $subject->getIdentity();
                    $newRating->reviewcat_id = 0;
                    $newRating->category_id = $subject->category_id;
                    $newRating->rating = $postData['review_rate_0'];
                    $newRating->save();
                }
                foreach($ratingParamsarray as $row => $value)
                {
                    if(isset($postData['review_rate_'.$value['reviewcat_id']]) && !empty($postData['review_rate_'.$value['reviewcat_id']]))
                    {
                        $newRating = $reviewRatingTable->createRow();
                        $newRating->review_id = $review->getIdentity();
                        $newRating->store_id = $subject->getIdentity();
                        $newRating->reviewcat_id = $value['reviewcat_id'];
                        $newRating->category_id = $subject->category_id;
                        $newRating->rating = $postData['review_rate_'.$value['reviewcat_id']];
                        $newRating->save();
                    }
                }

                // UPDATE REVIEW 
                $reviewRatingTable->storeRatingUpdate($sitestore->getIdentity());
                // UPDATE REVIEW POSITION ENDS
                
                if (empty($review_id) && !empty($viewer_id)) {
                    $activityApi = Engine_Api::_()->getDbtable('actions', 'seaocore');

                    // Activity feed
                    $action = $activityApi->addActivity($viewer, $sitestore, 'sitestorereview_new');

                    if ($action != null) {
                        $activityApi->attachActivity($action, $review);

                        //START NOTIFICATION AND EMAIL WORK
                        //Engine_Api::_()->sitestore()->sendNotificationEmail($sitestore, $action, 'sitestore_write_review', 'SITEPAGE_REVIEW_WRITENOTIFICATION_EMAIL', null, null, 'created', $review);
                        // $isChildIdLeader = Engine_Api::_()->getDbtable('listItems', 'sitestore')->checkLeader($sitestore);
                        // if (!empty($isChildIdLeader)) {
                        //     Engine_Api::_()->sitestore()->sendNotificationToFollowers($sitestore, 'sitestore_write_review');
                        // }
                        //END NOTIFICATION AND EMAIL WORK
                    }
                }

                $db->commit();
                $this->successResponseNoContent('no_content', true);

            } catch (Exception $e) {
                $db->rollback();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /*
    * Returns review detail
    *
    */
    public function viewAction() {

        $this->validateRequestMethod();

        // Gets logged in user info
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $review_id = $this->_getParam('review_id', $this->_getParam('review_id', null));
        if ($review_id) {
            $review = Engine_Api::_()->getItem('sitestorereview_review', $review_id);
        }

        if (!$review) {
            $this->respondWithError('no_record');
        }

        if (!Engine_Api::_()->core()->hasSubject('sitestore_store')) {
            $this->respondWithError('no_record');
        }
        // Get the store 
        $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');

        // Get user level id
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        // Get level id
        $can_view = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestore_store', "view");


         // if ($can_view != 2 && $viewer_id != $sitestore->owner_id && ($sitestore->draft == 1 || $sitestore->search == 0 || $sitestore->approved != 1)) {
         //     $this->respondWithError('unauthorized');
         // }
         // if ($can_view != 2 && ($review->status != 1 && empty($review->owner_id))) {
         //     $this->respondWithError('unauthorized');
         // }

        $params = array();
        $params = $review->toArray();
        $params['owner_title'] = $review->getOwner()->getTitle();
        // Get location
        if (!empty($sitestore->location) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.location', 1)) {
            $params['location'] = $sitestore->location;
        }

        $params['tag'] = $sitestore->getKeywords(', ');

        $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitestore');

        $category_id = $sitestore->category_id;

        if (!empty($category_id)) {

            $params['categoryname'] = Engine_Api::_()->getItem('sitestore_category', $category_id)->category_name;

            $subcategory_id = $sitestore->subcategory_id;

            if (!empty($subcategory_id)) {

                $params['subcategoryname'] = Engine_Api::_()->getItem('sitestore_category', $subcategory_id)->category_name;

                $subsubcategory_id = $sitestore->subsubcategory_id;

                if (!empty($subsubcategory_id)) {

                    $params['subsubcategoryname'] = Engine_Api::_()->getItem('sitestore_category', $subsubcategory_id)->category_name;
                }
            }
        }

        // Get the rating if present
        $ratingParams = Engine_Api::_()->getDbtable('ratings', 'sitestorereview')->profileRatingbyCategory($review->review_id);

        $params['ratingParams'] = $ratingParams;
        $guttermenu = $this->guttermenu($sitestore, $review, 'view');
        $params['gutterMenu'] = $guttermenu;

        $response['response'] = $params;
        $this->respondWithSuccess($response, true);
    }

    /*
    * Returns review search form
    *
    *
    */
    public function searchAction() {
        $this->validateRequestMethod();
        $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'Sitestorereview')->getReviewSearchForm(), true);
    }


    /*
    *  Returns review listing with pagination filtering the form fields
    *
    *
    */
    public function browseAction() {

        $this->validateRequestMethod();

        // Get viewer info
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // Page subject should be set
        if (!Engine_Api::_()->core()->hasSubject('sitestore_store'))
            $this->respondWithError('no_record');

        $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');

        $store_id = $sitestore->store_id;

        // Get params
        $params['type'] = '';

        $params = $this->_getAllParams();
        
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        
        if (isset($params['user_id']) && !empty($params['user_id']))
            $user_id = $params['user_id'];
        else
            $user_id = $viewer_id;

        // Get review table
        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitestorereview');
        // Get rating table
        $ratingTable = Engine_Api::_()->getDbTable('ratings', 'sitestorereview');

        $type = 'user';
        if (!empty($viewer_id)) {
          $level_id = Engine_Api::_()->user()->getViewer()->level_id;
         } else {
          $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
         }

        try {
            // Custom field work
            //$customFieldValues = array_intersect_key($searchParams, $searchForm->getFieldElements());
            // Get paginator
            // Get review table
            $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitestorereview');
            $paginator = $reviewTable->storeReviews($store_id);
            $paginator->setItemCountPerPage(10);
            $paginator->setCurrentPageNumber($this->_getParam('store', 1));

            if (isset($params['subcategory_id']) && $params['subcategory_id'])
                $searchParams['subcategory_id'] = $params['subcategory_id'];
            if (isset($params['subsubcategory_id']) && $params['subsubcategory_id'])
                $searchParams['subsubcategory_id'] = $params['subsubcategory_id'];

            // Get total reviews
            $totalReviews = $paginator->getTotalItemCount();
            
            // Start top section for overall rating and it's parameter
            $params['resource_id'] = $store_id;
            $params['resource_type'] = $sitestore->getType();
            $params['viewer_id'] = $viewer_id;
            $params['type'] = 'user';
            $noReviewCheck = $reviewTable->getAvgRecommendation($store_id);
            if (!empty($noReviewCheck)) {
                $noReviewCheck = $noReviewCheck->toArray();
                if ($noReviewCheck)
                    $recommend_percentage = round($noReviewCheck[0]['avg_recommend'] * 100, 3);
            }

            // for ($i = 5; $i > 0; $i--) {
            //     $ratingCount[$i] = $ratingTable->getNumbersOfUserRating($store_id, 'user', 0, $i, 0, 'sitestore_store', array());
            // }

            $ratingData = $ratingTable->ratingbyCategory($store_id);
            $hasPosted = $reviewTable->canPostReview($store_id, $viewer_id);
            $reviewRateMyData = $ratingTable->ratingsData($hasPosted);
            $coreApi = Engine_Api::_()->getApi('settings', 'core');

            $sitestorereview_proscons = $coreApi->getSetting('sitestorereview.proscons', 1);
            $sitestorereview_limit_proscons = $coreApi->getSetting('sitestorereview.limit.proscons', 500);
            $sitestorereview_recommend = $coreApi->getSetting('sitestorereview.recommend', 1);
            $sitestorereview_report = $coreApi->getSetting('sitestorereview.report', 1);
            $sitestorereview_email = $coreApi->getSetting('sitestorereview.email', 1);
            $sitestorereview_share = $coreApi->getSetting('sitestorereview.share', 1);

            $create_level_allow = $create_level_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestorereview_review', "review_create");

            $create_review = ($sitestore->owner_id == $viewer_id) ? Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.allowownerreview', 1) : 1;

            if (!$create_review || empty($create_level_allow)) {
                $can_create = 0;
            } else {
                $can_create = 1;
            }

            $can_delete = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestorereview_review', "review_delete");

            $can_reply = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestorereview_review', "review_reply");

            $can_update = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestorereview_review', "review_update");
            
            // review breackdown rating_params
            $ratings_params = $ratingTable->ratingbyCategory($store_id);
            $rating_params_data = array();
            if(!empty($ratings_params))
            {
                foreach($ratings_params as $value)
                {
                    if($value['reviewcat_name'])
                        $rating_params_data[] = $value;
                }
            }
            
            if (isset($params['getRating']) && !empty($params['getRating'])) {
                $ratings['rating_avg'] = $sitestore->rating;
                $ratings['rating_users'] = $sitestore->review_count;
                $ratings['breakdown_ratings_params'] = $rating_params_data;
                $ratings['myRatings'] = $reviewRateMyData;
                $ratings['hasPosted'] = $hasPosted;
                $ratings['recomended'] = $recommend_percentage . " %";
                $response['ratings'] = $ratings;
            }

            $metaParams = array();
            $response['total_reviews'] = $totalReviews;
            $response['content_title'] = $sitestore->getTitle();            


            $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitestore');

            $request = Zend_Controller_Front::getInstance()->getRequest();

            $category_id = $request->getParam('category_id', null);



            if (!empty($category_id)) {

                $metaParams['categoryname'] = Engine_Api::_()->getItem('sitestore_category', $category_id)->getCategorySlug();

                $subcategory_id = $request->getParam('subcategory_id', null);

                if (!empty($subcategory_id)) {

                    $metaParams['subcategoryname'] = Engine_Api::_()->getItem('sitestore_category', $subcategory_id)->getCategorySlug();

                    $subsubcategory_id = $request->getParam('subsubcategory_id', null);

                    if (!empty($subsubcategory_id)) {

                        $metaParams['subsubcategoryname'] = Engine_Api::_()->getItem('sitestore_category', $subsubcategory_id)->getCategorySlug();
                    }
                }
            }

            // Set meta titles
            // Todo error in set meta titles
            // Engine_Api::_()->sitestore()->setMetaTitles($metaParams);

            $allow_review = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.allowreview', 1);


            if (empty($allow_review)) {
                $this->respondWithError('unauthorized');
            }

            $metaParams['store_type_title'] = $this->translate('sitestore');

            // Get tag
            if ($this->_getParam('search', null)) {
                $metaParams['search'] = $this->_getParam('search', null);
            }

            foreach ($paginator as $review) {

                $params = $review->toArray();

                if($params['recommend'] == 1)
                    $params['recommend'] = "Yes";
                else
                    $params['recommend'] = "No";

                // isliked
                $corelikesTable = Engine_Api::_()->getDbtable('likes', 'core');
                $isliked = $corelikesTable->getLike($review , $viewer);
                $params['is_liked'] = false;
                if($isliked)
                    $params['is_liked'] = true;

                if (isset($params['body']) && !empty($params['body']))
                    $params['body'] = strip_tags($params['body']);

                $params["owner_title"] = $review->getOwner()->getTitle();

                $params['like_count'] = Engine_Api::_()->getDbtable("likes","core")->getLikeCount($review);
                $params['comment_count'] = Engine_Api::_()->getDbtable("comments","core")->getCommentCount($review);

                // Owner image Add images 
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($review, true);

                $params = array_merge($params, $getContentImages);
                $store_id = $review->store_id;
                $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

                $params['store_title'] = $sitestore->title;

                $user_ratings = Engine_Api::_()->getDbtable('ratings', 'sitestorereview')->ratingsData($review->review_id, $review->getOwner()->getIdentity(), $review->store_id, 0);
                $params['overall_rating'] = $user_ratings[0]['rating'];
                $params['category_name'] = Engine_Api::_()->getItem('sitestore_category', $sitestore->category_id)->category_name;
                // $helpfulTable = Engine_Api::_()->getDbtable('helpful', 'sitestore');
                // $helpful_entry = $helpfulTable->getHelpful($review->review_id, $viewer_id, 1);
                // $nothelpful_entry = $helpfulTable->getHelpful($review->review_id, $viewer_id, 2);
                // $params['is_helful'] = ($helpful_entry) ? true : false;
                // $params['is_not_helful'] = ($nothelpful_entry) ? true : false;
                // $params['helpful_count'] = $review->getCountHelpful(1);
                // $params['nothelpful_count'] = $review->getCountHelpful(2);
                // Add owner images
                $guttermenu = $this->guttermenu($sitestore, $review, 'browse');
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($sitestore);
                $params = array_merge($params, $getContentImages);
                $params['guttermenu'] = $guttermenu;
                
                // get rating params
                $profileRating = $ratingTable->profileRatingbyCategory($review->review_id);
                $breakdown_ratings_params = array();
                
                if(!empty($profileRating))
                {
                    foreach($profileRating as $value)
                    {
                        if($value['reviewcat_name'])
                            $breakdown_ratings_params[] = $value;
                    }
                }
                
                $params['breakdown_ratings_params'] = $breakdown_ratings_params;
                
                $tempResponse[] = $params;
            }
            if (isset($tempResponse) && !empty($tempResponse))
                $response['reviews'] = $tempResponse;
            $this->respondWithSuccess($response, true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /*
    * Action for deleting review
    *
    *
    */
    public function deleteAction() {

        // Validate request method
        $this->validateRequestMethod("DELETE");

        // Get logged in user info
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!empty($viewer_id)) {
          $level_id = Engine_Api::_()->user()->getViewer()->level_id;
         } else {
          $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
         }

        $review_id = $this->_getParam('review_id', $this->_getParam('review_id', null));
        if ($review_id) {
            $review = Engine_Api::_()->getItem('sitestorereview_review', $review_id);
        }

        if ($review->owner_id != $viewer_id && $level_id != 1) {
            $this->respondWithError('unauthorized');
        }

        if (!$review) {
            $this->respondWithError('no_record');
        }

        if (!Engine_Api::_()->core()->hasSubject('sitestore_store')) {
            $this->respondWithError('no_record');
        }
        // Get the store 
        $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            Engine_Api::_()->sitestorereview()->deleteContent($review_id);
            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /*
    * Returns comments list of the review
    *
    *
    */
    public function listCommentsAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$this->_helper->requireUser()->isValid()) {
            $this->respondWithError('unauthorized');
        }
        $review_id = $this->_getParam('review_id', $this->_getParam('review_id', null));
        if ($review_id) {
            $review = $subject = Engine_Api::_()->getItem('sitestorereview_review', $review_id);
        }

        if (!$review && empty($review))
            $this->respondWithError('no_record');

        $subjectParent = $subject->getParent();

        $canComment = $subject->authorization()->isAllowed($viewer, 'comment');

        $storeSubject = $subject->getParent();
        $storeApi = Engine_Api::_()->sitestore();
        $canComment = $storeApi->isManageAdmin($storeSubject, 'comment');
        $storeApi->isManageAdmin($storeSubject, 'edit');
        $viewAllLikes = $this->_getParam('viewAllLikes', false);
        $likes = $subject->likes()->getLikePaginator();
        $likesData = array();
        if (!empty($likes)) {
            foreach ($likes as $like) {
                $likesData[$like->like_id] = $like->toArray();
                $poster = $like->getPoster();
                $posterImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($poster, true);
                $likesData[$like->like_id]['owner_images'] = $posterImages;
                $likesData[$like->like_id]['owner_title'] = $poster->getTitle();
            }
        }
        // Comments
        // If has a store, display oldest to newest
        if (null !== ( $store = $this->_getParam('store'))) {
            $commentSelect = $subject->comments()->getCommentSelect('ASC');
            $commentSelect->order('comment_id ASC');
            $comments = Zend_Paginator::factory($commentSelect);
            $comments->setCurrentPageNumber($store);
            $comments->setItemCountPerPage(10);
        }
        // If not has a store, show the
        else {
            $commentSelect = $subject->comments()->getCommentSelect('DESC');
            $commentSelect->order('comment_id DESC');

            $comments = Zend_Paginator::factory($commentSelect);
            $comments->setCurrentPageNumber(1);
            $comments->setItemCountPerPage(4);
        }
        $commentsData = array();
        if (!empty($comments)) {
            foreach ($comments as $comment) {
                $poster = $comment->getPoster();
                $posterImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($poster, true);
                $likes = $comment->likes();
                $commentsData[$comment->comment_id] = $comment->toArray();
                $commentsData[$comment->comment_id]['owner_images'] = $posterImages;
                $commentsData[$comment->comment_id]['owner_title'] = $poster->getTitle();
            }
        }

        $response['likes'] = $likesData;
        $response['comments'] = $commentsData;

        $this->respondWithSuccess($response, true);
    }

    /*
    * Returns comment form and posts comment on a review
    *
    *
    */
    public function commentAction() {
        if (!$this->_helper->requireUser()->isValid()) {
            $this->respondWithError('unauthorized');
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $review_id = $this->_getParam('review_id', $this->_getParam('review_id', null));
        if ($review_id) {
            $review = $subject = Engine_Api::_()->getItem('sitestorereview_review', $review_id);
        }

        if (!$review && empty($review))
            $this->respondWithError('no_record');

        $subjectParent = $subject->getParent();


        if ($this->getRequest()->isGet()) {
            $commentform = Engine_Api::_()->getApi('Siteapi_Core', 'Sitestorereview')->getcommentForm($review->getType(), $review->review_id);
            $this->respondWithSuccess($commentform, true);
        }

        if ($this->getRequest()->isPost()) {
            $values = array();
            $values = $this->_getAllParams();
            
            // Start form validation
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitestorereview')->getcommentValidation();
            $values['validators'] = $validators;
            $validationMessage = $this->isValid($values);

            // Response validation error
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }
            
            $body = $values['body'];
            $values['type'] = $subject->getType();
            $values['id'] = $subject->review_id;
            $values['identity'] = $subject->review_id;
            $db = $subject->comments()->getCommentTable()->getAdapter();
            $db->beginTransaction();
            try {
                $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                $subjectOwner = $subject->getOwner('user');
                $subject->comments()->addComment($viewer, $body);

                // Activity
                $action = $activityApi->addActivity($viewer, $subject, 'comment_' . $subject->getType(), '', array(
                    'owner' => $subjectOwner->getGuid(),
                    'body' => $body
                ));

                if (!empty($action)) {
                    $activityApi->attachActivity($action, $subject);
                }


                // add notification
//                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
//                $notifyApi->addNotification($notifyUser, $viewer, $subject, 'commented_commented', array(
//                    'label' => $subject->getShortType()
//                ));
                

                // Add a notification for all users that commented or like except the viewer and poster
                // @todo we should probably limit this
//                $commentedUserNotifications = array();
//                foreach ($subject->comments()->getAllCommentsUsers() as $notifyUser) {
//                    if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity())
//                        continue;
//
//                    // Don't send a notification if the user both commented and liked this
//                    $commentedUserNotifications[] = $notifyUser->getIdentity();
//                    $notifyApi->addNotification($notifyUser, $viewer, $subject, 'commented_commented', array(
//                        'label' => $subject->getShortType()
//                    ));
//                }


                // Add a notification for all users that liked
                // @todo we should probably limit this
//                foreach ($subject->likes()->getAllLikesUsers() as $notifyUser) {
//                    // Skip viewer and owner
//                    if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity())
//                        continue;
//
//                    // Don't send a notification if the user both commented and liked this
//                    if (in_array($notifyUser->getIdentity(), $commentedUserNotifications))
//                        continue;
//
//                    $notifyApi->addNotification($notifyUser, $viewer, $subject, 'liked_commented', array(
//                        'label' => $subject->getShortType()
//                    ));
//
//
//                    //end check for store admin and store owner
//                }

                // Send notification to Page admins
                $sitestoreVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitestore')->version;
                if ($sitestoreVersion >= '4.2.9p3') {
                    Engine_Api::_()->sitestore()->itemCommentLike($subject, 'sitestore_contentcomment', $baseOnContentOwner);
                }

                // Increment comment count
                Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');

                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /*
    *   Updates a review form and posting
    *
    *
    */
    public function editAction() {

        if (Engine_Api::_()->core()->hasSubject('sitestore_store'))
            $sitestore = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
        else
            $this->respondWithError('no_record');

        $review_id = $this->_getParam('review_id', $this->_getParam('review_id', null));
        if ($review_id) {
            $review = $subject = Engine_Api::_()->getItem('sitestorereview_review', $review_id);
        }

        if (!$review && empty($review))
            $this->respondWithError('no_record');

        //GET VIEWER INFO
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!empty($viewer_id)) {
          $level_id = Engine_Api::_()->user()->getViewer()->level_id;
         } else {
          $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
         }

        
        $can_update = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestoreproduct_product', "review_update");

        if(empty($can_update))
            $this->respondWithError('unauthorized');
        
        // core settings
        $coreApi = Engine_Api::_()->getApi('settings', 'core');
        $sitestorereview_proscons = $coreApi->getSetting('sitestorereview.proscons', 1);
        $sitestorereview_limit_proscons = $coreApi->getSetting('sitestorereview.limit.proscons', 500);
        $sitestorereview_recommend = $coreApi->getSetting('sitestorereview.recommend', 1);


        //FETCH REVIEW CATEGORIES
        $categoryIdsArray = array();
        $categoryIdsArray[] = $sitestore->category_id;
        $categoryIdsArray[] = $sitestore->subcategory_id;
        $categoryIdsArray[] = $sitestore->subsubcategory_id;

        $ratingParams = Engine_Api::_()->getDbtable('reviewcats', 'sitestorereview')->reviewParams($sitestore->category_id);

        $ratingParam = array();
        $ratingParam[] = array(            
            'type' => 'Rating',
            'name' => 'review_rate_0',
            'label' => $this->translate('Overall Rating')
        );

        //$profileTypeReview = Engine_Api::_()->getDbtable('categories', 'sitestore')->getProfileType(array(), $sitestore->category_id);

        foreach ($ratingParams as $ratingparam_id) {
            $ratingParam[] = array(
                'type' => 'Rating',
                'name' => 'review_rate_' . $ratingparam_id->reviewcat_id,
                'label' => $ratingparam_id->reviewcat_name
            );
        }

        //GET LEVEL SETTING
        $can_view = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestore_store', "view");


        // if ($can_view != 2 && $viewer_id != $sitestore->owner_id && ($sitestore->draft == 1 || $sitestore->search == 0 || $sitestore->approved != 1)) {
        //     echo "database";die;
        //     $this->respondWithError('unauthorized');
        // }
        // if ($can_view != 2 && ($review->status != 1 && empty($review->owner_id))) {
        //     $this->respondWithError('unauthorized');
        // }

        $params = array();
        $params['pros'] = $review->pros;
        $params['cons'] = $review->cons;
        $params['title'] = $review->title;
        $params['body'] = $review->body;
        $params['owner_title'] = $review->getOwner()->getTitle();
        $params['recommend'] = $review->recommend;
        
        // GET LOCATION
        if (!empty($sitestore->location) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.location', 1)) {
            $params['location'] = $sitestore->location;
        }

        $params['tag'] = $sitestore->getKeywords(', ');

        //GET EVENT CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitestore');

        $category_id = $sitestore->category_id;

        if (!empty($category_id)) {

            $params['categoryname'] = Engine_Api::_()->getItem('sitestore_category', $category_id)->category_name;

            $subcategory_id = $sitestore->subcategory_id;

            if (!empty($subcategory_id)) {

                $params['subcategoryname'] = Engine_Api::_()->getItem('sitestore_category', $subcategory_id)->category_name;

                $subsubcategory_id = $sitestore->subsubcategory_id;

                if (!empty($subsubcategory_id)) {

                    $params['subsubcategoryname'] = Engine_Api::_()->getItem('sitestore_category', $subsubcategory_id)->category_name;
                }
            }
        }



        // Get the rating if present
        $ratingselect = Engine_Api::_()->getDbtable('ratings', 'sitestorereview')->select()
                        ->where("review_id = ?",$review->getIdentity());
        $ratingParams = $ratingselect->query()->fetchALL();
        $ratingParamsarray = array();
        
        foreach($ratingParams as $value)
        {
            if($value['reviewcat_id'])
                $ratingParamsarray['review_rate_'.$value['reviewcat_id']] = $value['rating'] ;
            else
                $ratingParamsarray['review_rate_0'] = $value['rating'];
        }

        $params = array_merge($params,$ratingParamsarray);
        if ($this->getRequest()->isGet()) {
            $response['form'] = Engine_Api::_()->getApi('Siteapi_Core', 'Sitestorereview')->getReviewCreateForm(array("settingsReview" => array('sitestorereview_proscons' => $sitestorereview_proscons, 'sitestorereview_limit_proscons' => $sitestorereview_limit_proscons, 'sitestorereview_recommend' => $sitestorereview_recommend), 'item' => $sitestore, 'profileTypeReview' => $profileTypeReview));
            $response['ratingParams'] = $ratingParam;
            $response['formValues'] = $params;
            $this->respondWithSuccess($response, true);
        }

        if ($this->getRequest()->isPost() || $this->getRequest()->isPut()) {
            // Convert post data into the array.
            $values = array();
            $values = $postData = $this->getAllParams();
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'Sitestorereview')->getReviewCreateForm(array("settingsReview" => array('sitestorereview_proscons' => $sitestorereview_proscons, 'sitestorereview_limit_proscons' => $sitestorereview_limit_proscons, 'sitestorereview_recommend' => $sitestorereview_recommend), 'item' => $sitestore, 'profileTypeReview' => $profileTypeReview));
            foreach ($getForm as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            // Start form validation
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitestorereview')->getReviewCreateFormValidators(array("settingsReview" => array('sitestorereview_proscons' => $sitestorereview_proscons, 'sitestorereview_limit_proscons' => $sitestorereview_limit_proscons, 'sitestorereview_recommend' => $sitestorereview_recommend), 'item' => $sitestore, 'profileTypeReview' => $profileTypeReview));
            $values['validators'] = $validators;
            $validationMessage = $this->isValid($values);

            if(!is_array($validationMessage))
                $validationMessage = array();

            // Minlength params
            if(isset($postData['pros']) && !isset($validationMessage['pros']) && strlen($postData['pros'])<10)
                $validationMessage['pros'] = $this->translate("Please enter at least 10 characters , You entered ".strlen($postData['pros']));

            if(isset($postData['cons']) && !isset($validationMessage['cons']) && strlen($postData['cons'])<10)
                $validationMessage['cons'] = $this->translate("Please enter at least 10 characters , You entered ".strlen($postData['cons']));

            if(isset($postData['title']) && !isset($validationMessage['title']) && strlen($postData['title'])<10)
                $validationMessage['title'] = $this->translate("Please enter at least 10 characters , You entered ".strlen($postData['title']));

            if(isset($postData['body']) && !isset($validationMessage['body']) && strlen($postData['body'])<10)
                $validationMessage['body'] = $this->translate("Please enter at least 10 characters , You entered ".strlen($postData['body']));
            //MinLength Params Ends

            // Response validation error
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                $values['owner_id'] = $viewer_id;
                $values['resource_id'] = $sitestore->store_id;
                $values['resource_type'] = $sitestore->getType();
                $values['profile_type_review'] = $profileTypeReview;
                $values['type'] = $viewer_id ? 'user' : 'visitor';

                if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.recommend', 1)) {
                    $values['recommend'] = 0;
                }

                $review->setFromArray($values);
                $review->view_count = 1;
                $review->save();

                $review_id = $review->getIdentity();


                // Increment review count
                if (!empty($viewer_id))
                    $sitestore->review_count++;
                $sitestore->save();

                $reviewRatingTable = Engine_Api::_()->getDbtable('ratings', 'sitestorereview');
                if ($review_id)
                    $reviewRatingTable->delete(array('review_id = ?' => $review->getIdentity()));

                
                //Insert rating params
                foreach($ratingParam as $row => $value)
                {
                    if(isset($postData[$value['name']]))
                    {
                        $de = explode('_', $value['name']);
                        $ratingCat_id = $de[2];
                        $newRating = $reviewRatingTable->createRow();
                        $newRating->review_id = $review->getIdentity();
                        $newRating->store_id = $sitestore->getIdentity();
                        $newRating->reviewcat_id = $ratingCat_id;
                        $newRating->category_id = $sitestore->category_id;
                        $newRating->rating = $postData[$value['name']];
                        $newRating->save();
                    }
                }

                // UPDATE REVIEW 
                $reviewRatingTable->storeRatingUpdate($sitestore->getIdentity());                
                // UPDATE REVIEW POSITION ENDS

                if (empty($review_id) && !empty($viewer_id)) {
                    $activityApi = Engine_Api::_()->getDbtable('actions', 'seaocore');

                    // Activity feed
                    $action = $activityApi->addActivity($viewer, $sitestore, 'sitestorereview_new');

                    if ($action != null) {
                        $activityApi->attachActivity($action, $review);

                        //START NOTIFICATION AND EMAIL WORK
                        //Engine_Api::_()->sitestore()->sendNotificationEmail($sitestore, $action, 'sitestore_write_review', 'SITEPAGE_REVIEW_WRITENOTIFICATION_EMAIL', null, null, 'created', $review);
                        // $isChildIdLeader = Engine_Api::_()->getDbtable('listItems', 'sitestore')->checkLeader($sitestore);
                        // if (!empty($isChildIdLeader)) {
                        //     Engine_Api::_()->sitestore()->sendNotificationToFollowers($sitestore, 'sitestore_write_review');
                        // }
                        //END NOTIFICATION AND EMAIL WORK
                    }
                }

                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollback();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /*
    * Allows to like a review
    *
    *
    */
    public function likeAction() {

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        $viewer = Engine_Api::_()->user()->getViewer();
        $review_id = $this->_getParam('review_id', $this->_getParam('review_id', null));
        if ($review_id) {
            $review = $subject = Engine_Api::_()->getItem('sitestorereview_review', $review_id);
        }

        if (!$review && empty($review))
            $this->respondWithError('no_record');
        // Validate request methods
        $this->validateRequestMethod("POST");
        if (!$this->_helper->requireUser()->isValid()) {
            $this->respondWithError('unauthorized');
        }
        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
            $this->respondWithError('unauthorized');
        }

        if ($this->getRequest()->isPost()) {
            $commentedItem = $subject;
            // Process
            $db = $commentedItem->likes()->getAdapter();
            $db->beginTransaction();
            try {

                if($commentedItem->likes()->isLike($viewer))
                {
                    $commentedItem->likes()->removeLike($viewer);
                }
                else
                {
                    $commentedItem->likes()->addLike($viewer);
                    // Add notification
                    $owner = $commentedItem->getOwner();
                    
                    $owner_guid = $owner->getGuid();
                    if ($owner->getType() == 'user' && ($owner->getIdentity() != $viewer->getIdentity())) {
//                        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
//                        $notifyApi->addNotification($owner, $viewer, $commentedItem, 'liked', array(
//                            'label' => $commentedItem->getShortType()
//                        ));
                    }

                    $sitestoreVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitestore')->version;
                    if ($sitestoreVersion >= '4.2.9p3') {
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember'))
                            Engine_Api::_()->sitestoremember()->joinLeave($subject, 'Join');
                        Engine_Api::_()->sitestore()->itemCommentLike($subject, 'sitestore_contentlike', '');
                    }
                }

                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $ex) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
        }
    }

    /*
    * Returns menu for a review
    *
    * @return array
    */
    private function guttermenu($sitestore = array(), $review = array(), $action = NULL) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!empty($viewer_id)) {
          $level_id = Engine_Api::_()->user()->getViewer()->level_id;
         } else {
          $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
         }
        $guttermenu = array();

        // if ($action != 'view') {
        //     $guttermenu[] = array(
        //         'label' => $this->translate("View Review"),
        //         'name' => 'View',
        //         'url' => "sitestore/review/view/" . $sitestore->store_id . "/" . $review->review_id,
        //     );
        // }
        if ($review->owner_id == $viewer_id || $level_id == 1) {
            $guttermenu[] = array(
                'label' => $this->translate("Delete Review"),
                'name' => 'delete',
                'url' => "sitestore/review/delete/" . $sitestore->store_id . "/" . $review->review_id,
            );

            $guttermenu[] = array(
                'label' => $this->translate("Update Review"),
                'name' => 'edit_review',
                'url' => "sitestore/review/edit/" . $sitestore->store_id . "/" . $review->review_id,                
            );
        }

        if ($action == 'view') {
            $guttermenu[] = array(
                'label' => $this->translate("Comment"),
                'url' => "sitestore/review/comment/" . $sitestore->store_id . "/" . $review->review_id,
                'name' => 'comment'
            );

            $likeTable = Engine_Api::_()->getDbtable('likes', 'core');
            if ($likeTable->isLike($review, $viewer)) {
                $guttermenu[] = array(
                    'label' => $this->translate("Unlike"),
                    'url' => "sitestore/review/unlike/" . $sitestore->store_id . "/" . $review->review_id,
                    'name' => 'unlike'
                );
            } else {
                $guttermenu[] = array(
                    'label' => $this->translate("like"),
                    'url' => "sitestore/review/like/" . $sitestore->store_id . "/" . $review->review_id,
                    'name' => 'unlike'
                );
            }
        }

        return $guttermenu;
    }

}
