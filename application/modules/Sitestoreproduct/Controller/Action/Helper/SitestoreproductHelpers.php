<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: SitestoreproductHelpers.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreproduct_Controller_Action_Helper_SitestoreproductHelpers extends Zend_Controller_Action_Helper_Abstract {

  function preDispatch() {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $session = new Zend_Session_Namespace('sitestoreproduct_viewer_cart');

    if (!empty($viewer_id) && !empty($session->sitestoreproduct_guest_user_cart)) {
      $this->addProductInCart($viewer_id);
    }
  }

  function postDispatch() {
    
  }

  protected function addProductInCart($viewer_id) {
    $session = new Zend_Session_Namespace('sitestoreproduct_viewer_cart');
    if (empty($session->sitestoreproduct_guest_user_cart) || empty($viewer_id)) {
      return;
    }
    $tempUserCart = array();
    $tempUserCart = @unserialize($session->sitestoreproduct_guest_user_cart);

    $cart_table = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct');
    $cart_product_table = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct');

    $login_viewer_cart_obj = $cart_table->fetchRow(array('owner_id =? ' => $viewer_id));
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    // IF THERE IS NO SHOPPING CART EXIST FOR CURRENT VIEWER, THEN CREATE VIEWER SHOPPING CART AND ADD LOGGED-OUT VIEWER CART PRODUCTS
    if (empty($login_viewer_cart_obj) || empty($login_viewer_cart_obj->cart_id)) {
      $row = $cart_table->createRow();
      $row->setFromArray(array('owner_id' => $viewer_id));
      $cart_id = $row->save();

      foreach ($tempUserCart as $product_id => $values) {
        // IF PRODUCT IS CONFIGURABLE OR VIRTUAL
        if (isset($values['config']) && is_array($values['config']) && !empty($values['config'])) {

          // Add Cofiguration code here
          $option_id = Engine_Api::_()->getDbTable('productfields', 'sitestoreproduct')->getOptionId($product_id);

          $form = new Sitestoreproduct_Form_Custom_Standard(array(
              'item' => 'sitestoreproduct_cartproduct',
              'topLevelId' => 1,
              'topLevelValue' => $option_id,
          ));

          foreach ($values['config'] as $formValues) {

            $form->populate($formValues);

            $cartProduct = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->createRow();
            $cartProduct->cart_id = $cart_id;
            $cartProduct->product_id = $product_id;
            $cartProduct->quantity = $formValues['quantity'];
            
            if( isset($formValues['starttime']) && isset($formValues['endtime']) && !empty($formValues['starttime']) && !empty($formValues['endtime']) ) {
             $viewerSelectedDateTime = array('starttime' => $view->locale()->toDate($formValues['starttime'], array('format' => 'MM/dd/Y')), 'endtime' => $view->locale()->toDate($formValues['endtime'], array('format' => 'MM/dd/Y')));
             $cartProduct->other_info = serialize($viewerSelectedDateTime);
            }
            
            $cartProduct->save();

            $form->setItem($cartProduct);
            $form->saveValues();
          }
        } else {
          $cart_product_table->insert(array('cart_id' => $cart_id, 'product_id' => $product_id, 'quantity' => $values['quantity']));
        }
      }
    } else {
      $login_viewer_cart_id = $login_viewer_cart_obj->cart_id;
      $login_viewer_cart_products_obj = $cart_product_table->getCart($login_viewer_cart_id, true);

      $temp_viewer_cart_product_id = array();
      foreach ($login_viewer_cart_products_obj as $product) {
        $temp_viewer_cart_product_id[] = $product['product_id'];
        $temp_viewer_cart_product_quantity[$product['product_id']] = $product['quantity'];
      }

      $productTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
      foreach ($tempUserCart as $product_id => $values) {
        // IF PRODUCT IS ALREADY IN LOGGED IN VIEWER CART.
        if (in_array($product_id, $temp_viewer_cart_product_id)) {
          if (isset($values['config']) && is_array($values['config']) && !empty($values['config'])) {

            // Product Configuration
            $option_id = Engine_Api::_()->getDbTable('productfields', 'sitestoreproduct')->getOptionId($product_id);
            $form = new Sitestoreproduct_Form_Custom_Standard(array(
                'item' => 'sitestoreproduct_cartproduct',
                'topLevelId' => 1,
                'topLevelValue' => $option_id,
            ));

            foreach ($values['config'] as $formValues) {

              $form->populate($formValues);

              $duplicateOrder = 0;

              $cartProductIds = $cart_product_table->getConfiguration(array("cartproduct_id"), $product_id, $login_viewer_cart_id);
              if (!empty($cartProductIds)) {

                $formValuesQuantity = $formValues['quantity'];
                $formValues = $form->getValues();
                $formValues['quantity'] = $formValuesQuantity;
                unset($formValues['submit']);

                $duplicateOrder = 0;

                //GET CART PRODUCT IDS
                foreach ($cartProductIds as $cartProductId) {
                  $cartProduct = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $cartProductId);
                  $values = Engine_Api::_()->fields()->getFieldsValues($cartProduct);
                  $valueRows = $values->getRowsMatching(array(
                      'item_id' => $cartProduct->getIdentity(),
                  ));

                  $valueRowsArray = array();

                  foreach ($valueRows as $key => $valueRow) {
                    $valueRow->field_id = "1_$option_id" . '_' . "$valueRow->field_id";
                    if (!array_key_exists($valueRow->field_id, $valueRowsArray)) {
                      $valueRowsArray[$valueRow->field_id] = $valueRow->value;
                    } else {
                      if (is_array($valueRowsArray[$valueRow->field_id])) {
                        $newArray = $valueRowsArray[$valueRow->field_id];
                        array_push($newArray, $valueRow->value);
                        $valueRowsArray[$valueRow->field_id] = $newArray;
                      } else {
                        $newArray = array();
                        $newArray[] = $valueRowsArray[$valueRow->field_id];
                        array_push($newArray, $valueRow->value);
                        $valueRowsArray[$valueRow->field_id] = $newArray;
                      }
                    }
                  }

                  $array_diff_assoc = Engine_Api::_()->sitestoreproduct()->multidimensional_array_diff($formValues, $valueRowsArray);
                  
                  if( isset($formValues['starttime']) && isset($formValues['endtime']) && !empty($formValues['starttime']) && !empty($formValues['endtime']) ) {
                    $viewerSelectedDateTime = array('starttime' => $view->locale()->toDate($formValues['starttime'], array('format' => 'MM/dd/Y')), 'endtime' => $view->locale()->toDate($formValues['endtime'], array('format' => 'MM/dd/Y')));
                    
                    // CHECK EXISTING DATE VALUES
                    $oldViewerSelectionDate = unserialize($cartProduct->other_info);
                    if( $oldViewerSelectionDate != $viewerSelectedDateTime )
                      $array_diff_assoc = false;
                  }

                  if ($array_diff_assoc) {
                    $cartProduct->quantity = $cartProduct->quantity + $formValues['quantity'];
                    $cartProduct->save();
                    $duplicateOrder = 1;
                    break;
                  }
                }

                if (empty($duplicateOrder)) {
                  $cartProduct = $cart_product_table->createRow();
                  $cartProduct->cart_id = $login_viewer_cart_id;
                  $cartProduct->product_id = $product_id;
                  $cartProduct->quantity = $formValues['quantity'];
                  if( isset($formValues['starttime']) && isset($formValues['endtime']) && !empty($formValues['starttime']) && !empty($formValues['endtime']) ) {
                    $cartProduct->other_info = serialize($viewerSelectedDateTime);
                  }
                  $cartProduct->save();
                }
              }

              if (empty($duplicateOrder)) {
                $form->setItem($cartProduct);
                $form->saveValues();
              }
            }
          } else {
            // UPDATE PRODUCT QUANTITY IN LOGGED IN VIEWER CART
            $product_quantity = $temp_viewer_cart_product_quantity[$product_id] + $values['quantity'];
            $cart_product_table->update(array(
                'quantity' => $product_quantity,
                    ), array(
                'product_id = ?' => $product_id,
                'cart_id = ?' => $login_viewer_cart_id
            ));
          }
        } else {
          if (isset($values['config']) && is_array($values['config']) && !empty($values['config'])) {

            // Add Cofiguration code here
            $option_id = Engine_Api::_()->getDbTable('productfields', 'sitestoreproduct')->getOptionId($product_id);
            $form = new Sitestoreproduct_Form_Custom_Standard(array(
                'item' => 'sitestoreproduct_cartproduct',
                'topLevelId' => 1,
                'topLevelValue' => $option_id,
            ));

            foreach ($values['config'] as $formValues) {

              $form->populate($formValues);

              $cartProduct = $cart_product_table->createRow();
              $cartProduct->cart_id = $login_viewer_cart_id;
              $cartProduct->product_id = $product_id;
              $cartProduct->quantity = $formValues['quantity'];
              
              if( isset($formValues['starttime']) && isset($formValues['endtime']) && !empty($formValues['starttime']) && !empty($formValues['endtime']) ) {
                $viewerSelectedDateTime = array('starttime' => $view->locale()->toDate($formValues['starttime'], array('format' => 'MM/dd/Y')), 'endtime' => $view->locale()->toDate($formValues['endtime'], array('format' => 'MM/dd/Y')));
                $cartProduct->other_info = serialize($viewerSelectedDateTime);
              }
              
              $cartProduct->save();

              $form->setItem($cartProduct);
              $form->saveValues();
            }
          } else {
            // INSERT PRODUCT IN VIEWER CART
            $cart_product_table->insert(array(
                'cart_id' => $login_viewer_cart_id,
                'product_id' => $product_id,
                'quantity' => $values['quantity']
            ));
          }
        }
      }
    }

    //DELETE SESSION
    $session->sitestoreproduct_guest_user_cart = false;
  }

}
