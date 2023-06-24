<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Review_SitemobileCreate extends Engine_Form {

    public $_error = array();
    protected $_item;
    protected $_profileTypeReview;

    public function getProfileTypeReview() {
        return $this->_profileTypeReview;
    }

    public function setProfileTypeReview($profileTypeReview) {
        $this->_profileTypeReview = $profileTypeReview;
        return $this;
    }

    public function getItem() {
        return $this->_item;
    }

    public function setItem($item) {
        $this->_item = $item;
        return $this;
    }

    public function init() {

        $coreApi = Engine_Api::_()->getApi('settings', 'core');

        //GET WIDGET PARAMETERS
        $sitestoreproduct_proscons = $coreApi->getSetting('sitestoreproduct.proscons', 1);
        $sitestoreproduct_limit_proscons = $coreApi->getSetting('sitestoreproduct.limit.proscons', 500);
        $sitestoreproduct_recommend = $coreApi->getSetting('sitestoreproduct.recommend', 1);

        //GET DECORATORS
        $this->loadDefaultDecorators();

        //GET VIEWER INFO
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //GET LISTING ID
        $getItemProduct = $this->getItem();
        $product_title = "<b>" . $getItemProduct->title . "</b>";

        //IF NOT HAS POSTED THEN THEN SET FORM
        $this->setTitle('Write a Review')
                ->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("Give your ratings and opinion for %s below:"), $product_title))
                ->setAttrib('name', 'sitestoreproduct_create')
                ->setAttrib('id', 'sitestoreproduct_create')
                ->getDecorator('Description')->setOption('escape', false);
        $this->setAttrib('class', 'global_form seaocore_form_comment');
        if (empty($viewer_id)) {
            $this->addElement('Text', 'anonymous_name', array(
                'label' => 'Name',
                'allowEmpty' => false,
                'required' => true,
                'filters' => array(
                    'StripTags',
                    new Engine_Filter_Censor(),
                    new Engine_Filter_StringLength(array('max' => '63')),
            )));

            $this->addElement('Text', 'anonymous_email', array(
                'label' => 'Email',
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                    array('NotEmpty', true),
                    array('EmailAddress', true)),
                'filters' => array(
                    'StripTags',
                    new Engine_Filter_Censor(),
                 )
            ));
            $this->anonymous_email->getValidator('EmailAddress')->getHostnameValidator()->setValidateTld(false);
        }

        if ($sitestoreproduct_proscons) {
            if ($sitestoreproduct_limit_proscons) {
                $this->addElement('Textarea', 'pros', array(
                    'label' => 'Pros',
                    'rows' => 2,
                    'description' => Zend_Registry::get('Zend_Translate')->_("What do you like about this Product?"),
                    'allowEmpty' => false,
                    'maxlength' => $sitestoreproduct_limit_proscons,
                    'required' => true,
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                        new Engine_Filter_HtmlSpecialChars(),
                        new Engine_Filter_EnableLinks(),
                    ),
                ));
            } else {
                $this->addElement('Textarea', 'pros', array(
                    'label' => 'Pros',
                    'rows' => 2,
                    'description' => Zend_Registry::get('Zend_Translate')->_("What do you like about this Product?"),
                    'allowEmpty' => false,
                    'required' => true,
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                        new Engine_Filter_HtmlSpecialChars(),
                        new Engine_Filter_EnableLinks(),
                    ),
                ));
            }
            $this->pros->getDecorator('Description')->setOptions(array('placement' => 'PREPAND', 'escape' => false));

            if ($sitestoreproduct_limit_proscons) {
                $this->addElement('Textarea', 'cons', array(
                    'label' => 'Cons',
                    'rows' => 2,
                    'description' => Zend_Registry::get('Zend_Translate')->_("What do you dislike about this Product?"),
                    'allowEmpty' => false,
                    'maxlength' => $sitestoreproduct_limit_proscons,
                    'required' => true,
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                        new Engine_Filter_HtmlSpecialChars(),
                        new Engine_Filter_EnableLinks(),
                    ),
                ));
            } else {
                $this->addElement('Textarea', 'cons', array(
                    'label' => 'Cons',
                    'rows' => 2,
                    'description' => Zend_Registry::get('Zend_Translate')->_("What do you dislike about this Product?"),
                    'allowEmpty' => false,
                    'required' => true,
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                        new Engine_Filter_HtmlSpecialChars(),
                        new Engine_Filter_EnableLinks(),
                    ),
                ));
            }
            $this->cons->getDecorator('Description')->setOptions(array('placement' => 'PREPAND', 'escape' => false));
        }

        $this->addElement('Textarea', 'title', array(
            'label' => 'One-line summary',
            'rows' => 1,
            'allowEmpty' => false,
            'maxlength' => 63,
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
            ),
        ));

        $profileTypeReview = $this->getProfileTypeReview();
        if (!empty($profileTypeReview)) {
            $customFields = new Sitestoreproduct_Form_Custom_Standard(array(
                'item' => 'sitestoreproduct_review',
                'topLevelId' => 1,
                'topLevelValue' => $profileTypeReview,
                'decorators' => array(
                    'FormElements'
            )));

            $customFields->removeElement('submit_addtocart');

            $this->addSubForms(array(
                'fields' => $customFields
            ));
        }

        $this->addElement('Textarea', 'body', array(
            'label' => 'Summary',
            'rows' => 3,
            'allowEmpty' => true,
            'required' => false,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
            ),
        ));

        if ($sitestoreproduct_recommend) {
            $this->addElement('Radio', 'recommend', array(
                'label' => 'Recommended',
                'description' => sprintf(Zend_Registry::get('Zend_Translate')->_("Would you recommend %s to a friend?"), $product_title),
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => 1
            ));
            $this->recommend->getDecorator('Description')->setOption('escape', false);
        }

        if (empty($viewer_id) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.captcha', 1)) {
            if (Engine_Api::_()->hasModuleBootstrap('siterecaptcha')) {
                Zend_Registry::get('Zend_View')->recaptcha($this);
            } else {
                $this->addElement('captcha', 'captcha', array(
                    'description' => 'Please type the characters you see in the image.',
                    'captcha' => 'image',
                    'required' => true,
                    'captchaOptions' => array(
                        'wordLen' => 6,
                        'fontSize' => '30',
                        'timeout' => '30000',
                        'imgDir' => APPLICATION_PATH . '/public/temporary/',
                        'imgUrl' => $this->getView()->baseUrl() . '/public/temporary',
                        'font' => APPLICATION_PATH . '/application/modules/Core/externals/fonts/arial.ttf'
                )));
            }
        }

        $this->addElement('Button', 'submit', array(
            'label' => 'Submit',
            'order' => 10,
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
