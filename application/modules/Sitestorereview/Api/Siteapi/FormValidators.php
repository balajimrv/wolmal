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



class Sitestorereview_Api_Siteapi_FormValidators extends Siteapi_Api_Validators {

    /**
     * Review form validators
     * 
     * @param type $widgetSettingsReviews
     * @return array
     */
    public function getReviewCreateFormValidators($widgetSettingsReviews) {

        $getItemPage = $widgetSettingsReviews['item'];
        $sitestorereview_proscons = $widgetSettingsReviews['settingsReview']['sitestorereview_proscons'];
        $sitestorereview_limit_proscons = $widgetSettingsReviews['settingsReview']['sitestorereview_limit_proscons'];
        $sitestorereview_recommend = $widgetSettingsReviews['settingsReview']['sitestorereview_recommend'];
        if ($sitestorereview_proscons) {
            if ($sitestorereview_limit_proscons) {
                $formValidators['pros'] = array(
                    'allowEmpty' => false,
                    'maxLength' => $widgetSettingsReviews['sitestorereview_limit_proscons'],
                    'required' => true,
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                        new Engine_Filter_HtmlSpecialChars(),
                        new Engine_Filter_EnableLinks(),
                        new Engine_Filter_StringLength(array('max' => $widgetSettingsReviews['sitestorereview_limit_proscons'],'min' => 10)),
                    ),
                );
            } else {
                $formValidators['pros'] = array(
                    'allowEmpty' => false,
                    'minlength' => 10,
                    'required' => true,
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                        new Engine_Filter_HtmlSpecialChars(),
                        new Engine_Filter_EnableLinks(),
                        new Engine_Filter_StringLength(array('min' => 10,'max' => $widgetSettingsReviews['sitestorereview_limit_proscons'])),
                    ),
                );
            }
            if ($sitestorereview_limit_proscons) {
                $formValidators['cons'] = array(
                    'allowEmpty' => false,
                    'minlength' => 10,
                    'maxlength' => $widgetSettingsReviews['sitestorereview_limit_proscons'],
                    'required' => true,
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                        new Engine_Filter_HtmlSpecialChars(),
                        new Engine_Filter_EnableLinks(),
                        new Engine_Filter_StringLength(array('min' => 10,'max' => $widgetSettingsReviews['sitestorereview_limit_proscons'])),
                    ),
                );
            } else {
                $formValidators['cons'] = array(
                    'allowEmpty' => false,
                    'minlength' => 10,
                    'required' => true,
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                        new Engine_Filter_HtmlSpecialChars(),
                        new Engine_Filter_EnableLinks(),
                        new Engine_Filter_StringLength(array('max' => $widgetSettingsReviews['sitestorereview_limit_proscons'],'min' => 10)),
                    ),
                );
            }
        }
        $formValidators['title'] = array(
            'required' => true,
            'minlength' => 10,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
                new Engine_Filter_StringLength(array('min' => 10)),
            ),
        );
        
        $formValidators['review_rate_0'] = array(
            'required' => true,
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
