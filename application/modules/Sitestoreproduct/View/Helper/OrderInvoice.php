<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: OrderInvoice.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_View_Helper_OrderInvoice extends Zend_View_Helper_Abstract {
  
  public function orderInvoice($order) {
    $isSitecreditEnabled = Engine_Api::_()->hasModuleBootstrap('sitecredit');
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
    
    $invoice .= '</b></div><div style="float:right;font-size: 13pt;"><strong> '.$this->view->translate("INVOICE").' </strong></div></div></div><div style="font-family:tahoma,arial,verdana,sans-serif;font-size:10pt;border:1px solid #CCCCCC;overflow:hidden;"><div style="border-right:1px solid #CCCCCC;float:left;width:298px;"><div style="padding: 10px;"><b>'.$this->view->translate("Store Name & Address") . '</b><br />'.Engine_Api::_()->sitestoreproduct()->getStoreAddress($order->store_id).'</div><div style="padding: 10px;border-top:1px solid #CCC;"><b>'.$this->view->translate("Name & Billing Address") . '</b><br />'.$billing_address->f_name.' '. $billing_address->l_name . '<br />'.$billing_address->address . '<br />'.@strtoupper($billing_address->city) . ' - ' . $billing_address->zip . '<br />'.@strtoupper(Zend_Locale::getTranslation($billing_address->country, "country")) . '<br />'.@strtoupper(Engine_Api::_()->getItem("sitestoreproduct_region", $billing_address->state)->region) .'<br />'.$this->view->translate("Ph: %s", $billing_address->phone) . '<br />';

//    if( empty($order->buyer_id) )
//        $invoice .= $billing_address->email . '<br /><br /><br />';

    $invoice .= '</div><div style="padding: 10px;border-top:1px solid #CCC;"><b>'.$this->view->translate("Name & Shipping Address") . '</b><br />'.$shipping_address->f_name . ' ' .$shipping_address->l_name . '<br />'.$shipping_address->address . '<br />'.@strtoupper($shipping_address->city) . ' - ' . $shipping_address->zip . '<br />'.@strtoupper(Zend_Locale::getTranslation($shipping_address->country, "country")) . '<br />'. @strtoupper(Engine_Api::_()->getItem("sitestoreproduct_region", $shipping_address->state)->region) .'<br />'.$this->view->translate("Ph: %s", $shipping_address->phone).'</div></div><div style="float: right; width: 298px; border-left: 1px solid #ccc; margin-left: -1px;"><ul style="padding:0;margin:0;">';

    $invoice .= '<li style="border-bottom: 1px solid #CCCCCC;list-style:none;padding:10px;margin:0;"><b>'.$this->view->translate("Order #%s", $order->order_id).'</b></li><li style="border-bottom: 1px solid #CCCCCC;list-style:none;padding:10px;margin:0;"><div style="width: 128px;float:left;"> <b>'.$this->view->translate("Status").'  </b> </div><div>: &nbsp;'. $this->view->getOrderStatus($order->order_status) . '<br/> </div></li><li style="border-bottom: 1px solid #CCCCCC;list-style:none;padding:10px;margin:0;"><div style="width: 128px;float:left;"> <b> '.$this->view->translate("Placed on").' </b> </div><div>: &nbsp;'. $this->view->locale()->toDateTime($order->creation_date) .'<br/> </div></li><li style="border-bottom: 1px solid #CCCCCC;list-style:none;padding:10px;margin:0;"><div style="width: 128px;float:left;"> <b> '. $this->view->translate("Payment Method").' </b> </div><div>: &nbsp; '. $this->view->translate(Engine_Api::_()->sitestoreproduct()->getGatwayName($order->gateway_id)) .' <br/> </div></li>';
 
    if( $order->shipping_title )
      $invoice .= '<li style="border-bottom: 1px solid #CCCCCC;list-style:none;padding:10px;margin:0;"><div style="width: 128px;float:left;"> <b> '.$this->view->translate("Shipping Method").' </b> </div><div>: &nbsp; '.$order->shipping_title .'<br/></div></li>';
    
    if( !empty($isDownPaymentEnable) && !empty($order->is_downpayment) ) {
      $tempColumn = '<th style="text-align:center;padding:7px 10px;width:128px;"> '.$this->view->translate("Downpayment Amount").' </th><th style="text-align:center;padding:7px 10px;width:128px;"> '.$this->view->translate("Remaining Amount").' </th>';
    } else {
      $tempColumn = '';
    }
    
    if( $order->gateway_id == 3 ) {
      $admin_cheque_detail = Engine_Api::_()->getApi('settings', 'core')->getSetting('send.cheque.to', null);
      $storeTitle = Engine_Api::_()->getDbtable('stores', 'sitestore')->getStoreAttribute($order->store_id, 'title');
      $storeChequeDetail = Engine_Api::_()->getDbtable('sellergateways', 'sitestoreproduct')->getStoreChequeDetail(array('store_id' => $order->store_id));
      $cheque_info = Engine_Api::_()->getDbtable('ordercheques', 'sitestoreproduct')->getChequeDetail($order->cheque_id);
      if( empty($order->direct_payment) && !empty($site_title) && !empty($admin_cheque_detail) ) {
        $invoice .= '<li style="border-bottom: 1px solid #CCCCCC;list-style:none;padding:10px;margin:0;"><b>'.$this->view->translate("%s's Bank Account Details", $site_title).'</b><div>'.$admin_cheque_detail.'</div></li>';
      } elseif( !empty($order->direct_payment) && !empty($storeTitle) && !empty($storeChequeDetail) ) {
        $invoice .= '<li style="border-bottom: 1px solid #CCCCCC;list-style:none;padding:10px;margin:0;"><b>'.$this->view->translate("%s store's Bank Account Details", $storeTitle).'</b><div>'.$storeChequeDetail.'</div></li>';
      }
      $invoice .= '<li style="border-bottom: 1px solid #CCCCCC;list-style:none;padding:10px;margin:0;"><b>'.$this->view->translate("Buyer Account Info").'</b><div style="overflow:hidden;"><div style="clear:both;"><div style="width:170px; float:left">'.$this->view->translate("Cheque No").'</div><div>: &nbsp;'.$cheque_info["cheque_no"].'</div></div><div style="clear:both;"><div style="width:170px; float:left">'.$this->view->translate("Account Holder Name").'</div><div>: &nbsp;'.$cheque_info["customer_signature"].'</div></div><div style="clear:both;"><div style="width:170px; float:left">'.$this->view->translate("Account Number").'</div><div>: &nbsp;'.$cheque_info["account_number"].'</div></div><div style="clear:both;"><div style="width:170px; float:left">'.$this->view->translate("Bank Rounting Number").'</div><div>: &nbsp;'.$cheque_info["bank_routing_number"].'</div></div></div></li>';
    }

    $invoice .= '</ul></div></div><b style="margin:10px 0 5px;display:block;">' . $this->view->translate("Order Details") . '</b><div id="manage_order_tab" style="font-family:tahoma,arial,verdana,sans-serif;font-size:10pt;overflow-x:auto;width: 100%;"><div style="border:none;margin:0 0 10px;float:left;"><table style="border: 1px solid #CCCCCC;margin-top: 1px;width: 100%;">     <tr style="background-color:#EAEAEA;"><th style="text-align:center;padding:7px 10px;width:252px;"> '.$this->view->translate("Product").' </th><th style="text-align:center;padding:7px 10px;width:128px;">'.$this->view->translate("Quantity").'</th><th style="text-align:center;padding:7px 10px;width:128px;"> '.$this->view->translate("Unit Price").' </th>' . $tempColumn . '<th style="text-align:center;padding:7px 10px;width:128px;"> '.$this->view->translate("Total").' </th></tr>';

    foreach( $order_products as $product ){
      
      $temp_lang_title = Engine_Api::_()->sitestoreproduct()->getProductTitle($product->product_title);

      $invoice .= '<tr><td title="'. $temp_lang_title .'" style="text-align:center;padding:7px 10px;">'. Engine_Api::_()->sitestoreproduct()->truncation($temp_lang_title, 40);
      if( !empty($product->order_product_info) ) {
        $order_product_info = unserialize($product->order_product_info);
      }
      if( !empty($order_product_info) && !empty($order_product_info['calendarDate']) && !empty($order_product_info['calendarDate']['starttime']) && !empty($order_product_info['calendarDate']['endtime']) ) {
        $invoice .=  '<br /><b>' . $this->view->translate("From: ") . '</b>' . $this->view->locale()->toDate($order_product_info['calendarDate']['starttime']) . '<br />';
        $invoice .=  '<b>' . $this->view->translate("To: ") . '</b>' . $this->view->locale()->toDate($order_product_info['calendarDate']['endtime']);
      }
      
      if( !empty($isDownPaymentEnable) && !empty($order->is_downpayment) ) {
        $downPaymentPrice = '<td style="text-align:center;padding:7px 10px;">' . Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($product->downpayment * $product->quantity) . '</td><td style="text-align:center;padding:7px 10px;">' . Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency((($product->price * $product->quantity) - ($product->downpayment * $product->quantity))) . '</td>';
      } else {
        $downPaymentPrice = '';
      }
      
      if( !empty($order_product_info) && !empty($order_product_info['price_range_text']) ) {
        $priceRangeText = $this->view->translate($order_product_info['price_range_text']);
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
      $tempInfo = '<div style="clear:both;"><div style="float:left;font-weight:bold;">'.$this->view->translate("Downpayment Grand Total").' &nbsp;&nbsp;</div><div style="float:right;">'.Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($order->downpayment_total).'</div></div><div style="clear:both;"><div style="float:left;font-weight:bold;">'.$this->view->translate("Remaining Amount Grand Total").' &nbsp;&nbsp;</div><div style="float:right;">'.Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($remainingAmount).'</div></div>';
    } else {
      $tempInfo = '';
    }

    $invoice .= '</table></div></div><div><b style="margin:10px 0 5px;display:block;">' . $this->view->translate("Order Summary") . '</b></div><div style="font-family:tahoma,arial,verdana,sans-serif;font-size:10pt;background-color:#EAEAEA;border:1px solid #CCCCCC;padding:10px;margin-bottom:10px;float:right;width:300px;"><div style="margin-bottom:5px;overflow:hidden;">';
    if( $isSitecreditEnabled && $order->credit_value) {
      $invoice .='<div style="clear:both;"><div style="float:left;"> <b> '.$this->view->translate("Credits Redeemed").' </b> </div><div style="float:right;">'.Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($order->credit_value) .' <br/></div></div>';
    }
    $invoice .='<div style="clear:both;"><div style="float:left;"> <b> '.$this->view->translate("Subtotal").' </b> </div><div style="float:right;">'.Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($order->sub_total) .' <br/></div></div><div style="clear:both;"><div style="float:left;"><b> '.$this->view->translate("Taxes").' </b></div><div style="float:right;">'.Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($order->store_tax + $order->admin_tax)) .'<br/> </div></div><div style="clear:both;"><div style="float:left;"><b>'.$this->view->translate("Shipping price").'</b></div><div style="float:right;">'.Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($order->shipping_price) .'<br/></div></div></div><div>' . $tempInfo . '<div style="clear:both;"><div style="float:left;"><h2 style="margin:5px 0 0;font-size:20px;"> '.$this->view->translate("Grand Total").' &nbsp;&nbsp;</h2></div><div style="float:right;"><h2 style="margin:5px 0 0;font-size:20px;">'.Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($order->grand_total).'</h2></div></div></div></div>';

    if(!empty($order->order_note)):
      $invoice .= '<div style="float:left"><div style="margin-bottom: 10px;border:1px solid #CCCCCC;width:270px;clear:both;padding:10px;"><div style="margin-bottom:2px;"><b>'. $this->view->translate("Buyer Note:").' </b></div>'.Engine_Api::_()->sitestoreproduct()->truncation($order->order_note, 310).'</div></div>';
    endif; 

    $invoice .= '</div></div></div>';
    
    //WORK FOR SHOWING THE PROFILE FIELDS OF STORE
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $order->store_id);
    if(!empty($sitestore))
      $storefieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitestore);
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitestore/View/Helper', 'Sitestore_View_Helper');
    $profileFields = $this->view->billFieldValueLoop($sitestore, $storefieldStructure, true);
    if (!empty($profileFields)) :
      $invoice.='<div style="overflow:hidden">';
      $invoice.='<div  style="margin: 10px auto;  width: 600px;">';
      $invoice .='<div  style="padding: 5px; width: 588px; border: 1px solid #ccc; margin: 5px auto;">' . $profileFields . '</div>';
      $invoice.='</div></div>';
    endif;

    return $invoice;
  }
}
