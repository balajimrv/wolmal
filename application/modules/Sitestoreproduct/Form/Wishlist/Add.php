<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Add.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Wishlist_Add extends Engine_Form {

  public function init() {

    $this->setTitle('Add To Wishlist')
            ->setAttrib('id', 'form-upload-wishlist')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $wishlistDatas = Engine_Api::_()->getDbtable('wishlists', 'sitestoreproduct')->getUserWishlists($viewer_id);
    $wishlistDatasCount = Count($wishlistDatas);
    $product_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('product_id', null);
    $product = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
 
    if ($wishlistDatasCount >= 1) {
      $this->setDescription("Please select the wishlists in which you want to add this Product.");
    }

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
        $this->addElement('Checkbox', 'inWishlist_' . $wishlistData->wishlist_id, array(
            'label' => $wishlistData->title,
            'value' => 1,
        ));
      } else {
        $this->addElement('Checkbox', 'wishlist_' . $wishlistData->wishlist_id, array(
            'label' => $wishlistData->title,
            'value' => 0,
        ));
      }
    }

    if ($wishlistDatasCount >= 1) {
      $this->addElement('dummy', 'dummy_text', array('label' => "You can also add this Product in a new wishlist below:"));
    } else {
      $this->addElement('dummy', 'dummy_text', array('label' => "You have not created any wishlist yet. Get Started by creating and adding Products!"));
    }

    if ($wishlistDatasCount) {
      $this->addElement('Text', 'title', array(
          'label' => 'Wishlist Name',
          'maxlength' => '63',
          'filters' => array(
            'StripTags',
              new Engine_Filter_Censor(),
              new Engine_Filter_StringLength(array('max' => '63')),
          )
      ));
    } else {
      $this->addElement('Text', 'title', array(
          'label' => 'Wishlist Name',
          'maxlength' => '63',
          'required' => true,
          'allowEmpty' => false,
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
              new Engine_Filter_StringLength(array('max' => '63')),
          )
      ));
    }

    $this->addElement('Textarea', 'body', array(
        'label' => 'Description',
        'maxlength' => '512',
        'filters' => array(
          'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '512')),
        )
    ));

    $availableLabels = array(
        'everyone' => 'Everyone',
        'registered' => 'All Registered Members',
        'owner_network' => 'Friends and Networks',
        'owner_member_member' => 'Friends of Friends',
        'owner_member' => 'Friends Only',
        'owner' => 'Just Me'
    );

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestoreproduct_wishlist', $viewer, 'auth_view');
    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));

    if (count($viewOptions) < 1) {

      $this->addElement('hidden', 'auth_view', array('value' => 'everyone'));
    } else {
      $this->addElement('Select', 'auth_view', array(
          'label' => 'View Privacy',
          'description' => 'Who may see this wishlist?',
          'multiOptions' => $viewOptions,
          'value' => key($viewOptions),
      ));
      $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
    }

    $this->addElement('Button', 'submit', array(
        'label' => 'Save',
        'ignore' => true,
        'decorators' => array('ViewHelper'),
        'type' => 'submit'
    ));

    $this->addElement('Cancel', 'cancel', array(
        'prependText' => ' or ',
        'label' => 'cancel',
        'link' => true,
        'onclick' => "javascript:parent.Smoothbox.close();",
        'decorators' => array(
            'ViewHelper'
        ),
    ));

    $this->addDisplayGroup(array(
        'submit',
        'cancel'
            ), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper',
        ),
    ));
  }

}