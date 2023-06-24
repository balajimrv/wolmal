<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Level.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreproduct_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract {

  public function init() {

    parent::init();

    $this->setTitle('Member Level Settings')
            ->setDescription("These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.");

    $view_element = "view";
    $this->addElement('Radio', "$view_element", array(
        'label' => 'Allow Viewing of Products?',
        'description' => 'Do you want to let members view products? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
            2 => 'Yes, allow viewing of all products, even private ones.',
            1 => 'Yes, allow viewing of products.',
            0 => 'No, do not allow products to be viewed.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if (!$this->isModerator()) {
      unset($this->$view_element->options[2]);
    }

    if (!$this->isPublic()) {

      $create_element = "create";
      $this->addElement('Radio', "$create_element", array(
          'label' => 'Allow Creation of Products?',
          'description' => 'Do you want to let members create products? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view products, but only want certain levels to be able to create products.',
          'multiOptions' => array(
              1 => 'Yes, allow creation of products.',
              0 => 'No, do not allow products to be created.'
          ),
          'value' => 1,
      ));

      $edit_element = "edit";
      $this->addElement('Radio', "$edit_element", array(
          'label' => 'Allow Editing of Products?',
          'description' => 'Do you want to let members edit products? If set to no, some other settings on this page may not apply.',
          'multiOptions' => array(
              2 => 'Yes, allow members to edit all products.',
              1 => 'Yes, allow members to edit their own products.',
              0 => 'No, do not allow members to edit their products.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->$edit_element->options[2]);
      }

      $delete_element = "delete";
      $this->addElement('Radio', "$delete_element", array(
          'label' => 'Allow Deletion of Products?',
          'description' => 'Do you want to let members delete products? If set to no, some other settings on this page may not apply.',
          'multiOptions' => array(
              2 => 'Yes, allow members to delete all products.',
              1 => 'Yes, allow members to delete their own products.',
              0 => 'No, do not allow members to delete their products.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->$delete_element->options[2]);
      }

      $comment_element = "comment";
      $this->addElement('Radio', "$comment_element", array(
          'label' => 'Allow Commenting on Products?',
          'description' => 'Do you want to let members of this level comment on products?',
          'multiOptions' => array(
              2 => 'Yes, allow members to comment on all products, including private ones.',
              1 => 'Yes, allow members to comment on products.',
              0 => 'No, do not allow members to comment on products.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->$comment_element->options[2]);
      }

      $style_element = "style";
      $this->addElement('Radio', "$style_element", array(
          'label' => 'Allow Custom CSS Styles?',
          'description' => 'If you enable this feature, your members will be able to customize the colors and fonts of their products by altering their CSS styles.',
          'multiOptions' => array(
              1 => 'Yes, enable custom CSS styles.',
              0 => 'No, disable custom CSS styles.',
          ),
          'value' => 1,
      ));

      $overview_element = "overview";
      $this->addElement('Radio', "$overview_element", array(
          'label' => 'Allow Overview?',
          'description' => 'Do you want to let members enter rich Overview for their products?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => 1,
      ));

      $this->addElement('Radio', "contact", array(
          'label' => 'Allow Contact Details',
          'description' => 'Do you want to let members enter contact details for their products?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => 1,
      ));

      $this->addElement('Radio', "metakeyword", array(
          'label' => 'Meta Tags / Keywords',
          'description' => 'Do you want to let members enter meta tags / keywords for their products?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => 1,
      ));

      $auth_view_element = "auth_view";
      $this->addElement('MultiCheckbox', "$auth_view_element", array(
          'label' => 'Product View Options',
          'description' => 'Your members can choose from any of the options checked below when they decide who can see their products. These options appear on your members "Add Products and "Edit Entry" pages. If you do not check any options, everyone will be allowed to view products.',
          'multiOptions' => array(
              'everyone' => 'Everyone',
              'registered' => 'All Registered Members',
              'owner_network' => 'Friends and Networks',
              'owner_member_member' => 'Friends of Friends',
              'owner_member' => 'Friends Only',
              'owner' => 'Just Me'
          ),
          'value' => array('everyone', 'registered', 'owner_network', 'owner_member_member', 'owner_member', 'owner')
      ));

      $auth_comment_element = "auth_comment";
      $this->addElement('MultiCheckbox', "$auth_comment_element", array(
          'label' => 'Product Comment Options',
          'description' => 'Your members can choose from any of the options checked below when they decide who can post comments on their products. If you do not check any options, everyone will be allowed to post comments on products.',
          'multiOptions' => array(
              'registered' => 'All Registered Members',
              'owner_network' => 'Friends and Networks',
              'owner_member_member' => 'Friends of Friends',
              'owner_member' => 'Friends Only',
              'owner' => 'Just Me'
          ),
          'value' => array('registered', 'owner_network', 'owner_member_member', 'owner_member', 'owner')
      ));
      $photo_element = "photo";
      $this->addElement('Radio', "$photo_element", array(
          'label' => 'Allow Uploading of Photos?',
          'description' => 'Do you want to let members upload Photos to products?',
          'multiOptions' => array(
              2 => 'Yes, allow photo uploading to products, including private ones.',
              1 => 'Yes, allow photo uploading to products.',
              0 => 'No, do not allow photo uploading.'
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->$photo_element->options[2]);
      }
      $auth_photo_element = "auth_photo";
      $this->addElement('MultiCheckbox', "$auth_photo_element", array(
          'label' => 'Photo Upload Options',
          'description' => 'Your members can choose from any of the options checked below when they decide who can upload the photos in their products. If you do not check any options, everyone will be allowed to upload photos to the products of this member level.',
          'multiOptions' => array(
              'registered' => 'All Registered Members',
              'owner_network' => 'Friends and Networks',
              'owner_member_member' => 'Friends of Friends',
              'owner_member' => 'Friends Only',
              'owner' => 'Just Me'
          ),
          'value' => array('registered', 'owner_network', 'owner_member_member', 'owner_member', 'owner')
      ));
      if (Engine_Api::_()->sitestoreproduct()->enableVideoPlugin()) {
        $video_element = "video";
        $this->addElement('Radio', "$video_element", array(
            'label' => 'Allow Uploading of Videos?',
            'description' => 'Do you want to let members upload Videos to products?',
            'multiOptions' => array(
                1 => 'Yes, allow video uploading to products.',
                0 => 'No, do not allow video uploading.'
            ),
            'value' => 1,
        ));

        $auth_video_element = "auth_video";
        $this->addElement('MultiCheckbox', "$auth_video_element", array(
            'label' => 'Video Upload Options',
            'description' => 'Your members can choose from any of the options checked below when they decide who can upload the videos in their products. If you do not check any options, everyone will be allowed to upload video.',
            'multiOptions' => array(
                'registered' => 'All Registered Members',
                'owner_network' => 'Friends and Networks',
                'owner_member_member' => 'Friends of Friends',
                'owner_member' => 'Friends Only',
                'owner' => 'Just Me'
            ),
            'value' => array('registered', 'owner_network', 'owner_member_member', 'owner_member', 'owner')
        ));
      }

      $approved_element = "approved";
      $this->addElement('Radio', "$approved_element", array(
          'label' => 'Product Approval Moderation',
          'description' => 'Do you want new Product to be automatically approved?',
          'multiOptions' => array(
              1 => 'Yes, automatically approve Product.',
              0 => 'No, site administrator approval will be required for all Product.'
          ),
          'value' => 1,
      ));

      $featured_element = "featured";
      $this->addElement('Radio', "$featured_element", array(
          'label' => 'Product Featured Moderation',
          'description' => 'Do you want new Product to be automatically made featured?',
          'multiOptions' => array(
              1 => 'Yes, automatically make Product featured.',
              0 => 'No, site administrator will be making Product featured.'
          ),
          'value' => 1,
      ));

      $sponsored_element = "sponsored";
      $this->addElement('Radio', "$sponsored_element", array(
          'label' => 'Product Sponsored Moderation',
          'description' => 'Do you want new Product to be automatically made Sponsored?',
          'multiOptions' => array(
              1 => 'Yes, automatically make Product Sponsored.',
              0 => 'No, site administrator will be making Product Sponsored.'
          ),
          'value' => 1,
      ));
      
      $wishlist_create_element = "create_wishlist";
      $this->addElement('Radio', "$wishlist_create_element", array(
          'label' => 'Allow Creation of Wishlists?',
          'description' => 'Do you want to let members create wishlists and add products to their wishlists? If set to no, members of this level will neither be able to create wishlists nor they will be able to add products to their wishlists',
          'multiOptions' => array(
              1 => 'Yes, allow creation of wishlists.',
              0 => 'No, do not allow wishlists to be created.'
          ),
          'value' => 1,
      ));      
      
    }

    $this->addElement('Radio', "wishlist", array(
        'label' => 'Allow Viewing of Wishlists?',
        'description' => 'Do you want to let members view Wishlists? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
            2 => 'Yes, allow members to view all wishlists, even private ones.',
            1 => 'Yes, allow viewing of wishlists.',
            0 => 'No, do not allow wishlists to be viewed.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
    ));

    if (!$this->isModerator()) {
      unset($this->wishlist->options[2]);
    }

    if (!$this->isPublic()) {

      $this->addElement('MultiCheckbox', "auth_wishlist", array(
          'label' => 'Wishlists View Privacy',
          'description' => 'Your members can choose from any of the options checked below when they decide who can see their wishlists. These options appear on your members\' "Create New Wishlists" and "Edit Wishlists" pages. If you do not check any options, everyone will be allowed to view wishlists.',
          'multiOptions' => array(
              'everyone' => 'Everyone',
              'registered' => 'All Registered Members',
              'owner_network' => 'Friends and Networks',
              'owner_member_member' => 'Friends of Friends',
              'owner_member' => 'Friends Only',
              'owner' => 'Just Me'
          )
      ));
    }

    $review_create_element = "review_create";
    $this->addElement('Radio', "$review_create_element", array(
        'label' => 'Allow Writing of Reviews',
        'description' => 'Do you want to let members write reviews for products?',
        'multiOptions' => array(
            1 => 'Yes, allow members to write reviews.',
            0 => 'No, do not allow members to write reviews.',
        ),
        'value' => 1,
    ));

    if (!$this->isPublic()) {

      $review_reply_element = "review_reply";
      $this->addElement('Radio', "$review_reply_element", array(
          'label' => 'Allow Commenting on Reviews?',
          'description' => 'Do you want to let members to comment on Reviews?',
          'multiOptions' => array(
              1 => 'Yes, allow members to comment on reviews.',
              0 => 'No, do not allow members to comment on reviews.',
          ),
          'value' => 1,
      ));
      if (!$this->isModerator()) {
        unset($this->$review_reply_element->options[2]);
      }

      $review_update_element = "review_update";
      $this->addElement('Radio', "$review_update_element", array(
          'label' => 'Allow Updating of Reviews?',
          'description' => 'Do you want to let members to update their reviews?',
          'multiOptions' => array(
              1 => 'Yes, allow members to update their own reviews.',
              0 => 'No, do not allow members to update their reviews.',
          ),
          'value' => 1,
      ));

      $review_delete_element = "review_delete";
      $this->addElement('Radio', "$review_delete_element", array(
          'label' => 'Allow Deletion of Reviews?',
          'description' => 'Do you want to let members delete reviews?',
          'multiOptions' => array(
              2 => 'Yes, allow members to delete all reviews.',
              1 => 'Yes, allow members to delete their own reviews.',
              0 => 'No, do not allow members to delete their reviews.',
          ),
          'value' => ( $this->isModerator() ? 2 : 0 ),
      ));
      if (!$this->isModerator()) {
        unset($this->$review_delete_element->options[2]);
      }
    }
  }

}
