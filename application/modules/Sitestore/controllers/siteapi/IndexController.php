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
class Sitestore_IndexController extends Siteapi_Controller_Action_Standard {

    public function init() {

        $viewer = Engine_Api::_()->user()->getViewer();

        // Authorization check        
        if (!Engine_Api::_()->authorization()->isAllowed('sitestore_store', $viewer, 'view'))
            $this->respondWithError('unauthorized');

    }

    /*
     * Get browse stores listing
     */

    public function browseAction() {
        $this->validateRequestMethod();
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $values = $this->_getAllParams();

        if (empty($values['page']))
            $values['page'] = 1;

        if (empty($values['limit']))
            $values['limit'] = 20;

        $values['type'] = "browse";

        $this->respondWithSuccess($this->getStorePagination($values) , true);
    }

    /*
     * Get manage stores listing
     */

    public function manageAction() {
        
        $this->validateRequestMethod();
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity())
            $this->respondWithError('unauthorized');

        $values = $this->_getAllParams();

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
        // Set the user object to get the respective result
        $values['user'] = $viewer;
        $values['type'] = "manage";


        $this->respondWithSuccess($this->getStorePagination($values),true);
    }

    /*
     * Get browse/manage page search api
     */

    public function searchFormAction() {
        $searchForm = Engine_Api::_()->getApi('Siteapi_Core', 'sitestore')->getSearchForm();

        $this->respondWithSuccess($searchForm,false);
    }

    /*
     * Get listing of stores
     * 
     * @param $values array
     * @return array
     */

    private function getStorePagination($values) {
        $response = array();
        $viewer = Engine_Api::_()->user()->getViewer();

        if (isset($values['action']) && $values['action'] == 'browse')
            $values['closed'] = 0;

        $paginator = Engine_Api::_()->getApi('Siteapi_Core', 'sitestore')->getStorePaginator($values);
        $response['totalItemCount'] = $paginator->getTotalItemCount();
        $response['canCreate'] = Engine_Api::_()->authorization()->isAllowed('sitestore_store', $viewer, 'create');

        // Get the data on each iteration 
        if (!empty($paginator)) {
            foreach ($paginator as $store) {
                $data = $store->toArray();

                // Set the Gutter Menus in manage stores calling
                if ($store->isOwner($viewer) && isset($values['action']) && ($values['action'] == 'manage'))
                    $data["menu"] = $this->_getGutterMenu($store, 'manage');

                // Set the category title name
                if (isset($data['category_id']) && !empty($data['category_id'])) {
                    $categoryObj = Engine_Api::_()->getItem('sitestore_category', $data['category_id']);
                    if (isset($categoryObj) && !empty($categoryObj))
                        $data['category_title'] = $categoryObj->getTitle();

                    if (!empty($data['category_title']))
                        $data['category_title'] = $this->translate($data['category_title']);
                }

                // Set package title
                if (isset($store['package_id']) && !empty($store['package_id'])) {
                    $package = Engine_Api::_()->getItem('sitestore_package', $store['package_id']);
                    if (!empty($package))
                        $data['package'] = $package->getTitle();
                }

                // Set the store url
                $contentUrl = Engine_Api::_()->getApi('Core', 'siteapi')->getContentURL($store);
                $data = @array_merge($data, $contentUrl);

                // Set the store images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($store);
                $data = @array_merge($data, $getContentImages);

                // Set the store owner images
                $getContentOwnerImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($store, true);
                $data = @array_merge($data, $getContentOwnerImages);

                // Set the owner title
                $data["owner_title"] = $this->translate($store->getOwner()->getTitle());

                // Set the store owner url
                $ownerUrl = Engine_Api::_()->getApi('Core', 'siteapi')->getContentURL($store->getOwner(), "owner_url");
                $data = @array_merge($data, $ownerUrl);

                // Set view privacy
                $isAllowedView = $store->authorization()->isAllowed($viewer, 'view');
                $data["allow_to_view"] = empty($isAllowedView) ? 0 : 1;

                $like = $store->likes()->isLike($viewer);
                $data["is_liked"] = ($like) ? 1 : 0;

                $follow = Engine_Api::_()->getDbtable('follows', 'seaocore')->isFollow($store, $viewer);

                $data['is_followed'] = $follow ? 1 : 0;

                // Set edit privacy
                $isAllowedEdit = $store->authorization()->isAllowed($viewer, 'edit');
                $data["edit"] = empty($isAllowedEdit) ? 0 : 1;

                // Set delete privacy
                $isAllowedDelete = $store->authorization()->isAllowed($viewer, 'delete');
                $data["delete"] = empty($isAllowedDelete) ? 0 : 1;

                $response['response'][] = $data;
            }
        }

        return $response;
    }

    /**
     * Returns Categories , Sub-Categories, SubSub-Categories and pages array
     * 
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
        $showStores = $this->getRequestParam('showStores', 1);
        if ($this->getRequestParam('showCount')) {
            $showCount = 1;
        } else {
            $showCount = $this->getRequestParam('showCount', 0);
        }
        $orderBy = $this->getRequestParam('orderBy', 'category_name');

        $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitestore');
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        $categories = array();

        // Get pages table
        $tableSitepage = Engine_Api::_()->getDbtable('stores', 'sitestore');
        $sitepageShowAllCategories = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.categorywithslug', 1);
        $showAllCategories = !empty($sitepageShowAllCategories) ? $showAllCategories : 0;

        if ($showCategories) {
            $store = 1;
            if ($showAllCategories)
                $store = 0;

            $category_info = $tableCategory->getAllCategories(0, 'category_id', $store, '', '', '');
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

                $categories[] = $category_array;
            }

            $response['categories'] = $categories;

            if (!empty($category_id)) {

                $store = 1;
                if ($showAllCategories)
                    $store = 0;

                $category_info2 = $tableCategory->getAllCategories($category_id, 'subcategory_id', $store, '', '', '');
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

                $store = 1;
                if ($showAllCategories)
                    $store = 0;

                $category_info2 = $tableCategory->getAllCategories($subCategory_id, 'subsubcategory_id', $store, '', '', '');
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

        if ($showStores && isset($category_id) && !empty($category_id)) {

            $params = array();
            $itemCount = $params['itemCount'] = $this->_getParam('itemCount', 0);

            // Get categories
            $categories = array();

            $category_info = $tableCategory->getAllCategories($category_id, 'subcategory_id', 1, '', '', '');
            $category_stores_array = array();

            $values = $this->getAllParams();

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
            $response['stores'] = $this->getStorePagination($values);
        }
        if (isset($categoriesCount) && !empty($categoriesCount))
            $response['totalItemCount'] = $categoriesCount;
        $response['canCreate'] = Engine_Api::_()->authorization()->isAllowed('sitestore_store', $viewer, 'create');
        $this->respondWithSuccess($response, false);
    }

    /*
     * Get the Gutter Menus for Manage Stores page.
     * 
     * @return array
     */

    private function _getGutterMenu($subject, $action = null) {
        $menu = array();
        $viewer = Engine_Api::_()->user()->getViewer();

        if ($subject->authorization()->isAllowed($viewer, 'edit')) {
            if ($subject->closed) {
                $menu[] = array(
                    'label' => $this->translate('Open Store'),
                    'name' => 'open',
                    'url' => 'sitestore/close/' . $subject->getIdentity(),
                );
            } else {
                $menu[] = array(
                    'label' => $this->translate('Close Store'),
                    'name' => 'close',
                    'url' => 'sitestore/close/' . $subject->getIdentity(),
                );
            }
        }

        if ($subject->authorization()->isAllowed($viewer, 'delete')) {
            $menu[] = array(
                'label' => $this->translate('Delete Store'),
                'name' => 'delete',
                'url' => 'sitestore/delete/' . $subject->getIdentity(),
            );
        }

        return $menu;
    }

    /*
     * Gets the payment type
     */

    private function getPaymentType($object, $itemType) {
        $length = 7;
        $encodeorder = 0;
        $obj_length = strlen($object);
        if ($length > $obj_length)
            $length = $obj_length;
        for ($i = 0; $i < $length; $i++) {
            $encodeorder += ord($object[$i]);
        }
        $req_mode = $encodeorder % strlen($itemType);
        $encodeorder +=ord($itemType[$req_mode]);
        $isEnabled = Engine_Api::_()->sitestore()->isEnabled();
        if (empty($isEnabled)) {
            return 0;
        } else {
            return $encodeorder;
        }
    }

    /*
     * Get payment authorization
     */

    public function getPaymentAuth($strKey) {
        $str = explode("-", $strKey);
        $str = $str[2];
        $char_array = array();
        for ($i = 0; $i < 6; $i++)
            $char_array[] = $str[$i];
        $key = array();
        foreach ($char_array as $value) {
            $v_a = ord($value);
            if ($v_a > 47 && $v_a < 58)
                continue;
            $possition = 0;
            $possition = $v_a % 10;
            if ($possition > 5)
                $possition -=4;
            $key[] = $char_array[$possition];
        }
        $isEnabled = Engine_Api::_()->sitestore()->isEnabled();
        if (empty($isEnabled)) {
            return 0;
        } else {
            return $getStr = implode($key);
        }
    }

}
