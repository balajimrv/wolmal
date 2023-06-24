<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Account.php 9812 2012-11-01 02:14:01Z matthew $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_modules_Core_Form_Contact extends Core_Form_Contact {

    public function init() {
        parent::init();

        if ($this->captcha) {
            $tabindex = $this->captcha->getAttrib('tabindex') - 1;

            $this->removeElement('captcha');
            if (!Engine_Api::_()->sitemobile()->isApp()) {
                if (
                        extension_loaded("gd") &&
                        function_exists("imagepng") &&
                        function_exists("imageftbbox")
                ) {
                    if (Engine_Api::_()->hasModuleBootstrap('siterecaptcha')) {
                        Zend_Registry::get('Zend_View')->recaptcha($this, $tabindex, 3);
                    } else {
                        $this->addElement('captcha', 'captcha', array(
                            'label' => 'Human Verification',
                            'description' => 'Please type the characters you see in the image.',
                            'captcha' => 'image',
                            'required' => true,
                            'allowEmpty' => false,
                            'order' => 3,
                            'captchaOptions' => array(
                                'wordLen' => 6,
                                'fontSize' => '30',
                                'timeout' => 300,
                                'imgDir' => APPLICATION_PATH . '/public/temporary/',
                                'imgUrl' => $this->getView()->baseUrl() . '/public/temporary',
                                'font' => APPLICATION_PATH . '/application/modules/Core/externals/fonts/arial.ttf',
                            )
                        ));
                    }
                } else {
                    if (Engine_Api::_()->hasModuleBootstrap('siterecaptcha')) {
                        Zend_Registry::get('Zend_View')->recaptcha($this, $tabindex, 3);
                    } else {
                        $captchaElement = new Zend_Form_Element_Captcha(
                                'captcha', array(
                            'label' => 'Human Verification',
                            'description' => 'Please type the characters you see in the image.',
                            'required' => true,
                            'allowEmpty' => false,
                            'order' => $tabindex,
                            'captcha' => array(
                                'captcha' => 'Figlet',
                                'wordLen' => 6,
                                'timeout' => 600))
                        );
                        $this->addElement($captchaElement, 'captcha');
                    }
                }
            }
        }
    }

}
