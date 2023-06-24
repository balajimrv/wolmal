<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Core.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Api_Siteapi_Core extends Core_Api_Abstract {

    public $_profileFieldsArray;

    public function productSearchForm() {
        $storesearchsettings = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');
        $settings = $coreSettings = Engine_Api::_()->getApi('settings', 'core');

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


            $form[] = array(
                'name' => 'orderby',
                'label' => $this->translate('Browse by'),
                'type' => 'Select',
                'multiOptions' => $multiOptionsOrderBy,
            );
        }

        $row = $storesearchsettings->getFieldsOptions('sitestoreproduct', 'search');
        if (!empty($row) && !empty($row->display)) {
            $form[] = array(
                'name' => 'search',
                'type' => 'Text',
                'label' => $this->translate('Name / Keyword'),
            );
        }

        // category based searching 
        $row = $storesearchsettings->getFieldsOptions('sitestoreproduct', 'category_id');
        if (!empty($row) && !empty($row->display)) {
            $categories = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getCategoriesByLevel('category');
            $multiOptions = array();
            $multiOptions[0] = '';
            $subcategoryArray = array();
            foreach ($categories as $row => $value) {
                $multiOptions[$value->category_id] = $this->translate($value->category_name);
                $subcategoryArray[$value->category_id]['form'] = array(
                    'name' => 'subcategory_id',
                    'type' => 'Select',
                    'label' => 'Subcategory',
                    'multiOptions' => array(0 => ''),
                );
            }

            $form[] = array(
                'name' => 'category_id',
                'label' => $this->translate('Category'),
                'type' => 'Select',
                'multiOptions' => $multiOptions,
            );

            $subcategories = Engine_Api::_()->getDbtable('categories', 'sitestore')->getCategoriesByLevel('subcategory');

            if (!empty($subcategories)) {
                foreach ($subcategories as $row => $value) {
                    $subcategoryArray[$value->cat_dependency]['form']['multiOptions'][$value->category_id] = $this->translate($value->category_name);
                    $subcategoryArray[$value->cat_dependency]['subsubcategories'][$value->category_id] = array(
                        'name' => 'subsubcategory_id',
                        'type' => 'Select',
                        'label' => $this->translate('3rd Level category'),
                        'multiOptions' => array(0 => ''),
                    );

                    $subsubcategories = Engine_Api::_()->getDbtable('categories', 'sitestore')->getCategoriesByLevel('subsubcategory');

                    if (!empty($subsubcategories)) {
                        foreach ($subsubcategories as $row => $subvalue) {
                            if ($subvalue->cat_dependency == $value->category_id)
                                $subcategoryArray[$value->cat_dependency]['subsubcategories'][$subvalue->cat_dependency]['multiOptions'][$subvalue->category_id] = $this->translate($subvalue->category_name);
                        }

                        foreach ($subcategoryArray[$value->cat_dependency]['subsubcategories'] as $catkey => $catvalue) {
                            if (count($catvalue['multiOptions']) == 1)
                                unset($subcategoryArray[$value->cat_dependency]['subsubcategories']);
                        }
                    }
                }
                foreach ($subcategoryArray as $key => $value) {
                    if (count($value['form']['multiOptions']) == 1)
                        unset($subcategoryArray[$key]);
                }
            }

            // profile fields
            $profileFields = $this->getProfileTypes();
            $this->_profileFieldsArray = $profileFields;
            // profile fields
        }
        // category based searching 


        $row = $storesearchsettings->getFieldsOptions('sitestoreproduct', 'closed');
        if (!empty($row) && !empty($row->display)) {
            $form[] = array(
                'name' => 'closed',
                'type' => 'Select',
                'label' => $this->translate('status'),
                'mulitOptions' => array(
                    '' => $this->translate('All Products'),
                    '0' => $this->translate('Only Open Products'),
                    '1' => $this->translate('Only Closed Products'),
                ),
            );
        }

        $row = $storesearchsettings->getFieldsOptions('sitestoreproduct', 'show');
        if (!empty($row) && !empty($row->display)) {
            $show_multiOptions = array();
            $show_multiOptions["1"] = "Everyone's Products";
            $show_multiOptions["2"] = "Only My Friends' Products";
            $show_multiOptions["4"] = "Products I Like";

            $value_deault = 1;
            $enableNetwork = $settings->getSetting('sitestoreproduct.network', 0);
            if (empty($enableNetwork)) {
                $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
                $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
                $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer_id));

                if (!empty($viewerNetwork)) {
                    $show_multiOptions["3"] = 'Only My Networks';
                    $browseDefaulNetwork = $settings->getSetting('sitestoreproduct.default.show', 0);

                    if (!isset($_GET['show']) && !empty($browseDefaulNetwork)) {
                        $value_deault = 3;
                    } elseif (isset($_GET['show'])) {
                        $value_deault = $_GET['show'];
                    }
                }
            }

            $form[] = array(
                'name' => 'show',
                'label' => $this->translate('Show'),
                'multiOptions' => $show_multiOptions,
                'value' => $value_deault,
            );
        }

        // location work
        // $row = $this->_searchForm->getFieldsOptions('sitestoreproduct', 'location');
        // if (!empty($row) && !empty($row->display) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.locationfield', 0)) {
        //     $form[] = array(
        //             'type' => 'Text',
        //             'name' => 'location',
        //             'label' => $this->translate(" Location "),
        //         );
        // }
        // $rowStreet = $storesearchsettings->getFieldsOptions('sitestoreproduct', 'street');
        // if (!empty($rowStreet) && $rowStreet->display) {
        //     $form[] = array(
        //         'name' => 'sitestoreproduct_street',
        //         'label' => $this->translate('Street'),
        //         'type' => 'Text',
        //     );
        // }
        // $rowCity = $storesearchsettings->getFieldsOptions('sitestoreproduct', 'city');
        // if (!empty($rowCity) && $rowCity->display) {
        //     $form[] = array(
        //         'name' => 'sitestoreproduct_city',
        //         'label' => $this->translate('City'),
        //         'type' => 'text',
        //     );
        // }
        // $rowState = $storesearchsettings->getFieldsOptions('sitestoreproduct', 'state');
        // if (!empty($rowState) && $rowState->display) {
        //     $form[] = array(
        //         'name' => 'state',
        //         'label' => $this->translate('State'),
        //         'type' => 'text',
        //     );
        // }
        // $rowCountry = $storesearchsettings->getFieldsOptions('sitestoreproduct', 'country');
        // if (!empty($rowCountry) && $rowCountry->display) {
        //     $form[] = array(
        //         'name' => 'country',
        //         'label' => $this->translate('Country'),
        //         'type' => 'text',
        //     );
        // }
        // location work ends
        // price work
        $row = $storesearchsettings->getFieldsOptions('sitestoreproduct', 'price');
        
        if (!empty($row) && !empty($row->display)) {
            $form[] = array(
                'type' => 'Slider',
                'name' => 'minPrice',
                'label' => $this->translate("Min Price"),
                'value' => '0',
            );
            $form[] = array(
                'type' => 'Slider',
                'name' => 'maxPrice',
                'label' => $this->translate("Max Price"),
                'value' => '999',
            );
        }

        // price work ends
        // Discounted products
        $row = $storesearchsettings->getFieldsOptions('sitestoreproduct', 'discount');
        if (!empty($row) && !empty($row->display)) {
            $form[] = array(
                'name' => 'discount',
                'type' => 'Radio',
                'label' => $this->translate('Discount'),
                'multiOptions' => array(
                    '' => "All",
                    '0_10' => 'Upto 10%',
                    '10_20' => '10% - 20%',
                    '20_30' => '20% - 30%',
                    '30_40' => '30% - 40%',
                    '40_50' => '40% - 50%',
                    '50_100' => 'More than 50%',
                ),
            );
        }

        $row = $storesearchsettings->getFieldsOptions('sitestoreproduct', 'in_stock');
        if (!empty($row) && !empty($row->display)) {
            $form[] = array(
                'name' => 'in_stock',
                'type' => 'Checkbox',
                'label' => $this->translate('Exclude Out of Stock Products'),
            );
        }

        $row = $storesearchsettings->getFieldsOptions('sitestoreproduct', 'has_review');
        if (!empty($row) && $row->display) {
            unset($multiOptions);
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3) {
                $multiOptions = array(
                    '' => '',
                    'rating_avg' => 'Any Reviews',
                    'rating_editor' => 'Editor Reviews',
                    'rating_users' => 'User Reviews',
                );
            } elseif (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2) {
                $multiOptions = array(
                    '' => '',
                    'rating_users' => 'User Reviews',
                );
            } elseif (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 1) {
                $multiOptions = array(
                    '' => '',
                    'rating_editor' => 'Editor Reviews',
                );
            }

            $form[] = array(
                'name' => 'has_review',
                'type' => 'Select',
                'label' => $this->translate('Products Having'),
                'multyiOptions' => $multiOptions,
            );
        }

        $form[] = array(
            'name' => 'done',
            'label' => $this->translate('Search'),
            'type' => 'Button'
        );

        $response['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $response['fields'] = $this->_getProfileFields();
        $response['categoriesForm'] = $subcategoryArray;
        $response['form'] = $form;

        return $response;
    }

    /**
     * Send Mail and Notification on Order Place
     *
     * @param array $order_ids : array of order ids
     * @param bool $payment_approve : calling from admin approve payment or not
     * @return send mail and notification
     */
    public function orderPlaceMailAndNotification($order_ids, $payment_approve = false) {
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $manage_admin_table = Engine_Api::_()->getDbtable('manageadmins', 'sitestore');
        $action_table = Engine_Api::_()->getDbtable('actions', 'activity');
        $notification_table = Engine_Api::_()->getDbtable('notifications', 'activity');
        $newVar = _ENGINE_SSL ? 'https://' : 'http://';
        $tempIndex = 0;
        $isDirectPaymentEnable = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
        foreach ($order_ids as $order_id) {
            $offer_id = 0;
            $order = Engine_Api::_()->getItem('sitestoreproduct_order', $order_id['order_id']);
            $coupon_details = unserialize($order->coupon_detail);
            if (!empty($coupon_details)) {
                $coupon_code = $coupon_details['coupon_code'];
                $discount_amount = $coupon_details['coupon_amount'];
                $discount_amount = empty($discount_amount) ? 0 : $discount_amount;
                $offer_id = Engine_Api::_()->getDbtable('offers', 'sitestoreoffer')->getCouponInfo(array("fetchColumn" => 1, "coupon_code" => $coupon_code));
            }
            $sitestore = Engine_Api::_()->getItem('sitestore_store', $order->store_id);
            $store_name = '<a href="' . $newVar . $_SERVER['HTTP_HOST'] . $sitestore->getHref() . '">' . $sitestore->getTitle() . '</a>';

            // TO FETCH BUYER DETAIL
            if (empty($tempIndex)) {                
                ++$tempIndex;
                $billing_email_id = $buyer = Engine_Api::_()->getItem('user', $order->buyer_id);
                if (empty($order->buyer_id)) {
                    $billing_name = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct')->getBillingName($order->order_id);
                    $order_billing_name = $billing_name->f_name . ' ' . $billing_name->l_name;
                    $billing_email_id = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct')->getBillingEmailId($order->order_id);
                }
            }

            // IF PAYMENT IS COMPLETED VIA BY CHEQUE
            if (empty($payment_approve) && empty($isDirectPaymentEnable)) {
                if ($order->gateway_id == 3) {
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($billing_email_id, 'sitestoreproduct_member_order_place_by_bycheque', array(
                        'object_name' => $store_name,
                        'order_id' => '#' . $order->order_id,
                    ));
                    continue;
                }

                if ($order->gateway_id == 4) {
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($billing_email_id, 'sitestoreproduct_member_order_place_by_cod', array(
                        'object_name' => $store_name,
                        'order_id' => '#' . $order->order_id,
                    ));
                    continue;
                }
            }

            // IF PAYMENT IS COMPLETED, THEN SEND ACTIVITY FEED, NOTIFICATION AND EMAIL
            if ($order->payment_status == 'active' || (!empty($isDirectPaymentEnable) && empty($payment_approve))) {
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                foreach ($roles as $role) {
                    $auth->setAllowed($order, $role, 'view', 1);
                    $auth->setAllowed($order, $role, 'comment', 1);
                }

                // SEND ACTIVITY FEED
                if (empty($order->is_private_order) && $order->payment_status == 'active')
                    if (!empty($order->buyer_id)) {
                        $action = $action_table->addActivity($buyer, $order, 'sitestoreproduct_order_place', null, array('count' => $order->item_count, 'product' => array($sitestore->getType(), $sitestore->getIdentity())));
                        if (!empty($action))
                            $action_table->attachActivity($action, $order, Activity_Model_Action::ATTACH_MULTI);
                    }

                // SEND NOTIFICATION AND EMAIL TO ALL STORE ADMINS
                $getPageAdmins = $manage_admin_table->getManageAdmin($order->store_id);
                if (!empty($getPageAdmins)) {
                    $view_url = $view->url(array('action' => 'store', 'store_id' => $order->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $order->order_id,'store_id' => $order->store_id), 'sitestore_store_dashboard', true);
                    $order_no = $view->htmlLink($view_url, '#' . $order->order_id);

                    /* Coupon Mail Work */
                    if (!empty($offer_id)) {
                        $sitestoreoffer = Engine_Api::_()->getItem('sitestoreoffer_offer', $offer_id);
                        $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
                        $offer_tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreoffer.profile-sitestoreoffers', $sitestoreoffer->store_id, $layout);
                        if ($sitestore->photo_id) {
                            $data['store_photo_path'] = $sitestore->getPhotoUrl('thumb.icon');
                        } else {
                            $data['store_photo_path'] = $view->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/nophoto_sitestore_thumb_icon.png';
                        }
                        $data['store_title'] = $store_name;

                        if ($sitestoreoffer->photo_id) {
                            $data['offer_photo_path'] = $sitestoreoffer->getPhotoUrl('thumb.icon');
                        } else {
                            $data['offer_photo_path'] = $view->layout()->staticBaseUrl . 'application/modules/Sitestoreoffer/externals/images/offer_thumb.png';
                        }

                        $data['offer_title'] = $view->htmlLink('http://' . $_SERVER['HTTP_HOST'] .
                                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('user_id' => $sitestoreoffer->owner_id, 'offer_id' => $sitestoreoffer->offer_id, 'tab' => $offer_tab_id, 'slug' => $sitestoreoffer->getOfferSlug($sitestoreoffer->title)), 'sitestoreoffer_view', true), $sitestoreoffer->title, array('style' => 'color:#3b5998;text-decoration:none;'));

                        $data['coupon_code'] = $sitestoreoffer->coupon_code;
                        $data['offer_time'] = gmdate('M d, Y', strtotime($sitestoreoffer->end_time));
                        $data['offer_time_setting'] = $sitestoreoffer->end_settings;
                        $data['claim_owner_name'] = !empty($order->buyer_id) ? $buyer->displayname : $order_billing_name;
                        $data['enable_mailtemplate'] = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');
                        $data['discount_amount'] = Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($discount_amount);
                        $data['order_no'] = '<a href="' . $newVar . $_SERVER['HTTP_HOST'] . $view_url . '">#' . $order->order_id . '</a>';
                        // INITIALIZE THE STRING TO BE SEND IN THE CLAIM MAIL
                        $template_header = "";
                        $template_footer = "";
                        $string = '';
                        $string = $view->offermail($data);

                        $subject = $view->translate('Coupon ') . $sitestoreoffer->title . $view->translate(' from ') . $sitestore->title . $view->translate(' store has been used for order ') . '#' . $order->order_id;

//                
//
//                if ($sitestoreoffer->claim_count > 0) {
//                  $sitestoreoffer->claim_count--;
//                }
//                $sitestoreoffer->claimed++;
//                $sitestoreoffer->save();
//                $claim_count = $sitestoreoffer->claim_count;
//                $offer_id = $sitestoreoffer->offer_id;
//
//                if (($claim_count == 0) && $sitestoreoffer->end_settings == 1 && $sitestoreoffer->end_time < $today)      {
//                  $sitestoreofferClaimTable->deleteClaimOffers($offer_id);
//                }
                    }

                    foreach ($getPageAdmins as $pageAdmin) {
                        if (!empty($pageAdmin->sitestoreproduct_notification)) {
                            continue;
                        }
                        $sellerObj = Engine_Api::_()->getItem('user', $pageAdmin->user_id);

                        //if (empty($order->buyer_id))
                            //$notification_table->addNotification($sellerObj, $buyer, $order, 'sitestoreproduct_order_place_logout_viewer', array('viewer' => $order_billing_name, 'order_id' => $order_no,'store_id' => $order->store_id, 'page' => array($sitestore->getType(), $sitestore->getIdentity())));
                        //else
                            //$notification_table->addNotification($sellerObj, $buyer, $order, 'sitestoreproduct_order_place_login_viewer', array('order_id' => $order_no, 'page' => array($sitestore->getType(), $sitestore->getIdentity())));

                        $order_no = '<a href="' . $newVar . $_SERVER['HTTP_HOST'] . $view_url . '">#' . $order->order_id . '</a>';

                        // SEND MAIL TO ALL PAGE ADMIN
                        Engine_Api::_()->getApi('mail', 'core')->sendSystem($sellerObj, 'sitestoreproduct_order_place_to_seller', array(
                            'order_id' => '#' . $order->order_id,
                            'order_no' => $order_no,
                            'object_title' => $sitestore->getTitle(),
                            'object_name' => $store_name,
                            'order_invoice' => $this->orderInvoice($order),
                        ));

                        if (!empty($offer_id)) {
                            // SEND MAIL CLAIM OFFER
                            Engine_Api::_()->getApi('mail', 'core')->sendSystem($sellerObj, 'offer_claim', array(
                                'subject' => $subject,
//                    'template_header' => $template_header,
                                'message' => $string,
//                    'template_footer' => $template_footer,
                                'queue' => false));

                            $today = date("Y-m-d H:i:s");
                        }
                    }
                }
//        if(!empty($offer_id))
//        {
//          $sitestoreofferClaimTable = Engine_Api::_()->getDbTable('claims', 'sitestoreoffer');
//
//                $db = Engine_Db_Table::getDefaultAdapter();
//                $db->beginTransaction();
//                try {
//
//                  //CREATE CLAIM FOR OFFER
//                  $sitestoreofferRow = $sitestoreofferClaimTable->createRow();
//                  $sitestoreofferRow->owner_id = !empty($buyer)? $buyer->getIdentity() : 0;
//                  $sitestoreofferRow->store_id = $sitestoreoffer->store_id;
//                  $sitestoreofferRow->offer_id = $sitestoreoffer->offer_id;
//                  $sitestoreofferRow->claim_value = 1;
//                  $sitestoreofferRow->save();
//
//                  $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $sitestoreoffer, 'sitestoreoffer_home');
//
//                  if ($action != null) {
//                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $sitestoreoffer);
//                  }
//
//                  $db->commit();
//                } catch (Exception $e) {
//                  $db->rollBack();
//                  throw $e;
//                }
//        }
                // SEND MAIL TO SITE ADMIN FOR THIS ORDER
                $storeOwnerId = $sitestore->getOwner()->getIdentity();
                if (!empty($storeOwnerId))
                    $storeOwnerObj = Engine_Api::_()->getItem('user', $storeOwnerId);

                $admin_email_id = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.contact', null);
                
                if (!empty($admin_email_id) && ($storeOwnerObj->email != $admin_email_id)) {
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($admin_email_id, 'sitestoreproduct_order_place_to_admin', array(
                        'order_id' => '#' . $order->order_id,
                        'order_no' => $order_no,
                        'object_title' => $sitestore->getTitle(),
                        'object_name' => $store_name,
                        'order_invoice' => $this->orderInvoice($order),
                    ));
                }

                // SEND MAIL TO BUYER
                if (empty($order->buyer_id))
                    $order_no = '#' . $order->order_id;
                else {
                    $order_no = $newVar . $_SERVER['HTTP_HOST'] . $view->url(array('action' => 'account', 'menuType' => 'my-orders', 'subMenuType' => 'order-view', 'orderId' => $order->order_id,'store_id' => $order->store_id), 'sitestoreproduct_general', true);
                    $order_no = '<a href="' . $order_no . '">#' . $order->order_id . '</a>';
                }

                Engine_Api::_()->getApi('mail', 'core')->sendSystem($billing_email_id, "sitestoreproduct_order_place_by_member", array(
                    'order_invoice' => $this->orderInvoice($order),
                    'object_name' => $store_name,
                    'order_id' => '#' . $order->order_id,
                    'order_no' => $order_no
                ));
            } else {
                if (empty($order->buyer_id))
                    $order_no = '#' . $order->order_id;
                else {
                    $order_no = $newVar . $_SERVER['HTTP_HOST'] . $view->url(array('action' => 'account', 'menuType' => 'my-orders', 'subMenuType' => 'order-view', 'orderId' => $order->order_id,'store_id' => $order->store_id), 'sitestoreproduct_general', true);
                    $order_no = '<a href="' . $order_no . '">#' . $order->order_id . '</a>';
                }

                Engine_Api::_()->getApi('mail', 'core')->sendSystem($billing_email_id, 'sitestoreproduct_order_place_by_member_payment_pending', array(
                    'object_name' => $store_name,
                    'order_id' => '#' . $order->order_id,
                    'order_no' => $order_no,
                ));
            }
        }
    }

    public function checkForWishlist($product)
    {
            $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        // CHECK FOR WISHLIST - PRODUCT PRESENT IN USER WISHLIST
            if($viewer_id)
            {
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
                        ->where($wishlistTableName . '.owner_id = ?', $viewer_id)
                        ->where($wishlistMapsTableName.'.product_id = ?',$product->getIdentity());

               $isWishlistExists = (bool)$select->query()->fetchAll();

                if($isWishlistExists)
                    return 1;
                else
                    return 0;
            }
            else
                return 0;
    }

    /*
     * get product
     */

    public function getProduct($product, $values) {

        $productTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $data = $product->toArray();


        // GET PRICE FOR GROUPED PRODUCT sTARTS
        if ($product->product_type == 'grouped') {
            $data['price'] = $this->getGroupedProductMinPrice($product);
        }

        $data['information'] = Engine_Api::_()->getApi("Siteapi_Core","sitestoreproduct")->getPriceFields($product);
        
        // GET PRICE FOR GROUPED ENDS


        // CHECK FOR WISHLIST - PRODUCT PRESENT IN USER WISHLIST
        $data['wishlistPresent'] = $this->checkForWishlist($product);
        // CHECK FOR WISHLIST ENDS

        $subject = Engine_Api::_()->getItem('sitestore_store', $product->store_id);

        $data = array_merge($data, Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($product));
        $data = array_merge($data, Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($product, true));

        if ($values['action'] != 'category')
        {
            $data['menu'] = $this->gutterMenus($subject, $product);
        }
        else
        {
            $data['menu'] = array();
            $data['menu'][] = array(
            'label' => $this->translate('Add to Wishlist'),
            'name' => 'wishlist',
            'url' => 'sitestore/product/wishlist/add/',
                'urlParams' => array(
                    "product_id" => $subject->getIdentity()
                )
            );
        }

        $data['owner_title'] = $this->translate($product->getOwner()->getTitle());
        $ownerUrl = Engine_Api::_()->getApi('Core', 'siteapi')->getContentURL($product->getOwner(), "owner_url");
        $data = array_merge($data, $ownerUrl);

        $contentUrl = Engine_Api::_()->getApi('Core', 'siteapi')->getContentURL($product);
        $data = array_merge($data, $contentUrl);

        $productTags = $product->tags()->getTagMaps();
        $tagString = '';

        foreach ($productTags as $tagmap) {

            if ($tagString !== '')
                $tagString .= ', ';
            $tagString .= $tagmap->getTag()->getTitle();
        }

        if ($tagString)
            $data['tags'] = $tagString;

        $isAllowedView = $product->authorization()->isAllowed($viewer, 'view');
        $data["allow_to_view"] = empty($isAllowedView) ? 0 : 1;

        $like = $product->likes()->isLike($viewer);
        $data["is_liked"] = ($like) ? 1 : 0;

        $follow = Engine_Api::_()->getDbtable('follows', 'seaocore')->isFollow($product, $viewer);

        $data['is_followed'] = $follow ? 1 : 0;

        $isAllowedEdit = $product->authorization()->isAllowed($viewer, 'edit');
        $data["edit"] = empty($isAllowedEdit) ? 0 : 1;

        $isAllowedDelete = $product->authorization()->isAllowed($viewer, 'delete');
        $data["delete"] = empty($isAllowedDelete) ? 0 : 1;

        return $data;
    }

    public function getStoreProductPaginator($values) {

        global $sitestoreproductSelectQuery;

        $sitestoreproductSelectQuery = "null";

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();

        return Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getSitestoreproductsPaginator($values);
    }

    public function gutterMenus($parent, $subject) {

        if (!$subject || !$parent)
            return null;

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $gutterMenu = array();

        if(!empty($viewer_id))
        {
            $gutterMenu[] = array(
                'label' => $this->translate('Add to Wishlist'),
                'name' => 'wishlist',
                'url' => 'sitestore/product/wishlist/add/',
                'urlParams' => array(
                    "product_id" => $subject->getIdentity()
                )
            );

            $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct');
            $hasPosted = $reviewTable->canPostReview(array('resource_id' => $subject->getIdentity(),'resource_type'=>$subject->getType(),'viewer_id' => $viewer_id));

            if($hasPosted)
            {
                $gutterMenu[] = array(
                    'name' => 'update_review',
                    'label' => $this->translate("Update Review"),
                    'url' => "sitestore/product/review/edit/".$subject->getIdentity()."/".$hasPosted,
                );
            }
            else
            {
                $gutterMenu[] = array(
                    'name' => 'create_review',
                    'label' => $this->translate("Write a Review "),
                    'url' => "sitestore/product/review/create/".$subject->getIdentity(),
                );
            }
        }

        // For Cart
        if (isset($_REQUEST['cart']) && !empty($_REQUEST['cart'])) {
            $gutterMenu[] = array(
                'label' => $this->translate('Cart'),
                'name' => 'cart'
            );
        }

        if (!empty($viewer_id)) {
            $showMessageOwner = 0;
            $showMessageOwner = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
            if ($showMessageOwner != 'none') {
                $showMessageOwner = 1;
            }

            //RETURN IF NOT AUTHORIZED
            if ($subject->owner_id == $viewer_id || empty($viewer_id) || empty($showMessageOwner)) {
                $showMessageOwner = 0;
            }

            if ($showMessageOwner) {
                $gutterMenu[] = array(
                    'name' => 'messageowner',
                    'label' => $this->translate('Message Owner'),
                    'url' => 'sitestore/product/messageowner/' . $parent->getIdentity() . '/' . $subject->getIdentity(),
                );
            }

            $gutterMenu[] = array(
                'name' => 'share',
                'label' => $this->translate('Share This Product'),
                'url' => 'activity/share',
                'urlParams' => array(
                    "type" => $subject->getType(),
                    "id" => $subject->getIdentity()
                )
            );

            $gutterMenu[] = array(
                'name' => 'report',
                'label' => $this->translate('Report This Product'),
                'url' => 'report/create/subject/' . $subject->getGuid(),
                'urlParams' => array(
                    "type" => $subject->getType(),
                    "id" => $subject->getIdentity()
                )
            );
        }

        if(!empty($viewer_id))
        {
            $gutterMenu[] = array(
                'name' => 'tellafriend',
                'label' => $this->translate("Tell a Friend"),
                'url' => 'sitestore/product/tellafriend/' . $parent->getIdentity() . '/' . $subject->getIdentity(),
            );

            $gutterMenu[] = array(
                'name' => 'askopinion',
                'label' => $this->translate("Ask For Opinion"),
                'url' => 'sitestore/product/askopinion/' . $parent->getIdentity() . '/' . $subject->getIdentity(),
            );
        }

        return $gutterMenu;
    }

    public function getproductForm($sitestore, $product) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
    }

    /**
     * Gets all categories and subcategories
     *
     * @param string $category_id
     * @param string $fieldname
     * @param int $storeCondition
     * @param string $store
     * @param  all categories and subcategories
     */
    public function getAllCategories($category_id, $fieldname, $productCondition, $product, $subcat = null, $limit = 0) {
        //GET CATEGORY TABLE NAME
        $tableCategories = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct');
        $tableCategoriesName = $tableCategories->info('name');

        //GET PRODUCTS TABLE
        $tableProduct = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
        $tableProductName = $tableProduct->info('name');
        // MAKE QUERY
        $select = $tableCategories->select()->setIntegrityCheck(false)->from($tableCategoriesName);

        $select = $select->joinLeft($tableProductName, $tableProductName . '.' . $fieldname . '=' . $tableCategoriesName . '.category_id', array("count(product_id) as count"));

        if (!empty($order)) {
            $select->order("$order");
        }

        $select = $select->where($tableCategoriesName . '.cat_dependency = ' . $category_id)
                ->group($tableCategoriesName . '.category_id')
                ->order('cat_order');

        if (!empty($limit)) {
            $select = $select->limit($limit);
        }

        if ($productCondition == 1)
            $select->where($tableProductName . '.approved = ?', 1)->where($tableProductName . '.draft = ?', 0)->where($tableProductName . '.search = ?', 1);

        if ($productCondition == 1) {
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
                $select->where($tableProductName . '.closed = ?', 0);
            }
        }

        if ($productCondition == 1)
            $select = $tableProduct->expirySQL($select);

        $select = $tableProduct->getNetworkBaseSql($select, array('not_groupBy' => 1));

        //RETURN DATA
        return $tableCategories->fetchAll($select);
    }

    /**
     * Gets the Profile Types of a Page Based on category
     *
     * @param array object of profilefieldmaps
     *
     * @return array
     */
    public function getProfileTypes($profileFields = array()) {

        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('sitestoreproduct_product');
        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            $profileTypeField = $topStructure[0]->getChild();
            $options = $profileTypeField->getOptions();

            $options = $profileTypeField->getElementParams('sitestoreproduct_product');
            if (isset($options['options']['multiOptions']) && !empty($options['options']['multiOptions']) && is_array($options['options']['multiOptions'])) {
                // Make exist profile fields array.
                foreach ($options['options']['multiOptions'] as $key => $value) {
                    if (!empty($key)) {
                        $profileFields[$key] = $value;
                    }
                }
            }
        }

        return $profileFields;
    }


    /*
    * Wishlist search form
    */
    public function getWishlistSearchForm() {

        $wishlistSearch[] = array(
            'type' => 'Text',
            'name' => 'search',
            'label' => $this->translate('Search'),
        );

        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if ($viewer_id) {
            $wishlistSearch[] = array(
                'type' => 'Select',
                'name' => 'search_wishlist',
                'label' => $this->translate('Wishlists'),
                'multiOptions' => array(
                    '' => '',
                    'my_wishlists' => $this->translate('My Wishlist'),
                    'friends_wishlists' => $this->translate('My Friends Wishlists'),
                ),
            );
        }

        $wishlistSearch[] = array(
            'type' => 'Text',
            'name' => 'text',
            'label' => $this->translate("Member's Name/Email"),
        );

        $wishlistSearch[] = array(
            'type' => 'Select',
            'name' => 'orderby',
            'label' => $this->translate('Browse By'),
            'multiOptions' => array(
                'wishlist_id' => $this->translate('Most Recent'),
                'total_item' => $this->translate('Maximum Events'),
                'view_count' => $this->translate('Most Viewed'),
            ),
        );

        $wishlistSearch[] = array(
            'type' => 'Submit',
            'name' => 'done',
            'label' => $this->translate('Search'),
        );
        return $wishlistSearch;
    }

    public function getAddToWishlistForm($product_id) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $wishlistDatas = Engine_Api::_()->getDbtable('wishlists', 'sitestoreproduct')->userWishlists($viewer);
        $wishlistDatasCount = Count($wishlistDatas);
        $listing_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('product_id', null);
        $event = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        $wishlistIdsDatas = Engine_Api::_()->getDbtable('wishlistmaps', 'sitestoreproduct')->pageWishlists($product_id, $viewer_id);

        if (!empty($wishlistIdsDatas)) {
            $wishlistIdsDatas = $wishlistIdsDatas->toArray();
            $wishlistIds = array();
            foreach ($wishlistIdsDatas as $wishlistIdsData) {
                $wishlistIds[] = $wishlistIdsData['wishlist_id'];
            }
        }

        foreach ($wishlistDatas as $wishlistData) {

            if (in_array($wishlistData->wishlist_id, $wishlistIds)) {
                $add[] = array(
                    'type' => 'Checkbox',
                    'name' => 'inWishlist_' . $wishlistData->wishlist_id,
                    'label' => $this->translate($wishlistData->title),
                    'value' => 1,
                );
            } else {
                $add[] = array(
                    'type' => 'Checkbox',
                    'name' => 'wishlist_' . $wishlistData->wishlist_id,
                    'label' => $this->translate($wishlistData->title),
                    'value' => 0,
                );
            }
        }

        if ($wishlistDatasCount) {
            $add[] = array(
                'type' => 'Text',
                'name' => 'title',
                'label' => $this->translate('Wishlist Name'),
            );
        } else {
            $add[] = array(
                'type' => 'Text',
                'name' => 'title',
                'label' => $this->translate('Wishlist Name'),
                'hasValidator' => 'true'
            );
        }

        $add[] = array(
            'type' => 'Textarea',
            'name' => 'body',
            'label' => $this->translate('Description'),
        );

        $availableLabels = array(
            'everyone' => $this->translate('Everyone'),
            'registered' => $this->translate('All Registered Members'),
            'owner_network' => $this->translate('Friends and Networks'),
            'owner_member_member' => $this->translate('Friends of Friends'),
            'owner_member' => $this->translate('Friends Only'),
            'owner' => $this->translate('Just Me')
        );

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestoreproduct_wishlist', $viewer, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
        $viewOptionsReverse = array_reverse($viewOptions);
        $orderPrivacyHiddenFields = 786590;

        if (count($viewOptions) > 1) {
            $add[] = array(
                'type' => 'Select',
                'name' => 'auth_view',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('View Privacy'),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may see this wishlist?'),
                'multiOptions' => $viewOptions,
                'value' => "everyone",
            );
        }

        $add[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Save'),
        );
        return $add;
    }

    public function getCreateWishlistForm() {
        if (_CLIENT_TYPE && (_CLIENT_TYPE == 'ios')) {
            $add[] = array(
                "type" => "Label",
                "name" => "create_wishlist_description",
                "label" => $this->translate('You can also add this Products in a new wishlist below:')
            );
        }
        $add[] = array(
            'type' => 'Text',
            'name' => 'title',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Wishlist Name'),
            'hasValidator' => 'true'
        );


        $add[] = array(
            'type' => 'Textarea',
            'name' => 'body',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Wishlist Note'),
        );

        $availableLabels = array(
            'everyone' => $this->translate('Everyone'),
            'registered' => $this->translate('All Registered Members'),
            'owner_network' => $this->translate('Friends and Networks'),
            'owner_member_member' => $this->translate('Friends of Friends'),
            'owner_member' => $this->translate('Friends Only'),
            'owner' => $this->translate('Just Me')
        );

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestoreproduct_wishlist', $viewer, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
        $viewOptionsReverse = array_reverse($viewOptions);
        $orderPrivacyHiddenFields = 786590;

        if (count($viewOptions) > 1) {
            $add[] = array(
                'type' => 'Select',
                'name' => 'auth_view',
                'label' => $this->translate('View Privacy'),
                'description' => $this->translate('Who may see this wishlist?'),
                'multiOptions' => $viewOptions,
                'value' => key($viewOptionsReverse),
            );
        }

        $add[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => $this->translate('Save'),
        );
        return $add;
    }

    /**
     * Gets default profile ids
     *
     * @param sitepage object
     * return array
     */
    public function getDefaultProfileTypeId($subject) {
        $getFieldId = null;
        $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($subject);
        if (!empty($fieldsByAlias['profile_type'])) {
            $optionId = $fieldsByAlias['profile_type']->getValue($subject);
            $getFieldId = $optionId->value;
        }
        if (empty($getFieldId)) {
            return;
        }

        return $getFieldId;
    }

    /**
     *  Gets the profile fields for the directory page based on category
     *
     *  @param array fieldsform
     *  @return array
     */
    private function _getProfileFields($fieldsForm = array()) {
        $fieldsForm = array();
        foreach ($this->_profileFieldsArray as $option_id => $prfileFieldTitle) {

            if (!empty($option_id)) {
                $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('sitestoreproduct_product');
                $getRowsMatching = $mapData->getRowsMatching('option_id', $option_id);

                $fieldArray = array();
                $getFieldInfo = Engine_Api::_()->fields()->getFieldInfo();
                $getHeadingName = '';
                foreach ($getRowsMatching as $map) {
                    $meta = $map->getChild();
                    $type = $meta->type;

                    if (!empty($type) && ($type == 'heading')) {
                        $getHeadingName = $meta->label;
                        continue;
                    }

                    if (!empty($this->_validateSearchProfileFields) && (!isset($meta->search) || empty($meta->search)))
                        continue;


                    $fieldForm = $getMultiOptions = array();
                    $key = $map->getKey();


                    // Findout respective form element field array.
                    if (isset($getFieldInfo['fields'][$type]) && !empty($getFieldInfo['fields'][$type])) {
                        $getFormFieldTypeArray = $getFieldInfo['fields'][$type];

                        // In case of Generic profile fields.
                        if (isset($getFormFieldTypeArray['category']) && ($getFormFieldTypeArray['category'] == 'generic')) {
                            // If multiOption enabled then perpare the multiOption array.

                            if (($type == 'select') || ($type == 'radio') || (isset($getFormFieldTypeArray['multi']) && !empty($getFormFieldTypeArray['multi']))) {
                                $getOptions = $meta->getOptions();
                                if (!empty($getOptions)) {
                                    foreach ($getOptions as $option) {
                                        $getMultiOptions[$option->option_id] = $option->label;
                                    }
                                }
                            }

                            // Prepare Generic form.
                            $fieldForm['type'] = ucfirst($type);
                            $fieldForm['name'] = $key . '_field_' . $meta->field_id;
                            $fieldForm['label'] = (isset($meta->label) && !empty($meta->label)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->label) : '';
                            $fieldForm['description'] = (isset($meta->description) && !empty($meta->description)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->description) : '';

                            // Add multiOption, If available.
                            if (!empty($getMultiOptions)) {
                                $fieldForm['multiOptions'] = $getMultiOptions;
                            }
                            // Add validator, If available.
                            if (isset($meta->required) && !empty($meta->required))
                                $fieldForm['hasValidator'] = true;

                            $fieldForm['heading'] = $getHeadingName;

                            if (COUNT($this->_profileFieldsArray) > 1) {

                                if (isset($this->_create) && !empty($this->_create) && $this->_create == 1) {
                                    $optionCategoryName = Engine_Api::_()->getDbtable('options', 'sitestoreproduct')->getProfileTypeLabel($option_id);
                                    $fieldsForm[$option_id][] = $fieldForm;
                                } else {
                                    $fieldsForm[$option_id][] = $fieldForm;
                                }
                            } else
                                $fieldsForm[] = $fieldForm;
                        }else if (isset($getFormFieldTypeArray['category']) && ($getFormFieldTypeArray['category'] == 'specific') && !empty($getFormFieldTypeArray['base'])) { // In case of Specific profile fields.
                            // Prepare Specific form.
                            $fieldForm['type'] = ucfirst($getFormFieldTypeArray['base']);
                            $fieldForm['name'] = $key . '_field_' . $meta->field_id;
                            $fieldForm['label'] = (isset($meta->label) && !empty($meta->label)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->label) : '';
                            $fieldForm['description'] = (isset($meta->description) && !empty($meta->description)) ? $meta->description : '';

                            // Add multiOption, If available.
                            if ($getFormFieldTypeArray['base'] == 'select') {
                                $getOptions = $meta->getOptions();
                                foreach ($getOptions as $option) {
                                    $getMultiOptions[$option->option_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($option->label);
                                }
                                $fieldForm['multiOptions'] = $getMultiOptions;
                            }

                            // Add validator, If available.
                            if (isset($meta->required) && !empty($meta->required))
                                $fieldForm['hasValidator'] = true;

                            if (COUNT($this->_profileFieldsArray) > 1) {
                                if (isset($this->_create) && !empty($this->_create) && $this->_create == 1) {
                                    $optionCategoryName = Engine_Api::_()->getDbtable('options', 'sitestoreproduct')->getProfileTypeLabel($option_id);
                                    $fieldsForm[$optionCategoryName][] = $fieldForm;
                                } else {
                                    $fieldsForm[$option_id][] = $fieldForm;
                                }
                            } else
                                $fieldsForm[] = $fieldForm;
//                                $fieldsForm[] = $fieldForm;
                        }
                    }
                }
            }
        }
        return $fieldsForm;
    }

    /**
     * Get the Profile Fields Information, which will show on product profile page.
     *
     * @param product object , setkeyasresponse boolean
     * @return array
     */
    public function getProfileInfo($product, $setKeyAsResponse = false) {
        // Getting the default Profile Type id.
        $getFieldId = $this->getDefaultProfileTypeId($product);
        // Start work to get form values.
        $values = Engine_Api::_()->fields()->getFieldsValues($product);

        $fieldValues = array();
        // In case if Profile Type available. like User module.
        if (!empty($getFieldId)) {
            // Set the default profile type.
            $this->_profileFieldsArray[$getFieldId] = $getFieldId;
            $_getProfileFields = $this->_getProfileFields();
            $specificProfileFields[$getFieldId] = $_getProfileFields[$getFieldId];
            foreach ($specificProfileFields as $heading => $tempValue) {
                foreach ($tempValue as $key => $value) {
                    $key = $value['name'];
                    $label = $value['label'];
                    $type = $value['type'];
                    $parts = @explode('_', $key);
                    $heading = $value['heading'];

                    if (count($parts) < 3)
                        continue;

                    list($parent_id, $option_id, $field_id) = $parts;

                    $valueRows = $values->getRowsMatching(array(
                        'field_id' => $field_id,
                        'item_id' => $product->getIdentity()
                    ));

                    if (!empty($valueRows)) {
                        foreach ($valueRows as $fieldRow) {

                            $tempValue = $fieldRow->value;

                            // In case of Select or Multi send the respective label.
                            if (isset($value['multiOptions']) && !empty($value['multiOptions']) && isset($value['multiOptions'][$fieldRow->value]))
                                $tempValue = $value['multiOptions'][$fieldRow->value];
                            $tempKey = !empty($setKeyAsResponse) ? $key : $label;
                            if($heading)
                                $fieldValues[$heading][$tempKey] = $tempValue;
                            else
                                $fieldValues[$tempKey] = $tempValue;
                        }
                    }
                }
            }
        } else { // In case, If there are no Profile Type available and only Profile Fields are available. like Classified.
            $getType = $product->getType();
            $_getProfileFields = $this->_getProfileFields($getType);

            foreach ($_getProfileFields as $value) {
                $key = $value['name'];
                $label = $value['label'];
                $parts = @explode('_', $key);

                if (count($parts) < 3)
                    continue;

                list($parent_id, $option_id, $field_id) = $parts;

                $valueRows = $values->getRowsMatching(array(
                    'field_id' => $field_id,
                    'item_id' => $product->getIdentity()
                ));

                if (!empty($valueRows)) {
                    foreach ($valueRows as $fieldRow) {
                        if (!empty($fieldRow->value)) {
                            $tempKey = !empty($setKeyAsResponse) ? $key : $label;
                            if($heading)
                                $fieldValues[$heading][$tempKey] = $tempValue;
                            else
                                $fieldValues[$tempKey] = $tempValue;
                        }
                    }
                }
            }
        }

        return $fieldValues;
    }

    public function getInfoFields($product) {
        $profileFields = $this->getProfileTypes();
        if (!empty($profileFields)) {
            $this->_profileFieldsArray = $profileFields;
        }

        return $this->getProfileInfo($product);
    }

    /*
     *  
     */

    public function getCombinationOptions($product) {
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();

        if (!$product)
            return;

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

        $option_id = $profileFields->getOptionId($product->getIdentity());

        if (!$option_id)
            return;


        // Get the first field which is of type select 
        // Get all the fields which are not of type select
        // $getFirstSelect = $combinationTable->select()
        //                                     ->from($combinationTableName , array())
        //                                     ->setIntegrityCheck(false)
        //                                     ->distinct()
        //                                     ->joinInner($combinationAttributeMapsTableName , "$combinationAttributeMapsTableName.combination_id = $combinationTableName.combination_id" , array())
        //                                     ->joinInner($combinationAttributesTableName , $combinationAttributesTableName.".attribute_id = ". $combinationAttributeMapsTableName.".attribute_id")
        //                                     ->joinInner($cartProductFieldsMetaTableName , "$cartProductFieldsMetaTableName.field_id = $combinationAttributesTableName.field_id" ,array('label as field_label'))
        //                                     ->joinInner($cartProductFieldsOptionsTableName , "$cartProductFieldsOptionsTableName.option_id = $combinationAttributesTableName.combination_attribute_id" , array('label'))
        //                                     ->where($combinationTableName.".status = ?" , '1')
        //                                     ->where($combinationTableName.".quantity > ?" , '0')
        //                                     ->where($combinationAttributesTableName.".order = ?" , '0')
        //                                     ->where($combinationAttributesTableName.".product_id = ?" , $product->getIdentity())
        //                                     ->group($combinationTableName.".combination_id");

        $select = $combinationAttributesTable->select()
                ->from($combinationAttributesTableName, array("DISTINCT(" . $combinationAttributesTableName . ".field_id)"))
                ->setIntegrityCheck(false)
                ->joinInner($cartProductFieldsMetaTableName, "$cartProductFieldsMetaTableName.field_id = $combinationAttributesTableName.field_id", array('label as field_label'))
                ->where("$combinationAttributesTableName.product_id = ?", $product->getIdentity())
                ->order("$combinationAttributesTableName.order asc");

        $getFirstOptions = $select->query()->fetchAll();

        if (count($getFirstOptions)) {
            $firstOption = $getFirstOptions[0];
            foreach ($getFirstOptions as $key => $value) {
                $count++;
                $form[] = array(
                    'name' => 'select_' . $value['field_id'],
                    'type' => 'select',
                    'hasValidator' => true,
                    'label' => $this->translate($value['field_label']),
                    'order' => $count
                );
            }

            $select = $combinationAttributesTable->select()
                    ->from($combinationAttributesTableName)
                    ->setIntegrityCheck(false)
                    ->where("$combinationAttributesTableName.product_id = ?", $product->getIdentity())
                    ->where("$combinationAttributesTableName.field_id = ? ", $firstOption['field_id'])
                    ->joinInner($cartProductFieldsOptionsTableName, "$cartProductFieldsOptionsTableName.option_id = $combinationAttributesTableName.combination_attribute_id", array("$cartProductFieldsOptionsTableName.label"))
                    ->order("$combinationAttributesTableName.attribute_id asc");

            $attributes = $select->query()->fetchAll();

            if ($attributes) {
                $form[0]['multiOptions'][0] = $this->translate("--- select ---");
                foreach ($attributes as $key => $value) {
                    $data = array('label' => $this->translate($value['label']), 'price' => $value['price'], 'price_increment' => ($value['price_increment']) ? true : false);
                    $form[0]['multiOptions'][$value['combination_attribute_id']] = $data;
                }
            }
        }

        $tempresponse['dependentFields'] = $form;
        $tempresponse['dependentFieldsCount'] = count($form);

        $form = array();

        $getFieldsSelect = $cartProductFieldsMeta->select()
                ->from($cartProductFieldsMeta->info('name'))
                ->where('option_id = ?', $option_id)
                ->where('type != ?', 'select');

        $fieldsMetas = $getFieldsSelect->query()->fetchAll();

        if (count($fieldsMetas) >= 1) {
            foreach ($fieldsMetas as $fieldMeta) {
                $fieldId = $fieldMeta['field_id'];
                $fieldOptions = $cartProductFieldsOptions->getOptions($fieldId);

                if (count($fieldOptions)==0 && !strstr($fieldMeta['type'] , "text" ))
                    continue;

                $multiOptions = array();

                foreach ($fieldOptions as $fieldOption) {
                    $data = array();
                    $data['label'] = $this->translate($fieldOption['label']);
                    $data['price'] = $fieldOption['price'];
                    $data['price_increment'] = ($fieldOption['price_increment']) ? true : false;
                    $price = $fieldOption['price'];
                    
                    if(count($fieldOptions))
                        $multiOptions[$fieldOption['option_id']] = $data;

                }
                
                $data = array(
                    'name' => $product->store_id . '_' . $product->getIdentity() . '_' . $fieldId,
                    'type' => $fieldMeta['type'],
                    'hasValidator' => true,
                    'label' => $this->translate($fieldMeta['label']),
                    'description' => $this->translate($fieldMeta['description']),
                    'multiOptions' => $multiOptions,
                );

                if(strstr($fieldMeta['type'] , 'text'))
                    $data['hasValidator'] = false;

                if(!count($fieldOptions))
                    unset($data['multiOptions']);

                $form[] = $data;
            }
        }

        $tempresponse["independentFields"] = $form;
        $tempresponse["independentFieldsCount"] = count($form);

        return $tempresponse;
    }

    public function getPriceFields($product)
    {
        $isVatAllow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0);


        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $productsTable = Engine_Api::_()->getDbTable('products', 'sitestoreproduct');

        $otherinfo = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->getOtherinfo($product->getIdentity());
        $information['price']['price'] = $product->price;

        if($isVatAllow)
        {
            $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product);
            // var_dump($productPricesArray);die;
            $information['price']['price'] = $productPricesArray['display_product_price'];
            $information['price']['sign'] = $productPricesArray['show_msg'] ? 1 : 0;
            if($productPricesArray['discount'])
            {
                $information['price']['discount'] = 1;
                $information['price']['price'] = $productPricesArray['origin_price']  ;
                $information['price']['discount_amount'] = $productPricesArray['discount'];
                $information['price']['discount_percentage'] = $productPricesArray['discountPercentage'];
                $information['price']['discounted_amount'] = $productPricesArray['display_product_price'] ;
            }
        }
        else
        {
            // CHECK IF DISCOUNT IS ENABLED FOR THE PRODUCT THEN FIND OUT THE DISCOUNT AMOUNT.
            if (!empty($product->price) && !empty($otherinfo->discount) && (@strtotime($otherinfo->discount_start_date) <= @time()) && (!empty($otherinfo->discount_permanant) || (@time() < @strtotime($otherinfo->discount_end_date))) && (empty($otherinfo->user_type) || ($otherinfo->user_type == 1 && empty($viewer_id)) || ($otherinfo->user_type == 2 && !empty($viewer_id)))) {
                $information['price']['discount'] = 1;
                $information['price']['discount_amount'] = $otherinfo->discount_amount;
                $information['price']['discount_percentage'] = $otherinfo->discount_percentage;
                $information['price']['discounted_amount'] = $information['price']['price'] - $otherinfo->discount_amount;
            }
        }

        if(!isset($information['price']['discount']))
        {
            $information['price']['discount'] = 0;
            $information['price']['discount_amount'] = 0;
            $information['price']['discount_percentage'] = 0;
            $information['price']['discounted_amount'] = 0;
        }

        if($product->product_type=='grouped')
            $information['price']['price'] = $this->getGroupedProductMinPrice($product);

        $information['price']['in_stock'] = (bool) ($product->stock_unlimited || $product->in_stock);

        return $information;

    }

    /**
     * Get information of product
     *
     * @param product object
     * @return array 
     */
    public function getInformation($product, $values = array()) {


        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $productsTable = Engine_Api::_()->getDbTable('products', 'sitestoreproduct');

        $information = $this->getPriceFields($product);

        $information["weight_unit"] = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.weight.unit', 'lbs');

        // Work for config fields start
        $combinationAttributes = Engine_Api::_()->getDbtable('combinationAttributes', 'sitestoreproduct');
        $combinationAttributesTableName = $combinationAttributes->info('name');

        $select = $combinationAttributes->select()
                ->distinct()
                ->from($combinationAttributesTableName, array('field_id'))
                ->where('product_id = ?', $product->getIdentity());

        $data = $select->query()->fetchALL();

        if (!empty($data)) {
            foreach ($data as $row => $value) {
                $label = Engine_Api::_()->getDbTable("cartproductFieldMeta", "sitestoreproduct")->getFieldLabel($value['field_id']);
                $options = Engine_Api::_()->getDbTable('cartproductFieldOptions', 'sitestoreproduct')->getOptions($value['field_id']);

                $information['configuration'][$value['field_id']]['name'] = $this->translate($label);
                if (!empty($options)) {
                    foreach ($options as $optionrow => $optionvalue) {
                        $information['configuration'][$value['field_id']]['value'][] = $this->translate($optionvalue['label']);
                    }
                }
            }
        }
        // Work for config fields end

        $information['review_count'] = Engine_Api::_()->getDbtable('reviews', 'sitestoreproduct')->totalReviews(array('resource_id' => $product->getIdentity(), 'resource_type' => $product->getType()));
        $profileFields = $this->getInfoFields($product);
        if(!empty($profileFields))
            $information['profileFields'] = $this->getInfoFields($product);

        // Likes count
        $information['like_count'] = $product->likes()->getLikeCount();

        if (!isset($values['page']) && empty($values['page']))
            $values['page'] = 1;

        if (!isset($values['limit']) && empty($values['limit']))
            $values['limit'] = 10;

        if (!isset($values['store_id']))
            $values['store_id'] = $product->store_id;

        if($product->product_type!="downloadable")
        {
            $shippingMethods = Engine_Api::_()->getDbtable('shippingmethods', 'sitestoreproduct')->getShippingMethodsPaginator($values);
            $information['shippingMethods']['totalItemCount'] = $shippingMethods->getTotalItemCount();
        }

        if (!empty($shippingMethods))
        {
            $dependencyArray = array();
            $dependencyArray[0] = $this->translate("Cost");
            $dependencyArray[1] = $this->translate("Weight");
            $dependencyArray[2] = $this->translate("Quantity");

            $chargeType = array();
            $chargeType[0] = "";
            $chargeType[1] = $this->translate("Percentage");
            $chargeType[2] = $this->translate("Per Unit Weight");

            foreach ($shippingMethods as $shippingrow => $shippingvalue)
            {
                $data = array();
                $data['title'] = $this->translate($shippingvalue->title);
                $data['country'] = $shippingvalue->country;
                $data['region'] = $shippingvalue->country != "ALL" ? (empty($shippingvalue->region)) ? $this->translate("All") : $this->translate($shippingvalue->region_name) : "-" ;

                if($shippingvalue->ship_end_limit <= $shippingvalue->ship_start_limit)
                    $shippingvalue->ship_end_limit = "NA";

                if($shippingvalue->allow_weight_to <= $shippingvalue->allow_weight_from)
                    $shippingvalue->allow_weight_to = "NA";

                if($shippingvalue->dependency == 1)
                    $data['weight_limit'] = "-";
                else
                    $data['weight_limit'] = @round($shippingvalue->allow_weight_from, 2) ." - ". (!empty($shippingvalue->allow_weight_to) ? round($shippingvalue->allow_weight_to, 2) . " ".$information['weight_unit']   : $this->translate('NA'));               

                $data['delivery_time'] = $shippingvalue->delivery_time ;
                $data['dependency'] = $dependencyArray[$shippingvalue->dependency];

                if($shippingvalue->dependency==1)
                    $data['limit'] = $shippingvalue->ship_start_limit." ". Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.weight.unit', 'lbs') ." - " .$shippingvalue->ship_end_limit;
                else
                    $data['limit'] = $shippingvalue->ship_start_limit." - ".$shippingvalue->ship_end_limit;

                $data['charge_on'] = ($shippingvalue->dependency==1) ? (!$shippingvalue->ship_type) ? $this->translate("Order Weight") : $this->translate("Per Unit Weight") :  (!$shippingvalue->ship_type) ? $this->translate("Per Order") : $this->translate("Per Item") ;
                
                $data['price_rate'] = array('value' => $shippingvalue->handling_fee , 'type' => $shippingvalue->handling_type , 'note' => 'type = 0 means amount (eg 1$) , 1 means percentage (eg: 1%)');

                $data['shipping_price'] = (!$shippingvalue->ship_type) ? (!$shippingvalue->handling_type) ? $shippingvalue->handling_fee : (($shippingvalue->handling_fee /100) * $product->price) : (ceil($product->weight) * $shippingvalue->handling_fee);
                $data['status'] = $shippingvalue->status;
                $data['creation_date'] = $shippingvalue->creation_date;
                $information['shippingMethods']['methods'][] = $data;
            }
        }

        $otherInfo = Engine_Api::_()->getDbTable('otherInfo', 'sitestoreproduct')->getOtherinfo($product->getIdentity());
        $information['description'] = $otherInfo->overview;


        // get related products
        $tags = $product->tags()->getTagMaps();
        $tagsArray = array();
        foreach($tags as $row => $tag)
            $tagsArray[] = $tag->tag_id ;

        $params = array();
        $params['product_id'] = $product->getIdentity();
        $params['page'] = 1;
        $params['limit'] = 6;
        $params['orderby'] = "rand()";

        if(!empty($tagsArray))
        {
            $params['tags'] = $tagsArray;
        }

        $relatedProducts = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->widgetProductsData($params);

        if(count($relatedProducts)<2)
        {
            unset($params['tags']);
            $params['category_id'] = $product->category_id;
            $relatedProducts = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->widgetProductsData($params);
        }

        $information['relatedProducts']['totalItemCount'] = 0;
        if (!empty($relatedProducts)) {
            $information['relatedProducts']['totalItemCount'] = count($relatedProducts);

            foreach ($relatedProducts as $row => $relatedproduct)
                $information['relatedProducts']['products'][] = $this->getProduct($relatedproduct);

        }

        return $information;
    }

    // out of stock if product is out of stock
    // profile fields
    // shipping details
    // description
    // related products

    /**
     *
     * Message owner form
     *
     * @return array
     */
    public function getMessageOwnerForm() {
        $message = array();

        // Init title
        $message[] = array(
            'type' => 'Text',
            'name' => 'title',
            'label' => $this->translate('Subject'),
            'hasValidators' => 'true'
        );

        // Init body - plain text
        $message[] = array(
            'type' => 'Textarea',
            'name' => 'body',
            'label' => $this->translate('Message'),
        );

        $message[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => $this->translate('Send Message'),
        );
        return $message;
    }

    /*
     *
     */

    public function profileTabs($sitestore, $sitestoreproduct) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $tabsMenu = array();

        $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
        $updates_count = $streamTable->select()
                        ->from($streamTable->info('name'), 'count(*) as count')
                        ->where('object_id = ?', $sitestoreproduct->getIdentity())
                        ->where('object_type = ?', "sitestoreproduct_product")
                        ->where('target_type = ?', "sitestoreproduct_product")
                        ->where('type like ?', "%post%")
                        ->query()->fetchColumn();

        // $tabsMenu[] = array(
        //     'count' => $updates_count,
        //     'name' => 'updates',
        //     'label' => $this->translate('Updates'),
        //     'url' => 'sitestore/updates/' . $sitestoreproduct->getIdentity(),
        // );

        if (strlen($sitestoreproduct->body) > 0) {
            $tabsMenu[] = array(
                'name' => 'overview',
                'label' => $this->translate('Overview'),
                'url' => 'sitestore/product/overview/' . $sitestore->getIdentity() . '/' . $sitestoreproduct->getIdentity(),
            );
        }

        $reviewCount = Engine_Api::_()->getDbtable('reviews', 'sitestoreproduct')->totalReviews(array('resource_id' => $sitestoreproduct->getIdentity(), 'resource_type' => $sitestoreproduct->getType()));

        if ($reviewCount) {
            $tabsMenu[] = array(
                'name' => 'review',
                'label' => $this->translate("User reviews"),
                'count' => $reviewCount,
                'url' => 'sitestore/product/review/browse/'. $sitestoreproduct->getIdentity(),
                'totalItemCount' => $reviewCount
            );
        }

        $photoCount = Engine_Api::_()->getDbTable('photos', 'sitestoreproduct')->GetProductPhoto($sitestoreproduct->getIdentity());

        $photoCount = count($photoCount);
        if ($photoCount) {
            $tabsMenu[] = array(
                'name' => 'photos',
                'count' => $photoCount,
                'label' => $this->translate("Photos"),
                'url' => 'sitestore/product/photos/' . $sitestore->getIdentity() . '/' . $sitestoreproduct->getIdentity(),
                'totalItemCount' => $photoCount
            );
        }


        return $tabsMenu;
    }

    /**
     * Tell a friend Form
     *
     * @return array
     */
    public function getTellAFriendForm() {

        $tell[] = array(
            'type' => 'Text',
            'name' => 'sender_name',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Your Name'),
            'hasValidator' => 'true'
        );

        $tell[] = array(
            'type' => 'Text',
            'name' => 'sender_email',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Your Email'),
            'has Validator' => 'true'
        );

        $tell[] = array(
            'type' => 'Text',
            'name' => 'receiver_emails',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('To'),
            'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Separate multiple addresses with commas'),
            'hasValidators' => 'true'
        );

        $tell[] = array(
            'type' => 'Textarea',
            'name' => 'message',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Message'),
            'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('You can send a personal note in the mail.'),
            'hasValidator' => 'true',
        );

        $tell[] = array(
            'type' => 'Checkbox',
            'name' => 'send_me',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Send a copy to my email address."),
        );


        $tell[] = array(
            'type' => 'Submit',
            'name' => 'send',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Tell a Friend'),
        );

        $response = array();
        $response['form'] = $tell;
        return $response;
    }

    // public function getAddToWishlistForm($product_id) {
    //     $viewer = Engine_Api::_()->user()->getViewer();
    //     $viewer_id = $viewer->getIdentity();
    //     $wishlistDatas = Engine_Api::_()->getDbtable('wishlists', 'sitestoreproduct')->getUserWishlists($viewer_id);
    //     $wishlistDatasCount = Count($wishlistDatas);
    //     $product_id = $_REQUEST['product_id'];
    //     $product = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

    //     $wishlistIdsDatas = Engine_Api::_()->getDbtable('wishlistmaps', 'sitestoreproduct')->pageWishlists($product_id, $viewer_id);

    //     if (!empty($wishlistIdsDatas)) {
    //         $wishlistIdsDatas = $wishlistIdsDatas->toArray();
    //         $wishlistIds = array();
    //         if (_CLIENT_TYPE && (_CLIENT_TYPE == 'ios') && $wishlistDatasCount > 0) {
    //             $add[] = array(
    //                 "type" => "Label",
    //                 "name" => "add_wishlist_description",
    //                 "label" => $this->translate('Please select the wishlists in which you want to add this Product.')
    //             );
    //         }
    //         foreach ($wishlistIdsDatas as $wishlistIdsData) {
    //             $wishlistIds[] = $wishlistIdsData['wishlist_id'];
    //         }
    //     }

    //     foreach ($wishlistDatas as $wishlistData) {

    //         if (in_array($wishlistData->wishlist_id, $wishlistIds)) {
    //             $add[] = array(
    //                 'type' => 'Checkbox',
    //                 'name' => 'inWishlist_' . $wishlistData->wishlist_id,
    //                 'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($wishlistData->title),
    //                 'value' => 1,
    //             );
    //         } else {
    //             $add[] = array(
    //                 'type' => 'Checkbox',
    //                 'name' => 'wishlist_' . $wishlistData->wishlist_id,
    //                 'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($wishlistData->title),
    //                 'value' => 0,
    //             );
    //         }
    //     }
    //     if (_CLIENT_TYPE && (_CLIENT_TYPE == 'ios') && $wishlistDatasCount > 0) {
    //         $add[] = array(
    //             "type" => "Label",
    //             "name" => "create_wishlist_description",
    //             "label" => $this->translate('You can also add this product in a new wishlist below:')
    //         );
    //     } else {
    //         $add[] = array(
    //             "type" => "Label",
    //             "name" => "create_wishlist_description",
    //             "label" => $this->translate('You have not created any wishlist yet. Get Started by creating and adding producs.')
    //         );
    //     }

    //     if ($wishlistDatasCount) {
    //         $add[] = array(
    //             'type' => 'Text',
    //             'name' => 'title',
    //             'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Wishlist Name'),
    //         );
    //     } else {
    //         $add[] = array(
    //             'type' => 'Text',
    //             'name' => 'title',
    //             'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Wishlist Name'),
    //             'hasValidator' => 'true'
    //         );
    //     }

    //     $add[] = array(
    //         'type' => 'Textarea',
    //         'name' => 'body',
    //         'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Description'),
    //     );

    //     $availableLabels = array(
    //         'everyone' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone'),
    //         'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
    //         'owner_network' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends and Networks'),
    //         'owner_member_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends of Friends'),
    //         'owner_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends Only'),
    //         'owner' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me')
    //     );

    //     $viewer = Engine_Api::_()->user()->getViewer();
    //     $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('siteevent_wishlist', $viewer, 'auth_view');
    //     $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
    //     $viewOptionsReverse = array_reverse($viewOptions);
    //     $orderPrivacyHiddenFields = 786590;

    //     if (count($viewOptions) > 1) {
    //         $add[] = array(
    //             'type' => 'Select',
    //             'name' => 'auth_view',
    //             'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('View Privacy'),
    //             'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may see this wishlist?'),
    //             'multiOptions' => $viewOptions,
    //             'value' => key($viewOptionsReverse),
    //         );
    //     }

    //     $add[] = array(
    //         'type' => 'Submit',
    //         'name' => 'submit',
    //         'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Save'),
    //     );
    //     return $add;
    // }

    private function orderInvoice($order)
    {
        $billing_address = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct')->getAddress($order->order_id, false);
    $shipping_address = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct')->getAddress($order->order_id, true);
    $order_products = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct')->getOrderProductsDetail($order->order_id);
    $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);
    $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
    $site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');
    
    if( !empty($directPayment) ) {
      $this->view->storeTitle = Engine_Api::_()->getDbtable('stores', 'sitestore')->getStoreAttribute($order->store_id, 'title');
      $this->view->storeChequeDetail = Engine_Api::_()->getDbtable('sellergateways', 'sitestoreproduct')->getStoreChequeDetail(array('store_id' => $order->store_id, "title = 'ByCheque'", "enabled = 1"));
    } else {
      $this->view->admin_cheque_detail = Engine_Api::_()->getApi('settings', 'core')->getSetting('send.cheque.to', null);
    }


    $invoice = '<div style="overflow:hidden"><div style="width:600px;margin:0 auto;"><div style="font-family:tahoma,arial,verdana,sans-serif;font-size:10pt;background-color:#EAEAEA;border:1px solid #CCCCCC;height:40px;line-height:39px;padding:2px 10px;"><div><div style="float:left;height:40px;max-height:40px;width:450px;font-size: 13pt;"><b> ';
    
    // FETCH TITLE OR LOGO
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_pages')->where('name = ?', 'header')->limit(1);

    $info = $select->query()->fetch();
    if( !empty($info) )
    {
      $page_id = $info['page_id'];

      $select = new Zend_Db_Select($db);
      $select->from('engine4_core_content', array("params"))
             ->where('page_id = ?', $page_id)
             ->where("name LIKE '%core.menu-logo%'")
             ->limit(1);
      $info = $select->query()->fetch();
      $params = json_decode($info['params']);
    }

    if( !empty($params) && !empty($params->logo) ) {
      $getBaseUrl = trim(Zend_Controller_Front::getInstance()->getBaseUrl(),'/');
      $getHost = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];

      $invoice .= '<img style="max-height:40px;" src="' . $getHost . '/' . $getBaseUrl . '/' . $params->logo . '" alt="' . $site_title . '" />';
    }else
      $invoice .= $site_title;
    
    $invoice .= '</b></div><div style="float:right;font-size: 13pt;"><strong> '.$this->translate("INVOICE").' </strong></div></div></div><div style="font-family:tahoma,arial,verdana,sans-serif;font-size:10pt;border:1px solid #CCCCCC;overflow:hidden;"><div style="border-right:1px solid #CCCCCC;float:left;width:298px;"><div style="padding: 10px;"><b>'.$this->translate("Store Name & Address") . '</b><br />'.Engine_Api::_()->sitestoreproduct()->getStoreAddress($order->store_id).'</div><div style="padding: 10px;border-top:1px solid #CCC;"><b>'.$this->translate("Name & Billing Address") . '</b><br />'.$billing_address->f_name.' '. $billing_address->l_name . '<br />'.$billing_address->address . '<br />'.@strtoupper($billing_address->city) . ' - ' . $billing_address->zip . '<br />'.@strtoupper(Zend_Locale::getTranslation($billing_address->country, "country")) . '<br />'.@strtoupper(Engine_Api::_()->getItem("sitestoreproduct_region", $billing_address->state)->region) .'<br />'.$this->translate("Ph: %s", $billing_address->phone) . '<br />';

//    if( empty($order->buyer_id) )
//        $invoice .= $billing_address->email . '<br /><br /><br />';

    $invoice .= '</div><div style="padding: 10px;border-top:1px solid #CCC;"><b>'.$this->translate("Name & Shipping Address") . '</b><br />'.$shipping_address->f_name . ' ' .$shipping_address->l_name . '<br />'.$shipping_address->address . '<br />'.@strtoupper($shipping_address->city) . ' - ' . $shipping_address->zip . '<br />'.@strtoupper(Zend_Locale::getTranslation($shipping_address->country, "country")) . '<br />'. @strtoupper(Engine_Api::_()->getItem("sitestoreproduct_region", $shipping_address->state)->region) .'<br />'.$this->translate("Ph: %s", $shipping_address->phone).'</div></div><div style="float: right; width: 298px; border-left: 1px solid #ccc; margin-left: -1px;"><ul style="padding:0;margin:0;">';
    
    // SET TIMEZONE AND DATETIME FORMATTING WORK START
    $tz = Engine_Api::_()->getApi('settings', 'core')->core_locale_timezone;
    if (!empty($viewer_id)) {
        $tz = $viewer->timezone;
    }

    $startDateObject = new Zend_Date(strtotime($order->creation_date));
    $startDateObject->setTimezone($tz);
    $dates['starttime'] = $startDateObject->get('YYYY-MM-dd HH:mm:ss');
    $date=date_create($dates['starttime']);
    $date = date_format($date,"M d, Y h:m a");

    // SET TIMEZONE AND DATETIME FORMATTING WORK ENDS

    $invoice .= '<li style="border-bottom: 1px solid #CCCCCC;list-style:none;padding:10px;margin:0;"><b>'.$this->translate("Order #%s", $order->order_id).'</b></li><li style="border-bottom: 1px solid #CCCCCC;list-style:none;padding:10px;margin:0;"><div style="width: 128px;float:left;"> <b>'.$this->translate("Status").'  </b> </div><div>: &nbsp;'. $this->getOrderStatus($order->order_status) . '<br/> </div></li><li style="border-bottom: 1px solid #CCCCCC;list-style:none;padding:10px;margin:0;"><div style="width: 128px;float:left;"> <b> '.$this->translate("Placed on").' </b> </div><div>: &nbsp;'. $date .'<br/> </div></li><li style="border-bottom: 1px solid #CCCCCC;list-style:none;padding:10px;margin:0;"><div style="width: 128px;float:left;"> <b> '. $this->translate("Payment Method").' </b> </div><div>: &nbsp; '. $this->translate(Engine_Api::_()->sitestoreproduct()->getGatwayName($order->gateway_id)) .' <br/> </div></li>';
 
    if( $order->shipping_title )
      $invoice .= '<li style="border-bottom: 1px solid #CCCCCC;list-style:none;padding:10px;margin:0;"><div style="width: 128px;float:left;"> <b> '.$this->translate("Shipping Method").' </b> </div><div>: &nbsp; '.$order->shipping_title .'<br/></div></li>';
    
    if( !empty($isDownPaymentEnable) && !empty($order->is_downpayment) ) {
      $tempColumn = '<th style="text-align:center;padding:7px 10px;width:128px;"> '.$this->translate("Downpayment Amount").' </th><th style="text-align:center;padding:7px 10px;width:128px;"> '.$this->translate("Remaining Amount").' </th>';
    } else {
      $tempColumn = '';
    }
    
    if( $order->gateway_id == 3 ) {
      $admin_cheque_detail = Engine_Api::_()->getApi('settings', 'core')->getSetting('send.cheque.to', null);
      $storeTitle = Engine_Api::_()->getDbtable('stores', 'sitestore')->getStoreAttribute($order->store_id, 'title');
      $storeChequeDetail = Engine_Api::_()->getDbtable('sellergateways', 'sitestoreproduct')->getStoreChequeDetail(array('store_id' => $order->store_id));
      $cheque_info = Engine_Api::_()->getDbtable('ordercheques', 'sitestoreproduct')->getChequeDetail($order->cheque_id);
      if( empty($order->direct_payment) && !empty($site_title) && !empty($admin_cheque_detail) ) {
        $invoice .= '<li style="border-bottom: 1px solid #CCCCCC;list-style:none;padding:10px;margin:0;"><b>'.$this->translate("%s's Bank Account Details", $site_title).'</b><div>'.$admin_cheque_detail.'</div></li>';
      } elseif( !empty($order->direct_payment) && !empty($storeTitle) && !empty($storeChequeDetail) ) {
        $invoice .= '<li style="border-bottom: 1px solid #CCCCCC;list-style:none;padding:10px;margin:0;"><b>'.$this->translate("%s store's Bank Account Details", $storeTitle).'</b><div>'.$storeChequeDetail.'</div></li>';
      }
      $invoice .= '<li style="border-bottom: 1px solid #CCCCCC;list-style:none;padding:10px;margin:0;"><b>'.$this->translate("Buyer Account Info").'</b><div style="overflow:hidden;"><div style="clear:both;"><div style="width:170px; float:left">'.$this->translate("Cheque No").'</div><div>: &nbsp;'.$cheque_info["cheque_no"].'</div></div><div style="clear:both;"><div style="width:170px; float:left">'.$this->translate("Account Holder Name").'</div><div>: &nbsp;'.$cheque_info["customer_signature"].'</div></div><div style="clear:both;"><div style="width:170px; float:left">'.$this->translate("Account Number").'</div><div>: &nbsp;'.$cheque_info["account_number"].'</div></div><div style="clear:both;"><div style="width:170px; float:left">'.$this->translate("Bank Rounting Number").'</div><div>: &nbsp;'.$cheque_info["bank_routing_number"].'</div></div></div></li>';
    }

    $invoice .= '</ul></div></div><b style="margin:10px 0 5px;display:block;">' . $this->translate("Order Details") . '</b><div id="manage_order_tab" style="font-family:tahoma,arial,verdana,sans-serif;font-size:10pt;overflow-x:auto;width: 100%;"><div style="border:none;margin:0 0 10px;float:left;"><table style="border: 1px solid #CCCCCC;margin-top: 1px;width: 100%;">     <tr style="background-color:#EAEAEA;"><th style="text-align:center;padding:7px 10px;width:252px;"> '.$this->translate("Product").' </th><th style="text-align:center;padding:7px 10px;width:128px;">'.$this->translate("Quantity").'</th><th style="text-align:center;padding:7px 10px;width:128px;"> '.$this->translate("Unit Price").' </th>' . $tempColumn . '<th style="text-align:center;padding:7px 10px;width:128px;"> '.$this->translate("Total").' </th></tr>';

    foreach( $order_products as $product ){
      
      $temp_lang_title = Engine_Api::_()->sitestoreproduct()->getProductTitle($product->product_title);

      $invoice .= '<tr><td title="'. $temp_lang_title .'" style="text-align:center;padding:7px 10px;">'. Engine_Api::_()->sitestoreproduct()->truncation($temp_lang_title, 40);
      if( !empty($product->order_product_info) ) {
        $order_product_info = unserialize($product->order_product_info);
      }
      if( !empty($order_product_info) && !empty($order_product_info['calendarDate']) && !empty($order_product_info['calendarDate']['starttime']) && !empty($order_product_info['calendarDate']['endtime']) ) {
        $invoice .=  '<br /><b>' . $this->translate("From: ") . '</b>' . $this->view->locale()->toDate($order_product_info['calendarDate']['starttime']) . '<br />';
        $invoice .=  '<b>' . $this->translate("To: ") . '</b>' . $this->view->locale()->toDate($order_product_info['calendarDate']['endtime']);
      }
      
      if( !empty($isDownPaymentEnable) && !empty($order->is_downpayment) ) {
        $downPaymentPrice = '<td style="text-align:center;padding:7px 10px;">' . Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($product->downpayment * $product->quantity) . '</td><td style="text-align:center;padding:7px 10px;">' . Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency((($product->price * $product->quantity) - ($product->downpayment * $product->quantity))) . '</td>';
      } else {
        $downPaymentPrice = '';
      }
      
      if( !empty($order_product_info) && !empty($order_product_info['price_range_text']) ) {
        $priceRangeText = $this->translate($order_product_info['price_range_text']);
      } else {
        $priceRangeText = '';
      }
      
      if( !empty($product->configuration) ){
        $configuration = Zend_Json::decode($product->configuration);
        $invoice .= '<br/>';
        foreach($configuration as $config_name => $config_value)
         $invoice .= "<b>".$config_name."</b>: $config_value<br/>";
    }
    
      $invoice .= '</td><td style="text-align:center;padding:7px 10px;"> '.$product->quantity.' </td><td style="text-align:center;padding:7px 10px;"> '.Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($product->price). ' ' . $priceRangeText . ' </td>' . $downPaymentPrice . '<td style="text-align:center;padding:7px 10px;"><b>'.Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($product->price * $product->quantity).' </b></td></tr>';
    }
    
    if( !empty($isDownPaymentEnable) && !empty($order->is_downpayment) ) {
      $remainingAmount = $order->grand_total - ($order->downpayment_total + $order->store_tax + $order->admin_tax + $order->shipping_price);
      $tempInfo = '<div style="clear:both;"><div style="float:left;font-weight:bold;">'.$this->translate("Downpayment Grand Total").' &nbsp;&nbsp;</div><div style="float:right;">'.Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($order->downpayment_total).'</div></div><div style="clear:both;"><div style="float:left;font-weight:bold;">'.$this->translate("Remaining Amount Grand Total").' &nbsp;&nbsp;</div><div style="float:right;">'.Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($remainingAmount).'</div></div>';
    } else {
      $tempInfo = '';
    }

    $invoice .= '</table></div></div><div><b style="margin:10px 0 5px;display:block;">' . $this->translate("Order Summary") . '</b></div><div style="font-family:tahoma,arial,verdana,sans-serif;font-size:10pt;background-color:#EAEAEA;border:1px solid #CCCCCC;padding:10px;margin-bottom:10px;float:right;width:300px;"><div style="margin-bottom:5px;overflow:hidden;"><div style="clear:both;"><div style="float:left;"> <b> '.$this->translate("Subtotal").' </b> </div><div style="float:right;">'.Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($order->sub_total) .' <br/></div></div><div style="clear:both;"><div style="float:left;"><b> '.$this->translate("Taxes").' </b></div><div style="float:right;">'.Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($order->store_tax + $order->admin_tax)) .'<br/> </div></div><div style="clear:both;"><div style="float:left;"><b>'.$this->translate("Shipping price").'</b></div><div style="float:right;">'.Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($order->shipping_price) .'<br/></div></div></div><div>' . $tempInfo . '<div style="clear:both;"><div style="float:left;"><h2 style="margin:5px 0 0;font-size:20px;"> '.$this->translate("Grand Total").' &nbsp;&nbsp;</h2></div><div style="float:right;"><h2 style="margin:5px 0 0;font-size:20px;">'.Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($order->grand_total).'</h2></div></div></div></div>';

    if(!empty($order->order_note)):
      $invoice .= '<div style="float:left"><div style="margin-bottom: 10px;border:1px solid #CCCCCC;width:270px;clear:both;padding:10px;"><div style="margin-bottom:2px;"><b>'. $this->translate("Buyer Note:").' </b></div>'.Engine_Api::_()->sitestoreproduct()->truncation($order->order_note, 310).'</div></div>';
    endif; 

    $invoice .= '</div></div></div>';
    
    //WORK FOR SHOWING THE PROFILE FIELDS OF STORE
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $order->store_id);
    if(!empty($sitestore))
      $storefieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitestore);
    //$this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitestore/View/Helper', 'Sitestore_View_Helper');
    // $profileFields = $this->view->billFieldValueLoop($sitestore, $storefieldStructure, true);
    // if (!empty($profileFields)) :
    //   $invoice.='<div style="overflow:hidden">';
    //   $invoice.='<div  style="margin: 10px auto;  width: 600px;">';
    //   $invoice .='<div  style="padding: 5px; width: 588px; border: 1px solid #ccc; margin: 5px auto;">' . $profileFields . '</div>';
    //   $invoice.='</div></div>';
    // endif;

    return $invoice;
    }

    public function getGroupedProductMinPrice($product)
    {
        $isVatAllow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0) ;
        $productTable = Engine_Api::_()->getDbtable("products","sitestoreproduct");
        $params = array();
        $params['product_type'] = 'grouped';
        $params['product_id'] = $product->getIdentity();

        $groupedProducts = $productTable->getCombinedProducts($params);

        $minprice = 0.00;
        foreach ($groupedProducts as $individualProduct) {
            $price = $productTable->getProductDiscountedPrice($individualProduct->product_id);

            if($isVatAllow)
            {
                $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($individualProduct);
                $price = $productPricesArray['product_price_after_discount'];
            }
            
            if($minprice==0.00 || $minprice>$price)
                $minprice = $price;
        }

        return $minprice;
    }

    private function getOrderStatus($status)
    {
        $statusArray = array();
        $statusArray['1'] = $this->translate("Payment Pending");
        $statusArray['2'] = $this->translate("Processing");
        $statusArray['3'] = $this->translate("On Hold");
        $statusArray['4'] = $this->translate("Fraud");
        $statusArray['5'] = $this->translate("Completed");
        $statusArray['6'] = $this->translate("Canceled");
        $statusArray['0'] = $this->translate("Approval Pending");

        return $statusArray[$status];
    }

    /**
     * Translate the text from english to specified language by user
     *
     *  @param message string
     *   @return string
     */
    private function translate($message) {
        return Engine_Api::_()->getApi('Core', 'siteapi')->translate($message);
    }
}
