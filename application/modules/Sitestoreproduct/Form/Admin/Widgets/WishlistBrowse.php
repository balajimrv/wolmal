<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Adsettings.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Admin_Widgets_WishlistBrowse extends Core_Form_Admin_Widget_Standard {

  public function init() {

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;    
    $url = $view->url(array("action" => "browse"), "sitestoreproduct_wishlist_general", true);
    
    $this->addElement('Text', 'titleLink', array(
              'label' => $view->translate('Enter Title Link'),
              'description' => 'If you do not want to show title link, then simply leave this field empty.',
              'value' => '<a href="'.$url.'">Get inspired by more picks »</a>'
            )
    );
    
    $this->addElement('MultiCheckbox', 'viewTypes', array(
              'label' => $view->translate('Choose the view types.'),
              'multiOptions' => array("list" => $view->translate("List View"), "grid" => $view->translate("Pinboard View")),
              'value' => 'grid',
            )
    );
    
    $this->addElement('Radio', 'viewTypeDefault', array(
              'label' => $view->translate('Choose the default view type'),
              'multiOptions' => array("list" => $view->translate("List View"), "grid" => $view->translate("Pinboard View")),
              'value' => 'grid',
            )
    );
    
    $this->addElement('Text', 'wishlistBlockWidth', array(
                'label' => $view->translate('Enter the width of wishlist block.'),
                'value' => 198,
            )
    );
    
    $this->addElement('Radio', 'is_only_featured', array(
                'label' => $view->translate('Do you want to show only Featured wishlists in this widget?'),
                'multiOptions' => array('1' => "Yes", '0' => "No",),
                'value' => 0,
            )
    );
    
    $this->addElement('Radio', 'is_bottom_title', array(
                'label' => $view->translate('Do you want to show wishlist title and number of products in a wishlist below the wishlist blocks? (This setting will only work, if you have disables Follow and Like link.)'),
                'multiOptions' => array('1' => "Yes", '0' => "No",),
                'value' => 0,
            )
    );
    
    $this->addElement('MultiCheckbox', 'followLike', array(
              'label' => $view->translate('Choose the action link to be available for each Wishlist pinboard item.'),
              'multiOptions' => array(
                  'follow' => $view->translate('Follow / Unfollow'),
                  'like' => $view->translate('Like / Unlike'),
              ),
          )
    );
    
    $this->addElement('MultiCheckbox', 'statisticsWishlist', array(
              'label' => $view->translate('Choose the statistics that you want to be displayed for the Wishlist in this block.'),
              'multiOptions' => array("viewCount" => "Views", "likeCount" => "Likes", "followCount" => "Followers", "productCount" => "Products"),
          )
    );
    
    $this->addElement('Text', 'listThumbsCount', array(
              'label' => $view->translate('Enter the number of product thumbnails to be shown along with the cover photo of a wishlist. (This setting will only work, if you have chosen Pinboard View from the above setting.)'),
              'value' => 4,
          )
    );
    
    $this->addElement('Text', 'itemCount', array(
              'label' => $view->translate('Number of wishlists to show per page'),
              'value' => 20,
          )
    );
    
    $this->addElement( 'Radio', 'wishlistCount', array(
              'label' => $view->translate('Do you want to show wishlist count or not?'),
              'multiOptions' => array('1' => "Yes", '0' => "No",),
              'value' => 1,
          )
    );
    
    $this->addElement('Radio', 'showPagination', array(
              'label' => $view->translate('Do you want to show pagination or not?'),
              'multiOptions' => array('1' => "Yes", '0' => "No",),
              'value' => 1,
          )
    );
    
    $this->addElement('Radio', 'displayBy', array(
              'label' => $view->translate('Default ordering of wishlists'),
              'description' =>'Select the default ordering of wishlists.',
              'multiOptions' => array(
                  'all' => "All wishlists in descending order of creation.",
                  'desc_view' => "All wishlists in descending order of views.",
                  'alphabetically' => "All wishlists in alphabetical order.",
                  'featured_wishlist' => "Featured wishlists followed by others in descending order of creation.",
                  ),
              'value' => 'all',
          )
    );
  }
}