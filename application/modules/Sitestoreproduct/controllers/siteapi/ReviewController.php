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
class Sitestoreproduct_ReviewController extends Siteapi_Controller_Action_Standard {

    public function init() {
        $viewer = Engine_Api::_()->user()->getViewer();

        // Authorization check
        if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', $viewer, "view")->isValid())
            $this->respondWithError('unauthorized');
    }

    /*
     * Browse product review
     */

    public function browseAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!empty($viewer_id)) {
            $level_id = Engine_Api::_()->user()->getViewer()->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $params = $this->_getAllParams();

        $response = array();

        $product_id = $this->_getParam('product_id');
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        $sitestore = $subject = Engine_Api::_()->getItem("sitestore_store" , $sitestoreproduct->store_id);

        if(!$sitestore || !$sitestoreproduct)
            $this->respondWithError("no_record");

        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct');
        $ratingTable = Engine_Api::_()->getDbTable('ratings', 'sitestoreproduct');
        $ratingTableName = $ratingTable->info('name');
        $ratingParamsTable = Engine_Api::_()->getDbtable('ratingparams', 'sitestoreproduct');
        $ratingParamsTableName = $ratingParamsTable->info('name');

        $hasPosted = $reviewTable->canPostReview($sitestoreproduct->getIdentity(), $viewer_id);

        $params['resource_type'] = "sitestoreproduct_product";
        $params['category_id'] = $sitestoreproduct->category_id;
        $params['subcategory_id'] = $sitestoreproduct->subcategory_id;
        $params['subsubcategory_id'] = $sitestoreproduct->subsubcategory_id;

        try {
            $paginator = $reviewTable->getReviewsPaginator($params);
            $paginator->setItemCountPerPage(10);
            $totalReviewCount = $paginator->getTotalItemCount();

            $noReviewCheck = $reviewTable->getAvgRecommendation($params);

            if (!empty($noReviewCheck)) {
                $noReviewCheck = $noReviewCheck->toArray();
                if ($noReviewCheck)
                    $recommend_percentage = round($noReviewCheck[0]['avg_recommend'] * 100, 3);
            }

            if ($viewer_id) {
                $create_level_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestoreproduct_review', "review_create");
            } else {
                $create_level_allow = 0;
            }

            $create_review = ($sitestoreproduct->owner_id == $viewer_id) ? Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproductreview.allowownerreview', 1) : 1;

            if (!$create_review || empty($create_level_allow)) {
                $can_create = 0;
            } else {
                $can_create = 1;
            }

            if ($viewer_id) {
                $can_delete = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestoreproduct_review', "review_delete");

                $can_reply = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestoreproduct_review', "review_reply");

                $can_update = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestoreproduct_review', "review_update");
            } else {
                $can_delete = $can_reply = $can_update = 0;
            }

            // review breackdown rating_params
            $profileRatingsselect = $ratingTable->select()
                    ->setIntegrityCheck(false)
                    ->from($ratingTableName, array('avg(rating) as avg_rating', 'ratingparam_id'))
                    ->joinLeft($ratingParamsTableName, "$ratingTableName.ratingparam_id = $ratingParamsTableName.ratingparam_id", array('ratingparam_name'))
                    ->where('resource_id=?', $sitestoreproduct->getIdentity())
                    ->group("$ratingParamsTableName.ratingparam_id");

            $averageProfileRatings = $profileRatingsselect->query()->fetchALL();

            $rating_params_data = array();
            if (!empty($averageProfileRatings)) {
                foreach ($averageProfileRatings as $value) {
                    if ($value['ratingparam_id'])
                        $rating_params_data[] = $value;
                }
            }

            $hasPosted = Engine_Api::_()->getDbtable('reviews', 'sitestoreproduct')->canPostReview(array('resource_type' => "sitestoreproduct_product","resource_id" => $sitestoreproduct->getIdentity(),'viewer_id' => $viewer_id));

            $reviewRateMyData = $ratingTable->ratingsData($hasPosted);

            if (isset($params['getRating']) && !empty($params['getRating'])) {
                $ratings['rating_avg'] = $sitestoreproduct->rating_avg;                
                $ratings['rating_users'] = $sitestoreproduct->rating_users;
                $ratings['breakdown_ratings_params'] = $rating_params_data;
                $ratings['myRatings'] = $reviewRateMyData;
                $ratings['hasPosted'] = $hasPosted;
                $ratings['recomended'] = $recommend_percentage . " %";
                $response['ratings'] = $ratings;
            }

            $metaParams = array();
            $response['total_reviews'] = $totalReviewCount;
            $response['content_title'] = $sitestoreproduct->title;

            $helpfulTable = Engine_Api::_()->getDbtable("helpful" , "sitestoreproduct");

            foreach ($paginator as $review) {

                $data = $review->toArray();
                $data['owner_title'] = $review->getOwner()->getTitle();
                $data = @array_merge($data, Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($review, true));
                $data = @array_merge($data, Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($review, false));
                $corelikesTable = Engine_Api::_()->getDbtable('likes', 'core');
                $isliked = $corelikesTable->getLike($review, $viewer);
                $data['store_id'] = $sitestore->getIdentity();
                $data['is_liked'] = false;
                if ($isliked)
                    $data['is_liked'] = true;

                $data['helpful_count'] = $helpfulTable->getCountHelpful($review->getIdentity(),1) ;
                $data['nothelpful_count'] = $helpfulTable->getCountHelpful($review->getIdentity(),2) ;

                $data['is_helful'] = (bool)$helpfulTable->getHelpful($review->getIdentity(),$viewer_id,1) ;
                $data['is_not_helful'] = (bool)$helpfulTable->getHelpful($review->getIdentity(),$viewer_id,2) ;

                $data['guttermenu'] = $this->gutterMenu($sitestore, $sitestoreproduct, $review);

                $breakdown_ratings_params = array();

                $profileRatingsselect = $ratingTable->select()
                        ->setIntegrityCheck(false)
                        ->from($ratingTableName, array('rating', 'ratingparam_id'))
                        ->joinLeft($ratingParamsTableName, "$ratingTableName.ratingparam_id = $ratingParamsTableName.ratingparam_id", array('ratingparam_name'))
                        ->where('review_id=?', $review->getIdentity());

                $profileRating = $profileRatingsselect->query()->fetchAll();

                if (!empty($profileRating)) {
                    foreach ($profileRating as $value) {
                        if ($value['ratingparam_id'])
                            $breakdown_ratings_params[] = $value;
                        else
                            $data['overall_rating'] = $value['rating'];
                    }
                }

                $data['breakdown_ratings_params'] = $breakdown_ratings_params;

                $response['reviews'][] = $data;

            }

            $this->respondwithsuccess($response, true);
        } catch (Exception $ex) {
            echo $ex;
            die;
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * Create Review
     */

    public function createAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $product_id = $this->_getParam('product_id');
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        $sitestore = $subject = Engine_Api::_()->getItem("sitestore_store" , $sitestoreproduct->store_id);

        if (!$sitestoreproduct || !$sitestore)
            $this->respondWithError('no_record');

        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $can_create = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestoreproduct_product', "review_create");

        if (empty($can_create)) {
            $this->respondwitherror('unauthorized');
        }

        $reviewTable = Engine_Api::_()->getDbtable("reviews","sitestoreproduct");

        $hasPosted = $reviewTable->canPostReview(array('resource_id' => $sitestoreproduct->getIdentity(),'resource_type'=>$sitestoreproduct->getType(),'viewer_id' => $viewer_id));

        if ($hasPosted)
            $this->respondwitherror('review_already_present');

        $categoryIdsArray = array();
        $categoryIdsArray['category_id'] = $sitestoreproduct->category_id;
        $categoryIdsArray['subcategory_id'] = $sitestoreproduct->subcategory_id;
        $categoryIdsArray['subsubcategory_id'] = $sitestoreproduct->subsubcategory_id;

        $ratingParamsTable = Engine_Api::_()->getDbtable('ratingparams', 'sitestoreproduct');
        $ratingParams = $ratingParamsTable->reviewParams($categoryIdsArray, null);

        $ratingParamsArray = array();
        $data = array();
        $data['type'] = "Rating";
        $data['name'] = "review_rate_0";
        $data['label'] = $this->translate("Overall Rating");
        $ratingParamsArray[] = $data;
        foreach ($ratingParams as $row => $value) {
            $data = array();
            $data['type'] = "Rating";
            $data['name'] = "review_rate_" . $value['ratingparam_id'];
            $data['label'] = $this->translate($value['ratingparam_name']);
            $ratingParamsArray[] = $data;
        }


        $coreApi = Engine_Api::_()->getApi('settings', 'core');
        $sitestoreproductreview_proscons = $coreApi->getSetting('sitestoreproduct.proscons', 1);
        $sitestoreproductreview_limit_proscons = $coreApi->getSetting('sitestoreproduct.limit.proscons', 1);
        $sitestoreproductreview_recommended = $coreApi->getSetting('sitestoreproduct.recommend', 1);
        $profileTypeReview = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getProfileType($categoryIdsArray, $sitestoreproduct->category_id);

        if ($this->getRequest()->isGet()) {

            if ($sitestoreproductreview_proscons) {
                $form[] = array(
                    'type' => 'Textarea',
                    'name' => 'pros',
                    'label' => $this->translate('Pros'),
                    'description' => $this->translate("What do you like about this Page?"),
                    'hasValidator' => true,
                );
                $form[] = array(
                    'type' => 'Textarea',
                    'name' => 'cons',
                    'label' => $this->translate('Cons'),
                    'description' => $this->translate("What do you dislike about this Page?"),
                    'hasValidator' => true,
                );
            }

            $form[] = array(
                'type' => 'Textarea',
                'name' => 'title',
                'label' => $this->translate('One-line summary'),
            );

            $form[] = array(
                'type' => 'Textarea',
                'name' => 'body',
                'label' => $this->translate("Summary"),
            );

            if ($sitestoreproductreview_recommended) {
                $form[] = array(
                    'name' => 'recommend',
                    'type' => 'Radio',
                    'label' => $this->translate("Recommend"),
                    'description' => $this->translate("Would you recommend this Page to a friend?"),
                    'multiOptions' => array(
                        '1' => $this->translate('Yes'),
                        '0' => $this->translate('NO'),
                    ),
                );
            }

            $form[] = array(
                    'type' => "Submit",
                    "name" => "submit",
                    'label' => $this->translate("Submit"),
                );

            $response['form'] = $form;

            $response['ratingParams'] = $ratingParamsArray;

            $this->respondwithsuccess($response, false);
        }

        if ($this->getRequest()->isPost()) {
            $values = $postData = $this->_getAllParams();

            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitestoreproduct')
                    ->getReviewCreateFormValidators(array("settingsReview" =>
                array('sitestoreproductreview_proscons' => $sitestoreproductreview_proscons,
                    'sitestoreproductreview_limit_proscons' => $sitestoreproductreview_limit_proscons,
                    'sitestoreproductreview_recommended' => $sitestoreproductreview_recommended)));

            $values['validators'] = $validators;
            $validationMessage = $this->isValid($values);

            if(!is_array($validationMessage))
                $validationMessage = array();

            // Minlength params
            if(isset($postData['pros']) && !isset($validationMessage['pros']) && strlen($postData['pros'])<10)
            {
                $validationMessage['pros'] = $this->translate("Please enter at least 10 characters , You entered ".strlen($postData['pros']));
            }

            if(isset($postData['cons']) && !isset($validationMessage['cons']) && strlen($postData['cons'])<10)
            {
                $validationMessage['cons'] = $this->translate("Please enter at least 10 characters , You entered ".strlen($postData['cons']));
            }

            if(isset($postData['title']) && !isset($validationMessage['title']) && strlen($postData['title'])<10)
            {
                $validationMessage['title'] = $this->translate("Please enter at least 10 characters , You entered ".strlen($postData['title']));
            }

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
                $values['user_id'] = $viewer_id;
                $values['owner_id'] = $viewer_id;
                $values['resource_id'] = $sitestoreproduct->getIdentity();
                $values['resource_type'] = $sitestoreproduct->getType();
                $values['category_id'] = $sitestoreproduct->category_id;
                $values['profile_type_review'] = $profileTypeReview;
                $values['type'] = $viewer_id ? 'user' : 'visitor';

                if (!$sitestoreproductreview_recommended)
                    $values['recommend'] = 0;

                $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct');
                $review = $reviewTable->createRow();
                $review->setFromArray($values);
                $review->view_count = 1;
                $review->save();

                $review_id = $review->getIdentity();
                $values['review_id'] = $review_id;

                if ($review_id)
                    ++$sitestoreproduct->review_count;
                $sitestoreproduct->save();

                $ratingTable = Engine_Api::_()->getDbTable('ratings', 'sitestoreproduct');
                $ratingTable->createRatingData($values, $values['type']);

                $ratingTable->listRatingUpdate($sitestoreproduct->getIdentity(),"sitestoreproduct_product");

                $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

                $action = $activityApi->addActivity($viewer, $sitestoreproduct, 'sitestoreproduct_review_add');

                if ($action != null) {
                    $activityApi->attachActivity($action, $review);
                }

                $db->commit();
                $this->successResponseNoContent('no_content');
            } catch (Exception $ex) {
                $db->rollback();
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
        }
    }

    /*
     * Edit review Action
     */

    public function editAction() {

        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        // Gets logged in user info
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $product_id = $this->_getParam('product_id');
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        $sitestore = $subject = Engine_Api::_()->getItem("sitestore_store" , $sitestoreproduct->store_id);

        if (!$sitestoreproduct || !sitestore)
            $this->respondWithError('no_record');

        $review_id = $this->_getParam('review_id');

        $sitestoreproductreview = Engine_Api::_()->getItem('sitestoreproduct_review', $review_id);

        if (!$sitestoreproductreview)
            $this->respondwithError('no_record');

        //FATCH REVIEW CATEGORIES
        $categoryIdsArray = array();
        $categoryIdsArray[] = $sitestoreproduct->category_id;
        $categoryIdsArray[] = $sitestoreproduct->subcategory_id;
        $categoryIdsArray[] = $sitestoreproduct->subsubcategory_id;
        $profileTypeReview = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getProfileType($categoryIdsArray, 0, 'profile_type_review');

        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $can_update = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestoreproduct_product', "review_update");

        if (empty($can_update)) {
            return $this->respondwitherror('unauthorized');
        }

        $ratingParamsTable = Engine_Api::_()->getDbtable('ratingparams', 'sitestoreproduct');
        $ratingParams = $ratingParamsTable->reviewParams($categoryIdsArray, null);

        $ratingParamsArray = array();
        $data = array();
        $data['type'] = "Rating";
        $data['name'] = "review_rate_0";
        $data['label'] = $this->translate("Overall Rating");
        $ratingParamsArray[] = $data;
        foreach ($ratingParams as $row => $value) {
            $data = array();
            $data['type'] = "Rating";
            $data['name'] = "review_rate_" . $value['ratingparam_id'];
            $data['label'] = $this->translate($value['ratingparam_name']);
            $ratingParamsArray[] = $data;
        }

        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct');
        $ratingTable = Engine_Api::_()->getDbTable('ratings', 'sitestoreproduct');
        $ratingTableName = $ratingTable->info('name');
        $ratingParamsTable = Engine_Api::_()->getDbtable('ratingparams', 'sitestoreproduct');
        $ratingParamsTableName = $ratingParamsTable->info('name');
        $coreApi = Engine_Api::_()->getApi('settings', 'core');
        $sitestoreproductreview_proscons = $coreApi->getSetting('sitestoreproduct.proscons', 1);
        $sitestoreproductreview_limit_proscons = $coreApi->getSetting('sitestoreproduct.limit.proscons', 1);
        $sitestoreproductreview_recommended = $coreApi->getSetting('sitestoreproduct.recommend', 1);
        $profileTypeReview = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getProfileType(array(), $sitestoreproduct->category_id);

        if ($this->getRequest()->isGet()) {

            if ($sitestoreproductreview_proscons) {
                // $form[] = array(
                //     'type' => 'Textarea',
                //     'name' => 'pros',
                //     'label' => $this->translate('Pros'),
                //     'description' => $this->translate("What do you like about this Page?"),
                //     'hasValidator' => true,
                // );
                // $form[] = array(
                //     'type' => 'Textarea',
                //     'name' => 'cons',
                //     'label' => $this->translate('Cons'),
                //     'description' => $this->translate("What do you dislike about this Page?"),
                //     'hasValidator' => true,
                // );
            }

            // $form[] = array(
            //     'type' => 'Textarea',
            //     'name' => 'title',
            //     'label' => $this->translate('One-line summary'),
            // );

            $form[] = array(
                'type' => 'Textarea',
                'name' => 'body',
                'label' => $this->translate("Summary"),
            );

            // if ($sitestoreproductreview_recommended) {
            //     $form[] = array(
            //         'name' => 'recommend',
            //         'type' => 'Radio',
            //         'label' => $this->translate("Recommend"),
            //         'description' => $this->translate("Would you recommend this Page to a friend?"),
            //         'multiOptions' => array(
            //             '1' => $this->translate('Yes'),
            //             '0' => $this->translate('NO'),
            //         ),
            //     );
            // }

            $form[] = array(
                    'type' => "Submit",
                    "name" => "submit",
                    'label' => $this->translate("Submit"),
                );

            $response = array();
            $response['form'] = $form;
            $data = $sitestoreproductreview->toArray();
            $profileRatingsselect = $ratingTable->select()
                    ->setIntegrityCheck(false)
                    ->from($ratingTableName, array('rating', 'ratingparam_id'))
                    ->joinLeft($ratingParamsTableName, "$ratingTableName.ratingparam_id = $ratingParamsTableName.ratingparam_id", array('ratingparam_name'))
                    ->where('review_id = ?', $sitestoreproductreview->getIdentity())
                    ->where('user_id = ?', $viewer_id);

            $profileRatings = $profileRatingsselect->query()->fetchAll();
            foreach($profileRatings as $ratingKey => $ratingValue)
                $data['review_rate_'.$ratingValue['ratingparam_id']] = $ratingValue['rating'];
            $response['formValues'] = $data;
            $response['ratingParams'] = $ratingParamsArray;

            $this->respondwithsuccess($response, false);
        }

        if ($this->getRequest()->isPost() || $this->getRequest()->isPut()) {
            $values = $postData = $this->_getAllParams();

            // $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitestoreproduct')
            //         ->getReviewCreateFormValidators(array("settingsReview" =>
            //     array('sitestoreproductreview_proscons' => $sitestoreproductreview_proscons,
            //         'sitestoreproductreview_limit_proscons' => $sitestoreproductreview_limit_proscons,
            //         'sitestoreproductreview_recommended' => $sitestoreproductreview_recommended)));

            // $values['validators'] = $validators;
            // $validationMessage = $this->isValid($values);

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
            // MinLength Params Ends

            // Response validation error
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $values['user_id'] = $viewer_id;
                $values['owner_id'] = $viewer_id;
                $values['resource_id'] = $sitestoreproduct->getIdentity();
                $values['resource_type'] = $sitestoreproduct->getType();
                $values['category_id'] = $sitestoreproduct->category_id;
                $values['profile_type_review'] = $profileTypeReview;
                $values['type'] = $viewer_id ? 'user' : 'visitor';

                if (!$sitestoreproductreview_recommended)
                    $values['recommend'] = 0;

                $sitestoreproductreview->setFromArray($values);
                $sitestoreproductreview->save();

                $review_id = $sitestoreproductreview->getIdentity();
                $values['review_id'] = $review_id;

                // code to remove the previous rating data
                $ratingTable->delete(array('review_id' => $sitestoreproductreview->getIdentity()));

                // create new ratings for the review
                $ratingTable->createRatingData($values, $values['type']);

                $ratingTable->listRatingUpdate($sitestoreproduct->getIdentity(),"sitestoreproduct_product");

                $db->commit();
                $this->successResponseNoContent('no_content');
            } catch (Exception $ex) {
                $db->rollback();
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
        }
    }

    /*
     * View review
     */

    public function viewAction() {
        $this->validateRequestMethod();

        // Require user
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        // Gets logged in user info
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $product_id = $this->_getParam('product_id');
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        $sitestore = $subject = Engine_Api::_()->getItem("sitestore_store" , $sitestoreproduct->store_id);

        if (!$sitestoreproduct || !sitestore)
            $this->respondWithError('no_record');

        $review_id = $this->_getParam('review_id');

        $sitestoreproductreview = Engine_Api::_()->getItem('sitestoreproduct_review', $review_id);

        if (!$sitestoreproductreview)
            $this->respondwithError('no_record');

        $sitestoreproductreview_params = $sitestoreproductreview->getRatingData();

        $response = array();
        $response['review'] = $sitestoreproductreview->toArray();
        $helpfulTable = Engine_Api::_()->getDbTable('helpful', 'sitestoreproduct');
        $helpful = $helpfulTable->select()
                ->from($helpfulTable->info('name'), '*')
                ->where('review_id = ?', $sitestoreproductreview->getIdentity())
                ->where('owner_id = ?', $viewer_id)
                ->query()
                ->fetch();

        $response['review']['helpful'] = 0;

        if ($helpful)
            $response['helpful'] = $helpful['helpful'];

        $response['review']['owner_title'] = $sitestoreproductreview->getOwner()->getTitle();
        $response['gutterMenu'] = $this->gutterMenu($sitestore, $sitestoreproduct, $sitestoreproductreview);
        $response['review']['rating'] = $sitestoreproductreview_params[0]['rating'];
        unset($sitestoreproductreview_params[0]);
        $response['ratingData'] = $sitestoreproductreview_params;
        $this->respondWithSuccess($response, false);
    }

    /*
     * delete review
     */

    public function deleteAction() {
        $this->validateRequestMethod("DELETE");
        // Require user
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        // Gets logged in user info
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $product_id = $this->_getParam('product_id');
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        $sitestore = $subject = Engine_Api::_()->getItem("sitestore_store" , $sitestoreproduct->store_id);

        if (!$sitestoreproduct || !sitestore)
            $this->respondWithError('no_record');

        $review_id = $this->_getParam('review_id');

        $sitestoreproductreview = Engine_Api::_()->getItem('sitestoreproduct_review', $review_id);

        if (!$sitestoreproductreview)
            $this->respondwithError('no_record');
        $ratingTable = Engine_Api::_()->getDbTable('ratings', 'sitestoreproduct');

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try { 
            $sitestoreproductreview->delete();
            $ratingTable->listRatingUpdate($sitestoreproduct->getIdentity(),"sitestoreproduct_product");
            $db->commit();
            $this->successResponseNoContent('no_content');
        } catch (Exception $ex) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * like / unline a review
     */

    public function likeAction() {
        $this->validateRequestMethod("POST");
        // Require user
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        // Gets logged in user info
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $product_id = $this->_getParam('product_id');
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        if (!$sitestoreproduct)
            $this->respondWithError('no_record');

        $review_id = $this->_getParam('review_id');

        $sitestoreproductreview = Engine_Api::_()->getItem('sitestoreproduct_review', $review_id);

        $sitestore = $subject = Engine_Api::_()->getItem("sitestore_store" , $sitestoreproduct->store_id);

        if (!$sitestoreproduct || !sitestore)
            $this->respondWithError('no_record');

        $commentedItem = $sitestoreproductreview;
        // Process
        $db = $commentedItem->likes()->getAdapter();
        $db->beginTransaction();
        try {

            if ($commentedItem->likes()->isLike($viewer)) {
                $commentedItem->likes()->removeLike($viewer);
            } else {
                $commentedItem->likes()->addLike($viewer);
                // Add notification
                $owner = $commentedItem->getOwner();
                if ($owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity()) {
                    $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                   try {
                        $notifyApi->addNotification($owner, $viewer, $commentedItem, 'liked', array(
                            'label' => $commentedItem->getShortType()
                        ));
                    } catch (Exception $ex) {
                        
                    }
                }
            }

            $db->commit();
            $this->successResponseNoContent('no_content');
        } catch (Exception $ex) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * comment on a review
     */

    public function commentAction() {
        // Require user
        // if (!$this->_helper->requireUser()->isValid())
        // $this->respondWithError('unauthorized');

        $this->validateRequestMethod("POST");

        // Gets logged in user info
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!$viewer_id)
            $this->respondWithError('unauthorized');

        $product_id = $this->_getParam('product_id');
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        $sitestore = $subject = Engine_Api::_()->getItem("sitestore_store" , $sitestoreproduct->store_id);

        if (!$sitestoreproduct || !sitestore)
            $this->respondWithError('no_record');

        $review_id = $this->_getParam('review_id');

        $sitestoreproductreview = Engine_Api::_()->getItem('sitestoreproduct_review', $review_id);

        if (!$sitestoreproductreview)
            $this->respondwithError('no_record');

        $values = array();
        $values = $this->_getAllParams();

        // Start form validation
        $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitestoreproduct')->getcommentValidation();
        $values['validators'] = $validators;
        $validationMessage = $this->isValid($values);

        // Response validation error
        if (!empty($validationMessage) && @is_array($validationMessage)) {
            $this->respondWithValidationError('validation_fail', $validationMessage);
        }

        $body = $values['body'];
        $values['type'] = $sitestoreproductreview->getType();
        $values['id'] = $sitestoreproductreview->review_id;
        $values['identity'] = $sitestoreproductreview->review_id;
        $db = $sitestoreproductreview->comments()->getCommentTable()->getAdapter();
        $db->beginTransaction();
        try {
            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $subjectOwner = $sitestoreproductreview->getOwner('user');
            $sitestoreproductreview->comments()->addComment($viewer, $body);

            // Activity
            $action = $activityApi->addActivity($viewer, $sitestoreproductreview, 'comment_' . $sitestoreproductreview->getType(), '', array(
                'owner' => $subjectOwner->getGuid(),
                'body' => $body
            ));

            if (!empty($action)) {
                $activityApi->attachActivity($action, $sitestoreproductreview);
            }

            // Increment comment count
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');

            $db->commit();
            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /*
     * list comments
     */

    public function listCommentsAction() {
        $this->validateRequestMethod();
        // Require user
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        // Gets logged in user info
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $product_id = $this->_getParam('product_id');
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        $sitestore = $subject = Engine_Api::_()->getItem("sitestore_store" , $sitestoreproduct->store_id);

        if (!$sitestoreproduct || !sitestore)
            $this->respondWithError('no_record');

        $review_id = $this->_getParam('review_id');

        $sitestoreproductreview = Engine_Api::_()->getItem('sitestoreproduct_review', $review_id);

        if (!$sitestoreproductreview)
            $this->respondwithError('no_record');

        $likes = $sitestoreproductreview->likes()->getLikePaginator();
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

        if (null !== ( $page = $this->_getParam('page'))) {
            $commentSelect = $sitestoreproductreview->comments()->getCommentSelect('ASC');
            $commentSelect->order('comment_id ASC');
            $comments = Zend_Paginator::factory($commentSelect);
            $comments->setCurrentPageNumber($page);
            $comments->setItemCountPerPage(10);
        }
        // If not has a page, show the
        else {
            $commentSelect = $sitestoreproductreview->comments()->getCommentSelect('DESC');
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

        $this->respondWithSuccess($response, false);
    }

    /*
     * helpful
     */

    public function helpfulAction() {
        // Require user
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        // Gets logged in user info
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $product_id = $this->_getParam('product_id');
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        $sitestore = $subject = Engine_Api::_()->getItem("sitestore_store" , $sitestoreproduct->store_id);

        if (!$sitestoreproduct || !sitestore)
            $this->respondWithError('no_record');

        $review_id = $this->_getParam('review_id');

        $sitestoreproductreview = Engine_Api::_()->getItem('sitestoreproduct_review', $review_id);

        if (!$sitestoreproductreview)
            $this->respondwithError('no_record');

        if ($this->getRequest()->isGet()) {
            $form = array();

            $form[] = array(
                'name' => 'helpful',
                'label' => $this->translate('Was this review Helpfull ?'),
                'type' => 'checkbox',
                'multiOptions' => array(
                    '1' => 'Helpful',
                    '2' => 'Not Helpful',
                ),
            );

            $this->respondwithsuccess($form, true);
        }

        if ($this->getRequest()->isPost()) {

            $values = $this->_getAllParams();

            if (empty($values['helpful']))
                $this->respondwithvalidationerror('parameter_missing', 'parameter name helpful missing');

            $helpful = $values['helpful'];

            $helpfulTable = Engine_Api::_()->getDbTable('helpful', 'sitestoreproduct');

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $helpfulTable->setHelful($sitestoreproductreview->getIdentity(), $viewer_id, $helpful);
                $db->commit();
                $this->successResponseNoContent('no_content');
            } catch (Exception $ex) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
        }
    }

    /*
     * review guttermenu
     */

    private function gutterMenu($sitestore, $sitestoreproduct, $sitestoreproductreview) {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $coreApi = Engine_Api::_()->getDbTable('settings', 'core');
        $sitestoreproductreview_proscons = $coreApi->getSetting('sitestoreproduct.proscons', 1);
        $sitestoreproductreview_limit_proscons = $coreApi->getSetting('sitestoreproduct.limit.proscons', 1);
        $sitestoreproductreview_recommended = $coreApi->getSetting('sitestoreproduct.recommend', 1);
        $profileTypeReview = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getProfileType(array(), $sitestoreproduct->category_id);
        $sitestoreproduct_share = $coreApi->getSetting('sitestoreproduct.share', 1);
        $sitestoreproduct_report = $coreApi->getSetting('sitestoreproduct.report', 1);
        $sitestoreproduct_email = $coreApi->getSetting('sitestoreproduct.email', 1);

        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        // can edit
        $can_edit = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestoreproduct_product', "edit");

        if ($can_edit) {
            $menu[] = array(
                'label' => $this->translate('Update Review'),
                'name' => 'edit_review',
                'url' => 'sitestore/product/review/edit/'. $sitestoreproduct->getIdentity() . '/' . $sitestoreproductreview->getIdentity(),
            );
        }

        // delete
        $can_delete = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestoreproduct_product', "delete");

        if ($can_delete) {
            $menu[] = array(
                'label' => $this->translate('Delete Review'),
                'name' => 'delete',
                'url' => 'sitestore/product/review/delete/' . $sitestoreproduct->getIdentity() . '/' . $sitestoreproductreview->getIdentity(),
            );
        }

        // $corelikesTable = Engine_Api::_()->getDbtable('likes', 'core');
        // $isliked = $corelikesTable->getLike($sitestoreproductreview, $viewer);
        // $label = $this->translate('Like');
        // $name = "like";
        // if ($isliked) {
        //     $label = $this->translate('Unlike');
        //     $name = 'unlike';
        // }

        // $menu[] = array(
        //     'label' => $label,
        //     'name' => $name,
        //     'url' => 'sitestore/product/review/like/' . $sitestore->getIdentity() . '/' . $sitestoreproduct->getIdentity() . '/' . $sitestoreproductreview->getIdentity(),
        // );

        // $menu[] = array(
        //     'label' => $this->translate('comment'),
        //     'name' => 'comment',
        //     'url' => 'sitestore/product/review/comment/' . $sitestore->getIdentity() . '/' . $sitestoreproduct->getIdentity() . '/' . $sitestoreproductreview->getIdentity(),
        // );

        // $menu[] = array(
        //     'label' => $this->translate('List Comments and likes'),
        //     'name' => 'listcomments',
        //     'url' => 'sitestore/product/review/list-comments/' . $sitestore->getIdentity() . '/' . $sitestoreproduct->getIdentity() . '/' . $sitestoreproductreview->getIdentity(),
        // );

        if ($sitestoreproduct_share) {
            $menu[] = array(
                'name' => 'share',
                'label' => $this->translate('Share This Page'),
                'url' => 'activity/share',
                'urlParams' => array(
                    "type" => $sitestoreproductreview->getType(),
                    "id" => $sitestoreproductreview->getIdentity()
                )
            );
        }

        if ($sitestoreproduct_report) {
            $menu[] = array(
                'name' => 'report',
                'label' => $this->translate('Report This Page'),
                'url' => 'report/create/subject/' . $sitestoreproductreview->getGuid(),
                'urlParams' => array(
                    "type" => $sitestoreproductreview->getType(),
                    "id" => $sitestoreproductreview->getIdentity()
                )
            );
        }

        // $menu[] = array(
        //     'label' => $this->translate('Was this review helpful'),
        //     'name' => 'helpful',
        //     'url' => 'sitestore/product/review/helpful/' . $sitestore->getIdentity() . '/' . $sitestoreproduct->getIdentity() . '/' . $sitestoreproductreview->getIdentity(),
        // );

        return $menu;
    }

}
