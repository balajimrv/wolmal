<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorereview_Form_Admin_Settings_Global extends Engine_Form {

  public function init() {
    $this
            ->setTitle('General Settings')
            ->setDescription('Reviews & ratings are an extremely useful feature that enables you to gather refined ratings, reviews and feedback for the stores in your community. Below, you can highly configure the settings for reviews & ratings for the stores on your site.');

    $this->addElement('Radio', 'sitestorereview_proscons', array(
        'label' => 'Pros and Cons in Reviews',
        'description' => 'Do you want Pros and Cons fields in Reviews? (If enabled, reviewers will be able to enter Pros and Cons for the Stores that they review, and the same will be shown in their reviews.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.proscons', 1),
    ));

    $this->addElement('Text', 'sitestorereview_limit_proscons', array(
        'label' => 'Pros and Cons Character Limit',
        'required' => true,
        'allowEmpty' => false,
        'description' => 'What character limit should be applied to the Pros and Cons fields? (Enter 0 for no character limitation.)',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.limit.proscons', 75),
    ));

    $this->addElement('Radio', 'sitestorereview_recommend', array(
        'label' => 'Recommended in Reviews',
        'description' => 'Do you want Recommended field in Reviews? (If enabled, reviewers will be able to select if they would recommend that Store to a friend, and the same will be shown in their review.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.recommend', 1),
    ));

    $this->addElement('Radio', 'sitestorereview_report', array(
    	'label' => 'Report as inappropriate',
      'description' => 'Allow logged-in users to report reviews as inappropriate.',
      'multiOptions' => array(
        1 => ' 	Yes',
        0 => ' 	No'
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.report', 1),
    ));

    $this->addElement('Radio', 'sitestorereview_review_show_menu', array(
        'label' => 'Reviews Link',
        'description' => 'Do you want to show the Reviews link on Stores Navigation Menu? (You might want to show this if Reviews from Stores are an important component on your website. This link will lead to a widgetized store listing all Store Reviews, with a search form for Store Reviews and multiple widgets.',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.review.show.menu', 1),
    ));

    $this->addElement('Radio', 'sitestorereview_order', array(
        'label' => 'Default Ordering in Store Reviews listing',
        'description' => 'Select the default ordering of reviews in Store Reviews listing. (This widgetized store will list all Store Reviews.)',
        'multiOptions' => array(
            1 => 'All reviews in descending order of creation.',
            2 => 'All reviews in alphabetical order.',
            3 => 'Featured reviews followed by others in descending order of creation.',
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.order', 1),
    ));

    $this->addElement('Radio', 'sitestorereview_photo', array(
        'label' => 'Photo for Reviews',
        'description' => 'Which photo do you want to be shown alongside the review entries in the various reviews widgets, on Store Reviews Home and Browse Reviews?',
        'multiOptions' => array(
            1 => 'Photo of the Reviewer',
            0 => 'Photo of the Store'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.photo', 1),
    ));

    $this->addElement('Text', 'sitestorereview_truncation_limit', array(
        'label' => 'Title Truncation Limit',
        'description' => 'What maximum limit should be applied to the number of characters in the titles of items in the widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.)',
        'required' => true,
        'maxLength' => 3,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.truncation.limit', 13),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));
	
			$this->addElement('Text', 'sitestorereview_manifestUrl', array(
        'label' => 'Store Reviews URL alternate text for "store-reviews"',
        'allowEmpty' => false,
        'required' => true,
        'description' => 'Please enter the text below which you want to display in place of "storereviews" in the URLs of this plugin.',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.manifestUrl', "store-reviews"),
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}
?>