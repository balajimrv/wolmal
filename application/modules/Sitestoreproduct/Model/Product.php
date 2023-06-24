<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Product.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_Product extends Core_Model_Item_Abstract {

    protected $_parent_type = 'user';
    protected $_searchTriggers = array('title', 'body', 'search', 'approved', 'closed', 'declined', 'draft', 'end_date_enable', 'start_date', 'end_date', 'stock_unlimited', 'min_order_quantity', 'in_stock', 'location');
    protected $_parent_is_owner = true;

    public function isSearchable() {

        return ( (!isset($this->search) || $this->search) && !empty($this->_searchTriggers) && is_array($this->_searchTriggers) && empty($this->closed) && $this->approved && empty($this->draft) && ($this->start_date <= date("Y-m-d H:i:s")) && ($this->end_date_enable == 0 || ($this->end_date > date("Y-m-d H:i:s"))) && ($this->stock_unlimited == 1 || $this->min_order_quantity <= $this->in_stock));
    }

    /**
     * Gets an absolute URL to the page to view this item
     *
     * @return string
     */
    public function getHref($params = array()) {

        $slug = $this->getSlug();
        $params = array_merge(array(
            'route' => "sitestoreproduct_entry_view",
            'reset' => true,
            'product_id' => $this->product_id,
            'slug' => $slug,
                ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);

        return Zend_Controller_Front::getInstance()->getRouter()
                        ->assemble($params, $route, $reset);
    }

    public function getParent($recurseType = null) {
        return $this->getOwner('user');
    }

    /**
     * Return a truncate product description
     * */
    public function getDescription() {

        $body = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('body');

        if (empty($this->$body)) {
            $tmpBody = strip_tags($this->body);
        } else {
            $tmpBody = strip_tags($this->$body);
        }

        return ( Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody );
    }

    // General
    public function getShortType($inflect = false) {

        if ($this->_identity) {

            if ($inflect) {
                return str_replace(' ', '', ucwords(str_replace('_', ' ', Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.titlesingular', 'Product'))));
            }

            return strtolower(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.titlesingular', 'Product'));
        } else {
            return parent::getShortType($inflect);
        }
    }

    // General
    public function getMediaType() {

        return strtolower(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.titlesingular', 'Product'));
    }

    /**
     * Return slug
     * */
    public function getSlug($str = null, $number = 225) {

        if (null === $str) {
            $str = $this->title;
        }

        return Engine_Api::_()->seaocore()->getSlug($str, $number);
    }

    /**
     * Return a category
     * */
    public function getCategory() {

        return Engine_Api::_()->getItem('sitestoreproduct_category', $this->category_id);
    }

    /**
     * Return Bottom Line
     * */
    public function getBottomLine() {

        $params = array();
        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct');
        $params['resource_id'] = $this->product_id;
        $params['resource_type'] = $this->getType();
        $params['type'] = 'editor';
        $isEditorReviewed = $reviewTable->canPostReview($params);
        if (!empty($isEditorReviewed)) {
            $bottomLine = $reviewTable->getColumnValue($isEditorReviewed, 'title');

            if (!empty($bottomLine)) {
                return $bottomLine;
            }
        }

        return $this->getDescription();
    }

    /**
     * Return Editor Reviewed Date
     * */
    public function getEditorReviewedDate() {

        $params = array();
        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct');
        $params['resource_id'] = $this->product_id;
        $params['resource_type'] = $this->getType();
        $params['type'] = 'editor';
        $isEditorReviewed = $reviewTable->canPostReview($params);
        if (!empty($isEditorReviewed)) {
            $editorReviewedDate = $reviewTable->getColumnValue($isEditorReviewed, 'creation_date');

            if (!empty($editorReviewedDate)) {
                return $editorReviewedDate;
            }
        }

        return date();
    }

    /**
     * Return keywords
     *
     * @param char separator 
     * @return keywords
     * */
    public function getKeywords($separator = ' ') {

        $keywords = array();
        foreach ($this->tags()->getTagMaps() as $tagmap) {
            $tag = $tagmap->getTag();
            $keywords[] = $tag->getTitle();
        }

        if (null == $separator) {
            return $keywords;
        }

        return join($separator, $keywords);
    }

    public function getRichContent() {
        $getIdentity = $this->getIdentity();
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $str = $view->productRichContent($getIdentity);
        return $str;
    }

    public function getEditorReview() {

        $reveiwTable = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct');
        $select = $reveiwTable->getReviewsSelect(array('product_id' => $this->product_id, 'type' => 'editor', 'resource_type' => $this->getType(), 'resource_id' => $this->product_id));
        return $reveiwTable->fetchRow($select);
    }

    public function getNoOfReviews($type = null) {

        if ($type == 'user-review') {
            if (!empty($this->rating_editor))
                return $this->review_count - 1;
        }else {
            return $this->review_count;
        }
    }

    public function getExpiryTime() {

        if (empty($this->approved_date))
            return;
        $duration = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.adminexpiryduration', array('1', 'week'));
        $interval_type = $duration[1];
        $interval_value = $duration[0];
        $part = 1;
        $interval_value = empty($interval_value) ? 1 : $interval_value;
        $rel = strtotime($this->approved_date);
        // Calculate when the next payment should be due

        switch ($interval_type) {
            case 'day':
                $part = Zend_Date::DAY;
                break;
            case 'week':
                $part = Zend_Date::WEEK;
                break;
            case 'month':
                $part = Zend_Date::MONTH;
                break;
            case 'year':
                $part = Zend_Date::YEAR;
                break;
        }

        $relDate = new Zend_Date($rel);
        $relDate->add((int) $interval_value, $part);

        return $relDate->toValue();
    }

    /**
     * Set product photo
     *
     * */
    public function setPhoto($photo) {

        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
        } else {
            throw new Engine_Exception('invalid argument passed to setPhoto');
        }
        // CTSTYLE-46
        Engine_Api::_()->sitestoreproduct()->checkPhotoOrientation($file);

        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => 'sitestoreproduct_product',
            'parent_id' => $this->getIdentity()
        );

        // Add autorotation for uploded images. It will work only for SocialEngine-4.8.9 Or more then.
        $usingLessVersion = Engine_Api::_()->seaocore()->usingLessVersion('core', '4.8.9');
        if (!empty($usingLessVersion)) {
            $image = Engine_Image::factory();
            $image->open($file)
                    ->resize(1500, 1500)
                    ->write($path . '/m_' . $name)
                    ->destroy();
            //RESIZE IMAGE (PROFILE)
            $image = Engine_Image::factory();
            $image->open($file)
                    ->resize(300, 500)
                    ->write($path . '/p_' . $name)
                    ->destroy();

            //RESIZE IMAGE (NORMAL)
            $image = Engine_Image::factory();
            $image->open($file)
                    ->resize(140, 160)
                    ->write($path . '/in_' . $name)
                    ->destroy();

            //RESIZE IMAGE (Midum)
            $image = Engine_Image::factory();
            $image->open($file)
                    ->resize(200, 200)
                    ->write($path . '/im_' . $name)
                    ->destroy();
        } else {
            $image = Engine_Image::factory();
            $image->open($file)
                    ->autoRotate()
                    ->resize(1500, 1500)
                    ->write($path . '/m_' . $name)
                    ->destroy();
            //RESIZE IMAGE (PROFILE)
            $image = Engine_Image::factory();
            $image->open($file)
                    ->autoRotate()
                    ->resize(300, 500)
                    ->write($path . '/p_' . $name)
                    ->destroy();

            //RESIZE IMAGE (NORMAL)
            $image = Engine_Image::factory();
            $image->open($file)
                    ->autoRotate()
                    ->resize(140, 160)
                    ->write($path . '/in_' . $name)
                    ->destroy();

            //RESIZE IMAGE (Midum)
            $image = Engine_Image::factory();
            $image->open($file)
                    ->autoRotate()
                    ->resize(200, 200)
                    ->write($path . '/im_' . $name)
                    ->destroy();
        }

        //RESIZE IMAGE (ICON)
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
                ->write($path . '/is_' . $name)
                ->destroy();

        //STORE
        $storage = Engine_Api::_()->storage();
        $iMain = $storage->create($path . '/m_' . $name, $params);
        $iProfile = $storage->create($path . '/p_' . $name, $params);
        $iIconNormal = $storage->create($path . '/in_' . $name, $params);
        $iIconNormalMidum = $storage->create($path . '/im_' . $name, $params);
        $iSquare = $storage->create($path . '/is_' . $name, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormalMidum, 'thumb.midum');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iSquare, 'thumb.icon');

        //REMOVE TEMP FILES
        @unlink($path . '/p_' . $name);
        @unlink($path . '/m_' . $name);
        @unlink($path . '/in_' . $name);
        @unlink($path . '/is_' . $name);

        //ADD TO ALBUM
        $viewer = Engine_Api::_()->user()->getViewer();

        $photoTable = Engine_Api::_()->getItemTable('sitestoreproduct_photo');
        $rows = $photoTable->fetchRow($photoTable->select()->from($photoTable->info('name'), 'order')->order('order DESC')->limit(1));
        $order = 0;
        if (!empty($rows)) {
            $order = $rows->order + 1;
        }
        $sitestoreproductAlbum = $this->getSingletonAlbum();
        $photoItem = $photoTable->createRow();
        $photoItem->setFromArray(array(
            'product_id' => $this->getIdentity(),
            'album_id' => $sitestoreproductAlbum->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'file_id' => $iMain->getIdentity(),
            'collection_id' => $sitestoreproductAlbum->getIdentity(),
            'order' => $order
        ));
        $photoItem->save();

        //UPDATE ROW
        $this->modified_date = date('Y-m-d H:i:s');
        $this->photo_id = $photoItem->file_id;
        $this->save();

        return $this;
    }

    public function getPhoto($photo_id) {

        $photoTable = Engine_Api::_()->getItemTable('sitestoreproduct_photo');
        $select = $photoTable->select()
                ->where('file_id = ?', $photo_id)
                ->limit(1);

        $photo = $photoTable->fetchRow($select);
        return $photo;
    }

    /**
     * Gets a url to the current photo representing this item. Return null if none
     * set
     *
     * @param string The photo type (null -> main, thumb, icon, etc);
     * @return string The photo url
     */
    public function getPhotoUrl($type = null) {

        $module = "Sitestoreproduct";
        $shorType = "product";

        if (empty($this->photo_id)) {

            $type = ( $type ? str_replace('.', '_', $type) : 'main' );
            return Zend_Registry::get('Zend_View')->layout()->staticBaseUrl . 'application/modules/' . $module . '/externals/images/nophoto_' . $shorType . '_'
                    . $type . '.png';
        } else {
            $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, $type);
            if (!$file) {
                $type = ( $type ? str_replace('.', '_', $type) : 'main' );
                return Zend_Registry::get('Zend_View')->layout()->staticBaseUrl . 'application/modules/' . $module . '/externals/images/nophoto_' . $shorType . '_'
                        . $type . '.png';
            }

            return $file->map();
        }
    }

    public function getSingletonAlbum() {

        $table = Engine_Api::_()->getItemTable('sitestoreproduct_album');
        $select = $table->select()
                ->where('product_id = ?', $this->getIdentity())
                ->order('album_id ASC')
                ->limit(1);

        $album = $table->fetchRow($select);

        if (null == $album) {
            $album = $table->createRow();
            $album->setFromArray(array(
                'title' => $this->getTitle(),
                'product_id' => $this->getIdentity()
            ));
            $album->save();
        }

        return $album;
    }

    public function ratingBaseCategory($type = null) {

        $result = Engine_Api::_()->getDbtable('ratings', 'sitestoreproduct')->ratingbyCategory($this->getIdentity(), $type, $this->getType());
        $ratings = array();
        foreach ($result as $res) {
            $ratings[$res['ratingparam_id']] = $res['avg_rating'];
        }
        return $ratings;
    }

    public function getNumbersOfUserRating($type = null, $ratingparam_id = 0, $value = 0) {

        return $result = Engine_Api::_()->getDbtable('ratings', 'sitestoreproduct')->getNumbersOfUserRating($this->getIdentity(), $type, $ratingparam_id, $value, 0, $this->getType());
    }

    public function updateAllCoverPhotos() {
        $photo = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id);
        if ($photo instanceof Storage_Model_File) {
            $file = $photo->temporary();
            $fileName = $photo->name;
            $name = basename($file);
            $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
            $params = array(
                'parent_type' => 'sitestoreproduct_product',
                'parent_id' => $this->getIdentity()
            );

            //STORE
            $storage = Engine_Api::_()->storage();
            $iMain = $photo;

            $thunmProfile = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, 'thumb.profile');

            if (empty($thunmProfile) || empty($thunmProfile->parent_file_id)) {
                //RESIZE IMAGE (PROFILE)
                $image = Engine_Image::factory();
                $image->open($file)
                        ->resize(300, 500)
                        ->write($path . '/p_' . $name)
                        ->destroy();
                $iProfile = $storage->create($path . '/p_' . $name, $params);
                $iMain->bridge($iProfile, 'thumb.profile');
                @unlink($path . '/p_' . $name);
            }



            $thunmMidum = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, 'thumb.midum');
            if (empty($thunmMidum) || empty($thunmMidum->parent_file_id)) {
                //RESIZE IMAGE (Midum)
                $image = Engine_Image::factory();
                $image->open($file)
                        ->resize(200, 200)
                        ->write($path . '/im_' . $name)
                        ->destroy();
                $iIconNormalMidum = $storage->create($path . '/im_' . $name, $params);
                $iMain->bridge($iIconNormalMidum, 'thumb.midum');
                //REMOVE TEMP FILES

                @unlink($path . '/m_' . $name);
            }
        }
    }

    /**
     * Gets a proxy object for the comment handler
     *
     * @return Engine_ProxyObject
     * */
    public function comments() {

        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
    }

    /**
     * Gets a proxy object for the like handler
     *
     * @return Engine_ProxyObject
     * */
    public function likes() {

        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
    }

    /**
     * Gets a proxy object for the tags handler
     *
     * @return Engine_ProxyObject
     * */
    public function tags() {

        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
    }

    /**
     * Insert global searching value
     *
     * */
//  protected function _insert() {
//
//    if (null == $this->search) {
//      $this->search = 1;
//    }
//
//    parent::_insert();
//  }

    /**
     * Delete the product and belongings
     * 
     */
    public function _delete() {

        $product_id = $this->product_id;
        $db = Engine_Db_Table::getDefaultAdapter();

        $db->beginTransaction();
        try {
            Engine_Api::_()->fields()->getTable('sitestoreproduct_product', 'search')->delete(array('item_id = ?' => $product_id));
            Engine_Api::_()->fields()->getTable('sitestoreproduct_product', 'values')->delete(array('item_id = ?' => $product_id));

            //FETCH PRODUCT IDS
            $reviewTable = Engine_Api::_()->getDbtable('reviews', 'sitestoreproduct');
            $select = $reviewTable->select()
                    ->from($reviewTable->info('name'), 'review_id')
                    ->where('resource_id = ?', $this->product_id)
                    ->where('resource_type = ?', 'sitestoreproduct_product');
            $reviewDatas = $reviewTable->fetchAll($select);
            foreach ($reviewDatas as $reviewData) {
                Engine_Api::_()->getItem('sitestoreproduct_review', $reviewData->review_id)->delete();
            }

            //FETCH PRODUCT VIDEO IDS
            $reviewVideoTable = Engine_Api::_()->getDbtable('videos', 'sitestoreproduct');
            $selectVideoTable = $reviewVideoTable->select()
                    ->from($reviewVideoTable->info('name'), 'video_id')
                    ->where('product_id = ?', $this->product_id);
            $reviewVideoDatas = $reviewVideoTable->fetchAll($selectVideoTable);
            foreach ($reviewVideoDatas as $reviewVideoData) {
                Engine_Api::_()->getItem('sitestoreproduct_video', $reviewVideoData->video_id)->delete();
            }

            Engine_Api::_()->getDbtable('clasfvideos', 'sitestoreproduct')->delete(array('product_id = ?' => $product_id));
            Engine_Api::_()->getDbtable('albums', 'sitestoreproduct')->delete(array('product_id = ?' => $product_id));
            Engine_Api::_()->getDbtable('photos', 'sitestoreproduct')->delete(array('product_id = ?' => $product_id));
            Engine_Api::_()->getDbtable('vieweds', 'sitestoreproduct')->delete(array('product_id = ?' => $product_id));
            Engine_Api::_()->getDbtable('posts', 'sitestoreproduct')->delete(array('product_id = ?' => $product_id));
            Engine_Api::_()->getDbtable('topics', 'sitestoreproduct')->delete(array('product_id = ?' => $product_id));
            Engine_Api::_()->getDbtable('topicWatches', 'sitestoreproduct')->delete(array('resource_id = ?' => $product_id));

            Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->delete(array('product_id = ?' => $product_id));

            //GET WISHLISTS HAVING THIS PRODUCT ID
            $wishlistTable = Engine_Api::_()->getDbtable('wishlists', 'sitestoreproduct');
            $wishlistTableName = $wishlistTable->info('name');
            $wishlistMapTable = Engine_Api::_()->getDbtable('wishlistmaps', 'sitestoreproduct');
            $wishlistMapTableName = $wishlistMapTable->info('name');

            $wishListDatas = $wishlistTable->select()
                    ->from($wishlistTableName, 'wishlist_id')
                    ->where('product_id = ?', $product_id)
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);

            if (!empty($wishListDatas)) {
                $select = $wishlistMapTable->select()
                        ->from($wishlistMapTableName)
                        ->where($wishlistMapTableName . '.product_id != ?', $product_id)
                        ->where("wishlist_id IN (?)", (array) $wishListDatas)
                        ->order('date ASC')
                        ->group($wishlistMapTableName . '.wishlist_id');

                $wishlistMapDatas = $wishlistMapTable->fetchAll($select);
                if (!empty($wishlistMapDatas)) {

                    $wishlistMapDatas = $wishlistMapDatas->toArray();
                    foreach ($wishlistMapDatas as $wishlistMapData) {
                        $wishlistTable->update(array('product_id' => $wishlistMapData['product_id']), array('wishlist_id = ?' => $wishlistMapData['wishlist_id'], 'product_id = ?' => $product_id));
                    }
                }
            }

            $wishlistTable->update(array('product_id' => 0), array('product_id = ?' => $product_id));
            $wishlistMapTable->delete(array('product_id = ?' => $product_id));

            //DELETE CONFIGURABLE AND VIRTUAL DATABASE ENTIRES
            if ($this->product_type == 'configurable' || $this->product_type == 'virtual') {
                $optionId = Engine_Api::_()->getDbTable('productfields', 'sitestoreproduct')->getOptionId($product_id);
                if (!empty($optionId)) {
                    $metaTable = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'meta');
                    $metaTableName = $metaTable->info('name');
                    $fieldIds = $metaTable->select()
                            ->from($metaTableName, 'field_id')
                            ->where('option_id = ?', $optionId)
                            ->query()
                            ->fetchAll(Zend_Db::FETCH_COLUMN);
                    foreach ($fieldIds as $fieldId) {
                        Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'options')->delete(array('field_id = ?' => $fieldId));
                        Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'meta')->delete(array('field_id = ?' => $fieldId));
                    }

                    Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'options')->delete(array('option_id = ?' => $optionId));

                    Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'maps')->delete(array('option_id = ?' => $optionId));
                }
            }

            Engine_Api::_()->getDbTable('productfields', 'sitestoreproduct')->delete(array('product_id = ?' => $this->product_id));

            // DELETE PRODUCT FROM CART
            $cartProductTable = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct');
            $cartProductSelect = $cartProductTable->select()
                    ->from($cartProductTable->info('name'), array('cartproduct_id'))
                    ->where('product_id =?', $this->product_id);
            $cartProductIds = $cartProductTable->fetchAll($cartProductSelect);

            foreach ($cartProductIds as $cartProductId) {
                $cartProductItem = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $cartProductId->cartproduct_id);
                $cartId = $cartProductItem->cart_id;
                $cartProductItem->delete();
                Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->deleteCart($cartId);
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        //DELETE PRODUCT
        parent::_delete();
    }

    public function getProductParams() {
//    $string = strip_tags($this->desc);
//    $desc = Engine_String::strlen($string) > 250 ? Engine_String::substr($string, 0, (247)) . '...' : $string;
        $desc = 'sitestoreproduct';
        return array(
            'title' => 'sitestoreproduct_order', //$this->title,
            'description' => $desc,
            'price' => $this->price,
            'extension_type' => 'order',
            'extension_id' => $this->order_id,
        );
    }

    public function getProduct() {
//    if (null === $this->_product) {
        $productsTable = Engine_Api::_()->getDbtable('products', 'payment');
        $this->_product = $productsTable->fetchRow($productsTable->select()
                        ->where('extension_type = ?', 'userads')
                        ->where('extension_id = ?', $this->getIdentity())
                        ->limit(1));
        // Create a new product?
        if (!$this->_product) {
            $this->_product = $productsTable->createRow();
            $this->_product->setFromArray($this->getProductParams());
            $this->_product->save();
        }
//    }

        return $this->_product;
    }

    public function getGatewayIdentity() {
        return $this->getProduct()->sku;
    }

    public function setImportPhoto($photo, $flag) {
        if (is_string($photo)) {
            $file_exists = 1;
        } else {
            $file_exists = 0;
        }
        if (is_string($photo) && !empty($file_exists)) {
            if (fopen($photo, "r")) {
                $file = $photo;
            } else {
                return;
            }
        } else {
            throw new Engine_Exception('invalid argument passed to setPhoto');
        }
        $name = basename($file);

        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => 'sitestoreproduct_product',
            'parent_id' => $this->getIdentity()
        );

        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(1500, 1500)
                ->write($path . '/m_' . $name)
                ->resize(300, 500)
                ->write($path . '/p_' . $name)
                ->resize(140, 160)
                ->write($path . '/in_' . $name)
                ->resize(200, 200)
                ->write($path . '/im_' . $name);


        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
                ->write($path . '/is_' . $name)
                ->destroy();


        //STORE
        $storage = Engine_Api::_()->storage();
        $iMain = $storage->create($path . '/m_' . $name, $params);
        $iProfile = $storage->create($path . '/p_' . $name, $params);
        $iIconNormal = $storage->create($path . '/in_' . $name, $params);
        $iIconNormalMidum = $storage->create($path . '/im_' . $name, $params);
        $iSquare = $storage->create($path . '/is_' . $name, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormalMidum, 'thumb.midum');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iSquare, 'thumb.icon');

        //REMOVE TEMP FILES
        @unlink($path . '/p_' . $name);
        @unlink($path . '/m_' . $name);
        @unlink($path . '/in_' . $name);
        @unlink($path . '/is_' . $name);

        //ADD TO ALBUM
        $viewer = Engine_Api::_()->user()->getViewer();

        $photoTable = Engine_Api::_()->getItemTable('sitestoreproduct_photo');
        $rows = $photoTable->fetchRow($photoTable->select()->from($photoTable->info('name'), 'order')->order('order DESC')->limit(1));
        $order = 0;
        if (!empty($rows)) {
            $order = $rows->order + 1;
        }
        $sitestoreproductAlbum = $this->getSingletonAlbum();
        $photoItem = $photoTable->createRow();


        $tempArray = array(
            'product_id' => $this->getIdentity(),
            'album_id' => $sitestoreproductAlbum->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'file_id' => $iMain->getIdentity(),
            'collection_id' => $sitestoreproductAlbum->getIdentity(),
            'order' => $order
        );

        if (empty($flag))
            $tempArray['photo_id'] = $iMain->getIdentity();

        $photoItem->setFromArray($tempArray);
        $photoItem->save();

        //UPDATE ROW
        if (!empty($flag)) {
            $this->modified_date = date('Y-m-d H:i:s');
            $this->photo_id = $photoItem->file_id;
            $this->save();
        }
        return $this;
    }

    /**
     * Convert title text in to current selected language
     *
     */
    public function getTitle() {
        $title = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('title');

        if (empty($this->$title)) {
            return $this->title;
        }

        //RETURN VALUE
        return $this->$title;
    }

    public function setCopyPhoto($flag, $file_id, $sitestoreproduct_new, $imageFlag) {

        if (!empty($flag)) {
            $tmpRow = Engine_Api::_()->getItem('storage_file', $file_id);
            $file = $tmpRow->temporary();
            $fileName = $tmpRow->name;
        } else {
            $file = $file_id;
            $tempFileFlag = true;
        }
        // CTSTYLE-46
        Engine_Api::_()->sitestoreproduct()->checkPhotoOrientation($file);

        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => 'sitestoreproduct_product',
            'parent_id' => $sitestoreproduct_new->getIdentity()
        );

        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(1500, 1500)
                ->write($path . '/m_' . $name)
                ->resize(300, 500)
                ->write($path . '/p_' . $name)
                ->resize(140, 160)
                ->write($path . '/in_' . $name)
                ->resize(200, 200)
                ->write($path . '/im_' . $name);


        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
                ->write($path . '/is_' . $name)
                ->destroy();
        //STORE
        $storage = Engine_Api::_()->storage();
        $iMain = $storage->create($path . '/m_' . $name, $params);
        $iProfile = $storage->create($path . '/p_' . $name, $params);
        $iIconNormal = $storage->create($path . '/in_' . $name, $params);
        $iIconNormalMidum = $storage->create($path . '/im_' . $name, $params);
        $iSquare = $storage->create($path . '/is_' . $name, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormalMidum, 'thumb.midum');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iSquare, 'thumb.icon');

        //REMOVE TEMP FILES
        @unlink($path . '/p_' . $name);
        @unlink($path . '/m_' . $name);
        @unlink($path . '/in_' . $name);
        @unlink($path . '/is_' . $name);
        if (!empty($tempFileFlag)) {

            if (is_file($file)) {
                @chmod($file, 0777);
                @unlink($file);
            }
        }

        //ADD TO ALBUM
        $viewer = Engine_Api::_()->user()->getViewer();

        $photoTable = Engine_Api::_()->getItemTable('sitestoreproduct_photo');
        $rows = $photoTable->fetchRow($photoTable->select()->from($photoTable->info('name'), 'order')->order('order DESC')->limit(1));
        $order = 0;
        if (!empty($rows)) {
            $order = $rows->order + 1;
        }
        $sitestoreproductAlbum = $sitestoreproduct_new->getSingletonAlbum();
        $photoItem = $photoTable->createRow();


        $tempArray = array(
            'product_id' => $sitestoreproduct_new->getIdentity(),
            'album_id' => $sitestoreproductAlbum->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'file_id' => $iMain->getIdentity(),
            'collection_id' => $sitestoreproductAlbum->getIdentity(),
            'order' => $order
        );
        if (!empty($imageFlag))
            $tempArray['photo_id'] = $iMain->getIdentity();

        $photoItem->setFromArray($tempArray);
        $photoItem->save();

        //UPDATE ROW
        if (empty($imageFlag)) {
            $sitestoreproduct_new->modified_date = date('Y-m-d H:i:s');
            $sitestoreproduct_new->photo_id = $photoItem->file_id;
            $sitestoreproduct_new->save();
        }
        return $sitestoreproduct_new;
    }

    /**
     * Set location
     *
     */
    public function setLocation($params = null, $location_name = null) {

        $id = $this->product_id;
        $locationFieldEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.locationfield', 0);
        if ($locationFieldEnable) {
            $sitestoreproduct = $this;
            if (empty($params)) {
                if (!empty($sitestoreproduct))
                    $location = $sitestoreproduct->location;
            } else {
                $location = $params;
            }

            if (!empty($location)) {
                $locationTable = Engine_Api::_()->getDbtable('locations', 'sitestoreproduct');
                $locationName = $locationTable->info('name');

                $locationRow = $locationTable->getLocation(array('id' => $id));
                if (isset($_POST['locationParams']) && $_POST['locationParams']) {
                    if (is_string($_POST['locationParams']))
                        $_POST['locationParams'] = Zend_Json_Decoder::decode($_POST['locationParams']);
                    if ($_POST['locationParams']['location'] === $location) {
                        try {
                            $loctionV = $_POST['locationParams'];
                            $loctionV['product_id'] = $id;
                            $loctionV['zoom'] = 16;
                            if (empty($locationRow))
                                $locationRow = $locationTable->createRow();
                            $locationRow->setFromArray($loctionV);
                            $locationRow->save();
                        } catch (Exception $e) {
                            throw $e;
                        }
                    }
                    return;
                }


                $selectLocQuery = $locationTable->select()->where('location = ?', $location);
                $locationValue = $locationTable->fetchRow($selectLocQuery);

                $enableSocialengineaddon = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('seaocore');

                if (empty($locationValue)) {
                    $getSEALocation = array();
                    if (!empty($enableSocialengineaddon)) {
                        $getSEALocation = Engine_Api::_()->getDbtable('locations', 'seaocore')->getLocation(array('location' => $location));
                    }
                    if (empty($getSEALocation)) {

                        $locationLocal = $location;
                        $urladdress = urlencode($locationLocal);
                        $delay = 0;
                        // Iterate through the rows, geocoding each address
                        $geocode_pending = true;
                        while ($geocode_pending) {
                            $request_url = "https://maps.googleapis.com/maps/api/geocode/json?address=$urladdress&sensor=true";
                            $ch = curl_init();
                            $timeout = 5;
                            curl_setopt($ch, CURLOPT_URL, $request_url);
                            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                            ob_start();
                            curl_exec($ch);
                            curl_close($ch);

                            $get_value = ob_get_contents();
                            if (empty($get_value)) {
                                $get_value = @file_get_contents($request_url);
                            }
                            $json_resopnse = Zend_Json::decode($get_value);
                            ob_end_clean();
                            $status = $json_resopnse['status'];
                            if (strcmp($status, "OK") == 0) {
                                // Successful geocode
                                $geocode_pending = false;
                                $result = $json_resopnse['results'];

                                // Format: Longitude, Latitude, Altitude
                                $lat = $result[0]['geometry']['location']['lat'];
                                $lng = $result[0]['geometry']['location']['lng'];
                                $f_address = $result[0]['formatted_address'];


                                $len_add = count($result[0]['address_components']);

                                $address = '';
                                $country = '';
                                $state = '';
                                $zip_code = '';
                                $city = '';
                                for ($i = 0; $i < $len_add; $i++) {
                                    $types_location = $result[0]['address_components'][$i]['types'][0];

                                    if ($types_location == 'country') {

                                        $country = $result[0]['address_components'][$i]['long_name'];
                                    } else if ($types_location == 'administrative_area_level_1') {
                                        $state = $result[0]['address_components'][$i]['long_name'];
                                    } else if ($types_location == 'administrative_area_level_2') {
                                        $city = $result[0]['address_components'][$i]['long_name'];
                                    } else if ($types_location == 'zip_code') {
                                        $zip_code = $result[0]['address_components'][$i]['long_name'];
                                    } else if ($types_location == 'street_address') {
                                        if ($address == '')
                                            $address = $result[0]['address_components'][$i]['long_name'];
                                        else
                                            $address = $address . ',' . $result[0]['address_components'][$i]['long_name'];
                                    }else if ($types_location == 'locality') {
                                        if ($address == '')
                                            $address = $result[0]['address_components'][$i]['long_name'];
                                        else
                                            $address = $address . ',' . $result[0]['address_components'][$i]['long_name'];
                                    }else if ($types_location == 'route') {
                                        if ($address == '')
                                            $address = $result[0]['address_components'][$i]['long_name'];
                                        else
                                            $address = $address . ',' . $result[0]['address_components'][$i]['long_name'];
                                    }else if ($types_location == 'sublocality') {
                                        if ($address == '')
                                            $address = $result[0]['address_components'][$i]['long_name'];
                                        else
                                            $address = $address . ',' . $result[0]['address_components'][$i]['long_name'];
                                    }
                                }
                                $db = Engine_Db_Table::getDefaultAdapter();
                                $db->beginTransaction();

                                try {
                                    // Create sitestore
                                    $loctionV = array();
                                    $loctionV['product_id'] = $id;
                                    $loctionV['latitude'] = $lat;
                                    $loctionV['location'] = $locationLocal;
                                    $loctionV['longitude'] = $lng;
                                    $loctionV['formatted_address'] = $f_address;
                                    $loctionV['country'] = $country;
                                    $loctionV['state'] = $state;
                                    $loctionV['zipcode'] = $zip_code;
                                    $loctionV['city'] = $city;
                                    $loctionV['address'] = $address;
                                    $loctionV['zoom'] = 16;
                                    if (!empty($location_name))
                                        $loctionV['locationname'] = $location_name;
                                    if (empty($locationRow))
                                        $locationRow = $locationTable->createRow();

                                    $locationRow->setFromArray($loctionV);
                                    $locationRow->save();
                                    // Commit
                                    $db->commit();
                                    if (!empty($enableSocialengineaddon)) {
                                        $location = Engine_Api::_()->getDbtable('locations', 'seaocore')->setLocation($loctionV);
                                    }
                                } catch (Exception $e) {
                                    $db->rollBack();
                                    throw $e;
                                }
                            } else if (strcmp($status, "620") == 0) {
                                // sent geocodes too fast
                                $delay += 100000;
                            } else {
                                // failure to geocode
                                $geocode_pending = false;
                                echo "Address " . $locationLocal . " failed to geocoded. ";
                                echo "Received status " . $status . "\n";
                            }
                            usleep($delay);
                        }
                    } else {
                        $db = Engine_Db_Table::getDefaultAdapter();
                        $db->beginTransaction();

                        try {
                            // Create sitestore location
                            $loctionV = array();
                            if (empty($locationRow))
                                $locationRow = $locationTable->createRow();
                            $value = $getSEALocation->toarray();
                            unset($value['location_id']);
                            $value['product_id'] = $id;
                            if (!empty($location_name))
                                $loctionV['locationname'] = $location_name;
                            $locationRow->setFromArray($value);
                            $locationRow->save();
                            // Commit
                            $db->commit();
                        } catch (Exception $e) {
                            $db->rollBack();
                            throw $e;
                        }
                    }
                } else {
                    $db = Engine_Db_Table::getDefaultAdapter();
                    $db->beginTransaction();

                    try {
                        // Create sitestore location
                        $loctionV = array();
                        if (empty($locationRow))
                            $locationRow = $locationTable->createRow();
                        $value = $locationValue->toarray();
                        unset($value['location_id']);
                        $value['product_id'] = $id;
                        if (!empty($location_name))
                            $loctionV['locationname'] = $location_name;
                        $locationRow->setFromArray($value);
                        $locationRow->save();
                        // Commit
                        $db->commit();
                    } catch (Exception $e) {
                        $db->rollBack();
                        throw $e;
                    }
                }
            }
        }
    }

}
