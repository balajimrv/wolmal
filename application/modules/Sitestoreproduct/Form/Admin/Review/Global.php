<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreproduct_Form_Admin_Review_Global extends Engine_Form {

  public function init() {

    $this->setTitle('Review Settings')
            ->setDescription('Reviews & ratings are an extremely useful feature that enables you to gather refined ratings, reviews and feedback for the products in your community. Below, you can highly configure the settings for reviews & ratings for the products on your site.');

    $settings = Engine_Api::_()->getApi('settings', 'core');


      $editorreviewDesc = 'Yes, allow editors to edit all "Editor Reviews".';
    

    $this->addElement('Radio', 'sitestoreproduct_editorreview', array(
        'label' => 'Editing Editor Reviews',
        'description' => 'Do you want to let editors edit all "Editor Reviews"?',
        'multiOptions' => array(
            1 => $editorreviewDesc,
            0 => 'No, editors can only edit their own "Editor Reviews".',
        ),
        'value' => $settings->getSetting('sitestoreproduct.editorreview', 0),
    ));

    $this->addElement('Radio', 'sitestoreproduct_proscons', array(
        'label' => 'Pros and Cons in User Reviews',
        'description' => 'Do you want Pros and Cons fields in Reviews? (If enabled, reviewers will be able to enter Pros and Cons for the Products that they review, and the same will be shown in their reviews.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sitestoreproduct.proscons', 1),
        'onclick' => 'prosconsInReviews(this.value)',
        'allowEmpty' => true,
        'required' => false,
    ));

    $this->addElement('Radio', 'sitestoreproduct_proncons', array(
        'label' => "Required Pros and Cons",
        'description' => 'Do you want to make Pros and Cons fields to be required when reviewers review products on your site?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sitestoreproduct.proncons', 1),
    ));

    $this->addElement('Text', 'sitestoreproduct_limit_proscons', array(
        'label' => 'Pros and Cons Character Limit',
        'description' => 'What character limit should be applied to the Pros and Cons fields? (Enter 0 for no character limitation.)',
        'value' => $settings->getSetting('sitestoreproduct.limit.proscons', 500),
        'allowEmpty' => false,
        'required' => true,
    ));

    $this->addElement('Radio', 'sitestoreproduct_recommend', array(
        'label' => 'Recommended in Reviews',
        'description' => 'Do you want Recommended field in Reviews? (If enabled, reviewers will be able to select if they would recommend that Product to a friend, and the same will be shown in their review.)',
        'value' => $settings->getSetting('sitestoreproduct.recommend', 1),
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'allowEmpty' => true,
        'required' => false,
    ));

    $this->addElement('Radio', 'sitestoreproduct_summary', array(
        'label' => 'Required Summary',
        'description' => 'Do you want to make Summary field to be required when reviewers review products on your site?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sitestoreproduct.summary', 1),
    ));


    $this->addElement('Radio', 'sitestoreproduct_report', array(
        'label' => 'Report',
        'description' => 'Allow logged-in users to report reviews as inappropriate.',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sitestoreproduct.report', 1),
        'allowEmpty' => true,
        'required' => false,
    ));

    $this->addElement('Radio', 'sitestoreproduct_share', array(
        'label' => 'Share',
        'description' => 'Allow logged-in users to share reviews in their activity feeds.',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sitestoreproduct.share', 1),
        'allowEmpty' => true,
        'required' => false,
    ));

    $this->addElement('Radio', 'sitestoreproduct_email', array(
        'label' => 'Email',
        'description' => 'Allow logged-in users to email the review content.',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sitestoreproduct.email', 1),
        'allowEmpty' => true,
        'required' => false,
    ));

    $this->addElement('Radio', 'sitestoreproduct_captcha', array(
        'label' => 'Enable Captcha',
        'description' => 'Do you want to enable captcha when visitors review products on your site?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sitestoreproduct.captcha', 1),
    ));

    $this->addElement('Textarea', 'sitestoreproduct_contact', array(
        'label' => 'Email Addresses',
        'description' => 'Enter the email addresses on which notification emails will be sent when visitors of your site review products on your site. From Member Level Settings, you can choose if visitors should be able to review products. (Note: You can add multiple addresses with commas.)',
        'value' => $settings->getSetting('sitestoreproduct.contact', $settings->getSetting('core.mail.from', 'email@domain.com')),
        'allowEmpty' => false,
        'required' => true,
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}