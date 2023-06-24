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
class Sitestore_ManageorderController extends Siteapi_Controller_Action_Standard {

	public $_statusArray = array();

	public function init()
	{

		$this->_statusArray['1'] = $this->translate("Payment Pending");
        		$this->_statusArray['2'] = $this->translate("Processing");
        		$this->_statusArray['3'] = $this->translate("On Hold");
        		$this->_statusArray['4'] = $this->translate("Fraud");
        		$this->_statusArray['5'] = $this->translate("Completed");
        		$this->_statusArray['6'] = $this->translate("Canceled");
                $this->_statusArray['0'] = $this->translate("Approval Pending");

		$viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // IF USER SELECT CHECKOUT AS A REGISTERED MEMBER
        if (empty($viewer_id)) {
            $this->respondWithError('unauthorized');
        }

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();

	}

    /* Search api */
    public function searchFormAction()
    {
        $this->validateRequestMethod();
        if($this->getRequest()->isGet())
            $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'sitestore')->getManageSearchForm() , false);
    }

	public function indexAction()
	{

		$viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $orderProductTable = Engine_Api::_()->getDbTable('orderProducts' , 'sitestoreproduct');
        $storeTableObj = Engine_Api::_()->getDbtable('stores', 'sitestore');

        $remainingAmountGateways = unserialize(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.remainingpaymentgateway', serialize(array('paypal', 'cheque', 'cod'))));

        $onlyCodGatewayEnable = false;
        if (!empty($remainingAmountGateways) && count($remainingAmountGateways) == 1 && in_array('cod', $remainingAmountGateways)) {
            $onlyCodGatewayEnable = true;
        }

        $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);
        $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();

        $values = $this->_getAllParams();

        if(!isset($values['page']) || empty($values['page']))
        	$values['page'] = 1;

        if(!isset($values['limit']) || empty($values['limit']))
        	$values['limit'] = 20;

        $values['buyer_id'] = $viewer_id;

        $viewerOrdersPaginator = Engine_Api::_()->getDbTable('orders' , 'sitestoreproduct')->getOrdersPaginator($values);

        $response['totalItemCount'] = $viewerOrdersPaginator->getTotalItemCount();
        $response['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD') ;

        if($response['totalItemCount'])
        {
        	foreach ($viewerOrdersPaginator as $key => $order) {

        		$tempArray = array();
        		$tempArray['order_id'] = $order->getIdentity();
        		$billingAddress = Engine_Api::_()->getDbTable('Orderaddresses','sitestoreproduct')->getAddress($order->getIdentity(), false , array('address_type' => '0')) ;
        		$shippingAddress = Engine_Api::_()->getDbTable('Orderaddresses','sitestoreproduct')->getAddress($order->getIdentity(), false , array('address_type' => '1')) ;

        		$tempArray['billing_name'] = $billingAddress->f_name." ".$billingAddress->l_name;
        		$tempArray['shipping_name'] = $shippingAddress->f_name." ".$shippingAddress->l_name;

        		// $tempArray['creation_date'] =  Engine_Api::_()->getApi('core','siteapi')->getBeautifyDate($order->creation_date);
                $tempArray['creation_date'] = $order->creation_date ;
        		$orderProducts = Engine_Api::_()->getDbTable('OrderProducts' , 'sitestoreproduct')->getOrderProducts($order->getIdentity());

        		$orderQuantity = 0;
        		foreach($orderProducts->toArray() as $row => $value)
        			$orderQuantity += $value['quantity'];

        		$tempArray['qty'] = $orderQuantity;

        		$tempArray['order_total'] = $order->grand_total;

        		$tempArray['order_status'] = $this->_statusArray[$order->order_status];

        		if($order->order_status == 8)
        			$tempArray['payment_status'] = $this->translate("marked as non-payment");
        		else if($order->payment_status == 'active')
        			$tempArray['payment_status'] = $this->translate("Yes");
        		else
        			$tempArray['payment_status'] = $this->translate('No');

        		$tempArray['delivery_time'] = '-';
        		if($order->order_status == 2 || $order->order_status == 3 || $order->order_status == 4)
        			$tempArray['delivery_time'] = empty($order->delivery_time) ? "-" : $order->delivery_time;

        		$tempArray['delivery_time'] = Engine_Api::_()->sitestoreproduct()->truncation($tempArray['delivery_time'], 12);

                // OPTIONS URL STARTS 
                $anyOtherProducts = $orderProductTable->checkProductType(array('order_id' => $order->order_id, 'virtual' => true));
                $bundleProductShipping = $orderProductTable->checkBundleProductShipping(array('order_id' => $order->order_id));
                $isStoreExist = $storeTableObj->getStoreAttribute($order->store_id, 'store_id');

        		$tempArray['options'] = array();
        		$tempArray['options'][] = array(
    					'name' => 'view',
    					'label' => $this->translate("View"),
    					'url' => 'sitestore/orders/view/'.$order->getIdentity(),
    			);


                if( !empty($isStoreExist) && !empty($anyOtherProducts) )
                {
                    $tempArray['options'][] = array(
                        'name' => 'reorder',
                        'label' => $this->translate("Re-order"),
                        'url' => 'sitestore/orders/reorder/'.$order->getIdentity(),
                    );
                }

        		if(preg_match('/^[1-4|6-9]$/' , $order->order_status))
        		{
        			
        			if( !empty($anyOtherProducts) && empty($bundleProductShipping) )
        			{
                        $shipTrackObj = Engine_Api::_()->getDbtable('shippingtrackings', 'sitestoreproduct')->getShipTracks($order->getIdentity());
                        $shipTrackArray = $shipTrackObj->toArray();
                        if(!empty($shipTrackArray))
                        {
                            $tempArray['options'][] = array(
                                'name' => 'shipping',
                                'label' => $this->translate('Shipping Details'),
                                'url' => 'sitestore/orders/order-ship/'.$order->getIdentity(),
                            );
                        }
        			}
        		}

        		// if( preg_match('/^[0-4|6-9]$/' , $order->order_status) && !empty(Engine_Api::_()->sitestore()->isManageAdmin(Engine_Api::_()->getItem('sitestore_store',$order->store_id), 'edit')) )
        		// {                    
        		// 	$tempArray['options'][] = array(
        		// 			'name' => 'cancel',
        		// 			'label' => $this->translate("Cancel Order"),
        		// 			'url' => 'sitestore/orders/cancel/'.$order->getIdentity(),
        		// 		);
        		// }

                $makePaymentText = "";

                if( empty($directPayment) && empty($isDownPaymentEnable) && empty($order->is_downpayment) && empty($order->direct_payment) && (($order->gateway_id == 1 || $order->gateway_id == 2) && $order->payment_status != 'active') )
                    $makePaymentText = $this->translate("Make Payment");
                elseif(!empty($isDownPaymentEnable) && empty($onlyCodGatewayEnable) && $order->is_downpayment == 1 && ( (($order->gateway_id == 1 || $order->gateway_id == 2) && $order->payment_status == 'active') || $order->gateway_id == 3 || $order->gateway_id == 4 ))
                    $makePaymentText = $this->translate("Pay Remaining Amount");

                if(!empty($makePaymentText))
                {
                    $host = $this->getSiteUrl();

                    $url = $this->_helper->url->url(array('module' => 'sitestoreproduct', 'controller' => 'index', 'action' => 'make-payment', 'order_id' => $order->order_id), 'default', true);

                    $tempArray['options'][] = array(
                        'name'  => 'make-payment',
                        'label' => $this->translate($makePaymentText),
                        'url' => $host.$url,
                    );
                }

                // OPTIONS URL STARTS 

        		$response['orders'][] = $tempArray;

        	}
        }

        $this->respondWithSuccess($response , false);
	}

    public function downloadableFilesAction()
    {
        $this->validateRequestMethod();
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if(!$viewer_id)
            $this->respondWithError('unauthorized');

        $params = array();
        $params['buyer_id'] = $viewer_id;
        $params['page'] = $this->_getParam('page', 1);
        $params['limit'] = 20;

        $downloadableFiles = Engine_Api::_()->getDbtable('orderdownloads', 'sitestoreproduct')->getOrderDownloadsPaginator($params);

        $response = array();
        $response['totalItemCount'] = $downloadableFiles->getTotalItemCount();

        foreach($downloadableFiles as $row => $file)
        {
            $tempArray = array();
            $tempArray['order_id'] = $file->order_id;
            $tempArray['orderdownload_id'] = $file->orderdownload_id;
            $tempArray['downloads'] = $file->downloads;
            $tempArray['remainingDownloads'] = $file->max_downloads - $file->downloads;
            $tempArray['title'] = $this->translate($file->title);

            if($file->order_status!=5)
                $tempArray['option'] = $this->_statusArray[$file->order_status];
            elseif($tempArray['remainingDownloads'])
            {
                $getOauthToken = Engine_Api::_()->getApi('oauth', 'siteapi')->getAccessOauthToken($viewer);
                $host = $this->getSiteUrl();
                // $fileurl = $this->_helper->url->url(array('product_id' => Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($file->product_id), 'downloadablefile_id' => Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($file->downloadablefile_id), 'download_id' => Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($file->orderdownload_id)), 'sitestoreproduct_downloads', true);

                $fileurl[] = array(
                    'url' => 'sitestore/orders/download',
                    'urlparams' => array(
                            'product_id' => Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($file->product_id),
                            'downloadablefile_id' => Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($file->downloadablefile_id),
                            'download_id' => Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($file->orderdownload_id),
                        ),
                );
                // $tempArray['option'] = $host.$fileurl;
                // $tempArray['token'] = $getOauthToken['token'];
                $tempArray['option'] = $fileurl;
                $tempArray['filename'] = $file->filename;
                $tempArray['extension'] = $file->extension;
                $tempArray['size'] = $file->size;
            }
            elseif(!$tempArray['remainingDownloads'])
                $tempArray['option'] = $this->translate("Max download limit reached");

            $response['downloadablefiles'][] = $tempArray;
        }

        $this->respondWithSuccess($response , false);
    }

    /* increase the download limit count */
    public function incrementDownloadAction()
    {
        $this->validateRequestMethod("POST");
        $orderdownload_id = $this->_getParam("orderdownload_id" , 0);

        if(!$orderdownload_id)
            $this->respondWithValidationError('parameter_missing' , 'orderdownload_id missing');

        $downloadablefile = Engine_Api::_()->getItem('sitestoreproduct_orderdownload' , $orderdownload_id) ;

        if(!$downloadablefile)
            $this->respondWithError("no_record");

        try
        {
            $downloadablefile->downloads += 1;
            $downloadablefile->save();
            $this->successResponseNoContent("no_content");
        }
        catch(Exception $e)
        {
            $this->respondWithError('internal_server_error', $e->getMessage());
        }

    }

	public function viewAction()
	{

		$viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $orderProductTable = Engine_Api::_()->getDbTable('orderProducts' , 'sitestoreproduct');
        $regionsTable = Engine_Api::_()->getDbTable('regions' , 'sitestoreproduct');
        $commentsTable = Engine_Api::_()->getDbTable('orderComments' , 'sitestoreproduct');

        $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
        $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);

        $values = $this->_getAllParams();

        $order_id = $this->_getParam('order_id', null);

        if(!isset($order_id) || empty($order_id))
        	$this->respondWithValidationError('validation_fail', "order_id is required");

        $order = Engine_Api::_()->getItem('sitestoreproduct_order', $order_id);

        if(!$order)
        	$this->respondWithError('no_record');

        // GETTING ORDER DETAILS
        $response = array();

        $response['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD') ;

        // $response['order']['order_date'] = Engine_Api::_()->getApi('core','siteapi')->getBeautifyDate($order->creation_date) ;
        $response['order']['order_date'] = $order->creation_date;
        $response['order']['order_status'] = $this->_statusArray[$order->order_status] ;
        $response['order']['tax_amount'] = $order->store_tax + $order->admin_tax ;
        $response['order']['shipping_amount'] = $order->shipping_price ;

        $response['order']['delivery_time'] = '-';
    		if($order->order_status == 2 || $order->order_status == 3 || $order->order_status == 4)
    			$response['order']['delivery_time'] = empty($order->delivery_time) ? "-" : $order->delivery_time;

        $response['order']['delivery_time'] = Engine_Api::_()->sitestoreproduct()->truncation($response['order']['delivery_time'], 12);

        $response['order']['ip_address'] = urlencode($order->ip_address);
        $response['order']['label'] = $this->translate("Order Information");

        $billingAddress = Engine_Api::_()->getDbTable('Orderaddresses','sitestoreproduct')->getAddress($order->getIdentity(), false , array('address_type' => '0')) ;

        $shippingAddress = Engine_Api::_()->getDbTable('Orderaddresses','sitestoreproduct')->getAddress($order->getIdentity(), false , array('address_type' => '1')) ;

        // BILLING ADDRESS
        if(!empty($billingAddress))
        {
            $response['billing_address'][] = $this->translate("Name & Billing Address");
            $response['billing_address'][] = $this->translate($billingAddress->f_name." ".$billingAddress->l_name);
            $response['billing_address'][] = $this->translate($billingAddress->address);
            $response['billing_address'][] = $this->translate(Engine_Api::_()->getItem('sitestoreproduct_region' , $billingAddress->state)->region);
            $response['billing_address'][] = $this->translate($billingAddress->city);
            $response['billing_address'][] = $this->translate($billingAddress->zip);
            $response['billing_address'][] = $this->translate($billingAddress->phone);
        }

        // SHIPPING ADDRESS
        if(!empty($shippingAddress))
        {
            $response['shipping_address'][] = $this->translate("Name & Shipping Address");
            $response['shipping_address'][] = $this->translate($shippingAddress->f_name." ".$shippingAddress->l_name);
            $response['shipping_address'][] = $this->translate($shippingAddress->address);
            $response['shipping_address'][] = $this->translate(Engine_Api::_()->getItem('sitestoreproduct_region' , $shippingAddress->state)->region);
            $response['shipping_address'][] = $this->translate($shippingAddress->city);
            $response['shipping_address'][] = $this->translate($shippingAddress->zip);
            $response['shipping_address'][] = $this->translate($shippingAddress->phone);
        }

        // PAYMENT INFORMATION
        if(Engine_Api::_()->sitestoreproduct()->getGatwayName($order->gateway_id))
        {
            $response['payment']['label'] = $this->translate("Payment Information");
            $response['payment']['payment_method'] = $this->translate(Engine_Api::_()->sitestoreproduct()->getGatwayName($order->gateway_id));
            if($order->cheque_id)
            {
                $orderCheques = Engine_Api::_()->getDbtable('Ordercheques' , 'sitestoreproduct')->getChequeDetail($order->cheque_id);
                if(!empty($orderCheques))
                {
                    $response['payment']['cheque_no'] = $orderCheques['cheque_no'];
                    $response['payment']['account_no'] = $orderCheques['account_number'];
                    $response['payment']['account_holdername'] = $orderCheques['customer_signature'];
                    $response['payment']['rounting_number'] = $orderCheques['bank_routing_number'];                    
                }
            }
        }

        // SHIPPING INFORMATION
        // $response['shipping']['label'] = $this->translate("Shipping Information");
        // $shippingTracks = Engine_Api::_()->getDbtable('Shippingtrackings' , 'sitestoreproduct')->getShipTracks($order->getIdentity());
        // $shippingTracksArray = array();
        // foreach($shippingTracks as $row => $value)
        //     $response['shipping']['tracking'][] = $value->toArray();

        if($order->shipping_title)
        {
            $response['shipping'] = array();
            $response['shipping']['label'] = $this->translate("Shipping Information");
            $response['shipping']['name'] = $this->translate($order->shipping_title);
        }

       	// ORDER DETAIL
       	$orderProducts = $orderProductTable->getOrderProducts($order_id);

        $totalQuantity =0;

       	if(!empty($orderProducts))
       	{
       		foreach($orderProducts as $row => $orderproduct)
       		{
       			$tempArray = array();

                $product = Engine_Api::_()->getItem("sitestoreproduct_product" , $orderproduct['product_id']);

       			$product_title = unserialize($orderproduct->product_title) ;

                $store_id = $product->store_id;

       			$tempArray['title'] = $product_title['title'];
                $tempArray['unitPrice'] = $orderproduct->price;

       			if(!empty($orderproduct->configuration))
       				$tempArray['config'] = Zend_Json::decode($orderproduct->configuration);

       			$tempArray['product_sku'] = $orderproduct->product_sku;
       			$tempArray['quantity'] = $orderproduct->quantity;
                $totalQuantity += $orderproduct->quantity;

                
                $tempArray['tax'] = $orderproduct->tax_amount;

                if($tempArray['tax'])
                {
                    $tax_detail = unserialize($tempArray['tax_title']) ;
                    if(is_array($tax_detail))
                        $tempArray['tax_detail'] = is_array(unserialize($tempArray['tax_title'])) ? unserialize($tempArray['tax_title']) : null ;
                }

       			$tempArray['price'] = $tempArray['unitPrice'] * $tempArray['quantity'];
            
                if(!empty($directPayment) && !empty($isDownPaymentEnable))
                {
                    $tempArray['downPayment'] = $orderproduct->downpayment;
                    $tempArray['remainingTotalAmount'] = $tempArray['sub_total'] - $tempArray['downPayment'];
                }
       			$response['stores'][$store_id]['products'][] = $tempArray;
       		}


            $store = Engine_Api::_()->getItem('sitestore_store' , $store_id);

            // ORDER SUMMARY
            $response['stores'][$store_id]['subTotal'] = $order->sub_total;

            $response['stores'][$store_id]['name'] = $store->getTitle();
           $response['stores'][$store_id]['name'] = !empty($store) ? $store->getTitle() : "";
            $response['stores'][$store_id]['link'] = !empty($store) ? "http://" . $_SERVER['HTTP_HOST'] . $store->getHref() : "";
            $response['stores'][$store_id]['totalProductsQuantity'] = $totalQuantity;

            if(!empty($order->coupon_detail))
            {
                $coupon_detail = unserialize($order->coupon_detail);
                $response['stores'][$store_id]['coupon']['coupon_code'] = $coupon_detail['coupon_code'];
                $response['stores'][$store_id]['coupon']['value'] = $coupon_detail['coupon_amount'];
            }

            if($order->shipping_title)
            {
                $response['stores'][$store_id]['shipping_method'] = $this->translate($order->shipping_title);
                $response['stores'][$store_id]['shipping_method_price'] = $order->shipping_price ;
            }

            $response['stores'][$store_id]['tax'] = $order->store_tax + $order->admin_tax ;
            $response['stores'][$store_id]['total'] = $order->grand_total ;

            $response['totalAmountFields'] = array('Grand Total:' => $order->grand_total);
            $response['grandTotal'] = $order->grand_total;

       	}

       	// ORDER NOTE
       	if(!empty($order->order_note))
       	{
       		$response['order_note']['label'] = $this->translate(" Order Note ");
       		$response['order_note']['note'] = $this->translate($order->order_note);
       	}

       	// COMMENTS WORK
       	// $buyerComments = $commentsTable->getBuyerComments($order_id , $viewer_id);
       	// $sellerComments = $commentsTable->getSellerComments($order_id, array());
       	// $siteAdminComments = $commentsTable->getSiteAdminComments($order_id, array());
       	// $response['comments']['buyerComments']['totalItemCount'] =0;
       	// $response['comments']['sellerComments']['totalItemCount']=0;
       	// $response['comments']['siteAdminComments']['totalItemCount']=0;
       	// if(!empty($buyerComments))
       	// {
       	// 	$response['comments']['buyerComments']['totalItemCount'] = count($buyerComments);
       	// 	foreach($buyerComments as $row => $buyerComment)
       	// 	{
       	// 		$response['comments']['buyerComments']['comments'][] = array('creation_date' => Engine_Api::_()->getApi('core','siteapi')->getBeautifyDate($buyerComment['creation_date']) , 'comment' => $this->translate($buyerComment['comment']));
       	// 	}
       	// }

       	// if(!empty($sellerComments))
       	// {
       	// 	$response['comments']['sellerComments']['totalItemCount'] = count($sellerComments);
       	// 	foreach($sellerComments as $row => $sellerComment)
       	// 	{
       	// 		$response['comments']['sellerComments']['comments'][] = array('creation_date' => Engine_Api::_()->getApi('core','siteapi')->getBeautifyDate($sellerComment['creation_date']) , 'comment' => $this->translate($sellerComment['comment']));
       	// 	}
       	// }

       	// if(!empty($siteAdminComments))
       	// {
       	// 	$response['comments']['siteAdminComments']['totalItemCount'] = count($siteAdminComments);
       	// 	foreach($siteAdminComments as $row => $siteAdminComment)
       	// 	{
       	// 		$response['comments']['sellerComments']['comments'][] = array('creation_date' => Engine_Api::_()->getApi('core','siteapi')->getBeautifyDate($siteAdminComment['creation_date']) , 'comment' => $this->translate($siteAdminComment['comment']));
       	// 	}
       	// }
        // COMMENTS WORK ENDS
        $response['order']['ip_address'] = urlencode($response['order']['ip_address']);
        $this->respondWithSuccess($response , false);

	}

	/*
	* Create buyer comment 
	*/
	public function commentAction()
	{
		// GET THE VIEWER
		$viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $commentsTable = Engine_Api::_()->getDbTable('orderComments' , 'sitestoreproduct');

        $order_id = $this->_getParam('order_id' , 0);

        if(!isset($order_id) || empty($order_id))
        	$this->respondWithValidationError('validation_fail', "order_id is required");

        $order = Engine_Api::_()->getItem('sitestoreproduct_order', $order_id);

        if(!$order)
        	$this->respondWithError('no_record');

        if($order->buyer_id != $viewer_id)
        	$this->respondWithValidationError('validation_fail', "Buyer of this order adn the current user are not same");

        if($this->getRequest()->isGet()){
        	$response['form'] = array(
        							'name' => 'commentText',
        							'type' => 'text',
        							'label' => $this->translate("Add a comment"),
        						);
        	$this->respondWithSuccess($response , false);

        }

        if($this->getRequest()->isPost()){
        	$values = $this->_getAllParams();

        	if(!isset($values['commentText']) || empty($values['commentText']))
        		$this->respondWithValidationError('validation_fail','commentText missing');
        	
        	try {
        		$date = date('Y-m-d H:i:s');
	        	$newComment = $commentsTable->createRow();
	        	$newComment->order_id = $order_id;
	        	$newComment->owner_id = $viewer_id;
	        	$newComment->creation_date = $date;
	        	$newComment->modified_date = $date;
	        	$newComment->comment = $values['commentText'];
	        	$newComment->buyer_status = 1 ; 
	        	$newComment->store_owner_status = 1 ;
	        	$newComment->store_admin_status = 1 ;
	        	$newComment->user_type = 0;
	        	$newComment->save();
        	} catch (Exception $e) {
        		// black exception 
        	}
        	$this->successResponseNoContent('no_content');
        }
	}

	/*
	* Order shipping details 
	*/
	public function orderShipAction() {

        // ONLY LOGGED IN USER 

        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $order_id = $this->_getParam('order_id', null);

        $order_id = $this->_getParam('order_id' , 0);

        if(!isset($order_id) || empty($order_id))
        	$this->respondWithValidationError('validation_fail', "order_id is required");


        // USER IS BUYER OR NOT
        $orderObj = Engine_Api::_()->getItem('sitestoreproduct_order', $order_id);
        $store_id = $orderObj->store_id;

        if ($viewer_id != $orderObj->buyer_id) {
            $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);

            //IS USER IS STORE ADMIN OR NOT
            if (empty($authValue) || $authValue == 1) {
                $this->respondWithValidationError('validation_fail' , $this->translate("Order not available or you are not permitted to view shipping details of this order."));
            }
        }

        $anyOtherProducts = Engine_Api::_()->getDbtable('OrderProducts', 'sitestoreproduct')->checkProductType(array('order_id' => $order_id, 'virtual' => true));
        if (empty($anyOtherProducts)) {
            $this->respondWithValidationError('validation_fail' , $this->translate("Order not available or you are not permitted to view shipping details of this order."));
        }

        $isStoreExist = Engine_Api::_()->getDbtable('stores', 'sitestore')->getStoreAttribute($store_id, 'store_id');
        $shipTrackObj = Engine_Api::_()->getDbtable('shippingtrackings', 'sitestoreproduct')->getShipTracks($order_id);

        $trackingstatusArray = array();
        $trackingstatusArray[1] = $this->translate("Active");
        $trackingstatusArray[2] = $this->translate("Completed");
        $trackingstatusArray[3] = $this->translate("Canceled");

		if(count($shipTrackObj))
		{
			$response['totalItemCount'] = count($shipTrackObj);

			foreach($shipTrackObj as $row => $shippingTrack)
			{
				$tempArray = array();
				$tempArray['service'] = $shippingTrack->service ;
				$tempArray['title'] = (isset($shippingTrack->title) && !empty($shippingTrack->title)) ? $this->translate($shippingTrack->title) : "-" ;
				$tempArray['tracking_number'] = $shippingTrack->tracking_num ;
				$tempArray['date'] = $shippingTrack->creation_date;
				$tempArray['status'] = $trackingstatusArray[$shippingTrack->status] ;
				$tempArray['note'] = empty($shippingTrack->note) ? "-" : Engine_Api::_()->sitestoreproduct()->truncation($shippingTrack->note);
				$response['shipment_tracking'] = $tempArray;
			}

			$this->respondWithSuccess($response , false);

		}
		else
			$this->respondWithError("no_record");

    }

    /* order cancel api */
    public function cancelAction()
    {
        $this->validateRequestMethod("PUT");

        $order_id = $this->_getParam('order_id', null);

        if(!$order_id)
            $this->respondWithValidationError("parameter_missing" , "order_id missing");
        $order = Engine_Api::_()->getItem("sitestoreproduct_order" , $order_id);

        if(!$order)
            $this->respondWithError("no_record");

        try
        {
            $order->order_status = 6;
            $order->save();
            $this->successResponseNoContent("no_content");
        }
        catch(Exception $e)
        {
            $this->respondWithError('internal_server_error', $e->getMessage());
        }

    }

    public function downloadAction()
    {
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        $productId = Engine_Api::_()->sitestoreproduct()->getEncodeToDecode($this->_getParam('product_id', NULL));
        $downloadableFileId = Engine_Api::_()->sitestoreproduct()->getEncodeToDecode($this->_getParam('downloadablefile_id', NULL));
        $orderDownloadId = Engine_Api::_()->sitestoreproduct()->getEncodeToDecode($this->_getParam('download_id', NULL));
        $orderDownloadItem = Engine_Api::_()->getItem('sitestoreproduct_orderdownload', $orderDownloadId);
        $downloadablefileItem = Engine_Api::_()->getItem('sitestoreproduct_downloadablefile', $downloadableFileId);
        $orderItem = Engine_Api::_()->getItem('sitestoreproduct_order', $orderDownloadItem->order_id);

        if (empty($orderDownloadItem) ||
                empty($downloadablefileItem) ||
                empty($orderItem) ||
                ( (!empty($orderDownloadItem->max_downloads) && $orderDownloadItem->downloads >= $orderDownloadItem->max_downloads) )
        )
            $this->respondWithError("no_record");



        // Get path
        $path = $relPath = (string) APPLICATION_PATH . '/public/sitestoreproduct_product/file_' . $productId . '/main';

        $downloadablefile_name = $downloadablefileItem->filename;
        $path = $path . '/' . $downloadablefile_name;

        if (@file_exists($path) && @is_file($path)) {
            if (!empty($orderDownloadItem->max_downloads)) {
//        Engine_Api::_()->getDbtable('orderdownloads', 'sitestoreproduct')->update(array(
//            'downloads' => new Zend_Db_Expr("downloads + 1")
//            ), array(
//                'orderdownload_id = ?' => $orderDownloadId
//              ));
                $orderDownloadItem->downloads += 1;
                $orderDownloadItem->save();
            }

            // Kill zend's ob
            $isGZIPEnabled = false;
            if (ob_get_level()) {
                $isGZIPEnabled = true;
                @ob_end_clean();
            }

            header("Content-Disposition: attachment; filename=" . @urlencode(@basename($path)), true);
            header("Content-Transfer-Encoding: Binary", true);
            header("Content-Type: application/force-download", true);
            header("Content-Type: application/octet-stream", true);
            header("Content-Type: application/download", true);
            header("Content-Description: File Transfer", true);
            if (empty($isGZIPEnabled)) {
                header("Content-Length: " . @filesize($path), true);
                @flush();
            }

            $fp = @fopen($path, "r");
            while (!feof($fp)) {
                echo @fread($fp, 65536);
                if (empty($isGZIPEnabled))
                    @flush();
            }
            @fclose($fp);
        }
        exit();
    }


    /*
    * Reorder action 
    */
    public function reorderAction() {

        $this->validateRequestMethod("POST");

        $order_id = $this->_getParam('order_id', null);

        //REORDER THE ORDER PRODUCTS
        if (!empty($order_id)) {
            //GET VIEWER ID
            $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

            //IF VIEWER IS NOT LOGGED-IN
            if (empty($viewer_id)) {
                $this->respondWithValidationError('validation_fail' , "not logged in");
            }

            $buyer_id = Engine_Api::_()->getItem('sitestoreproduct_order', $order_id)->buyer_id;

            //IF VIEWER AND BUYER ARE NOT SAME
            if (($buyer_id != $viewer_id)) {
                $this->respondWithValidationError('validation_fail' , 'The current user is not the owner of the order');
            }

            $params = array();
            $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
            $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);
            // if (!empty($directPayment) && !empty($isDownPaymentEnable)) {
            //     $params['fetchDownpaymentValue'] = 1;
            // }

            $order_products = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct')->getReorderProducts($order_id, $params);

            $cart_table = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct');
            $cart_product_table = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct');

            $viewerCartObj = $cart_table->fetchRow(array('owner_id = ?' => $viewer_id));
            if (!empty($viewerCartObj))
                $cart_id = $viewerCartObj->cart_id;

            if (!empty($order_products)) {
                if (empty($cart_id)) {
                    $cart_table->insert(array('owner_id' => $viewer_id, 'creation_date' => date('Y-m-d H:i:s')));
                    $cart_id = $cart_table->getAdapter()->lastInsertId();

                    foreach ($order_products as $product) {
                        if ($product['product_type'] == 'downloadable') {
                            $isAnyFileExist = Engine_Api::_()->getDbtable('downloadablefiles', 'sitestoreproduct')->isAnyMainFileExist($product['product_id']);

                            if (!empty($isAnyFileExist))
                                $cart_product_table->insert(array('cart_id' => $cart_id, 'product_id' => $product['product_id'], 'quantity' => $product['quantity']));
                        } else
                            $cart_product_table->insert(array('cart_id' => $cart_id, 'product_id' => $product['product_id'], 'quantity' => $product['quantity']));
                    }
                } else {
                    // CHECK PRODUCT PAYMENT TYPE => DOWNPAYMENT OR NOT
                    if (!empty($directPayment) && !empty($isDownPaymentEnable)) {
                        $productIds = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->getCartProductIds($cart_id);
                        $product_ids = implode(",", $productIds);
                        $cartProductPaymentType = Engine_Api::_()->sitestoreproduct()->getProductPaymentType($product_ids);
                    }

                    foreach ($order_products as $product) {
                        if (!empty($directPayment) && !empty($isDownPaymentEnable)) {

                            if (empty($order_products['downpayment_value']) && !empty($cartProductPaymentType)) {
                                $this->respondWithValidationError('validation_fail' , 'no downpayment for the product');
                            } else if (!empty($order_products['downpayment_value']) && empty($cartProductPaymentType)) {
                                $this->respondWithValidationError('validation_fail' , 'no downpayment for the product');
                            }
                        }
                        $quantity = $product['quantity'];
                        $cart_product_obj = $cart_product_table->fetchRow(array('cart_id = ?' => $cart_id, 'product_id =?' => $product['product_id']));
                        if (!empty($cart_product_obj))
                            $cart_product_id = $cart_product_obj->product_id;
                        else
                            $cart_product_id = '';

                        if (empty($cart_product_id)) {
                            if ($product['product_type'] == 'downloadable') {
                                $isAnyFileExist = Engine_Api::_()->getDbtable('downloadablefiles', 'sitestoreproduct')->isAnyMainFileExist($product['product_id']);
                                if (!empty($isAnyFileExist))
                                    $cart_product_table->insert(array('cart_id' => $cart_id, 'product_id' => $product['product_id'], 'quantity' => $product['quantity']));
                            } else if($product['product_type'] != 'configurable')
                                $cart_product_table->insert(array('cart_id' => $cart_id, 'product_id' => $product['product_id'], 'quantity' => $quantity));
                        } else {
                            if ($product['product_type'] == 'downloadable') {
                                $isAnyFileExist = Engine_Api::_()->getDbtable('downloadablefiles', 'sitestoreproduct')->isAnyMainFileExist($product['product_id']);
                                if (empty($isAnyFileExist))
                                    continue;
                            }
                            if($product['product_type'] != "configurable")
                            {
                                try {

                                  $cart_product_table->update(array(
                                        'quantity' => new Zend_Db_Expr("quantity + $quantity"),
                                            ), array(
                                        'product_id = ?' => $product['product_id'],
                                        'cart_id = ?' => $cart_id
                                    ));

                                  } catch (Exception $e) {
                                        // black exception 
                                  }
                            }
                        }
                    }
                }
            }
        }

        $this->setRequestMethod();

        $this->_forward('view' , 'cart' , 'sitestore' , array());

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


}
