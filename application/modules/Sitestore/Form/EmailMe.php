<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: EmailMe.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_EmailMe extends Engine_Form {

    public $_error = array();

    public function init() {

        $store_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('id', null);
        $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
        $StoreTitle = $sitestore->title;

        $this->setTitle('Email Me')
                ->setDescription('Please fill the form given below to contact this Store.')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setAttrib('name', 'sitestores_create');
        $this->setAttrib('class', 'global_form seaocore_form_comment');
        $viewr_name = "";
        $viewr_email = "";
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity() > 0) {
            $viewr_name = $viewer->getTitle();
            $viewr_email = $viewer->email;
        }
        // TITLE
        $this->addElement('Text', 'sitestore_sender_name', array(
            'label' => 'Your Name *',
            'allowEmpty' => false,
            'required' => true,
            'value' => $viewr_name,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
        )));

        //SENDER EMAIL
        $this->addElement('Text', 'sitestore_sender_email', array(
            'label' => 'Your Email *',
            'allowEmpty' => false,
            'required' => true,
            'value' => $viewr_email,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
        )));

        // RECIVER EMAILS
//     $this->addElement('Text', 'sitestore_reciver_emails', array(
//         'label' => 'To *',
//         'allowEmpty' => false,
//         'required' => true,
//         'description' => 'Separate multiple addresses with commas.',
//         'filters' => array(
//             new Engine_Filter_Censor(),
//         ),
//     ));


        $text_value = Zend_Registry::get('Zend_Translate')->_('Thought you would be interested in this.');
//     $this->sitestore_reciver_emails->getDecorator("Description")->setOption("placement", "append");

        $photo = 'http://' . $_SERVER['HTTP_HOST'] . $sitestore->getPhotoUrl('thumb.icon');
        $photo = "<img src='$photo' style='  float: left;height: 35px;margin-right: 5px;width: 35px;' /></img>";

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $title = $photo . "<a href='" . $view->url(array('store_url' => $sitestore->store_url), 'sitestore_entry_view') . "'  target='_blank'>" . ucfirst($sitestore->getTitle()) . "</a>";
        $this->addElement('Dummy', 'sitestore_reciver_emails', array(
            'label' => 'To *',
            'description' => $title,
        ));
        $this->getElement('sitestore_reciver_emails')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));



        // MESSAGE
        $this->addElement('textarea', 'sitestore_message', array(
            'label' => 'Message *',
            'required' => true,
            'allowEmpty' => false,
            'attribs' => array('rows' => 24, 'cols' => 150, 'style' => 'width:230px; max-width:400px;height:120px;'),
            'value' => $text_value,
            'description' => 'You can send a personal note in the mail.',
            'filters' => array(
                'StripTags',
                new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
                new Engine_Filter_Censor(),
            ),
        ));
        $this->sitestore_message->getDecorator("Description")->setOption("placement", "append");
        // SEND COPY TO ME
        $this->addElement('Checkbox', 'sitestore_send_me', array(
            'label' => "Send a copy to my email address.",
        ));
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.captcha.post', 1) && empty($viewer_id)) {
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
                        'timeout' => 300,
                        'imgDir' => APPLICATION_PATH . '/public/temporary/',
                        'imgUrl' => $this->getView()->baseUrl() . '/public/temporary',
                        'font' => APPLICATION_PATH . '/application/modules/Core/externals/fonts/arial.ttf'
                )));
                $this->captcha->getDecorator("Description")->setOption("placement", "append");
            }
        }

        // Element: SEND
        $this->addElement('Button', 'sitestore_send', array(
            'label' => 'Send',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));
        // Element: cancel
        $this->addElement('Cancel', 'sitestore_cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            // 'href' => 'history(-2)'
            //'onclick' => 'history.go(-1); return false;',
            'onclick' => 'javascript:parent.Smoothbox.close()',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        // DisplayGroup: buttons
        $this->addDisplayGroup(array(
            'sitestore_send',
            'sitestore_cancel',
                ), 'sitestore_buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper'
            ),
        ));
    }

}

?>