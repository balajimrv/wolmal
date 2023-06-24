<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    FormValidation.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */



class Sitestoreproduct_Api_Siteapi_FormValidators extends Siteapi_Api_Validators {

    /**
     * Review form validators
     * 
     * @param type $widgetSettingsReviews
     * @return array
     */
    public function getReviewCreateFormValidators($widgetSettingsReviews) {
        
        $sitestoreproductreview_proscons = $widgetSettingsReviews['settingsReview']['sitestoreproductreview_proscons'];
        $sitestoreproductreview_limit_proscons = $widgetSettingsReviews['settingsReview']['sitestoreproductreview_limit_proscons'];
        $sitestoreproductreview_recommended = $widgetSettingsReviews['settingsReview']['sitestoreproductreview_recommended'];
        if ($sitestoreproductreview_proscons) {
            if ($sitestoreproductreview_limit_proscons) {
                $formValidators['pros'] = array(
                    'allowEmpty' => false,
                    'maxlength' => $widgetSettingsReviews['$sitestoreproductreview_limit_proscons'],
                    'required' => true,
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                        new Engine_Filter_HtmlSpecialChars(),
                        new Engine_Filter_EnableLinks(),
                    ),
                );
            } else {
                $formValidators['pros'] = array(
                    'allowEmpty' => false,
                    'required' => true,
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                        new Engine_Filter_HtmlSpecialChars(),
                        new Engine_Filter_EnableLinks(),
                    ),
                );
            }
            if ($sitestoreproductreview_limit_proscons) {
                $formValidators['cons'] = array(
                    'allowEmpty' => false,
                    'maxlength' => $widgetSettingsReviews['$sitestoreproductreview_limit_proscons'],
                    'required' => true,
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                        new Engine_Filter_HtmlSpecialChars(),
                        new Engine_Filter_EnableLinks(),
                    ),
                );
            } else {
                $formValidators['cons'] = array(
                    'allowEmpty' => false,
                    'required' => true,
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                        new Engine_Filter_HtmlSpecialChars(),
                        new Engine_Filter_EnableLinks(),
                    ),
                );
            }
        }
        $formValidators['title'] = array(
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
            ),
        );
        
        $formValidators['review_rate_0'] = array(
            'required' => true,
        );
        
        return $formValidators;
    }

    public function tellaFriendFormValidators()
    {
        $formValidators = array();
        
        $formValidators['sender_name'] = array(
            'required' => true,
            'allowEmpty' => false,
        );
        
        $formValidators['sender_email'] = array(
            'required' => true,
            'allowEmpty' => false,
        );
        
        $formValidators['receiver_emails'] = array(
            'required' => true,
            'allowEmpty' => false,
        );
        
        $formValidators['message'] = array(
            'required' => true,
            'allowEmpty' => false,
        );
        
        return $formValidators;   
    }

    public function getMessageOwnerFormValidators()
    {
        $formValidators = array();
        
        $formValidators['title'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', false, array(3, 63))
            )
        );
        
        $formValidators['body'] = array(
            'required' => true,
            'allowEmpty' => false,
        );
        
        return $formValidators;   
    }
    
    /*
    * Comment validation form
    *
    * @return array
    */
    public function getcommentValidation()
    {
        $formValidators['body'] = array(
            'required' => true,
        );
        
        return $formValidators;
    }

}
