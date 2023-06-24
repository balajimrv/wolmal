<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Startupcreate.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreproduct_Form_Admin_Startupcreate extends Engine_Form {

    public function init() {
        // Conditions: When click on 'edit' from the admin-help & learnmore-manage page for showing prefields for selected ID.
        
        $textFlag = Zend_Controller_Front::getInstance()->getRequest()->getParam('textFlag', 1);
        $pageId = Zend_Controller_Front::getInstance()->getRequest()->getParam('startuppages_id', null);
        if (empty($pageId)) {
            $this->setTitle('Create a new Store Startup Page')->setDescription("Create a new startup help page using the rich editor below.");
        }


        if (!empty($pageId)) {
            $pageObj = Engine_Api::_()->getItem('sitestoreproduct_startuppage', $pageId);
            $title = $pageObj->title;
            $description = $pageObj->description;
            $shortDescription = $pageObj->short_description;
            $setHiddenValue = $pageId;
            $this->setTitle('Edit ' . $title . ' page')
            ->setDescription('Edit ' . $title . ' using the rich editor below.');
        } else {
            $title = $shortDescription = $description = '';
            $setHiddenValue = 0;
        }

        $this->addElement('Text', 'title', array(
            'label' => 'Page Title',
            'description' => 'Enter the heading of the page.',
            'required' => true,
            'value' => $title
        ));

        $this->addElement('Dummy', 'text_flag', array(
            'label' => 'Language Support',
        ));

        if( empty($textFlag) ) {
            $this->addElement('Textarea', 'short_description', array(
                'label' => 'Short Description',
                'description' => 'Enter short description for this page. (This short description will be displayed on the Store Startup page.)',
                'maxlength' => '60',
                'value' => $shortDescription
            ));
            
            $this->addElement('Textarea', 'description', array(
                'label' => 'Page Content',
                'description' => 'Enter the rich content for this page.',
                'value' => $description
            ));
        }else {
            $this->addElement('TinyMce', 'short_description', array(
                'label' => 'Short Description',
                'description' => 'Enter short description for this page. (This short description will be displayed on the Store Startup page.)',
                'maxlength' => '60',
                'value' => $shortDescription
            ));
            
            $this->addElement('TinyMce', 'description', array(
                'label' => 'Page Content',
                'description' => 'Enter the rich content for this page.',
                'value' => $description
            ));
        }
        
        $this->addElement('Hidden', 'is_opration', array(
            'value' => $setHiddenValue
        ));


        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));
    }

}