<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Products.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Products extends Engine_Db_Table {

    protected $_rowClass = "Sitestoreproduct_Model_Product";
    protected $_serializedColumns = array('main_video', 'networks_privacy');

    public function expirySQL($select, $showExpiry = 0) {
        if (empty($select))
            return;
        $reviewApi = Engine_Api::_()->sitestoreproduct();

        $expirySettings = $reviewApi->expirySettings();

        if ($expirySettings == 2) {
            $approveDate = $reviewApi->adminExpiryDuration();
            if (empty($showExpiry))
                $select->where($this->info('name') . ".`approved_date` >= ?", $approveDate);
            else
                $select->where($this->info('name') . ".`approved_date` < ?", $approveDate);
        } elseif ($expirySettings == 1) {
            $current_date = date("Y-m-d i:s:m", time());
            if (empty($showExpiry))
                $select->where("(" . $this->info('name') . ".`end_date` = '0000-00-00 00:00:00' or " . $this->info('name') . ".`end_date` IS NULL or " . $this->info('name') . ".`end_date` >= ?)", $current_date);
            else {
                $select->where("(" . $this->info('name') . ".`end_date` != '0000-00-00 00:00:00' and " . $this->info('name') . ".`end_date` < ?)", $current_date);
            }
        }
        return $select;
    }
    public function ThumbListIcons($params = array()) {
        $productTableName = $this->info('name');
        $otherInfoTableName = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->info('name');

        $groupBy = 1;
        $sqlTimeStr = '';
        $popularity = $params['popularity'];
        $current_time = date("Y-m-d H:i:s");

        $title_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('title');
        $body_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('body');
        if ($title_column != 'title') {
            $temp_colums_array = array('product_id', "$title_column", 'title', 'price', 'photo_id', 'owner_id', 'category_id', 'view_count', 'review_count', 'like_count', 'comment_count', 'rating_avg', 'rating_editor', 'rating_users', 'sponsored', 'featured', 'newlabel', 'creation_date', "$body_column", 'body', 'product_type', 'in_stock', 'min_order_quantity', 'max_order_quantity', 'stock_unlimited', 'closed', 'draft', 'search', 'approved', 'start_date', 'end_date', 'end_date_enable', 'allow_purchase', 'store_id');
        } else {
            $temp_colums_array = array('product_id', 'title', 'price', 'photo_id', 'owner_id', 'category_id', 'view_count', 'review_count', 'like_count', 'comment_count', 'rating_avg', 'rating_editor', 'rating_users', 'sponsored', 'featured', 'newlabel', 'creation_date', 'body', 'product_type', 'in_stock', 'min_order_quantity', 'max_order_quantity', 'stock_unlimited', 'closed', 'draft', 'search', 'approved', 'start_date', 'end_date', 'end_date_enable', 'allow_purchase', 'store_id');
        }

        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($productTableName, $temp_colums_array)
                ->joinLeft($otherInfoTableName, "($otherInfoTableName.product_id = $productTableName.product_id)", array('discount', 'discount_value', 'discount_start_date', 'discount_end_date', 'discount_permanant', 'handling_type', 'user_type', 'discount_amount', 'product_info'));

        if ($popularity == 'review_count') {
            $popularityTable = Engine_Api::_()->getDbtable('reviews', 'sitestoreproduct');
            $popularityTableName = $popularityTable->info('name');
            $select = $select->joinLeft($popularityTableName, "($popularityTableName.resource_id = $productTableName.product_id and $reviewTableName .resource_type ='sitestoreproduct_product')", array("COUNT(review_id) as total_count"))
                    ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
                    ->order("total_count DESC");
        } elseif ($popularity == 'rating_avg' || $popularity == 'rating_editor' || $popularity == 'rating_users') {

            $ratingTable = Engine_Api::_()->getDbTable('ratings', 'sitestoreproduct');
            $ratingTableName = $ratingTable->info('name');

            $popularityTable = Engine_Api::_()->getDbtable('reviews', 'sitestoreproduct');
            $popularityTableName = $popularityTable->info('name');
            $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $productTableName . '.product_id', array(""))
                    ->join($ratingTableName, $ratingTableName . '.review_id = ' . $popularityTableName . '.review_id')
                    ->where($popularityTableName . '.resource_type = ?', 'sitestoreproduct_product')
                    ->where($ratingTableName . '.ratingparam_id = ?', 0);

            if ($popularity == 'rating_editor') {
                $select->where("$popularityTableName.type = ?", 'editor');
            } elseif ($popularity == 'rating_users') {
                $select->where("$popularityTableName.type = ?", 'user')
                        ->orWhere("$popularityTableName.type = ?", 'visitor');
            }
            $select->order("$productTableName.$popularity DESC");
        } elseif ($popularity == 'like_count') {
            $popularityTable = Engine_Api::_()->getDbtable('likes', 'core');
            $popularityTableName = $popularityTable->info('name');

            $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $productTableName . '.product_id', array("COUNT(like_id) as total_count"))
                    ->order("total_count DESC");
        } elseif ($popularity == 'comment_count') {
            $popularityTable = Engine_Api::_()->getDbtable('comments', 'core');
            $popularityTableName = $popularityTable->info('name');

            $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $productTableName . '.product_id', array("COUNT(comment_id) as total_count"))
                    ->order("total_count DESC");
        } elseif ($popularity == 'most_discussed') {
            $popularityTable = Engine_Api::_()->getDbtable('posts', 'sitestoreproduct');
            $popularityTableName = $popularityTable->info('name');
            $select = $select->joinLeft($popularityTableName, $popularityTableName . '.product_id = ' . $productTableName . '.product_id', array("COUNT(post_id) as total_count"))
                    ->order("total_count DESC");
        } elseif ($popularity == 'view_count' || $popularity == 'product_id' || $popularity == 'modified_date' || $popularity == 'creation_date') {
            $select->order("$productTableName.$popularity DESC");
        } elseif (($popularity == 'review_count' || $popularity == 'like_count' || $popularity == 'comment_count' || $popularity == 'rating_avg' || $popularity == 'rating_editor' || $popularity == 'rating_users')) {
            $select->order("$productTableName.$popularity DESC");
        }

        $select = $this->expirySQL($select);
        $select->group($productTableName . '.product_id');
        $select
                ->where($productTableName . '.approved = ?', '1')
                ->where($productTableName . '.search = ?', '1')
                ->where($productTableName . '.draft = ?', '0')
                ->where("$productTableName .start_date <= NOW()")
                ->where("$productTableName.end_date_enable = 0 OR $productTableName.end_date > NOW()")
                ->where("$productTableName.stock_unlimited = 1 OR $otherInfoTableName.out_of_stock = 1 OR $productTableName.min_order_quantity <= $productTableName.in_stock");

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
            $select->where($productTableName . '.closed = ?', '0');
        }

        if (isset($params['popularity']) && $params['popularity'] == 'discount_amount') {
            $select->order($otherInfoTableName . '.discount_amount DESC');
        }

        if (isset($params['popularity']) && $params['popularity'] == 'random') {
            $select->order('RAND() DESC ');
        }

        //Start Network work
        $select = $this->getNetworkBaseSql($select, array('not_groupBy' => $groupBy));
        //End Network work
        if (isset($params['paginator']) && !empty($params['paginator'])) {
            return $paginator = Zend_Paginator::factory($select);
        }
        if (isset($params['limit']) && !empty($params['limit'])) {
            $select->limit($params['limit']);
        }
        if (empty($params['productNonImage'])) {
            $select->where('photo_id != ?', 0);
        }

        if (isset($params['store_id']) && !empty($params['store_id'])) {
            $select->where('store_id = ?', $params['store_id']);
        }
        return $this->fetchAll($select);
    }

    public function getSitestoreproductsPaginator($params = array(), $customParams = null) {
        $paginator = Zend_Paginator::factory($this->getSitestoreproductsSelect($params, $customParams));
        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }

        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }

        return $paginator;
    }

//  public function getAllProductByStore($params = array(), $customParams = null){
//    $select = $this->getSitestoreproductsSelect($params, $customParams);
//    return $this->fetchAll($select);
//  }
    //GET SITESTOREPRODUCT SELECT QUERY
    public function getSitestoreproductsSelect($params = array(), $customParams = null) {

        //GET PRODUCT TABLE NAME
        $sitestoreproductTableName = $this->info('name');
        $tempSelect = array();
        global $sitestoreproductSelectQuery;

        //GET TAGMAP TABLE NAME
        $tagMapTableName = Engine_Api::_()->getDbtable('TagMaps', 'core')->info('name');

        //GET SEARCH TABLE
        $searchTable = Engine_Api::_()->fields()->getTable('sitestoreproduct_product', 'search')->info('name');

        //GET API
        $settings = Engine_Api::_()->getApi('settings', 'core');

        //GET LOCATION TABLE
        $locationTable = Engine_Api::_()->getDbtable('locations', 'sitestoreproduct');
        $locationTableName = $locationTable->info('name');


        $otherInfoTableName = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->info('name');

        $title_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('title');
        $body_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('body');
        if ($title_column != 'title') {
            $temp_colums_array = array('product_id', "$title_column", 'title', "$body_column", 'body', 'owner_id', 'store_id', 'profile_type', 'photo_id', 'section_id', 'category_id', 'subcategory_id', 'subsubcategory_id', 'creation_date', 'modified_date', 'start_date', 'end_date', 'end_date_enable', 'approved', 'featured', 'sponsored', 'newlabel', 'rating_avg', 'rating_editor', 'rating_users', 'price', 'review_count', 'view_count', 'comment_count', 'like_count', 'search', 'closed', 'approved_date', 'draft', 'main_video', 'video_snapshot_id', 'networks_privacy', 'product_code', 'product_type', 'weight', 'stock_unlimited', 'in_stock', 'min_order_quantity', 'max_order_quantity', 'user_tax', 'highlighted', 'allow_purchase', 'location');
            //$temp_colums_array = array('product_id',  "$title_column", 'title', "$body_column", 'body',   'owner_id', 'store_id', 'profile_type', 'photo_id', 'section_id', 'category_id',    'subcategory_id', 'subsubcategory_id', 'creation_date', 'modified_date', 'start_date',  'end_date', 'end_date_enable', 'approved',  'featured', 'sponsored',    'newlabel', 'rating_avg',   'rating_editor',    'rating_users', 'price',    'review_count', 'view_count',   'comment_count',    'like_count', 'search', 'closed',   'approved_date',    'draft',    'main_video',   'video_snapshot_id',    'networks_privacy', 'product_code', 'product_type', 'weight',   'stock_unlimited', 'in_stock', 'min_order_quantity',    'max_order_quantity',   'user_tax', 'highlighted',  'allow_purchase');
        } else {
            $temp_colums_array = array('product_id', 'title', 'body', 'owner_id', 'store_id', 'profile_type', 'photo_id', 'section_id', 'category_id', 'subcategory_id', 'subsubcategory_id', 'creation_date', 'modified_date', 'start_date', 'end_date', 'end_date_enable', 'approved', 'featured', 'sponsored', 'newlabel', 'rating_avg', 'rating_editor', 'rating_users', 'price', 'review_count', 'view_count', 'comment_count', 'like_count', 'search', 'closed', 'approved_date', 'draft', 'main_video', 'video_snapshot_id', 'networks_privacy', 'product_code', 'product_type', 'weight', 'stock_unlimited', 'in_stock', 'min_order_quantity', 'max_order_quantity', 'user_tax', 'highlighted', 'allow_purchase', 'location');
            //$temp_colums_array = array('product_id',  'title', 'body',    'owner_id', 'store_id', 'profile_type', 'photo_id', 'section_id', 'category_id',    'subcategory_id', 'subsubcategory_id', 'creation_date', 'modified_date', 'start_date',  'end_date', 'end_date_enable', 'approved',  'featured', 'sponsored',    'newlabel', 'rating_avg',   'rating_editor',    'rating_users', 'price',    'review_count', 'view_count',   'comment_count',    'like_count', 'search', 'closed',   'approved_date',    'draft',    'main_video',   'video_snapshot_id',    'networks_privacy', 'product_code', 'product_type', 'weight',   'stock_unlimited', 'in_stock', 'min_order_quantity',    'max_order_quantity',   'user_tax', 'highlighted',  'allow_purchase');
        }

        /* START - Fix search Issue */
        $optionSearchParams = $this->_getOptionSearchParams($customParams);
        /* END - Fix search Issue */

        //MAKE QUERY
        $select = $this->select();

        $select = $select
                ->setIntegrityCheck(false)
                ->from($sitestoreproductTableName, $temp_colums_array)
                ->group($sitestoreproductTableName . '.product_id');

        $select = $select->join($otherInfoTableName, "($sitestoreproductTableName.product_id = $otherInfoTableName.product_id)", array("discount", "discount_start_date", "discount_end_date", "discount_permanant", "user_type", "handling_type", "discount_value", "discount_amount", "product_info"));

        if (!empty($params['product_ids']) && isset($params['product_ids'])) {
            $idsStr = $params['product_ids'];
            if (is_array($params['product_ids']))
                $idsStr = @implode(",", $params['product_ids']);if($idsStr=='searchParams')$idsStr=0;

            $select->where("$sitestoreproductTableName.product_id IN (" . $idsStr . ")");
        }

        if (empty($params['is_owner']) && isset($params['is_widget']) && !empty($params['is_widget'])) {

            $select->where("$sitestoreproductTableName .start_date <= NOW()")
                    ->where("$sitestoreproductTableName.end_date_enable = 0 OR $sitestoreproductTableName.end_date > NOW()");

            if (!isset($params['in_stock'])) {
                $select->where("$sitestoreproductTableName.stock_unlimited = 1 OR $otherInfoTableName.out_of_stock = 1 OR $sitestoreproductTableName.min_order_quantity <= $sitestoreproductTableName.in_stock");
            }
        }

        if (empty($params['is_owner']) && isset($params['in_stock']) && !empty($params['in_stock'])) {
            $select->where("$sitestoreproductTableName.stock_unlimited = 1 OR $sitestoreproductTableName.min_order_quantity <= $sitestoreproductTableName.in_stock");
        }

//    if(empty($params['is_owner'])){
//            $select->where("$sitestoreproductTableName.stock_unlimited = 1 OR ($sitestoreproductTableName.min_order_quantity <= $sitestoreproductTableName.in_stock OR $otherInfoTableName.out_of_stock = 1)");
//        }

        if (isset($params['discount']) && !empty($params['discount'])) {
            $select->where("$otherInfoTableName.discount = 1");
            switch ($params['discount']) {
                case '0_10' : $select->where("$otherInfoTableName.discount_value BETWEEN 0 AND 10");
                    break;
                case '10_20' : $select->where("$otherInfoTableName.discount_value BETWEEN 10 AND 20");
                    break;
                case '20_30' : $select->where("$otherInfoTableName.discount_value BETWEEN 20 AND 30");
                    break;
                case '30_40' : $select->where("$otherInfoTableName.discount_value BETWEEN 30 AND 40");
                    break;
                case '40_50' : $select->where("$otherInfoTableName.discount_value BETWEEN 40 AND 50");
                    break;
                case '50_100' : $select->where("$otherInfoTableName.discount_value > 50");
                    break;
            }
        }

        $selectedProductTypes = isset($params['selected_product_types']) ? $params['selected_product_types'] : array();
        if (!empty($selectedProductTypes) && is_array($selectedProductTypes) && count($selectedProductTypes) > 0) {
            $flag_query = false;
            $temp_query = null;
            foreach ($selectedProductTypes as $productType) {
                if (empty($flag_query)) {
                    $temp_query = $sitestoreproductTableName . ".product_type LIKE ('%$productType%')";
                } else {
                    $temp_query = $temp_query . ' OR ' . $sitestoreproductTableName . ".product_type LIKE ('%$productType%')";
                }
                $flag_query = true;
            }
            $select->where($temp_query);
        }

        if (empty($params['is_owner']) && isset($params['type']) && !empty($params['type'])) {
            if ($params['type'] == 'browse' || $params['type'] == 'home') {
                $select = $select
                        ->where($sitestoreproductTableName . '.approved = ?', '1')
                        ->where($sitestoreproductTableName . '.draft = ?', '0');
                $showExpiry = (isset($params['show']) && $params['show'] == 'only_expiry') ? 1 : 0;
                $select = $this->expirySQL($select, $showExpiry);

                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0) && $params['type'] == 'browse' && isset($params['showClosed']) && !$params['showClosed']) {
                    $select = $select->where($sitestoreproductTableName . '.closed = ?', '0');
                }
            } elseif ($params['type'] == 'browse_home_zero') {
                $select = $select
                        ->where($sitestoreproductTableName . '.approved = ?', '1')
                        ->where($sitestoreproductTableName . '.draft = ?', '0');

                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
                    $select->where($sitestoreproductTableName . '.closed = ?', '0');
                }

                $select = $this->expirySQL($select);
            }
            if ($params['type'] != 'manage') {
                $select->where($sitestoreproductTableName . ".search = ?", 1);
            }
        }

        if (isset($params['store_id']) && !empty($params['store_id'])) {
            $select->where($sitestoreproductTableName . '.store_id =? ', $params['store_id']);
        }

        if (isset($params['product_type']) && !empty($params['product_type'])) {
            $select->where($sitestoreproductTableName . '.product_type =? ', $params['product_type']);
        }

        if (empty($params['is_owner']) && isset($customParams)) {

            /* :START - Fix search Issue */
            if (!empty($optionSearchParams)) {
                $combinAttrTb = "engine4_sitestoreproduct_combination_attributes";
                $cartFieldOptTb = "engine4_sitestoreproduct_cartproduct_fields_options";

                foreach ($optionSearchParams["labels"] as $optionLabel) {
                    $orOptionQuery[] = $cartFieldOptTb . ".label LIKE '" . $optionLabel . "'";
                }

                $select->setIntegrityCheck(false)
                        ->joinLeft($combinAttrTb, $combinAttrTb . ".product_id = " . $sitestoreproductTableName . ".product_id", null)
                        ->joinLeft($cartFieldOptTb, $combinAttrTb . ".combination_attribute_id = " . $cartFieldOptTb . ".option_id AND " . $cartFieldOptTb . ".field_id = " . $combinAttrTb . ".field_id", null);

                $select->where(implode(" OR ", $orOptionQuery))
                        ->having("COUNT(DISTINCT $cartFieldOptTb.option_id) >= " . count($orOptionQuery));
            }

            //PROCESS OPTIONS
//      $tmp = array();
//      foreach ($customParams as $k => $v) {
//        if (null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0)) {
//          continue;
//        } else if (false !== strpos($k, '_field_')) {
//          list($null, $field) = explode('_field_', $k);
//          $tmp['field_' . $field] = $v;
//        } else if (false !== strpos($k, '_alias_')) {
//          list($null, $alias) = explode('_alias_', $k);
//          $tmp[$alias] = $v;
//        } else {
//          $tmp[$k] = $v;
//        }
//      }
//      $customParams = $tmp;
//
//      $select = $select
//              ->setIntegrityCheck(false)
//              ->joinLeft($searchTable, "$searchTable.item_id = $sitestoreproductTableName.product_id", null);
//
//      $searchParts = Engine_Api::_()->fields()->getSearchQuery('sitestoreproduct_product', $customParams);
//      foreach ($searchParts as $k => $v) {
//        $select->where("`{$searchTable}`.{$k}", $v);
//      }

            /* :END - Fix search Issue */
        }

        if (isset($params['type']) && !empty($params['type']) && ($params['type'] == 'browse' || $params['type'] == 'home')) {
            //START NETWORK WORK
            $enableNetwork = $settings->getSetting('sitestoreproduct.network', 0);
            if (!empty($enableNetwork) || (isset($params['show']) && $params['show'] == "3")) {
                $select = $this->getNetworkBaseSql($select, array('browse_network' => 1));
            }
            //END NETWORK WORK
        }

        if (isset($params['section']) && !empty($params['section'])) {
            $select->where($sitestoreproductTableName . '.section_id = ?', $params['section']);
        }
        if (isset($params['downpayment']) && !empty($params['downpayment'])) {
            if ($params['downpayment'] == 1)
                $select->where($otherInfoTableName . '.downpayment_value != ?', 0);
            else if ($params['downpayment'] == 2)
                $select->where($otherInfoTableName . '.downpayment_value = ?', 0);
        }
        if (isset($params['category']) && !empty($params['category'])) {
            $select->where($sitestoreproductTableName . '.category_id = ?', $params['category']);
        }

        $api = Engine_Api::_()->sitestoreproduct();
        if (isset($params['price']['min']) && !empty($params['price']['min'])) {
            $select->where($sitestoreproductTableName . '.price >= ?', $api->getPriceWithCurrency($params['price']['min'], 1, 1));
        }

        if (isset($params['minPrice']) && !empty($params['minPrice'])) {
            $select->where($sitestoreproductTableName . '.price >= ?', $api->getPriceWithCurrency($params['minPrice'], 1, 1));
        }

        if (isset($params['price']['max']) && !empty($params['price']['max'])) {
            $select->where($sitestoreproductTableName . '.price <= ?', $api->getPriceWithCurrency($params['price']['max'], 1, 1));
        }

        if (isset($params['maxPrice']) && !empty($params['maxPrice'])) {
            $select->where($sitestoreproductTableName . '.price <= ?', $api->getPriceWithCurrency($params['maxPrice'], 1, 1));
        }

        if (empty($params['is_owner']) && !empty($params['user_id']) && is_numeric($params['user_id'])) {
            $select->where($sitestoreproductTableName . '.owner_id = ?', $params['user_id']);
        }

        if (empty($params['is_owner']) && !empty($params['user']) && $params['user'] instanceof User_Model_User) {
            $select->where($sitestoreproductTableName . '.owner_id = ?', $params['user_id']->getIdentity());
        }

        if (empty($params['is_owner']) && !empty($params['users'])) {
            $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
            $select->where($sitestoreproductTableName . '.owner_id in (?)', new Zend_Db_Expr($str));
        }

        if (empty($params['is_owner']) && empty($params['users']) && isset($params['show']) && $params['show'] == '2') {
            $select->where($sitestoreproductTableName . '.owner_id = ?', '0');
        }

        if (!empty($params['selling_price_count']) || !empty($params['selling_item_count'])) {
            $orderProductsTableName = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct')->info('name');
            $orderTableName = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->info('name');
            $select->setIntegrityCheck(false)
                    ->join($orderProductsTableName, "$orderProductsTableName.product_id = $sitestoreproductTableName.product_id", null)
                    ->joinInner($orderTableName, "$orderTableName.order_id = $orderProductsTableName.order_id", null)
                    ->group('product_id');
            if ($params['selling_price_count'] == 'selling_price_count') {
                $select->order("SUM($orderProductsTableName.price) DESC");
            }
            if ($params['selling_item_count'] == 'selling_item_count') {
                $select->order("SUM($orderProductsTableName.quantity) DESC");
            }
        }

        if (empty($params['is_owner']) && (isset($params['show']) && $params['show'] == "4")) {
            $likeTableName = Engine_Api::_()->getDbtable('likes', 'core')->info('name');
            $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            $select->setIntegrityCheck(false)
                    ->join($likeTableName, "$likeTableName.resource_id = $sitestoreproductTableName.product_id")
                    ->where($likeTableName . '.poster_type = ?', 'user')
                    ->where($likeTableName . '.poster_id = ?', $viewer_id)
                    ->where($likeTableName . '.resource_type = ?', 'sitestoreproduct_product');
        }

        //Location Work

        if (!isset($params['location']) && (isset($params['detactLocation']) && $params['detactLocation'] && isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['defaultLocationDistance']) && $params['defaultLocationDistance'])) {
            $radius = $params['defaultLocationDistance']; //in miles
            $latitude = $params['latitude'];
            $longitude = $params['longitude'];
//            $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.proximity.search.kilometer', 0);
//            if (!empty($flage)) {
//                $radius = $radius * (0.621371192);
//            }
            //$latitudeRadians = deg2rad($latitude);
            $latitudeSin = "sin(radians($latitude))";
            $latitudeCos = "cos(radians($latitude))";

            $select->join($locationTableName, "$sitestoreproductTableName.product_id = $locationTableName.product_id", array("(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172) AS distance", $locationTableName . '.location AS locationName'));
            $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
            $sqlstring .= ")";
            $select->where($sqlstring);

            if (isset($params['orderby']) && $params['orderby'] == "distance") {
                $select->order("distance");
            }

            $select->group("$sitestoreproductTableName.product_id");
        }

        if ((isset($params['location']) && !empty($params['location'])) || (!empty($params['Latitude']) && !empty($params['Longitude']))) {
            $enable = $settings->getSetting('sitestoreproduct.proximitysearch', 0);
            $longitude = 0;
            $latitude = 0;
            $detactLatLng = false;
            if (isset($params['location']) && $params['location']) {
                $cookieLocation = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
                $detactLatLng = isset($cookieLocation['location']) && $cookieLocation['location'] != $params['location'];
            }
            if ((isset($params['locationmiles']) && (!empty($params['locationmiles']) && !empty($enable))) || $detactLatLng) {

                if (!empty($params['location'])) {
                    $selectLocQuery = $locationTable->select()->where('location = ?', $params['location']);
                    $locationValue = $locationTable->fetchRow($selectLocQuery);
                }

                //check for zip code in location search.
                if ((empty($params['Latitude']) && empty($params['Longitude'])) || $detactLatLng) {
                    if (empty($locationValue)) {

                        $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $params['location'], 'module' => 'Stores / Marketplace - Ecommerce'));
                        if (!empty($locationResults['latitude']) && !empty($locationResults['longitude'])) {
                            $latitude = $locationResults['latitude'];
                            $longitude = $locationResults['longitude'];
                        }
                    } else {
                        $latitude = (float) $locationValue->latitude;
                        $longitude = (float) $locationValue->longitude;
                    }
                } else {
                    $latitude = (float) $params['Latitude'];
                    $longitude = (float) $params['Longitude'];
                }
                if ($latitude && $latitude && isset($params['location']) && $params['location']) {
                    $seaocore_myLocationDetails['latitude'] = $latitude;
                    $seaocore_myLocationDetails['longitude'] = $longitude;
                    $seaocore_myLocationDetails['location'] = $params['location'];
                    $seaocore_myLocationDetails['locationmiles'] = $params['locationmiles'];

                    Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($seaocore_myLocationDetails);
                }
            }

            if (isset($params['locationmiles']) && (!empty($params['locationmiles']) && !empty($enable)) && $latitude && $longitude) {
                $radius = $params['locationmiles'];

//                $flage = $settings->getSetting('siteevent.proximity.search.kilometer', 0);
//                if (!empty($flage)) {
//                    $radius = $radius * (0.621371192);
//                }
                //$latitudeRadians = deg2rad($latitude);
                $latitudeSin = "sin(radians($latitude))";
                $latitudeCos = "cos(radians($latitude))";
                $select->join($locationTableName, "$sitestoreproductTableName.product_id = $locationTableName.product_id", array("(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172) AS distance"));
                $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
                $sqlstring .= ")";
                $select->where($sqlstring);

                if (isset($params['orderby']) && $params['orderby'] == "distance") {
                    $select->order("distance");
                }
            } else {
                $select->join($locationTableName, "$sitestoreproductTableName.product_id = $locationTableName.product_id", null);
                $select->where("`{$locationTableName}`.formatted_address LIKE ? or `{$locationTableName}`.location LIKE ? or `{$locationTableName}`.city LIKE ? or `{$locationTableName}`.state LIKE ?", "%" . $params['location'] . "%");
            }
        }

        if (isset($params['sitetoreproduct_street']) && !empty($params['sitetoreproduct_street']) || isset($params['sitetoreproduct_city']) && !empty($params['sitetoreproduct_city']) || isset($params['sitetoreproduct_state']) && !empty($params['sitetoreproduct_state']) || isset($params['sitetoreproduct_country']) && !empty($params['sitetoreproduct_country'])) {
            $select->join($locationTableName, "$sitestoreproductTableName.product_id = $locationTableName.product_id   ", null);
        }


        if (isset($params['sitetoreproduct_street']) && !empty($params['sitetoreproduct_street'])) {
            $select->where($locationTableName . '.address   LIKE ? ', '%' . $params['sitetoreproduct_street'] . '%');
        } if (isset($params['sitetoreproduct_street']) && !empty($params['sitetoreproduct_street'])) {
            $select->where($locationTableName . '.city = ?', $params['sitetoreproduct_street']);
        } if (isset($params['sitetoreproduct_state']) && !empty($params['sitetoreproduct_state'])) {
            $select->where($locationTableName . '.state = ?', $params['sitetoreproduct_state']);
        } if (isset($params['sitetoreproduct_country']) && !empty($params['sitetoreproduct_country'])) {
            $select->where($locationTableName . '.country = ?', $params['sitetoreproduct_country']);
        }


        if ((isset($params['sitestoreproduct_location']) && !empty($params['sitestoreproduct_location'])) || (!empty($params['formatted_address']))) {
            $enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.proximitysearch', 1);
            if (isset($params['locationmiles']) && (!empty($params['locationmiles']) && !empty($enable))) {
                $longitude = 0;
                $latitude = 0;


                //check for zip code in location search.
                if (empty($params['Latitude']) && empty($params['Longitude'])) {
                    $selectLocQuery = $locationTable->select()->where('location = ?', $params['sitestoreproduct_location']);
                    $locationValue = $locationTable->fetchRow($selectLocQuery);

                    if (empty($locationValue)) {
                        $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $params['sitestoreproduct_location'], 'module' => 'Stores / Marketplace - Ecommerce'));
                        if (!empty($locationResults['latitude']) && !empty($locationResults['longitude'])) {
                            $latitude = $locationResults['latitude'];
                            $longitude = $locationResults['longitude'];
                        }
                    } else {
                        $latitude = (float) $locationValue->latitude;
                        $longitude = (float) $locationValue->longitude;
                    }
                } else {
                    $latitude = (float) $params['Latitude'];
                    $longitude = (float) $params['Longitude'];
                }

                $radius = $params['locationmiles']; //in miles
//              $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.proximity.search.kilometer', 0);
//              if (!empty($flage)) {
//              $radius = $radius * (0.621371192);
//              }
                //$latitudeRadians = deg2rad($latitude);
                $latitudeSin = "sin(radians($latitude))";
                $latitudeCos = "cos(radians($latitude))";

                $select->join($locationTableName, "$sitestoreproductTableName.product_id = $locationTableName.product_id", array("(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172) AS distance", $locationTableName . '.location AS locationName'));
                $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
                $sqlstring .= ")";
                $select->where($sqlstring);

                $select->order("distance");
            } else {
//              if ($params['sitestore_postalcode'] == 'postalCode') { 
//                  $select->join($locationName, "$rName.store_id = $locationName.store_id", null);
//                  $select->where("`{$locationName}`.formatted_address LIKE ? ", "%" . $params['formatted_address'] . "%");
//              } 
//              else {
                $select->join($locationTableName, "$sitestoreproductTableName.product_id = $locationTableName.product_id", null);
                $select->where("`{$locationTableName}`.formatted_address LIKE ? or `{$locationTableName}`.location LIKE ? or `{$locationTableName}`.city LIKE ? or `{$locationTableName}`.state LIKE ?", "%" . urldecode($params['sitestoreproduct_location']) . "%");
                //}
            }
        } elseif ((int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoregeolocation') && isset($params['has_currentlocation']) && !empty($params['has_currentlocation']) && !empty($params['latitude']) && !empty($params['longitude'])) {
            $radius = Engine_Api::_()->getApi('settings', 'core')->getSetting('sgl.geolocation.range', 100); // in miles
            $latitude = $params['latitude'];
            $longitude = $params['longitude'];
//      $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.proximity.search.kilometer', 0);
//      if (!empty($flage)) {
//        $radius = $radius * (0.621371192);
//      }
            //$latitudeRadians = deg2rad($latitude);
            $latitudeSin = "sin(radians($latitude))";
            $latitudeCos = "cos(radians($latitude))";
            $select->join($locationTableName, "$sitestoreproductTableName.product_id = $locationTableName.product_id   ", array("(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172) AS distance"));
            $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
            $sqlstring .= ")";
            $select->where($sqlstring);
            $select->order("distance");
        }


        if (!empty($params['tag_id'])) {
            $select
                    ->setIntegrityCheck(false)
                    ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $sitestoreproductTableName.product_id")
                    ->where($tagMapTableName . '.resource_type = ?', 'sitestoreproduct_product')
                    ->where($tagMapTableName . '.tag_id = ?', $params['tag_id']);
        }

        if (isset($params['category_id']) && !empty($params['category_id'])) {
            $select->where($sitestoreproductTableName . '.category_id = ?', $params['category_id']);
        }

        if (isset($params['subcategory_id']) && !empty($params['subcategory_id'])) {
            $select->where($sitestoreproductTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }

        if (isset($params['subsubcategory_id']) && !empty($params['subsubcategory_id'])) {
            $select->where($sitestoreproductTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
        }

        if (empty($params['is_owner']) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0) && isset($params['closed']) && $params['closed'] != "") {
            $select->where($sitestoreproductTableName . '.closed = ?', $params['closed']);
        }

        // Could we use the search indexer for this?
        if (!empty($params['search'])) {

            $tagName = Engine_Api::_()->getDbtable('Tags', 'core')->info('name');
            $select
                    ->setIntegrityCheck(false)
                    ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $sitestoreproductTableName.product_id and " . $tagMapTableName . ".resource_type = 'sitestoreproduct_product'")
                    ->joinLeft($tagName, "$tagName.tag_id = $tagMapTableName.tag_id");

            if ($title_column != 'title') {
                $select->where($sitestoreproductTableName . "." . $title_column . " LIKE ? OR " . $sitestoreproductTableName . ".title LIKE ? OR " . $sitestoreproductTableName . "." . $body_column . " LIKE ? OR " . $sitestoreproductTableName . ".body LIKE ? OR " . $tagName . ".text LIKE ? ", '%' . $params['search'] . '%');
            } else {
                $select->where($sitestoreproductTableName . "." . $title_column . " LIKE ? OR " . $sitestoreproductTableName . "." . $body_column . " LIKE ? OR " . $tagName . ".text LIKE ? ", '%' . $params['search'] . '%');
            }
        }

        if (empty($params['is_owner']) && !empty($params['start_date'])) {
            $select->where($sitestoreproductTableName . ".creation_date > ?", date('Y-m-d', $params['start_date']));
        }


        if (isset($params['temp_stock']) && !empty($params['temp_stock'])) {
            $select->where("$sitestoreproductTableName.stock_unlimited = 0 AND ($sitestoreproductTableName.in_stock = 0 OR $sitestoreproductTableName.min_order_quantity > $sitestoreproductTableName.in_stock)");
        }
        if (isset($params['temp_stock']) && empty($params['temp_stock'])) {
            $select->where("$sitestoreproductTableName.stock_unlimited = 1 OR $sitestoreproductTableName.min_order_quantity <= $sitestoreproductTableName.in_stock");
        }


//      if (isset($params['featured'])) {
//      $select->where($sitestoreproductTableName . ".featured = ?", $params['featured']);
//    }
//    
//       if (isset($params['sponsored'])) {
//      $select->where($sitestoreproductTableName . ".sponsored = ?", $params['sponsored']);
//    }
//    
//    if (isset($params['newlabel'])) {
//      $select->where($sitestoreproductTableName . ".newlabel = ?", $params['newlabel']);
//    }
//    
//     if (isset($params['status'])) {
//      $select->where($sitestoreproductTableName . ".closed = ?", $params['status']);
//    }


        if (empty($params['is_owner']) && !empty($params['end_date'])) {
            $select->where($sitestoreproductTableName . ".creation_date < ?", date('Y-m-d', $params['end_date']));
        }

        if (empty($params['is_owner']) && !empty($params['has_photo'])) {
            $select->where($sitestoreproductTableName . ".photo_id > ?", 0);
        }

        if (empty($params['is_owner']) && !empty($params['has_review'])) {
            $has_review = $params['has_review'];
            $select->where($sitestoreproductTableName . ".$has_review > ?", 0);
        }

        if (!empty($params['orderby']) && $params['orderby'] == "price_high_to_low") {
            $select->order($sitestoreproductTableName . '.price DESC');
        } else if (!empty($params['orderby']) && $params['orderby'] == "price_low_to_high") {
            $select->order($sitestoreproductTableName . '.price ASC');
        } else if (!empty($params['orderby']) && $params['orderby'] == "discount_amount") {
            $select->order($otherInfoTableName . '.discount_percentage DESC');
        } else if (!empty($params['orderby']) && $params['orderby'] == "title") {
            $select->order($sitestoreproductTableName . '.' . $params['orderby']);
        } else if (!empty($params['orderby']) && $params['orderby'] == "fespfe") {
            $select->order($sitestoreproductTableName . '.sponsored' . ' DESC')
                    ->order($sitestoreproductTableName . '.featured' . ' DESC');
        } else if (!empty($params['orderby']) && $params['orderby'] == "spfesp") {
            $select->order($sitestoreproductTableName . '.featured' . ' DESC')
                    ->order($sitestoreproductTableName . '.sponsored' . ' DESC');
        } else if (!empty($params['orderby']) && $params['orderby'] != 'product_id') {
            $select->order($sitestoreproductTableName . '.' . $params['orderby'] . ' DESC');
        }
        $select->order($sitestoreproductTableName . '.product_id DESC');
        $getResult = !empty($sitestoreproductSelectQuery) ? $select : $tempSelect;
        return $getResult;
    }

    /**
     * Get product count based on category
     *
     * @param int $id
     * @param string $column_name
     * @param int $authorization
     * @return product count
     */
    public function getProductsCount($id, $column_name, $foruser = null) {

        //MAKE QUERY
        $select = $this->select()
                ->from($this->info('name'), array('COUNT(*) AS count'));

        if (!empty($foruser)) {
            $select
                    ->where('approved = ?', 1)
                    ->where('draft = ?', 0)
                    ->where('search = ?', 1);

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
                $select->where('closed = ?', 0);
            }

            $select = $this->expirySQL($select);
            $select = $this->getNetworkBaseSql($select, array('not_groupBy' => 1));
        }

        if (!empty($column_name) && !empty($id)) {
            $select->where("$column_name = ?", $id);
        }
        $totalProducts = $select->query()->fetchColumn();

        //RETURN PRODUCTS COUNT
        return $totalProducts;
    }

    /**
     * Has Products 
     */
    public function hasProducts() {

        $select = $this->select()
                ->from($this->info('name'), 'product_id')
                ->where('approved = ?', 1)
                ->where('draft = ?', 0)
                ->where('search = ?', 1);

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
            $select->where('closed = ?', 0);
        }

        $select = $this->expirySQL($select);

        return $select->query()->fetchColumn();
    }

    /**
     * Get products based on category
     * @param string $title : search text
     * @param int $category_id : category id
     * @param char $popularity : result sorting based on views, reviews, likes, comments
     * @param char $interval : time interval
     * @param string $sqlTimeStr : Time durating string for where clause 
     */
    public function productsBySettings($params = array()) {

        $groupBy = 1;
        $productTableName = $this->info('name');

        $popularity = $params['popularity'];
        $interval = $params['interval'];

        //MAKE TIMING STRING
        $sqlTimeStr = '';
        $current_time = date("Y-m-d H:i:s");
        if ($interval == 'week') {
            $time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
            $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'";
        } elseif ($interval == 'month') {
            $time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
            $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
        }

        $otherInfoTableName = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->info('name');

        $title_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('title');
        $body_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('body');
        if ($title_column != 'title') {
            $temp_colums_array = array('product_id', "$title_column", 'title', 'price', 'photo_id', 'owner_id', 'category_id', 'view_count', 'review_count', 'like_count', 'comment_count', 'rating_avg', 'rating_editor', 'rating_users', 'sponsored', 'featured', 'newlabel', 'creation_date', "$body_column", 'body', 'product_type', 'in_stock', 'min_order_quantity', 'max_order_quantity', 'stock_unlimited', 'closed', 'draft', 'search', 'approved', 'start_date', 'end_date', 'end_date_enable', 'allow_purchase', 'store_id');
        } else {
            $temp_colums_array = array('product_id', 'title', 'price', 'photo_id', 'owner_id', 'category_id', 'view_count', 'review_count', 'like_count', 'comment_count', 'rating_avg', 'rating_editor', 'rating_users', 'sponsored', 'featured', 'newlabel', 'creation_date', 'body', 'product_type', 'in_stock', 'min_order_quantity', 'max_order_quantity', 'stock_unlimited', 'closed', 'draft', 'search', 'approved', 'start_date', 'end_date', 'end_date_enable', 'allow_purchase', 'store_id');
        }

        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($productTableName, $temp_colums_array)
                ->joinLeft($otherInfoTableName, "($otherInfoTableName.product_id = $productTableName.product_id)", array('discount', 'discount_value', 'discount_start_date', 'discount_end_date', 'discount_permanant', 'handling_type', 'user_type', 'discount_amount', 'product_info'));

        if ($interval != 'overall' && $popularity == 'review_count') {

            $popularityTable = Engine_Api::_()->getDbtable('reviews', 'sitestoreproduct');
            $popularityTableName = $popularityTable->info('name');
            $select = $select->joinLeft($popularityTableName, "($popularityTableName.resource_id = $productTableName.product_id and $reviewTableName .resource_type ='sitestoreproduct_product')", array("COUNT(review_id) as total_count"))
                    ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
                    ->order("total_count DESC");
        } elseif ($interval != 'overall' && ($popularity == 'rating_avg' || $popularity == 'rating_editor' || $popularity == 'rating_users')) {

            if ($interval == 'week') {
                $time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
                $sqlTimeStr = ".modified_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'";
            } elseif ($interval == 'month') {
                $time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
                $sqlTimeStr = ".modified_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
            }

            $ratingTable = Engine_Api::_()->getDbTable('ratings', 'sitestoreproduct');
            $ratingTableName = $ratingTable->info('name');

            $popularityTable = Engine_Api::_()->getDbtable('reviews', 'sitestoreproduct');
            $popularityTableName = $popularityTable->info('name');
            $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $productTableName . '.product_id', array(""))
                    ->join($ratingTableName, $ratingTableName . '.review_id = ' . $popularityTableName . '.review_id')
                    ->where($popularityTableName . '.resource_type = ?', 'sitestoreproduct_product')
                    ->where($ratingTableName . '.ratingparam_id = ?', 0);

            if ($popularity == 'rating_editor') {
                $select->where("$popularityTableName.type = ?", 'editor');
            } elseif ($popularity == 'rating_users') {
                $select->where("$popularityTableName.type = ?", 'user')
                        ->orWhere("$popularityTableName.type = ?", 'visitor');
            }

            $select->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.modified_date is null');
            $select->order("$productTableName.$popularity DESC");
        } elseif ($interval != 'overall' && $popularity == 'like_count') {

            $popularityTable = Engine_Api::_()->getDbtable('likes', 'core');
            $popularityTableName = $popularityTable->info('name');

            $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $productTableName . '.product_id', array("COUNT(like_id) as total_count"))
                    ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
                    ->order("total_count DESC");
        } elseif ($interval != 'overall' && $popularity == 'comment_count') {

            $popularityTable = Engine_Api::_()->getDbtable('comments', 'core');
            $popularityTableName = $popularityTable->info('name');

            $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $productTableName . '.product_id', array("COUNT(comment_id) as total_count"))
                    ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
                    ->order("total_count DESC");
        } elseif ($popularity == 'most_discussed') {

            $popularityTable = Engine_Api::_()->getDbtable('posts', 'sitestoreproduct');
            $popularityTableName = $popularityTable->info('name');
            $select = $select->joinLeft($popularityTableName, $popularityTableName . '.product_id = ' . $productTableName . '.product_id', array("COUNT(post_id) as total_count"))
                    ->order("total_count DESC");

            if ($interval != 'overall') {
                $select->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null');
            }
        } elseif ($popularity == 'view_count' || $popularity == 'product_id' || $popularity == 'modified_date' || $popularity == 'creation_date') {
            $select->order("$productTableName.$popularity DESC");
        } elseif ($interval == 'overall' && ($popularity == 'review_count' || $popularity == 'like_count' || $popularity == 'comment_count' || $popularity == 'rating_avg' || $popularity == 'rating_editor' || $popularity == 'rating_users')) {
            $select->order("$productTableName.$popularity DESC");
        }

        $select = $this->expirySQL($select);
        $select->group($productTableName . '.product_id');
        $select
                ->where($productTableName . '.approved = ?', '1')
                ->where($productTableName . '.search = ?', '1')
                ->where($productTableName . '.draft = ?', '0')
                ->where("$productTableName .start_date <= NOW()")
                ->where("$productTableName.end_date_enable = 0 OR $productTableName.end_date > NOW()")
                ->where("$productTableName.stock_unlimited = 1 OR $otherInfoTableName.out_of_stock = 1 OR $productTableName.min_order_quantity <= $productTableName.in_stock");

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
            $select->where($productTableName . '.closed = ?', '0');
        }

        if (isset($params['featured']) && !empty($params['featured'])) {
            $select->where('featured = ?', 1);
        }

        if (isset($params['sponsored']) && !empty($params['sponsored'])) {
            $select->where('sponsored = ?', 1);
        }

        if (isset($params['newlabel']) && !empty($params['newlabel'])) {
            $select->where('newlabel = ?', 1);
        }

        if (isset($params['category_id']) && !empty($params['category_id'])) {
            $select->where($productTableName . '.category_id = ?', $params['category_id']);
        }

        if (isset($params['subcategory_id']) && !empty($params['subcategory_id'])) {
            $select->where($productTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }

        if (isset($params['subsubcategory_id']) && !empty($params['subsubcategory_id'])) {
            $select->where($productTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
        }

        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            if (isset($params['popularity']) && !empty($params['popularity']) && $params['popularity'] != 'product_id' && $params['popularity'] != 'creation_date' && $params['popularity'] != 'random') {
                $select->order($productTableName . ".product_id DESC");
            }
        } else {
            if (isset($params['popularity']) && !empty($params['popularity']) && $params['popularity'] != 'product_id' && $params['popularity'] != 'creation_date' && $params['popularity'] != 'random' && $params['popularity'] != 'top_selling') {
                $select->order($productTableName . ".product_id DESC");
            }
        }

        if (isset($params['popularity']) && $params['popularity'] == 'discount_amount') {
            $select->order($otherInfoTableName . '.discount_amount DESC');
        }

        if (isset($params['popularity']) && $params['popularity'] == 'random') {
            $select->order('RAND() DESC ');
        }

        //SITEMOBILE TOP SELLING WORK 
        $sitestoreproductTableName = $this->info('name');

        if (isset($params['popularity']) && $params['popularity'] == 'top_selling' && !Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $orderProductTableName = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct')->info('name');
            $select->join($orderProductTableName, "($sitestoreproductTableName.product_id = $orderProductTableName.product_id)", array("SUM($orderProductTableName.quantity) as maxProductQuantity"))
                    ->order("maxProductQuantity DESC");
        }

        //Start Network work
        $select = $this->getNetworkBaseSql($select, array('not_groupBy' => $groupBy));
        //End Network work
        if (isset($params['paginator']) && !empty($params['paginator'])) {
            return $paginator = Zend_Paginator::factory($select);
        }
        if (isset($params['limit']) && !empty($params['limit'])) {
            $select->limit($params['limit']);
        }


        return $this->fetchAll($select);
    }

    /**
     * Get pages to add as item of the day
     * @param string $title : search text
     * @param int $limit : result limit
     */
    public function getDayItems($title, $limit = 10) {

        //PRODUCT TABLE NAME
        $sitestoreproductTableName = $this->info('name');

        //GET TAGS
        $tagMapTableName = Engine_Api::_()->getDbtable('TagMaps', 'core')->info('name');
        $tagName = Engine_Api::_()->getDbtable('Tags', 'core')->info('name');

        $title_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('title');
        $body_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('body');
        if ($title_column != 'title') {
            $temp_colums_array = array('product_id', 'owner_id', "$title_column", 'title', 'photo_id', 'product_type', 'in_stock', 'allow_purchase', 'store_id');
        } else {
            $temp_colums_array = array('product_id', 'owner_id', 'title', 'photo_id', 'product_type', 'in_stock', 'allow_purchase', 'store_id');
        }
        //MAKE QUERY
        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($sitestoreproductTableName, $temp_colums_array)
                ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $sitestoreproductTableName.product_id and " . $tagMapTableName . ".resource_type = 'sitestoreproduct_product'")
                ->joinLeft($tagName, "$tagName.tag_id = $tagMapTableName.tag_id")
                ->where($sitestoreproductTableName . "." . $title_column . " LIKE ? OR " . $sitestoreproductTableName . "." . $body_column . " LIKE ? OR " . $tagName . ".text LIKE ? ", '%' . $title . '%')
                ->where($sitestoreproductTableName . '.approved = ?', '1')
                ->where($sitestoreproductTableName . '.draft = ?', '0')
                ->where($sitestoreproductTableName . '.search = ?', '1')
                ->order($sitestoreproductTableName . '.title ASC')
                ->limit($limit);

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
            $select->where($sitestoreproductTableName . '.closed = ?', 0);
        }

        $select = $this->expirySQL($select);

        //RETURN RESULTS
        return $this->fetchAll($select);
    }

    /**
     * Return product data
     *
     * @param array params
     * @return Zend_Db_Table_Select
     */
    public function widgetProductsData($params = array()) {

        //GET TABLE NAME
        $tableProductName = $this->info('name');

        $otherInfoTableName = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->info('name');

        $title_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('title');
        $body_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('body');
        if ($title_column != 'title') {
            $temp_colums_array = array('product_id', "$title_column", 'title', 'price', 'photo_id', 'owner_id', 'category_id', 'subcategory_id', 'subsubcategory_id', 'view_count', 'review_count', 'like_count', 'comment_count', 'rating_avg', 'rating_editor', 'rating_users', 'sponsored', 'featured', 'newlabel', 'creation_date', "$body_column", 'body', 'product_type', 'in_stock', 'min_order_quantity', 'max_order_quantity', 'stock_unlimited', 'closed', 'draft', 'search', 'approved', 'start_date', 'end_date', 'end_date_enable', 'store_id', 'allow_purchase');
        } else {
            $temp_colums_array = array('product_id', 'title', 'price', 'photo_id', 'owner_id', 'category_id', 'subcategory_id', 'subsubcategory_id', 'view_count', 'review_count', 'like_count', 'comment_count', 'rating_avg', 'rating_editor', 'rating_users', 'sponsored', 'featured', 'newlabel', 'creation_date', 'body', 'product_type', 'in_stock', 'min_order_quantity', 'max_order_quantity', 'stock_unlimited', 'closed', 'draft', 'search', 'approved', 'start_date', 'end_date', 'end_date_enable', 'store_id', 'allow_purchase');
        }

        //MAKE QUERY
        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($tableProductName, $temp_colums_array)
                ->joinLeft($otherInfoTableName, "($otherInfoTableName.product_id = $tableProductName.product_id)", array('discount', 'discount_value', 'discount_start_date', 'discount_end_date', 'discount_permanant', 'handling_type', 'user_type', 'discount_amount', 'product_info'));

        //SELECT ONLY AUTHENTICATE PRODUCTS
        $select = $select->where('approved = ?', 1)
                ->where('draft = ?', 0)
                ->where('search = ?', 1)
                ->where("$tableProductName .start_date <= NOW()")
                ->where("$tableProductName.end_date_enable = 0 OR $tableProductName.end_date > NOW()")
                ->where("$tableProductName.stock_unlimited = 1 OR $otherInfoTableName.out_of_stock = 1 OR $tableProductName.min_order_quantity <= $tableProductName.in_stock");

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
            $select->where('closed = ?', 0);
        }

        $select = $this->expirySQL($select);

        if (isset($params['zero_count']) && !empty($params['zero_count'])) {
            $select->where($params['zero_count'] . ' != ?', 0);
        }

        if (isset($params['owner_id']) && !empty($params['owner_id'])) {
            $select->where('owner_id = ?', $params['owner_id']);
        }

        if (isset($params['product_id']) && !empty($params['product_id'])) {
            $select->where($tableProductName . '.product_id != ?', $params['product_id']);
        }

        if (isset($params['featured']) && !empty($params['featured'])) {
            $select->where('featured = ?', 1);
        }

        if ((isset($params['category_id']) && !empty($params['category_id']))) {
            $select->where('category_id = ?', $params['category_id']);
        }

        if (isset($params['tags']) && !empty($params['tags'])) {

            //GET TAG MAPS TABLE NAME
            $tableTagmapsName = Engine_Api::_()->getDbtable('TagMaps', 'core')->info('name');

            $select//->setIntegrityCheck(false)
                    ->joinLeft($tableTagmapsName, "$tableTagmapsName.resource_id = $tableProductName.product_id")
                    ->where($tableTagmapsName . '.resource_type = ?', 'sitestoreproduct_product');

            foreach ($params['tags'] as $tag_id) {
                $tagSqlArray[] = "$tableTagmapsName.tag_id = $tag_id";
            }
            $select->where("(" . join(") or (", $tagSqlArray) . ")");
        }

        if (isset($params['orderby']) && !empty($params['orderby'])) {
            $select->order($params['orderby']);
        }

        $select->order('product_id DESC');

        if (isset($params['limit']) && !empty($params['limit'])) {
            $select->limit($params['limit']);
        }
        //Start Network work
        $select = $this->getNetworkBaseSql($select, array('not_groupBy' => 1));
        //End Network work
        $select->group('product_id');

        return $this->fetchAll($select);
    }

    public function getMappedSitestoreproduct($category_id) {

        //RETURN IF CATEGORY ID IS NULL
        if (empty($category_id)) {
            return null;
        }

        //MAKE QUERY
        $select = $this->select()
                ->from($this->info('name'), 'product_id')
                ->where("category_id = $category_id OR subcategory_id = $category_id OR subsubcategory_id = $category_id");

        //GET DATA
        $categoryData = $this->fetchAll($select);

        if (!empty($categoryData)) {
            return $categoryData->toArray();
        }

        return null;
    }

    public function getNetworkBaseSql($select, $params = array()) {

        if (empty($select))
            return;

        //GET SITESTOREPRODUCT TABLE NAME
        $sitestoreproductTableName = $this->info('name');

        //START NETWORK WORK
        $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.network', 0);
        if (!empty($enableNetwork) || (isset($params['browse_network']) && !empty($params['browse_network']))) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
            if (!Zend_Registry::isRegistered('viewerNetworksIdsSR')) {
                $viewerNetworkIds = $networkMembershipTable->getMembershipsOfIds($viewer);
                Zend_Registry::set('viewerNetworksIdsSR', $viewerNetworkIds);
            } else {
                $viewerNetworkIds = Zend_Registry::get('viewerNetworksIdsSR');
            }

            if (!Engine_Api::_()->sitestoreproduct()->listBaseNetworkEnable()) {
                if (!empty($viewerNetworkIds)) {
                    if (isset($params['setIntegrity']) && !empty($params['setIntegrity'])) {
                        $select->setIntegrityCheck(false)
                                ->from($sitestoreproductTableName);
                    }
                    $networkMembershipName = $networkMembershipTable->info('name');
                    $select
                            ->join($networkMembershipName, "`{$sitestoreproductTableName}`.owner_id = `{$networkMembershipName}`.user_id  ", null)
                            ->where("`{$networkMembershipName}`.`resource_id`  IN (?) ", (array) $viewerNetworkIds);
                    if (!isset($params['not_groupBy']) || empty($params['not_groupBy'])) {
                        $select->group($sitestoreproductTableName . ".product_id");
                    }
                }
            } else {
                // $viewerNetwork = $networkMembershipTable->getMembershipsOfInfo($viewer);

                $str = array();
                $columnName = "`{$sitestoreproductTableName}`.networks_privacy";
                foreach ($viewerNetworkIds as $networkId) {
                    $str[] = '\'%"' . $networkId . '"%\'';
                }
                if (!empty($str)) {
                    $likeNetworkVale = (string) ( join(" or $columnName  LIKE ", $str) );
                    $select->where($columnName . ' LIKE ' . $likeNetworkVale . ' or ' . $columnName . " IS NULL");
                } else {
                    $select->where($columnName . " IS NULL");
                }
            }
        }
        //END NETWORK WORK
        //RETURN QUERY
        return $select;
    }

    public function recentlyViewed($params = array()) {

        //GET VIEWER ID
        $viewer_id = $params['viewer_id'];

        //GET VIEWER TABLE
        $viewTable = Engine_Api::_()->getDbtable('vieweds', 'sitestoreproduct');
        $viewTableName = $viewTable->info('name');

        //GET SITESTOREPRODUCT TABLE NAME
        $sitestoreproductTableName = $this->info('name');
        $otherInfoTableName = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->info('name');

        // QUERY TO FETCH THE RESULTS ACCRODING TO THE MAX DATE
        $subQuery = $viewTable->select()
                ->from($viewTableName, array('product_id', 'max(date) as date'))
                ->group($viewTableName . '.product_id');


        $subSubQuery = $viewTable->select()
                ->from(array('t1' => $viewTableName), array('product_id', 'date', 'viewer_id'))
                ->joinInner(array('t2' => $subQuery), 't1.product_id = t2.product_id and t1.date = t2.date', array(''))
                ->group('t1.product_id');

        $title_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('title');
        $body_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('body');
        if ($title_column != 'title') {
            $temp_colums_array = array('product_id', "$title_column", 'title', 'price', 'photo_id', 'owner_id', 'category_id', 'view_count', 'review_count', 'like_count', 'comment_count', 'rating_avg', 'rating_editor', 'rating_users', 'sponsored', 'featured', 'newlabel', 'creation_date', "$body_column", 'body', 'product_type', 'in_stock', 'min_order_quantity', 'max_order_quantity', 'stock_unlimited', 'closed', 'draft', 'search', 'approved', 'start_date', 'end_date', 'end_date_enable', 'allow_purchase', 'store_id');
        } else {
            $temp_colums_array = array('product_id', 'title', 'price', 'photo_id', 'owner_id', 'category_id', 'view_count', 'review_count', 'like_count', 'comment_count', 'rating_avg', 'rating_editor', 'rating_users', 'sponsored', 'featured', 'newlabel', 'creation_date', 'body', 'product_type', 'in_stock', 'min_order_quantity', 'max_order_quantity', 'stock_unlimited', 'closed', 'draft', 'search', 'approved', 'start_date', 'end_date', 'end_date_enable', 'allow_purchase', 'store_id');
        }

        //MAKE QUERY
        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($sitestoreproductTableName, $temp_colums_array)
                ->joinLeft($otherInfoTableName, "($otherInfoTableName.product_id = $sitestoreproductTableName.product_id)", array('discount', 'discount_value', 'discount_start_date', 'discount_end_date', 'discount_permanant', 'handling_type', 'user_type', 'discount_amount', 'product_info'))
                ->joinInner(array($viewTableName => $subSubQuery), "$sitestoreproductTableName . product_id = $viewTableName . product_id", array('viewer_id'))
                ->group($viewTableName . '.product_id');

        $select = $this->getNetworkBaseSql($select);
        if ($params['show'] == 1) {

            //GET MEMBERSHIP TABLE
            $membership_table = Engine_Api::_()->getDbtable('membership', 'user');
            $ids = $membership_table->getMembershipsOfIds(Engine_Api::_()->user()->getViewer());
            if (empty($ids))
                $ids[] = -1;
            $select->where($viewTableName . '.viewer_id  In(?)', (array) $ids);
//      $select->joinInner($member_name, "$member_name . user_id = $viewTableName . viewer_id", NULL)
//              ->where($member_name . '.resource_id = ?', $viewer_id)
//              ->where($viewTableName . '.viewer_id <> ?', $viewer_id)
//              ->where($member_name . '.active = ?', 1);
        } else {
            $select->where($viewTableName . '.viewer_id = ?', $viewer_id);
        }

        $select->order('date DESC');

        if (isset($params['featured']) && !empty($params['featured'])) {
            $select->where($sitestoreproductTableName . '.featured = ?', 1);
        }

        if (isset($params['sponsored']) && !empty($params['sponsored'])) {
            $select->where($sitestoreproductTableName . '.sponsored = ?', 1);
        }

        if (isset($params['newlabel']) && !empty($params['newlabel'])) {
            $select->where($sitestoreproductTableName . '.newlabel = ?', 1);
        }

        $select->where($sitestoreproductTableName . '.approved = ?', '1')
                ->where($sitestoreproductTableName . '.draft = ?', '0')
                ->where($sitestoreproductTableName . ".search = ?", '1')
                ->where("$sitestoreproductTableName .start_date <= NOW()")
                ->where("$sitestoreproductTableName.end_date_enable = 0 OR $sitestoreproductTableName.end_date > NOW()")
                ->where("$sitestoreproductTableName.stock_unlimited = 1 OR $otherInfoTableName.out_of_stock = 1 OR $sitestoreproductTableName.min_order_quantity <= $sitestoreproductTableName.in_stock");

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
            $select->where($sitestoreproductTableName . '.closed = ?', 0);
        }

        $select = $this->expirySQL($select);

        if (isset($params['paginator']) && !empty($params['paginator'])) {
            return $paginator = Zend_Paginator::factory($select);
        }

        if (isset($params['limit']) && !empty($params['limit'])) {
            $select->limit($params['limit']);
        }

        //RETURN RESULTS
        return $this->fetchAll($select);
    }

    // get lising according to requerment
    public function getProduct($sitestoreproducttype = '', $params = array()) {

        $limit = 10;
        $table = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
        $sitestoreproductTableName = $table->info('name');
        $otherInfoTableName = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->info('name');
        $coreTable = Engine_Api::_()->getDbtable('likes', 'core');
        $coreName = $coreTable->info('name');
        $select = $table->select()
                ->setIntegrityCheck(false)
                ->from($sitestoreproductTableName)
                ->join($otherInfoTableName, "($sitestoreproductTableName.product_id = $otherInfoTableName.product_id)")
                ->where($sitestoreproductTableName . '.approved = ?', '1')
                ->where($sitestoreproductTableName . '.draft = ?', '0')
                ->where($sitestoreproductTableName . ".search = ?", 1)
                ->where("$sitestoreproductTableName .start_date <= NOW()")
                ->where("$sitestoreproductTableName.end_date_enable = 0 OR $sitestoreproductTableName.end_date > NOW()")
                ->where("$sitestoreproductTableName.stock_unlimited = 1 OR $otherInfoTableName.out_of_stock = 1 OR $sitestoreproductTableName.min_order_quantity <= $sitestoreproductTableName.in_stock");

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
            $select->where($sitestoreproductTableName . '.closed = ?', 0);
        }

        $select = $this->expirySQL($select);

        if (isset($params['product_id']) && !empty($params['product_id'])) {
            $select->where($sitestoreproductTableName . '.product_id != ?', $params['product_id']);
        }

        if (isset($params['category_id']) && !empty($params['category_id'])) {
            $select->where($sitestoreproductTableName . '.category_id = ?', $params['category_id']);
        }

        if (isset($params['subcategory_id']) && !empty($params['subcategory_id'])) {
            $select->where($sitestoreproductTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }

        if (isset($params['subsubcategory_id']) && !empty($params['subsubcategory_id'])) {
            $select->where($sitestoreproductTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
        }

        if (isset($params['popularity']) && !empty($params['popularity'])) {
            $select->order($sitestoreproductTableName . '.' . $params['popularity'] . " DESC");
        }

        if (isset($params['popularity']) && $params['popularity'] == 'discount_amount') {
            $select->order($otherInfoTableName . '.discount_amount DESC');
        }

        if (isset($params['featured']) && !empty($params['featured']) || $sitestoreproducttype == 'featured') {
            $select->where("$sitestoreproductTableName.featured = ?", 1);
        }

        if (isset($params['sponsored']) && !empty($params['sponsored']) || $sitestoreproducttype == 'sponsored') {
            $select = $select->where($sitestoreproductTableName . '.sponsored = ?', '1');
        }

        if (isset($params['newlabel']) && !empty($params['newlabel']) || $sitestoreproducttype == 'newlabel') {
            $select = $select->where($sitestoreproductTableName . '.newlabel = ?', '1');
        }

        if (isset($params['new_arrival']) && !empty($params['new_arrival']) || $sitestoreproducttype == 'new_arrival') {
            $select = $select->where($sitestoreproductTableName . '.newlabel = ?', '1');
        }

        if (isset($params['new_arrivals']) && !empty($params['new_arrivals']) || $sitestoreproducttype == 'new_arrivals') {
            $select = $select->where($sitestoreproductTableName . '.newlabel = ?', '1');
        }

        if (isset($params['top_selling']) && !empty($params['top_selling']) || $sitestoreproducttype == 'top_selling') {
            $orderProductTableName = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct')->info('name');
            $select->join($orderProductTableName, "($sitestoreproductTableName.product_id = $orderProductTableName.product_id)", array("SUM($orderProductTableName.quantity) as maxProductQuantity"))
                    ->order("maxProductQuantity DESC");
        }

        if (isset($params['similarItems']) && !empty($params['similarItems'])) {
            $select->where("$sitestoreproductTableName.product_id IN (?)", (array) $params['similarItems']);
        }

        //Start Network work
        $select = $table->getNetworkBaseSql($select, array('setIntegrity' => 1));
        //End Network work

        if ($sitestoreproducttype == 'most_popular') {
            $select = $select->order($sitestoreproductTableName . '.view_count DESC');
        }

        if ($sitestoreproducttype == 'most_reviews' || $sitestoreproducttype == 'most_reviewed') {
            $select = $select->order($sitestoreproductTableName . '.review_count DESC');
        }

        if (isset($params['similar_items_order']) && !empty($params['similar_items_order'])) {
            if (isset($params['ratingType']) && !empty($params['ratingType']) && $params['ratingType'] != 'rating_both') {
                $ratingType = $params['ratingType'];
                $select->order($sitestoreproductTableName . ".$ratingType DESC");
            } else {
                $select->order($sitestoreproductTableName . '.rating_avg DESC');
            }
            $select->order('RAND()');
        } else {
            $select->order($sitestoreproductTableName . '.product_id DESC');
        }

        if (isset($params['limit']) && !empty($params['limit'])) {
            $limit = $params['limit'];
        }
        
        if (!isset($params['location']) && (isset($params['detactLocation']) && $params['detactLocation'] && isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['defaultLocationDistance']) && $params['defaultLocationDistance'])) {
            
            $locationTable = Engine_Api::_()->getDbtable('locations', 'sitestoreproduct');
            $locationTableName = $locationTable->info('name');
            $radius = $params['defaultLocationDistance']; //in miles
            $latitude = $params['latitude'];
            $longitude = $params['longitude'];
//            $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.proximity.search.kilometer', 0);
//            if (!empty($flage)) {
//                $radius = $radius * (0.621371192);
//            }
            //$latitudeRadians = deg2rad($latitude);
            $latitudeSin = "sin(radians($latitude))";
            $latitudeCos = "cos(radians($latitude))";

            $select->join($locationTableName, "$sitestoreproductTableName.product_id = $locationTableName.product_id", array("(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172) AS distance", $locationTableName . '.location AS locationName'));
            $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
            $sqlstring .= ")";
            $select->where($sqlstring);

            if (isset($params['orderby']) && $params['orderby'] == "distance") {
                $select->order("distance");
            }

           // $select->group("$sitestoreproductTableName.product_id");
        }

        $select->group($sitestoreproductTableName . '.product_id');

        if (isset($params['start_index']) && $params['start_index'] >= 0) {
            $select = $select->limit($limit, $params['start_index']);
            return $table->fetchAll($select);
        } else {

            $paginator = Zend_Paginator::factory($select);
            if (!empty($params['page'])) {
                $paginator->setCurrentPageNumber($params['page']);
            }

            if (!empty($params['limit'])) {
                $paginator->setItemCountPerPage($limit);
            }

            return $paginator;
        }
    }

    //GET DISCUSSED PRODUCTS
    public function getDiscussedProduct($params = array()) {

        //GET SITESTOREPRODUCT TABLE NAME
        $sitestoreproductTableName = $this->info('name');

        //GET TOPIC TABLE
        $topictable = Engine_Api::_()->getDbTable('topics', 'sitestoreproduct');
        $topic_tableName = $topictable->info('name');

        $otherInfoTableName = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->info('name');

        $title_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('title');
        if ($title_column != 'title') {
            $temp_colums_array = array('product_id', "$title_column", 'title', 'price', 'photo_id', 'owner_id', 'category_id', 'subcategory_id', 'subsubcategory_id', 'rating_avg', 'rating_editor', 'rating_users', 'product_type', 'in_stock', 'min_order_quantity', 'max_order_quantity', 'stock_unlimited', 'closed', 'draft', 'search', 'approved', 'start_date', 'end_date', 'end_date_enable', 'allow_purchase', 'store_id');
        } else {
            $temp_colums_array = array('product_id', 'title', 'price', 'photo_id', 'owner_id', 'category_id', 'subcategory_id', 'subsubcategory_id', 'rating_avg', 'rating_editor', 'rating_users', 'product_type', 'in_stock', 'min_order_quantity', 'max_order_quantity', 'stock_unlimited', 'closed', 'draft', 'search', 'approved', 'start_date', 'end_date', 'end_date_enable', 'allow_purchase', 'store_id');
        }


        //MAKE QUERY

        $select = $this->select()->setIntegrityCheck(false)
                ->from($sitestoreproductTableName, $temp_colums_array)
                ->joinLeft($otherInfoTableName, "($otherInfoTableName.product_id = $sitestoreproductTableName.product_id)", array('out_of_stock', 'discount', 'discount_value', 'discount_start_date', 'discount_end_date', 'discount_permanant', 'handling_type', 'user_type', 'discount_amount', 'product_info'))
                ->join($topic_tableName, $topic_tableName . '.product_id = ' . $sitestoreproductTableName . '.product_id', array('count(*) as counttopics', '(sum(post_count) - count(*) ) as total_count'))
                ->where($sitestoreproductTableName . '.approved = ?', '1')
                ->where($sitestoreproductTableName . '.draft = ?', '0')
                ->where($sitestoreproductTableName . ".search = ?", 1)
                ->where($topic_tableName . '.post_count > ?', '1')
                ->where("$sitestoreproductTableName .start_date <= NOW()")
                ->where("$sitestoreproductTableName.end_date_enable = 0 OR $sitestoreproductTableName.end_date > NOW()")
                ->where("$sitestoreproductTableName.stock_unlimited = 1 OR $otherInfoTableName.out_of_stock = 1 OR $sitestoreproductTableName.min_order_quantity <= $sitestoreproductTableName.in_stock")
                ->group($topic_tableName . '.product_id')
                ->order('total_count DESC')
                ->order('counttopics DESC')
                ->limit($params['limit']);

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
            $select->where($sitestoreproductTableName . '.closed = ?', 0);
        }

        $select = $this->expirySQL($select);

        if (isset($params['featured']) && !empty($params['featured'])) {
            $select->where($sitestoreproductTableName . '.featured = ?', 1);
        }

        if (isset($params['category_id']) && (!empty($params['category_id']) && $params['category_id'] != -1)) {
            $select->where($sitestoreproductTableName . '.category_id = ?', $params['category_id']);
        }

        if (isset($params['subcategory_id']) && (!empty($params['subcategory_id']) && $params['subcategory_id'] != -1)) {
            $select->where($sitestoreproductTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }

        if (isset($params['subsubcategory_id']) && (!empty($params['subsubcategory_id']) && $params['subsubcategory_id'] != -1)) {
            $select->where($sitestoreproductTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
        }

        //START NETWORK WORK
        $select = $this->getNetworkBaseSql($select);
        //END NETWORK WORK
        //FETCH RESULTS
        return $this->fetchAll($select);
    }

    // get sitestoreproduct sitestoreproduct relative to sitestoreproduct owner
    public function userProduct($owner_id, $product_id, $limit = 3) {

        //GET SITESTOREPRODUCT TABLE NAME
        $sitestoreproductTableName = $this->info('name');

        //MAKE QUERY

        $otherInfoTableName = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->info('name');

        $title_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('title');
        $body_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('body');
        if ($title_column != 'title') {
            $temp_colums_array = array('product_id', "$title_column", 'title', 'price', 'photo_id', 'owner_id', 'category_id', 'subcategory_id', 'subsubcategory_id', 'view_count', 'review_count', 'like_count', 'comment_count', 'rating_avg', 'rating_editor', 'rating_users', 'sponsored', 'featured', 'newlabel', 'creation_date', "$body_column", 'body', 'product_type', 'in_stock', 'min_order_quantity', 'max_order_quantity', 'stock_unlimited', 'closed', 'draft', 'search', 'approved', 'start_date', 'end_date', 'end_date_enable', 'allow_purchase', 'store_id');
        } else {
            $temp_colums_array = array('product_id', 'title', 'price', 'photo_id', 'owner_id', 'category_id', 'subcategory_id', 'subsubcategory_id', 'view_count', 'review_count', 'like_count', 'comment_count', 'rating_avg', 'rating_editor', 'rating_users', 'sponsored', 'featured', 'newlabel', 'creation_date', 'body', 'product_type', 'in_stock', 'min_order_quantity', 'max_order_quantity', 'stock_unlimited', 'closed', 'draft', 'search', 'approved', 'start_date', 'end_date', 'end_date_enable', 'allow_purchase', 'store_id');
        }

        //MAKE QUERY
        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($sitestoreproductTableName, $temp_colums_array)
                ->joinLeft($otherInfoTableName, "($otherInfoTableName.product_id = $sitestoreproductTableName.product_id)", array('out_of_stock', 'discount', 'discount_value', 'discount_start_date', 'discount_end_date', 'discount_permanant', 'handling_type', 'user_type', 'discount_amount', 'product_info'))
                ->where($sitestoreproductTableName . '.approved = ?', '1')
                ->where($sitestoreproductTableName . '.draft = ?', '0')
                ->where($sitestoreproductTableName . ".search = ?", 1)
                ->where($sitestoreproductTableName . '.product_id <> ?', $product_id)
                ->where($sitestoreproductTableName . '.owner_id = ?', $owner_id)
                ->where("$sitestoreproductTableName .start_date <= NOW()")
                ->where("$sitestoreproductTableName.end_date_enable = 0 OR $sitestoreproductTableName.end_date > NOW()")
                ->where("$sitestoreproductTableName.stock_unlimited = 1 OR $otherInfoTableName.out_of_stock = 1 OR $sitestoreproductTableName.min_order_quantity <= $sitestoreproductTableName.in_stock")
                ->limit($limit);

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
            $select->where($sitestoreproductTableName . '.closed = ?', 0);
        }

        $select = $this->expirySQL($select);
        //Start Network work
        $select = $this->getNetworkBaseSql($select, array('setIntegrity' => 1));
        //End Network work
        //RETURN RESULTS
        return $this->fetchAll($select);
    }

    /**
     * Handle archive sitestoreproduct
     * @param array $results : document owner archive sitestoreproduct array
     * @return sitestoreproduct with detail.
     */
    public function getArchiveSitestoreproduct($spec) {
        if (!($spec instanceof User_Model_User)) {
            return null;
        }

        $localeObject = Zend_Registry::get('Locale');
        if (!$localeObject) {
            $localeObject = Zend_Registry::get('Locale');
        }

        $dates = $this->select()
                ->from($this->info('name'), 'creation_date')
                ->where('owner_id = ?', $spec->getIdentity())
                ->where('approved = ?', '1')
                ->where('draft = ?', '0')
                ->where("search = ?", 1)
                ->order('product_id DESC');

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
            $dates->where('closed = ?', 0);
        }

        $select = $this->expirySQL($dates);

        $dates = $dates
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);

        $time = time();

        $archive_sitestoreproduct = array();
        foreach ($dates as $date) {

            $date = strtotime($date);
            $ltime = localtime($date, true);
            $ltime["tm_mon"] = $ltime["tm_mon"] + 1;
            $ltime["tm_year"] = $ltime["tm_year"] + 1900;

            // LESS THAN A YEAR AGO - MONTHS
            if ($date + 31536000 > $time) {
                $date_start = mktime(0, 0, 0, $ltime["tm_mon"], 1, $ltime["tm_year"]);
                $date_end = mktime(0, 0, 0, $ltime["tm_mon"] + 1, 1, $ltime["tm_year"]);
                $type = 'month';

                $dateObject = new Zend_Date($date);
                $format = $localeObject->getTranslation('yMMMM', 'dateitem', $localeObject);
                $label = $dateObject->toString($format, $localeObject);
            }
            // MORE THAN A YEAR AGO - YEARS
            else {
                $date_start = mktime(0, 0, 0, 1, 1, $ltime["tm_year"]);
                $date_end = mktime(0, 0, 0, 1, 1, $ltime["tm_year"] + 1);
                $type = 'year';

                $dateObject = new Zend_Date($date);
                $format = $localeObject->getTranslation('yyyy', 'dateitem', $localeObject);
                if (!$format) {
                    $format = $localeObject->getTranslation('y', 'dateitem', $localeObject);
                }
                $label = $dateObject->toString($format, $localeObject);
            }

            if (!isset($archive_sitestoreproduct[$date_start])) {
                $archive_sitestoreproduct[$date_start] = array(
                    'type' => $type,
                    'label' => $label,
                    'date' => $date,
                    'date_start' => $date_start,
                    'date_end' => $date_end,
                    'count' => 1
                );
            } else {
                $archive_sitestoreproduct[$date_start]['count'] ++;
            }
        }

        return $archive_sitestoreproduct;
    }

    /**
     * Get products 
     *
     * @param int $id
     * @param string $column_name
     * @param int $authorization
     * @return product count
     */
    public function getProducts($params = array()) {

        //MAKE QUERY
        $select = $this->select()
                ->where('approved = ?', 1)
                ->where('draft = ?', 0)
                ->where('search = ?', 1);

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
            $select->where('closed = ?', 0);
        }

        $select = $this->expirySQL($select);
        if (isset($params['list_ids']) && !empty($params['list_ids'])) {
            $select->where('product_id  IN(?)', (array) $params['list_ids']);
        }
        if (isset($params['category_id']) && !empty($params['category_id'])) {
            $select->where('category_id  = ?', $params['category_id']);
        }

        return $this->fetchAll($select);
    }

    public function getSimilarItems($params = NULL) {

        $sitestoreproductTableName = $this->info('name');


        $title_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('title');
        $body_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('body');
        if ($title_column != 'title') {
            $temp_colums_array = array('product_id', 'photo_id', "$title_column", 'title');
        } else {
            $temp_colums_array = array('product_id', 'photo_id', 'title');
        }




        $select = $this->select()
                ->from($sitestoreproductTableName, $temp_colums_array);

        if (isset($params['textSearch']) && !empty($params['textSearch'])) {
            $select->where($sitestoreproductTableName . "." . $title_column . " LIKE ? ", "%" . $params['textSearch'] . "%");
        }

        if (isset($params['product_id']) && !empty($params['product_id'])) {
            $select->where($sitestoreproductTableName . '.product_id != ?', $params['product_id']);
        }

        if (isset($params['category_id']) && !empty($params['category_id']) && $params['category_id'] != -1) {
            $select->where($sitestoreproductTableName . '.category_id = ?', $params['category_id']);
        }

        if (isset($params['subcategory_id']) && !empty($params['subcategory_id']) && $params['subcategory_id'] != -1) {
            $select->where($sitestoreproductTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }

        if (isset($params['limit']) && !empty($params['limit'])) {
            $select->limit($params['limit']);
        }

        if (isset($params['productIds']) && !empty($params['productIds'])) {
            $productIds = join(',', $params['productIds']);
            $select->where("product_id IN ($productIds)");
        }

        if (isset($params['notProductIds']) && !empty($params['notProductIds'])) {
            $notProductIds = join(',', $params['notProductIds']);
            $select->where("product_id NOT IN ($notProductIds)");
        }

        return Zend_Paginator::factory($select);
    }

    /**
     * Return products which have this category and this mapping
     *
     * @param int category_id
     * @return Zend_Db_Table_Select
     */
    public function getCategoryList($category_id, $categoryType) {

        //RETURN IF CATEGORY ID IS NULL
        if (empty($category_id)) {
            return null;
        }

        //MAKE QUERY
        $select = $this->select()
                ->from($this->info('name'), 'product_id')
                ->where("$categoryType = ?", $category_id);

        //GET DATA
        return $this->fetchAll($select);
    }

    /**
     * Return top reviewers
     *
     * @param Array $params
     * @return top reviewers
     */
    public function topPosters($params = array()) {

        //GET USER TABLE INFO
        $tableUser = Engine_Api::_()->getDbtable('users', 'user');
        $tableUserName = $tableUser->info('name');

        //GET REVIEW TABLE NAME
        $sitestoreproductTableName = $this->info('name');

        //MAKE TIMING STRING
        $sqlTimeStr = '';
        $current_time = date("Y-m-d H:i:s");
        if ($params['interval'] == 'week') {
            $time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
            $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'";
        } elseif ($params['interval'] == 'month') {
            $time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
            $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
        }

        //MAKE QUERY
        $select = $tableUser->select()
                ->setIntegrityCheck(false)
                ->from($tableUserName, array('user_id', 'displayname', 'username', 'photo_id'))
                ->join($sitestoreproductTableName, "$tableUserName.user_id = $sitestoreproductTableName.owner_id", array('COUNT(engine4_sitestoreproduct_products.product_id) AS product_count'));

        if ($params['interval'] != 'overall') {
            $select->where($sitestoreproductTableName . "$sqlTimeStr  or " . $sitestoreproductTableName . '.creation_date is null');
        }

        $select = $select->where($sitestoreproductTableName . '.draft = ?', 0)
                ->where($sitestoreproductTableName . '.search = ?', 1)
                ->where($sitestoreproductTableName . '.approved = ?', 1)
                ->group($tableUserName . ".user_id")
                ->order('product_count DESC')
                ->order('user_id DESC')
                ->limit($params['limit']);

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
            $select->where($sitestoreproductTableName . '.closed = ?', 0);
        }

        $select = $this->expirySQL($select);

        //RETURN THE RESULTS
        return $tableUser->fetchAll($select);
    }

    /**
     * Gets a object for supplied parameter
     *
     * @param string containing the products id
     * @return object
     */
    public function getProductObject($param) {
        $select = $this->select()
                ->where("product_id IN ($param)");

        return $this->fetchAll($select);
    }

    /**
     * Return logged out viewer cart
     *
     * @param $product_ids
     * @param $flag
     * @return object
     */
    public function getLoggedOutViewerCartDetail($product_ids, $flag = false, $params = array()) {
        $otherinfoTableName = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->info('name');
        $productTableName = $this->info('name');

        $title_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('title');
        $body_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('body');
        if ($title_column != 'title') {
            $temp_colums_array = array("store_id", "product_id", "$title_column", "title", "photo_id", "price", "in_stock", "user_tax", "weight", "product_type", "min_order_quantity", "max_order_quantity", "stock_unlimited", "closed", "draft", "end_date", "end_date_enable", "approved", "start_date", "search", 'allow_purchase');
        } else {
            $temp_colums_array = array("store_id", "product_id", "title", "photo_id", "price", "in_stock", "user_tax", "weight", "product_type", "min_order_quantity", "max_order_quantity", "stock_unlimited", "closed", "draft", "end_date", "end_date_enable", "approved", "start_date", "search", 'allow_purchase');
        }


        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($productTableName, $temp_colums_array)
                ->join($otherinfoTableName, "($productTableName.product_id = $otherinfoTableName.product_id)", array("discount", "discount_start_date", "discount_end_date", "handling_type", "discount_value", "discount_amount", "user_type", "discount_permanant", "product_info"))
                ->where("$productTableName.product_id IN ($product_ids)")
        ;

        if (isset($params['store_id']) && !empty($params['store_id']))
            $select->where("$productTableName.store_id =?", $params['store_id']);

        if (empty($flag)) {
            return $select->order("$productTableName.store_id DESC")->query()->fetchAll();
        } else {
            return $this->fetchAll($select);
        }
    }

    /**
     * Return product attribute
     *
     * @param array $fetch_column_array
     * @param $params
     * @param $temp_cart_flag
     * @return string
     */
    public function getProductAttribute($fetch_column_array, $params, $temp_cart_flag = false) {
        $productTableName = $this->info('name');
        $select = $this->select()
                ->from($this->info('name'), $fetch_column_array);

        if (!empty($temp_cart_flag)) {
            $select->where($params);
        } else {
            if (!empty($params['product_id'])) {
                $select->where($productTableName . ".product_id = ?", $params['product_id']);
            }
        }

        return $select;
    }

    /**
     * Return products
     *
     * @param array $params
     * @return object
     */
    public function getSitestoreproductProducts($params) {
        $productTableName = $this->info('name');
        $orderProductTableName = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct')->info('name');
        $orderTableName = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->info('name');
        $otherInfoTableName = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->info('name');

        $interval = $params['interval'];

        //MAKE TIMING STRING
        $sqlTimeStr = '';
        $current_time = date("Y-m-d H:i:s");
        if ($interval == 'week') {
            $time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
            $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'";
        } elseif ($interval == 'month') {
            $time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
            $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
        }

        $title_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('title');
        $body_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('body');
        if ($title_column != 'title') {
            $temp_colums_array = array('product_id', "$title_column", 'title', 'price', 'photo_id', 'owner_id', 'category_id', 'view_count', 'review_count', 'like_count', 'comment_count', 'rating_avg', 'rating_editor', 'rating_users', 'sponsored', 'featured', 'newlabel', 'creation_date', "$body_column", 'body', 'product_type', 'in_stock', 'min_order_quantity', 'max_order_quantity', 'stock_unlimited', 'closed', 'draft', 'search', 'approved', 'start_date', 'end_date', 'end_date_enable', 'allow_purchase', 'store_id');
        } else {
            $temp_colums_array = array('product_id', 'title', 'price', 'photo_id', 'owner_id', 'category_id', 'view_count', 'review_count', 'like_count', 'comment_count', 'rating_avg', 'rating_editor', 'rating_users', 'sponsored', 'featured', 'newlabel', 'creation_date', 'body', 'product_type', 'in_stock', 'min_order_quantity', 'max_order_quantity', 'stock_unlimited', 'closed', 'draft', 'search', 'approved', 'start_date', 'end_date', 'end_date_enable', 'allow_purchase', 'store_id');
        }



        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($productTableName, $temp_colums_array)
                ->joinLeft($otherInfoTableName, "($otherInfoTableName.product_id = $productTableName.product_id)", array('discount', 'discount_value', 'discount_start_date', 'discount_end_date', 'discount_permanant', 'handling_type', 'user_type', 'discount_amount', 'product_info'));

        $select = $this->getNetworkBaseSql($select);
        if (isset($params['popularity']) && !empty($params['popularity']) && $params['popularity'] == 'top_selling') {
            $select->joinInner($orderProductTableName, "$productTableName.product_id = $orderProductTableName.product_id", array("SUM($orderProductTableName.quantity) as quantity"));
        } else if (isset($params['popularity']) && !empty($params['popularity']) && ($params['popularity'] == 'last_order_all' || $params['popularity'] == 'last_order_viewer')) {
            $select->joinInner($orderProductTableName, "$orderProductTableName.product_id = $productTableName.product_id", array("$orderProductTableName.quantity"));
        }

        $select->joinInner($orderTableName, "$orderTableName.order_id = $orderProductTableName.order_id", array(""))
                ->where("order_status > 1 AND payment_status = 'active'");

        if ($interval != 'overall') {
            $select->where($orderTableName . "$sqlTimeStr  or " . $orderTableName . '.creation_date is null');
        }

        if (isset($params['product_type']) && !empty($params['product_type']) && $params['product_type'] != 'all') {
            $select->where($productTableName . '.product_type = ?', $params['product_type']);
        }

        if (isset($params['product_id']) && !empty($params['product_id'])) {
            $select->where($productTableName . '.product_id != ?', $params['product_id']);
        }

        if (!empty($params['store_id'])) {
            $select->where($productTableName . '.store_id = ?', $params['store_id']);
        }

        if (isset($params['category_id']) && !empty($params['category_id']) && $params['category_id'] != -1) {
            $select->where($productTableName . '.category_id = ?', $params['category_id']);
        }

        if (isset($params['subcategory_id']) && !empty($params['subcategory_id']) && $params['subcategory_id'] != -1) {
            $select->where($productTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }

        if (isset($params['subsubcategory_id']) && !empty($params['subsubcategory_id']) && $params['subsubcategory_id'] != -1) {
            $select->where($productTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
        }

        if (isset($params['popularity']) && !empty($params['popularity']) && $params['popularity'] == 'top_selling') {
            $select->order("SUM($orderProductTableName.quantity) DESC")
                    ->group('product_id');
        } else if (isset($params['popularity']) && !empty($params['popularity']) && $params['popularity'] == 'last_order_viewer') {
            $select->where("$orderTableName.buyer_id =?", Engine_Api::_()->user()->getViewer()->getIdentity())
                    ->order("$orderTableName.creation_date DESC")->order("$orderTableName.order_id DESC");
        } else if (isset($params['popularity']) && !empty($params['popularity']) && $params['popularity'] == 'last_order_all') {
            $select->order("$orderTableName.creation_date DESC")->order("$orderTableName.order_id DESC");
        }
        $select
                ->where($productTableName . '.approved = ?', '1')
                ->where($productTableName . '.draft = ?', '0')
                ->where($productTableName . ".search = ?", 1)
                ->where("$productTableName .start_date <= NOW()")
                ->where("$productTableName.end_date_enable = 0 OR $productTableName.end_date > NOW()")
                ->where("$productTableName.stock_unlimited = 1 OR $otherInfoTableName.out_of_stock = 1 OR $productTableName.min_order_quantity <= $productTableName.in_stock");

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
            $select->where($productTableName . '.closed = ?', 0);
        }

        $select->group("$productTableName.product_id");

        if (isset($params['limit']) && !empty($params['limit'])) {
            $select->limit($params['limit']);
        }

        return $this->fetchAll($select);
    }

    /**
     * Get viewer carts
     *
     * @param $cart_id
     * @return array
     */
    public function getCart($cart_id) {
        $productTableName = $this->info('name');
        $cartProductTable = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct');
        $cartProductTableName = $cartProductTable->info('name');

        $title_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('title');

        if ($title_column != 'title') {
            $temp_colums_array = array("$productTableName.store_id", "$productTableName.$title_column", "$productTableName.title", "$productTableName.price", "$productTableName.photo_id", "$productTableName.product_type");
        } else {
            $temp_colums_array = array("$productTableName.store_id", "$productTableName.title", "$productTableName.price", "$productTableName.photo_id", "$productTableName.product_type");
        }



        $select = $this->select()
                ->from($productTableName, $temp_colums_array)
                ->setIntegrityCheck(false)
                ->join($cartProductTableName, "($cartProductTableName.product_id = $productTableName.product_id)", array("$cartProductTableName.quantity", "$cartProductTableName.product_id", "$cartProductTableName.cartproduct_id"))
                ->where("$cartProductTableName.cart_id = ?", $cart_id)
                ->order("$productTableName.store_id DESC")
                ->order("$cartProductTableName.cartproduct_id DESC");

        return $this->fetchAll($select);
    }

    /**
     * Return products by text
     *
     * @param $store_ids
     * @param $text
     * @param $limit
     * @param $product_ids
     * @param $viewer_id
     * @param $productCreateFlag
     * @return int
     */
    public function getProductsByText($store_ids = null, $text = null, $limit = 20, $product_ids = null, $viewer_id = null, $productCreateFlag = null, $selectedProductTypes = null) {
        $productTableName = $this->info('name');
        $title_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('title');

        if ($title_column != 'title') {
            $temp_colums_array = array("$productTableName.store_id", "$productTableName.$title_column", "$productTableName.title", "$productTableName.price", "$productTableName.photo_id", "$productTableName.product_type");
        } else {
            $temp_colums_array = array("$productTableName.store_id", "$productTableName.title", "$productTableName.price", "$productTableName.photo_id", "$productTableName.product_type");
        }


        $select = $this->select()
                ->order($title_column . ' ASC')
                ->limit($limit);
        if (!empty($store_ids))
            $select->where("store_id IN ($store_ids)");

        if (!empty($product_ids))
            $select->where("product_id NOT IN ($product_ids)");

        if (!empty($text))
            $select->where($title_column . ' LIKE ?', '%' . $text . '%');

        if (!empty($productCreateFlag)) {
            if (!empty($selectedProductTypes))
                $select->where("product_type IN ($selectedProductTypes)");
            else
                $select->where("product_type IN ('simple')");

            $select->where('approved = ?', '1')
                    ->where('search = ?', '1')
                    ->where('draft = ?', '0')
                    ->where("start_date <= NOW()")
                    ->where("end_date_enable = 0 OR end_date > NOW()")
                    ->where("stock_unlimited = 1 OR min_order_quantity <= in_stock");

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
                $select->where('closed = ?', 0);
            }
        } else
        if (!empty($viewer_id))
            $select->where('owner_id = ?', $viewer_id);

        return $this->fetchAll($select);
    }

    /**
     * Return products availability to create group or bundled products
     *
     * @param $store_id
     * @param $product_type
     * @return int
     */
    public function getProductAvailability($store_id, $product_type) {

        if ($product_type == 'grouped') {
            $producctTypeCondition = "product_type LIKE 'simple'";
        } elseif ($product_type == 'bundled') {
            $producctTypeCondition = "product_type LIKE 'simple' OR product_type LIKE 'configurable' OR product_type LIKE 'virtual' OR product_type LIKE 'downloadable'";
        }

        $select = $this->select()
                ->from($this->info('name'), array("COUNT(product_id) as product_count"))
                ->where('store_id = ?', $store_id)
                ->where('approved = ?', '1')
                ->where('draft = ?', '0')
                ->where("start_date <= NOW()")
                ->where("end_date_enable = 0 OR end_date > NOW()")
                ->where("stock_unlimited = 1 OR min_order_quantity <= in_stock");

        if (!empty($producctTypeCondition)) {
            $select->where($producctTypeCondition);
        }

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
            $select->where('closed = ?', 0);
        }
        return $select->limit(2)->query()->fetchColumn();
    }

    /**
     * Return combined products
     *
     * @param array $params
     * @return object
     */
    public function getCombinedProducts($params) {

        $otherInfoTable = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct');
        $otherInfoTableName = $otherInfoTable->info('name');
        $productsTableName = $this->info('name');

        $encodedProductIds = $otherInfoTable->select()
                ->from($otherInfoTableName, array('mapped_ids'))
                ->where('product_id = ?', $params['product_id'])
                ->query()
                ->fetch();

        $mapped_ids = Zend_Json_Decoder::decode($encodedProductIds['mapped_ids']);
        $product_ids = implode(',', $mapped_ids);

        if (isset($params['getMappedIds']) && !empty($params['getMappedIds'])) {
            return $mapped_ids;
        }

        $select = $this->select()->from($productsTableName);

        if (isset($params['product_type']) && $params['product_type'] == 'grouped') {
            $select->where($productsTableName . '.closed = ?', '0')
                    ->where($productsTableName . '.approved = ?', '1')
                    ->where($productsTableName . '.draft = ?', '0')
                    ->where($productsTableName . ".search = ?", 1)
                    ->where("$productsTableName .start_date <= NOW()")
                    ->where("$productsTableName.end_date_enable = 0 OR $productsTableName.end_date > NOW()")
                    ->where("$productsTableName.stock_unlimited = 1 OR $productsTableName.min_order_quantity <= $productsTableName.in_stock");

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
                $select->where($productsTableName . '.closed = ?', 0);
            }
        }

        $select->where("$productsTableName.product_id IN ($product_ids)");

        return $this->fetchAll($select);
    }

    /**
     * Return product price after discount
     *
     * @param $product_id
     * @return object
     */
    public function getProductDiscountedPrice($product_id) {
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $productsTableName = $this->info('name');
        $otherInfoTableName = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->info('name');

        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($productsTableName, array("price"))
                ->join($otherInfoTableName, "($productsTableName.product_id = $otherInfoTableName.product_id)", array("discount", "discount_start_date", "discount_end_date", "discount_permanant", "user_type", "discount_amount"))
                ->where("$productsTableName.product_id =?", $product_id);
        $cart_product = $this->fetchRow($select)->toArray();

        $is_discount = false;

        // CHECK DISCOUNT CONDITION
        if (!empty($cart_product['price']) &&
                !empty($cart_product['discount']) &&
                (@strtotime($cart_product['discount_start_date']) <= @time()) &&
                (!empty($cart_product['discount_permanant']) || (@time() < @strtotime($cart_product['discount_end_date']))) &&
                ( empty($cart_product['user_type']) ||
                ($cart_product['user_type'] == 1 && empty($viewer_id)) ||
                ($cart_product['user_type'] == 2 && !empty($viewer_id))
                )
        ) {
            $is_discount = true;
            $discounted_price = @round($cart_product['price'], 2) - @round($cart_product['discount_amount'], 2);
        }
        if (empty($is_discount)) {
            $discounted_price = @round($cart_product['price'], 2);
        }

        return $discounted_price;
    }

    /**
     * Return products according to tax
     *
     * @param $tax_id
     * @return object
     */
    public function getProductsByTax($tax_id) {
        $select = $this->select()
                ->from($this->info('name'), array("product_id", "user_tax"))
                ->where("user_tax LIKE ? ", "%\"$tax_id\"%");
        return $this->fetchAll($select);
    }

    /**
     * Return item of the day
     *
     * @param $product_id
     * @return object
     */
    public function getItemOfTheDay($product_id) {
        $productsTableName = $this->info('name');
        $otherInfoTableName = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->info('name');

        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($productsTableName)
                ->join($otherInfoTableName, "($productsTableName.product_id = $otherInfoTableName.product_id)", array("discount", "discount_start_date", "discount_end_date", "discount_permanant", "user_type", "handling_type", "discount_value", "discount_amount", "product_info"))
                ->where("$productsTableName.product_id =?", $product_id)
                ->where($productsTableName . '.closed = ?', '0')
                ->where($productsTableName . '.approved = ?', '1')
                ->where($productsTableName . '.draft = ?', '0')
                ->where($productsTableName . ".search = ?", 1)
                ->where("$productsTableName .start_date <= NOW()")
                ->where("$productsTableName.end_date_enable = 0 OR $productsTableName.end_date > NOW()")
                ->where("$productsTableName.stock_unlimited = 1 OR $productsTableName.min_order_quantity <= $productsTableName.in_stock");

        return $this->fetchRow($select);
    }

    /**
     * Return no of products in a store
     *
     * @param $store_id
     * @param $verified
     * @return int
     */
    public function getProductsCountInStore($store_id, $verified = 0) {

        //MAKE QUERY
        $select = $this->select()
                ->from($this->info('name'), array('COUNT(*) AS count'));

        if (!empty($verified)) {
            $select->where('approved = ?', 1)
                    ->where('draft = ?', 0)
                    ->where('search = ?', 1);

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
                $select->where('closed = ?', 0);
            }

            $select = $this->expirySQL($select);
            $select = $this->getNetworkBaseSql($select, array('not_groupBy' => 1));
        }

        $select->where("store_id = ?", $store_id);

        return $select->query()->fetchColumn();
    }

    /**
     * Return product title
     *
     * @param $product_id
     * @return string
     */
    public function getProductTitle($product_id) {
        return $this->select()->from($this->info('name'), 'title')->where('product_id = ?', $product_id)->query()->fetchColumn();
    }

    public function isProductExist($product_id) {
        return $this->select()->from($this->info('name'), 'product_id')->where('product_id = ?', $product_id)->query()->fetchColumn();
    }

    public function getProductsBySection($section_id, $store_id) {
        $productTableName = $this->info('name');
        $select = $this->select();

        if (empty($section_id)) {
            $sectionTableName = Engine_Api::_()->getDbtable('sections', 'sitestoreproduct')->info('name');
            $select->setIntegrityCheck(false)
                    ->from($productTableName)
                    ->joinLeft($sectionTableName, "($sectionTableName.section_id = $productTableName.section_id)", array('section_name'))
                    ->where($productTableName . '.store_id = ?', $store_id);
        } else {
            $select->where('section_id = ?', $section_id)
                    ->where('store_id = ?', $store_id);
        }
        return $this->fetchAll($select);
    }

    public function getProductsByStore($store_id) {
        $productTableName = $this->info('name');
        $select = $this->select();
        $select->where('store_id = ?', $store_id);
        return $this->fetchAll($select);
    }

//   public function exportProduct($store_id, $params = array()) {
//     
//     
//     $params['selected_product_types'] = array('simple', 'configurable', 'virtual', 'downloadable');
//     
//    //GET SITESTOREPRODUCT TABLE NAME
//    $sitestoreproductTableName = $this->info('name');
//    //MAKE QUERY
//    $select = $this->select()
//            ->where('approved = ?', '1')
//            ->where('draft = ?', '0')
//            ->where("search = ?", 1)
//            ->where('store_id = ?', $store_id)
//            ->where("start_date <= NOW()")
//            ->where("end_date_enable = 0 OR end_date > NOW()")
//            ->where("product_type NOT LIKE '%bundled%'")
//            ->where("product_type NOT LIKE '%grouped%'");    
//      
//    if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
//      $select->where($sitestoreproductTableName . '.closed = ?', 0);
//    }
//
//    return $this->fetchAll($select);
//  }
//   public function productIdByTagId($tag_id, $store_id){
//      $productTableName = $this->info('name');
//      $tagTableName = Engine_Api::_()->getDbtable('tagmappings', 'sitestoreproduct')->info('name'); 
//      $select = $this->select()->setIntegrityCheck(false);
//      $select->from($productTableName)
//              ->join($tagTableName, "$productTableName.`product_id` = ".$tagTableName.".`product_id`", null)
//              ->where($tagTableName.'.store_id =?', $store_id)
//              ->where($tagTableName.'.printingtag_id =?', $tag_id);
//      return $this->fetchAll($select);
//    }
//    public function isPrintingTagAllowed($product_id){
//      $getProductId = $this->select()
//              ->from($this->info('name'), array("product_id"))
//              ->where("product_type NOT LIKE '%virtual%'")
//              ->where("product_type NOT LIKE '%downloadable%'")
//              ->where('product_id = ?', $product_id)
//              ->query()->fetchColumn();
//
//      $getProductId = !empty($getProductId)? $getProductId: 0;
//      return $getProductId;
//    }

    /**
     * Create new columns in Sitestoreproduct_products and otherinfo table for language support
     *
     * @param array $columns
     */
    public function createColumns($columns = array()) {

        //RETURN IF COLUMNS ARRAY IS EMPTY
        if (empty($columns)) {
            return;
        }

        foreach ($columns as $key => $label) {

            if ($label == 'en') {
                continue;
            }

            $title_column = "'title_$label'";
            $body_column = "'body_$label'";
            $overview_column = "'overview_$label'";

            $create_title_column = "`title_$label`";
            $create_body_column = "`body_$label`";
            $create_overview_column = "`overview_$label`";

            $db = Engine_Db_Table::getDefaultAdapter();

            //CHECK COLUMNS ARE ALREADY EXISTS
            $title_column_exist = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_products LIKE $title_column")->fetch();
            $body_column_exist = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_products LIKE $body_column")->fetch();
            $overview_column_exist = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_otherinfo LIKE $overview_column")->fetch();

            //CREATE COLUMNS IF NOT EXISTS
            if (empty($title_column_exist) && empty($body_column_exist) && empty($overview_column_exist)) {
                $db->query("ALTER TABLE `engine4_sitestoreproduct_products` ADD $create_title_column varchar(255) COLLATE utf8_unicode_ci NOT NULL AFTER `body` , ADD $create_body_column TEXT NOT NULL AFTER $create_title_column ");
                $db->query("ALTER TABLE `engine4_sitestoreproduct_otherinfo` ADD $create_overview_column mediumtext COLLATE utf8_unicode_ci NOT NULL AFTER `overview` ");
            }

            $import_title_column_exist = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_imports LIKE $title_column")->fetch();
            $import_body_column_exist = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_imports LIKE $body_column")->fetch();

            //CREATE COLUMNS IF NOT EXISTS
            if (empty($import_title_column_exist) && empty($import_body_column_exist)) {
                $db->query("ALTER TABLE `engine4_sitestoreproduct_imports` ADD $create_title_column varchar(255) COLLATE utf8_unicode_ci NOT NULL AFTER `description` , ADD $create_body_column TEXT NOT NULL AFTER $create_title_column ");
            }
        }
    }

    /* START - Fix search Issue */

    private function _getOptionSearchParams($customParams) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $optionSearchParams = array();
        if(!empty($customParams)){
            foreach ($customParams as $key => $optionId) {
                if (!empty($optionId) && count(($optionPaths = explode("_", $key))) == 5) {
                    $optionSearchParams["fields"][] = (int) $optionPaths[4];
                    $optionSearchParams["options"][] = (int) $optionId;
                }
            }  
        }

        if (!empty($optionSearchParams)) {
            $options = $db->select()->from("engine4_sitestoreproduct_product_fields_options", 'label')
                            ->where("option_id in (?)", $optionSearchParams["options"])
                            ->query()->fetchAll();
            foreach ($options as $opt) {
                $optionSearchParams["labels"][] = $opt["label"];
            }
        }
        return $optionSearchParams;
    }

}